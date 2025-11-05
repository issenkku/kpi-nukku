@extends('layouts.app')
@section('title', 'กราฟผลลัพธ์')

@section('header')
    กราฟผลลัพธ์การประเมิน
@endsection

@section('subheader')
    ระบบบริหารจัดการข้อมูลการรับรองสถาบันจากสภาการพยาบาล
@endsection

@section('content')
    <div class="space-y-6">
        <div class="card-container">
            <h2 class="card-title">คะแนนรวมตามปี</h2>
            <!-- ✅ Checkbox เลือกปี -->
            <div id="year-filters" style="margin-bottom:10px;margin-left: 20px;">
                @foreach ($filters['years'] as $y)
                    <label style="margin-right:10px;">
                        <input name="year-checkbox-overall" type="checkbox" class="year-checkbox" value="{{ $y }}"
                            checked>
                        {{ $y }}
                    </label>
                @endforeach
            </div>

            <!-- เปลี่ยนจาก canvas เป็น div สำหรับ ApexCharts -->
            <div class="chart-card" id="scoreLineCard" style="position:relative;">
                <button data-html2canvas-ignore="true" type="button" class="btn-download" data-target="scoreLineCard"
                    style="background:#fff;border:1px solid #ddd;padding:6px;border-radius:8px;cursor:pointer;line-height:1;display:flex;align-items:center;justify-content:center;transition:all .2s ease;position:absolute;top:10px;right:10px;z-index:2;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16" />
                    </svg>
                </button>
                <div id="scoreLineChart"></div>
            </div>
        </div>
        <div class="card-container">
            <h2 class="card-title">คะแนนรวมมาตรฐานตามปี</h2>

            <!-- ✅ Checkbox ปี -->
            <div id="year-filters-standard" style="margin-bottom:10px;margin-left: 20px;">
                @foreach ($filters['years'] as $y)
                    <label style="margin-right:10px;">
                        <input name="year-checkbox-std" type="checkbox" class="year-checkbox-std"
                            value="{{ $y }}" checked>
                        {{ $y }}
                    </label>
                @endforeach
            </div>
            <!-- ✅ Grid 3 คอลัมน์ -->
            <div class="charts-of-standards">
                @foreach ($chartsStandardBars as $chart)
                    <div class="chart-card standard-card" id="stdCard-{{ $chart['id'] }}" style="position:relative;">
                        <button data-html2canvas-ignore="true" type="button" class="btn-download"
                            data-target="stdCard-{{ $chart['id'] }}"
                            style="background:#fff;border:1px solid #ddd;padding:6px;border-radius:8px;cursor:pointer;line-height:1;display:flex;align-items:center;justify-content:center;transition:all .2s ease;position:absolute;top:10px;right:10px;z-index:2;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16" />
                            </svg>
                        </button>
                        <h3 style="margin-bottom:10px;">{{ $chart['name'] }}</h3>
                        <div id="stdChart-{{ $chart['id'] }}" style="height:auto;"></div>

                        <script type="application/json" id="stdData-{{ $chart['id'] }}">
                {!! json_encode([
                    'labels' => $chart['labels'],
                    'scores' => $chart['scores'],
                    'max'    => $chart['max'],
                ], JSON_UNESCAPED_UNICODE) !!}
            </script>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="card-container">
            <h2 class="card-title">คะแนนรวมตามด้าน</h2>
            <!-- ✅ Checkbox ปี -->
            <div id="year-filters-dim" style="margin-bottom:10px;margin-left: 20px;">
                @foreach ($filters['years'] as $y)
                    <label style="margin-right:10px;">
                        <input name="year-checkbox-dim" type="checkbox" class="year-checkbox-dim"
                            value="{{ $y }}" checked>
                        {{ $y }}
                    </label>
                @endforeach
            </div>
            <div class="charts-of-dimensions">
                @foreach ($chartDimensions as $chart)
                    <div class="chart-card dim-card" id="dimCard-{{ $chart['id'] }}" style="position:relative;">
                        <button data-html2canvas-ignore="true" type="button" class="btn-download"
                            data-target="dimCard-{{ $chart['id'] }}"
                            style="background:#fff;border:1px solid #ddd;padding:6px;border-radius:8px;cursor:pointer;line-height:1;display:flex;align-items:center;justify-content:center;transition:all .2s ease;position:absolute;top:10px;right:10px;z-index:2;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16" />
                            </svg>
                        </button>
                        <h3 style="margin-bottom:10px;">{{ $chart['name'] }}</h3>
                        <div id="dimChart-{{ $chart['id'] }}" style="height:auto;"></div>

                        <script type="application/json" id="dimData-{{ $chart['id'] }}">
                    {!! json_encode([
                        'labels' => $chart['labels'],
                        'scores' => $chart['scores'],
                        'max'    => $chart['max'],
                    ], JSON_UNESCAPED_UNICODE) !!}
                </script>
                    </div>
                @endforeach
            </div>
        </div>
        <!-- Toggle Switch -->
        <div class="flex items-center justify-end gap-2">
            <label class="switch">
                <input name="filter-checkbox" type="checkbox" id="toggle-filter">
                <span class="slider round"></span>
            </label>
            <span>กรองข้อมูล</span>
        </div>

        <!-- Filter Component -->
        <x-filter :years="$filters['years'] ?? []" :standards="$filters['standards'] ?? []" :departments="[]" :collectors="[]" :dimensions="$filters['dimensions'] ?? []"
            :filters="$filters" :action="route('dashboard.getData')" title="กรองข้อมูลการประเมิน" filterId="filter-panel"
            formId="result-filter-form" :showFields="[
                'year' => true,
                'codes' => true,
                'standard' => true,
                'dimension' => true,
                'department' => false,
                'collector' => false,
                'type' => true,
            ]">
            <script>
                // Custom JavaScript สำหรับหน้า result
                document.addEventListener('DOMContentLoaded', function() {
                    // รอให้ FilterComponent โหลดเสร็จ
                    setTimeout(function() {
                        if (typeof window.FilterComponent !== 'undefined') {
                            window.FilterComponent.init({
                                filterId: 'filter-panel',
                                formId: 'result-filter-form',
                                onApply: function() {
                                    console.log('Filter applied in result page');
                                    // เรียกใช้ฟังก์ชัน applyFilters ของหน้า result
                                    if (typeof window.applyResultFilters === 'function') {
                                        window.applyResultFilters();
                                    }
                                },
                                onReset: function() {
                                    console.log('Filter reset in result page');
                                    // เรียกใช้ฟังก์ชัน resetFilters ของหน้า result
                                    if (typeof window.resetResultFilters === 'function') {
                                        window.resetResultFilters();
                                    }
                                }
                            });
                        }
                    }, 100);
                });
            </script>
        </x-filter>
        <div class="card-container">
            <h2 class="card-title">คะแนนตามตัวชี้วัด</h2>

            <div class="search-box flex-1 max-w-[420px]">
                <div class="icon">
                    <!-- search icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" style="color:#9ca3af;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input name="custom-search" type="text" id="custom-search" class="search-input"
                    placeholder="ค้นหารายการตัวบ่งชี้">
            </div>
            <div class="charts-grid">
                @foreach ($standards as $standard)
                    <div class="text-center border-y border-gray-400 border-dashed py-1 mt-3">
                        <h3>กราฟผลลัพธ์ {{ $standard->name }}</h3>
                    </div>
                    @php $bucket = $chartsByStandard[$standard->id] ?? null; @endphp
                    @if ($bucket && !empty($bucket['indicators']))
                        <div class="charts-of-standard-inds" data-standard-id="{{ $standard->id }}">

                            @foreach ($bucket['indicators'] as $i => $c)
                                <div class="chart-card enhanced-chart-card" id="card-{{ $c['indicator_id'] }}"
                                    data-standard="{{ $standard->id }}" data-dimension="{{ $c['category_name'] }}"
                                    data-type="{{ $c['indicator_type'] }}" data-code="{{ $c['indicator_code'] }}"
                                    data-years='@json($c['years'])' data-index="{{ $i }}"
                                    style="{{ $i >= 5 ? 'display:none;' : '' }}background:#fff;border-radius:16px;
                                padding:20px;box-shadow:0 4px 20px rgba(0,0,0,.08);position:relative;
                                border: 1px solid rgba(0,0,0,0.05);">

                                    {{-- หัวข้อ --}}
                                    <div class="chart-header"
                                        style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                                        <div class="chart-title" style="font-weight:700;color:#1f2937;font-size:14px;">
                                            {{ $c['indicator_code'] ? '[' . $c['indicator_code'] . '] ' : '' }}
                                            {{ $c['indicator_name'] }}
                                        </div>

                                        <button data-html2canvas-ignore="true" type="button" class="btn-download"
                                            data-target="card-{{ $c['indicator_id'] }}"
                                            style="background:#fff;border:1px solid #ddd;
           padding:6px;border-radius:8px;cursor:pointer;
           line-height:1;display:flex;align-items:center;
           justify-content:center;transition:all .2s ease;">
                                            <!-- SVG icon -->
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16" />
                                            </svg>
                                        </button>
                                    </div>
                                    {{-- กราฟ --}}
                                    <div class="chart-wrapper" style="position:relative;margin-bottom:10px;">
                                        <div id="chart-{{ $standard->id }}-{{ $c['indicator_id'] }}"
                                            class="responsive-chart-individual">
                                        </div>
                                    </div>
                                    <script id="data-{{ $standard->id }}-{{ $c['indicator_id'] }}" type="application/json">
                                        {!! json_encode(
                                            [
                                                'years' => $c['years'],
                                                'values' => $c['values'],
                                                'max_values' => $c['max_values'],
                                            ],
                                            JSON_UNESCAPED_UNICODE,
                                        ) !!}
                                    </script>
                                </div>
                            @endforeach
                        </div>
                        @if (count($bucket['indicators']) > 10)
                            <div class="divider-btn" data-standard-id="{{ $standard->id }}">
                                <span class="divider-line"></span>
                                <button type="button" class="btn-show-more" data-standard-id="{{ $standard->id }}">
                                    แสดงเพิ่มเติม ▼
                                </button>
                                <span class="divider-line"></span>
                            </div>
                        @endif
                    @else
                        <div style="color:#6b7280;margin-bottom:16px;">ไม่มีข้อมูลตัวบ่งชี้ที่มีการบันทึกผลลัพธ์</div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.469.0/dist/umd/lucide.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <!-- ✅ โหลด html2canvas ที่นี่ -->
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <!-- โหลด ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        // Responsive chart height function
        function getResponsiveChartHeight() {
            const width = window.innerWidth;
            if (width <= 639) return 200; // Mobile
            if (width <= 767) return 220; // Small Tablet
            if (width <= 1023) return 250; // Large Tablet
            if (width <= 1279) return 280; // Small Desktop
            if (width <= 1535) return 300; // Large Desktop
            return 320; // Extra Large Desktop
        }

        document.addEventListener("DOMContentLoaded", () => {
            const charts = {};
            const originals = {};
            const chartHeight = getResponsiveChartHeight();

            document.querySelectorAll('[id^="stdData-"]').forEach(el => {
                const id = el.id.replace('stdData-', '');
                const payload = JSON.parse(el.textContent);

                originals[id] = payload;

                const options = {
                    chart: {
                        type: 'bar',
                        height: chartHeight,
                        toolbar: {
                            show: false
                        }
                    },
                    series: [{
                            name: "คะแนนที่ได้",
                            data: (payload.scores || []).map(v => Number(v) || 0)

                        },
                        {
                            name: "คะแนนเต็ม",
                            data: payload.max
                        }
                    ],
                    xaxis: {
                        categories: payload.labels,
                        title: {
                            text: "ปีการประเมิน",
                            style: {
                                fontSize: window.innerWidth <= 639 ? '11px' : '12px'
                            }
                        },
                        labels: {
                            style: {
                                fontSize: window.innerWidth <= 639 ? '10px' : '11px'
                            }
                        }
                    },
                    yaxis: {
                        title: {
                            text: "คะแนน",
                            style: {
                                fontSize: window.innerWidth <= 639 ? '11px' : '12px'
                            }
                        },
                        labels: {
                            style: {
                                fontSize: window.innerWidth <= 639 ? '10px' : '11px'
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: val => val.toLocaleString('th-TH'),
                        style: {
                            fontSize: window.innerWidth <= 639 ? '9px' : '10px'
                        }
                    },
                    colors: ['#3b82f6', '#9ca3af'],
                    legend: {
                        position: 'top',
                        fontSize: window.innerWidth <= 639 ? '10px' : '12px'
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: window.innerWidth <= 639 ? 2 : 4,
                            columnWidth: window.innerWidth <= 639 ? '60%' : '50%'
                        }
                    }
                };

                const chart = new ApexCharts(document.querySelector(`#stdChart-${id}`), options);
                chart.render();
                charts[id] = chart;
            });

            // Window resize handler
            let resizeTimeout;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(function() {
                    const newHeight = getResponsiveChartHeight();
                    Object.values(charts).forEach(chart => {
                        chart.updateOptions({
                            chart: {
                                height: newHeight
                            },
                            xaxis: {
                                title: {
                                    style: {
                                        fontSize: window.innerWidth <= 639 ?
                                            '11px' : '12px'
                                    }
                                },
                                labels: {
                                    style: {
                                        fontSize: window.innerWidth <= 639 ?
                                            '10px' : '11px'
                                    }
                                }
                            },
                            yaxis: {
                                title: {
                                    style: {
                                        fontSize: window.innerWidth <= 639 ?
                                            '11px' : '12px'
                                    }
                                },
                                labels: {
                                    style: {
                                        fontSize: window.innerWidth <= 639 ?
                                            '10px' : '11px'
                                    }
                                }
                            },
                            dataLabels: {
                                style: {
                                    fontSize: window.innerWidth <= 639 ? '9px' :
                                        '10px'
                                }
                            },
                            legend: {
                                fontSize: window.innerWidth <= 639 ? '10px' : '12px'
                            },
                            plotOptions: {
                                bar: {
                                    borderRadius: window.innerWidth <= 639 ? 2 : 4,
                                    columnWidth: window.innerWidth <= 639 ? '60%' :
                                        '50%'
                                }
                            }
                        });
                    });
                }, 250);
            });

            // === filter by year ===
            function applyYearFilter() {
                const years = Array.from(document.querySelectorAll('.year-checkbox-std:checked'))
                    .map(cb => String(cb.value));

                Object.entries(charts).forEach(([id, chart]) => {
                    const orig = originals[id];
                    const idxs = orig.labels.map((y, i) => years.includes(String(y)) ? i : -1).filter(i =>
                        i >= 0);

                    const newLabels = idxs.map(i => orig.labels[i]);
                    const newScores = idxs.map(i => orig.scores[i]);
                    const newMax = idxs.map(i => orig.max[i]);

                    chart.updateOptions({
                        xaxis: {
                            categories: newLabels
                        },
                        series: [{
                                name: "คะแนนที่ได้",
                                data: newScores
                            },
                            {
                                name: "คะแนนเต็ม",
                                data: newMax
                            }
                        ]
                    }, true, true);
                });
            }

            document.querySelectorAll('.year-checkbox-std').forEach(cb => {
                cb.addEventListener('change', applyYearFilter);
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const chartsDim = {};
            const originalsDim = {};
            const chartHeight = getResponsiveChartHeight();

            document.querySelectorAll('[id^="dimData-"]').forEach(el => {
                const id = el.id.replace('dimData-', '');
                const payload = JSON.parse(el.textContent);

                originalsDim[id] = payload;

                const options = {
                    chart: {
                        type: 'bar',
                        height: chartHeight,
                        toolbar: {
                            show: false
                        }
                    },
                    series: [{
                            name: "คะแนนที่ได้",
                            data: payload.scores
                        },
                        {
                            name: "คะแนนเต็ม",
                            data: payload.max
                        }
                    ],
                    xaxis: {
                        categories: payload.labels,
                        title: {
                            text: "ปีการประเมิน",
                            style: {
                                fontSize: window.innerWidth <= 639 ? '11px' : '12px'
                            }
                        },
                        labels: {
                            style: {
                                fontSize: window.innerWidth <= 639 ? '10px' : '11px'
                            }
                        }
                    },
                    yaxis: {
                        title: {
                            text: "คะแนน",
                            style: {
                                fontSize: window.innerWidth <= 639 ? '11px' : '12px'
                            }
                        },
                        labels: {
                            style: {
                                fontSize: window.innerWidth <= 639 ? '10px' : '11px'
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: val => val.toLocaleString('th-TH'),
                        style: {
                            fontSize: window.innerWidth <= 639 ? '9px' : '10px'
                        }
                    },
                    colors: ['#10b981', '#9ca3af'], // เขียว + เทา
                    legend: {
                        position: 'top',
                        fontSize: window.innerWidth <= 639 ? '10px' : '12px'
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: window.innerWidth <= 639 ? 2 : 4,
                            columnWidth: window.innerWidth <= 639 ? '60%' : '50%'
                        }
                    }
                };

                const chart = new ApexCharts(document.querySelector(`#dimChart-${id}`), options);
                chart.render();
                chartsDim[id] = chart;
            });

            // Window resize handler for dimension charts
            let resizeTimeoutDim;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimeoutDim);
                resizeTimeoutDim = setTimeout(function() {
                    const newHeight = getResponsiveChartHeight();
                    Object.values(chartsDim).forEach(chart => {
                        chart.updateOptions({
                            chart: {
                                height: newHeight
                            },
                            xaxis: {
                                title: {
                                    style: {
                                        fontSize: window.innerWidth <= 639 ?
                                            '11px' : '12px'
                                    }
                                },
                                labels: {
                                    style: {
                                        fontSize: window.innerWidth <= 639 ?
                                            '10px' : '11px'
                                    }
                                }
                            },
                            yaxis: {
                                title: {
                                    style: {
                                        fontSize: window.innerWidth <= 639 ?
                                            '11px' : '12px'
                                    }
                                },
                                labels: {
                                    style: {
                                        fontSize: window.innerWidth <= 639 ?
                                            '10px' : '11px'
                                    }
                                }
                            },
                            dataLabels: {
                                style: {
                                    fontSize: window.innerWidth <= 639 ? '9px' :
                                        '10px'
                                }
                            },
                            legend: {
                                fontSize: window.innerWidth <= 639 ? '10px' : '12px'
                            },
                            plotOptions: {
                                bar: {
                                    borderRadius: window.innerWidth <= 639 ? 2 : 4,
                                    columnWidth: window.innerWidth <= 639 ? '60%' :
                                        '50%'
                                }
                            }
                        });
                    });
                }, 250);
            });

            // === filter by year ===
            function applyDimYearFilter() {
                const years = Array.from(document.querySelectorAll('.year-checkbox-dim:checked'))
                    .map(cb => String(cb.value));

                Object.entries(chartsDim).forEach(([id, chart]) => {
                    const orig = originalsDim[id];
                    const idxs = orig.labels.map((y, i) => years.includes(String(y)) ? i : -1).filter(i =>
                        i >= 0);

                    const newLabels = idxs.map(i => orig.labels[i]);
                    const newScores = idxs.map(i => orig.scores[i]);
                    const newMax = idxs.map(i => orig.max[i]);

                    chart.updateOptions({
                        xaxis: {
                            categories: newLabels
                        },
                        series: [{
                                name: "คะแนนที่ได้",
                                data: newScores
                            },
                            {
                                name: "คะแนนเต็ม",
                                data: newMax
                            }
                        ]
                    }, true, true);
                });
            }
            document.querySelectorAll('.year-checkbox-dim').forEach(cb => {
                cb.addEventListener('change', applyDimYearFilter);
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const chartsMain = {};
            const originalsMain = {};
            // === โหลดข้อมูลจาก blade ===
            const payload = @json($yearlyTotals);
            originalsMain['scoreLine'] = payload;

            // Get responsive height for main chart
            function getMainChartHeight() {
                const width = window.innerWidth;
                if (width <= 639) return 280; // Mobile
                if (width <= 767) return 320; // Small Tablet
                if (width <= 1023) return 350; // Large Tablet
                if (width <= 1279) return 365; // Small Desktop
                if (width <= 1535) return 365; // Large Desktop
                return 400; // Extra Large Desktop
            }

            const options = {
                chart: {
                    type: 'area',
                    height: getMainChartHeight(),
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    }
                },
                series: [{
                        name: "คะแนนที่ได้",
                        data: payload.map(r => Number(r.score) || 0)
                    },
                    {
                        name: "คะแนนเต็ม",
                        data: payload.map(r => Number(r.max) || 0)
                    }
                ],
                xaxis: {
                    categories: payload.map(r => r.year),
                    title: {
                        text: "ปีการประเมิน",
                        style: {
                            fontSize: window.innerWidth <= 639 ? '12px' : '14px'
                        }
                    },
                    labels: {
                        style: {
                            fontSize: window.innerWidth <= 639 ? '11px' : '12px'
                        }
                    }
                },
                yaxis: {
                    min: 0,
                    max: payload.length ? Math.max(...payload.map(r => r.max)) * 1.15 : 100,
                    title: {
                        text: "คะแนน",
                        style: {
                            fontSize: window.innerWidth <= 639 ? '12px' : '14px'
                        }
                    },
                    labels: {
                        style: {
                            fontSize: window.innerWidth <= 639 ? '11px' : '12px'
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    background: {
                        enabled: true,
                        foreColor: '#fff',
                        borderRadius: 4,
                        padding: 4,
                        opacity: 0.9
                    },
                    formatter: function(val, opts) {
                        if (val === null || isNaN(val)) return "";
                        if (opts.seriesIndex === 0) {
                            const row = payload[opts.dataPointIndex];
                            const percent = row.max ? ((row.score / row.max) * 100).toFixed(1) : 0;
                            return `${val} (${percent}%)`;
                        }
                        return "";
                    },
                    offsetY: -10,
                    style: {
                        fontSize: window.innerWidth <= 639 ? '9px' : '11px'
                    }
                },
                colors: ['#4f46e5', '#94a3b8'],
                stroke: {
                    curve: 'smooth',
                    width: window.innerWidth <= 639 ? 2 : 3
                },
                markers: {
                    size: window.innerWidth <= 639 ? 4 : 5,
                    colors: ['#fff'],
                    strokeColors: ['#4f46e5', '#94a3b8'],
                    strokeWidth: 2,
                    hover: {
                        size: window.innerWidth <= 639 ? 6 : 7
                    }
                },
                grid: {
                    padding: {
                        top: 40,
                        right: window.innerWidth <= 639 ? 15 : 30,
                        bottom: 10,
                        left: window.innerWidth <= 639 ? 10 : 20
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    fontSize: window.innerWidth <= 639 ? '11px' : '12px'
                }
            };

            const chart = new ApexCharts(document.querySelector("#scoreLineChart"), options);
            chart.render();
            chartsMain['scoreLine'] = chart;

            // Window resize handler for main chart
            let resizeTimeoutMain;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimeoutMain);
                resizeTimeoutMain = setTimeout(function() {
                    const newHeight = getMainChartHeight();
                    chartsMain['scoreLine'].updateOptions({
                        chart: {
                            height: newHeight
                        },
                        xaxis: {
                            title: {
                                style: {
                                    fontSize: window.innerWidth <= 639 ? '12px' : '14px'
                                }
                            },
                            labels: {
                                style: {
                                    fontSize: window.innerWidth <= 639 ? '11px' : '12px'
                                }
                            }
                        },
                        yaxis: {
                            title: {
                                style: {
                                    fontSize: window.innerWidth <= 639 ? '12px' : '14px'
                                }
                            },
                            labels: {
                                style: {
                                    fontSize: window.innerWidth <= 639 ? '11px' : '12px'
                                }
                            }
                        },
                        dataLabels: {
                            style: {
                                fontSize: window.innerWidth <= 639 ? '9px' : '11px'
                            }
                        },
                        stroke: {
                            width: window.innerWidth <= 639 ? 2 : 3
                        },
                        markers: {
                            size: window.innerWidth <= 639 ? 4 : 5,
                            hover: {
                                size: window.innerWidth <= 639 ? 6 : 7
                            }
                        },
                        grid: {
                            padding: {
                                top: 40,
                                right: window.innerWidth <= 639 ? 15 : 30,
                                bottom: 10,
                                left: window.innerWidth <= 639 ? 10 : 20
                            }
                        },
                        legend: {
                            fontSize: window.innerWidth <= 639 ? '11px' : '12px'
                        }
                    });
                }, 250);
            });

            // === filter by year ===
            function applyYearFilter() {
                const years = Array.from(document.querySelectorAll('.year-checkbox:checked'))
                    .map(cb => String(cb.value));
                const orig = originalsMain['scoreLine'];
                const idxs = orig.map((r, i) => years.includes(String(r.year)) ? i : -1).filter(i => i >= 0);
                const newLabels = idxs.map(i => orig[i].year);
                const newScores = idxs.map(i => Number(orig[i].score) || 0);
                const newMax = idxs.map(i => Number(orig[i].max) || 0);
                chartsMain['scoreLine'].updateOptions({
                    xaxis: {
                        categories: newLabels
                    },
                    series: [{
                            name: "คะแนนที่ได้",
                            data: newScores
                        },
                        {
                            name: "คะแนนเต็ม",
                            data: newMax
                        }
                    ],
                    yaxis: {
                        min: 0,
                        max: newMax.length ? Math.max(...newMax) * 1.15 : 100,
                        title: {
                            text: "คะแนน"
                        }
                    }
                }, true, true);
            }
            document.querySelectorAll('.year-checkbox').forEach(cb => {
                cb.addEventListener('change', applyYearFilter);
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll('.btn-download').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const targetId = this.dataset.target;
                    const cardEl = document.getElementById(targetId);
                    if (!cardEl) return;
                    try {
                        const canvas = await html2canvas(cardEl, {
                            scale: 3,
                            backgroundColor: "#ffffff"
                        });
                        const dataUrl = canvas.toDataURL('image/png');

                        const link = document.createElement('a');
                        link.href = dataUrl;
                        link.download = targetId + '.png';
                        link.click();
                    } catch (err) {
                        console.error("html2canvas error:", err);
                    }
                });
            });
        });
    </script>
    <script>
        document.getElementById('custom-search').addEventListener('input', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('.enhanced-chart-card').forEach(card => {
                const title = card.querySelector('.chart-title')?.textContent.toLowerCase() || '';
                card.style.display = title.includes(q) ? '' : 'none';
            });
        });
        // ==== เก็บ instance ของ ApexCharts (ย้ายออกมาข้างนอก) ====
        window.chartInstances = window.chartInstances || {};
        window.chartOriginals = window.chartOriginals || {};
        // ==== init ApexCharts (ย้ายออกมาข้างนอก) ====
        window.initChartsFromInlineJSON = function() {
            document.querySelectorAll('div[id^="chart-"]').forEach(container => {
                const key = container.id.replace(/^chart-/, '');
                const dataEl = document.getElementById('data-' + key);
                if (!dataEl) return;
                let payload = {
                    years: [],
                    values: [],
                    max_values: []
                };
                try {
                    payload = JSON.parse(dataEl.textContent || '{}');
                } catch {}
                const years = (payload.years || []).map(y => String(y));
                const values = (payload.values || []).map(v => Number(v));
                const maxValues = (payload.max_values || []).map(v => Number(v));
                // 🔥 เก็บข้อมูลต้นฉบับ
                window.chartOriginals[container.id] = {
                    years,
                    values,
                    maxValues
                };

                // Get responsive height for individual charts
                function getIndividualChartHeight() {
                    const width = window.innerWidth;
                    if (width <= 639) return 180; // Mobile
                    if (width <= 767) return 200; // Small Tablet
                    if (width <= 1023) return 220; // Large Tablet
                    return 220; // Desktop and larger
                }

                const options = {
                    series: [{
                            name: "คะแนนที่ได้",
                            data: years.map((x, i) => ({
                                x,
                                y: values[i] ?? null
                            }))
                        },
                        {
                            name: "คะแนนเต็ม",
                            data: years.map((x, i) => ({
                                x,
                                y: maxValues[i] ?? null
                            }))
                        }
                    ],
                    chart: {
                        type: 'area',
                        height: getIndividualChartHeight(),
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: window.innerWidth <= 639 ? 4 : 6,
                            columnWidth: window.innerWidth <= 639 ? '50%' : '40%'
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        style: {
                            fontSize: window.innerWidth <= 639 ? '9px' : '10px'
                        }
                    },
                    xaxis: {
                        categories: years,
                        labels: {
                            style: {
                                fontSize: window.innerWidth <= 639 ? '10px' : '11px'
                            }
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'คะแนน',
                            style: {
                                fontSize: window.innerWidth <= 639 ? '11px' : '12px'
                            }
                        },
                        labels: {
                            style: {
                                fontSize: window.innerWidth <= 639 ? '10px' : '11px'
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                        fontSize: window.innerWidth <= 639 ? '10px' : '11px'
                    },
                    stroke: {
                        curve: 'smooth',
                        width: window.innerWidth <= 639 ? 2 : 3
                    },
                    markers: {
                        size: window.innerWidth <= 639 ? 3 : 4,
                        hover: {
                            size: window.innerWidth <= 639 ? 5 : 6
                        }
                    },
                    colors: ['#4f46e5', '#94a3b8']
                };

                const chart = new ApexCharts(container, options);
                chart.render();

                // 🔥 เก็บ instance ของ chart
                window.chartInstances[container.id] = chart;

                // Window resize handler for individual charts
                let resizeTimeoutIndividual;
                window.addEventListener('resize', function() {
                    clearTimeout(resizeTimeoutIndividual);
                    resizeTimeoutIndividual = setTimeout(function() {
                        const newHeight = getIndividualChartHeight();
                        if (window.chartInstances[container.id]) {
                            window.chartInstances[container.id].updateOptions({
                                chart: {
                                    height: newHeight
                                },
                                plotOptions: {
                                    bar: {
                                        borderRadius: window.innerWidth <= 639 ? 4 : 6,
                                        columnWidth: window.innerWidth <= 639 ? '50%' :
                                            '40%'
                                    }
                                },
                                dataLabels: {
                                    style: {
                                        fontSize: window.innerWidth <= 639 ? '9px' :
                                            '10px'
                                    }
                                },
                                xaxis: {
                                    labels: {
                                        style: {
                                            fontSize: window.innerWidth <= 639 ?
                                                '10px' : '11px'
                                        }
                                    }
                                },
                                yaxis: {
                                    title: {
                                        style: {
                                            fontSize: window.innerWidth <= 639 ?
                                                '11px' : '12px'
                                        }
                                    },
                                    labels: {
                                        style: {
                                            fontSize: window.innerWidth <= 639 ?
                                                '10px' : '11px'
                                        }
                                    }
                                },
                                legend: {
                                    fontSize: window.innerWidth <= 639 ? '10px' : '11px'
                                },
                                stroke: {
                                    width: window.innerWidth <= 639 ? 2 : 3
                                },
                                markers: {
                                    size: window.innerWidth <= 639 ? 3 : 4,
                                    hover: {
                                        size: window.innerWidth <= 639 ? 5 : 6
                                    }
                                }
                            });
                        }
                    }, 250);
                });
            });
        };
        // ==== ฟังก์ชัน Show More (ย้ายออกมาข้างนอก) ====
        window.bindShowMore = function() {
            document.querySelectorAll('.btn-show-more').forEach(btn => {
                btn.addEventListener('click', function() {
                    const sid = this.dataset.standardId;
                    const container = document.querySelector(
                        `.charts-of-standard-inds[data-standard-id="${sid}"]`);
                    if (!container) return;
                    const cards = container.querySelectorAll('.chart-card[data-index]');
                    const isExpanded = this.classList.contains('expanded');
                    if (!isExpanded) {
                        cards.forEach(c => c.style.display = '');
                        this.textContent = 'แสดงน้อยลง';
                        this.classList.add('expanded');
                    } else {
                        cards.forEach(c => {
                            const idx = parseInt(c.dataset.index, 10);
                            c.style.display = idx < 5 ? '' : 'none';
                        });
                        this.textContent = 'แสดงเพิ่มเติม';
                        this.classList.remove('expanded');
                    }
                });
            });
        };

        (function() {
            // หยุดการทำงานถ้า FilterComponent มีอยู่แล้ว
            if (typeof window.FilterComponent !== 'undefined') {
                console.log('FilterComponent detected, initializing with proper integration');
                // แต่ยังต้องสร้าง global functions สำหรับ FilterComponent
                window.applyResultFilters = function() {
                    const $year = document.getElementById('filter-year');
                    const $code = document.getElementById('filter-codes');
                    const $std = document.getElementById('filter-standard');
                    const $dim = document.getElementById('filter-dimension');
                    const $type = document.getElementById('filter-type');

                    if (!$year || !$code || !$std || !$dim || !$type) return;

                    const vYear = String($year.value || '');
                    const vCode = $code.value;
                    const vStd = $std.value;
                    const vDim = $dim.value;
                    const vType = $type.value;

                    document.querySelectorAll('.charts-of-standard-inds').forEach(container => {
                        const cards = container.querySelectorAll('.chart-card[data-index]');
                        let visibleCount = 0;

                        cards.forEach(card => {
                            let show = true;
                            if (vStd && card.dataset.standard !== vStd) show = false;
                            if (vDim && (card.dataset.dimension || '') !== vDim) show = false;
                            if (vType && (card.dataset.type || '') !== vType) show = false;
                            if (vCode && (card.dataset.code || '') !== vCode) show = false;
                            if (vYear) {
                                try {
                                    const raw = JSON.parse(card.getAttribute('data-years') || '[]');
                                    const years = (raw || []).map(y => String(y));
                                    if (!years.includes(vYear)) show = false;
                                } catch {}
                            }

                            if (show) {
                                // ✅ limit แค่ 5 ตัวแรก
                                if (visibleCount < 5) {
                                    card.style.display = '';
                                } else {
                                    card.style.display = 'none';
                                }
                                visibleCount++;
                            } else {
                                card.style.display = 'none';
                            }
                        });

                        // ✅ reset ปุ่ม show more
                        const btn = container.parentElement.querySelector('.btn-show-more');
                        if (btn) {
                            btn.classList.remove('expanded');
                            btn.textContent = 'แสดงเพิ่มเติม ▼';
                            btn.style.display = visibleCount > 5 ? '' : 'none';
                        }
                    });

                    // === update charts ตาม year filter ===
                    Object.entries(window.chartInstances || {}).forEach(([id, chart]) => {
                        const orig = window.chartOriginals[id];
                        if (!orig) return;

                        let newYears = orig.years.slice();
                        let newValues = orig.values.slice();
                        let newMax = orig.maxValues.slice();
                        if (vYear) {
                            const idxs = orig.years.map((y, i) => ({
                                    y,
                                    i
                                }))
                                .filter(o => String(o.y) === vYear)
                                .map(o => o.i);
                            newYears = idxs.map(i => orig.years[i]);
                            newValues = idxs.map(i => orig.values[i]);
                            newMax = idxs.map(i => orig.maxValues[i]);
                        }

                        chart.updateOptions({
                            xaxis: {
                                categories: newYears
                            },
                            series: [{
                                    name: 'คะแนนที่ได้',
                                    data: newYears.map((x, i) => ({
                                        x,
                                        y: newValues[i]
                                    }))
                                },
                                {
                                    name: 'คะแนนเต็ม',
                                    data: newYears.map((x, i) => ({
                                        x,
                                        y: newMax[i]
                                    }))
                                }
                            ]
                        }, false, true);
                    });
                };

                window.resetResultFilters = function() {
                    const $year = document.getElementById('filter-year');
                    const $code = document.getElementById('filter-codes');
                    const $std = document.getElementById('filter-standard');
                    const $dim = document.getElementById('filter-dimension');
                    const $type = document.getElementById('filter-type');

                    [$year, $code, $std, $dim, $type].forEach(sel => {
                        if (sel) sel.selectedIndex = 0;
                    });

                    // รีเซ็ตการ์ดของทุก standard
                    document.querySelectorAll('.charts-of-standard-inds').forEach(container => {
                        const cards = container.querySelectorAll('.chart-card[data-index]');
                        cards.forEach(c => {
                            const idx = parseInt(c.dataset.index, 10);
                            c.style.display = idx < 5 ? '' : 'none';
                        });
                    });

                    // รีเซ็ต chart กลับเป็นข้อมูลต้นฉบับ
                    Object.entries(window.chartInstances || {}).forEach(([id, chart]) => {
                        const orig = window.chartOriginals[id];
                        if (!orig) return;
                        chart.updateOptions({
                            xaxis: {
                                categories: orig.years
                            },
                            series: [{
                                    name: 'คะแนนที่ได้',
                                    data: orig.years.map((x, i) => ({
                                        x,
                                        y: orig.values[i]
                                    }))
                                },
                                {
                                    name: 'คะแนนเต็ม',
                                    data: orig.years.map((x, i) => ({
                                        x,
                                        y: orig.maxValues[i]
                                    }))
                                }
                            ]
                        }, false, true);
                    });

                    // รีเซ็ตปุ่ม show more ทุกตัว
                    document.querySelectorAll('.btn-show-more').forEach(btn => {
                        btn.classList.remove('expanded');
                        btn.textContent = 'แสดงเพิ่มเติม ▼';
                    });

                    if (typeof window.applyResultFilters === 'function') {
                        window.applyResultFilters();
                    }
                };

                return; // ออกจาก function เพื่อไม่ให้ทำงานต่อ
            }
            const FILTERS = @json($filters, JSON_UNESCAPED_UNICODE);
            const $year = document.getElementById('filter-year');
            const $code = document.getElementById('filter-codes');
            const $std = document.getElementById('filter-standard');
            const $dim = document.getElementById('filter-dimension');
            const $type = document.getElementById('filter-type');
            // ==== Helper สำหรับ select ====
            function fillSelect(sel, items, mapper) {
                [...sel.querySelectorAll('option')].forEach(o => {
                    if (o.value) o.remove();
                });
                (items || []).forEach(it => {
                    const opt = document.createElement('option');
                    if (mapper) {
                        const {
                            value,
                            label
                        } = mapper(it);
                        opt.value = value;
                        opt.textContent = label;
                    } else {
                        opt.value = it;
                        opt.textContent = it;
                    }
                    sel.appendChild(opt);
                });
            }

            function sortCodes(codes) {
                return (codes || []).slice().sort((a, b) => {
                    const ma = String(a).match(/^([A-Za-z]+)[- ]?(\d+)$/i);
                    const mb = String(b).match(/^([A-Za-z]+)[- ]?(\d+)$/i);
                    const prefixA = ma ? ma[1].toUpperCase() : String(a);
                    const prefixB = mb ? mb[1].toUpperCase() : String(b);
                    const numA = ma ? parseInt(ma[2], 10) : 0;
                    const numB = mb ? parseInt(mb[2], 10) : 0;
                    if (prefixA === prefixB) return numA - numB;
                    return prefixA.localeCompare(prefixB, 'th');
                });
            }
            fillSelect($year, (FILTERS.years || []).slice().sort());
            fillSelect($code, sortCodes(FILTERS.codes || []));
            fillSelect($std, (FILTERS.standards || []).slice().sort((a, b) => a.name.localeCompare(b.name, 'th')), it =>
                ({
                    value: String(it.id),
                    label: it.name
                }));
            fillSelect($dim, (FILTERS.dimensions || []).slice().sort());
            fillSelect($type, (FILTERS.types || []).slice().sort());
            // ==== ฟิลเตอร์ ====
            async function applyFilters() {
                const vYear = String($year.value || '');
                const vCode = $code.value,
                    vStd = $std.value,
                    vDim = $dim.value,
                    vType = $type.value;

                document.querySelectorAll('.charts-of-standard-inds').forEach(container => {
                    const cards = container.querySelectorAll('.chart-card[data-index]');
                    let visibleCount = 0;

                    cards.forEach(card => {
                        let show = true;
                        if (vStd && card.dataset.standard !== vStd) show = false;
                        if (vDim && (card.dataset.dimension || '') !== vDim) show = false;
                        if (vType && (card.dataset.type || '') !== vType) show = false;
                        if (vCode && (card.dataset.code || '') !== vCode) show = false;
                        if (vYear) {
                            try {
                                const raw = JSON.parse(card.getAttribute('data-years') || '[]');
                                const years = (raw || []).map(y => String(y));
                                if (!years.includes(vYear)) show = false;
                            } catch {}
                        }

                        if (show) {
                            // ✅ limit แค่ 5 ตัวแรก
                            if (visibleCount < 5) {
                                card.style.display = '';
                            } else {
                                card.style.display = 'none';
                            }
                            visibleCount++;
                        } else {
                            card.style.display = 'none';
                        }
                    });

                    // ✅ reset ปุ่ม show more
                    const btn = container.parentElement.querySelector('.btn-show-more');
                    if (btn) {
                        btn.classList.remove('expanded');
                        btn.textContent = 'แสดงเพิ่มเติม ▼';
                        btn.style.display = visibleCount > 5 ? '' : 'none';
                    }
                });

                // === update charts ตาม year filter ===
                Object.entries(window.chartInstances).forEach(([id, chart]) => {
                    const orig = window.chartOriginals[id];
                    if (!orig) return;

                    let newYears = orig.years.slice();
                    let newValues = orig.values.slice();
                    let newMax = orig.maxValues.slice();
                    if (vYear) {
                        const idxs = orig.years.map((y, i) => ({
                                y,
                                i
                            }))
                            .filter(o => o.y === vYear)
                            .map(o => o.i);
                        newYears = idxs.map(i => orig.years[i]);
                        newValues = idxs.map(i => orig.values[i]);
                        newMax = idxs.map(i => orig.maxValues[i]);
                    }

                    chart.updateOptions({
                        xaxis: {
                            categories: newYears
                        },
                        series: [{
                                name: 'คะแนนที่ได้',
                                data: newYears.map((x, i) => ({
                                    x,
                                    y: newValues[i]
                                }))
                            },
                            {
                                name: 'คะแนนเต็ม',
                                data: newYears.map((x, i) => ({
                                    x,
                                    y: newMax[i]
                                }))
                            }
                        ]
                    }, false, true);
                });
            }

            function resetFilters() {
                [$year, $code, $std, $dim, $type].forEach(sel => {
                    if (sel) sel.selectedIndex = 0;
                });

                // รีเซ็ตการ์ดของทุก standard
                document.querySelectorAll('.charts-of-standard-inds').forEach(container => {
                    const cards = container.querySelectorAll('.chart-card[data-index]');
                    cards.forEach(c => {
                        const idx = parseInt(c.dataset.index, 10);
                        c.style.display = idx < 5 ? '' : 'none'; // ✅ โชว์เฉพาะ 5 การ์ดแรก
                    });
                });

                // รีเซ็ต chart กลับเป็นข้อมูลต้นฉบับ
                Object.entries(window.chartInstances).forEach(([id, chart]) => {
                    const orig = window.chartOriginals[id];
                    chart.updateOptions({
                        xaxis: {
                            categories: orig.years
                        },
                        series: [{
                                name: 'คะแนนที่ได้',
                                data: orig.years.map((x, i) => ({
                                    x,
                                    y: orig.values[i]
                                }))
                            },
                            {
                                name: 'คะแนนเต็ม',
                                data: orig.years.map((x, i) => ({
                                    x,
                                    y: orig.maxValues[i]
                                }))
                            }
                        ]
                    }, false, true);
                });

                // รีเซ็ตปุ่ม show more ทุกตัว
                document.querySelectorAll('.btn-show-more').forEach(btn => {
                    btn.classList.remove('expanded');
                    btn.textContent = 'แสดงเพิ่มเติม ▼';
                });
            }

            // สร้าง global functions สำหรับ FilterComponent
            window.applyResultFilters = applyFilters;
            window.resetResultFilters = function() {
                resetFilters();
                applyFilters();
            };

            // Fallback event listeners
            document.getElementById('apply-filters')?.addEventListener('click', applyFilters);
            document.getElementById('reset-filters')?.addEventListener('click', () => {
                resetFilters();
                applyFilters();
            });

            // ==== init ====
            if (typeof window.FilterComponent === 'undefined') {
                // เฉพาะเมื่อไม่มี FilterComponent เท่านั้น
                window.initChartsFromInlineJSON();
                window.bindShowMore();
            }
        })();

        // ==== เริ่มต้น FilterComponent (ย้ายออกมาข้างนอก) ====
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof window.FilterComponent !== 'undefined') {
                window.FilterComponent.init({
                    filterId: 'filter-panel',
                    formId: 'result-filter-form',
                    onApply: function() {
                        if (typeof window.applyResultFilters === 'function') {
                            window.applyResultFilters();
                        }
                    },
                    onReset: function() {
                        if (typeof window.resetResultFilters === 'function') {
                            window.resetResultFilters();
                        }
                    }
                });

                // Initialize charts เมื่อใช้ FilterComponent
                setTimeout(function() {
                    if (typeof window.initChartsFromInlineJSON === 'function') {
                        window.initChartsFromInlineJSON();
                    }
                    if (typeof window.bindShowMore === 'function') {
                        window.bindShowMore();
                    }
                }, 100);
            } else {
                // Fallback สำหรับเมื่อไม่มี FilterComponent
                if (typeof window.initChartsFromInlineJSON === 'function') {
                    window.initChartsFromInlineJSON();
                }
                if (typeof window.bindShowMore === 'function') {
                    window.bindShowMore();
                }
            }
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Initialize FilterComponent toggle if available
            if (typeof window.FilterComponent !== 'undefined') {
                // FilterComponent จะจัดการ toggle เอง
                const toggle = document.getElementById('toggle-filter');
                if (toggle) {
                    toggle.addEventListener('change', function() {
                        const panel = document.getElementById('filter-panel');
                        if (panel) {
                            panel.style.display = this.checked ? 'block' : 'none';
                        }
                    });
                }
            } else {
                // Fallback for legacy toggle
                const toggle = document.getElementById('toggle-filter');
                const panel = document.getElementById('filter-panel');
                if (toggle && panel) {
                    toggle.addEventListener('change', function() {
                        panel.style.display = this.checked ? '' : 'none';
                    });
                }
            }
        });
    </script>
