@extends('layouts.app')
@section('title', 'รายการรายงาน SAR')
@section('header', 'รายการรายงาน SAR')
@section('subheader', 'ระบบบริหารจัดการข้อมูลการรับรองสถาบันจากสภาการพยาบาล')

@section('content')
    <div class="card-header-table">
        <div class="search-button-container">
            <div class="search-bar shadow-sm">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="search" name="custom_search" id="custom-search" placeholder="ค้นหารายการรายงาน SAR..."
                    aria-label="ค้นหารายการรายงาน SAR" aria-controls="myTable" autocomplete="off"
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40" />
            </div>
            <div class="sort-filter-container">
                <!-- Sort Button with Dropdown -->
                <div class="dropdown-inds w-full" id="sort-dropdown-container">
                    <button id="sort-button" class="btns">
                        <span>เรียงลำดับ</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                        </svg>
                    </button>
                    <div id="sort-dropdown"
                        class="hidden absolute left-0 mt-2 w-56 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                        <div class="py-1" role="menu" aria-orientation="vertical">
                            <button
                                class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                data-column="0" data-order="asc" role="menuitem">ปี (น้อยไปมาก)</button>
                            <button
                                class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                data-column="0" data-order="desc" role="menuitem">ปี (มากไปน้อย)</button>
                            <button
                                class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                data-column="1" data-order="asc" role="menuitem">ชื่อรายงาน (A-Z)</button>
                            <button
                                class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                data-column="1" data-order="desc" role="menuitem">ชื่อรายงาน (Z-A)</button>
                            <button
                                class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                data-column="2" data-order="asc" role="menuitem">วันที่สร้าง (เก่า→ใหม่)</button>
                            <button
                                class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                data-column="2" data-order="desc" role="menuitem">วันที่สร้าง (ใหม่→เก่า)</button>
                            <button id="clear-sort" type="button"
                                class=" text-left block w-full px-4 py-2 text-sm text-gray-600 hover:bg-gray-100">ล้างตัวเรียงลำดับ</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="action-buttons-container">
            <x-year-export-modal :years="$years" context="year-export"
                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md inline-flex items-center space-x-2 shadow">
                <i data-lucide="upload" class="w-4 h-4"></i>
                <span>EXPORT</span>
            </x-year-export-modal>
        </div>
    </div>

    <!-- ตารางรายการรายงาน SAR -->
    <div class="border border-gray-200 rounded-lg shadow-sm overflow-x-hidden">

        <table id="myTable" class="w-full min-w-full">
            <thead>
                <tr>
                    <th class="w-fit text-xs sm:text-sm font-medium text-gray-900 cursor-pointer select-none"
                        title="ปีของรายงาน">
                        <div class="flex items-center justify-center min-w-6">
                            ปี
                        </div>
                    </th>
                    <th class="w-full text-xs sm:text-sm font-medium text-gray-900 text-left cursor-pointer select-none"
                        title="ชื่อเอกสารรายงาน SAR">
                        <div class="flex items-center justify-center min-w-40 sm:min-w-56">
                            ชื่อเอกสาร
                        </div>
                    </th>
                    <th class="w-fit text-xs sm:text-sm font-medium text-gray-900 cursor-pointer select-none hidden sm:table-cell"
                        title="วันที่สร้างเอกสาร">
                        <div class="flex items-center justify-center min-w-32">
                            วันที่สร้างเอกสาร
                        </div>
                    </th>
                    <th class="w-fit text-xs sm:text-sm font-medium text-gray-900 cursor-pointer select-none"
                        title="จัดการข้อมูล">
                        <div class="flex items-center justify-center min-w-20">
                            จัดการ
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white">
                @forelse($reports as $r)
                    <tr>
                        <td class="max-w-6 text-xs sm:text-sm text-gray-700 text-center align-top">{{ $r->year }}</td>
                        <td class="max-w-full text-pretty text-xs sm:text-sm text-gray-700 align-top">
                            <div class="text-pretty" title="{{ $r->title }}">
                                {{ $r->title }}
                            </div>
                        </td>
                        <td class="max-w-32 text-xs sm:text-sm text-gray-700 text-center align-top hidden sm:table-cell">
                            {{ $r->created_at ? $r->created_at->format('d/m/Y') : '-' }}
                        </td>
                        <td class="text-xs sm:text-sm text-gray-700 align-top text-center" data-rowlink-ignore>
                            <div x-data="{
                                open: false,
                                pos: { top: 0, left: 0 },
                                menuWidth: 180,
                                position() {
                                    const r = this.$refs.trigger.getBoundingClientRect();
                                    let left = r.right - this.menuWidth;
                                    let top = r.bottom + 8;
                                    const spaceBelow = window.innerHeight - r.bottom;
                                    const spaceAbove = r.top;
                                    const mh = this.$refs.menu ? this.$refs.menu.offsetHeight : 160;
                                    if (spaceBelow < mh + 8 && spaceAbove > spaceBelow) {
                                        top = r.top - mh - 8;
                                    }
                                    this.pos = { top, left };
                                }
                            }" class="relative inline-block text-left"
                                @scroll.window="open && position()" @resize.window="open && position()">

                                <!-- ปุ่มหลัก (3-dot menu) -->
                                <button type="button" x-ref="trigger" @click="position(); open = !open"
                                    @keydown.escape.window="open=false"
                                    class="inline-flex items-center p-2 bg-gray-100 border border-gray-300 rounded-md shadow-sm 
                   hover:bg-gray-200 focus:outline-none transition cursor-pointer">
                                    <i data-lucide="more-vertical" class="w-5 h-5 text-gray-700"></i>
                                </button>

                                <!-- เมนูหลัก -->
                                <template x-teleport="body">
                                    <div x-show="open" x-ref="menu" @click.away="open = false"
                                        @keydown.escape.window="open=false" x-transition
                                        :style="`position: fixed; top: ${pos.top}px; left: ${pos.left}px; width: ${menuWidth}px;`"
                                        class="mt-2 bg-white rounded-md shadow-lg border border-gray-300 z-[9999]">

                                        <div class="py-1 text-gray-800 text-sm font-medium">
                                            <!-- ปุ่มแก้ไข -->
                                            <a href="{{ route('sar_reports.edit', $r->id) }}"
                                                class="flex items-center px-4 py-2 hover:bg-gray-100 transition gap-2">
                                                <i class="fa fa-edit"></i>
                                                แก้ไขเอกสาร
                                            </a>

                                            <!-- เส้นคั่น -->
                                            <div class="border-t border-gray-200 my-1"></div>

                                            <!-- ปุ่มลบ -->
                                            <form id="del-sar-{{ $r->id }}"
                                                action="{{ route('sar_reports.destroy', $r->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <x-modal title="ยืนยันการลบข้อมูล" size="sm">
                                                    <x-slot:trigger>
                                                        <button type="button"
                                                            class="flex items-center w-full px-4 py-2 text-red-700 hover:bg-red-50 transition cursor-pointer gap-2">
                                                            <i class="fa fa-trash"></i>
                                                            ลบเอกสาร
                                                        </button>
                                                    </x-slot:trigger>

                                                    <div class=" text-center text-gray-700 text-sm">
                                                        <p>คุณต้องการลบเอกสาร </p>
                                                        <p>"<span
                                                                class="font-semibold text-red-600 text-pretty">{{ $r->title }}</span>"
                                                        </p>
                                                        <p>หรือไม่?</p>
                                                    </div>

                                                    <x-slot:footer>
                                                        <div class="flex justify-between gap-5">
                                                            <button type="button" class="btn btn-outline"
                                                                @click="$dispatch('modal:close')"><i
                                                                    class="fa fa-undo"></i>ยกเลิก</button>
                                                            <button type="button" class="btn btn-danger"
                                                                onclick="document.getElementById('del-sar-{{ $r->id }}').submit()"><i
                                                                    class="fa fa-trash"></i>ยืนยันการลบ</button>
                                                        </div>
                                                    </x-slot:footer>
                                                </x-modal>
                                            </form>

                                            <!-- เส้นคั่น -->
                                            <div class="border-t border-gray-200 my-1"></div>

                                            <!-- ปุ่ม Export (Dropdown ซ้อน) -->
                                            <div x-data="{ open: false }" class="relative">
                                                <button type="button" @click="open = !open"
                                                    class="flex items-center w-full px-4 py-2 hover:bg-gray-100 transition cursor-pointer gap-2">
                                                    <i class="fa fa-download"></i>
                                                    ส่งออกเอกสาร
                                                </button>

                                                <!-- Submenu Export -->
                                                <div x-show="open" @click.away="open = false" x-cloak
                                                    class="absolute right-0 mt-2 w-48 bg-white border border-gray-300 rounded-md shadow-lg z-50">

                                                    <!-- DOCX -->
                                                    <a href="{{ route('sar_reports.export.docx', $r->id) }}"
                                                        class="flex items-center px-4 py-2 hover:bg-gray-100 transition">
                                                        <i data-lucide="file-text" class="w-4 h-4 mr-2 text-blue-700"></i>
                                                        Word (DOCX)
                                                    </a>

                                                    <!-- Excel -->
                                                    <a href="{{ route('sar_reports.export.xlsx', $r->id) }}"
                                                        class="flex items-center px-4 py-2 hover:bg-gray-100 transition">
                                                        <i data-lucide="file-spreadsheet"
                                                            class="w-4 h-4 mr-2 text-green-700"></i> Excel
                                                    </a>

                                                    <!-- PDF Preview -->
                                                    <a href="{{ route('sar_reports.export.pdf', $r->id) }}"
                                                        target="_blank"
                                                        class="flex items-center px-4 py-2 hover:bg-gray-100 transition">
                                                        <i data-lucide="file" class="w-4 h-4 mr-2 text-red-700"></i>
                                                        PDF Preview
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </td>
                    </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>
    </div>
