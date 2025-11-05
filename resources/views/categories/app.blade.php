@extends('layouts.app')
@section('title', 'จัดการข้อมูลมาตรฐานและด้านการประเมิน')

@section('header', 'จัดการข้อมูลมาตรฐานและด้านการประเมิน')
@section('subheader', 'ระบบบริหารจัดการข้อมูลการรับรองสถาบันจากสภาการพยาบาล')

@section('content')
    <div class="categories-card">
        <x-card class="space-y-4">
            <div class="card-title">เพิ่มมาตรฐานการประเมิน</div>
            <form action="{{ route('standards.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">ชื่อมาตรฐานการประเมิน <span class="required">*</span></label>
                    <input type="text" name="name" class="form-input" required aria-label="ชื่อมาตรฐาน">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> บันทึก
                </button>
            </form>
        </x-card>

        <x-card class="space-y-4">
            <div class="card-title">รายชื่อมาตรฐานการประเมินที่มี</div>
            <!-- Search & Filter Controls Group -->
            <div class="search-button-container">
                <div class="search-bar shadow-sm">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" id="custom-search-standards" aria-label="ค้นหามาตรฐาน"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40"
                        placeholder="ค้นหามาตรฐาน">
                </div>
                <!-- Sort -->
                <div class="dropdown-inds w-full" id="sort-dropdown-container">
                    <button id="sort-button-standards" class="btns">
                        <span>เรียงลำดับ</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                        </svg>
                    </button>
                    <div id="sort-dropdown-standards" class="dropdown-menus hidden" role="menu"
                        aria-orientation="vertical">
                        <button class="dropdown-item sort-option" data-column="0" data-order="asc" role="menuitem">ลำดับ
                            (น้อยไปมาก)</button>
                        <button class="dropdown-item sort-option" data-column="0" data-order="desc" role="menuitem">ลำดับ
                            (มากไปน้อย)</button>
                        <button class="dropdown-item sort-option" data-column="1" data-order="asc"
                            role="menuitem">ชื่อผู้ใช้งาน
                            (A-Z)</button>
                        <button class="dropdown-item sort-option" data-column="1" data-order="desc"
                            role="menuitem">ชื่อผู้ใช้งาน (Z-A)</button>
                        <div class="dropdown-divider"></div>
                        <button id="clear-sort-standards" type="button" class="dropdown-item"
                            style="color:#4b5563;">ล้างการเรียงลำดับ</button>
                    </div>
                </div>
            </div>

            <table class="table" id="table1">
                <thead>
                    <tr>
                        <th class="text-xs !text-center sm:text-sm font-medium text-gray-900 cursor-pointer">ลำดับ</th>
                        <th class="text-xs !text-center sm:text-sm font-medium text-gray-900 cursor-pointer">
                            ชื่อมาตรฐานการประเมิน</th>
                        <th class="text-xs !text-center sm:text-sm font-medium text-gray-900 cursor-pointer w-5"><span
                                class="w-fit">จัดการ</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($standards as $index => $item)
                        <tr>
                            <td class="text-xs text-center sm:text-sm text-gray-700 align-top">{{ $index + 1 }}</td>
                            <td class="text-xs sm:text-sm text-gray-700 align-top">{{ $item->name }}</td>
                            <td class="text-xs !text-center sm:text-sm text-gray-700 align-top w-fit">
                                <div class="categories-actions">
                                    <button class="btn btn-outline"
                                        onclick="openEditModalStandards({{ $item->id }}, '{{ $item->name }}')">
                                        <i class="fa fa-edit"></i> แก้ไข
                                    </button>

                                    <button class="btn btn-outline !text-red-500 !border-red-500"
                                        onclick="openDeleteModalStandards({{ $item->id }}, '{{ $item->name }}')">
                                        <i class="fa fa-trash"></i> ลบ
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-card>

        <x-card class="space-y-4">
            <div class="card-title">เพิ่มด้าน และคะแนนเต็มการประเมิน</div>
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">ชื่อด้านการประเมิน <span class="required">*</span></label>
                    <input type="text" name="name" class="form-input" required aria-label="ชื่อหมวดหมู่">
                </div>
                <div class="form-group">
                    <label class="form-label">ตะแนนเต็มของด้านการประเมิน <span class="required">*</span></label>
                    <input type="text" name="max_score" class="form-input" required aria-label="คะแนนสูงสุด">
                </div>
                <div class="form-group">
                    <label class="form-label">เลือกมาตรของด้านการประเมิน <span class="required">*</span></label>
                    <select name="standard_id" class="form-input" required aria-label="มาตรฐาน">
                        <option value="">-- เลือกมาตรฐาน --</option>
                        @foreach ($standards as $standard)
                            <option value="{{ $standard->id }}">{{ $standard->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> บันทึก
                </button>
            </form>
        </x-card>


        <x-card class="space-y-4">
            <div class="card-title">รายชื่อด้านการประเมินที่มี</div>
            <!-- รายการด้านการประเมิน -->
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
                    <input type="text" id="custom-search-categories" aria-label="ค้นหาหมวดหมู่"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40"
                        placeholder="ค้นหารายการชื่อผู้ใช้">
                </div>
                <!-- Sort Button with Dropdown -->
                <div class="dropdown" id="sort-dropdown-container">
                    <button id="sort-button-categories" class="btns">
                        <span>เรียงลำดับ</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                        </svg>
                    </button>
                    <div id="sort-dropdown-categories" class="dropdown-menus hidden" role="menu"
                        aria-orientation="vertical">
                        <button class="dropdown-item sort-option" data-column="0" data-order="asc" role="menuitem">ลำดับ
                            (น้อยไปมาก)</button>
                        <button class="dropdown-item sort-option" data-column="0" data-order="desc"
                            role="menuitem">ลำดับ
                            (มากไปน้อย)</button>
                        <button class="dropdown-item sort-option" data-column="1" data-order="asc"
                            role="menuitem">ชื่อผู้ใช้งาน
                            (A-Z)</button>
                        <button class="dropdown-item sort-option" data-column="1" data-order="desc"
                            role="menuitem">ชื่อผู้ใช้งาน (Z-A)</button>
                        <div class="dropdown-divider"></div>
                        <button id="clear-sort-categories" type="button" class="dropdown-item"
                            style="color:#4b5563;">ล้างการเรียงลำดับ</button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-scroll">
                <table class="table" id="table2">
                    <thead>
                        <tr>
                            <th class="text-xs !text-center sm:text-sm font-medium text-gray-900 cursor-pointer">ลำดับ</th>
                            <th class="text-xs !text-center sm:text-sm font-medium text-gray-900 cursor-pointer">
                                ชื่อด้านการประเมิน</th>
                            <th class="text-xs !text-center sm:text-sm font-medium text-gray-900 cursor-pointer">คะแนนเต็ม
                            </th>
                            <th class="text-xs !text-center sm:text-sm font-medium text-gray-900 cursor-pointer">มาตรฐาน
                            </th>
                            <th class="text-xs !text-center sm:text-sm font-medium text-gray-900 cursor-pointer w-5"><span
                                    class="w-fit">จัดการ</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $index => $cat)
                            <tr>
                                <td class="text-xs text-center sm:text-sm text-gray-700 align-top">{{ $index + 1 }}
                                </td>
                                <td class="text-xs sm:text-sm text-gray-700 align-top">{{ $cat->name }}</td>
                                <td class="text-xs text-center sm:text-sm text-gray-700 align-top">{{ $cat->max_score }}
                                </td>
                                <td class="text-xs text-center sm:text-sm text-gray-700 align-top">
                                    {{ $cat->standard->name ?? '-' }}</td>
                                <td class="text-xs text-center sm:text-sm text-gray-700 align-top">
                                    <div class="categories-actions">
                                        <button class="btn btn-outline"
                                            onclick="openEditModalCat({{ $cat->id }}, '{{ $cat->name }}', '{{ $cat->max_score }}', '{{ $cat->standard_id }}', '{{ $cat->standard->name }}')">
                                            <i class="fa fa-edit"></i> แก้ไข
                                        </button>

                                        <button class="btn btn-outline !text-red-500 !border-red-500"
                                            onclick="openDeleteModalCat({{ $cat->id }}, '{{ $cat->name }}', '{{ $cat->max_score }}', '{{ $cat->standard->name }}')">
                                            <i class="fa fa-trash"></i> ลบ
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>

    <!-- Edit Modal Standards-->
    <x-modal title="แก้ไขชื่อมาตรฐานการประเมิน" size="md" context="editModalStandards" :closeOnBg="false">
        <div class="mb-4 text-sm text-gray-600">
            แก้ไขชื่อมาตรฐานการประเมินที่ต้องการแล้วกดบันทึกเพื่อบันทึกผลที่ต้องการ
            <p class="mt-2">ชื่อด้านการประเมินเดิม : <span id="currentstandardsName"
                    class="font-semibold text-pretty"></span></p>
        </div>

        <form id="editFormStandards" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    ชื่อมาตรฐานการประเมิน <span class="text-red-500">*</span>
                </label>
                <input type="text" id="editNameStandards" name="name" aria-label="ชื่อมาตรฐาน"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required value="{{ old('name') }}">
            </div>
            @error('name')
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ $message }}
                </div>
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

    <!-- Delete Modal Standards -->
    <x-modal title="ลบชื่อมาตรฐานการประเมิน" size="md" context="deleteModalStandards" :closeOnBg="false">
        <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-sm text-yellow-800">
                <strong>คำเตือน :</strong> การลบชื่อมาตรฐานการประเมินที่ถูกนำมาใช้แล้วจะไม่สามารถลบได้
            </p>
        </div>

        <div class="mb-6 text-center text-gray-700 text-sm">
            คุณต้องการลบข้อมูลด้านการประเมิน "<span id="deleteNameStandards"
                class="font-semibold text-red-600 text-pretty"></span>"
            หรือไม่?
        </div>

        <form id="deleteFormStandards" method="POST">
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

    <!-- Edit Modal Categories-->
    <x-modal title="แก้ไขชื่อด้านการประเมิน" size="md" context="editModalCategories" :closeOnBg="false">
        <div class="mb-4 text-sm text-gray-600">
            แก้ไขชื่อด้านการประเมินที่ต้องการแล้วกดบันทึกเพื่อบันทึกผลที่ต้องการ
            <p class="mt-2">ชื่อด้านการประเมินเดิม : <span id="currentcategoriesName"
                    class="font-semibold text-pretty"></span></p>
            <p>คะแนนเต็มเดิม : <span id="currentcategoriesMaxScore" class="font-semibold text-pretty"></span></p>
            <p>มาตรฐานเดิม : <span id="currentcategoriesStandardName" class="font-semibold text-pretty"></span></p>
        </div>

        <form id="editFormCategories" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    ชื่อด้านการประเมิน <span class="text-red-500">*</span>
                </label>
                <input type="text" id="editNameCategories" name="name" aria-label="ชื่อหมวดหมู่"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required value="{{ old('name') }}">
            </div>
            @error('name')
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ $message }}
                </div>
            @enderror

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    คะแนนเต็ม <span class="text-red-500">*</span>
                </label>
                <input type="number" id="editMaxScore" name="max_score" aria-label="คะแนนสูงสุด"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required value="{{ old('max_score') }}">
            </div>
            @error('max_score')
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ $message }}
                </div>
            @enderror

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    เลือกมาตรฐาน <span class="text-red-500">*</span>
                </label>
                <select id="editStandardId" name="standard_id" aria-label="มาตรฐาน"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required>
                    <option value="">-- เลือกมาตรฐาน --</option>
                    @foreach ($standards as $standard)
                        <option value="{{ $standard->id }}">{{ $standard->name }}</option>
                    @endforeach
                </select>
            </div>
            @error('standard_id')
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ $message }}
                </div>
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

    <!-- Delete Modal Categories -->
    <x-modal title="ลบชื่อด้านการประเมิน" size="md" context="deleteModalCategories" :closeOnBg="false">
        <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-sm text-yellow-800">
                <strong>คำเตือน :</strong> การลบชื่อด้านการประเมินที่ถูกนำมาใช้แล้วจะไม่สามารถลบได้
            </p>
        </div>

        <div class="mb-6 text-center text-gray-700 text-sm">
            <p>คุณต้องการลบข้อมูลด้านการประเมิน </p>
            <p> "<span id="deleteNameCategories" class="font-semibold text-red-600 text-pretty"></span>" </p>
            <p>(คะแนนเต็ม: <span id="deleteMaxScoreCategories" class="font-semibold text-pretty"></span>)</p>
            <p>มาตรฐาน: <span id="deleteStandardNameCategories" class="font-semibold text-pretty"></span></p>
            <p class="mt-2">หรือไม่?</p>
        </div>

        <form id="deleteFormCategories" method="POST">
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
                // Adjust this to show the correct modal based on which form had errors
                window.dispatchEvent(new CustomEvent('modal:open', {
                    detail: {
                        context: 'editModalCategories'
                    }
                }));
            });
        </script>
    @endif
