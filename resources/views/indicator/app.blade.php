@extends('layouts.app')

@section('title', 'รายการตัวบ่งชี้')

@section('header', 'รายการตัวบ่งชี้')
@section('subheader', 'ระบบบริหารจัดการข้อมูลการรับรองสถาบันจากสภาการพยาบาล')

@section('content')
    <div>
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
                    <input type="search" name="custom_search" id="custom-search" placeholder="ค้นหารายการตัวบ่งชี้..."
                        aria-label="ค้นหารายการตัวบ่งชี้" aria-controls="myTable" autocomplete="off"
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
                            class="hidden absolute left-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                            <div class="py-1" role="menu" aria-orientation="vertical">
                                <button
                                    class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    data-column="0" data-order="asc" role="menuitem">ปี (น้อยไปมาก)</button>
                                <button
                                    class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    data-column="0" data-order="desc" role="menuitem">ปี (มากไปน้อย)</button>
                                <button
                                    class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    data-column="3" data-order="asc" role="menuitem">ชื่อตัวบ่งชี้ (A-Z)</button>
                                <button
                                    class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    data-column="3" data-order="desc" role="menuitem">ชื่อตัวบ่งชี้ (Z-A)</button>
                                <button
                                    class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    data-column="8" data-order="asc" role="menuitem">ผลลัพธ์ (น้อยไปมาก)</button>
                                <button
                                    class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    data-column="8" data-order="desc" role="menuitem">ผลลัพธ์ (มากไปน้อย)</button>
                                <button id="clear-sort" type="button"
                                    class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">ล้างตัวเรียงลำดับ</button>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Button with Dropdown (styled like assigned dashboard) -->
                    <div class="dropdown-inds w-full" id="filter-dropdown-container">
                        <button id="filter-button" class="btns">
                            <span>กรองข้อมูล</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                        </button>

                        <div id="filter-dropdown" class="dropdown-menus hidden">
                            <div class="filter-grid">
                                <div class="filter-section">
                                    {{-- Section: ปี --}}
                                    <span class="dropdown-title">ปี</span>
                                    <div class="dropdown-multiselect" id="yearDropdown">
                                        <div class="dropdown-btn" onclick="toggleDropdown('yearDropdown')">
                                            <span id="year-label">เลือกปี</span>
                                            <i class="fa-solid fa-caret-down"></i>
                                        </div>
                                        <div class="dropdown-content">
                                            <div class="dropdown-tools" data-section="yearDropdown">
                                                <input type="text" name="year_search" class="filter-search" aria-label="ค้นหาปี"
                                                    placeholder="ค้นหา..." aria-label="ค้นหาปี">
                                                <div class="tools-actions">
                                                    <button type="button" class="tool-btn"
                                                        data-action="select-all">ทั้งหมด</button>
                                                    <button type="button" class="tool-btn"
                                                        data-action="clear-all">ล้างทั้งหมด</button>
                                                </div>
                                            </div>
                                            @foreach ($indicators->pluck('year')->unique()->sortDesc() as $year)
                                                <label>
                                                    <input type="checkbox" name="year[]" class="filter-option"
                                                        data-column="0" data-value="{{ $year }}">
                                                    <span>
                                                        {{ $year }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="filter-section">
                                    {{-- Section: มาตรฐาน --}}
                                    <span class="dropdown-title">มาตรฐาน</span>
                                    <div class="dropdown-multiselect" id="standardDropdown">
                                        <div class="dropdown-btn" onclick="toggleDropdown('standardDropdown')">
                                            <span id="standard-label">เลือกมาตรฐาน</span>
                                            <i class="fa-solid fa-caret-down"></i>
                                        </div>

                                        <div class="dropdown-content">
                                            <div class="dropdown-tools" data-section="standardDropdown">
                                                <input type="text" name="standard_search" class="filter-search" aria-label="ค้นหามาตรฐาน"
                                                    placeholder="ค้นหา..." aria-label="ค้นหามาตรฐาน">
                                                <div class="tools-actions">
                                                    <button type="button" class="tool-btn"
                                                        data-action="select-all">ทั้งหมด</button>
                                                    <button type="button" class="tool-btn"
                                                        data-action="clear-all">ล้างทั้งหมด</button>
                                                </div>
                                            </div>
                                            @foreach ($indicators->pluck('standard.name')->unique() as $std)
                                                <label>
                                                    <input type="checkbox" name="standard[]" class="filter-option "
                                                        data-column="2" data-value="{{ $std }}">
                                                    <span>{{ $std }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="filter-section">
                                    {{-- Section: ด้าน --}}
                                    <span class="dropdown-title">ด้าน</span>
                                    <div class="dropdown-multiselect" id="dimensionDropdown">
                                        <div class="dropdown-btn" onclick="toggleDropdown('dimensionDropdown')">
                                            <span id="dimension-label">เลือกด้าน</span>
                                            <i class="fa-solid fa-caret-down"></i>
                                        </div>
                                        <div class="dropdown-content">
                                            <div class="dropdown-tools" data-section="dimensionDropdown">
                                                <input type="text" name="dimension_search" class="filter-search" aria-label="ค้นหามิติ"
                                                    placeholder="ค้นหา..." aria-label="ค้นหาด้าน">
                                                <div class="tools-actions">
                                                    <button type="button" class="tool-btn"
                                                        data-action="select-all">ทั้งหมด</button>
                                                    <button type="button" class="tool-btn"
                                                        data-action="clear-all">ล้างทั้งหมด</button>
                                                </div>
                                            </div>
                                            @foreach ($indicators->pluck('category.name')->unique() as $dim)
                                                <label>
                                                    <input type="checkbox" name="dimension[]" class="filter-option"
                                                        data-column="1" data-value="{{ $dim }}">
                                                    <span>{{ $dim }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="filter-section">
                                    {{-- Section: สังกัดงาน --}}
                                    <span class="dropdown-title">สังกัดงาน</span>
                                    <div class="dropdown-multiselect" id="workGroupDropdown">
                                        <div class="dropdown-btn" onclick="toggleDropdown('workGroupDropdown')">
                                            <span id="workGroup-label">เลือกสังกัดงาน</span>
                                            <i class="fa-solid fa-caret-down"></i>
                                        </div>
                                        <div class="dropdown-content">
                                            <div class="dropdown-tools" data-section="workGroupDropdown">
                                                <input type="text" name="work_group_search" class="filter-search"
                                                    aria-label="ค้นหาสังกัดงาน" placeholder="ค้นหา...">
                                                <div class="tools-actions">
                                                    <button type="button" class="tool-btn"
                                                        data-action="select-all">ทั้งหมด</button>
                                                    <button type="button" class="tool-btn"
                                                        data-action="clear-all">ล้างทั้งหมด</button>
                                                </div>
                                            </div>
                                            @php
                                                $workGroupOptions = $indicators
                                                    ->flatMap(function ($i) {
                                                        return collect($i['assignments'] ?? [])
                                                            ->pluck('user.work_group_name')
                                                            ->filter();
                                                    })
                                                    ->unique()
                                                    ->values();
                                            @endphp
                                            @foreach ($workGroupOptions as $workGroup)
                                                <label>
                                                    <input type="checkbox" name="work_group[]" class="filter-option "
                                                        data-column="6" data-value="{{ $workGroup }}">
                                                    <span>{{ $workGroup }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="filter-section">
                                    {{-- Section: หน่วยงานที่รับผิดชอบ --}}
                                    <span class="dropdown-title">หน่วยงานที่รับผิดชอบ</span>
                                    <div class="dropdown-multiselect" id="deptDropdown">
                                        <div class="dropdown-btn" onclick="toggleDropdown('deptDropdown')">
                                            <span id="dept-label">เลือกหน่วยงาน</span>
                                            <i class="fa-solid fa-caret-down"></i>
                                        </div>
                                        <div class="dropdown-content">
                                            <div class="dropdown-tools" data-section="deptDropdown">
                                                <input type="text" name="dept_search" class="filter-search" aria-label="ค้นหาภาควิชา/หน่วยงาน"
                                                    placeholder="ค้นหา..." aria-label="ค้นหาหน่วยงานที่รับผิดชอบ">
                                                <div class="tools-actions">
                                                    <button type="button" class="tool-btn"
                                                        data-action="select-all">ทั้งหมด</button>
                                                    <button type="button" class="tool-btn"
                                                        data-action="clear-all">ล้างทั้งหมด</button>
                                                </div>
                                            </div>
                                            @php
                                                $deptOptions = $indicators
                                                    ->flatMap(function ($i) {
                                                        return collect($i['assignments'] ?? [])
                                                            ->pluck('user.department_name')
                                                            ->filter();
                                                    })
                                                    ->unique()
                                                    ->values();
                                            @endphp
                                            @foreach ($deptOptions as $dept)
                                                <label>
                                                    <input type="checkbox" name="dept[]" class="filter-option "
                                                        data-column="7" data-value="{{ $dept }}">
                                                    <span>{{ $dept }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="filter-section">
                                    {{-- Section: ประเภทตัวบ่งชี้ --}}
                                    <span class="dropdown-title">ประเภทตัวบ่งชี้</span>
                                    <div class="dropdown-multiselect" id="typeDropdown">
                                        <div class="dropdown-btn" onclick="toggleDropdown('typeDropdown')">
                                            <span id="type-label">เลือกประเภท</span>
                                            <i class="fa-solid fa-caret-down"></i>
                                        </div>
                                        <div class="dropdown-content">
                                            <div class="dropdown-tools" data-section="typeDropdown">
                                                <input type="text" name="type_search" class="filter-search" aria-label="ค้นหาประเภท"
                                                    placeholder="ค้นหา..." aria-label="ค้นหาประเภทตัวบ่งชี้">
                                                <div class="tools-actions">
                                                    <button type="button" class="tool-btn"
                                                        data-action="select-all">ทั้งหมด</button>
                                                    <button type="button" class="tool-btn"
                                                        data-action="clear-all">ล้างทั้งหมด</button>
                                                </div>
                                            </div>
                                            @foreach ($indicators->pluck('type')->unique() as $type)
                                                <label>
                                                    <input type="checkbox" name="type[]" class="filter-option "
                                                        data-column="5" data-value="{{ $type ?? 'ไม่ระบุ' }}">
                                                    <span>
                                                        {{ $type ?? 'ไม่ระบุ' }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="filter-section">
                                    {{-- Section: สถานะตัวบ่งชี้ --}}
                                    <span class="dropdown-title">สถานะตัวบ่งชี้</span>
                                    <div class="dropdown-multiselect" id="statusDropdown">
                                        <div class="dropdown-btn" onclick="toggleDropdown('statusDropdown')">
                                            <span id="status-label">เลือกสถานะ</span>
                                            <i class="fa-solid fa-caret-down"></i>
                                        </div>
                                        @php
                                            $statusMap = [
                                                0 => 'รอดำเนินการ',
                                                1 => 'รอดำเนินการ / บันทึกฉบับร่าง',
                                                2 => 'รอดำเนินการ / บันทึกฉบับจริง',
                                                3 => 'ผลการดำเนินงานครบถ้วนตามเกณฑ์มาตรการ',
                                                4 => 'ผลการดำเนินงานยังไม่ครบถ้วนตามเกณฑ์',
                                            ];

                                            $statusCodes = $indicators->pluck('status')->unique()->sort()->values();
                                        @endphp
                                        <div class="dropdown-content">
                                            <div class="dropdown-tools" data-section="statusDropdown">
                                                <input type="text" name="status_search" class="filter-search" aria-label="ค้นหาสถานะ"
                                                    placeholder="ค้นหา..." aria-label="ค้นหาสถานะตัวบ่งชี้">
                                                <div class="tools-actions">
                                                    <button type="button" class="tool-btn"
                                                        data-action="select-all">ทั้งหมด</button>
                                                    <button type="button" class="tool-btn"
                                                        data-action="clear-all">ล้างทั้งหมด</button>
                                                </div>
                                            </div>
                                            @foreach ($statusCodes as $statusCode)
                                                <label>
                                                    <input type="checkbox" name="status[]" class="filter-option"
                                                        data-column="10" data-value="{{ $statusCode }}">
                                                    <span>{{ $statusMap[$statusCode] ?? 'ไม่ระบุ' }}</span>
                                                </label>
                                            @endforeach
                                        </div>

                                    </div>
                                </div>

                                <div class="filter-section">
                                    {{-- Section: สถานะเอกสาร --}}
                                    <span class="dropdown-title">สถานะเอกสาร</span>
                                    <div class="dropdown-multiselect" id="statusEnvDropdown">
                                        <div class="dropdown-btn" onclick="toggleDropdown('statusEnvDropdown')">
                                            <span id="statusEnv-label">เลือกสถานะเอกสาร</span>
                                            <i class="fa-solid fa-caret-down"></i>
                                        </div>
                                        @php
                                            $docStatusOptions = ['รอดำเนินการ', 'ครบ', 'ไม่ครบ'];
                                        @endphp
                                        <div class="dropdown-content">
                                            <div class="dropdown-tools" data-section="statusEnvDropdown">
                                                <input type="text" name="doc_status_search" class="filter-search" aria-label="ค้นหาสถานะแอกสาร"
                                                    placeholder="ค้นหา..." aria-label="ค้นหาสถานะเอกสาร">
                                                <div class="tools-actions">
                                                    <button type="button" class="tool-btn"
                                                        data-action="select-all">ทั้งหมด</button>
                                                    <button type="button" class="tool-btn"
                                                        data-action="clear-all">ล้างทั้งหมด</button>
                                                </div>
                                            </div>
                                            @foreach ($docStatusOptions as $docStatus)
                                                <label>
                                                    <input type="checkbox" name="doc_status[]" class="filter-option"
                                                        data-column="11" data-value="{{ $docStatus }}">
                                                    <span>{{ $docStatus }}</span>
                                                </label>
                                            @endforeach
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="filters-actions">
                                {{-- Buttons --}}
                                <button id="clear-filters" type="button" class="btn px-1.5 py-2.5">ล้างตัวกรอง</button>
                                <button id="apply-filters" type="button" class="btn btn-primary px-1.5 py-2.5">ใช้ตัวกรอง</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons Group -->
            <div class="action-buttons-container">
                @if ($indicators->isNotEmpty())
                    <!-- ปุ่มเปิด Modal -->
                    <button type="button" onclick="document.getElementById('preset-modal').classList.remove('hidden')"
                        class="btn bg-purple-500 hover:bg-purple-600 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12" />
                        </svg>
                        <span>Preset</span>
                    </button>

                    <!-- Modal Component -->
                    <x-indicator-preset-modal :indicators="$indicators" modalId="preset-modal" />
                @endif

                <!-- Global Year Export Modal (reusable) -->
                @if (!auth()->user()->hasRole('administration_admin'))
                    <x-year-export-modal :years="$years" context="year-export" />
                @endif

                <button id="add_indicator_button"
                    class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>เพิ่มตัวบ่งชี้</span>
                </button>
            </div>
        </div>

        <!-- ตารางรายการตัวบ่งชี้ -->
        <div class=" border border-gray-200 rounded-lg shadow-sm overflow-x-hidden">
            <table id="myTable" class="w-full min-w-full ">
                <thead>
                    <tr>
                        <th class="w-fit text-xs sm:text-sm font-medium text-gray-900 cursor-pointer select-none hidden sm:table-cell"
                            title="ปีของตัวบ่งชี้">
                            <div class="flex items-center justify-center min-w-6">
                                ปี
                            </div>
                        </th>
                        <th class="w-fit text-xs sm:text-sm font-medium text-gray-900 cursor-pointer select-none hidden md:table-cell"
                            title="มาตรฐานตัวบ่งชี้ 3 หมวด">
                            <div class="flex items-center justify-center min-w-15">
                                มาตรฐาน
                            </div>
                        </th>
                        <th class="w-fit text-xs sm:text-sm font-medium text-gray-900 cursor-pointer select-none hidden lg:table-cell"
                            title="ด้านตัวบ่งชี้ใน 3 หมวด">
                            <div class="flex items-center justify-center min-w-20">
                                ด้าน
                            </div>
                        </th>
                        <th class="w-full text-xs sm:text-sm font-medium text-gray-900 text-left cursor-pointer select-none"
                            title="ชื่อตัวบ่งชี้">
                            <div class="flex items-center justify-center min-w-40 sm:min-w-56">
                                ชื่อตัวบ่งชี้
                            </div>
                        </th>
                        <th class="w-fit text-xs sm:text-sm font-medium text-gray-900 cursor-pointer select-none"
                            title="รหัสตัวบ่งชี้">
                            <div class="flex items-center justify-center min-w-9">
                                รหัส
                            </div>
                        </th>
                        <th class="w-fit text-xs sm:text-sm font-medium text-gray-900 cursor-pointer select-none hidden xl:table-cell"
                            title="ประเภทตัวบ่งชี้ (คุณภาพ, ปริมาณ, คุณภาพ/ปริมาณ)">
                            <div class="flex items-center justify-center min-w-11">
                                ประเภท
                            </div>
                        </th>
                        <th class="w-fit text-xs sm:text-sm font-medium text-gray-900 cursor-pointer select-none hidden xl:table-cell"
                            title="สังกัดงาน">
                            <div class="flex items-center justify-center min-w-28">
                                สังกัดงาน
                            </div>
                        </th>
                        <th class="w-fit text-xs sm:text-sm font-medium text-gray-900 cursor-pointer select-none hidden xl:table-cell"
                            title="หน่วยงานที่รับผิดชอบในตัวบ่งชี้">
                            <div class="flex items-center justify-center min-w-32 lg:min-w-48">
                                หน่วยงาน
                            </div>
                        </th>
                        <th class="w-fit text-xs sm:text-sm font-medium text-gray-900 cursor-pointer select-none hidden md:table-cell"
                            title="ผลลัพธ์จากการกรอกข้อมูลของตัวบ่งชี้">
                            <div class="flex items-center justify-center min-w-9">
                                ผลลัพธ์
                            </div>
                        </th>
                        <th class="w-fit text-xs sm:text-sm font-medium text-gray-900 cursor-pointer select-none hidden lg:table-cell"
                            title="คะแนนเต็มของตัวบ่งชี้">
                            <div class="flex items-center justify-center min-w-9 text-nowrap">
                                คะแนนเต็ม
                            </div>
                        </th>
                        <th class="w-fit text-xs sm:text-sm font-medium text-gray-900 cursor-pointer select-none"
                            title="สถานะของตัวบ่งชี้">
                            <div class="flex items-center justify-center w-fit">
                                สถานะ
                            </div>
                        </th>
                        <th class="w-fit text-xs sm:text-sm font-medium text-gray-900 cursor-pointer select-none"
                            title="สถานะเอกสาร">
                            <div class="flex items-center justify-center">
                                เอกสาร
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white ">
                    @forelse($indicators as $indicator)
                        <tr data-href="{{ route('indicator.show', $indicator['id']) }}" tabindex="0" role="link"
                            aria-label="เปิด {{ $indicator['name'] }}" title="คลิกเพื่อดูรายละเอียด">
                            <td
                                class="max-w-6 text-xs sm:text-sm text-gray-700 text-center align-top hidden sm:table-cell">
                                {{ $indicator['year'] }}</td>
                            <td
                                class="max-w-15 text-xs sm:text-sm text-balance text-gray-700 align-top hidden md:table-cell">
                                {{ $indicator['category']['name'] }}</td>
                            <td
                                class="max-w-15 text-xs sm:text-sm text-balance text-gray-700 align-top hidden lg:table-cell">
                                {{ $indicator['standard']['name'] }}</td>
                            <td class="max-w-full text-xs sm:text-sm text-gray-700 align-top">
                                <div class="text-pretty" title="{{ $indicator['name'] }}">
                                    {{ $indicator['name'] }}
                                </div>
                            </td>
                            <td class="max-w-9 text-center text-xs sm:text-sm text-gray-700 truncate align-top">
                                {{ $indicator['code'] }}</td>
                            <td
                                class="max-w-11 text-xs sm:text-sm text-gray-700 text-center align-top hidden xl:table-cell">
                                {{ $indicator['type'] ?? 'ไม่ระบุ' }}
                            </td>
                            <td class="text-xs sm:text-sm text-gray-700 align-top max-w-full cursor-auto hidden xl:table-cell"
                                data-rowlink-ignore>
                                @php
                                    $workGroups = collect($indicator['assignments'] ?? [])
                                        ->pluck('user.work_group_name')
                                        ->filter(fn($name) => filled($name))
                                        ->unique()
                                        ->values();
                                    $workGroupTotal = $workGroups->count();
                                @endphp

                                @if ($workGroupTotal === 0)
                                    <span class="text-gray-400">ไม่ระบุ</span>
                                @else
                                    <div x-data="{ open: false }" class="flex flex-wrap gap-1 ">
                                        @foreach ($workGroups as $name)
                                            <span x-show="@json($loop->iteration <= 3) || open" x-cloak
                                                class="inline-flex items-center rounded-full bg-slate-50 text-slate-700 ring-1 ring-inset ring-slate-200 px-2 py-0.5 text-xs md:text-xs max-w-52 truncate"
                                                title="{{ $name }}">
                                                {{ $name }}
                                            </span>
                                        @endforeach

                                        @if ($workGroupTotal > 3)
                                            <button type="button"
                                                class="cursor-pointer inline-flex items-center rounded-full bg-slate-300 text-slate-700 ring-1 ring-inset ring-slate-200 px-2 py-0.5 text-xs md:text-xs hover:bg-slate-200"
                                                @click="open = !open" :aria-expanded="open.toString()"
                                                x-text="open ? 'แสดงน้อยลง' : '+{{ $workGroupTotal - 3 }}'"></button>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="text-xs sm:text-sm text-gray-700 align-top max-w-full cursor-auto hidden xl:table-cell"
                                data-rowlink-ignore>
                                @php
                                    // Unique, non-empty department names
                                    $departments = collect($indicator['assignments'] ?? [])
                                        ->pluck('user.department_name', 'user.department_id') // value, key
                                        ->filter(fn($name) => filled($name))
                                        ->unique() // de-dup by name
                                        ->values();

                                    $total = $departments->count();
                                @endphp

                                @if ($total === 0)
                                    <span class="text-gray-400">ไม่มีการมอบหมาย</span>
                                @else
                                    <div x-data="{ open: false }" class="flex flex-wrap gap-1 ">
                                        @foreach ($departments as $name)
                                            <span x-show="@json($loop->iteration <= 3) || open" x-cloak
                                                class="inline-flex items-center rounded-full bg-slate-50 text-slate-700 ring-1 ring-inset ring-slate-200 px-2 py-0.5 text-xs md:text-xs max-w-52 truncate"
                                                title="{{ $name }}">
                                                {{ $name }}
                                            </span>
                                        @endforeach

                                        @if ($total > 3)
                                            <button type="button"
                                                class="cursor-pointer inline-flex items-center rounded-full bg-slate-300 text-slate-700 ring-1 ring-inset ring-slate-200 px-2 py-0.5 text-xs md:text-xs hover:bg-slate-200"
                                                @click="open = !open" :aria-expanded="open.toString()"
                                                x-text="open ? 'แสดงน้อยลง' : '+{{ $total - 3 }}'"></button>
                                        @endif
                                    </div>
                                @endif
                            </td>

                            <td class="text-xs sm:text-sm text-gray-700 text-center align-top hidden md:table-cell">
                                {{ number_format($indicator['score_acc'], 2) ?? '0.00' }}
                            </td>
                            <td class="text-xs sm:text-sm text-gray-700 text-center align-top hidden lg:table-cell">
                                {{ number_format($indicator['max_score'], 2) ?? '0.00' }}
                            </td>
                            @php
                                $statusCode = (int) ($indicator['status'] ?? -1);
                            @endphp

                            <td class="text-xs sm:text-sm text-center align-top" data-search="{{ $statusCode }}"
                                data-order="{{ $statusCode }}">
                                @switch($statusCode)
                                    @case(0)
                                        <span class="tooltip" data-tooltip="รอดำเนินการ">
                                            <i data-lucide="clock" class="w-5 h-5 text-red-500"></i>
                                            <span class="sr-only">รอดำเนินการ</span>
                                        </span>
                                    @break

                                    @case(1)
                                        <span class="tooltip" data-tooltip="รอดำเนินการ / บันทึกฉบับร่าง">
                                            <i data-lucide="clock" class="w-5 h-5 text-red-500"></i>
                                            <span class="sr-only">รอดำเนินการ / บันทึกฉบับร่าง</span>
                                        </span>
                                    @break

                                    @case(2)
                                        <span class="tooltip" data-tooltip="รอดำเนินการ / บันทึกฉบับจริง">
                                            <i data-lucide="clock" class="w-5 h-5 text-red-500"></i>
                                            <span class="sr-only">รอดำเนินการ / บันทึกฉบับจริง</span>
                                        </span>
                                    @break

                                    @case(3)
                                        <span class="tooltip" data-tooltip="ผลการดำเนินงานครบถ้วนตามเกณฑ์มาตรการ">
                                            <i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>
                                            <span class="sr-only">ผลการดำเนินงานครบถ้วนตามเกณฑ์มาตรการ</span>
                                        </span>
                                    @break

                                    @case(4)
                                        <span class="tooltip" data-tooltip="ผลการดำเนินงานยังไม่ครบถ้วนตามเกณฑ์">
                                            <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-500"></i>
                                            <span class="sr-only">ผลการดำเนินงานยังไม่ครบถ้วนตามเกณฑ์</span>
                                        </span>
                                    @break

                                    @default
                                        <span class="tooltip" data-tooltip="สถานะไม่ระบุ">
                                            <i data-lucide="help-circle" class="w-5 h-5 text-gray-400"></i>
                                            <span class="sr-only">สถานะไม่ระบุ</span>
                                        </span>
                                @endswitch
                            </td>

                            @php
                                if ($indicator['criteria_status'] == 2) {
                                    $docText = 'ไม่ครบ';
                                    $docOrder = 2;
                                    $badgeCls = 'bg-red-100 text-red-800';
                                } elseif ($indicator['criteria_status'] === 1) {
                                    $docText = 'ครบ';
                                    $docOrder = 1;
                                    $badgeCls = 'bg-green-100 text-green-800';
                                } else {
                                    $docText = 'รอดำเนินการ';
                                    $docOrder = 0;
                                    $badgeCls = 'bg-gray-100 text-gray-800';
                                }
                            @endphp

                            <td class="align-top {{ $badgeCls }}" data-search="{{ $docText }}"
                                data-order="{{ $docOrder }}">
                                <div class="justify-center text-center font-medium truncate text-xs">
                                    {{ $docText }}
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="10" class=" px-4 py-8 text-center text-gray-500">
                                    ไม่พบข้อมูลตัวบ่งชี้
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        </div>
    @endsection

    @push('styles')
        {{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.dataTables.min.css"> --}}
        <style>
            /* ================ Base / Datatable ================ */
            #myTable,
            table.dataTable {
                width: 100% !important;
            }

            table.dataTable thead th,
            table.dataTable tbody td {
                padding: 10px 6px;
            }

            table.dataTable thead th {
                position: relative;
                background-color: #f9fafb;
                border-bottom: 1px solid #e5e7eb;
                font-weight: 600;
            }

            .dataTables_wrapper .dataTables_info {
                color: #4b5563;
            }

            .dataTables_wrapper .dataTables_length select {
                border: 1px solid #a0aec0;
                border-radius: 0.5rem;
                padding: 0.5rem;
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

            table.dataTable thead th:hover {
                background-color: #f3f4f6;
            }


            /* Row hover/focus */
            #myTable tbody tr {
                transition: background-color .15s ease, transform .05s ease;
                cursor: pointer;
            }

            #myTable tbody tr:hover {
                background-color: #dbeafe !important;
            }

            table.dataTable tbody tr,
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
                height: fit-content;
                background: #fff;
                border-radius: 8px;
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
                height: fit-content;
            }

            .action-buttons-container button {
                display: flex;
                align-items: center;
                gap: 4px;
                font-size: 14px;
                /* color: #fff; */
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

            .dropdown-menus {
                position: absolute;
                top: 100%;
                /* left: calc(100% - 260px); */
                left: 0;
                margin-top: 8px;
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                padding: 12px;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, .1), 0 4px 6px -4px rgba(0, 0, 0, .1);
                z-index: 50;
                max-height: 400px;
            }

            .filter-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 12px;
                height: 310px;
                overflow-y: auto;
            }

            .filter-section {
                display: flex;
                flex-direction: column;
                gap: 5px;
                width: 100%;
                min-width: 350px;
                font-size: 14px;
                color: #374151;
            }

            .filters-actions {
                display: flex;
                justify-content: space-between;
                margin-top: 12px;
            }

            .dropdown-title {
                font-weight: 600;
                color: #111827;
                padding-left: 6px;
            }

            /* ================ Multiselect Components ================ */
            .dropdown-multiselect {
                border: 1px solid #e5e7eb;
                border-radius: 8px;
            }

            .dropdown-multiselect .dropdown-btn {
                display: flex;
                align-items: center;
                justify-content: space-between;
                width: 100%;
                padding: 8px 10px;
                cursor: pointer;
            }

            .dropdown-multiselect .dropdown-btn i,
            .dropdown-multiselect .dropdown-btn .fa-caret-down {
                transition: transform .2s ease;
            }

            .dropdown-multiselect.open .dropdown-btn i,
            .dropdown-multiselect.open .dropdown-btn .fa-caret-down {
                transform: rotate(180deg);
            }

            .dropdown-multiselect .dropdown-content {
                display: none;
                max-height: 220px;
                overflow-y: auto;
                border-top: 1px solid #e5e7eb;
                padding: 8px 10px;
                background: #fff;
            }

            .dropdown-multiselect.open .dropdown-content {
                /* display: flex;
                flex-direction: column; */
                /* position: absolute; */

                display: inline-flex;
                flex-direction: column;
                margin-top: 3px;
                gap: 4px;
                max-height: 150px;
                max-width: 350px;
                overflow-y: auto;
                z-index: 50;
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, .1), 0 4px 6px -4px rgba(0, 0, 0, .1);
            }

            .dropdown-multiselect.open .dropdown-content label {
                display: flex;
                align-items: center;
                gap: 6px;
                padding: 3px 4px;
                cursor: pointer;
            }

            .dropdown-tools {
                display: flex;
                align-items: center;
                gap: 8px;
                width: 100%;
                border-top: 1px dashed #e5e7eb;
                border-bottom: 1px dashed #e5e7eb;
                background: #fafafa;
                padding: 6px;
            }

            .dropdown-tools .filter-search {
                flex: 1;
                border: 1px solid #e5e7eb;
                border-radius: 6px;
                padding: 6px 8px;
                font-size: 12px;
            }

            .dropdown-tools .tools-actions {
                display: flex;
                gap: 6px;
            }

            .dropdown-tools .tool-btn {
                border: 1px solid #e5e7eb;
                background: #fff;
                color: #374151;
                border-radius: 6px;
                padding: 6px 8px;
                font-size: 10px;
                cursor: pointer;
            }

            .dropdown-tools .tool-btn:hover {
                background: #f3f4f6;
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

                /* Make main filter menu align to right on mobile for better fit */
                .dropdown-menus {
                    right: 0;
                    left: auto;
                    padding: 6px;
                }

                .filter-grid {
                    grid-template-columns: 1fr;
                    height: 310px;
                    overflow-y: auto;
                }

                .filter-section,
                .filters-actions {
                    width: 350px;
                    font-size: 13px;
                }

                .dropdown-multiselect.open .dropdown-content {
                    position: initial;
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

                .dropdown-menus {
                    left: calc(100% - 385px);
                }

                .filter-grid {
                    grid-template-columns: 1fr;
                    height: 310px;
                    overflow-y: auto;
                }

                .filter-section,
                .filters-actions {
                    width: 350px;
                    font-size: 13px;
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

                .dropdown-menus {
                    left: calc(100% - 260px);
                }

                .filter-grid {
                    grid-template-columns: 1fr;
                    height: 310px;
                    overflow-y: auto;
                }

                .filter-section,
                .filters-actions {
                    width: 350px;
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

                /* keep icon-only on md */
                .search-bar {
                    width: 100%;
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

                .dropdown-menus {
                    left: calc(100% - 390px);
                }

                .search-bar input,
                .action-buttons-container button,
                .btns {
                    font-size: 13px;
                }
            }

            /* 1280px–1535px */
            @media (min-width: 1280px) and (max-width: 1535px) {
                .container {
                    max-width: 1400px;
                    padding: 24px;
                }

                .dropdown-menus {
                    left: calc(100% - 200px);
                }
            }
        </style>
    @endpush


    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

        <script>
            // Helpers for dropdown widgets (like in assigned dashboard)
            function toggleDropdown(id) {
                const el = document.getElementById(id);
                if (!el) return;

                // Close all other filter dropdowns first
                const allDropdowns = ['yearDropdown', 'standardDropdown', 'dimensionDropdown', 'workGroupDropdown',
                    'deptDropdown', 'typeDropdown', 'statusDropdown', 'statusEnvDropdown'
                ];
                allDropdowns.forEach(dropdownId => {
                    if (dropdownId !== id) {
                        const otherEl = document.getElementById(dropdownId);
                        if (otherEl) {
                            otherEl.classList.remove('open');
                        }
                    }
                });

                // Toggle the clicked dropdown
                el.classList.toggle('open');
            }

            function setupDropdownLabel(dropdownId, labelId, defaultText) {
                const root = document.getElementById(dropdownId);
                const label = document.getElementById(labelId);
                if (!root || !label) return;
                root.addEventListener('change', () => {
                    const checked = root.querySelectorAll('input.filter-option:checked');
                    if (checked.length === 0) {
                        label.textContent = defaultText;
                    } else {
                        label.textContent = `${defaultText} (${checked.length})`;
                    }
                });
            }
        </script>
        <script>
            // ไปหน้ารายละเอียดเมื่อคลิกแถว (ยกเว้นคลิก element ที่ควรคลิกเองอยู่แล้ว)
            $(document).on('click', '#myTable tbody tr[data-href]', function(e) {
                if ($(e.target).closest('a, button, input, select, textarea, label, [data-rowlink-ignore]').length) {
                    return; // อย่าพาไป link ถ้าคลิกสิ่งที่ interactive อยู่แล้ว
                }
                const url = this.dataset.href;
                if (url) window.location.href = url;
            });

            // รองรับ Enter/Space เพื่อเข้าหน้าใหม่ (accessibility)
            $(document).on('keydown', '#myTable tbody tr[data-href]', function(e) {
                const isEnter = e.key === 'Enter' || e.keyCode === 13;
                const isSpace = e.key === ' ' || e.keyCode === 32;
                if (isEnter || isSpace) {
                    e.preventDefault();
                    const url = this.dataset.href;
                    if (url) window.location.href = url;
                }
            });
        </script>

        <script>
            $(document).ready(function() {
                // Initialize DataTable
                let table = new DataTable('#myTable', {
                    // Remove default search box since we have a custom one
                    searching: true,
                    responsive: true,
                    autoWidth: false,
                    // Customize pagination and info text
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
                    // Adjust the DOM structure for Tailwind CSS compatibility
                    dom: '<"flex flex-col md:flex-row justify-between items-center p-3"<"flex"l>>' +
                        't' +
                        '<"flex flex-col md:flex-row justify-between items-center p-3"<"flex-1"i><"flex"p>>',

                });

                const defaultOrder = JSON.parse(JSON.stringify(table.order()));
                const sortButtonDefault = $('#sort-button span').text();
                const filterButtonDefault = $('#filter-button span').text();

                // Adjust columns to fill available width
                setTimeout(() => {
                    table.columns.adjust().draw(false);
                }, 0);
                $(window).on('resize', function() {
                    table.columns.adjust();
                });

                // Connect custom search box to DataTable with real-time search
                $('#custom-search').on('keyup input', function() {
                    table.search(this.value).draw();
                });

                // Clear search when input is empty
                $('#custom-search').on('search', function() {
                    if (this.value === '') {
                        table.search('').draw();
                    }
                });

                $('#add_indicator_button').on('click', function() {
                    window.location.href = "{{ route('indicator.create') }}";
                });

                // Sorting dropdown functionality
                $('#sort-button').on('click', function(e) {
                    e.stopPropagation();
                    $('#sort-dropdown').toggleClass('hidden');
                    $('#filter-dropdown').addClass('hidden'); // Close other dropdown
                    $('.dropdown-multiselect').removeClass('open'); // Close all filter section dropdowns
                });

                // Filter dropdown functionality
                $('#filter-button').on('click', function(e) {
                    e.stopPropagation();
                    $('#filter-dropdown').toggleClass('hidden');
                    $('#sort-dropdown').addClass('hidden'); // Close other dropdown
                    $('.dropdown-multiselect').removeClass('open'); // Close all filter section dropdowns
                });

                // Close dropdowns when clicking outside
                $(document).on('click', function(e) {
                    if (!$(e.target).closest(
                            '#sort-dropdown-container, #filter-dropdown-container, .dropdown-multiselect')
                        .length) {
                        $('#sort-dropdown, #filter-dropdown').addClass('hidden');
                        $('.dropdown-multiselect').removeClass('open');
                    }
                });

                // Close all filter sub-dropdowns when clicking inside the main filter dropdown area
                $('#filter-dropdown').on('click', function(e) {
                    // Only close sub-dropdowns if not clicking on a dropdown button or its content
                    if (!$(e.target).closest('.dropdown-multiselect').length) {
                        $('.dropdown-multiselect').removeClass('open');
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

                // Track active filters
                let activeFilters = {};

                // Handle filter checkboxes
                $('.filter-option').on('change', function() {
                    const columnKey = String($(this).data('column'));
                    const rawValue = $(this).data('value');
                    const value = rawValue === null || rawValue === undefined ? '' : String(rawValue).trim();

                    if (!activeFilters[columnKey]) {
                        activeFilters[columnKey] = [];
                    }

                    if ($(this).is(':checked')) {
                        if (value !== '' && !activeFilters[columnKey].includes(value)) {
                            activeFilters[columnKey].push(value);
                        }
                    } else {
                        activeFilters[columnKey] = activeFilters[columnKey].filter((v) => v !== value);
                        if (activeFilters[columnKey].length === 0) {
                            delete activeFilters[columnKey];
                        }
                    }
                });

                // helper: escape regex
                const escapeRegex = s => s.toString().replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                // helper: multiple exact choices
                const makeExactRegex = (values) => `^(?:${values.map(escapeRegex).join('|')})$`;

                                $('#apply-filters').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const columnCount = table.columns().count();

                    table.columns().every(function() {
                        this.search('');
                    });

                    let filterCount = 0;

                    Object.keys(activeFilters).forEach((columnKey) => {
                        const selections = activeFilters[columnKey];
                        if (!Array.isArray(selections) || selections.length === 0) {
                            return;
                        }

                        const colIdx = Number(columnKey);
                        if (Number.isNaN(colIdx) || colIdx < 0 || colIdx >= columnCount) {
                            return;
                        }

                        filterCount += selections.length;

                        if (colIdx === 6 || colIdx === 7) {
                            const regex = selections
                                .map((v) => escapeRegex(String(v)))
                                .join('|');
                            table.column(colIdx).search(regex, true, false);
                            return;
                        }

                        const regex = makeExactRegex(selections.map((v) => String(v)));
                        table.column(colIdx).search(regex, true, false);
                    });

                    const filterLabel = filterCount > 0
                        ? `${filterButtonDefault} (${filterCount})`
                        : filterButtonDefault;
                    $('#filter-button span').text(filterLabel);

                    table.draw();
                    $('#filter-dropdown').addClass('hidden');
                });



                // Clear filters button
                $('#clear-filters').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    $('.filter-option').each(function() {
                        if ($(this).is(':checked')) {
                            $(this).prop('checked', false).trigger('change');
                        }
                    });

                    activeFilters = {};

                    $('#filter-button span').text(filterButtonDefault);

                    table.columns().every(function() {
                        this.search('');
                    });
                    table.draw();

                    $('#filter-dropdown').addClass('hidden');

                    $('#year-label').text('เลือกปี');
                    $('#standard-label').text('เลือกมาตรฐาน');
                    $('#dimension-label').text('เลือกด้าน');
                    $('#workGroup-label').text('เลือกสังกัดงาน');
                    $('#dept-label').text('เลือกหน่วยงาน');
                    $('#type-label').text('เลือกประเภท');
                    $('#status-label').text('เลือกสถานะ');
                    if ($('#statusEnv-label').length) {
                        $('#statusEnv-label').text('เลือกสถานะเอกสาร');
                    }
                    if ($('#assigned-label').length) {
                        $('#assigned-label').text('เลือกการมอบหมาย');
                    }
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
        <script>
            // Initialize dropdown label counters on load
            document.addEventListener('DOMContentLoaded', function() {
                setupDropdownLabel('yearDropdown', 'year-label', 'เลือกปี');
                setupDropdownLabel('standardDropdown', 'standard-label', 'เลือกมาตรฐาน');
                setupDropdownLabel('dimensionDropdown', 'dimension-label', 'เลือกด้าน');
                setupDropdownLabel('workGroupDropdown', 'workGroup-label', 'เลือกสังกัดงาน');
                setupDropdownLabel('deptDropdown', 'dept-label', 'เลือกหน่วยงาน');
                setupDropdownLabel('typeDropdown', 'type-label', 'เลือกประเภท');
                setupDropdownLabel('statusDropdown', 'status-label', 'เลือกสถานะ');
                // setupDropdownLabel('assignedEnvDropdown', 'assignedEnv-label', 'เลือกการมอบหมายเอกสาร');
                setupDropdownLabel('statusEnvDropdown', 'statusEnv-label', 'เลือกสถานะเอกสาร');

                // Keyboard accessibility for dropdown buttons
                document.querySelectorAll('.dropdown-multiselect .dropdown-btn').forEach(btn => {
                    btn.setAttribute('role', 'button');
                    btn.setAttribute('tabindex', '0');
                    btn.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            const id = btn.parentElement.id;
                            toggleDropdown(id);
                        }
                    });
                });

                // Per-section search: live filter labels in the section
                $(document).on('input', '.dropdown-tools .filter-search', function() {
                    const tools = $(this).closest('.dropdown-tools');
                    const sectionId = tools.data('section');
                    const q = $(this).val().toString().toLowerCase();
                    const content = $('#' + sectionId + ' .dropdown-content');
                    content.find('label').each(function() {
                        const text = $(this).text().toLowerCase();
                        $(this).toggle(text.indexOf(q) !== -1);
                    });
                });

                // Select all / ล้างทั้งหมด in a section
                $(document).on('click', '.dropdown-tools [data-action] ', function() {
                    const action = $(this).data('action');
                    const tools = $(this).closest('.dropdown-tools');
                    const sectionId = tools.data('section');
                    const content = $('#' + sectionId + ' .dropdown-content');
                    const checkboxes = content.find('input.filter-option');

                    if (action === 'select-all') {
                        checkboxes.each(function() {
                            if (!$(this).is(':checked')) {
                                $(this).prop('checked', true).trigger('change');
                            }
                        });
                    } else if (action === 'clear-all') {
                        checkboxes.each(function() {
                            if ($(this).is(':checked')) {
                                $(this).prop('checked', false).trigger('change');
                            }
                        });
                    }

                    // Update label counters after bulk action
                        const btn = document.querySelector('#' + sectionId + ' .dropdown-btn span');
                        if (btn) {
                            const defaultText = btn.id === 'year-label' ? 'เลือกปี' :
                                btn.id === 'standard-label' ? 'เลือกมาตรฐาน' :
                                btn.id === 'dimension-label' ? 'เลือกด้าน' :
                                btn.id === 'workGroup-label' ? 'เลือกสังกัดงาน' :
                                btn.id === 'dept-label' ? 'เลือกหน่วยงาน' :
                                btn.id === 'type-label' ? 'เลือกประเภท' :
                                btn.id === 'status-label' ? 'เลือกสถานะ' :
                                btn.id === 'statusEnv-label' ? 'เลือกสถานะเอกสาร' : '';
                        const count = content.find('input.filter-option:checked').length;
                        btn.textContent = count > 0 ? `${defaultText} (${count})` : defaultText;
                    }
                });
            });
        </script>
    @endpush
