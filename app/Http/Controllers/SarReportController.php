<?php

namespace App\Http\Controllers;

use App\Models\SarReport;
use App\Models\Standard;
use App\Models\Indicator;
use App\Models\Criteria;
use App\Models\Evidence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

use PhpOffice\PhpWord\Style\ListItem;

class SarReportController extends Controller
{
    protected array $tempImageFiles = [];
    public function index()
    {
        $reports = SarReport::with(['standard', 'indicator', 'criteria'])->paginate(10);

        // Collect all distinct years from both SAR reports and Indicators
        $sarYears = SarReport::whereNotNull('year')
            ->selectRaw('DISTINCT year')
            ->orderBy('year', 'desc')
            ->pluck('year');

        $indicatorYears = Indicator::whereNotNull('year')
            ->selectRaw('DISTINCT year')
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Merge, unique and sort desc so the modal shows every year available in DB
        $years = $sarYears
            ->merge($indicatorYears)
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();

        return view('sar_reports.index', compact('reports', 'years'));
    }
    // public function part3()
    // {
    //     $standards = Indicator::with([
    //         'category.standard',   // ดึง category + standard
    //         'criterias.evidences'  // ดึง criteria + evidences
    //     ])->get()
    //         ->groupBy(fn($ind) => $ind->category->standard->name ?? 'ไม่ทราบมาตรฐาน');

    //     return view('sar_reports.part3', compact('standards'));
    // }
    public function create(Request $request)
    {
        $year = $request->input('year');

        $standards = Indicator::with([
            'category.standard',
            'criterias' => function ($q) {
                $q->orderBy('sequence');
            },
            'criterias.evidences',
        ])
            ->when($year, fn($q) => $q->where('year', $year)) // ✅ กรองปีตรงนี้
            ->orderByRaw("CASE LEFT(code, 3) WHEN 'NCS' THEN 1 WHEN 'NCO' THEN 2 WHEN 'NCP' THEN 3 ELSE 4 END")
            ->orderByRaw("CASE WHEN split_part(code, '-', 2) ~ '^[0-9]+' THEN CAST(split_part(code, '-', 2) AS INTEGER) ELSE 999999 END")
            ->get()
            ->groupBy(fn($ind) => optional(optional($ind->category)->standard)->name ?? 'ไม่ระบุมาตรฐาน');

        return view('sar_reports.create', compact('standards', 'year'));
    }


