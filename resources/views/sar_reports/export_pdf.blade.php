<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>SAR Report {{ $report->year }}</title>
    <style>
        @php
            $notoRegPath  = storage_path('fonts/NotoSansThai-Regular.ttf');
            $notoBoldPath = storage_path('fonts/NotoSansThai-Bold.ttf');
            $sarRegPath   = storage_path('fonts/Sarabun-Regular.ttf');
            $sarBoldPath  = storage_path('fonts/Sarabun-Bold.ttf');

            $notoOk = file_exists($notoRegPath) && file_exists($notoBoldPath);
            $sarOk  = file_exists($sarRegPath) && file_exists($sarBoldPath);

            // Build file:/// URLs for Dompdf (reliable on Windows too)
            $toFileUrl = function ($p) {
                $n = str_replace('\\\\', '/', $p);
                return 'file:///' . ltrim($n, '/'); // file:///C:/...
            };

            $notoReg = $toFileUrl($notoRegPath);
            $notoBold = $toFileUrl($notoBoldPath);
            $sarReg  = $toFileUrl($sarRegPath);
            $sarBold = $toFileUrl($sarBoldPath);
        @endphp

        @if ($sarOk)
        @font-face { font-family: 'SarabunLocal'; font-style: normal; font-weight: 400; src: url('{{ $sarReg }}') format('truetype'); }
        @font-face { font-family: 'SarabunLocal'; font-style: normal; font-weight: 700; src: url('{{ $sarBold }}') format('truetype'); }
        body, * { font-family: 'SarabunLocal', sans-serif !important; }
        @elseif ($notoOk)
        @font-face { font-family: 'NotoSansThai'; font-style: normal; font-weight: 400; src: url('{{ $notoReg }}') format('truetype'); }
        @font-face { font-family: 'NotoSansThai'; font-style: normal; font-weight: 700; src: url('{{ $notoBold }}') format('truetype'); }
        body, * { font-family: 'NotoSansThai', sans-serif !important; }
        @else
        /* Last resort fallback; may not fully support Thai */
        body, * { font-family: 'DejaVu Sans', sans-serif !important; }
        @endif
        @page { size: A4 portrait; margin: 25mm; }
        body { font-size: 12px; line-height: 1.4; color: #111; }

        h2 { text-align: center; font-size: 16px; margin-bottom: 12px; font-weight: 700; }
        h3 { font-size: 14px; margin-top: 14px; border-bottom: 1px solid #000; font-weight: 600; }
        h4 { font-size: 12px; margin-top: 10px; font-weight: 600; }
        h5 { font-size: 11px; margin-top: 8px; font-weight: 600; }
        strong, b { font-weight: 600; }

        p { margin: 0 0 6px 0; }
        ul { margin: 0; padding-left: 18px; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            table-layout: fixed;
        }

        /* Prevent nested tables from overflowing their cells */
        td table {
            width: 100% !important;
            max-width: 100% !important;
            table-layout: fixed;
            page-break-inside: auto;
        }
        .criteria-table td table td,
        .criteria-table td table th {
            height: auto !important;
            page-break-inside: auto;
        }
        /* Compact but consistent font in criteria report column */
        .criteria-table td:nth-child(4),
        .criteria-table td:nth-child(4) * {
            font-size: 8px;
            line-height: 1.1;
        }
        .criteria-table td:nth-child(4) table td,
        .criteria-table td:nth-child(4) table th {
            padding: 1px 2px;
        }
        .criteria-table td:nth-child(4) * {
            max-width: 100% !important;
            word-break: break-word;
            overflow-wrap: anywhere;
            height: auto !important;
            max-height: none !important;
            overflow: visible !important;
        }
        .criteria-table td:nth-child(4),
        .criteria-table td:nth-child(4) table,
        .criteria-table td:nth-child(4) tr,
        .criteria-table td:nth-child(4) td,
        .criteria-table td:nth-child(4) th {
            page-break-inside: auto;
        }
        .criteria-report,
        .criteria-report.long,
        .criteria-report.longer,
        .criteria-report.longest {
            font-size: 8px;
            line-height: 1.1;
        }
        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }
        /* Allow rows to split across pages to prevent truncation */
        tr { page-break-inside: auto; }
        .criteria-table tr { page-break-inside: auto; }
        td, th {
            border: 1px solid #000;
            padding: 3px 4px;
            vertical-align: top;
            word-break: break-word;
            overflow-wrap: anywhere;
            white-space: normal;
            overflow: visible;
            page-break-inside: auto;
        }
        th { background: #f5f7fa; text-align: center; }

        /* Zebra rows for readability */
        tbody tr:nth-child(odd) { background: #fbfbfd; }

        /* Make lists tidy inside cells */
        td ul { margin: 0; padding-left: 16px; }
        td ol { margin: 0; padding-left: 18px; }

        /* Scale images if any appear in rich text */
        td img { max-width: 100%; max-height: 120mm; height: auto; page-break-inside: avoid; }

        .score { text-align: center; font-weight: bold; }
        .tick-img { height: 12px; vertical-align: middle; }

        /* Criteria table column widths (match DOCX proportions) */
        .criteria-table th:nth-child(1),
        .criteria-table td:nth-child(1) { width: 6%; min-width: 32px; }
        .criteria-table th:nth-child(2),
        .criteria-table td:nth-child(2) { width: 20%; }
        .criteria-table th:nth-child(3),
        .criteria-table td:nth-child(3) { width: 12%; }
        .criteria-table th:nth-child(4),
        .criteria-table td:nth-child(4) { width: 50%; }
        .criteria-table th:nth-child(5),
        .criteria-table td:nth-child(5) { width: 12%; }

        .criteria-table td,
        .criteria-table th,
        .score-table td,
        .score-table th { font-size: 12px; }

        .criteria-table th:first-child,
        .criteria-table td:first-child {
            padding: 1px 2px;
            font-size: 10px;
            white-space: nowrap;
        }
    </style>

    @php
        $indicatorCollection = collect(
            ($report && method_exists($report, 'relationLoaded') && $report->relationLoaded('indicators') && $report->indicators && $report->indicators->isNotEmpty())
                ? $report->indicators
                : (($report && isset($report->indicator) && $report->indicator) ? [$report->indicator] : [])
        );
        // Inline SVG checkmark as base64 (works without special fonts)
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 16 16"><path fill="#000" d="M6.173 12.414L2.1 8.343l1.414-1.414 2.657 2.657 6.314-6.314 1.414 1.414z"/></svg>';
        $tickImg = '<img class="tick-img" src="data:image/svg+xml;base64,' . base64_encode($svg) . '" alt="check">';
    @endphp
</head>
<body>
    <h2>รายงานการประเมินตนเอง (SAR) ปี {{ $report->year }}</h2>

    {{-- ===== ส่วนที่ 1 ===== --}}
    <h3>ส่วนที่ 1: ข้อมูลทั่วไปคณะพยาบาลศาสตร์</h3>
    <div>{!! $report->section1 ?? '' !!}</div>

    {{-- ===== ส่วนที่ 2 ===== --}}
    <h3>ส่วนที่ 2: ข้อมูลด้านคุณภาพ</h3>
    <div>{!! $report->section2 ?? '' !!}</div>

    {{-- ===== ส่วนที่ 3 ===== --}}
    <h3>ส่วนที่ 3: การประเมินตนเองตามตัวบ่งชี้</h3>
    @foreach ($indicatorCollection->groupBy(fn($ind) => optional(optional($ind->category)->standard)->name ?? 'ไม่ระบุมาตรฐาน') as $stdName => $indsByStd)
        <h4>มาตรฐาน: {{ $stdName }}</h4>

        @foreach ($indsByStd->groupBy('category.name') as $catName => $inds)
            <h5>ด้าน: {{ $catName }}</h5>

            @foreach ($inds as $ind)
                <p><strong>[{{ $ind->code }}] {{ $ind->name }}</strong></p>

                {{-- ✅ ตารางเกณฑ์ (แยกทีละ indicator) --}}
                <table class="criteria-table">
                    <colgroup>
                     <col style="width: 40px;">
                        <col style="width: 32%;">
                        <col style="width: 12%;">
                        <col style="width: 41%;">
                        <col style="width: 12%;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>ข้อ</th>
                            <th>เกณฑ์มาตรฐาน</th>
                            <th>ผลการดำเนินงาน</th>
                            <th>รายงานผลการดำเนินงาน</th>
                            <th>เอกสารหลักฐาน</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ind->criterias as $i => $cri)
                            <tr>
                                <td class="score">{{ $i + 1 }}</td>
                                <td>{{ $cri->name }}</td>
                                <td class="score">{!! $cri->status ? $tickImg : '-' !!}</td>
                                @php
                                    // Prefer criteria.report, fallback to most recent evidence detail
                                    $detailHtml = (string) ($cri->report ?? '');
                                    if (trim(strip_tags(html_entity_decode($detailHtml))) === '' && $cri->relationLoaded('evidences')) {
                                        $ordered = $cri->evidences->sortByDesc(function($e){ return $e->created_at; });
                                        foreach ($ordered as $ev) {
                                            $h = (string) ($ev->detail ?? '');
                                            if (trim(strip_tags(html_entity_decode($h))) !== '') { $detailHtml = $h; break; }
                                        }
                                    }
                                    $detailHtml = preg_replace('/\sstyle=("|\')(.*?)\1/i', '', $detailHtml);
                                    $detailHtml = preg_replace('/\s(width|height|cellpadding|cellspacing)=("|\')?[^"\'>\s]+\2?/i', '', $detailHtml);
                                    $detailHtml = preg_replace('/\s(width|height)=("|\')?[^"\'>\s]+\2?/i', '', $detailHtml);
                                    $detailPlain = trim(strip_tags(html_entity_decode($detailHtml)));
                                    $detailLen = function_exists('mb_strlen')
                                        ? mb_strlen($detailPlain, 'UTF-8')
                                        : strlen($detailPlain);
                                    $reportClass = 'criteria-report';
                                    if ($detailLen >= 800) {
                                        $reportClass .= ' longest';
                                    } elseif ($detailLen >= 400) {
                                        $reportClass .= ' longer';
                                    } elseif ($detailLen >= 200) {
                                        $reportClass .= ' long';
                                    }
                                @endphp
                                <td class="{{ $reportClass }}">{!! $detailHtml !== '' ? $detailHtml : '-' !!}</td>
                                <td>
                                    @if ($cri->evidenceRequirements->isNotEmpty())
                                        <div>
                                            <strong>รายการที่ต้องส่ง:</strong>
                                            {{ $cri->evidenceRequirements->pluck('name')->filter()->implode(', ') ?: '-' }}
                                        </div>
                                    @endif
                                    @if ($cri->evidences->isNotEmpty())
                                        <ul>
                                            @foreach ($cri->evidences as $ev)
                                                <li>
                                                    {{ $ev->name }}
                                                    @if ($ev->requirement)
                                                        [{{ $ev->requirement->name }}]
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="score">ไม่มีข้อมูล</td></tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- ✅ ตารางคะแนน (แยกทีละ indicator) --}}
               @php
    $lines = [];
    if (!empty($ind->comment)) {
        $plain = preg_replace('/<\/(p|div|li|br)>/i', "\n", $ind->comment);
        $plain = strip_tags($plain);
        $plain = html_entity_decode($plain, ENT_QUOTES, 'UTF-8');
        $lines = preg_split('/\r\n|\r|\n/', $plain);
        $lines = array_filter(array_map('trim', $lines));
    }
    $score = $ind->self_score ?? ($ind->score_acc ?? null);
