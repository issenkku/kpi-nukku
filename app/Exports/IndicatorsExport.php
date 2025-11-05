<?php

// app/Exports/IndicatorsExport.php
namespace App\Exports;

use App\Models\Indicator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Events\AfterSheet;

class IndicatorsExport implements FromCollection, WithEvents
{
    private $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        // คืน collection ว่าง เพื่อให้เราควบคุม output ใน AfterSheet
        return new Collection([]);
    }

    private function getData()
    {
        $q = Indicator::query()
            ->select([
                'indicators.id',
                'indicators.name',
                'indicators.code',
                'indicators.year',
                'categories.name as category_name',
                'standards.name as standard_name'
            ])
            ->leftJoin('categories', 'categories.id', '=', 'indicators.categorie_id')
            ->leftJoin('standards', 'standards.id', '=', 'categories.standard_id');

        // ===== ฟิลเตอร์ =====
        if (!empty($this->filters['year'])) {
            $years = (array) $this->filters['year'];
            $q->whereIn('indicators.year', $years);
        }

        if (!empty($this->filters['standard_id'])) {
            $std = $this->filters['standard_id'];
            if (is_numeric($std) && (int)$std > 0) {
                $q->where('categories.standard_id', (int)$std);
            } else {
                // Fallback: allow passing standard name (string)
                $q->where('standards.name', (string) $std);
            }
        }

        // Category filter: support NAME grouping across standards and legacy ID
        $categoryName = $this->filters['category'] ?? $this->filters['category_name'] ?? null;
        if (!empty($categoryName)) {
            $names = (array) $categoryName;
            $names = array_values(array_filter(array_map(fn($v) => trim((string)$v), $names)));
            if (!empty($names)) {
                $q->whereIn('categories.name', $names);
            }
        } elseif (array_key_exists('category_id', $this->filters) && $this->filters['category_id'] !== null && $this->filters['category_id'] !== '') {
            $cat = $this->filters['category_id'];
            if (is_array($cat)) {
                $ids = [];
                $names = [];
                foreach ($cat as $c) {
                    if (is_numeric($c)) $ids[] = (int) $c; else $names[] = trim((string)$c);
                }
                $q->where(function($qq) use ($ids, $names) {
                    if (!empty($ids)) {
                        $qq->orWhereIn('indicators.categorie_id', $ids);
                    }
                    if (!empty($names)) {
                        $qq->orWhereIn('categories.name', $names);
                    }
                });
            } else {
                if (is_numeric($cat) && (int)$cat > 0) {
                    $q->where('indicators.categorie_id', (int)$cat);
                } else {
                    $q->where('categories.name', trim((string)$cat));
                }
            }
        }

        if (isset($this->filters['status']) && $this->filters['status'] !== '' && (int)$this->filters['status'] >= 0) {
            $q->where('indicators.status', (int)$this->filters['status']);
        }

        if (!empty($this->filters['code'])) {
            $code = trim(strtolower($this->filters['code']));
            if (str_contains($code, '%')) {
                $q->where('indicators.code', 'ILIKE', $code);
            } else {
                $q->whereRaw('LOWER(indicators.code) = ?', [$code]);
            }
        }
        // dd($this->filters, $q->toSql(), $q->getBindings());
        return $q->get()->groupBy('standard_name');
    }



    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $e) {
                $s = $e->sheet->getDelegate();
                $standards = $this->getData();

                // ลำดับด้านที่ fix
                $categoryOrder = [
                    'ด้านองค์กรและการบริหารองค์กร',
                    'ด้านบุคลากร',
                    'ด้านการจัดการศึกษา',
                    'ด้านการวิจัยและนวัตกรรมและผลผลิตทางวิชาการ',
                    'ด้านการบริการวิชาการ/วิชาชีพแก่สังคม',
                    'ด้านการทำนุบำรุงศิลปะและวัฒนธรรม',
                    'ด้านนิสิตและนักศึกษา',
                ];

                $row = 1;

                foreach ($standards as $stdName => $cats) {
                    // ===== หัวมาตรฐาน =====
                    $s->mergeCells("A{$row}:F{$row}")
                        ->setCellValue("A{$row}", "มาตรฐาน: {$stdName}");
                    $s->getStyle("A{$row}")->getFont()->setBold(true);
                    $row++;

                    // ===== loop ด้านตามลำดับ fix =====
                    foreach ($categoryOrder as $catName) {
                        $inds = $cats->where('category_name', $catName);

                        // หัวด้าน
                        $s->mergeCells("A{$row}:F{$row}")
                            ->setCellValue("A{$row}", "ด้าน: {$catName}");
                        $row++;

                        // หัวตาราง (ปิดใช้งานระดับด้าน; จะสร้างต่อ-ตัวชี้วัดแทน)
                        if (false) {
                            $s->mergeCells("A{$row}:A" . ($row + 1))->setCellValue("A{$row}", 'ข้อ');
                            $s->mergeCells("B{$row}:B" . ($row + 1))->setCellValue("B{$row}", 'เกณฑ์มาตรฐาน');
                            $s->mergeCells("C{$row}:D{$row}")->setCellValue("C{$row}", 'ผลการดำเนินงาน');
                            $s->setCellValue("C" . ($row + 1), 'มี');
                            $s->setCellValue("D" . ($row + 1), 'ไม่มี');
                            $s->mergeCells("E{$row}:E" . ($row + 1))->setCellValue("E{$row}", 'รายงานผลการดำเนินงาน');
                            $s->mergeCells("F{$row}:F" . ($row + 1))->setCellValue("F{$row}", 'เอกสาร/หลักฐาน');
                            $row += 2;
                        }

                        // เติม Indicators ถ้ามี
                        $i = 1;
                        if ($inds->count() > 0) {
                            // preload criterias (ordered) and evidences (approved only)
                            $ids = $inds->pluck('id')->unique()->values();
                            $relations = \App\Models\Indicator::with([
                                'criterias' => function ($q) {
                                    $q->orderBy('sequence')->orderBy('name');
                                },
                                'criterias.evidences', // ดึงมาหมด
                            ])->whereIn('id', $ids)->get()->keyBy('id');

                            // helper: extract plain text from evidence.detail for a criteria
                            $extractDetail = function ($criteria) {
                                try {
                                    if ($criteria && $criteria->relationLoaded('evidences')) {
                                        foreach ($criteria->evidences as $ev) {
                                            $html = (string) ($ev->detail ?? '');
                                            $htmlTrim = trim(strip_tags(html_entity_decode($html)));
                                            if ($htmlTrim !== '') {
                                                $text = preg_replace('/[\x{00A0}\s]+/u', ' ', $htmlTrim);
                                                return trim((string) $text);
                                            }
                                        }
                                    }
                                } catch (\Throwable $e) {}
                                return '';
                            };

                            // แล้วค่อย filter ตอนใช้งาน



                            foreach ($inds as $ind) {
                                // ===== หัวตัวชี้วัด =====
                                $title = trim(($ind->code ? "[{$ind->code}] " : '') . ($ind->name ?? ''));
                                $s->mergeCells("A{$row}:F{$row}")->setCellValue("A{$row}", $title);
                                $s->getStyle("A{$row}")->getFont()->setBold(true);
                                $row++;

                                // ===== หัวตารางต่อ-ตัวชี้วัด =====
                                $s->mergeCells("A{$row}:A" . ($row + 1))->setCellValue("A{$row}", 'ข้อ');
                                $s->mergeCells("B{$row}:B" . ($row + 1))->setCellValue("B{$row}", 'เกณฑ์มาตรฐาน');
                                $s->mergeCells("C{$row}:D{$row}")->setCellValue("C{$row}", 'ผลการดำเนินงาน');
                                $s->setCellValue("C" . ($row + 1), 'มี');
                                $s->setCellValue("D" . ($row + 1), 'ไม่มี');
                                $s->mergeCells("E{$row}:E" . ($row + 1))->setCellValue("E{$row}", 'รายงานผลการดำเนินงาน');
                                $s->mergeCells("F{$row}:F" . ($row + 1))->setCellValue("F{$row}", 'เอกสาร/หลักฐาน');
                                $row += 2;

                                // ===== แสดงรายการเกณฑ์ (Criteria) ของตัวชี้วัดนี้ =====
                                $rel = $relations[$ind->id] ?? null;
                                $criterias = ($rel && $rel->relationLoaded('criterias')) ? $rel->criterias : collect();
                                $ci = 1;
                                if ($criterias->isEmpty()) {
                                    $s->setCellValue("A{$row}", '-');
                                    $s->mergeCells("B{$row}:F{$row}")->setCellValue("B{$row}", 'ไม่มีข้อมูล');
                                    $row++;
                                }

                                foreach ($criterias as $c) {
                                    $critRow = $row; // remember row for this criteria
                                    // --- เก็บค่าหลัก ๆ ของ criteria ---
                                    $s->setCellValue("A{$row}", $ci);
                                    $s->setCellValue("B{$row}", $c->name ?? '');

                                    $has = (bool) ($c->status ?? false);
                                    $s->setCellValue("C{$row}", $has ? '✓' : '');
                                    $s->setCellValue("D{$row}", $has ? '' : '✓');
                                    // Column E: รายงานผลการดำเนินงาน (จาก evidence.detail)
                                    $detailText = $extractDetail($c);
                                    $s->setCellValue("E{$row}", $detailText);

                                    // --- ถ้ามี evidences ---
                                    if ($has && $c->evidences->isNotEmpty()) {
                                        $first = true;
                                        foreach ($c->evidences as $ev) {
                                            if (!$first) {
                                                $row++;
                                                // ✅ merge A–E ให้ต่อเนื่องเหมือน criteria เดิม
                                                $s->mergeCells("A{$row}:E{$row}");
                                            }

                                            $s->setCellValue("F{$row}", $ev->name ?: 'Evidence');
                                            try {
                                                $url = route('evidences.download', ['id' => $ev->id]);
                                                $s->getCell("F{$row}")->getHyperlink()->setUrl($url);
                                                $s->getStyle("F{$row}")->getFont()->getColor()->setARGB('FF0000FF');
                                                $s->getStyle("F{$row}")->getFont()->setUnderline(true);
                                            } catch (\Throwable $ex) {
                                            }

                                            $first = false;
                                        }
                                    } else {
                                        // --- ถ้าไม่มี evidences หรือยังไม่อนุมัติ ---
                                        $s->setCellValue("F{$row}", '-');
                                    }

                                    // Re-assert detail text to guard against later merges overwriting E cell
                                    if (isset($detailText) && $detailText !== '') {
                                        try { $s->setCellValue("E{$critRow}", $detailText); } catch (\Throwable $ex) {}
                                    }
                                    $row++;
                                    $ci++;
                                }

                                // ===== เกณฑ์การให้คะแนน + การประเมินตนเอง (ท้ายตัวชี้วัด) =====
                                $start2 = $row + 1;
                                $s->mergeCells("A{$start2}:D{$start2}")->setCellValue("A{$start2}", 'เกณฑ์การให้คะแนน');
                                $s->mergeCells("E{$start2}:F{$start2}")->setCellValue("E{$start2}", 'การประเมินตนเอง');

                                $r = $start2 + 1;
                                // ใช้ comment จาก Indicator ฉบับเต็มที่ preload ไว้ใน $rel (จะมีทุกคอลัมน์)
                                $rawComment = $rel->comment ?? ($ind->comment ?? '');
                                // แปลง HTML → ข้อความธรรมดา และ decode entities (&nbsp; ฯลฯ)
                                $commentText = html_entity_decode(strip_tags((string) $rawComment));
                                // จัดช่องว่างให้สะอาด และ trim
                                $commentText = preg_replace('/[\x{00A0}\s]+/u', ' ', $commentText ?? '');
                                $commentText = trim((string) $commentText);
                                if ($commentText === '') {
                                    $commentText = '............................';
                                }
                                $s->mergeCells("A{$r}:C{$r}")->setCellValue("A{$r}", $commentText);
                                $s->setCellValue("D{$r}", '........... คะแนน');
                                $s->mergeCells("E{$r}:F{$r}")->setCellValue("E{$r}", '');
                                for ($k = 0; $k < 0; $k++) {
                                    $r++;
                                    $s->mergeCells("A{$r}:C{$r}")->setCellValue("A{$r}", '............................');
                                    $s->setCellValue("D{$r}", '........... คะแนน');
                                    $s->mergeCells("E{$r}:F{$r}")->setCellValue("E{$r}", $k === 1 ? '✓' : '');
                                }
                                // แปลง comment ที่เป็น <li> ให้แสดงแยกแถว
                                // ถ้าไม่มีรายการ <li> ให้กรอกคะแนนลงแถวแรกและติ๊กถูก
                                if (stripos((string) $rawComment, '<li') === false) {
                                    $r1 = $start2 + 1;
                                    $score = (float) (($rel->score_acc ?? null) ?? ($ind->score_acc ?? 0));
                                    $scoreText = rtrim(rtrim(number_format($score, 2, '.', ''), '0'), '.');
                                    try {
                                        $s->setCellValue("D{$r1}", ($scoreText === '' ? '0' : $scoreText) . ' คะแนน');
                                        $s->mergeCells("E{$r1}:F{$r1}")->setCellValue("E{$r1}", '✓');
                                    } catch (\Throwable $ex) {
                                    }
                                }
                                $liItems = [];
                                if (is_string($rawComment) && stripos($rawComment, '<li') !== false) {
                                    if (preg_match_all('/<li[^>]*>(.*?)<\/li>/si', (string) $rawComment, $m)) {
                                        foreach ($m[1] as $it) {
                                            $txt = html_entity_decode(strip_tags($it));
                                            $txt = preg_replace('/[\x{00A0}\s]+/u', ' ', $txt ?? '');
                                            $txt = trim((string) $txt);
                                            if ($txt !== '') {
                                                $liItems[] = $txt;
                                            }
                                        }
                                    }
                                }
                                if (!empty($liItems)) {
                                    $writeRow = $start2 + 1;
                                    foreach ($liItems as $txt) {
                                        $scoreText = '';
                                        if (preg_match('/\(\s*(?:([0-9]+(?:\.[0-9]+)?)\s*คะแนน|คะแนน\s*([0-9]+(?:\.[0-9]+)?)|([0-9]+(?:\.[0-9]+)?))\s*\)/u', $txt, $mm)) {
                                            $vals = array_values(array_filter([
                                                $mm[1] ?? null,
                                                $mm[2] ?? null,
                                                $mm[3] ?? null,
                                            ]));
                                            if (!empty($vals)) {
                                                $scoreText = rtrim(rtrim(number_format((float)$vals[0], 2, '.', ''), '0'), '.');
                                            }
                                        } elseif (preg_match('/([0-9]+(?:\.[0-9]+)?)/', $txt, $mm)) {
                                            $scoreText = rtrim(rtrim(number_format((float)$mm[1], 2, '.', ''), '0'), '.');
                                        }
                                        if ($writeRow > $r) {
                                            $s->mergeCells("A{$writeRow}:C{$writeRow}")->setCellValue("A{$writeRow}", $txt);
                                            $s->setCellValue("D{$writeRow}", '........... คะแนน');
                                            $s->mergeCells("E{$writeRow}:F{$writeRow}")->setCellValue("E{$writeRow}", '');
                                            $r = $writeRow;
                                        } else {
                                            $s->mergeCells("A{$writeRow}:C{$writeRow}")->setCellValue("A{$writeRow}", $txt);
                                            $s->setCellValue("D{$writeRow}", '........... คะแนน');
                                            $s->mergeCells("E{$writeRow}:F{$writeRow}")->setCellValue("E{$writeRow}", '');
                                        }
                                        // override placeholder score with parsed value if available
                                        try {
                                            $s->setCellValue("D{$writeRow}", ($scoreText === '' ? '...........' : $scoreText) . ' คะแนน');
                                        } catch (\Throwable $ex) {}
                                        $writeRow++;
                                    }
                                }

                                // เติมคะแนนและติ๊กถูกตามเกณฑ์จากรายการ li
                                if (!empty($liItems)) {
                                    $score = (float) (($relations[$ind->id]->score_acc ?? null) ?? ($ind->score_acc ?? 0));
                                    $scoreClean = (float) number_format($score, 2, '.', '');
                                    $rowPtr = $start2 + 1;
                                    foreach ($liItems as $txt) {
                                        $liScore = null;
                                        if (preg_match('/\(\s*(?:([0-9]+(?:\.[0-9]+)?)\s*คะแนน|คะแนน\s*([0-9]+(?:\.[0-9]+)?)|([0-9]+(?:\.[0-9]+)?))\s*\)/u', $txt, $mm)) {
                                            $vals = array_values(array_filter([
                                                $mm[1] ?? null,
                                                $mm[2] ?? null,
                                                $mm[3] ?? null,
                                            ]));
                                            if (!empty($vals)) {
                                                $liScore = (float) $vals[0];
                                            }
                                        }
                                        if ($liScore === null) {
                                        if (preg_match('/\(?\s*([0-9]+(?:\.[0-9]+)?)\s*\)?\s*คะแนน/u', $txt, $mm)) {
                                            $liScore = (float) $mm[1];
                                        } elseif (preg_match('/([0-9]+(?:\.[0-9]+)?)/', $txt, $mm)) {
                                            $liScore = (float) $mm[1];
                                        }
                                        }
                                        $match = ($liScore !== null) && (abs($liScore - $scoreClean) < 0.001);
                                        if ($match) {
                                            $s->setCellValue("D{$rowPtr}", rtrim(rtrim(number_format($score, 2, '.', ''), '0'), '.') . ' คะแนน');
                                            $s->mergeCells("E{$rowPtr}:F{$rowPtr}")->setCellValue("E{$rowPtr}", '✓');
                                        }
                                        $rowPtr++;
                                    }
                                }

                                $s->getStyle("A{$start2}:F{$r}")->applyFromArray([
                                    'borders' => [
                                        'allBorders' => [
                                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                        ],
                                    ],
                                    'alignment' => [
                                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                                        'wrapText' => true,
                                    ],
                                ]);

                                $row = $r + 2;
                                $i++;
                                continue; // ข้ามโค้ดแสดงแถวตัวชี้วัดแบบเดิม
                                $s->setCellValue("A{$row}", $i);
                                $s->setCellValue("B{$row}", $ind->name ?? '');
                                $s->setCellValue("C{$row}", $ind->status == 'yes' ? '✓' : '');
                                $s->setCellValue("D{$row}", '');
                                // คอลัมน์ E: รายงานผลการดำเนินงาน (ปล่อยว่างให้กรอกทีหลัง)
                                $s->setCellValue("E{$row}", '');

                                // คอลัมน์ F: หลักฐานที่อนุมัติแล้วเป็นลิงก์ดาวน์โหลด; ถ้ามีหลายรายการให้เพิ่มแถว
                                $rel = $relations[$ind->id] ?? null;
                                $approved = collect();
                                if ($rel && $rel->relationLoaded('evidences')) {
                                    $approved = $rel->evidences->values();
                                }
                                if ($approved->isEmpty()) {
                                    $s->setCellValue("F{$row}", '-');
                                } else {
                                    $first = $approved->first();
                                    $s->setCellValue("F{$row}", $first->name ?: 'Evidence');
                                    try {
                                        $url = route('evidences.download', ['id' => $first->id]);
                                        $s->getCell("F{$row}")->getHyperlink()->setUrl($url);
                                        $s->getStyle("F{$row}")->getFont()->getColor()->setARGB('FF0000FF');
                                        $s->getStyle("F{$row}")->getFont()->setUnderline(true);
                                    } catch (\Throwable $ex) {
                                    }

                                    foreach ($approved->slice(1) as $ev) {
                                        $row++;
                                        $s->setCellValue("A{$row}", '');
                                        $s->setCellValue("B{$row}", '');
                                        $s->setCellValue("C{$row}", '');
                                        $s->setCellValue("D{$row}", '');
                                        $s->setCellValue("E{$row}", '');
                                        $s->setCellValue("F{$row}", $ev->name ?: 'Evidence');
                                        try {
                                            $url = route('evidences.download', ['id' => $ev->id]);
                                            $s->getCell("F{$row}")->getHyperlink()->setUrl($url);
                                            $s->getStyle("F{$row}")->getFont()->getColor()->setARGB('FF0000FF');
                                            $s->getStyle("F{$row}")->getFont()->setUnderline(true);
                                        } catch (\Throwable $ex) {
                                        }
                                    }
                                }

                                // ===== เกณฑ์การให้คะแนน + การประเมินตนเอง (ต่อ-ตัวชี้วัด) =====
                                $start = $row + 1;
                                $s->mergeCells("A{$start}:D{$start}")->setCellValue("A{$start}", 'เกณฑ์การให้คะแนน');
                                $s->mergeCells("E{$start}:F{$start}")->setCellValue("E{$start}", 'การประเมินตนเอง');

                                // เก็บเกณฑ์จาก criterias ของตัวชี้วัดนี้
                                $criteriaTexts = [];
                                if ($rel && $rel->relationLoaded('criterias')) {
                                    foreach ($rel->criterias as $c) {
                                        $seq = trim((string)($c->sequence ?? ''));
                                        $label = $c->name ?: '';
                                        $criteriaTexts[] = ($seq !== '' ? "{$seq}. " : '') . $label;
                                    }
                                }
                                if (empty($criteriaTexts)) {
                                    $criteriaTexts = [];
                                }

                                foreach ($criteriaTexts as $txt) {
                                    $row++;
                                    $s->mergeCells("A{$row}:C{$row}")->setCellValue("A{$row}", $txt);
                                    $s->setCellValue("D{$row}", '........... คะแนน');
                                    $s->mergeCells("E{$row}:F{$row}")->setCellValue("E{$row}", '');
                                }

                                // สไตล์กรอบของบล็อคคะแนน (รวมหัว)
                                $firstRow = $start;
                                $lastRow  = $row;
                                $s->getStyle("A{$firstRow}:F{$lastRow}")->applyFromArray([
                                    'borders' => [
                                        'allBorders' => [
                                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                        ],
                                    ],
                                    'alignment' => [
                                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                                        'wrapText' => true,
                                    ],
                                ]);

                                // เว้นบรรทัดก่อนตัวชี้วัดถัดไป
                                $row = $lastRow + 2;
                                $i++;
                            }
                        } else {
                            // ถ้าไม่มี indicator → แสดง "ไม่มีข้อมูล"
                            $s->setCellValue("A{$row}", '-');
                            $s->mergeCells("B{$row}:F{$row}")
                                ->setCellValue("B{$row}", 'ไม่มีข้อมูล');
                            $row++;
                        }

                        // แสดงเกณฑ์การให้คะแนนรายตัวชี้วัดแล้วด้านบน จึงข้ามบล็อคระดับด้านด้านล่าง
                        $row += 2;
                        continue;

                        // ===== เกณฑ์การให้คะแนน + การประเมินตนเอง =====
                        $start = $row + 1;
                        $s->mergeCells("A{$start}:D{$start}")->setCellValue("A{$start}", 'เกณฑ์การให้คะแนน');
                        $s->mergeCells("E{$start}:F{$start}")->setCellValue("E{$start}", 'การประเมินตนเอง');

                        // รวบรวมเกณฑ์การให้จาก Criteria ของตัวชี้วัดในด้านนี้ แทนที่จุดไข่ปลา
                        $criteriaTexts = [];
                        if (isset($relations) && $relations instanceof \Illuminate\Support\Collection) {
                            foreach ($inds as $ind) {
                                $rel = $relations[$ind->id] ?? null;
                                if ($rel && $rel->relationLoaded('criterias')) {
                                    foreach ($rel->criterias as $c) {
                                        $seq = trim((string)($c->sequence ?? ''));
                                        $label = $c->name ?: '';
                                        $criteriaTexts[] = ($seq !== '' ? "{$seq}. " : '') . $label;
                                    }
                                }
                            }
                        }
                        if (empty($criteriaTexts)) {
                            $criteriaTexts = [];
                        }
                        foreach ($criteriaTexts as $txt) {
                            $row++;
                            $s->mergeCells("A{$row}:C{$row}")->setCellValue("A{$row}", $txt);
                            $s->setCellValue("D{$row}", '........... คะแนน');
                            $s->mergeCells("E{$row}:F{$row}")->setCellValue("E{$row}", '');
                        }

                        // สไตล์กรอบของบล็อคคะแนน (รวมหัวเรื่อง)
                        $firstRow = $start; // หัวบล็อค
                        $lastRow  = $row;   // แถวสุดท้ายของรายการ
                        $s->getStyle("A{$firstRow}:F{$lastRow}")->applyFromArray([
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                ],
                            ],
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                                'wrapText' => true,
                            ],
                        ]);
                        // เว้นบรรทัดก่อนส่วนถัดไป
                        $row = $lastRow + 2;
                    }

                    $row += 2; // เว้นบรรทัดก่อนมาตรฐานใหม่
                }

                // ปรับความกว้างคอลัมน์
                $s->getColumnDimension('A')->setWidth(5);
                $s->getColumnDimension('B')->setWidth(50);
                $s->getColumnDimension('C')->setWidth(12);
                $s->getColumnDimension('D')->setWidth(12);
                $s->getColumnDimension('E')->setWidth(20);
                $s->getColumnDimension('F')->setWidth(25);

                // เส้นกรอบรวม
                $s->getStyle("A1:F{$row}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);
            },
        ];
    }
}
