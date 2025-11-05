@props([
    'years' => [],
    'standards' => [],
    'departments' => [],
    'collectors' => [],
    'dimensions' => [],
    'filters' => [],
    'action' => '#',
    'selectedYear' => null,
    'filterId' => 'filter-card',
    'formId' => 'filter-form',
    'title' => 'กรองข้อมูลการประเมิน',
    'showFields' => [
        'year' => true,
        'codes' => true,
        'standard' => true,
        'dimension' => true,
        'department' => true,
        'collector' => true,
        'type' => true,
    ],
])

<div id="{{ $filterId }}" class="filter-card card" style="display:none; margin-top:15px;">
    <h2 class="card-title">{{ $title }}</h2>

    <form id="{{ $formId }}" method="GET" action="{{ $action }}">
        <div class="form-grid">
            @if ($showFields['year'])
                <!-- ปีการประเมิน -->
                <div class="field">
                    <label for="filter-year">ปีการประเมิน</label>
                    <select id="filter-year" name="year" aria-label="กรองตามปี">
                        <option value="">ทั้งหมด</option>
                        @foreach ($years as $y)
                            <option value="{{ $y }}"
                                {{ (string) $selectedYear === (string) $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if ($showFields['codes'])
                <!-- Codes -->
                <div class="field">
                    <label for="filter-code">รหัสตัวบ่งชี้</label>
                    <select id="filter-code" name="code" aria-label="กรองตามรหัสตัวชี้วัด">
                        <option value="">ทั้งหมด</option>
                        @foreach ($filters['codes'] ?? [] as $code)
                            @php
                                $codeValue = is_object($code) ? $code->code ?? $code : $code;
                                $codeName = is_object($code) ? $code->name ?? ($code->code ?? $code) : $code;
                            @endphp
                            <option value="{{ $codeValue }}">
                                {{ $codeName }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif @if ($showFields['standard'])
                    <!-- มาตรฐานตัวบ่งชี้ -->
                    <div class="field">
                        <label for="filter-standard">มาตรฐานตัวบ่งชี้</label>
                        <select id="filter-standard" name="standard_id" aria-label="กรองตามมาตรฐาน">
                            <option value="">ทั้งหมด</option>
                            @foreach ($standards as $std)
                                @php
                                    if (is_object($std)) {
                                        $value = $std->name ?? ($std->id ?? '');
                                        $label = $std->name ?? ($std->id ?? '');
                                    } elseif (is_array($std)) {
                                        $value = $std['name'] ?? ($std['id'] ?? '');
                                        $label = $std['name'] ?? ($std['id'] ?? '');
                                    } else {
                                        $value = $std;
                                        $label = $std;
                                    }
                                @endphp
                                <option value="{{ $value }}">
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if ($showFields['dimension'])
                    <!-- ด้านตัวบ่งชี้ -->
                    <div class="field">
                        <label for="filter-dimension">ด้านตัวบ่งชี้</label>
                        <select id="filter-dimension" name="category_id" aria-label="กรองตามมิติ">
                            <option value="">ทั้งหมด</option>
                            @foreach ($dimensions as $dim)
                                @php
                                    if (is_object($dim)) {
                                        $value = $dim->name ?? ($dim->id ?? '');
                                        $label = $dim->name ?? ($dim->id ?? '');
                                    } elseif (is_array($dim)) {
                                        $value = $dim['name'] ?? ($dim['id'] ?? '');
                                        $label = $dim['name'] ?? ($dim['id'] ?? '');
                                    } else {
                                        $value = $dim;
                                        $label = $dim;
                                    }
                                @endphp
                                <option value="{{ $value }}">
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if ($showFields['department'])
                    <!-- หน่วยงานที่รับผิดชอบ -->
                    <div class="field">
                        <label for="filter-dept">หน่วยงานที่รับผิดชอบ</label>
                        <select id="filter-dept" name="dept_id">
                            <option value="">ทั้งหมด</option>
                            @foreach ($departments as $dept)
                                @php
                                    if (is_object($dept)) {
                                        $value = $dept->name ?? ($dept->id ?? '');
                                        $label = $dept->name ?? ($dept->id ?? '');
                                    } elseif (is_array($dept)) {
                                        $value = $dept['name'] ?? ($dept['id'] ?? '');
                                        $label = $dept['name'] ?? ($dept['id'] ?? '');
                                    } else {
                                        $value = $dept;
                                        $label = $dept;
                                    }
                                @endphp
                                <option value="{{ $value }}">
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if ($showFields['collector'])
                    <!-- ผู้รับผิดชอบในการรวบรวมข้อมูล -->
                    <div class="field">
                        <label for="filter-collector">ผู้รับผิดชอบในการรวบรวมข้อมูล</label>
                        <select id="filter-collector" name="collector" aria-label="กรองตามผู้รวบรวม">
                            <option value="">ทั้งหมด</option>
                            @foreach ($collectors as $col)
                                @php
                                    if (is_object($col)) {
                                        $value = $col->display_name ?? ($col->email ?? '');
                                        $label = $col->display_name ?? ($col->email ?? '-');
                                    } elseif (is_array($col)) {
                                        $value = $col['name'] ?? ($col['email'] ?? '');
                                        $label = $col['name'] ?? ($col['email'] ?? '-');
                                    } else {
                                        $value = $col;
                                        $label = $col;
                                    }
                                @endphp
                                <option value="{{ $value }}">
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if ($showFields['type'])
                    <!-- ประเภทตัวบ่งชี้ (new UI field; client-side filter) -->
                    <div class="field">
                        <label for="filter-type">ประเภทตัวบ่งชี้</label>
                        <select id="filter-type" name="type" aria-label="กรองตามประเภท">
                            <option value="">ทั้งหมด</option>
                            @foreach ($filters['types'] ?? [] as $type)
                                @php
                                    $typeValue = is_object($type) ? $type->type ?? $type : $type;
                                    $typeName = is_object($type) ? $type->name ?? ($type->type ?? $type) : $type;
                                @endphp
                                <option value="{{ $typeValue }}">
                                    {{ $typeName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
        </div>

        <div class="card-actions">
            <button type="button" id="reset-filters" class="btn btn-outline">ล้างค่า</button>
            <button type="button" id="apply-filters" class="btn btn-primary">กรองข้อมูล</button>
        </div>
    </form>
</div>

<!-- Custom scripts slot -->
@if (isset($slot) && $slot)
    {{ $slot }}
@endif

<script>
    // Filter Component Core JavaScript
    window.FilterComponent = {
        init: function(options = {}) {
            const defaults = {
                filterId: '{{ $filterId }}',
                formId: '{{ $formId }}',
                toggleId: 'toggle-filter',
                resetId: 'reset-filters',
                applyId: 'apply-filters',
                onApply: null,
                onReset: null
            };

            this.options = Object.assign(defaults, options);
            this.bindEvents();
        },

        bindEvents: function() {
            const self = this;

            // Toggle filter visibility
            const toggleEl = document.getElementById(this.options.toggleId);
            if (toggleEl) {
                toggleEl.addEventListener('change', function() {
                    const card = document.getElementById(self.options.filterId);
                    if (card) {
                        card.style.display = this.checked ? 'block' : 'none';
                    }
                });
            }

            // Prevent form submission
            const formEl = document.getElementById(this.options.formId);
            if (formEl) {
                formEl.addEventListener('submit', function(e) {
                    e.preventDefault();
                });
            }

            // Apply filters
            const applyEl = document.getElementById(this.options.applyId);
            if (applyEl) {
                applyEl.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (typeof self.options.onApply === 'function') {
                        self.options.onApply();
                    } else {
                        self.defaultApply();
                    }
                });
            }

            // Reset filters
            const resetEl = document.getElementById(this.options.resetId);
            if (resetEl) {
                resetEl.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (typeof self.options.onReset === 'function') {
                        self.options.onReset();
                    } else {
                        self.defaultReset();
                    }
                });
            }
        },

        defaultApply: function() {
            // Default apply logic - can be overridden
            console.log('Filter applied');
        },

        defaultReset: function() {
            // Reset all form fields
            const form = document.getElementById(this.options.formId);
            if (form) {
                const selects = form.querySelectorAll('select');
                selects.forEach(select => {
                    select.selectedIndex = 0;
                });
            }

            // Trigger apply after reset
            if (typeof this.options.onApply === 'function') {
                this.options.onApply();
            }
        },

        getFilterValues: function() {
            const form = document.getElementById(this.options.formId);
            const values = {};

            if (form) {
                const selects = form.querySelectorAll('select');
                selects.forEach(select => {
                    if (select.name && select.value) {
                        values[select.name] = select.value;
                    }
                });
            }

            return values;
        }
    };
</script>

<style>
    .card {
        background: var(--color-white);
        border-radius: 8px;
        ;
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        padding: 24px;
        border: 1px solid var(--color-gray-100);
        margin: 16px 0;
    }

    .card-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 18px;
    }

    .field label {
        display: block;
        font-size: 13px;
        color: var(--color-gray-700);
        margin-bottom: 6px;
        font-weight: 600
    }

    .field select {
        width: 100%;
        height: 40px;
        border: 1px solid var(--color-gray-200);
        border-radius: 10px;
        padding: 0 36px 0 12px;
        font-size: 14px;
        color: var(--color-gray-700);
        background: var(--color-white);
        outline: none;
        transition: box-shadow .2s, border-color .2s;
        appearance: none;
        cursor: pointer;
        background-image: linear-gradient(45deg, transparent 50%, var(--color-gray-400) 50%), linear-gradient(135deg, var(--color-gray-400) 50%, transparent 50%);
        background-position: calc(100% - 18px) 16px, calc(100% - 12px) 16px;
        background-size: 6px 6px, 6px 6px;
        background-repeat: no-repeat;
    }

    .field select:hover {
        border-color: var(--color-blue-500)
    }

    .field select:focus {
        border-color: var(--color-blue-500);
        box-shadow: 0 0 0 3px rgba(80, 162, 221, .15)
    }

    .field select:disabled {
        background: var(--color-gray-50);
        color: var(--color-gray-400);
        cursor: not-allowed
    }

    .btn {
        height: 40px;
        padding: 0 16px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        border: 1px solid transparent;
        transition: transform .05s ease, background .2s, border-color .2s, color .2s
    }

    .btn:active {
        transform: translateY(1px)
    }

    .btn-outline {
        background: var(--color-white);
        color: var(--color-blue-500);
        border-color: var(--color-blue-500)
    }

    .btn-outline:hover {
        background: var(--color-blue-50)
    }

    .btn-primary {
        background: var(--color-blue-500);
        color: var(--color-white)
    }

    .btn-primary:hover {
        background: var(--color-blue-700)
    }

    /* ===== Filter Card styles (from your snippet) ===== */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px 16px;
    }

    @media (max-width: 767px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
