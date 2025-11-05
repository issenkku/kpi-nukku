@extends('layouts.app')
@section('title', 'แดชบอร์ด')

@section('header', 'แดชบอร์ด')
@section('subheader', 'ระบบบริหารจัดการข้อมูลการรับรองสถาบันจากสภาการพยาบาล')

@section('content')

    <!-- Toggle Switch -->
    <div class="text-right">
        <label for="toggle-filter" class="switch">
            <input type="checkbox" id="toggle-filter">
            <span class="slider round"></span>
        </label>
        <span>กรองข้อมูล</span>
    </div>

    <!-- Filter Card (component) -->
    <x-filter :years="$yearsForFilter" :standards="$allStandards" :departments="$departments" :collectors="$collectors" :dimensions="$dimensionStats" :filters="$filters"
        :action="route('dashboard.index')" :selectedYear="$displayYear" />

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-title">
            <h3>สถานะทั้งหมดของตัวบ่งชี้ต่อปี</h3>
            <span class="year" id="display-years">{{ $displayYearText }}</span>
        </div>

        <div class="stat-card">
            <div class="stat-body">
                <div class="chart-wrap">
                    <canvas id="satisfactionChart" width="310" height="260"></canvas>
                </div>

                <div class="legend-wrap">
                    @php $totalStatus = array_sum($statusCounts); @endphp
                    @foreach ($legendConfig as $item)
                        @php $count = $statusCounts[$item['key']] ?? 0; @endphp
                        {{-- legend items below as cards --}}
                    @endforeach

                    <div class="stats-card" id="card-total">
                        <div class="stats-icon">
                            <i class="fa fa-list"></i>
                        </div>
                        <div class="stats-info">
                            <div class="stats-value" id="indicator-total">0</div>
                            <div class="stats-label">จำนวนตัวบ่งชี้ทั้งหมด</div>
                        </div>
                    </div>

                    <div class="stats-card legend-item" data-key="complete">
                        <div class="stats-icon success">
                            <i data-lucide="check-circle"></i>
                        </div>
                        <div class="stats-info">
                            <div class="stats-value legend-count">{{ $statusCounts['complete'] ?? 0 }}</div>
                            <div class="stats-label">ผลการดำเนินงานครบถ้วนตามเกณฑ์มาตรการ</div>
                        </div>
                    </div>

                    <div class="stats-card legend-item" data-key="incomplete">
                        <div class="stats-icon warn">
                            <i data-lucide="alert-triangle"></i>
                        </div>
                        <div class="stats-info">
                            <div class="stats-value legend-count">{{ $statusCounts['incomplete'] ?? 0 }}</div>
                            <div class="stats-label">ผลการดำเนินงานยังไม่ครบถ้วนตามเกณฑ์</div>
                        </div>
                    </div>

                    <div class="stats-card legend-item" data-key="pending">
                        <div class="stats-icon danger">
                            <i data-lucide="clock"></i>
                        </div>
                        <div class="stats-info">
                            <div class="stats-value legend-count">{{ $statusCounts['pending'] ?? 0 }}</div>
                            <div class="stats-label">อยู่ระหว่างดำเนินการ</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="stat-title">
            <h3>รายการตัวบงชี้</h3>
        </div>

        <div class="table-container">
            <div class="table-card-header">
                <div class="search-box flex-1">
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" style="color:#9ca3af;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" id="custom-search" class="search-input" placeholder="ค้นหารายการตัวบ่งชี้">
                </div>

                <!-- ปุ่ม Export -->
                @if (!auth()->user()->hasRole('administration_admin'))
                    <button id="exportExell" type="button"
                        class="btn btn-primary !bg-green-500 hover:!bg-green-600 hover:!border-green-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12" />
                        </svg>
                        <span class="inline">EXPORT</span>
                    </button>
                @endif

            </div>

            <div style="margin: 10px">
                <table class="table" id="dashboardTable">
                    <thead>
                        <tr>
                            <th>ปีการประเมิน</th>
                            <th>มาตรฐานตัวบ่งชี้</th>
                            <th>ชื่อตัวบ่งชี้</th>
                            <th>รหัส</th>
                            <th>ประเภทตัวบ่งชี้</th>
                            <th>หน่วยงานที่รับผิดชอบ</th>
                            <th>ผลลัพธ์</th>
                            <th>คะแนนรวม</th>
                            <th>สถานะตัวบ่งชี้</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($indicators as $index => $indicator)
                            @php
                                $statusKey = match ((int) $indicator->status) {
                                    3 => 'complete',
                                    4 => 'incomplete',
                                    0, 2, 1 => 'pending',
                                    default => 'pending',
                                };

                                $standardName = $indicator->category->standard->name ?? '';
                                $dimensionName = $indicator->category->name ?? '';
                                $collectorName = $indicator->assignments->first()->collectorUser->display_name ?? '';
                                $deptName = '';
                                foreach ($indicator->assignments as $assignment) {
                                    $deptName = optional($assignment->collectorUser?->department)->name ?? '';
                                    if ($deptName) {
                                        break;
                                    }
                                }
                            @endphp
                            <tr data-max="{{ (float) $indicator->max_score }}" data-standard="{{ $standardName }}"
                                data-dimension="{{ $dimensionName }}" data-collector="{{ $collectorName }}"
                                data-dept="{{ $deptName }}" data-status="{{ $statusKey }}">
                                <td class="status-cell">{{ $indicator->year }}</td>
                                <td class="status-cell">{{ $standardName ?: '-' }}</td>
                                <td>{{ $indicator->name }}</td>
                                <td class="status-cell">{{ $indicator->code }}</td>
                                <td class="status-cell">{{ $indicator->type }}</td>
                                <td class="status-cell">{{ $deptName ?: '-' }}</td>
                                <td class="status-cell">{{ $indicator->score_acc }}</td>
                                <td class="status-cell">{{ $indicator->max_score }}</td>
                                <td class="status-cell">
                                    @switch($indicator->status)
                                        @case(0)
                                        @case(1)

                                        @case(2)
                                            <span class="tip" data-tip="อยู่ระหว่างดำเนินการ">
                                                <i data-lucide="clock" class="status-icon text-danger"></i>
                                            </span>
                                        @break

                                        @case(4)
                                            <span class="tip" data-tip="ผลการดำเนินงานยังไม่ครบถ้วนตามเกณฑ์">
                                                <i data-lucide="alert-triangle" class="status-icon text-warn"></i>
                                            </span>
                                        @break

                                        @case(3)
                                            <span class="tip" data-tip="ผลการดำเนินงานครบถ้วนตามเกณฑ์มาตรการ">
                                                <i data-lucide="check-circle" class="status-icon text-success"></i>
                                            </span>
                                        @break

                                        @default
                                            <span class="tip" data-tip="สถานะไม่ระบุ ({{ $indicator->status }})">
                                                <i data-lucide="help-circle" class="status-icon text-gray-500"></i>
                                            </span>
                                    @endswitch
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <!-- lucide -->
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.469.0/dist/umd/lucide.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- html2canvas (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

    <script>
        // Export to Excel (server-side)
        document.getElementById('exportExell').addEventListener('click', function() {
            const params = new URLSearchParams();

            const year = document.getElementById('filter-year')?.value || '';
            if (year) params.set('year', year);

            const standard = document.getElementById('filter-standard')?.value || '';
            if (standard) params.set('standard_id', standard);

            const dimension = document.getElementById('filter-dimension')?.value || '';
            if (dimension) params.set('category_id', dimension);

            // status param is optional if you add a status select later
            const status = document.getElementById('filter-status')?.value || '';
            if (status !== '') params.set('status', status);

            const dept = document.getElementById('filter-dept')?.value || '';
            if (dept) params.set('dept_id', dept);

            const code = document.getElementById('filter-code')?.value || '';
            if (code) params.set('code', code);

            // type is client-side only here; skip unless your controller supports it
            // const type = document.getElementById('filter-type')?.value || '';
            // if (type) params.set('type', type);

            const url = "{{ route('dashboard.export') }}" + (params.toString() ? `?${params}` : '');
            window.location.href = url;
        });

        // Initialize Filter Component
        if (typeof window.FilterComponent !== 'undefined') {
            window.FilterComponent.init({
                onApply: function() {
                    if (typeof applyFilters === 'function') {
                        applyFilters();
                    }
                },
                onReset: function() {
                    // Reset form and apply filters
                    const form = document.getElementById('filter-form');
                    if (form) {
                        const selects = form.querySelectorAll('select');
                        selects.forEach(select => {
                            select.selectedIndex = 0;
                        });
                    }

                    // Reset to latest year
                    const latestYearVal = getLatestYear();
                    const yearSelect = document.getElementById('filter-year');
                    if (latestYearVal && yearSelect) {
                        yearSelect.value = latestYearVal;
                    }

                    if (typeof applyFilters === 'function') {
                        applyFilters();
                    }
                }
            });
        } else {
            // Fallback for toggle filter card visibility
            document.getElementById('toggle-filter').addEventListener('change', function() {
                const card = document.getElementById('filter-card');
                if (!card) return;
                card.style.display = this.checked ? 'block' : 'none';
            });
        }
    </script>

    <script>
        (function($) {
            let table, donutChart;

            const stripHtml = (s) => {
                const d = document.createElement('div');
                d.innerHTML = String(s ?? '');
                return (d.textContent || d.innerText || '').trim();
            };
            const numberFormat = (n) => (isNaN(n) ? 0 : Number(n)).toLocaleString('th-TH');

            const YEARLY_TOTALS_ARRAY = @json($yearlyTotals);
            const YEARLY_TOTALS_MAP = Array.isArray(YEARLY_TOTALS_ARRAY) ?
                YEARLY_TOTALS_ARRAY.reduce((acc, item) => {
                    const y = String(item.year ?? '');
                    acc[y] = {
                        total: Number(item.total_score ?? 0),
                        max: Number(item.max_score ?? 0)
                    };
                    return acc;
                }, {}) : {};

            const getLatestYear = () => {
                const years = Array.isArray(window.ALL_YEARS) ? window.ALL_YEARS : [];
                const nums = years.map(y => parseInt(String(y), 10)).filter(n => !isNaN(n));
                return nums.length ? Math.max(...nums) : '';
            };

            $(function() {
                // Init DataTable
                table = $('#dashboardTable').DataTable({
                    searching: true,
                    lengthChange: false,
                    dom: 'rtip',
                    order: [],
                    stateSave: false,
                    language: {
                        paginate: {
                            previous: 'ก่อนหน้า',
                            next: 'ถัดไป'
                        },
                        info: 'แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ',
                        emptyTable: 'ไม่พบข้อมูล',
                        zeroRecords: 'ไม่พบข้อมูลที่ตรงกับการค้นหา',
                    },
                });

                // Column indexes
                const heads = $('#dashboardTable thead th').map((i, th) => $(th).text().trim()).get();
                const findCol = (cands) => {
                    for (const kw of cands) {
                        const idx = heads.findIndex((h) => h.includes(kw));
                        if (idx !== -1) return idx;
                    }
                    return -1;
                };

                const idxYear = findCol(['ปีการประเมิน']);
                const idxCode = findCol(['รหัส']);
                const idxDept = findCol(['หน่วยงานที่รับผิดชอบ']);
                const idxScore = findCol(['คะแนนรวม']);
                const idxType = findCol(['ประเภทตัวบ่งชี้']); // NEW

                // Selects
                const $year = $('#filter-year'),
                    $code = $('#filter-code'),
                    $dept = $('#filter-dept'),
                    $std = $('#filter-standard'),
                    $dim = $('#filter-dimension'),
                    $collector = $('#filter-collector'),
                    $type = $('#filter-type');

                // Prefill selects from server/global vars if needed
                window.ALL_YEARS = @json($yearsForFilter);
                window.ALL_DEPARTMENTS = @json($departments);
                window.ALL_COLLECTORS = @json($collectors);
                window.ALL_STANDARDS = @json($allStandards);
                window.ALL_DIMENSIONS = @json($dimensionNames);
                window.ALL_CODES = @json($filters['codes'] ?? []);
                window.ALL_TYPES = @json($filters['types'] ?? []);

                function fillSelect($sel, items, mapper) {
                    $sel.find('option:not([value=""])').remove();
                    (items || []).forEach((item) => {
                        const opt = typeof mapper === 'function' ?
                            mapper(item) : {
                                value: String(item),
                                label: String(item)
                            };
                        $sel.append(`<option value="${opt.value}">${opt.label}</option>`);
                    });
                }

                // Fill dynamic-from-table lists
                function populateFromColumn($sel, colIdx) {
                    $sel.find('option:not([value=""])').remove();
                    if (colIdx === -1) return;
                    const vals = table.column(colIdx).data().toArray().map(stripHtml).filter(Boolean);
                    const uniq = [...new Set(vals)].sort((a, b) => a.localeCompare(b, 'th'));
                    uniq.forEach((v) => $sel.append(`<option value="${v}">${v}</option>`));
                }

                // From server
                fillSelect($year, window.ALL_YEARS);
                fillSelect($dept, window.ALL_DEPARTMENTS, it => ({
                    value: String(it?.name ?? it),
                    label: String(it?.name ?? it)
                }));
                fillSelect($collector, window.ALL_COLLECTORS, it => ({
                    value: String(it?.name ?? it),
                    label: String(it?.name ?? it)
                }));
                fillSelect($std, window.ALL_STANDARDS, it => ({
                    value: String(it?.name ?? it),
                    label: String(it?.name ?? it)
                }));
                fillSelect($dim, window.ALL_DIMENSIONS);

                // From table: code + type (and allow them to auto-refresh if table data changes)
                populateFromColumn($code, idxCode);
                populateFromColumn($type, idxType);

                // Extra search predicate
                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                    if (settings.nTable !== document.getElementById('dashboardTable')) return true;

                    const vYear = $year.val();
                    const yearVal = idxYear !== -1 ? String(data[idxYear]).trim() : '';
                    const yearNum = parseInt(yearVal, 10);
                    const vYearNum = parseInt(vYear, 10);
                    if (vYear && yearNum !== vYearNum) return false;

                    const codeVal = $code.val();
                    const deptVal = $dept.val();
                    const stdVal = $std.val();
                    const dimVal = $dim.val();
                    const colVal = $collector.val();
                    const typeVal = $type.val();

                    const node = table.row(dataIndex).node();

                    if (codeVal && String(data[idxCode]).trim() !== codeVal) return false;
                    if (deptVal && String(data[idxDept]).trim() !== deptVal) return false;
                    if (stdVal && (node?.dataset?.standard || '') !== stdVal) return false;
                    if (dimVal && (node?.dataset?.dimension || '') !== dimVal) return false;
                    if (colVal && (node?.dataset?.collector || '') !== colVal) return false;
                    if (typeVal && idxType !== -1 && String(data[idxType]).trim() !== typeVal)
                        return false;

                    if (window.selectedStatusFilter && (node?.dataset?.status || '') !== window
                        .selectedStatusFilter) {
                        return false;
                    }
                    return true;
                });

                // Default year to latest
                const latestYearVal = getLatestYear();
                if (latestYearVal) $year.val(latestYearVal).trigger('change');

                function anyExtraFilterActive() {
                    const hasNonYearSelect = ($code.val() || $dept.val() || $std.val() || $dim.val() ||
                        $collector.val() || $type.val());
                    const hasGlobalSearch = !!table.search();
                    let hasColumnSearch = false;
                    table.columns().every(function() {
                        if (this.search()) hasColumnSearch = true;
                    });
                    return !!(hasNonYearSelect || hasGlobalSearch || hasColumnSearch);
                }

                function parseNumberCell(s) {
                    const t = stripHtml(String(s)).replace(/[^0-9.,-]/g, '').replace(/,/g, '');
                    const n = Number(t);
                    return isNaN(n) ? 0 : n;
                }

                function computeFilteredTotalsForYear(targetYear) {
                    let total = 0,
                        max = 0;
                    table.rows({
                        search: 'applied',
                        page: 'all'
                    }).every(function() {
                        const node = this.node();
                        const rowData = this.data();
                        const rowYear = (idxYear !== -1 ? stripHtml(rowData[idxYear]) : '').toString();
                        if (targetYear && rowYear && rowYear !== targetYear) return;

                        const rowTotal = Number(node?.dataset?.total ?? NaN);
                        const rowMax = Number(node?.dataset?.max ?? NaN);

                        if (!Number.isNaN(rowTotal)) total += rowTotal;
                        else if (idxScore !== -1) total += parseNumberCell(rowData[idxScore]);

                        if (!Number.isNaN(rowMax)) max += rowMax;
                    });
                    return {
                        total,
                        max
                    };
                }

                function updateIndicatorTotal() {
                    const selectedYear = ($year.val() || '').toString();
                    const fallbackYear = getLatestYear();
                    const effectiveYear = selectedYear || fallbackYear || '';

                    let count = 0;
                    table.rows({
                        search: 'applied',
                        page: 'all'
                    }).every(function() {
                        const rowData = this.data();
                        let rowYear = '';
                        try {
                            rowYear = idxYear !== -1 ? stripHtml(rowData[idxYear]) : '';
                        } catch (e) {}
                        if (effectiveYear && rowYear && rowYear !== effectiveYear) return;
                        count++;
                    });
                    $("#indicator-total").text(count.toLocaleString('th-TH'));
                }

                function updateSummary() {
                    const selectedYear = ($year.val() || '').toString();

                    const latestFromMap = Object.keys(YEARLY_TOTALS_MAP).map(Number).filter(n => !isNaN(n))
                        .sort((a, b) => b - a)[0];
                    const latestYearFromMap = latestFromMap ? String(latestFromMap) : '';
                    const fallbackYearFromTable = getLatestYear() || '';
                    const effectiveYear = selectedYear || latestYearFromMap || fallbackYearFromTable;

                    if (!effectiveYear) {
                        $('#display-year, #display-years').text('ไม่มีข้อมูล');
                        $('#display-total').text('0');
                        $('#display-max').text('0');
                        return;
                    }

                    $('#display-year, #display-years').text(effectiveYear);

                    if (!anyExtraFilterActive()) {
                        const y = YEARLY_TOTALS_MAP[effectiveYear] || {
                            total: 0,
                            max: 0
                        };
                        $('#display-total').text(numberFormat(y.total));
                        $('#display-max').text(numberFormat(y.max));
                        return;
                    }

                    const {
                        total,
                        max
                    } = computeFilteredTotalsForYear(effectiveYear);
                    $('#display-total').text(numberFormat(total));
                    $('#display-max').text(numberFormat(max));
                }

                function applyFilters() {
                    table.draw();
                }

                // Chart.js Pie (status)
                let donutCanvas = document.getElementById('satisfactionChart');
                let chartKeys = [],
                    chartLabels = [],
                    chartColors = [];

                if (donutCanvas) {
                    const donutCtx = donutCanvas.getContext('2d');
                    chartLabels = @json(array_column($legendConfig, 'label'));
                    chartColors = @json(array_column($legendConfig, 'color'));
                    chartKeys = @json(array_column($legendConfig, 'key'));

                    const countsMap = @json($statusCounts);
                    const dataValues = chartKeys.map((k) => Number(countsMap[k] ?? 0));

                    donutChart = new Chart(donutCtx, {
                        type: 'pie',
                        data: {
                            labels: chartLabels,
                            datasets: [{
                                data: dataValues,
                                backgroundColor: chartColors,
                                borderColor: '#ffffff',
                                borderWidth: 2,
                                hoverOffset: 6,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        font: {
                                            size: 12
                                        },
                                        padding: 15
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: (ctx) => {
                                            const total = ctx.dataset.data.reduce((a, b) => a + b,
                                                0);
                                            const val = ctx.parsed;
                                            const pct = total > 0 ? ((val / total) * 100).toFixed(
                                                1) : '0.0';
                                            return ` ${ctx.label}: ${val} (${pct}%)`;
                                        },
                                    },
                                },
                                datalabels: {
                                    color: '#fff',
                                    font: {
                                        weight: 'bold',
                                        size: 14
                                    },
                                    formatter: (value, ctx) => {
                                        const total = ctx.chart.data.datasets[0].data.reduce((a,
                                            b) => a + b, 0);
                                        const pct = total > 0 ? (value / total * 100).toFixed(1) :
                                            0;
                                        return pct + '%';
                                    }
                                }
                            },
                            onClick: (evt, elements) => {
                                if (elements.length > 0) {
                                    const index = elements[0].index;
                                    const key = chartKeys[index];
                                    window.selectedStatusFilter = (window.selectedStatusFilter ===
                                        key) ? null : key;
                                    table.draw();
                                }
                            }
                        },
                        plugins: [ChartDataLabels]
                    });
                }

                function computeStatusCountsFiltered() {
                    const counts = Object.fromEntries(chartKeys.map((k) => [k, 0]));
                    const selectedYear = ($year.val() || '').toString();
                    const fallbackYear = getLatestYear();
                    const effectiveYear = selectedYear || fallbackYear || '';

                    table.rows({
                        search: 'applied',
                        page: 'all'
                    }).every(function() {
                        const key = this.node().dataset.status;
                        let rowYear = '';
                        try {
                            const rowData = this.data();
                            rowYear = idxYear !== -1 ? stripHtml(rowData[idxYear]) : '';
                        } catch (e) {}
                        if (effectiveYear && rowYear && rowYear !== effectiveYear) return;
                        if (key && Object.prototype.hasOwnProperty.call(counts, key)) counts[key] += 1;
                    });
                    return counts;
                }

                function updateLegend(counts) {
                    chartKeys.forEach((k) => {
                        const c = counts[k] ?? 0;
                        const $item = $(`.legend-item[data-key="${k}"]`);
                        $item.find('.legend-count').text(c);
                    });
                }

                function updateDonutAndLegend() {
                    if (!donutChart) return;
                    const counts = computeStatusCountsFiltered();
                    donutChart.data.datasets[0].data = chartKeys.map((k) => counts[k] ?? 0);
                    donutChart.update();
                    updateLegend(counts);
                }

                // เพิ่มฟังก์ชัน applyFilters สำหรับ FilterComponent
                window.applyFilters = function() {
                    table.page('first').draw(false);
                    table.one('draw', function() {
                        updateSummary();
                        updateDonutAndLegend();
                        updateIndicatorTotal();
                    });
                };

                // Global search debounce
                let timer;
                $('#custom-search')
                    .on('input', function() {
                        clearTimeout(timer);
                        const val = this.value;
                        timer = setTimeout(() => {
                            table.search(val).draw();
                        }, 150);
                    })
                    .on('search', function() {
                        if (this.value === '') {
                            table.search('').draw();
                        }
                    });

                // Recalc on draw
                table.on('draw', function() {
                    updateSummary();
                    updateDonutAndLegend();
                    updateIndicatorTotal();
                });

                // Initial calc
                updateSummary();
                updateDonutAndLegend();
                updateIndicatorTotal();

                // Render lucide icons
                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons();
                }
            });
        })(jQuery);
    </script>
