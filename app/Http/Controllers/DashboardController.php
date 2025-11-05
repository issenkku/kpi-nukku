<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Department;
use App\Models\Evidence;
use App\Models\Indicator;
use App\Models\Standard;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        // 1) ปีทั้งหมดสำหรับ Filter
        $yearsForFilter = Indicator::query()
            ->whereHas('assignments')
            ->whereNotNull('year')
            ->selectRaw('DISTINCT CAST(year AS INTEGER) AS y')
            ->orderBy('y')
            ->pluck('y');

        // 2) Summary รวมคะแนนตามปี
        $yearlyTotals = Indicator::query()
            ->whereHas('assignments')
            ->whereNotNull('year')
            ->selectRaw('CAST(year AS INTEGER) as year, SUM(score_acc) AS total_score, SUM(max_score) AS max_score')
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        // ปีล่าสุด
        $latestYear = $yearlyTotals->max('year') ?? $yearsForFilter->max();

        // 3) ปีที่จะแสดงผล
        $displayYear = $request->filled('year')
            ? (int) $request->input('year')
            : (int) $latestYear;

        $indicatorCount = Indicator::query()
            ->whereHas('assignments')
            ->whereNotNull('year')
            ->where('year', $displayYear)
            ->count();

        $currentYearData = $yearlyTotals->firstWhere('year', $displayYear);
        $totalScore = (float) ($currentYearData->total_score ?? 0);
        $maxScore = (float) ($currentYearData->max_score ?? 0);
        $displayYearText = $displayYear ? (string) $displayYear : 'ไม่มีข้อมูล';

        // 4) Indicators สำหรับแสดงในตาราง
        $indicators = Indicator::query()
            ->whereHas('assignments')
            ->with([
                'category:id,name,standard_id',
                'category.standard:id,name',
                'assignments.collectorUser' => fn($q) => $q->select('id', 'first_name', 'last_name', 'department_id'),
                'assignments.collectorUser.department:id,name',
            ])
            ->withCount(['criterias as criteria_count', 'evidences as evidence_count'])
            ->orderBy('indicators.year', 'asc')
            ->orderByRaw("
            CASE
                WHEN indicators.code LIKE 'NCS-%' THEN 1
                WHEN indicators.code LIKE 'NCP-%' THEN 2
                WHEN indicators.code LIKE 'NCO-%' THEN 3
                ELSE 99
            END
        ")
            ->orderByRaw("COALESCE(NULLIF(SPLIT_PART(indicators.code, '-', 2), ''), '0')::int ASC")
            ->orderBy('indicators.code', 'asc')
            ->get()
            ->map(function ($indicator) {
                $criteriaCount = (int) ($indicator->criteria_count ?? 0);
                $evidenceCount = (int) ($indicator->evidence_count ?? 0);
                $indicator->status_doc = $evidenceCount === 0
                    ? 'รอดำเนินการ'
                    : ($evidenceCount < $criteriaCount ? 'ไม่ครบ' : 'ครบ');
                return $indicator;
            });

        // 5) นับสถานะตัวชี้วัด
        $indicatorsForStatus = Indicator::query()
            ->whereHas('assignments')
            ->whereNotNull('year')
            ->get();

        $statusCounts = [
            'complete'   => $indicatorsForStatus->where('status', 3)->count(),
            'incomplete' => $indicatorsForStatus->where('status', 4)->count(),
            'pending' => $indicatorsForStatus->whereIn('status', [0, 1, 2])->count(),

        ];

        // 6) Legend Config
        $legendConfig = [
            ['key' => 'complete', 'label' => 'ผลการดำเนินงานครบถ้วนตามเกณฑ์มาตรการ', 'color' => '#22c55e'],
            ['key' => 'incomplete', 'label' => 'ผลการดำเนินงานยังไม่ครบถ้วนตามเกณฑ์', 'color' => '#facc15'],
            ['key' => 'pending', 'label' => 'อยู่ระหว่างดำเนินการ', 'color' => '#ef4444'],
        ];

        // 7) Dropdown filters
        $allStandards = Standard::orderBy('name')->get(['id', 'name']);
        
        $departments    = Department::orderBy('name')->pluck('name');
        $collectors     = User::query()
            ->whereIn('id', function ($q) {
                $q->select('collector')->from('assignments');
            })
            ->orderBy('first_name')
            ->get()
            ->pluck('display_name');
        // Unique dimension/aspect names for dropdown (6–7 choices, no duplicates)
        $dimensionNames = Category::query()
            ->whereNotNull('name')
            ->pluck('name')
            ->map(fn($n) => trim($n))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();


        $dimensionStats = Category::select('id', 'name', 'standard_id')
            ->with('standard:id,name')
            ->withCount(['indicators as total_items' => fn($q) => $q->whereHas('assignments')])
            ->orderBy('name')
            ->get();

        // 8) ปีล่าสุด 5 ปี
        $allYears   = $yearsForFilter->toArray();
        $last5Years = array_slice($allYears, max(0, count($allYears) - 5));

        // 9) Filters สำหรับ codes และ types
        $codes = Indicator::query()
            ->whereNotNull('code')
            ->select('code')
            ->distinct()
            ->orderBy('code')
            ->pluck('code')
            ->toArray();

        $types = Indicator::query()
            ->whereNotNull('type')
            ->select('type')
            ->distinct()
            ->orderBy('type')
            ->pluck('type')
            ->toArray();

        $filters = [
            'codes' => $codes,
            'types' => $types,
        ];

        // ===== RETURN VIEW =====
        return view('dashboard.app', compact(
            'indicators',
            'totalScore',
            'maxScore',
            'displayYear',
            'displayYearText',
            'statusCounts',
            'legendConfig',
            'allStandards',
            'dimensionNames',
            'departments',
            'collectors',
            'dimensionStats',
            'yearlyTotals',
            'yearsForFilter',
            'last5Years',
            'indicatorCount',
            'filters'
        ));
    }

    public function getData()
    {
        // -------- 0) รับปีจากหลายชื่อฟิลด์ เผื่อฟอร์มตั้งชื่อไม่ตรง --------
        $req = request();
        $pickedYearRaw = $req->input(
            'year',
            $req->input(
                'filter-year',
                $req->input('assessment_year', '')
            )
        );

        $pickedYear = null;
        if ($pickedYearRaw !== null && trim((string) $pickedYearRaw) !== '') {
            $s = trim((string) $pickedYearRaw);
            if (!in_array(strtolower($s), ['ทั้งหมด', 'all', '*', '0'], true)) {
                $pickedYear = (int) $s;
            }
        }

        // -------- 1) ช่วงปีที่ต้องแสดง (ดึงจาก DB จริง) --------
        if (is_null($pickedYear)) {
            $yearRange = Indicator::whereNotNull('year')
                ->distinct()
                ->orderBy('year')
                ->pluck('year')
                ->map(fn($y) => (int) $y)
                ->toArray();
        } else {
            $yearRange = [$pickedYear];
        }
        $yearRangeStr = array_map('strval', $yearRange);

        // -------- 2) รายชื่อมาตรฐาน --------
        $standards = Standard::select('id', 'name')->get();

        // -------- 3) ดึงข้อมูลตามช่วงปี --------
        $rows = Indicator::query()
            ->join('categories', 'categories.id', '=', 'indicators.categorie_id')
            ->join('standards', 'standards.id', '=', 'categories.standard_id')
            ->whereHas('assignments')
            ->whereNotNull('indicators.year')
            ->when(
                !is_null($pickedYear),
                fn($q) => $q->where('indicators.year', $pickedYear),
                fn($q) => $q->whereIn('indicators.year', $yearRange)
            )
            ->selectRaw('
        standards.id   as standard_id,
        standards.name as standard_name,
        categories.id  as category_id,
        categories.name as category_name,
        indicators.id  as indicator_id,
        indicators.name as indicator_name,
        indicators.code as indicator_code,
        indicators.type as indicator_type,
        indicators.year,
        SUM(indicators.score_acc) as total_score,
        SUM(indicators.max_score) as max_score
     ')
            ->groupBy(
                'standards.id',
                'standards.name',
                'categories.id',
                'categories.name',
                'indicators.id',
                'indicators.name',
                'indicators.code',
                'indicators.type',
                'indicators.year'
            )
            ->orderBy('standards.id')
            ->orderBy('indicators.code')
            ->orderBy('indicators.year')
            ->get();


        // -------- 4) ฟังก์ชัน normalize รหัส indicator --------
        $normalize = function ($c) {
            $c = strtoupper(trim((string) $c));
            $c = preg_replace('/\s+/', '', $c);
            if (preg_match('/^([A-Z]+)[\s_\-]?0*(\d+)$/', $c, $m))
                return $m[1] . '-' . (int) $m[2];
            return $c;
        };

        $prefixOrder = ['NCS' => 0, 'NCP' => 1, 'NCO' => 2];

        // -------- 5) รวมกราฟด้วย indicator_code --------
        $chartsByStandard = [];
        foreach ($rows as $r) {
            $sid = $r->standard_id;
            $code = $normalize($r->indicator_code);

            $chartsByStandard[$sid] ??= [
                'standard_id'   => $sid,
                'standard_name' => $r->standard_name,
                'indicators'    => [],
            ];
            $chartsByStandard[$sid]['indicators'][$code] ??= [
                'indicator_id'   => $r->indicator_id,
                'indicator_key'  => $code,
                'indicator_code' => $code,
                'indicator_name' => $r->indicator_name,
                'indicator_type' => $r->indicator_type,
                'category_id'    => $r->category_id,
                'category_name'  => $r->category_name,
                'years'          => [],
                'values'         => [],
                'max_values'     => [], // ✅ เพิ่มตรงนี้
            ];

            $cb = &$chartsByStandard[$sid]['indicators'][$code];
            $cb['years'][]      = (string) $r->year;
            $cb['values'][]     = (float) $r->total_score;
            $cb['max_values'][] = (float) $r->max_score; // ✅ เก็บ max

        }

        // -------- 6) บังคับ labels/data และ sort indicators --------
        foreach ($chartsByStandard as $sid => &$bucket) {
            foreach ($bucket['indicators'] as &$ind) {
                $mapScore = [];
                $mapMax   = [];
                foreach ($ind['years'] as $i => $y) {
                    $yy = (string) $y;
                    $mapScore[$yy] = ($mapScore[$yy] ?? 0) + (float) $ind['values'][$i];
                    $mapMax[$yy]   = ($mapMax[$yy] ?? 0) + (float) $ind['max_values'][$i];
                }
                $ind['years']      = array_values($yearRangeStr);
                $ind['values']     = array_map(fn($y) => (float) ($mapScore[$y] ?? 0), $yearRangeStr);
                $ind['max_values'] = array_map(fn($y) => (float) ($mapMax[$y] ?? 0), $yearRangeStr);
            }


            // ✅ sort indicators ตาม indicator_code
            usort($bucket['indicators'], function ($a, $b) use ($prefixOrder) {
                preg_match('/^([A-Z]+)-(\d+)$/', $a['indicator_code'], $ma);
                preg_match('/^([A-Z]+)-(\d+)$/', $b['indicator_code'], $mb);
                $pa = $ma[1] ?? $a['indicator_code'];
                $pb = $mb[1] ?? $b['indicator_code'];
                $ra = $prefixOrder[$pa] ?? 999;
                $rb = $prefixOrder[$pb] ?? 999;
                if ($ra !== $rb) return $ra <=> $rb;
                if ($pa !== $pb) return strcmp($pa, $pb);
                $na = isset($ma[2]) ? (int) $ma[2] : PHP_INT_MAX;
                $nb = isset($mb[2]) ? (int) $mb[2] : PHP_INT_MAX;
                return $na <=> $nb;
            });
        }
        unset($bucket);

        // -------- 7) Filters สำหรับดรอปดาวน์ --------
        $filterYears = Indicator::whereNotNull('year')
            ->distinct()
            ->orderBy('year')
            ->pluck('year')
            ->map(fn($y) => (string) $y)
            ->toArray();

        if (!is_null($pickedYear) && !in_array((string) $pickedYear, $filterYears, true)) {
            $filterYears[] = (string) $pickedYear;
            sort($filterYears);
        }

        $allStandards = Standard::query()
            ->select('id', 'name')->orderBy('name')->get()
            ->map(fn($s) => ['id' => $s->id, 'name' => trim($s->name)])->toArray();

        $allDimensions = Category::query()->whereNotNull('name')->pluck('name')
            ->map(fn($n) => trim($n))->filter()->unique()->sort()->values()->toArray();

        $allTypes = Indicator::query()->whereNotNull('type')->pluck('type')
            ->map(fn($t) => trim($t))->filter()->unique()->sort()->values()->toArray();

        $codesRaw = Indicator::query()->whereNotNull('code')->pluck('code')->toArray();
        $codesNormalized = array_map($normalize, $codesRaw);
        $codesUnique = array_values(array_unique(array_filter($codesNormalized)));
        usort($codesUnique, function ($a, $b) use ($prefixOrder) {
            preg_match('/^([A-Z]+)-(\d+)$/', $a, $ma);
            preg_match('/^([A-Z]+)-(\d+)$/', $b, $mb);
            $pa = $ma[1] ?? $a;
            $pb = $mb[1] ?? $b;
            $ra = $prefixOrder[$pa] ?? 999;
            $rb = $prefixOrder[$pb] ?? 999;
            if ($ra !== $rb) return $ra <=> $rb;
            if ($pa !== $pb) return strcmp($pa, $pb);
            $na = isset($ma[2]) ? (int) $ma[2] : PHP_INT_MAX;
            $nb = isset($mb[2]) ? (int) $mb[2] : PHP_INT_MAX;
            return $na <=> $nb;
        });

        $filters = [
            'years'      => $filterYears,
            'codes'      => $codesUnique,
            'standards'  => $allStandards,
            'dimensions' => $allDimensions,
            'types'      => $allTypes,
            'pickedYear' => $pickedYear,
            'range'      => ['start' => reset($yearRange), 'end' => end($yearRange)],
        ];
        $yearlyTotals = Indicator::query()
            ->whereHas('assignments')
            ->whereNotNull('year')
            ->selectRaw('CAST(year AS INTEGER) as year, SUM(score_acc) as total_score, SUM(max_score) as max_score')
            ->groupBy('year')
            ->orderBy('year')
            ->get()
            ->map(fn($r) => [
                'year'  => (int) $r->year,
                'score' => (float) $r->total_score,
                'max'   => (float) $r->max_score,
            ])
            ->toArray();


        return view('dashboard.result', [
            'standards'        => $standards,
            'chartsByStandard' => $chartsByStandard,
            'filters'          => $filters,
            'yearlyTotals'     => $yearlyTotals,
            'chartsStandardBars' => $this->buildChartStandardsPerStandard($standards),
            'chartDimensions'  => $this->buildChartDimensionsPerYear(),
        ]);
    }
    private function buildChartStandardsPerStandard($standards)
    {
        $allYears = Indicator::whereNotNull('year')
            ->distinct()
            ->orderBy('year')
            ->pluck('year')
            ->map(fn($y) => (int) $y)
            ->toArray();

        $rows = Indicator::query()
            ->join('categories', 'categories.id', '=', 'indicators.categorie_id')
            ->join('standards', 'standards.id', '=', 'categories.standard_id')
            ->whereHas('assignments')
            ->whereNotNull('indicators.year')
            ->selectRaw('
            CAST(indicators.year AS INTEGER) as year,
            standards.id as sid,
            SUM(indicators.score_acc) as total_score,
            SUM(indicators.max_score) as max_score
        ')
            ->groupByRaw('CAST(indicators.year AS INTEGER), standards.id')
            ->orderByRaw('CAST(indicators.year AS INTEGER)')
            ->get();

        // เตรียม matrix
        $series = [];
        foreach ($standards as $std) {
            foreach ($allYears as $y) {
                $series[$std->id][$y] = ['score' => 0.0, 'max' => 0.0];
            }
        }
        foreach ($rows as $r) {
            $series[$r->sid][$r->year] = [
                'score' => (float) $r->total_score,
                'max'   => (float) $r->max_score,
            ];
        }

        // ส่งออก
        $charts = [];
        foreach ($standards as $i => $std) {
            $charts[] = [
                'id' => $std->id,
                'name' => $std->name,
                'labels' => $allYears,
                'scores' => array_column($series[$std->id], 'score'),
                'max'    => array_column($series[$std->id], 'max'),
            ];
        }

        return $charts;
    }
    private function buildChartDimensionsPerYear()
    {
        // ปีทั้งหมด
        $allYears = Indicator::whereNotNull('year')
            ->distinct()
            ->orderBy('year')
            ->pluck('year')
            ->map(fn($y) => (int) $y)
            ->toArray();

        // ✅ ดึงรวมตาม "ด้าน" (categories.name) แทน category ย่อย
        $rows = Indicator::query()
            ->join('categories', 'categories.id', '=', 'indicators.categorie_id')
            ->whereHas('assignments')
            ->whereNotNull('indicators.year')
            ->selectRaw('
            CAST(indicators.year AS INTEGER) as year,
            categories.name as dim_name,
            SUM(indicators.score_acc) as total_score,
            SUM(indicators.max_score) as max_score
        ')
            ->groupByRaw('CAST(indicators.year AS INTEGER), categories.name')
            ->orderByRaw('CAST(indicators.year AS INTEGER)')
            ->get();

        // เตรียม matrix [ด้าน][ปี]
        $series = [];
        $dimNames = [];

        foreach ($rows as $r) {
            $dim = trim($r->dim_name);
            $dimNames[$dim] = $dim;
            foreach ($allYears as $y) {
                $series[$dim][$y] ??= ['score' => 0.0, 'max' => 0.0];
            }
            $series[$dim][$r->year] = [
                'score' => (float) $r->total_score,
                'max'   => (float) $r->max_score,
            ];
        }

        // สร้าง chart object
        $charts = [];
        foreach ($series as $dim => $data) {
            $charts[] = [
                'id'     => md5($dim),
                'name'   => $dim,
                'labels' => $allYears,
                'scores' => array_column($data, 'score'),
                'max'    => array_column($data, 'max'),
            ];
        }

        return $charts;
    }
}