@endpush

@push('styles')
    <style>
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

        .charts-of-dimensions {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }


        .charts-of-standards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }

        .chart-card {
            background: #fff;
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .06);
        }

        .standard-card {}

        .dim-card {}

        .resul-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px;
        }

        .resul-header {
            margin-bottom: 32px;
        }

        .resul-header h1 {
            font-size: 32px;
            font-weight: 700;
            color: var(--color-gray-900);
            margin: 0 0 8px 0;
        }

        .subtitle {
            color: var(--color-gray-600);
            font-size: 16px;
            margin: 0;
        }

        #scoreLineChart {
            min-height: 365px;
            width: 100%;
            max-width: 100%;
        }

        /* Charts Grid */
        .charts-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
            justify-content: flex-start;
        }

        .charts-of-standard-inds {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }

        .card-container {
            flex: 0 0 320px;
            background: var(--color-white);
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .06);
        }

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

        .chart-content {
            position: relative;
            height: 420px;
        }

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
            background-color: #ccc;
            transition: .3s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #2196F3;
        }

        input:checked+.slider:before {
            transform: translateX(22px);
        }

        .divider-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            /* margin: 16px 0; */
        }

        .divider-line {
            flex: 1;
            height: 1px;
            background-color: #3b82f6;
            margin: 0 8px;
        }

        .btn-show-more {
            background: none;
            border: none;
            color: #3b82f6;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            padding: 0 4px;
            transition: color 0.2s;
        }

        .btn-show-more:hover {
            color: #2563eb;
        }
    </style>
    <style>
        /* Legacy mobile styles for backward compatibility */
        @media (max-width: 480px) {}

        /* Mobile (max-width: 639px) */
        @media (max-width: 639px) {}

        /* Small Tablet (640px - 767px) */
        @media (min-width: 640px) and (max-width: 767px) {}

        /* Large Tablet (768px - 1023px) */
        @media (min-width: 768px) and (max-width: 1023px) {}

        /* Small Desktop (1024px - 1279px) */
        @media (min-width: 1024px) and (max-width: 1279px) {}

        /* Large Desktop (1280px - 1535px) */
        @media (min-width: 1280px) and (max-width: 1535px) {}

        /* Extra Large Desktop (min-width: 1536px) */
        @media (min-width: 1536px) {}
    </style>
@endpush