@endpush

@push('styles')
    <style>
        .card-title {
            font-size: 18px;
            color: var(--blue-default);
            margin: 0 0 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            position: relative;
            padding-left: 10px;
        }

        .card-title::before {
            content: "";
            width: 4px;
            height: 20px;
            border-radius: 8px;
            background: var(--blue-default);
            position: absolute;
            left: 0;
            top: 2px;
            opacity: .25;
        }

        /* ===== Switch ===== */
        .switch {
            position: relative;
            display: inline-block;
            width: 46px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            inset: 0;
            background: var(--color-gray-300);
            transition: .3s;
            border-radius: 34px;
        }

        .slider:before {
            content: "";
            position: absolute;
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background: var(--color-white);
            transition: .3s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background: var(--color-blue-500);
        }

        input:checked+.slider:before {
            transform: translateX(22px);
        }

        /* ===== Search / Stats / Chart / Table (kept from your page) ===== */
        .search-box {
            position: relative;
            background: var(--color-white);
            border-radius: 8px;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            max-width: 420px;
        }

        .search-box .icon {
            position: absolute;
            inset: 0 auto 0 12px;
            display: flex;
            align-items: center;
            pointer-events: none;
        }

        .search-input {
            padding: 8px 16px 8px 40px;
            width: 100%;
            outline: 0;
            border: 1px solid var(--color-gray-300);
            border-radius: 16px;
        }

        .search-input:focus {
            border-color: var(--color-blue-500);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .2);
        }

        .stats-grid {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .stat-card {
            background: var(--color-white);
            border: 1px solid var(--color-gray-200);
            border-radius: 12px;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            padding: 18px 18px 20px;
        }

        .stat-title {
            display: flex;
            align-items: baseline;
            gap: 10px;
            flex-wrap: wrap;
            padding-left: 30px;
        }

        .stat-title h3 {
            margin-top: 20px;
            font-size: 20px;
            font-weight: 800;
            color: var(--color-gray-900);
        }

        .stat-body {
            display: flex;
            gap: 28px;
            align-items: center;
        }

        .legend-wrap {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .stats-card {
            display: flex;
            align-items: center;
            gap: 16px;
            background: var(--color-white);
            border: 1px solid var(--color-gray-200);
            border-radius: 12px;
            padding: 16px 20px;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            margin-bottom: 14px;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, .08);
        }

        .stats-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            border-radius: 8px;
            background: var(--color-gray-50);
            color: var(--color-gray-700);
        }

        .stats-icon.success {
            color: var(--color-emerald-600);
        }

        .stats-icon.warn {
            color: var(--color-amber-500);
        }

        .stats-icon.danger {
            color: var(--color-red-500);
        }

        .stats-info {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .stats-value {
            font-size: 20px;
            font-weight: 700;
            color: var(--color-gray-900);
        }

        .stats-label {
            font-size: 13px;
            color: var(--color-gray-500);
        }

        .status-cell {
            text-align: center;
            vertical-align: middle;
            padding: .5rem;
        }

        .status-icon {
            width: 18px;
            height: 18px;
            display: inline-block;
            vertical-align: middle;
        }

        .text-success {
            color: var(--color-green-500);
        }

        .text-danger {
            color: var(--color-red-500);
        }

        .text-warn {
            color: var(--color-yellow-400);
        }

        .tip {
            position: relative;
            display: inline-flex;
            align-items: center;
        }

        .tip[data-tip]::after {
            content: attr(data-tip);
            position: absolute;
            left: 50%;
            bottom: calc(100% + 10px);
            transform: translateX(-50%) translateY(4px);
            white-space: nowrap;
            background: var(--color-white);
            color: var(--color-slate-700);
            border: 1px solid var(--color-gray-200);
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 12px;
            line-height: 1.2;
            box-shadow: 0 6px 18px rgba(0, 0, 0, .08);
            opacity: 0;
            pointer-events: none;
            transition: opacity .16s ease, transform .16s ease;
            z-index: 50;
        }

        .tip[data-tip]::before {
            content: "";
            position: absolute;
            left: 50%;
            bottom: calc(100% + 6px);
            width: 8px;
            height: 8px;
            background: var(--color-white);
            border-left: 1px solid var(--color-gray-200);
            border-top: 1px solid var(--color-gray-200);
            transform: translateX(-50%) rotate(45deg);
            box-shadow: 0 2px 4px rgba(0, 0, 0, .06);
            opacity: 0;
            transition: opacity .16s ease;
            z-index: 49;
        }

        .btn-export-excel {
            background: var(--color-green-50);
            color: var(--color-green-600);
            border: 2px solid var(--color-green-300);
            padding: 8px 14px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: 0 1px 0 rgba(0, 0, 0, .02);
        }

        .btn-export-excel:hover {
            background: var(--color-green-100);
        }

        .tip:hover::after,
        .tip:hover::before,
        .tip:focus-visible::after,
        .tip:focus-visible::before {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        .chart-wrap {
            width: 480px;
            height: 320px;
            padding: 6px 10px;
        }

        .table-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 20px 30px 15px;

        }

        .table-container {
            width: 100%;
            /* max-width: 1500px; */
            background: var(--color-white);
            border: 1px solid var(--color-gray-200);
            box-shadow: 0 2px 10px rgba(0, 0, 0, .1);
            /* overflow: hidden */
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th {
            text-align: left;
            font-weight: 600;
            background: var(--color-gray-50);
            border-bottom: 1px solid var(--color-gray-200);
            padding: 12px;
        }

        .table tbody td {
            padding: 12px;
            border-bottom: 1px solid var(--color-gray-200);
        }

        i[data-lucide] {
            display: inline-block;
            vertical-align: middle;
        }

        /* ====== Responsive ranges you use ====== */
        @media (max-width: 639px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .stat-title {
                padding-left: 12px;
            }

            .stat-title h3 {
                margin-top: 6px;
                font-size: 16px;
            }

            .stats-grid {
                gap: 12px;
            }

            .stat-card {
                padding: 14px;
            }

            .stat-body {
                flex-direction: column;
                align-items: stretch;
                gap: 16px;
            }

            .chart-wrap {
                width: 100%;
                height: 220px;
            }

            .table-card-header {
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
            }

            .btn-export-excel {
                align-self: flex-end;
            }


            .table {
                font-size: 12px;
                min-width: 700px;
            }

            .table thead th,
            .table tbody td {
                padding: 8px;
            }

            .table-container {
                width: 100%;
                /* max-width: 1500px; */
                background: var(--color-white);
                border: 1px solid var(--color-gray-200);
                box-shadow: 0 2px 10px rgba(0, 0, 0, .1);
                /* overflow: hidden */
            }

            .search-box {
                max-width: none;
            }



        }

        @media (min-width:640px) and (max-width:767px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .stat-title {
                padding-left: 16px;
            }

            .stat-title h3 {
                font-size: 18px;
            }

            .stat-body {
                flex-direction: column;
                align-items: stretch;
                gap: 18px;
            }

            .chart-wrap {
                width: 100%;
                height: 260px;
            }

            .table {
                font-size: 13px;
                min-width: 900px;
            }

            .table thead th,
            .table tbody td {
                padding: 10px;
            }
        }

        @media (min-width:768px) and (max-width:1023px) {
            .form-grid {
                grid-template-columns: 1fr 1fr;
            }

            .stat-title {
                padding-left: 20px;
            }

            .stat-title h3 {
                font-size: 19px;
            }

            .stat-body {
                flex-direction: row;
                gap: 22px;
            }

            .chart-wrap {
                width: 420px;
                height: 280px;
            }

            .table {
                font-size: 14px;
            }

            .table thead th,
            .table tbody td {
                padding: 10px;
            }
        }

        @media (min-width:1024px) and (max-width:1279px) {
            .stat-title {
                padding-left: 24px;
            }

            .stat-title h3 {
                font-size: 20px;
            }

            .stat-body {
                gap: 24px;
            }

            .chart-wrap {
                width: 460px;
                height: 300px;
            }

            .table thead th,
            .table tbody td {
                padding: 12px;
            }
        }

        @media (min-width:1280px) and (max-width:1535px) {
            .stat-title {
                padding-left: 28px;
            }

            .stat-title h3 {
                font-size: 20px;
            }

            .stat-body {
                gap: 26px;
            }

            .chart-wrap {
                width: 480px;
                height: 320px;
            }
        }

        @media (min-width:1536px) {
            .chart-wrap {
                width: 520px;
                height: 340px;
            }
        }
    </style>
@endpush
