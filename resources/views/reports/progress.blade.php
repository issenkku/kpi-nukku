@extends('layouts.app')

@section('title', 'รายงานความก้าวหน้าผลการดำเนินงาน')

@section('content')
<style>
    .report-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px; }
    .title-center { text-align: center; }
    .title-center h1 { font-size: 22px; font-weight: 700; margin: 8px 0 4px; }
    .title-center h2 { font-size: 18px; font-weight: 700; margin: 2px 0; }
    .title-center .total { color: #dc2626; font-weight: 800; margin-top: 4px; }

    .report-table { width: 100%; border-collapse: collapse; font-size: 14px; }
    .report-table th, .report-table td { border: 2px solid #94a3b8; padding: 8px 10px; vertical-align: top; }
    .report-table th { background: #cfe2ff; text-align: center; font-weight: 800; color: #0f172a; }
    .report-table .th-year { background: #dbeafe; }
    .report-table .col-max, .report-table .col-year { text-align: right; width: 120px; }
    .report-table .col-topic { width: 280px; }
    .report-table .row-std { background: #e6fffa; font-weight: 700; }
    .report-table .row-subtotal { background: #d1fae5; font-weight: 800; }
    .report-table tfoot td { background: #fde68a; font-weight: 900; color: #7c2d12; }

    .controls { display: flex; gap: 8px; align-items: center; margin: 10px 0 16px; flex-wrap: wrap; }
    .controls select { border: 1px solid #d1d5db; border-radius: 8px; padding: 6px 8px; }
    .btn { background: #2563eb; color: #fff; border: 0; padding: 6px 12px; border-radius: 8px; }
    .note { color: #64748b; font-size: 12px; }
    .sticky-head thead th { position: sticky; top: 0; z-index: 1; }
    .scroll-x { overflow-x: auto; }
</style>

<div class="report-card">
    <div class="title-center">
        <h1>รายงานความก้าวหน้าสรุปผลการรายงานการดำเนินงานแต่ละตัวชี้วัด ประจำปี {{ $y2 }} - {{ $y1 }}</h1>
        <h2>ส่วนที่ {{ request('std', 1) }} {{ $selectedStandard ? $selectedStandard : 'มาตรฐาน' }}</h2>
        <div class="total">คะแนนรวม {{ number_format($grand['max'] ?? 0, 2) }} คะแนน</div>
    </div>

    <form method="get" class="controls" action="{{ route('reports.progress') }}">
        <input type="hidden" name="std" value="{{ request('std', 1) }}" />
        <label>ปีล่าสุด:
            <select name="y1">
                @foreach(($yearOptions ?? []) as $y)
                    <option value="{{ $y }}" {{ (int)$y === (int)$y1 ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </label>
        <label>ปีก่อนหน้า:
            <select name="y2">
                @foreach(($yearOptions ?? []) as $y)
                    <option value="{{ $y }}" {{ (int)$y === (int)$y2 ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </label>
        <button type="submit" class="badge">ปรับปี</button>
        <span class="note">คำแนะนำ: เลือกปี 2 ค่าเพื่อเทียบผล</span>
    </form>

    <div class="scroll-x">
        <table class="report-table sticky-head">
            <thead>
                <tr>
                    <th class="col-topic">ด้าน</th>
                    <th>โครงสร้าง</th>
                    <th class="col-max">คะแนนเต็ม</th>
                    <th class="col-year th-year">{{ $y2 }}@if(request('asof2')) ({{ request('asof2') }}) @endif</th>
                    <th class="col-year th-year">{{ $y1 }}@if(request('asof1')) ({{ request('asof1') }}) @endif</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sections as $sIndex => $sec)
                    <tr class="row-std">
                        <td> {{ $sIndex + 1 }}. {{ $sec['standard'] }} </td>
                        <td></td>
                        <td class="col-max text-right"></td>
                        <td class="col-year"></td>
                        <td class="col-year"></td>
                    </tr>

                    @forelse($sec['categories'] as $cIndex => $cat)
                        <tr>
                            <td style="background:#eef2ff; font-weight:700;">{{ $sIndex + 1 }}.{{ $cIndex + 1 }} ด้าน</td>
                            <td style="background:#eef2ff; font-weight:700;">{{ $cat['name'] }}</td>
                            <td class="col-max" style="background:#eef2ff; font-weight:700;">{{ number_format($cat['max'] ?? 0, 2) }}</td>
                            <td class="col-year" style="background:#eef2ff;"></td>
                            <td class="col-year" style="background:#eef2ff;"></td>
                        </tr>

                        @forelse($cat['rows'] as $row)
                            <tr>
                                <td></td>
                                <td>
                                    <div style="font-weight:600;color:#0f172a;">{{ $row['name'] }}</div>
                                    <div class="note">รหัส: {{ $row['code'] }}</div>
                                </td>
                                <td class="col-max">{{ number_format($row['max'] ?? 0, 2) }}</td>
                                <td class="col-year" style="background:#fff7cc;">{{ $row['y2'] !== null ? number_format($row['y2'], 2) : '' }}</td>
                                <td class="col-year" style="background:#fff7cc;">{{ $row['y1'] !== null ? number_format($row['y1'], 2) : '' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td></td>
                                <td colspan="4">ไม่มีข้อมูลตัวชี้วัดในด้านนี้</td>
                            </tr>
                        @endforelse

                        <tr class="row-subtotal">
                            <td></td>
                            <td style="text-align:left">คะแนนรวมทั้งสิ้น</td>
                            <td class="col-max">{{ number_format($cat['max'] ?? 0, 2) }}</td>
                            <td class="col-year">{{ number_format($cat['totals']['y2'] ?? 0, 2) }}</td>
                            <td class="col-year">{{ number_format($cat['totals']['y1'] ?? 0, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td></td>
                            <td colspan="4">ไม่มีข้อมูลตัวชี้วัดสำหรับมาตรฐานนี้</td>
                        </tr>
                    @endforelse
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center">ไม่มีข้อมูลสำหรับปีที่เลือก</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td></td>
                    <td style="text-align:left">คะแนนโครงสร้างรวมทั้งสิ้น</td>
                    <td class="col-max">{{ number_format($grand['max'] ?? 0, 2) }}</td>
                    <td class="col-year">{{ number_format($grand['y2'] ?? 0, 2) }}</td>
                    <td class="col-year">{{ number_format($grand['y1'] ?? 0, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
