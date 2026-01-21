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
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class IndicatorsExport implements FromCollection, WithEvents
{
    private $filters;
    private array $tempImageFiles = [];

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
        $q->orderBy('standards.name')
            ->orderByRaw("
                CASE
                    WHEN indicators.code LIKE 'NCS-%' THEN 1
                    WHEN indicators.code LIKE 'NCP-%' THEN 2
                    WHEN indicators.code LIKE 'NCO-%' THEN 3
                    ELSE 99
                END
            ")
            ->orderByRaw("COALESCE(NULLIF(SPLIT_PART(indicators.code, '-', 2), ''), '0')::int ASC")
            ->orderBy('indicators.code', 'asc');

        // dd($this->filters, $q->toSql(), $q->getBindings());
        return $q->get()->groupBy('standard_name');
    }

    private function getCriteriaReportHtml($criteria): string
    {
        $html = (string) ($criteria->report ?? '');
        if (trim(strip_tags(html_entity_decode($html, ENT_QUOTES, 'UTF-8'))) !== '') {
            return $html;
        }
        try {
            if ($criteria && $criteria->relationLoaded('evidences')) {
                foreach ($criteria->evidences as $ev) {
                    $html = (string) ($ev->detail ?? '');
                    if (trim(strip_tags(html_entity_decode($html, ENT_QUOTES, 'UTF-8'))) !== '') {
                        return $html;
                    }
                }
            }
        } catch (\Throwable $e) {
        }
        return '';
    }

    private function extractReportBlocksFromHtml(string $html): array
    {
        $html = (string) ($html ?? '');
        if (trim($html) === '') {
            return [];
        }

        $doc = new \DOMDocument('1.0', 'UTF-8');
        $htmlBody = '<body>' . mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8') . '</body>';
        @$doc->loadHTML($htmlBody, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $body = $doc->getElementsByTagName('body')->item(0);
        if (!$body) {
            $text = trim(strip_tags(html_entity_decode($html, ENT_QUOTES, 'UTF-8')));
            return $text !== '' ? [['type' => 'text', 'text' => $text]] : [];
        }

        $blocks = [];
        $currentText = '';
        $flushText = function () use (&$blocks, &$currentText) {
            $text = trim(preg_replace('/\s+/', ' ', $currentText));
            if ($text !== '') {
                $blocks[] = ['type' => 'text', 'text' => $text];
            }
            $currentText = '';
        };

        $walk = function ($node) use (&$walk, &$blocks, &$currentText, $flushText) {
            if (!$node) return;
            if ($node->nodeType === XML_TEXT_NODE) {
                $currentText .= ' ' . $node->textContent;
                return;
            }
            if ($node->nodeType !== XML_ELEMENT_NODE) {
                return;
            }

            $tag = strtolower($node->nodeName);
            if ($tag === 'img') {
                $flushText();
                $src = $node->getAttribute('src');
                if ($src !== '') {
                    $blocks[] = ['type' => 'image', 'src' => $src];
                }
                return;
            }
            if ($tag === 'table') {
                $flushText();
                $rows = [];
                foreach ($node->getElementsByTagName('tr') as $tr) {
                    $cells = [];
                    foreach ($tr->childNodes as $cell) {
                        if ($cell->nodeType !== XML_ELEMENT_NODE) {
                            continue;
                        }
                        $cellTag = strtolower($cell->nodeName);
                        if ($cellTag !== 'td' && $cellTag !== 'th') {
                            continue;
                        }
                        $cells[] = trim(strip_tags($cell->textContent ?? ''));
                    }
                    if (!empty($cells)) {
                        $rows[] = $cells;
                    }
                }
                if (!empty($rows)) {
                    $blocks[] = ['type' => 'table', 'rows' => $rows];
                }
                return;
            }

            $isBlock = in_array($tag, ['p', 'div', 'li'], true);
            foreach ($node->childNodes as $child) {
                $walk($child);
            }
            if ($tag === 'br' || $isBlock) {
                $currentText .= "\n";
            }
        };

        foreach ($body->childNodes as $child) {
            $walk($child);
        }
        $flushText();

        return $blocks;
    }

    private function storeTempImage(string $data, string $ext): ?string
    {
        $ext = preg_replace('/[^a-z0-9]+/i', '', strtolower($ext)) ?: 'png';
        $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('export_img_', true) . '.' . $ext;
        if (@file_put_contents($tmp, $data) === false) {
            return null;
        }
        $this->tempImageFiles[] = $tmp;
        return $tmp;
    }

    private function resolveImagePath(string $src): ?string
    {
        $src = trim($src);
        if ($src === '') return null;

        if (preg_match('/^data:image\\/(\\w+);base64,(.+)$/', $src, $m)) {
            $ext = strtolower($m[1]);
            $data = base64_decode($m[2]);
            if ($data === false) return null;
            return $this->storeTempImage($data, $ext);
        }

        if (file_exists($src)) {
            return $src;
        }

        if (filter_var($src, FILTER_VALIDATE_URL)) {
            $data = @file_get_contents($src);
            if ($data === false) return null;
            return $this->storeTempImage($data, 'png');
        }

        return null;
    }

    public function __destruct()
    {
        foreach ($this->tempImageFiles as $path) {
            if (is_string($path) && file_exists($path)) {
                @unlink($path);
            }
        }
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
                    $s->mergeCells("A{$row}:I{$row}")
                        ->setCellValue("A{$row}", "มาตรฐาน: {$stdName}");
                    $s->getStyle("A{$row}")->getFont()->setBold(true);
                    $row++;

                    // ===== loop ด้านตามลำดับ fix =====
                    foreach ($categoryOrder as $catName) {
                        $inds = $cats->where('category_name', $catName);

                        // หัวด้าน
                        $s->mergeCells("A{$row}:I{$row}")
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
                                'criterias.evidenceRequirements',
                                'criterias.evidences.requirement', // ดึงมาหมด
                            ])->whereIn('id', $ids)->get()->keyBy('id');

                            // helper: extract plain text from evidence.detail for a criteria
                            // report html prefers criterias.report, fallback to evidence.detail

                            // แล้วค่อย filter ตอนใช้งาน



                            foreach ($inds as $ind) {
                                // ===== หัวตัวชี้วัด =====
                                $title = trim(($ind->code ? "[{$ind->code}] " : '') . ($ind->name ?? ''));
                                $s->mergeCells("A{$row}:I{$row}")->setCellValue("A{$row}", $title);
                                $s->getStyle("A{$row}")->getFont()->setBold(true);
                                $row++;

                                // ===== หัวตารางต่อ-ตัวชี้วัด =====
                                $s->mergeCells("A{$row}:A" . ($row + 1))->setCellValue("A{$row}", 'ข้อ');
                                $s->mergeCells("B{$row}:B" . ($row + 1))->setCellValue("B{$row}", 'เกณฑ์มาตรฐาน');
                                $s->mergeCells("C{$row}:D{$row}")->setCellValue("C{$row}", 'ผลการดำเนินงาน');
                                $s->setCellValue("C" . ($row + 1), 'มี');
                                $s->setCellValue("D" . ($row + 1), 'ไม่มี');
                                $s->mergeCells("E{$row}:H" . ($row + 1))->setCellValue("E{$row}", 'รายงานผลการดำเนินงาน');
                                $s->mergeCells("I{$row}:I" . ($row + 1))->setCellValue("I{$row}", 'เอกสาร/หลักฐาน');
                                $row += 2;

                                // ===== แสดงรายการเกณฑ์ (Criteria) ของตัวชี้วัดนี้ =====
                                $rel = $relations[$ind->id] ?? null;
                                $criterias = ($rel && $rel->relationLoaded('criterias')) ? $rel->criterias : collect();
                                $ci = 1;
                                if ($criterias->isEmpty()) {
                                    $s->setCellValue("A{$row}", '-');
                                    $s->mergeCells("B{$row}:I{$row}")->setCellValue("B{$row}", 'ไม่มีข้อมูล');
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
                                    $detailHtml = $this->getCriteriaReportHtml($c);
                                    $blocks = $this->extractReportBlocksFromHtml($detailHtml);
                                    $reportRow = $row;
                                    $wroteReport = false;

                                    if (empty($blocks)) {
                                        $s->mergeCells("E{$reportRow}:H{$reportRow}");
                                        $s->setCellValue("E{$reportRow}", '-');
                                        $s->getStyle("E{$reportRow}")->getAlignment()->setWrapText(true);
                                        $wroteReport = true;
                                        $reportRow++;
                                    } else {
                                        foreach ($blocks as $block) {
                                            $type = $block['type'] ?? '';
                                            if ($type === 'text') {
                                                $s->mergeCells("E{$reportRow}:H{$reportRow}");
                                                $s->setCellValue("E{$reportRow}", (string)($block['text'] ?? ''));
                                                $s->getStyle("E{$reportRow}")->getAlignment()->setWrapText(true);
                                                $wroteReport = true;
                                                $reportRow++;
                                                continue;
                                            }
                                            if ($type === 'table') {
                                                foreach ($block['rows'] ?? [] as $cells) {
                                                    $cells = array_map(function ($c) {
                                                        return trim(preg_replace('/\s+/', ' ', (string) $c));
                                                    }, (array) $cells);
                                                    $s->setCellValue("E{$reportRow}", $cells[0] ?? '');
                                                    $s->setCellValue("F{$reportRow}", $cells[1] ?? '');
                                                    $s->setCellValue("G{$reportRow}", $cells[2] ?? '');
                                                    $s->setCellValue("H{$reportRow}", $cells[3] ?? '');
                                                    if (count($cells) > 4) {
                                                        $extra = implode(' ', array_slice($cells, 4));
                                                        $s->setCellValue("H{$reportRow}", trim(($cells[3] ?? '') . ' ' . $extra));
                                                    }
                                                    $s->getStyle("E{$reportRow}:H{$reportRow}")->getAlignment()->setWrapText(true);
                                                    $wroteReport = true;
                                                    $reportRow++;
                                                }
                                                continue;
                                            }
                                            if ($type === 'image') {
                                                $path = $this->resolveImagePath((string) ($block['src'] ?? ''));
                                                if ($path) {
                                                    $s->mergeCells("E{$reportRow}:H{$reportRow}");
                                                    $drawing = new Drawing();
                                                    $drawing->setPath($path);
                                                    $drawing->setHeight(90);
                                                    $drawing->setCoordinates("E{$reportRow}");
                                                    $drawing->setWorksheet($s);
                                                    $s->getRowDimension($reportRow)->setRowHeight(95);
                                                    $wroteReport = true;
                                                    $reportRow++;
                                                }
                                                continue;
                                            }
                                        }
                                    }

                                    $reqList = $c->evidenceRequirements->pluck('name')->filter()->implode(', ');
                                    $evList = $c->evidences->map(function ($ev) {
                                        $label = $ev->name ?? '';
                                        $reqName = $ev->requirement?->name;
                                        if ($reqName) {
                                            $label .= ' [' . $reqName . ']';
                                        }
                                        return trim($label);
                                    })->filter()->implode(', ');
                                    $cellValue = $reqList !== ''
                                        ? "รายการที่ต้องส่ง: {$reqList}\nหลักฐาน: " . ($evList ?: '-')
                                        : ($evList ?: '-');
                                    $s->setCellValue("I{$row}", $cellValue);
                                    $s->getStyle("I{$row}")->getAlignment()->setWrapText(true);

                                    if (!$wroteReport) {
                                        $s->mergeCells("E{$row}:H{$row}");
                                        $s->setCellValue("E{$row}", '-');
                                        $s->getStyle("E{$row}")->getAlignment()->setWrapText(true);
                                        $reportRow = $row + 1;
                                    }

                                    $row = max($row + 1, $reportRow);
                                    $ci++;
                                }

                                // ===== เกณฑ์การให้คะแนน + การประเมินตนเอง (ท้ายตัวชี้วัด) =====
                                $start2 = $row + 1;
                                $s->mergeCells("A{$start2}:D{$start2}")->setCellValue("A{$start2}", 'เกณฑ์การให้คะแนน');
                                $s->mergeCells("E{$start2}:I{$start2}")->setCellValue("E{$start2}", 'การประเมินตนเอง');

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
                                $s->mergeCells("E{$r}:I{$r}")->setCellValue("E{$r}", '');
                                for ($k = 0; $k < 0; $k++) {
                                    $r++;
                                    $s->mergeCells("A{$r}:C{$r}")->setCellValue("A{$r}", '............................');
                                    $s->setCellValue("D{$r}", '........... คะแนน');
                                    $s->mergeCells("E{$r}:I{$r}")->setCellValue("E{$r}", $k === 1 ? '✓' : '');
                                }
                                // แปลง comment ที่เป็น <li> ให้แสดงแยกแถว
                                // ถ้าไม่มีรายการ <li> ให้กรอกคะแนนลงแถวแรกและติ๊กถูก
                                if (stripos((string) $rawComment, '<li') === false) {
                                    $r1 = $start2 + 1;
                                    $score = (float) (($rel->score_acc ?? null) ?? ($ind->score_acc ?? 0));
                                    $scoreText = rtrim(rtrim(number_format($score, 2, '.', ''), '0'), '.');
                                    try {
                                        $s->setCellValue("D{$r1}", ($scoreText === '' ? '0' : $scoreText) . ' คะแนน');
                                        $s->mergeCells("E{$r1}:I{$r1}")->setCellValue("E{$r1}", '✓');
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
                                            $s->mergeCells("E{$writeRow}:I{$writeRow}")->setCellValue("E{$writeRow}", '');
                                            $r = $writeRow;
                                        } else {
                                            $s->mergeCells("A{$writeRow}:C{$writeRow}")->setCellValue("A{$writeRow}", $txt);
                                            $s->setCellValue("D{$writeRow}", '........... คะแนน');
                                            $s->mergeCells("E{$writeRow}:I{$writeRow}")->setCellValue("E{$writeRow}", '');
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
                                            $s->mergeCells("E{$rowPtr}:I{$rowPtr}")->setCellValue("E{$rowPtr}", '✓');
                                        }
                                        $rowPtr++;
                                    }
                                }

                                $s->getStyle("A{$start2}:I{$r}")->applyFromArray([
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
                                $s->mergeCells("E{$start}:I{$start}")->setCellValue("E{$start}", 'การประเมินตนเอง');

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
                                    $s->mergeCells("E{$row}:I{$row}")->setCellValue("E{$row}", '');
                                }

                                // สไตล์กรอบของบล็อคคะแนน (รวมหัว)
                                $firstRow = $start;
                                $lastRow  = $row;
                                $s->getStyle("A{$firstRow}:I{$lastRow}")->applyFromArray([
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
                            $s->mergeCells("B{$row}:I{$row}")
                                ->setCellValue("B{$row}", 'ไม่มีข้อมูล');
                            $row++;
                        }

                        // แสดงเกณฑ์การให้คะแนนรายตัวชี้วัดแล้วด้านบน จึงข้ามบล็อคระดับด้านด้านล่าง
                        $row += 2;
                        continue;

                        // ===== เกณฑ์การให้คะแนน + การประเมินตนเอง =====
                        $start = $row + 1;
                        $s->mergeCells("A{$start}:D{$start}")->setCellValue("A{$start}", 'เกณฑ์การให้คะแนน');
                        $s->mergeCells("E{$start}:I{$start}")->setCellValue("E{$start}", 'การประเมินตนเอง');

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
                            $s->mergeCells("E{$row}:I{$row}")->setCellValue("E{$row}", '');
                        }

                        // สไตล์กรอบของบล็อคคะแนน (รวมหัวเรื่อง)
                        $firstRow = $start; // หัวบล็อค
                        $lastRow  = $row;   // แถวสุดท้ายของรายการ
                        $s->getStyle("A{$firstRow}:I{$lastRow}")->applyFromArray([
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
                $s->getColumnDimension('E')->setWidth(18);
                $s->getColumnDimension('F')->setWidth(18);
                $s->getColumnDimension('G')->setWidth(18);
                $s->getColumnDimension('H')->setWidth(18);
                $s->getColumnDimension('I')->setWidth(25);

                // เส้นกรอบรวม
                $s->getStyle("A1:I{$row}")->applyFromArray([
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
