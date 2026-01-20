@extends('layouts.app')
@section('title', 'จัดการข้อมูลสังกัดงาน')

@section('header', 'จัดการข้อมูลสังกัดงาน')
@section('subheader', 'ระบบบริหารจัดการข้อมูลการรับรองสถาบันจากสภาการพยาบาล')

@section('content')
    @php
        $affiliations = $affiliations ?? ($workGroups ?? []);
    @endphp
    <div class="department-card">
        <!-- ฟอร์มเพิ่มสังกัดงาน -->
        <x-card>
            <div class="card-title">เพิ่มสังกัดงาน</div>
            <form action="{{ Route::has('affiliations.store') ? route('affiliations.store') : url('/affiliations/store') }}"
                method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">ชื่อสังกัดงาน <span class="required">*</span></label>
                    <input type="text" name="name" class="form-input" placeholder="กรอกชื่อสังกัดงาน" required>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> บันทึก
                </button>
            </form>
        </x-card>

        <x-card class="space-y-4">
            <div class="card-title">รายชื่อสังกัดงานที่มี</div>

            <div class="search-button-container">
                <!-- Search -->
                <div class="search-bar shadow-sm">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" id="custom-search"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40"
                        placeholder="ค้นหาสังกัดงาน">
                </div>

                <!-- Sort -->
                <div class="dropdown-inds w-full" id="sort-dropdown-container">
                    <button id="sort-button" class="btns">
                        <span>เรียงลำดับ</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                        </svg>
                    </button>
                    <div id="sort-dropdown" class="dropdown-menus hidden" role="menu" aria-orientation="vertical">
                        <button class="dropdown-item sort-option" data-column="0" data-order="asc" role="menuitem">ลำดับ
                            (น้อยไปมาก)</button>
                        <button class="dropdown-item sort-option" data-column="0" data-order="desc" role="menuitem">ลำดับ
                            (มากไปน้อย)</button>
                        <button class="dropdown-item sort-option" data-column="1" data-order="asc"
                            role="menuitem">ชื่อสังกัดงาน
                            (A-Z)</button>
                        <button class="dropdown-item sort-option" data-column="1" data-order="desc"
                            role="menuitem">ชื่อสังกัดงาน (Z-A)</button>
                        <div class="dropdown-divider"></div>
                        <button id="clear-sort" type="button" class="dropdown-item"
                            style="color:#4b5563;">ล้างการเรียงลำดับ</button>
                    </div>
                </div>
            </div>
            <table class="table" id="table1">
                <thead>
                    <tr>
                        <th class="text-xs !text-center sm:text-sm font-medium text-gray-900 cursor-pointer">ลำดับ</th>
                        <th class="text-xs !text-center sm:text-sm font-medium text-gray-900 cursor-pointer">ชื่อสังกัดงาน
                        </th>
                        <th class="text-xs !text-center sm:text-sm font-medium text-gray-900 cursor-pointer w-5"><span class="w-fit">จัดการ</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($affiliations as $index => $item)
                        @php
                            $itemId = data_get($item, 'id', $index + 1);
                            $itemName = data_get($item, 'name', $item);
                        @endphp
                        <tr>
                            <td class="text-xs text-center sm:text-sm text-gray-700 align-top">{{ $index + 1 }}</td>
                            <td class="text-xs sm:text-sm text-gray-700 align-top">{{ $itemName }}</td>
                            <td class="text-xs text-center sm:text-sm text-gray-700 align-top">
                                <div class="categories-actions">
                                    <button type="button" class="btn btn-outline"
                                        data-action="edit-affiliation" data-id="{{ $itemId }}" data-name="{{ $itemName }}">
                                        <i class="fa fa-edit"></i> แก้ไข
                                    </button>

                                    <button type="button" class="btn btn-outline !text-red-500 !border-red-500"
                                        data-action="delete-affiliation" data-id="{{ $itemId }}" data-name="{{ $itemName }}">
                                        <i class="fa fa-trash"></i> ลบ
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-card>
    </div>

    <!-- Edit Modal -->
    <x-modal title="แก้ไขชื่อสังกัดงาน" size="md" context="editModal" :closeOnBg="false">
        <div class="mb-4 text-sm text-gray-600">
            แก้ไขชื่อสังกัดงานที่ต้องการแล้วกดบันทึกเพื่อบันทึกผลที่ต้องการ
            <p class="mt-2">ชื่อสังกัดงานเดิม : <span id="currentDepartmentName" class="font-semibold text-pretty"></span>
            </p>
        </div>

        <form id="editForm" method="POST"
            action="{{ session('edit_affiliation_id') ? url('/affiliations/' . session('edit_affiliation_id')) : '' }}">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    ชื่อสังกัดงาน <span class="text-red-500">*</span>
                </label>
                <input type="text" id="editName" name="name"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required value="{{ old('name') }}">
            </div>
            @error('name')
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ $message }}</div>
            @enderror
            <div class="flex gap-2 justify-between">
                <button type="button" class="btn btn-outline" @click="$dispatch('modal:close')">
                    <i class="fa fa-undo"></i>ยกเลิก
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i>บันทึก
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Delete Modal -->
    <x-modal title="ลบชื่อสังกัดงาน" size="md" context="deleteModal" :closeOnBg="false">
        <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-sm text-yellow-800">
                <strong>คำเตือน :</strong> การลบชื่อสังกัดงานที่ถูกนำมาใช้แล้วจะไม่สามารถลบได้
            </p>
        </div>

        <div class="mb-6 text-center text-gray-700 text-sm">
            คุณต้องการลบข้อมูลสังกัดงาน "<span id="deleteName" class="font-semibold text-red-600 text-pretty"></span>"
            หรือไม่?
        </div>

        <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="flex gap-2 justify-between">
                <button type="button" class="btn btn-outline" @click="$dispatch('modal:close')">
                    <i class="fa fa-undo"></i>ยกเลิก
                </button>
                <button type="submit" class="btn btn-danger">
                    <i class="fa fa-trash"></i>ยืนยันการลบ
                </button>
            </div>
        </form>
    </x-modal>
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                window.dispatchEvent(new CustomEvent('modal:open', {
                    detail: {
                        context: 'editModal'
                    }
                }));
            });
        </script>
    @endif