    public function store(Request $request)
    {
        // Accept section fields from the form and optional meta fields
        $data = $request->validate([
            'section1' => 'nullable|string',
            'section2' => 'nullable|string',
            'section4' => 'nullable|string',
            'year' => 'nullable|integer',
            'standard_id' => 'nullable|exists:standards,id',
            'indicator_id' => 'nullable|exists:indicators,id',
            'criteria_id' => 'nullable|exists:criterias,id',
            'title' => 'nullable|string|max:255',
        ]);

        // If required relational fields are missing, derive sensible defaults
        $indicatorId = $data['indicator_id'] ?? null;
        $criteriaId  = $data['criteria_id'] ?? null;
        $standardId  = $data['standard_id'] ?? null;

        if (!$indicatorId || !$criteriaId || !$standardId) {
            $indicator = Indicator::with(['category.standard', 'criterias' => function ($q) {
                $q->orderBy('sequence');
            }])
                ->orderBy('code')
                ->first();

            if (!$indicator || $indicator->criterias->isEmpty() || !$indicator->category || !$indicator->category->standard) {
                return back()->withInput()->withErrors([
                    'general' => 'ไม่พบตัวชี้วัด/เกณฑ์/มาตรฐานสำหรับบันทึกข้อมูล',
                ]);
            }

            $indicatorId = $indicatorId ?: $indicator->id;
            $criteriaId  = $criteriaId  ?: $indicator->criterias->first()->id;
            $standardId  = $standardId  ?: $indicator->category->standard->id;
        }

        $year = $data['year'] ?? null;
        if (!$year) {
            // Use indicator year if available, otherwise current year
            $year = optional(Indicator::find($indicatorId))->year ?? (int) now()->format('Y');
        }

        $payload = array_merge($data, [
            'year' => $year,
            'standard_id' => $standardId,
            'indicator_id' => $indicatorId,
            'criteria_id' => $criteriaId,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        $report = SarReport::create($payload);

        if ($request->filled('evidence_ids')) {
            $report->evidences()->sync($request->input('evidence_ids'));
        }

        return redirect()->route('sar_reports.index')->with('success', 'บันทึก SAR เรียบร้อยแล้ว');
    }
    public function edit($id)
    {
        $report = SarReport::findOrFail($id);

        $standards = Indicator::with([
            'category.standard',
            'criterias' => function ($q) {
                $q->orderBy('sequence');
            },
            'criterias.evidences',
        ])
            ->where('year', $report->year)
            ->orderByRaw("CASE LEFT(code, 3) WHEN 'NCS' THEN 1 WHEN 'NCO' THEN 2 WHEN 'NCP' THEN 3 ELSE 4 END")
            ->orderByRaw("CASE WHEN split_part(code, '-', 2) ~ '^[0-9]+' THEN CAST(split_part(code, '-', 2) AS INTEGER) ELSE 999999 END")
            ->get()
            ->groupBy(function ($ind) {
                return optional(optional($ind->category)->standard)->name ?? 'ไม่ระบุมาตรฐาน';
            });

        return view('sar_reports.edit', compact('report', 'standards'));
    }

    public function update($id, Request $request)
    {
        $report = SarReport::findOrFail($id);

        $data = $request->validate([
            'section1' => 'nullable|string',
            'section2' => 'nullable|string',
            'section4' => 'nullable|string',
            'year' => 'nullable|integer',
            'standard_id' => 'nullable|exists:standards,id',
            'indicator_id' => 'nullable|exists:indicators,id',
            'criteria_id' => 'nullable|exists:criterias,id',
            'title' => 'nullable|string|max:255',
        ]);

        $payload = [
            'section1' => $data['section1'] ?? $report->section1,
            'section2' => $data['section2'] ?? $report->section2,
            'section4' => $data['section4'] ?? $report->section4,
            'year' => $data['year'] ?? $report->year,
            'standard_id' => $data['standard_id'] ?? $report->standard_id,
            'indicator_id' => $data['indicator_id'] ?? $report->indicator_id,
            'criteria_id' => $data['criteria_id'] ?? $report->criteria_id,
            'title' => $data['title'] ?? $report->title,
            'updated_by' => Auth::id(),
        ];

        $report->update($payload);

        if ($request->filled('evidence_ids')) {
            $report->evidences()->sync($request->input('evidence_ids'));
        }

        return redirect()->route('sar_reports.index')->with('success', 'อัปเดตรายงาน SAR สำเร็จ');
    }
    public function updateReport($id, Request $request)
    {
        $request->validate([
            'report' => 'nullable|string',
        ]);

        $criteria = Criteria::findOrFail($id);
        $reportHtml = $request->input('report');

        // Persist to criterias.report (no evidence auto-create)
        $criteria->report = $reportHtml;
        $criteria->save();

        // If a detail evidence already exists, sync it for compatibility
        try {
            $evi = Evidence::where('criteria_id', $criteria->id)
                ->whereNotNull('detail')
                ->orderByDesc('id')
                ->first();

            if ($evi) {
                $evi->detail = $reportHtml;
                $evi->save();
            }
        } catch (\Throwable $e) {
            // Non-fatal: report already saved on criteria
        }

        return response()->json([
            'success' => true,
            'message' => 'บันทึกสำเร็จ',
            'report'  => $reportHtml,
        ]);
    }




    /**
     * Safely add HTML to a PhpWord container, with fallback to plain text.
     */
    // protected function addHtmlSafe($element, ?string $html): void
    // {
    //     $content = trim($html ?? '');

    //     if ($content === '') {
    //         $element->addText('-');
    //         return;
    //     }

    //     try {
    //         // ✅ ให้ PhpWord parse เอง
    //         Html::addHtml($element, $content, false, false);

    //     } catch (\Throwable $e) {
    //         // fallback plain text
    //         $element->addText(strip_tags($content));
    //     }
    // }

    public function destroy($id)
    {
        try {
            $report = SarReport::findOrFail($id);



            $report->delete();

            return redirect()
                ->route('sar_reports.index')
                ->with('success', 'ลบรายงานสำเร็จ');
        } catch (\Throwable $e) {
            return back()
                ->with('error', 'ไม่สามารถลบรายการได้')
                ->withErrors(['delete' => $e->getMessage()]);
        }
    }

    protected function addHtmlSafe($element, ?string $html): void
    {
        $content = trim($html ?? '');

        if ($content === '') {
            $element->addText('-');
            return;
        }

        // ✅ ทำความสะอาด HTML ให้เหมาะกับ Word/PhpWord (ตัด font/span/style ฯลฯ)
        $allowTablesImages = stripos($content, '<table') !== false || stripos($content, '<img') !== false;
        $content = $this->cleanHtmlForWord($content, $allowTablesImages);

        // ถ้าไม่มีแท็ก HTML ให้แตกบรรทัดเป็นย่อหน้า
        if (strpos($content, '<') === false && strpos($content, '>') === false) {
            $lines = preg_split("/\r\n|\r|\n/", $content);
            $added = false;
            foreach ($lines as $line) {
                $line = $this->sanitizeWordText($line);
                if ($line === '') {
                    $element->addTextBreak();
                    $added = true;
                    continue;
                }
                $element->addText($line);
                $element->addTextBreak();
                $added = true;
            }
            if ($added) {
                return;
            }
        }

        // ✅ parse DOM
        $doc = new \DOMDocument('1.0', 'UTF-8');
        $htmlBody = '<body>' . mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8') . '</body>';
        @$doc->loadHTML(
            $htmlBody,
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        $body = $doc->getElementsByTagName('body')->item(0);
        if (!$body) {
            $element->addText($this->sanitizeWordText(strip_tags($content)) ?: '-');
            return;
        }

        foreach ($body->childNodes as $node) {
            $this->appendNodeSafe($element, $node, 0);
        }
    }


    protected function appendNodeSafe($element, $node, int $listLevel = 0): void
    {
        if (!$node) return;

        if ($node->nodeType === XML_TEXT_NODE) {
            $text = preg_replace("/[\r\n]+/", " ", $node->textContent);
            $text = $this->sanitizeWordText($text);
            if ($text !== '') {
                $element->addText($text);
            }
            return;
        }

        if ($node->nodeType === XML_ELEMENT_NODE) {
            $tag = strtolower($node->nodeName);
            $text = preg_replace("/[\r\n]+/", " ", $node->textContent ?? '');
            $text = $this->sanitizeWordText($text);

            switch ($tag) {
                case 'p':
                    if ($text !== '') {
                        $element->addText($text);
                    }
                    $element->addTextBreak();
                    break;
                case 'table':
                    $rows = [];
                    $maxCols = 0;
                    foreach ($node->childNodes as $child) {
                        $childTag = $child->nodeType === XML_ELEMENT_NODE ? strtolower($child->nodeName) : '';
                        if (in_array($childTag, ['thead', 'tbody', 'tfoot'], true)) {
                            foreach ($child->childNodes as $tr) {
                                if ($tr->nodeType === XML_ELEMENT_NODE && strtolower($tr->nodeName) === 'tr') {
                                    $rows[] = $tr;
                                }
                            }
                        } elseif ($childTag === 'tr') {
                            $rows[] = $child;
                        }
                    }
                    foreach ($rows as $tr) {
                        $count = 0;
                        foreach ($tr->childNodes as $cell) {
                            if ($cell->nodeType === XML_ELEMENT_NODE) {
                                $cellTag = strtolower($cell->nodeName);
                                if ($cellTag === 'td' || $cellTag === 'th') {
                                    $count++;
                                }
                            }
                        }
                        $maxCols = max($maxCols, $count);
                    }
                    $cellWidth = $maxCols > 0 ? (int) floor(9000 / $maxCols) : 2000;
                    $tbl = $element->addTable(['borderSize' => 6, 'borderColor' => '000000']);
                    foreach ($rows as $tr) {
                        $tbl->addRow();
                        foreach ($tr->childNodes as $cellNode) {
                            if ($cellNode->nodeType !== XML_ELEMENT_NODE) {
                                continue;
                            }
                            $cellTag = strtolower($cellNode->nodeName);
                            if ($cellTag !== 'td' && $cellTag !== 'th') {
                                continue;
                            }
                            $cell = $tbl->addCell($cellWidth);
                            foreach ($cellNode->childNodes as $child) {
                                $this->appendNodeSafe($cell, $child, $listLevel);
                            }
                        }
                    }
                    break;
                case 'img':
                    $src = $node->getAttribute('src');
                    if ($src) {
                        $this->addImageFromSrc($element, $src);
                    }
                    break;

                case 'ul':
                case 'ol':
                    // Keep current level for first-level list; nested lists will increase inside children
                    foreach ($node->childNodes as $li) {
                        $this->appendNodeSafe($element, $li, $listLevel);
                    }
                    break;

                case 'li':
                    if ($text !== '') {
                        // Use bullet list to avoid numbering schema corruption in Word
                        $safeLevel = max(0, (int)$listLevel);
                        $element->addListItem($text, $safeLevel, null, ListItem::TYPE_BULLET_FILLED);
                    }
                    break;

                case 'br':
                    $element->addTextBreak();
                    break;

                case 'b':
                case 'strong':
                    if ($text !== '') {
                        $element->addText($text, ['bold' => true]);
                    }
                    break;

                case 'i':
                case 'em':
                    if ($text !== '') {
                        $element->addText($text, ['italic' => true]);
                    }
                    break;

                case 'u':
                    if ($text !== '') {
                        $element->addText($text, ['underline' => 'single']);
                    }
                    break;

                default:
                    foreach ($node->childNodes as $child) {
                        $this->appendNodeSafe($element, $child, $listLevel);
                    }
            }
        }
    }

    protected function addImageFromSrc($element, string $src): void
    {
        if (preg_match('/^data:image\\/(\\w+);base64,(.+)$/', $src, $m)) {
            $ext = strtolower($m[1]);
            $data = base64_decode($m[2]);
            if ($data === false) return;
            $path = $this->storeTempImage($data, $ext);
            if ($path) {
                $element->addImage($path, ['width' => 380]);
            }
            return;
        }
        if (filter_var($src, FILTER_VALIDATE_URL) || file_exists($src)) {
            $element->addImage($src, ['width' => 380]);
        }
    }

    protected function extractImageSources(string $html): array
    {
        $srcs = [];
        if ($html === '') return $srcs;
        if (preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $html, $m)) {
            $srcs = $m[1] ?? [];
        }
        return array_values(array_unique(array_filter($srcs)));
    }

    protected function extractTableTextFromHtml(string $html): string
    {
        $html = (string) ($html ?? '');
        if (trim($html) === '') return '';

        $doc = new \DOMDocument('1.0', 'UTF-8');
        $htmlBody = '<body>' . mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8') . '</body>';
        @$doc->loadHTML($htmlBody, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $body = $doc->getElementsByTagName('body')->item(0);
        if (!$body) {
            return trim(strip_tags(html_entity_decode($html, ENT_QUOTES, 'UTF-8')));
        }

        $parts = [];
        $tableRows = [];
        foreach ($body->childNodes as $node) {
            if ($node->nodeType !== XML_ELEMENT_NODE) {
                $text = trim($node->textContent ?? '');
                if ($text !== '') $parts[] = $text;
                continue;
            }
            $tag = strtolower($node->nodeName);
            if ($tag === 'table') {
                $rows = [];
                foreach ($node->getElementsByTagName('tr') as $tr) {
                    $cells = [];
                    foreach ($tr->childNodes as $cell) {
                        if ($cell->nodeType !== XML_ELEMENT_NODE) continue;
                        $cellTag = strtolower($cell->nodeName);
                        if ($cellTag !== 'td' && $cellTag !== 'th') continue;
                        $cellText = trim(strip_tags($cell->textContent ?? ''));
                        $cells[] = $cellText;
                    }
                    if ($cells) {
                        $rows[] = $cells;
                    }
                }
                if ($rows) {
                    $tableRows = array_merge($tableRows, $rows);
                }
            } else {
                $text = trim($node->textContent ?? '');
                if ($text !== '') $parts[] = $text;
            }
        }

        if ($tableRows) {
            $parts[] = $this->formatAsciiTable($tableRows);
        }

        $result = trim(implode("\n", $parts));
        if ($result !== '') {
            return $result;
        }

        // Fallback: simple regex-based table to TSV
        $raw = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $raw = preg_replace('/<\/(td|th)>/i', "\t", $raw);
        $raw = preg_replace('/<\/tr>/i', "\n", $raw);
        $raw = strip_tags($raw);
        $raw = preg_replace("/\t+/", "\t", $raw);
        $raw = preg_replace("/\n+/", "\n", $raw);
        return trim($raw);
    }

    protected function extractTableRowsFromHtml(string $html): array
    {
        $html = (string) ($html ?? '');
        if (trim($html) === '') return [];
        $doc = new \DOMDocument('1.0', 'UTF-8');
        $htmlBody = '<body>' . mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8') . '</body>';
        @$doc->loadHTML($htmlBody, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $body = $doc->getElementsByTagName('body')->item(0);
        if (!$body) return [];

        $rows = [];
        foreach ($body->getElementsByTagName('table') as $table) {
            foreach ($table->getElementsByTagName('tr') as $tr) {
                $cells = [];
                foreach ($tr->childNodes as $cell) {
                    if ($cell->nodeType !== XML_ELEMENT_NODE) continue;
                    $cellTag = strtolower($cell->nodeName);
                    if ($cellTag !== 'td' && $cellTag !== 'th') continue;
                    $cellText = trim(strip_tags($cell->textContent ?? ''));
                    $cells[] = $cellText;
                }
                if ($cells) {
                    $rows[] = $cells;
                }
            }
        }
        return $rows;
    }

    protected function extractReportBlocksFromHtml(string $html): array
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
            if (!$node) {
                return;
            }
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

    protected function extractPlainTextWithoutTables(string $html): string
    {
        $html = (string) ($html ?? '');
        if (trim($html) === '') return '';
        $doc = new \DOMDocument('1.0', 'UTF-8');
        $htmlBody = '<body>' . mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8') . '</body>';
        @$doc->loadHTML($htmlBody, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $body = $doc->getElementsByTagName('body')->item(0);
        if (!$body) {
            return trim(strip_tags(html_entity_decode($html, ENT_QUOTES, 'UTF-8')));
        }
        $parts = [];
        foreach ($body->childNodes as $node) {
            if ($node->nodeType !== XML_ELEMENT_NODE) {
                $text = trim($node->textContent ?? '');
                if ($text !== '') $parts[] = $text;
                continue;
            }
            if (strtolower($node->nodeName) === 'table') {
                continue;
            }
            $text = trim($node->textContent ?? '');
            if ($text !== '') $parts[] = $text;
        }
        return trim(implode("\n", $parts));
    }

    protected function formatAsciiTable(array $rows): string
    {
        if (empty($rows)) return '';
        $lines = [];
        foreach ($rows as $r) {
            $cells = array_map(function ($c) {
                $c = trim(preg_replace('/\s+/', ' ', (string) $c));
                return $c;
            }, $r);
            $lines[] = implode("\t", $cells);
        }
        return implode("\n", $lines);
    }

    protected function storeTempImage(string $data, string $ext): ?string
    {
        $dir = storage_path('app/tmp');
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $ext = preg_replace('/[^a-z0-9]/i', '', $ext) ?: 'png';
        $path = $dir . DIRECTORY_SEPARATOR . 'sar_img_' . uniqid('', true) . '.' . $ext;
        if (@file_put_contents($path, $data) === false) {
            return null;
        }
        $this->tempImageFiles[] = $path;
        register_shutdown_function(function () use ($path) {
            @unlink($path);
        });
        return $path;
    }

    /**
     * Clean HTML: ลบแท็ก <font> ออก และ normalize ให้ Word อ่านได้
     */
    protected function cleanHtmlForWord(string $html, bool $allowTablesImages = false): string
    {
        if (trim($html) === '') {
            return '';
        }

        // 1) ลบ XML declaration ถ้ามี
        $html = preg_replace('/<\?xml.*?\?>/i', '', $html);

        // 2) ลบแท็ก <font> ทั้งหมด (เก่าและ Word ไม่รองรับ)
        $html = preg_replace('/<\/?font[^>]*>/i', '', $html);

        // 3) อนุญาตเฉพาะแท็กที่ Word รองรับ
        $allowed = '<p><br><b><strong><i><em><u><ul><ol><li>';
        if ($allowTablesImages) {
            $allowed .= '<table><thead><tbody><tr><th><td><img>';
        }
        $html = strip_tags($html, $allowed);

        // 4) Normalize <br> → <br/>
        $html = preg_replace('/<br(\s*)>/i', '<br/>', $html);

        // 5) ลบ style inline ที่ Word ไม่ใช้ (เช่น font-size, font-family)
        $html = preg_replace('/\s*style=("|\')(.*?)\1/i', '', $html);

        return trim($html);
    }

    protected function sanitizeWordText(?string $text): string
    {
        $text = $text ?? '';
        if ($text === '') return '';

        // Normalize newlines
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        // Decode HTML entities
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Replace non-breaking space with normal space
        $text = str_replace("\xC2\xA0", ' ', $text);

        // Remove zero-width and BOM-like characters that can upset Word
        $text = preg_replace('/[\x{FEFF}\x{200B}\x{200C}\x{200D}\x{2060}]/u', '', $text);

        // Ensure valid UTF-8 (drop invalid sequences)
        if (function_exists('iconv')) {
            $converted = @iconv('UTF-8', 'UTF-8//IGNORE', $text);
            if ($converted !== false) {
                $text = $converted;
            }
        }

        // Remove characters invalid in XML 1.0 (keep TAB, LF, CR)
        $text = preg_replace('/[^\x09\x0A\x0D\x20-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u', '', $text);

        // Collapse excessive spaces
        $text = preg_replace('/[ \t]+/', ' ', $text);

        return trim($text);
    }


    public function export(SarReport $report, $type)
    {
        // Make sure Thai fonts are available for Dompdf
        $this->ensurePdfFontsInstalled();
        // โหลดความสัมพันธ์ที่จำเป็น
        $report->load([
            'indicator.criterias.evidences',
            'indicator.category.standard',
        ]);

        // Sanitize inline styles that force non-Thai fonts from WYSIWYG/Word paste
        $stripFonts = function (?string $html) {
            if (!$html) return $html;
            // Remove <font> tags
            $html = preg_replace('/<\/?font[^>]*>/i', '', $html ?? '');
            // Remove deprecated attributes
            $html = preg_replace('/\sface=(["\']).*?\1/i', '', $html);
            $html = preg_replace('/\scolor=(["\']).*?\1/i', '', $html);
            $html = preg_replace('/\ssize=(["\']).*?\1/i', '', $html);
            // Remove font-family / font / mso- properties from style attributes
            $html = preg_replace_callback('/style=(["\'])(.*?)\1/i', function ($m) {
                $style = $m[2];
                $style = preg_replace('/\s*(font-family|font|letter-spacing|word-spacing|mso-[^:]+)\s*:[^;]*;?/i', '', $style);
                $style = trim(trim($style), ';');
                return $style !== '' ? 'style="' . $style . '"' : '';
            }, $html);
            return $html;
        };


        // helper to get first non-empty evidence.detail HTML for a criteria
        $getEvidenceDetailHtml = function ($cri) {
            try {
                if ($cri && $cri->relationLoaded('evidences')) {
                    foreach ($cri->evidences as $ev) {
                        $html = (string) ($ev->detail ?? '');
                        if (trim(strip_tags(html_entity_decode($html))) !== '') {
                            return $html;
                        }
                    }
                }
            } catch (\Throwable $e) {}
            return '';
        };
        // helper to prefer criterias.report, fallback to evidence.detail
        $getCriteriaReportHtml = function ($cri) use ($getEvidenceDetailHtml) {
            $reportHtml = (string) ($cri->report ?? '');
            if (trim(strip_tags(html_entity_decode($reportHtml))) === '') {
                $reportHtml = $getEvidenceDetailHtml($cri);
            }
            return $reportHtml;
        };

        // Clone object for rendering and scrub HTML fields
        $reportToRender = clone $report;
        $reportToRender->section1 = $stripFonts($reportToRender->section1);
        $reportToRender->section2 = $stripFonts($reportToRender->section2);
        $reportToRender->section4 = $stripFonts($reportToRender->section4);
        if ($reportToRender->indicator && $reportToRender->indicator->criterias) {
            foreach ($reportToRender->indicator->criterias as $cri) {
                $cri->report = $stripFonts($getCriteriaReportHtml($cri));
            }
        }


        if ($type === 'pdf') {
            // โหลด indicators ทั้งหมด
            $allIndicators = Indicator::with([
                'category.standard',
                'criterias' => fn($q) => $q->orderBy('sequence'),
                'criterias.evidenceRequirements',
                'criterias.evidences.requirement',
            ])
                ->where('year', $report->year)
                ->join('categories', 'categories.id', '=', 'indicators.categorie_id')
                ->join('standards', 'standards.id', '=', 'categories.standard_id')
                ->orderBy('standards.id')
                ->orderBy('categories.id')
                ->orderBy('indicators.id')
                ->select('indicators.*')
                ->get();
            foreach ($allIndicators as $ind) {
                foreach ($ind->criterias as $cri) {
                    $cri->report = $stripFonts($getCriteriaReportHtml($cri));
                }
            }

            $reportToRender->setRelation('indicators', $allIndicators);

            $pdf = Pdf::loadView('sar_reports.export_pdf', ['report' => $reportToRender])
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled'   => true,
                    'isRemoteEnabled'        => true,
                    // Ensure Dompdf picks a Unicode Thai font
                    'default_font'           => 'SarabunLocal',
                    'enable_font_subsetting' => true,
                    'font_dir'               => storage_path('fonts'),
                    'font_cache'             => storage_path('fonts'),
                    'chroot'                 => base_path(),
                ]);

            // ✅ เปลี่ยนจาก download() เป็น stream()
            return $pdf->stream("sar-report-{$report->year}.pdf");
        }

        if ($type === 'excel') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Indicators');

            $row = 1;
            $sheet->mergeCells("A{$row}:I{$row}")
                ->setCellValue("A{$row}", "SAR Report " . (string)$report->year);
            $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal('center');
            $row += 2;

            $indicators = Indicator::with([
                'category.standard',
                'criterias' => fn($q) => $q->orderBy('sequence'),
                'criterias.evidenceRequirements',
                'criterias.evidences.requirement'
            ])
                ->where('year', $report->year)
                ->join('categories', 'categories.id', '=', 'indicators.categorie_id')
                ->join('standards', 'standards.id', '=', 'categories.standard_id')
                ->orderBy('standards.id')
                ->orderBy('categories.id')
                ->orderBy('indicators.id')
                ->select('indicators.*')
                ->get()
                ->groupBy(fn($ind) => optional(optional($ind->category)->standard)->name ?? 'ไม่ระบุมาตรฐาน');

            foreach ($indicators as $stdName => $indsByStd) {
                // ===== หัวมาตรฐาน =====
                $sheet->mergeCells("A{$row}:I{$row}")
                    ->setCellValue("A{$row}", "มาตรฐาน: {$stdName}");
                $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                $row++;

                foreach ($indsByStd->groupBy(fn($i) => optional($i->category)->name ?? 'ไม่ระบุด้าน') as $catName => $inds) {
                    // ===== หัวด้าน =====
                    $sheet->mergeCells("A{$row}:I{$row}")
                        ->setCellValue("A{$row}", "ด้าน: {$catName}");
                    $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                    $row++;

                    foreach ($inds as $ind) {
                        // ===== หัวตัวบ่งชี้ =====
                        $title = "[{$ind->code}] {$ind->name}";
                        $sheet->mergeCells("A{$row}:I{$row}")->setCellValue("A{$row}", $title);
                        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                        $row++;

                        // ===== หัวตารางเกณฑ์มาตรฐาน =====
                        $sheet->mergeCells("A{$row}:A" . ($row + 1))->setCellValue("A{$row}", 'ข้อ');
                        $sheet->mergeCells("B{$row}:B" . ($row + 1))->setCellValue("B{$row}", 'เกณฑ์มาตรฐาน');
                        $sheet->mergeCells("C{$row}:D{$row}")->setCellValue("C{$row}", 'ผลการดำเนินงาน');
                        $sheet->setCellValue("C" . ($row + 1), 'มี');
                        $sheet->setCellValue("D" . ($row + 1), 'ไม่มี');
                        $sheet->mergeCells("E{$row}:H" . ($row + 1))->setCellValue("E{$row}", 'รายงานผลการดำเนินงาน');
                        $sheet->mergeCells("I{$row}:I" . ($row + 1))->setCellValue("I{$row}", 'เอกสาร/หลักฐาน');
                        $row += 2;

                        // ===== loop criterias =====
                        $ci = 1;
                        foreach ($ind->criterias as $cri) {
                            $sheet->setCellValue("A{$row}", $ci++);
                            $sheet->setCellValue("B{$row}", $cri->name ?? '');
                            $has = (bool)($cri->status ?? false);
                            $sheet->setCellValue("C{$row}", $has ? '✓' : '');
                            $sheet->setCellValue("D{$row}", $has ? '' : '✓');
                            $detailHtml = $getCriteriaReportHtml($cri);
                            $blocks = $this->extractReportBlocksFromHtml($detailHtml);
                            $reportRow = $row;
                            $wroteReport = false;

                            if (empty($blocks)) {
                                $sheet->mergeCells("E{$reportRow}:H{$reportRow}");
                                $sheet->setCellValue("E{$reportRow}", '-');
                                $sheet->getStyle("E{$reportRow}")->getAlignment()->setWrapText(true);
                                $sheet->getStyle("E{$reportRow}")->getFont()->setName('Courier New');
                                $wroteReport = true;
                                $reportRow++;
                            } else {
                                foreach ($blocks as $block) {
                                    if (($block['type'] ?? '') === 'text') {
                                        $sheet->mergeCells("E{$reportRow}:H{$reportRow}");
                                        $sheet->setCellValue("E{$reportRow}", $block['text'] ?? '');
                                        $sheet->getStyle("E{$reportRow}")->getAlignment()->setWrapText(true);
                                        $sheet->getStyle("E{$reportRow}")->getFont()->setName('Courier New');
                                        $wroteReport = true;
                                        $reportRow++;
                                        continue;
                                    }
                                    if (($block['type'] ?? '') === 'table') {
                                        foreach ($block['rows'] ?? [] as $cells) {
                                            $cells = array_map(function ($c) {
                                                return trim(preg_replace('/\s+/', ' ', (string) $c));
                                            }, (array) $cells);
                                            $sheet->setCellValue("E{$reportRow}", $cells[0] ?? '');
                                            $sheet->setCellValue("F{$reportRow}", $cells[1] ?? '');
                                            $sheet->setCellValue("G{$reportRow}", $cells[2] ?? '');
                                            $sheet->setCellValue("H{$reportRow}", $cells[3] ?? '');
                                            $sheet->getStyle("E{$reportRow}:H{$reportRow}")->getAlignment()->setWrapText(true);
                                            $sheet->getStyle("E{$reportRow}:H{$reportRow}")->getFont()->setName('Courier New');
                                            $wroteReport = true;
                                            $reportRow++;
                                        }
                                        continue;
                                    }
                                    if (($block['type'] ?? '') === 'image') {
                                        $src = (string) ($block['src'] ?? '');
                                        $path = null;
                                        if (preg_match('/^data:image\\/(\\w+);base64,(.+)$/', $src, $m)) {
                                            $ext = strtolower($m[1]);
                                            $data = base64_decode($m[2]);
                                            if ($data !== false) {
                                                $path = $this->storeTempImage($data, $ext);
                                            }
                                        } elseif (filter_var($src, FILTER_VALIDATE_URL) || file_exists($src)) {
                                            $path = $src;
                                        }
                                        if ($path) {
                                            $drawing = new Drawing();
                                            $drawing->setPath($path);
                                            $drawing->setHeight(90);
                                            $drawing->setCoordinates("E{$reportRow}");
                                            $drawing->setWorksheet($sheet);
                                            $sheet->getRowDimension($reportRow)->setRowHeight(95);
                                            $wroteReport = true;
                                            $reportRow++;
                                        }
                                        continue;
                                    }
                                }
                            }

                            $evList = $cri->evidences->pluck('name')->implode(', ');
                            $sheet->setCellValue("I{$row}", $evList ?: '-');
                            if (!$wroteReport) {
                                $sheet->mergeCells("E{$row}:H{$row}");
                                $sheet->setCellValue("E{$row}", '-');
                                $sheet->getStyle("E{$row}")->getAlignment()->setWrapText(true);
                                $sheet->getStyle("E{$row}")->getFont()->setName('Courier New');
                                $reportRow = $row + 1;
                            }
                            $row = max($row + 1, $reportRow);
                        }

                        // ===== ตารางเกณฑ์การให้คะแนน =====
                        $sheet->mergeCells("A{$row}:D{$row}")->setCellValue("A{$row}", 'เกณฑ์การให้คะแนน');
                        $sheet->mergeCells("E{$row}:I{$row}")->setCellValue("E{$row}", 'การประเมินตนเอง');
                        $row++;

                        $lines = [];
                        if (!empty($ind->comment)) {
                            $plain = preg_replace('/<\/(p|div|li|br)>/i', "\n", $ind->comment);
                            $plain = strip_tags($plain);
                            $plain = html_entity_decode($plain, ENT_QUOTES, 'UTF-8');
                            $lines = preg_split('/\r\n|\r|\n/', $plain);
                            $lines = array_filter(array_map('trim', $lines));
                        }

                        $score = $ind->self_score ?? ($ind->score_acc ?? null);

                        if (empty($lines)) {
                            $sheet->mergeCells("A{$row}:C{$row}")->setCellValue("A{$row}", '............................');
                            $sheet->setCellValue("D{$row}", '........ คะแนน');
                            $sheet->mergeCells("E{$row}:I{$row}")->setCellValue("E{$row}", '');
                            $row++;
                        } else {
                            foreach ($lines as $line) {
                                // ดึงคะแนนจากข้อความ
                                $scoreFromLine = null;
                                if (preg_match(
                                    '/\(\s*(?:([0-9]+(?:\.[0-9]+)?)\s*คะแนน|คะแนน\s*([0-9]+(?:\.[0-9]+)?)|([0-9]+(?:\.[0-9]+)?))\s*\)/u',
                                    $line,
                                    $mm
                                )) {
                                    $scoreFromLine = (float) ($mm[1] ?? $mm[2] ?? $mm[3]);
                                }

                                // เช็คว่าตรงกับ self_score หรือไม่
                                $match = $score !== null && $scoreFromLine !== null && abs($scoreFromLine - (float) $score) < 0.001;

                                // เขียนแถวลง Excel
                                $sheet->mergeCells("A{$row}:C{$row}")->setCellValue("A{$row}", $line);
                                $sheet->setCellValue("D{$row}", $scoreFromLine !== null ? "{$scoreFromLine} คะแนน" : '........ คะแนน');
                                $sheet->mergeCells("E{$row}:I{$row}")->setCellValue("E{$row}", $match ? '✓' : '');
                                $row++;
                            }
                        }


                        // ===== ใส่ border + alignment =====
                        $sheet->getStyle("A1:I{$row}")->applyFromArray([
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                ],
                            ],
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                                'wrapText'   => true,
                            ],
                        ]);

                        $row += 2; // เว้นบรรทัดก่อน Indicator ถัดไป
                    }
                }
            }

            // ปรับความกว้างคอลัมน์
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(50);
            $sheet->getColumnDimension('C')->setWidth(12);
            $sheet->getColumnDimension('D')->setWidth(12);
            $sheet->getColumnDimension('E')->setWidth(18);
            $sheet->getColumnDimension('F')->setWidth(18);
            $sheet->getColumnDimension('G')->setWidth(18);
            $sheet->getColumnDimension('H')->setWidth(18);
            $sheet->getColumnDimension('I')->setWidth(25);

            $writer = new Xlsx($spreadsheet);
            $filename = "sar-report-{$report->year}.xlsx";
            return response()->streamDownload(function () use ($writer) {
                if (ob_get_length()) {
                    @ob_end_clean();
                }
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'max-age=0, no-cache, must-revalidate, proxy-revalidate',
                'Pragma' => 'public',
            ]);
        }