@endphp

<table class="score-table">
    <thead>
        <tr>
            <th>เกณฑ์การให้คะแนน</th>
            <th style="width:100px">คะแนน</th>
            <th style="width:120px">การประเมินตนเอง</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($lines as $line)
            @php
                $scoreFromLine = null;

                if (preg_match(
                    '/\(\s*(?:([0-9]+(?:\.[0-9]+)?)\s*คะแนน|คะแนน\s*([0-9]+(?:\.[0-9]+)?)|([0-9]+(?:\.[0-9]+)?))\s*\)/u',
                    $line,
                    $mm
                )) {
                    $scoreFromLine = (float) (
                        $mm[1] ?? $mm[2] ?? $mm[3] ?? null
                    );
                }

                $match = $score !== null
                    && $scoreFromLine !== null
                    && abs($scoreFromLine - (float) $score) < 0.001;
            @endphp
            <tr>
                <td>{{ $line }}</td>
                <td class="score">
                    {{ $scoreFromLine !== null ? $scoreFromLine . ' คะแนน' : '...... คะแนน' }}
                </td>
                <td class="score">{!! $match ? $tickImg : '' !!}</td>
            </tr>
        @empty
            <tr>
                <td>............................</td>
                <td class="score">........... คะแนน</td>
                <td></td>
            </tr>
        @endforelse
    </tbody>
</table>

            @endforeach
        @endforeach
    @endforeach

    {{-- ===== ส่วนที่ 4 ===== --}}
    <h3>ส่วนที่ 4: อื่นๆ / ข้อเสนอแนะ</h3>
    <div>{!! $report->section4 ?? '' !!}</div>
</body>
</html>