@endsection

@push('scripts')
    <script>
        // Standards Modal Functions - Updated for Alpine.js
        function openEditModalStandards(id, name) {
            document.getElementById('editNameStandards').value = name;
            document.getElementById('editFormStandards').action = `/standards/${id}`;
            document.getElementById('currentstandardsName').innerText = name;
            window.dispatchEvent(new CustomEvent('modal:open', {
                detail: {
                    context: 'editModalStandards'
                }
            }));
        }

        function openDeleteModalStandards(id, name) {
            document.getElementById('deleteNameStandards').textContent = name;
            document.getElementById('deleteFormStandards').action = `/standards/${id}`;
            window.dispatchEvent(new CustomEvent('modal:open', {
                detail: {
                    context: 'deleteModalStandards'
                }
            }));
        }

        // Categories Modal Functions - Updated for Alpine.js
        function openEditModalCat(id, name, max_score, standard_id, standard_name) {
            document.getElementById('editNameCategories').value = name;
            document.getElementById('editMaxScore').value = max_score;
            document.getElementById('editStandardId').value = standard_id;
            document.getElementById('editFormCategories').action = `/categories/${id}`;
            document.getElementById('currentcategoriesName').innerText = name;
            document.getElementById('currentcategoriesMaxScore').innerText = max_score;
            document.getElementById('currentcategoriesStandardName').innerText = standard_name;
            window.dispatchEvent(new CustomEvent('modal:open', {
                detail: {
                    context: 'editModalCategories'
                }
            }));
        }

        function openDeleteModalCat(id, name, max_score, standard_name) {
            document.getElementById('deleteNameCategories').textContent = name;
            document.getElementById('deleteMaxScoreCategories').textContent = max_score;
            document.getElementById('deleteStandardNameCategories').textContent = standard_name;
            document.getElementById('deleteFormCategories').action = `/categories/${id}`;
            window.dispatchEvent(new CustomEvent('modal:open', {
                detail: {
                    context: 'deleteModalCategories'
                }
            }));
        }
    </script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        // ---------- Helper: init ตาราง + event ชุดเดียว ----------
        function initTableControls({
            tableSelector,
            searchInputSelector,
            sortButtonSelector,
            sortDropdownSelector,
            clearSortSelector
        }) {
            // 1) Init DataTable
            const dt = $(tableSelector).DataTable({
                searching: true,
                lengthChange: false,
                dom: 'rtip',
                order: [], // ไม่มี default sort
                stateSave: false, // ไม่จำสถานะ
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

            // 2) ค้นหา (debounce เล็กน้อย)
            let typingTimer;
            const doSearch = (val) => dt.search(val).draw();

            $(searchInputSelector)
                .on('input', function() {
                    clearTimeout(typingTimer);
                    const val = this.value;
                    typingTimer = setTimeout(() => doSearch(val), 150);
                })
                .on('search', function() { // รองรับกด x เคลียร์
                    if (this.value === '') doSearch('');
                });

            // 3) เปิด/ปิด sort dropdown
            $(sortButtonSelector).on('click', function(e) {
                e.stopPropagation();
                $(sortDropdownSelector).toggleClass('hidden');
            });

            // 4) คลิกตัวเลือกเรียงลำดับ
            $(sortDropdownSelector).on('click', '.sort-option', function() {
                const col = Number($(this).data('column'));
                const order = String($(this).data('order')); // 'asc' | 'desc'
                dt.order([col, order]).draw(false);

                // อัปเดตข้อความปุ่มให้ผู้ใช้รู้ว่าตอนนี้เรียงตามอะไร
                const label = $(this).text().trim();
                const $btnSpan = $(sortButtonSelector).find('span').first();
                $btnSpan.text('เรียงลำดับ: ' + label);

                // ปิด dropdown
                $(sortDropdownSelector).addClass('hidden');
            });

            // 5) ล้างการเรียงลำดับ
            $(clearSortSelector).on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // ล้าง order → แล้ว fallback ให้เรียงคอลัมน์ลำดับ (0) จากน้อยไปมาก
                dt.order([]).draw(false);
                dt.order([0, 'asc']).draw(false);

                // รีเซ็ตข้อความปุ่ม + ปิด dropdown
                const $btnSpan = $(sortButtonSelector).find('span').first();
                $btnSpan.text('เรียงลำดับ');
                $(sortDropdownSelector).addClass('hidden');
            });

            return dt;
        }

        // ---------- ป้องกัน dropdown เปิดค้าง (คลิกภายนอกแล้วปิด) ----------
        function setupGlobalDropdownCloser(dropdownSelectors = []) {
            $(document).on('click', function(e) {
                // ถ้าคลิกนอก dropdown ทั้งหมด ให้ปิดทุก dropdown
                const clickedInsideAny = dropdownSelectors.some(sel => $(e.target).closest(sel).length > 0);
                if (!clickedInsideAny) {
                    dropdownSelectors.forEach(sel => $(sel).addClass('hidden'));
                }
            });
        }

        // ---------- เริ่มทำงานเมื่อ DOM พร้อม ----------
        $(function() {
            // Init สำหรับ Standards (#table1)
            const dt1 = initTableControls({
                tableSelector: '#table1',
                searchInputSelector: '#custom-search-standards',
                sortButtonSelector: '#sort-button-standards',
                sortDropdownSelector: '#sort-dropdown-standards',
                clearSortSelector: '#clear-sort-standards'
            });

            // Init สำหรับ Categories (#table2)
            const dt2 = initTableControls({
                tableSelector: '#table2',
                searchInputSelector: '#custom-search-categories',
                sortButtonSelector: '#sort-button-categories',
                sortDropdownSelector: '#sort-dropdown-categories',
                clearSortSelector: '#clear-sort-categories'
            });

            // ปิด dropdown เมื่อคลิกพื้นที่ว่าง (ครอบคลุมทั้งสองชุด)
            setupGlobalDropdownCloser([
                '#sort-dropdown-standards',
                '#sort-dropdown-categories'
            ]);

            // (ถ้ามี filter เพิ่มภายหลังค่อย bind แยกตามตารางแบบเดียวกับ sort/search)
        });
    </script>