        if ($type === 'docx') {
            $phpWord = new PhpWord();
            $section = $phpWord->addSection();

            // ===== ส่วนที่ 1 =====
            $section->addTitle($this->sanitizeWordText('ส่วนที่ 1: ข้อมูลทั่วไปคณะพยาบาลศาสตร์'), 2);
            $this->addHtmlSafe($section, $report->section1 ?? '-');

            // ===== ส่วนที่ 2 =====
            $section->addTitle($this->sanitizeWordText('ส่วนที่ 2: ข้อมูลด้านคุณภาพ'), 2);
            $this->addHtmlSafe($section, $report->section2 ?? '-');

            // ===== ส่วนที่ 3 =====
            $section->addTitle($this->sanitizeWordText('ส่วนที่ 3: การประเมินตนเองตามตัวบ่งชี้'), 2);

            $indicators = Indicator::with([
                'category.standard',
                'criterias' => fn($q) => $q->orderBy('sequence'),
                'criterias.evidences'
            ])
                ->where('year', $report->year)
                ->join('categories', 'categories.id', '=', 'indicators.categorie_id')
                ->join('standards', 'standards.id', '=', 'categories.standard_id')
                ->orderBy('standards.id')
                ->orderBy('categories.id')
                ->orderBy('indicators.id')
                ->select('indicators.*')
                ->get()
                ->groupBy(fn($ind) => optional(optional($ind->category)->standard)->name ?? 'ไม่ระบุมาตรฐาน');

            foreach ($indicators as $stdName => $indsByStd) {
                $section->addTitle($this->sanitizeWordText("มาตรฐาน: {$stdName}"), 3);

                foreach ($indsByStd->groupBy(fn($i) => optional($i->category)->name ?? 'ไม่ระบุด้าน') as $catName => $inds) {
                    $section->addTitle($this->sanitizeWordText("ด้าน: {$catName}"), 4);

                    foreach ($inds as $ind) {
                        $section->addText($this->sanitizeWordText("[{$ind->code}] {$ind->name}"), ['bold' => true]);

                        // ===== ตารางเกณฑ์มาตรฐาน =====
                        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
                        $table->addRow();
                        $table->addCell(500)->addText('ข้อ', ['bold' => true]);
                        $table->addCell(3000)->addText('เกณฑ์มาตรฐาน', ['bold' => true]);
                        $table->addCell(1500)->addText('ผลการดำเนินงาน', ['bold' => true]);
                        $table->addCell(2500)->addText('รายงานผลการดำเนินงาน', ['bold' => true]);
                        $table->addCell(2000)->addText('เอกสารหลักฐาน', ['bold' => true]);

                        foreach ($ind->criterias as $i => $cri) {
                            $table->addRow();
                            $table->addCell(500)->addText((string)($i + 1));
                            $table->addCell(3000)->addText($this->sanitizeWordText($cri->name));
                            $table->addCell(1500)->addText($cri->status ? '✓' : '-');

                            $cell = $table->addCell(2500);
                            $this->addHtmlSafe($cell, $stripFonts($getCriteriaReportHtml($cri) ?: '-'));

                            $evList = $cri->evidences->pluck('name')->implode(', ');
                            $table->addCell(2000)->addText($this->sanitizeWordText($evList ?: '-'));
                        }

                        // ===== ตารางเกณฑ์การให้คะแนน =====
                        $lines = [];
                        if (!empty($ind->comment)) {
                            $plain = preg_replace('/<\/(p|div|li|br)>/i', "\n", $ind->comment);
                            $plain = strip_tags($plain);
                            $plain = html_entity_decode($plain, ENT_QUOTES, 'UTF-8');
                            $lines = preg_split('/\r\n|\r|\n/', $plain);
                            $lines = array_filter(array_map('trim', $lines));
                        }
                        $score = $ind->self_score ?? ($ind->score_acc ?? null);

                        $scoreTable = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
                        $scoreTable->addRow();
                        $scoreTable->addCell(6000)->addText('เกณฑ์การให้คะแนน', ['bold' => true]);
                        $scoreTable->addCell(1500)->addText('คะแนน', ['bold' => true]);
                        $scoreTable->addCell(2000)->addText('การประเมินตนเอง', ['bold' => true]);

                        if (!empty($lines)) {
                            foreach ($lines as $line) {
                                $scoreFromLine = null;
                                if (preg_match(
                                    '/\(\s*(?:([0-9]+(?:\.[0-9]+)?)\s*คะแนน|คะแนน\s*([0-9]+(?:\.[0-9]+)?)|([0-9]+(?:\.[0-9]+)?))\s*\)/u',
                                    $line,
                                    $mm,
                                )) {
                                    $scoreFromLine = (float) $mm[1];
                                }
                                $match = $score !== null && $scoreFromLine !== null && abs($scoreFromLine - (float)$score) < 0.001;

                                $scoreTable->addRow();
                                $scoreTable->addCell(6000)->addText($this->sanitizeWordText($line));
                                $scoreTable->addCell(1500)->addText($scoreFromLine !== null ? "{$scoreFromLine} คะแนน" : '........ คะแนน');
                                $scoreTable->addCell(2000)->addText($match ? '✓' : '');
                            }
                        } else {
                            $scoreTable->addRow();
                            $scoreTable->addCell(6000)->addText('............................');
                            $scoreTable->addCell(1500)->addText('........ คะแนน');
                            $scoreTable->addCell(2000)->addText('');
                        }
                    }
                }
            }

            // ===== ส่วนที่ 4 =====
            $section->addTitle($this->sanitizeWordText('ส่วนที่ 4: สรุปผลการประเมินตนเองตามเกณฑ์ของสภาการพยาบาล'), 2);
            $this->addHtmlSafe($section, $report->section4 ?? '-');

            // ===== ดาวน์โหลดไฟล์ =====
            $filename = "sar-report-{$report->year}.docx";
            $writer = IOFactory::createWriter($phpWord, 'Word2007');

            return response()->streamDownload(function () use ($writer) {
                if (ob_get_length()) {
                    @ob_end_clean();
                }
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'Cache-Control' => 'max-age=0, no-cache, must-revalidate, proxy-revalidate',
                'Pragma' => 'public',
            ]);
        }