@endsection

@push('scripts')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        // กัน error ถ้าไม่ได้โหลด lucide
        if (window.lucide && typeof lucide.createIcons === 'function') {
            lucide.createIcons();
        }

        let table;

        // รอ DOM พร้อมก่อนเสมอ
        $(function() {
            // กัน error ปุ่มที่ไม่มีใน DOM
            const addBtn = document.getElementById('add-user-button');
            if (addBtn) {
                addBtn.addEventListener('click', function() {
                    window.location.href = "{{ route('users.create') }}";
                });
            }

            // --- DataTable init ---
            table = $('#table1').DataTable({
                searching: true,
                lengthChange: false,
                dom: 'rtip',
                order: [], // ไม่กำหนด default order
                stateSave: false,
                language: {
                    paginate: {
                        previous: 'ก่อนหน้า',
                        next: 'ถัดไป'
                    },
                    info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                    emptyTable: "ไม่พบข้อมูล",
                    zeroRecords: "ไม่พบข้อมูลที่ตรงกับการค้นหา"
                }
            });

            // --- Custom search ---
            // แนะนำให้เปลี่ยน input เป็น type="search" (ดูโค้ด HTML ข้างล่าง)
            let timer;
            $('#custom-search').on('input', function() {
                clearTimeout(timer);
                const val = this.value;
                timer = setTimeout(() => table.search(val).draw(), 150);
            });

            // ถ้าอยากให้คลิกปุ่ม (x) แล้วเคลียร์ผล ให้ฟังอีเวนต์ 'search' ด้วย (ทำงานกับ type="search")
            $('#custom-search').on('search', function() {
                if (this.value === '') table.search('').draw();
            });

            // --- Dropdown toggles ---
            $('#sort-button').on('click', function(e) {
                e.stopPropagation();
                $('#sort-dropdown').toggleClass('hidden');
                $('#filter-dropdown').addClass('hidden'); // ถ้าไม่มี #filter-dropdown ก็ไม่เป็นไร
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#sort-dropdown-container, #filter-dropdown-container').length) {
                    $('#sort-dropdown, #filter-dropdown').addClass('hidden');
                }
            });

            // --- Sorting ---
            $('#sort-dropdown').on('click', '.sort-option', function(e) {
                e.stopPropagation();
                const col = Number($(this).data('column'));
                const order = String($(this).data('order')); // 'asc' | 'desc'
                table.order([
                    [col, order]
                ]).draw(false);

                $('#sort-button span').text('เรียงลำดับ: ' + $(this).text().trim());
                $('#sort-dropdown').addClass('hidden');
            });

            $('#clear-sort').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // ล้าง order ปัจจุบัน
                table.order([]).draw(false);

                // (ถ้าต้องการ) ให้กลับมาที่คอลัมน์ลำดับจากน้อยไปมาก
                table.order([
                    [0, 'asc']
                ]).draw(false);

                $('#sort-button span').text('เรียงลำดับ');
                $('#sort-dropdown').addClass('hidden');
            });
        });

        // Modal helpers - Updated for Alpine.js component
        window.openEditModal = function(id, name) {
            const editName = document.getElementById('editName');
            const editForm = document.getElementById('editForm');
            const currentName = document.getElementById('currentDepartmentName');
            if (!editName || !editForm || !currentName) {
                return;
            }
            editName.value = name || '';
            editForm.action = `/affiliations/${id}`;
            currentName.innerText = name || '';
            window.dispatchEvent(new CustomEvent('modal:open', {
                detail: {
                    context: 'editModal'
                }
            }));
        }

        window.openDeleteModal = function(id, name) {
            const deleteName = document.getElementById('deleteName');
            const deleteForm = document.getElementById('deleteForm');
            if (!deleteName || !deleteForm) {
                return;
            }
            deleteName.textContent = name || '';
            deleteForm.action = `/affiliations/${id}`;
            window.dispatchEvent(new CustomEvent('modal:open', {
                detail: {
                    context: 'deleteModal'
                }
            }));
        }
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('[data-action]');
            if (!btn) return;
            const action = btn.getAttribute('data-action');
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            if (action === 'edit-affiliation') {
                openEditModal(id, name);
            }
            if (action === 'delete-affiliation') {
                openDeleteModal(id, name);
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        .container {
            max-width: 960px !important;
        }

        .department-card {
            display: flex;
            flex-direction: column;
            background: var(--color-white);
            border-radius: var(--radius-default);
            box-shadow: var(--shadow-default);
            border: 1px solid var(--color-gray-100);
            padding: 24px;
            gap: 24px;
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

        .action-bts {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 20px;
        }

        .search-bar {
            position: relative;
            display: flex;
            align-items: center;
            width: 60%;
            min-width: 250px;
            max-width: 400px;
            height: fit-content;
            background: #fff;
            border-radius: 8px;
        }

        .search-bar input {
            font-size: 14px;
        }

        .dropdown-inds {
            position: relative;
            display: inline-block;
            width: fit-content;
            height: fit-content;
        }

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

        .search-button-container {
            display: flex;
            gap: 8px;
            width: 100%;
        }

        .dropdown-menus {
            position: absolute;
            top: 100%;
            left: 0;
            margin-top: 8px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, .1), 0 4px 6px -4px rgba(0, 0, 0, .1);
            z-index: 50;
            max-height: 400px;
            width: fit-content;
            overflow-y: auto;
        }

        .dropdown-menus.hidden {
            display: none !important;
        }

        .dropdown-item {
            display: block;
            width: 100%;
            text-align: left;
            padding: 8px 16px;
            font-size: 14px;
            color: #374151;
            background: transparent;
            border: 0;
            cursor: pointer;
            white-space: nowrap;
        }

        .dropdown-item:hover {
            background: #f3f4f6;
        }

        .categories-actions {
            display: flex;
            gap: 8px;
            justify-content: center;
            width: fit-content;
        }

        /* ================ Responsive Design ================ */
        /* < 640px (Mobile) */
        @media (max-width: 639px) {
            .container {
                max-width: 100% !important;
                padding: 8px !important;
            }

            .department-card {
                padding: 12px;
                gap: 16px;
            }

            .card-title {
                font-size: 16px;
                padding-left: 8px;
            }

            .card-title::before {
                width: 3px;
                height: 16px;
            }

            .search-button-container {
                flex-direction: column;
                gap: 8px;
            }

            .search-bar {
                width: 100%;
                min-width: 100%;
                max-width: 100%;
            }

            .search-bar input {
                font-size: 13px;
                padding: 8px 12px 8px 36px;
            }

            .dropdown-inds {
                width: 100%;
            }

            .btns {
                width: 100%;
                justify-content: center;
                font-size: 13px;
                padding: 8px 12px;
            }

            .btns svg {
                width: 14px;
                height: 14px;
            }

            .dropdown-menus {
                left: 0;
                right: 0;
                width: calc(100% - 24px);
                max-height: 250px;
            }

            .dropdown-item {
                font-size: 13px;
                padding: 6px 12px;
            }

            table.table {
                font-size: 12px;
            }

            table.table th,
            table.table td {
                padding: 8px 4px;
            }

            .action-bts {
                flex-direction: column;
                gap: 8px;
                margin-top: 16px;
            }

            .action-bts button {
                width: 100%;
                font-size: 13px;
            }

            .categories-actions {
                flex-direction: column;
                width: 100%;
                gap: 6px;
            }

            .categories-actions button {
                width: 100%;
                font-size: 12px;
            }

            .form-group input,
            .form-group select {
                font-size: 13px;
                padding: 8px 12px;
            }
        }

        /* 640px–767px (Small Tablet) */
        @media (min-width: 640px) and (max-width: 767px) {
            .container {
                max-width: 100% !important;
                padding: 12px !important;
            }

            .department-card {
                padding: 16px;
                gap: 20px;
            }

            .card-title {
                font-size: 17px;
            }

            .search-button-container {
                flex-wrap: wrap;
            }

            .search-bar {
                width: 100%;
                min-width: 100%;
                max-width: 100%;
            }

            .search-bar input {
                font-size: 13px;
            }

            .dropdown-inds {
                flex: 1;
                min-width: calc(50% - 4px);
            }

            .btns {
                font-size: 13px;
                width: 100%;
            }

            .btns svg {
                width: 15px;
                height: 15px;
            }

            .dropdown-menus {
                max-height: 300px;
            }

            table.table {
                font-size: 13px;
            }

            table.table th,
            table.table td {
                padding: 10px 6px;
            }

            .action-bts {
                margin-top: 18px;
            }

            .categories-actions {
                gap: 6px;
            }
        }

        /* 768px–1023px (Tablet) */
        @media (min-width: 768px) and (max-width: 1023px) {
            .container {
                max-width: 768px !important;
                padding: 16px !important;
            }

            .department-card {
                padding: 20px;
                gap: 22px;
            }

            .card-title {
                font-size: 17px;
            }

            .search-button-container {
                flex-wrap: nowrap;
            }

            .search-bar {
                width: 55%;
                min-width: 220px;
                max-width: 380px;
            }

            .search-bar input {
                font-size: 14px;
            }

            .btns {
                font-size: 14px;
                padding: 8px 14px;
            }

            .btns svg {
                width: 15px;
                height: 15px;
            }

            .dropdown-menus {
                max-height: 350px;
            }

            table.table {
                font-size: 14px;
            }

            table.table th,
            table.table td {
                padding: 10px 8px;
            }

            .action-bts {
                margin-top: 20px;
            }

            .categories-actions button {
                padding: 6px 10px;
            }
        }

        /* 1024px–1279px (Desktop) */
        @media (min-width: 1024px) and (max-width: 1279px) {
            .container {
                max-width: 900px !important;
            }

            .department-card {
                padding: 22px;
            }

            .search-bar {
                width: 58%;
                max-width: 390px;
            }

            .btns {
                font-size: 14px;
            }

            .dropdown-menus {
                max-height: 380px;
            }

            table.table th,
            table.table td {
                padding: 10px;
            }
        }

        /* 1280px–1535px (Large Desktop) */
        @media (min-width: 1280px) and (max-width: 1535px) {
            .container {
                max-width: 960px !important;
            }

            .department-card {
                padding: 24px;
                gap: 24px;
            }

            .search-bar {
                width: 60%;
                max-width: 400px;
            }

            .dropdown-menus {
                max-height: 400px;
            }
        }

        /* 1536px+ (Extra Large) */
        @media (min-width: 1536px) {
            .container {
                max-width: 1024px !important;
            }
        }
    </style>
@endpush