@endpush

@push('styles')
    <style>
        .container {
            max-width: 960px !important;
        }

        .categories-card {
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

        .search-button-container {
            display: flex;
            gap: 8px;
            width: 100%;
        }

        .dropdown-inds {
            position: relative;
            display: inline-block;
            width: fit-content;
            height: fit-content;
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

            .categories-card {
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

            .table {
                font-size: 12px;
            }

            .table th,
            .table td {
                padding: 8px 4px;
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

            form .form-group {
                margin-bottom: 12px;
            }

            form input,
            form select,
            form textarea {
                font-size: 13px;
                padding: 8px 12px;
            }

            .action-bts {
                flex-direction: column;
                gap: 8px;
            }

            .action-bts button {
                width: 100%;
            }
        }

        /* 640px–767px (Small Tablet) */
        @media (min-width: 640px) and (max-width: 767px) {
            .container {
                max-width: 100% !important;
                padding: 12px !important;
            }

            .categories-card {
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

            .dropdown-menus {
                max-height: 300px;
            }

            .table {
                font-size: 13px;
            }

            .table th,
            .table td {
                padding: 10px 6px;
            }

            .categories-actions {
                gap: 6px;
            }

            form input,
            form select,
            form textarea {
                font-size: 14px;
            }
        }

        /* 768px–1023px (Tablet) */
        @media (min-width: 768px) and (max-width: 1023px) {
            .container {
                max-width: 768px !important;
                padding: 16px !important;
            }

            .categories-card {
                padding: 20px;
            }

            .search-button-container {
                flex-wrap: nowrap;
            }

            .search-bar {
                width: 50%;
                min-width: 200px;
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

            .table {
                font-size: 14px;
            }

            .table th,
            .table td {
                padding: 10px 8px;
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

            .categories-card {
                padding: 22px;
            }

            .search-bar {
                width: 55%;
            }

            .btns {
                font-size: 14px;
            }

            .dropdown-menus {
                max-height: 380px;
            }

            .table th,
            .table td {
                padding: 10px;
            }
        }

        /* 1280px–1535px (Large Desktop) */
        @media (min-width: 1280px) and (max-width: 1535px) {
            .container {
                max-width: 960px !important;
            }

            .categories-card {
                padding: 24px;
            }

            .search-bar {
                width: 60%;
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