        return back()->with('error', 'ไม่รองรับรูปแบบไฟล์นี้');
    }

    /**
     * Ensure Dompdf can find Thai fonts. Copies any .ttf from public/fonts to storage/fonts.
     */
    protected function ensurePdfFontsInstalled(): void
    {
        try {
            $source = public_path('fonts');
            $destination = storage_path('fonts');

            if (!is_dir($source)) {
                return; // nothing to copy; view may still embed fonts via @font-face if paths exist
            }

            if (!is_dir($destination)) {
                @mkdir($destination, 0755, true);
            }

            // Copy missing TTFs
            foreach (glob($source . DIRECTORY_SEPARATOR . '*.ttf') as $file) {
                $target = $destination . DIRECTORY_SEPARATOR . basename($file);
                if (!file_exists($target)) {
                    @copy($file, $target);
                }
            }

            // Fallback: if bundled Thai fonts look suspiciously small, copy Windows fonts as a substitute
            $sarabunReg = $destination . DIRECTORY_SEPARATOR . 'Sarabun-Regular.ttf';
            $sarabunBold = $destination . DIRECTORY_SEPARATOR . 'Sarabun-Bold.ttf';
            $tooSmall = function ($p) {
                return !file_exists($p) || filesize($p) < 200000;
            };
            if ($tooSmall($sarabunReg) || $tooSmall($sarabunBold)) {
                $winFonts = getenv('WINDIR') ? getenv('WINDIR') . DIRECTORY_SEPARATOR . 'Fonts' : 'C:\\Windows\\Fonts';
                $tahomaReg = $winFonts . DIRECTORY_SEPARATOR . 'tahoma.ttf';
                $tahomaBold = $winFonts . DIRECTORY_SEPARATOR . 'tahomabd.ttf';
                if (@is_file($tahomaReg) && @is_file($tahomaBold)) {
                    @copy($tahomaReg, $sarabunReg);
                    @copy($tahomaBold, $sarabunBold);
                }
            }
        } catch (\Throwable $e) {
            // Non-fatal: PDF generation may still work if fonts already cached
        }
    }
}
