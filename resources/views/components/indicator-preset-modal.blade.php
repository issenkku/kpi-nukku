@props(['indicators', 'standards' => [], 'modalId' => 'preset-modal'])

<div id="{{ $modalId }}"
    class="fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-3 sm:p-6">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-4xl max-h-[85vh] overflow-auto p-5 relative">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold">จัดการ Preset ตัวชี้วัด</h2>
            <!-- ปุ่มปิด -->
            <button type="button" onclick="document.getElementById('{{ $modalId }}').classList.add('hidden')"
                class="btn btn-delete !w-fit !text-gray-500 hover:!text-red-500 btn btn-xs hover:!shadow-none">
                <i data-lucide="x"></i>
            </button>
        </div>

        <!-- ✅ Filter มาตรฐาน -->
        @php
            // Build standards from indicators when not passed in
            if (empty($standards)) {
                $standards = collect($indicators)
                    ->map(function ($i) {
                        return data_get($i, 'category.standard') ?? data_get($i, 'standard');
                    })
                    ->filter()
                    ->unique('id')
                    ->values();
            }

            // Build year options from indicators
            $years = collect($indicators)
                ->map(fn($i) => (int) data_get($i, 'year'))
                ->filter()
                ->unique()
                ->sortDesc()
                ->values();
        @endphp

        <div x-data="{ showFilters: false }" class="mb-4">
            <!-- ✅ Toggle Switch -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-start gap-2 sm:gap-0 mb-3">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input name="show-filters" type="checkbox" x-model="showFilters" class="sr-only peer">
                    <div
                        class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer dark:bg-gray-600
           peer-checked:bg-[#2196F3] transition-colors duration-300">


                    </div>
                    <div
                        class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full border border-gray-300
                       transition-transform duration-300 peer-checked:translate-x-5">
                    </div>
                </label>
                <span class="ml-3 text-sm font-medium text-gray-700">แสดงตัวกรอง</span>
            </div>

            <!-- ✅ ฟิลเตอร์ -->
            <div x-show="showFilters" x-transition
                class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-gray-50 p-3 sm:p-4 rounded-lg shadow">
                <div>
                    <label for="year-filter-{{ $modalId }}" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">กรองตามปี</label>
                    <select id="year-filter-{{ $modalId }}" aria-label="กรองตามปี"
                        class="w-full border rounded px-2 py-1 text-xs sm:text-sm focus:ring focus:ring-green-200">
                        <option value="">-- แสดงทั้งหมด --</option>
                        @foreach ($years as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="standard-filter-{{ $modalId }}" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">กรองตามมาตรฐาน</label>
                    <select id="standard-filter-{{ $modalId }}" aria-label="กรองตามมาตรฐาน"
                        class="w-full border rounded px-2 py-1 text-xs sm:text-sm focus:ring focus:ring-green-200">
                        <option value="">-- แสดงทั้งหมด --</option>
                        @foreach ($standards as $std)
                            <option value="{{ data_get($std, 'id') }}">{{ data_get($std, 'name') }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>


        <!-- ✅ Search Box -->
        <div class="mb-3">
            <input type="text" id="search-{{ $modalId }}" placeholder="ค้นหาตัวชี้วัด..."
                class="w-full border rounded px-2 py-1 text-xs sm:text-sm focus:ring focus:ring-green-200">
        </div>

        <!-- ✅ Select All -->
        <div class="flex items-center space-x-2 mb-3">
            <input type="checkbox" id="select-all-{{ $modalId }}" class="rounded border-gray-300" aria-label="เลือกทั้งหมด">
            <label for="select-all-{{ $modalId }}" class="text-xs sm:text-sm font-medium">เลือกทั้งหมด</label>
        </div>

        <!-- ✅ Check list Indicators -->
        <!-- ✅ Export + Import เป็น responsive grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
            <!-- Export Preset -->
            <form id="preset-export-form" method="GET" action="{{ route('indicator.export.bulk') }}"
                class="flex flex-col border rounded-lg p-3 sm:p-4">
                <div class="max-h-48 sm:max-h-64 overflow-y-auto border rounded p-2 mb-3 sm:mb-4 text-xs sm:text-sm"
                    id="indicator-list-{{ $modalId }}">
                    @php
                        $currentYear = now()->year;
                    @endphp
                    @foreach ($indicators as $ind)
                        <label class="flex items-center space-x-2 py-1 indicator-item"
                            data-standard="{{ data_get($ind, 'category.standard.id') ?? data_get($ind, 'standard.id') }}"
                            data-year="{{ data_get($ind, 'year') }}">
                            <input type="checkbox" name="ids[]" value="{{ data_get($ind, 'id') }}"
                                class="rounded border-gray-300">
                            <span>
                                {{ data_get($ind, 'code') }} - {{ data_get($ind, 'name') }}
                                ({{ data_get($ind, 'year') }})
                            </span>
                        </label>
                    @endforeach

                </div>
                <input type="hidden" name="year" id="hidden-year-{{ $modalId }}" value="">
                <button type="submit"
                    class="btn bg-blue-500 text-white py-2 rounded hover:bg-blue-600 text-xs sm:text-sm font-medium flex justify-center items-center">
                    นำออกข้อมูล
                </button>
            </form>

            <!-- Import Preset -->
            <form action="{{ route('indicator.import') }}" method="POST" enctype="multipart/form-data"
                class="flex flex-col border rounded-lg p-3 sm:p-4 space-y-3">
                @csrf
                <!-- เลือกไฟล์ -->
                <input type="file" name="preset_file" accept=".json" required
                    class="block w-full border rounded px-2 py-1 text-xs sm:text-sm focus:ring focus:ring-green-200">

                <!-- เลือกปี -->
                <label for="year" class="block text-xs sm:text-sm font-medium text-gray-700">ปีที่ต้องการนำเข้า</label>
                <input type="number" id="year" name="year" value="{{ now()->year }}" min="2000" max="2100"
                    class="block w-full border rounded px-2 py-1 text-xs sm:text-sm focus:ring focus:ring-green-200"
                    required>

                <button type="submit"
                    class="btn bg-green-500 text-white py-2 rounded hover:bg-green-600 text-xs sm:text-sm font-medium flex justify-center items-center">
                    นำเข้าข้อมูล
                </button>
            </form>

            <!-- Duplicate To Year -->
            <form id="preset-duplicate-form-{{ $modalId }}" method="POST"
                action="{{ route('indicator.duplicate') }}"
                class="flex flex-col border rounded-lg p-3 sm:p-4 space-y-3">
                @csrf
                <div>
                    <label for="target_year" class="block text-xs sm:text-sm font-medium text-gray-700 ">คัดลอกไปยังปี</label>
                    <input type="number" id="target_year" name="target_year" value="{{ now()->year }}" min="2000" max="2100"
                        class="block w-full border rounded px-2 py-1 text-xs sm:text-sm focus:ring focus:ring-green-200 "
                        required>
                </div>
                <div id="duplicate-ids-container-{{ $modalId }}"></div>

                <button type="button" id="duplicate-submit-{{ $modalId }}"
                    class="btn bg-indigo-500 text-white py-2 rounded hover:bg-indigo-600 text-xs sm:text-sm font-medium flex justify-center items-center">
                    คัดลอกข้อมูล
                </button>
            </form>
        </div>

    </div>
</div>

<!-- ✅ Script -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const modalId = @json($modalId);
        const selectAll = document.getElementById(`select-all-${modalId}`);
        const indicatorList = document.getElementById(`indicator-list-${modalId}`);
        const standardFilter = document.getElementById(`standard-filter-${modalId}`);
        const yearFilter = document.getElementById(`year-filter-${modalId}`);
        const searchBox = document.getElementById(`search-${modalId}`);
        const hiddenYear = document.getElementById(`hidden-year-${modalId}`);
        const duplicateForm = document.getElementById(`preset-duplicate-form-${modalId}`);
        const duplicateIdsContainer = document.getElementById(`duplicate-ids-container-${modalId}`);
        const duplicateSubmitBtn = document.getElementById(`duplicate-submit-${modalId}`);

        // ฟังก์ชันรีเฟรช filter
        function applyFilter() {
            const selectedStd = standardFilter?.value || "";
            const selectedYear = yearFilter?.value || "";
            const searchTerm = searchBox?.value.toLowerCase() || "";

            indicatorList.querySelectorAll(".indicator-item").forEach(item => {
                const matchesStandard = !selectedStd || item.dataset.standard === selectedStd;
                const matchesYear = !selectedYear || item.dataset.year === selectedYear;
                const text = item.innerText.toLowerCase();
                const matchesSearch = !searchTerm || text.includes(searchTerm);

                if (matchesStandard && matchesYear && matchesSearch) {
                    item.classList.remove("hidden");
                } else {
                    item.classList.add("hidden");
                }
            });
        }

        // ✅ เลือกทั้งหมด (เลือกเฉพาะที่มองเห็น)
        selectAll?.addEventListener("change", function() {
            indicatorList.querySelectorAll(".indicator-item:not(.hidden) input[type=checkbox]").forEach(
                cb => {
                    cb.checked = selectAll.checked;
                });
        });

        // ✅ กรองตามมาตรฐาน
        standardFilter?.addEventListener("change", applyFilter);
        yearFilter?.addEventListener("change", function() {
            if (hiddenYear) hiddenYear.value = yearFilter.value || "";
            applyFilter();
        });

        // ✅ ค้นหา
        searchBox?.addEventListener("input", applyFilter);

        // Duplicate: require at least one selected indicator
        duplicateSubmitBtn?.addEventListener('click', function() {
            if (!duplicateForm) return;
            // Clear previous ids
            if (duplicateIdsContainer) duplicateIdsContainer.innerHTML = '';

            const checked = indicatorList.querySelectorAll(
                "input[type=checkbox][name='ids[]']:checked");
            if (checked.length === 0) {
                alert('กรุณาเลือกตัวชี้วัดอย่างน้อย 1 รายการ');
                return;
            }
            checked.forEach((cb) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                duplicateIdsContainer.appendChild(input);
            });

            duplicateForm.submit();
        });
    });
</script>
