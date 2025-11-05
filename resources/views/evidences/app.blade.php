@extends('layouts.app')
@section('title', 'เอกสารและหลักฐาน')
@section('header', 'เอกสารและหลักฐาน')
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
                <input type="search" name="custom_search" id="custom-search" placeholder="ค้นหาเอกสารและหลักฐาน..."
                    aria-label="ค้นหาเอกสารและหลักฐาน" aria-controls="evidenceTable" autocomplete="off"
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
                                data-column="8" data-order="asc" role="menuitem">ปี (น้อยไปมาก)</button>
                            <button
                                class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                data-column="8" data-order="desc" role="menuitem">ปี (มากไปน้อย)</button>
                            <button
                                class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                data-column="1" data-order="asc" role="menuitem">ชื่อไฟล์ (A-Z)</button>
                            <button
                                class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                data-column="1" data-order="desc" role="menuitem">ชื่อไฟล์ (Z-A)</button>
                            <button
                                class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                data-column="3" data-order="asc" role="menuitem">ประเภทไฟล์ (A-Z)</button>
                            <button
                                class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                data-column="3" data-order="desc" role="menuitem">ประเภทไฟล์ (Z-A)</button>
                            <button id="clear-sort"
                                class="sort-option text-left block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">ล้างตัวเรียงลำดับ</button>
                        </div>
                    </div>
                </div>

                <!-- Filter Button with Dropdown -->
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
                                <h3 class="dropdown-title">ปี</h3>
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
                                                    data-action="select-all">เลือกทั้งหมด</button>
                                                <button type="button" class="tool-btn"
                                                    data-action="clear-all">ล้างทั้งหมด</button>
                                            </div>
                                        </div>
                                        @foreach ($years as $year)
                                            <label>
                                                <input type="checkbox" name="year[]" class="filter-option"
                                                    data-column="8" data-value="{{ $year }}">
                                                <span>{{ $year }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="filter-section">
                                {{-- Section: มาตรฐาน --}}
                                <h3 class="dropdown-title">มาตรฐาน</h3>
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
                                                    data-action="select-all">เลือกทั้งหมด</button>
                                                <button type="button" class="tool-btn"
                                                    data-action="clear-all">ล้างทั้งหมด</button>
                                            </div>
                                        </div>
                                        @foreach ($standards as $std)
                                            <label>
                                                <input type="checkbox" name="standard[]" class="filter-option"
                                                    data-column="9" data-value="{{ $std }}">
                                                <span>{{ $std }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="filter-section">
                                {{-- Section: ด้าน --}}
                                <h3 class="dropdown-title">ด้าน</h3>
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
                                                    data-action="select-all">เลือกทั้งหมด</button>
                                                <button type="button" class="tool-btn"
                                                    data-action="clear-all">ล้างทั้งหมด</button>
                                            </div>
                                        </div>
                                        @foreach ($dimensions as $dim)
                                            <label>
                                                <input type="checkbox" name="dimension[]" class="filter-option"
                                                    data-column="10" data-value="{{ $dim }}">
                                                <span>{{ $dim }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="filter-section">
                                {{-- Section: ผู้รับผิดชอบ --}}
                                <h3 class="dropdown-title">ผู้รับผิดชอบ</h3>
                                <div class="dropdown-multiselect" id="collectorDropdown">
                                    <div class="dropdown-btn" onclick="toggleDropdown('collectorDropdown')">
                                        <span id="collector-label">เลือกผู้รับผิดชอบ</span>
                                        <i class="fa-solid fa-caret-down"></i>
                                    </div>
                                    <div class="dropdown-content">
                                        <div class="dropdown-tools" data-section="collectorDropdown">
                                            <input type="text" name="collector_search" class="filter-search" aria-label="ค้นหาผู้รวบรวม"
                                                placeholder="ค้นหา..." aria-label="ค้นหาผู้รับผิดชอบ">
                                            <div class="tools-actions">
                                                <button type="button" class="tool-btn"
                                                    data-action="select-all">เลือกทั้งหมด</button>
                                                <button type="button" class="tool-btn"
                                                    data-action="clear-all">ล้างทั้งหมด</button>
                                            </div>
                                        </div>
                                        @foreach ($collectors as $collector)
                                            <label>
                                                <input type="checkbox" name="collector[]" class="filter-option"
                                                    data-column="5" data-value="{{ $collector }}">
                                                <span>{{ $collector }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="filter-section">
                                {{-- Section: หน่วยงาน --}}
                                <h3 class="dropdown-title">หน่วยงาน</h3>
                                <div class="dropdown-multiselect" id="deptDropdown">
                                    <div class="dropdown-btn" onclick="toggleDropdown('deptDropdown')">
                                        <span id="dept-label">เลือกหน่วยงาน</span>
                                        <i class="fa-solid fa-caret-down"></i>
                                    </div>
                                    <div class="dropdown-content">
                                        <div class="dropdown-tools" data-section="deptDropdown">
                                            <input type="text" name="dept_search" class="filter-search" aria-label="ค้นหาภาควิชา/หน่วยงาน"
                                                placeholder="ค้นหา..." aria-label="ค้นหาหน่วยงาน">
                                            <div class="tools-actions">
                                                <button type="button" class="tool-btn"
                                                    data-action="select-all">เลือกทั้งหมด</button>
                                                <button type="button" class="tool-btn"
                                                    data-action="clear-all">ล้างทั้งหมด</button>
                                            </div>
                                        </div>
                                        @foreach ($departments as $dept)
                                            <label>
                                                <input type="checkbox" name="dept[]" class="filter-option"
                                                    data-column="12" data-value="{{ $dept }}">
                                                <span>{{ $dept }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="filter-section">
                                {{-- Section: ประเภทไฟล์ --}}
                                <h3 class="dropdown-title">ประเภทไฟล์</h3>
                                <div class="dropdown-multiselect" id="typeDropdown">
                                    <div class="dropdown-btn" onclick="toggleDropdown('typeDropdown')">
                                        <span id="type-label">เลือกประเภทไฟล์</span>
                                        <i class="fa-solid fa-caret-down"></i>
                                    </div>
                                    <div class="dropdown-content">
                                        <div class="dropdown-tools" data-section="typeDropdown">
                                            <input type="text" name="type_search" class="filter-search" aria-label="ค้นหาประเภท"
                                                placeholder="ค้นหา..." aria-label="ค้นหาประเภทไฟล์">
                                            <div class="tools-actions">
                                                <button type="button" class="tool-btn"
                                                    data-action="select-all">เลือกทั้งหมด</button>
                                                <button type="button" class="tool-btn"
                                                    data-action="clear-all">ล้างทั้งหมด</button>
                                            </div>
                                        </div>
                                        @foreach ($fileTypes as $type)
                                            <label>
                                                <input type="checkbox" name="type[]" class="filter-option"
                                                    data-column="3" data-value="{{ $type }}">
                                                <span>{{ $type }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="filters-actions">
                            <button id="clear-filters" class="btn">ล้างตัวกรอง</button>
                            <button id="apply-filters" class="btn btn-primary">ใช้ตัวกรอง</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- ตารางเอกสารและหลักฐาน -->
    <div class="border border-gray-200 rounded-lg overflow-x-auto ">
        <table id="evidenceTable" class="w-full min-w-full overflow-x-auto">
            <thead>
                <tr>
                    <th class="w-fit text-xs sm:text-sm font-medium text-gray-900 cursor-pointer select-none hidden sm:table-cell"
                        title="ลำดับ">
                        <div class="flex items-center justify-center">ลำดับ</div>
                    </th> <!-- 0 -->
                    <th class="w-full text-xs sm:text-sm font-medium text-gray-900 cursor-pointer select-none"
                        title="ชื่อไฟล์">
                        <div class="flex items-center justify-start min-w-40 sm:min-w-56">ชื่อไฟล์</div>
                    </th> <!-- 1 -->
                    <th class="w-fit text-xs sm:text-sm font-medium text-gray-900 cursor-pointer select-none hidden md:table-cell"
                        title="ขนาดไฟล์">
                        <div class="flex items-center justify-center min-w-9">ขนาด</div>
                    </th> <!-- 2 -->
                    <th class="w-fit text-xs sm:text-sm font-medium text-gray-900 cursor-pointer select-none hidden lg:table-cell"
                        title="ประเภทไฟล์">
                        <div class="flex items-center justify-center min-w-11">ประเภท</div>
                    </th> <!-- 3 -->
                    <th class="w-fit text-xs sm:text-sm font-medium text-gray-900 cursor-pointer select-none hidden xl:table-cell"
                        title="วันที่อัปโหลด">
                        <div class="flex items-center justify-center min-w-28">วันที่อัปโหลด</div>
                    </th> <!-- 4 -->
                    <th class="w-fit text-xs sm:text-sm font-medium text-gray-900 cursor-pointer select-none hidden lg:table-cell"
                        title="ชื่อผู้อัปโหลด">
                        <div class="flex items-center justify-center min-w-32">ชื่อผู้อัปโหลด</div>
                    </th> <!-- 5 -->
                    <th class="w-fit text-xs sm:text-sm font-medium text-gray-900 cursor-pointer select-none"
                        title="ตัวบ่งชี้">
                        <div class="flex items-center justify-center min-w-40 sm:min-w-56">ตัวบ่งชี้</div>
                    </th> <!-- 6 -->
                    <th class="w-fit text-xs sm:text-sm font-medium text-gray-900 cursor-pointer select-none"
                        title="จัดการ">
                        <div class="flex items-center justify-center max-w-40">จัดการ</div>
                    </th> <!-- 7 -->

                    <!-- ✅ hidden columns -->
                    <th style="display:none;">ปี</th> <!-- 8 -->
                    <th style="display:none;">มาตรฐาน</th> <!-- 9 -->
                    <th style="display:none;">ด้าน</th> <!-- 10 -->
                    <th style="display:none;">สถานะ</th> <!-- 11 -->
                    <th style="display:none;">หน่วยงาน</th> <!-- 12 -->
                </tr>
            </thead>
            <tbody>
                @foreach ($evidences as $index => $evidence)
                    @php
                        $indicator = $evidence->criteria->indicator ?? null;
                    @endphp
                    <tr>
                        <td class="w-fit text-center text-xs sm:text-sm text-gray-700 hidden sm:table-cell">
                            {{ $index + 1 }}
                        </td>
                        <td class="text-xs sm:text-sm align-top w-1/4">
                            @php $previewUrl = evidence_preview_url($evidence); @endphp
                            @php $isOffice = in_array($evidence->type, ['doc','docx','xls','xlsx','ppt','pptx']); @endphp

                            @if ($previewUrl && !$isOffice)
                                <a href="{{ $previewUrl }}" target="_blank" rel="noopener noreferrer"
                                    class="block max-w-56 text-left truncate">
                                    <div class="file-info flex space-x-2 items-start text-left h-auto">
                                        <div class="file-icon w-fit h-fit">
                                            @if ($evidence->type === 'pdf')
                                                <i data-lucide="file-text"
                                                    style="width:20px; height:20px; color:#dc2626;"></i>
                                            @elseif (in_array($evidence->type, ['doc', 'docx']))
                                                <i data-lucide="file-text"
                                                    style="width:20px; height:20px; color:#2563eb;"></i>
                                            @elseif (in_array($evidence->type, ['ppt', 'pptx']))
                                                <i data-lucide="file-text"
                                                    style="width:20px; height:20px; color:#eb7e25;"></i>
                                            @elseif (in_array($evidence->type, ['jpg', 'jpeg', 'png', 'gif', 'svg']))
                                                <i data-lucide="image"
                                                    style="width:20px; height:20px; color:#16a34a;"></i>
                                            @elseif (in_array($evidence->type, ['xls', 'xlsx']))
                                                <i data-lucide="file-spreadsheet"
                                                    style="width:20px; height:20px; color:#059669;"></i>
                                            @elseif ($evidence->type === 'url')
                                                <i data-lucide="link" style="width:20px; height:20px; color:#9333ea;"></i>
                                            @else
                                                <i data-lucide="file" style="width:20px; height:20px; color:#6b7280;"></i>
                                            @endif
                                        </div>
                                        <div class="file-details relative group">
                                            <div class="file-name text-blue-600 underline hover:text-red-600"
                                                title="{{ $evidence->name }}">
                                                {{ $evidence->name }}
                                            </div>
                                            @if ($evidence->detail)
                                                <div class="file-description text-sm text-gray-500"
                                                    title="{{ strip_tags($evidence->detail) }}">
                                                    {{ Str::limit(strip_tags($evidence->detail), 50) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @elseif($isOffice)
                                <div class="block w-full text-left rounded p-2 hover:bg-gray-50 cursor-not-allowed">
                                    <div class="file-info flex space-x-2 items-start">
                                        <div class="file-icon w-5 h-5 ">
                                            @if (in_array($evidence->type, ['doc', 'docx']))
                                                <i data-lucide="file-text"
                                                    style="width:20px; height:20px; color:#2563eb;"></i>
                                            @elseif (in_array($evidence->type, ['ppt', 'pptx']))
                                                <i data-lucide="file-text"
                                                    style="width:20px; height:20px; color:#eb7e25;"></i>
                                            @elseif (in_array($evidence->type, ['xls', 'xlsx']))
                                                <i data-lucide="file-spreadsheet"
                                                    style="width:20px; height:20px; color:#059669;"></i>
                                            @else
                                                <i data-lucide="file" style="width:20px; height:20px; color:#6b7280;"></i>
                                            @endif
                                        </div>
                                        <div class="file-details relative group">
                                            <div class="file-name text-gray-700 cursor-not-allowed hover:text-red-600"
                                                title="{{ $evidence->name }}">
                                                {{ $evidence->name }}
                                            </div>
                                            <div
                                                class="pointer-events-none absolute left-1/2 -translate-x-1/2 top-full mt-1 opacity-0 group-hover:opacity-100 w-max bg-gray-800 text-white text-xs rounded px-2 py-1 shadow z-50">
                                                ดาวน์โหลดเท่านั้น
                                            </div>

                                            @if ($evidence->detail)
                                                <div class="file-description text-sm text-gray-500"
                                                    title="{{ strip_tags($evidence->detail) }}">
                                                    {{ Str::limit(strip_tags($evidence->detail), 50) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-400">ไม่มีไฟล์</span>
                            @endif
                        </td>
                        <td class="text-center text-xs sm:text-sm text-gray-700 hidden md:table-cell">
                            {{ $evidence->total_size_human ?? '-' }}</td>
                        <td class="text-center text-xs sm:text-sm text-gray-700 hidden lg:table-cell"
                            data-search="{{ $evidence->type }}">
                            {{ $evidence->type }}</td>
                        <td class="text-center text-xs sm:text-sm text-gray-700 hidden xl:table-cell"
                            data-order="{{ optional($evidence->created_at)->timestamp }}">
                            {{ $evidence->created_at?->format('M d, Y') ?? '-' }}
                        </td>
                        <td class="text-xs sm:text-sm text-gray-700 text-center hidden lg:table-cell"
                            data-search="{{ optional($evidence->user)->display_name ?? '' }}">
                            {{ $evidence->user->display_name ?? '-' }}
                        </td>
                        @php
                            $is_assigned = (bool) ($indicator['is_assigned'] ?? false);
                            $rowUrl =
                                auth()->user() && auth()->user()->hasRole('user')
                                    ? route('dashboardkpi.user.show', [
                                        'id' => $indicator['id'],
                                        'is_assigned' => $is_assigned,
                                    ])
                                    : route('dashboardkpi.admin.show', [
                                        'id' => $indicator['id'],
                                        'is_assigned' => $is_assigned,
                                    ]);

                            $rowClass = $is_assigned ? 'assigned-row' : 'unassigned-row';
                        @endphp

                        <td data-search="{{ optional($indicator)->code ?? '' }}" class="relative group w-1/4">
                            <span onclick="window.location='{{ $rowUrl }}';"
                                title="ไปยังตัวบ่งชี้{{ $indicator->name }}"
                                class="text-xs text-pretty sm:text-sm text-gray-700 hover:text-red-600 cursor-pointer">{{ $indicator->name ?? '-' }}</span>
                        </td>


                        <td class="text-center w-fit">
                            {{-- <div class="evidence-actions"> --}}
                            @if ($evidence->type === 'url' && !empty($evidence->path['urls'][0]))
                                <button type="button"
                                    class="inline-flex items-center justify-center gap-1 px-2 py-1.5 sm:px-3 sm:py-2 text-xs sm:text-sm font-medium text-purple-700 bg-purple-100 border border-purple-200 rounded-md hover:bg-purple-200 hover:border-purple-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-1 transition-all duration-200 "
                                    onclick="window.open('{{ $evidence->path['urls'][0] }}', '_blank')"
                                    title="เปิดลิงก์ในแท็บใหม่">
                                    <i data-lucide="external-link" class="w-3 h-3 sm:w-4 sm:h-4 flex-shrink-0"></i>
                                    <span class="hidden sm:inline whitespace-nowrap">เปิดลิงก์</span>
                                    <span class="inline sm:hidden">เปิด</span>
                                </button>
                            @else
                                <button type="button"
                                    class="inline-flex items-center justify-center gap-1 px-2 py-1.5 sm:px-3 sm:py-2 text-xs sm:text-sm font-medium text-blue-700 bg-blue-100 border border-blue-200 rounded-md hover:bg-blue-200 hover:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition-all duration-200"
                                    onclick="window.location.href='{{ route('evidences.download', $evidence->id) }}'"
                                    title="ดาวน์โหลดไฟล์">
                                    <i data-lucide="download" class="w-3 h-3 sm:w-4 sm:h-4 flex-shrink-0"></i>
                                    <span class="hidden sm:inline whitespace-nowrap">ดาวน์โหลด</span>
                                    <span class="inline sm:hidden">โหลด</span>
                                </button>
                            @endif
                            {{-- </div> --}}
                        </td>

                        <!-- ✅ hidden values for filtering -->
                        <td style="display:none;">{{ $indicator->year ?? '' }}</td>
                        <td style="display:none;">{{ $indicator->category->standard->name ?? '' }}</td>
                        <td style="display:none;">{{ $indicator->category->name ?? '' }}</td>
                        <td style="display:none;">{{ $statusMap[$indicator->status ?? 0] ?? 'ไม่ทราบ' }}</td>
                        <td style="display:none;">
                            {{ $indicator->assignments->first()?->collectorUser?->department->name ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    </table>
    </div>
@endsection

@push('scripts')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables CSS -->

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.469.0/dist/umd/lucide.min.js"></script>

    <script>
        let table;

        // ฟังก์ชันดาวน์โหลดไฟล์
        function downloadFile(evidenceId) {
            // window.location.href = "{{ route('evidences.download', ':id') }}".replace(':id', evidenceId);
            window.location.href = '/evidences/' + evidenceId + '/download';
        }

        // Helpers for dropdown widgets
        function toggleDropdown(id) {
            const el = document.getElementById(id);
            if (!el) return;

            // Close all other filter dropdowns first
            const allDropdowns = ['yearDropdown', 'standardDropdown', 'dimensionDropdown', 'collectorDropdown',
                'deptDropdown', 'typeDropdown'
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
                const count = root.querySelectorAll('input.filter-option:checked').length;
                label.textContent = count > 0 ? `${defaultText} (${count})` : defaultText;
            });
        }

        $(function() {
            // --- DataTable init ---
            table = $('#evidenceTable').DataTable({
                searching: true,
                lengthChange: false,
                dom: 'rtip',
                order: [], // ไม่มี default sort
                stateSave: false, // ปิดจำสถานะ (กัน order เด้งกลับ)
                language: {
                    paginate: {
                        previous: 'ก่อนหน้า',
                        next: 'ถัดไป'
                    },
                    info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                    emptyTable: "ไม่พบข้อมูล",
                    zeroRecords: "ไม่พบข้อมูลที่ตรงกับการค้นหา"
                },
                // Adjust the DOM structure for Tailwind CSS compatibility (removed 'f' to disable built-in search)
                dom: 't' +
                    '<"flex flex-col md:flex-row justify-between items-center p-3"<"flex-1"i><"flex"p>>',
            });
            table.on('draw', function() {
                if (window.lucide?.createIcons) lucide.createIcons();
            });
            // --- Custom search ---
            let timer;
            $('#custom-search')
                .on('input', function() {
                    clearTimeout(timer);
                    const val = this.value;
                    timer = setTimeout(() => table.search(val).draw(), 150);
                })
                .on('search', function() {
                    if (this.value === '') table.search('').draw();
                });

            // --- Dropdown toggles ---
            $('#sort-button').on('click', function(e) {
                e.stopPropagation();
                $('#sort-dropdown').toggleClass('hidden');
                $('#filter-dropdown').addClass('hidden');
            });

            $('#filter-button').on('click', function(e) {
                e.stopPropagation();
                $('#filter-dropdown').toggleClass('hidden');
                $('#sort-dropdown').addClass('hidden');
            });

            // ปิด dropdown เมื่อคลิกข้างนอก
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#sort-dropdown-container, #filter-dropdown-container').length) {
                    $('#sort-dropdown, #filter-dropdown').addClass('hidden');
                }
            });

            // --- Sorting ---
            $('#sort-dropdown').on('click', '.sort-option', function(e) {
                e.stopPropagation();
                const col = Number($(this).data('column'));
                const order = String($(this).data('order'));
                table.order([
                    [col, order]
                ]).draw(false);

                $('#sort-button span').text('เรียงลำดับ: ' + $(this).text().trim());
                $('#sort-dropdown').addClass('hidden');
            });

            // ล้างการเรียงลำดับ
            $('#clear-sort').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                table.order([]).draw(false);
                table.order([
                    [0, 'asc']
                ]).draw(false);

                $('#sort-button span').text('เรียงลำดับ');
                $('#sort-dropdown').addClass('hidden');
            });

            // --- Filtering ---
            let activeFilters = {};

            $('.filter-option').on('change', function() {
                const column = String($(this).data('column'));
                const value = String($(this).data('value'));

                if (!activeFilters[column]) activeFilters[column] = [];
                if (this.checked) {
                    if (!activeFilters[column].includes(value)) activeFilters[column].push(value);
                } else {
                    activeFilters[column] = activeFilters[column].filter(v => v !== value);
                    if (activeFilters[column].length === 0) delete activeFilters[column];
                }
            });

            // ใช้ตัวกรอง
            $('#apply-filters').on('click', function() {
                table.columns().every(function() {
                    this.search('');
                });

                let filterCount = 0;

                for (const column in activeFilters) {
                    if (activeFilters[column].length > 0) {
                        filterCount += activeFilters[column].length;

                        const regex = activeFilters[column]
                            .map(v => v.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'))
                            .join('|');

                        table.column(Number(column)).search(regex, true, false);
                    }
                }

                $('#filter-button span').text(filterCount > 0 ? `กรองข้อมูล (${filterCount})` :
                    'กรองข้อมูล');
                table.draw();
                $('#filter-dropdown').addClass('hidden');
            });

            // ล้างตัวกรอง
            $('#clear-filters').on('click', function(e) {
                e.preventDefault();

                $('.filter-option').prop('checked', false);
                activeFilters = {};
                $('#filter-button span').text('กรองข้อมูล');

                // reset label ทั้ง 2 dropdown
                $('#type-label').text('เลือกประเภทไฟล์');
                $('#user-label').text('เลือกผู้ใช้งาน');
                $('#indicator-label').text('เลือกตัวบ่งชี้');

                table.columns().search('').draw();
            });


        });
    </script>
    <script>
        // Initialize dropdown label counters on load
        document.addEventListener('DOMContentLoaded', function() {
            setupDropdownLabel('yearDropdown', 'year-label', 'เลือกปี');
            setupDropdownLabel('standardDropdown', 'standard-label', 'เลือกมาตรฐาน');
            setupDropdownLabel('dimensionDropdown', 'dimension-label', 'เลือกด้าน');
            setupDropdownLabel('collectorDropdown', 'collector-label', 'เลือกผู้รับผิดชอบ');
            setupDropdownLabel('deptDropdown', 'dept-label', 'เลือกหน่วยงาน');
            setupDropdownLabel('typeDropdown', 'type-label', 'เลือกประเภทไฟล์');

            // Keyboard accessibility for dropdown buttons
            document.querySelectorAll('.dropdown-multiselect .dropdown-btn').forEach(btn => {
                btn.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
            });

            // Per-section search: live filter labels in the section
            $(document).on('input', '.dropdown-tools .filter-search', function() {
                const searchTerm = this.value.toLowerCase();
                const content = $(this).closest('.dropdown-content');
                content.find('label').each(function() {
                    const text = $(this).text().toLowerCase();
                    $(this).toggle(text.includes(searchTerm));
                });
            });

            // Select all / Clear all in a section
            $(document).on('click', '.dropdown-tools [data-action]', function() {
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
                const defaultText = tools.data('default-text') || 'เลือก';
                const btn = $('#' + sectionId + ' .dropdown-btn span');
                const count = content.find('input.filter-option:checked').length;
                btn.text(count > 0 ? `${defaultText} (${count})` : defaultText);
            });
        });
    </script>
@endpush

@push('styles')
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.dataTables.min.css"> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ================ Base / Datatable ================ */
        #evidenceTable,
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

        /* Force center alignment for specific headers */
        table.dataTable thead th.text-center {
            text-align: center !important;
        }

        /* Override DataTables default alignment */
        /* #evidenceTable thead th {
                        text-align: center !important;
                    } */

        /* Keep filename column left-aligned */
        /* #evidenceTable thead th:nth-child(2) {
                        text-align: left !important;
                    } */

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
        #evidenceTable tbody tr {
            transition: background-color .15s ease, transform .05s ease;
            /* cursor: pointer; */
        }

        #evidenceTable tbody tr:hover {
            background-color: #dbeafe !important;
        }

        table.dataTable tbody tr,
        #evidenceTable tbody tr:hover td {
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
            display: flex;
            flex-direction: column;
            position: absolute;
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

        /* ================ Action Buttons ================ */
        /* .evidence-actions {
                        display: flex;
                        gap: 6px;
                        flex-wrap: wrap;
                        justify-content: center;
                        align-items: center;

                    } */

        .btn-download,
        .btn-link,
        .btn-edit,
        .btn-delete {
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            background: #fff;
            cursor: pointer;
            font-size: 12px;
            white-space: nowrap;
            transition: all .2s ease;
            min-width: 70px;
        }

        .btn-link {
            color: #9333ea;
            background-color: #f3e8ff;
            border-color: #c084fc;
        }

        .btn-link:hover {
            background-color: #e9d5ff;
            border-color: #a855f7;
            transform: translateY(-1px);
        }

        .btn-download {
            color: #1d4ed8;
            background-color: #dbeafe;
            border-color: #60a5fa;
        }

        .btn-download:hover {
            background-color: #bfdbfe;
            border-color: #3b82f6;
            transform: translateY(-1px);
        }

        .btn-edit {
            color: #059669;
            background-color: #d1fae5;
            border-color: #34d399;
        }

        .btn-edit:hover {
            background-color: #a7f3d0;
            border-color: #10b981;
            transform: translateY(-1px);
        }

        .btn-delete {
            color: #dc2626;
            background-color: #fee2e2;
            border-color: #f87171;
        }

        .btn-delete:hover {
            background-color: #fecaca;
            border-color: #ef4444;
            transform: translateY(-1px);
        }

        /* Status badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 64px;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 500;
            line-height: 1.4;
            text-align: center;
            white-space: nowrap;
        }

        .status-completed {
            background: #dcfce7;
            color: #15803d;
        }

        .status-warning {
            background: #fef3c7;
            color: #b45309;
        }

        .status-error {
            background: #fee2e2;
            color: #b91c1c;
        }

        /* ================ Responsive ================ */
        /* < 640px */
        @media (max-width: 639px) {
            .container {
                padding: 8px;
            }

            .dataTables_wrapper,
            #evidenceTable_wrapper {
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
            .btns {
                font-size: 12px;
            }

            .dropdown-menus {
                right: 0;
                left: auto;
                padding: 6px;
            }

            .filter-grid {
                grid-template-columns: 1fr;
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

            .btn-download,
            .btn-link,
            .btn-edit,
            .btn-delete {
                padding: 4px 6px;
                font-size: 11px;
                min-width: 60px;
                gap: 2px;
            }

            .btn-download i,
            .btn-link i,
            .btn-edit i,
            .btn-delete i {
                width: 12px !important;
                height: 12px !important;
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
                flex-direction: column;
                gap: 16px;
                padding: 0 8px 16px;
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
                padding: 16px;
                max-width: 1200px;
            }

            table.dataTable thead th,
            table.dataTable tbody td {
                padding: 8px 5px;
            }

            .search-button-container {
                width: 90%;
            }

            .dropdown-menus {
                left: calc(100% - 300px);
            }

            .filter-grid {
                grid-template-columns: 1fr;
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
                padding: 20px;
                max-width: 1300px;
            }

            .dropdown-menus {
                left: calc(100% - 440px);
            }

            .search-bar input,
            .btns {
                font-size: 14px;
            }
        }

        /* 1280px–1535px */
        @media (min-width: 1280px) and (max-width: 1535px) {
            .container {
                max-width: 1400px !important;
            }

            .dropdown-menus {
                left: calc(100% - 200px);
            }
        }

        /* Hide DataTables default elements */
        .dataTables_length,
        .dataTables_filter {
            display: none;
        }

        /* Lucide icons */
        i[data-lucide] {
            display: inline-block;
            vertical-align: middle;
        }
    </style>
@endpush
