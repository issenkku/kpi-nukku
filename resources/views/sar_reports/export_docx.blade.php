<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>SAR Report {{ $report->year }}</title>
    <style>
        @php
            $sarReg  = str_replace('\\\\', '/', storage_path('fonts/Sarabun-Regular.ttf'));
            $sarBold = str_replace('\\\\', '/', storage_path('fonts/Sarabun-Bold.ttf'));
        @endphp

        @font-face {
            font-family: 'SarabunLocal';
            font-style: normal;
            font-weight: 400;
            src: url('{{ $sarReg }}') format('truetype');
        }
        @font-face {
            font-family: 'SarabunLocal';
            font-style: normal;
            font-weight: 700;
            src: url('{{ $sarBold }}') format('truetype');
        }

        body, * { font-family: "SarabunLocal", sans-serif !important; }
        body { font-size: 13px; line-height: 1.4; }

        h2 { text-align: center; font-size: 20px; margin-bottom: 20px; }
        h3 { font-size: 18px; margin-top: 20px; border-bottom: 1px solid #000; }
        h4 { font-size: 16px; margin-top: 12px; }
        h5 { font-size: 15px; margin-top: 10px; }

        p { margin: 0 0 6px 0; }
        ul { margin: 0; padding-left: 18px; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }
        /* Allow rows to split across pages to prevent truncation of long rows */
        tr { page-break-inside: auto; }
        td, th {
            border: 1px solid #000;
            padding: 4px 5px;
            vertical-align: top;
            word-break: break-word;
            white-space: pre-line; /* keep line breaks but collapse spaces */
        }
        th { background: #f0f0f0; text-align: center; }

        .score { text-align: center; font-weight: bold; }

        /* Criteria table column widths */
        .criteria-table th:nth-child(1),
        .criteria-table td:nth-child(1) { width: 40px; }
        .criteria-table th:nth-child(3),
        .criteria-table td:nth-child(3) { width: 80px; }
        .criteria-table th:nth-child(4),
        .criteria-table td:nth-child(4) { width: 200px; }
        .criteria-table th:nth-child(5),
        .criteria-table td:nth-child(5) { width: 150px; }

        .criteria-table td,
        .criteria-table th,
        .score-table td,
        .score-table th { font-size: 12px; }
    </style>
</head>
<body>

    <h2>รายงานการประเมินตนเอง (SAR) ปี {{ $report->year }}</h2>

    {{-- ส่วนที่ 1 --}}
    <h3>ส่วนที่ 1: ข้อมูลทั่วไปคณะพยาบาลศาสตร์</h3>
    <div>{!! $report->section1 !!}</div>

    {{-- ส่วนที่ 2 --}}
    <h3>ส่วนที่ 2: ข้อมูลด้านคุณภาพ</h3>
    <div>{!! $report->section2 !!}</div>

    {{-- ส่วนที่ 3 --}}
    <h3>ส่วนที่ 3: การประเมินตนเองตามตัวบ่งชี้</h3>

    @php
        // กำหนด indicators collection (รองรับทั้ง singular และ many)
        $indicatorCollection = collect(
            ($report && method_exists($report, 'relationLoaded') && $report->relationLoaded('indicators') && $report->indicators && $report->indicators->isNotEmpty())
                ? $report->indicators
                : (($report && isset($report->indicator) && $report->indicator) ? [$report->indicator] : [])
        );
    @endphp

    @foreach ($indicatorCollection->groupBy(fn($ind) => optional(optional($ind->category)->standard)->name ?? 'ไม่ระบุมาตรฐาน') as $stdName => $indsByStd)
        <h4>มาตรฐาน: {{ $stdName }}</h4>

        @foreach ($indsByStd->groupBy('category.name') as $catName => $inds)
            <h5>ด้าน: {{ $catName }}</h5>

            @foreach ($inds as $ind)
                <p><strong>[{{ $ind->code }}] {{ $ind->name }}</strong></p>

                {{-- ตารางเกณฑ์ --}}
                <table>
                    <thead>
                        <tr>
                            <th style="width:40px">ข้อ</th>
                            <th>เกณฑ์มาตรฐาน</th>
                            <th style="width:80px">ผลการดำเนินงาน</th>
                            <th style="width:200px">รายงานผลการดำเนินงาน</th>
                            <th style="width:150px">เอกสารหลักฐาน</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ind->criterias as $i => $cri)
                            <tr>
                                <td class="score">{{ $i + 1 }}</td>
                                <td>{{ $cri->name }}</td>
                                <td class="score">{{ $cri->status ? '✓' : '-' }}</td>
                                @php
                                    // Prefer criteria.report, fallback to evidence detail
                                    $detailHtml = (string) ($cri->report ?? '');
                                    if (trim(strip_tags(html_entity_decode($detailHtml))) === '' && $cri->relationLoaded('evidences')) {
                                        foreach ($cri->evidences as $ev) {
                                            $h = (string) ($ev->detail ?? '');
                                            if (trim(strip_tags(html_entity_decode($h))) !== '') { $detailHtml = $h; break; }
                                        }
                                    }
                                @endphp
                                <td>{!! $detailHtml !== '' ? $detailHtml : '-' !!}</td>
                                <td>
                                    @if ($cri->evidences->isNotEmpty())
                                        <ul>
                                            @foreach ($cri->evidences as $ev)
                                                <li>{{ $ev->name }}</li>
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

                {{-- ตารางคะแนน --}}
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

                <table>
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
                                                        if (
                                                            preg_match(
                                                                '/\(\s*(?:([0-9]+(?:\.[0-9]+)?)\s*คะแนน|คะแนน\s*([0-9]+(?:\.[0-9]+)?)|([0-9]+(?:\.[0-9]+)?))\s*\)/u',
                                                                $line,
                                                                $mm,
                                                            )
                                                        ) {
                                                            $scoreFromLine =
                                                                (float) (array_values(
                                                                    array_filter([
                                                                        $mm[1] ?? null,
                                                                        $mm[2] ?? null,
                                                                        $mm[3] ?? null,
                                                                    ]),
                                                                )[0] ?? null);
                                                        }

                                                        $match =
                                                            $score !== null &&
                                                            $scoreFromLine !== null &&
                                                            abs($scoreFromLine - (float) $score) < 0.001;
                                                    @endphp
                            <tr>
                                <td>{{ $line }}</td>
                                <td class="score">{{ $scoreFromLine ? $scoreFromLine . ' คะแนน' : '...... คะแนน' }}</td>
                                <td class="score">{{ $match ? '✓' : '-' }}</td>
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

    {{-- ส่วนที่ 4 --}}
    <h3>ส่วนที่ 4: อื่นๆ</h3>
    <div>{!! $report->section4 !!}</div>

</body>
</html>