@endsection

@push('styles')
    <style>
        /* ================ Base / Datatable ======================= */
        #myTable,
        table.dataTable {
            width: 100% !important;
        }

        table.dataTable thead th,
        table.dataTable tbody td {
            padding: 10px 4px;
        }

        table.dataTable thead th {
            position: relative;
            background-color: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 600;
        }

        table.dataTable thead th:hover {
            background-color: #f3f4f6;
        }

        .dataTables_wrapper .dataTables_info {
            color: #4b5563;
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #a0aec0;
            border-radius: .5rem;
            padding: .5rem;
            background-color: #fff;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.5rem 1rem;
            margin-left: 0.25rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            background-color: #fff;
            color: #4b5563 !important;
            transition: background-color .2s ease;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background-color: #f3f4f6 !important;
            border-color: #e2e8f0;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background-color: #aaaaaa !important;
            border-color: #ffffff;
            color: #fff !important;
        }

        table.dataTable thead .sorting:after,
        table.dataTable thead .sorting_asc:after,
        table.dataTable thead .sorting_desc:after {
            position: absolute;
            right: 8px;
            display: none;
        }

        /* Row hover/focus */
        #myTable tbody tr {
            transition: background-color .15s ease, transform .05s ease;
            /* cursor: pointer; */
        }

        table.dataTable tbody tr {
            background-color: inherit !important;
        }

        #myTable tbody tr:hover {
            background-color: #dbeafe !important;
        }

        #myTable tbody tr:hover td {
            background-color: inherit !important;
        }

        /* ================ Page Layout / Header Controls ================ */
        .container {
            max-width: 1400px !important;
            min-height: 750px !important;
        }

        .card-header-table {
            display: flex;
            justify-content: space-between;
            gap: 26px;
            padding: 0 16px 16px;
        }

        .search-button-container {
            display: flex;
            gap: 8px;
            width: 100%;
        }

        .search-bar {
            position: relative;
            display: flex;
            align-items: center;
            width: 60%;
            min-width: 250px;
            max-width: 400px;
            background: #fff;
            border-radius: 8px;
            height: fit-content;
        }

        .search-bar input {
            font-size: 14px;
        }

        .sort-filter-container {
            display: flex;
            gap: 8px;
            width: fit-content;
        }

        .action-buttons-container {
            display: flex;
            gap: 12px;
        }

        .action-buttons-container button {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 14px;
            color: #fff;
            border-radius: 8px;
            padding: 8px 16px;
            white-space: nowrap;
            transition: background-color .2s ease;
        }

        /* ================ Reusable Buttons ================ */
        .btns {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #374151;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
            transition: all .2s ease;
        }

        .btns:hover {
            background: #f9fafb;
            border-color: #d1d5db;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, .1);
        }

        .btns:active {
            transform: translateY(0);
            box-shadow: 0 1px 2px rgba(0, 0, 0, .05);
        }

        .btns svg {
            width: 16px;
            height: 16px;
            transition: transform .2s ease;
        }

        .btns:hover svg {
            transform: scale(1.05);
        }

        /* ================ Dropdown: Sort & Filter Shells ================ */
        .dropdown-inds {
            position: relative;
            display: inline-block;
            width: fit-content;
            height: fit-content;
        }

        /* ================ Responsive ================ */
        /* < 640px */
        @media (max-width: 639px) {
            .container {
                padding: 8px;
            }

            .dataTables_wrapper,
            #myTable_wrapper {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            table.dataTable thead th,
            table.dataTable tbody td {
                padding: 6px 3px;
                vertical-align: top;
            }

            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter,
            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                text-align: left;
                float: none;
                width: 100%;
                margin: 5px 0;
            }

            .dataTables_wrapper .dataTables_length select {
                padding: .25rem;
            }

            .dataTables_wrapper .dataTables_paginate .paginate_button {
                padding: .25rem .5rem;
                margin-left: .125rem;
            }

            .card-header-table {
                flex-direction: column-reverse;
                gap: 16px;
                padding: 0 6px 12px;
            }

            .search-button-container {
                flex-direction: column;
            }

            .search-bar {
                width: 100%;
                max-width: none;
            }

            .search-bar input,
            .action-buttons-container button,
            .btns {
                font-size: 12px;
            }

            .sort-option {
                font-size: 13px;
            }

            .sort-filter-container {
                width: 100%;
                justify-content: space-between;
            }

            .dropdown-inds,
            .dropdown-inds .btns {
                width: 100%;
            }
        }

        /* 640px–767px */
        @media (min-width: 640px) and (max-width: 767px) {
            .container {
                padding: 12px;
            }

            table.dataTable thead th,
            table.dataTable tbody td {
                padding: 8px 4px;
            }

            .card-header-table {
                flex-direction: column-reverse;
                gap: 16px;
                padding: 0 6px 12px;
            }

            .search-bar {
                width: 100%;
            }

            .search-bar input,
            .action-buttons-container button,
            .btns {
                font-size: 12px;
            }
        }

        /* 768px–1023px */
        @media (min-width: 768px) and (max-width: 1023px) {
            .container {
                max-width: 900px;
                padding: 16px;
            }

            table.dataTable thead th,
            table.dataTable tbody td {
                padding: 9px 5px;
            }

            .search-button-container {
                width: 90%;
            }

            .card-header-table {
                gap: 13px;
                padding: 0 6px 16px;
            }

            .action-buttons-container {
                gap: 8px;
            }

            .action-buttons-container button span {
                display: none;
            }

            .search-bar input,
            .action-buttons-container button,
            .btns {
                font-size: 12px;
            }

            .action-buttons-container button {
                min-width: 36px;
            }
        }

        /* 1024px–1279px */
        @media (min-width: 1024px) and (max-width: 1279px) {
            .container {
                max-width: 1100px;
                padding: 20px;
            }

            .search-bar input,
            .action-buttons-container button,
            .btns {
                font-size: 13px;
            }
        }

        /* 1280px–1535px (Large Desktop) */
        @media (min-width: 1280px) and (max-width: 1535px) {
            .container {
                max-width: 1400px !important;
            }

            .card-header-table {
                padding: 0 20px 20px;
            }

            .search-bar {
                width: 60%;
                max-width: 450px;
            }

            .action-buttons-container button {
                font-size: 15px;
                padding: 9px 18px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            let table = new DataTable('#myTable', {
                searching: true,
                responsive: true,
                autoWidth: false,
                language: {
                    paginate: {
                        previous: 'ก่อนหน้า',
                        next: 'ถัดไป'
                    },
                    info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                    lengthMenu: "แสดง _MENU_ รายการต่อหน้า",
                    emptyTable: "ไม่พบข้อมูล",
                    zeroRecords: "ไม่พบข้อมูลที่ตรงกับการค้นหา"
                },
                dom: '<"flex flex-col w-full md:flex-row justify-between items-center p-3"<"flex"l>>' +
                    't' +
                    '<"flex flex-col md:flex-row justify-between items-center p-3"<"flex-1"i><"flex"p>>',
            });

            const defaultOrder = JSON.parse(JSON.stringify(table.order()));
            const sortButtonDefault = $('#sort-button span').text();

            // Adjust columns to fill available width
            setTimeout(() => {
                table.columns.adjust().draw(false);
            }, 0);
            $(window).on('resize', function() {
                table.columns.adjust();
            });

            // Connect custom search box to DataTable
            $('#custom-search').on('keyup input', function() {
                table.search(this.value).draw();
            });

            // Clear search when input is empty
            $('#custom-search').on('search', function() {
                if (this.value === '') {
                    table.search('').draw();
                }
            });

            // Sorting dropdown functionality
            $('#sort-button').on('click', function(e) {
                e.stopPropagation();
                $('#sort-dropdown').toggleClass('hidden');
            });

            // Close dropdowns when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#sort-dropdown-container').length) {
                    $('#sort-dropdown').addClass('hidden');
                }
            });

            // Handle sort options
            $('.sort-option').on('click', function() {
                const columnData = $(this).data('column');
                const order = $(this).data('order');

                const columnIndex = Number(columnData);
                if (Number.isNaN(columnIndex) || !order) {
                    return;
                }

                table.order([columnIndex, order]).draw();

                const labelText = $(this).text().trim();
                const truncated = labelText.length > 24 ? `${labelText.slice(0, 24)}...` : labelText;
                $('#sort-button span').text(`เรียงลำดับ: ${truncated}`);

                $('#sort-dropdown').addClass('hidden');
            });

            // Clear sort button
            $('#clear-sort').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                table.order(defaultOrder).draw();
                $('#sort-button span').text(sortButtonDefault);
                $('#sort-dropdown').addClass('hidden');
            });
        });
    </script>
@endpush
