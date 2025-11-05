<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Indicator;
use App\Models\Category;

class ProgressReportController extends Controller
{
    /**
     * Show progress summary across standards for two years.
     * Query params: y1, y2 (academic years in DB e.g., 2566, 2567)
     */
    public function index(Request $request)
    {
        $y1 = $request->integer('y1');
        $y2 = $request->integer('y2');
        $std = $request->integer('std', 1); // ส่วนที่ 1 ตามตัวอย่าง

        // Collect available years for reference
        $availableYears = Indicator::whereNotNull('year')
            ->selectRaw('DISTINCT year')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->values();

        // Build full year options (not limited to available years)
        $thaiCurrent = (int) now()->format('Y') + 543;
        $minDb = (int) (Indicator::min('year') ?: $thaiCurrent - 10);
        $maxDb = (int) (Indicator::max('year') ?: $thaiCurrent + 1);
        $startYear = min(2550, $minDb);
        $endYear = max($thaiCurrent + 1, $maxDb);
        $yearOptions = range($startYear, $endYear);

        // Fallback defaults when not provided
        if (!$y1 && $availableYears->count() > 0) {
            $y1 = (int) $availableYears[0];
        }
        if (!$y2) {
            $y2 = (int) ($availableYears->get(1) ?? ($y1 ? $y1 - 1 : null));
        }

        $years = collect([$y1, $y2])
            ->filter()
            ->unique()
            ->values()
            ->all();

        // Load categories (ด้าน) with standard and indicators filtered by selected years
        $categories = Category::with([
                'standard',
                'indicators' => function ($q) use ($years) {
                    if (!empty($years)) {
                        $q->whereIn('year', $years);
                    }
                    $q->orderBy('code');
                }
            ])
            ->when($std, fn($q) => $q->where('standard_id', $std))
            ->orderBy('id')
            ->get();

        // Group data: Standard -> Categories (ด้าน) -> Indicators
        $byStandard = [];
        foreach ($categories as $cat) {
            $standard = optional($cat->standard);
            $stdKey = $standard ? ('std_' . $standard->id) : 'std_unknown';
            $stdName = $standard ? $standard->name : 'ไม่ทราบมาตรฐาน';

            if (!isset($byStandard[$stdKey])) {
                $byStandard[$stdKey] = [
                    'name' => $stdName,
                    'categories' => [],
                ];
            }

            // Compute category totals per selected year
            $sumY1 = 0; $sumY2 = 0;
            foreach ($cat->indicators as $ind) {
                if ($y1 && (int)$ind->year === (int)$y1) { $sumY1 += (float)($ind->score_acc ?? 0); }
                if ($y2 && (int)$ind->year === (int)$y2) { $sumY2 += (float)($ind->score_acc ?? 0); }
            }

            // Merge indicators with the same code into a single row (show y1/y2 in columns)
            $rowsByCode = [];
            foreach ($cat->indicators as $ind) {
                $code = $ind->code ?: ('IND-' . $ind->id);
                if (!isset($rowsByCode[$code])) {
                    $rowsByCode[$code] = [
                        'code' => $code,
                        'name' => $ind->name,
                        'max'  => 0,
                        'y1'   => null,
                        'y2'   => null,
                    ];
                }
                // Keep the largest max score across years (defensive)
                $rowsByCode[$code]['max'] = max((float)$rowsByCode[$code]['max'], (float)($ind->max_score ?? 0));
                // Prefer non-empty name
                if (empty($rowsByCode[$code]['name']) && !empty($ind->name)) {
                    $rowsByCode[$code]['name'] = $ind->name;
                }
                if ($y1 && (int)$ind->year === (int)$y1) {
                    $rowsByCode[$code]['y1'] = (float)($ind->score_acc ?? 0);
                }
                if ($y2 && (int)$ind->year === (int)$y2) {
                    $rowsByCode[$code]['y2'] = (float)($ind->score_acc ?? 0);
                }
            }
            $rows = array_values($rowsByCode);

            $byStandard[$stdKey]['categories'][] = [
                'name' => $cat->name,
                'max'  => (float)($cat->max_score ?? 0),
                'rows' => $rows,
                'totals' => [ 'y1' => $sumY1, 'y2' => $sumY2 ],
            ];
        }

        // Transform for view: compute grand totals per standard and overall
        $sections = [];
        $grand = [ 'max' => 0, 'y1' => 0, 'y2' => 0 ];
        foreach ($byStandard as $std) {
            $stdCats = $std['categories'];
            $stdTotalMax = 0; $stdTotalY1 = 0; $stdTotalY2 = 0;
            foreach ($stdCats as $sc) {
                $stdTotalMax += (float)($sc['max'] ?? 0);
                $stdTotalY1  += (float)($sc['totals']['y1'] ?? 0);
                $stdTotalY2  += (float)($sc['totals']['y2'] ?? 0);
            }
            $sections[] = [
                'standard' => $std['name'],
                'categories' => $stdCats,
                'totals' => [ 'max' => $stdTotalMax, 'y1' => $stdTotalY1, 'y2' => $stdTotalY2 ],
            ];
            $grand['max'] += $stdTotalMax; $grand['y1'] += $stdTotalY1; $grand['y2'] += $stdTotalY2;
        }

        // Sort sections by name for predictability
        usort($sections, function ($a, $b) {
            return strcmp($a['standard'], $b['standard']);
        });

        $breadcrumbs = [
            ['title' => 'รายงาน', 'url' => route('dashboard.index')],
            ['title' => 'รายงานความก้าวหน้า', 'url' => ''],
        ];

        return view('reports.progress', [
            'y1' => $y1,
            'y2' => $y2,
            'sections' => $sections,
            'grand' => $grand,
            'availableYears' => $availableYears,
            'yearOptions' => $yearOptions,
            'selectedStandard' => $sections[0]['standard'] ?? null,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
