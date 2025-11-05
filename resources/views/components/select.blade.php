@props([
    'name',
    'label' => '',
    'options' => [],
    'placeholder' => 'กรุณาเลือก',
    'required' => false,
    'optionValue' => 'id',
    'optionLabel' => 'name',
    'searchable' => false, // แค่บอกให้โชว์ช่องค้นหาหรือไม่
    'value' => null, // รับค่าเริ่มต้นจากหน้าแก้ไข
])

@php
    $oldValue = old($name, $value); // ใช้ old value หรือค่าที่ส่งมา
    // ทำให้เป็น format เดียว [{value, label}]
    $norm = collect($options)
        ->map(function ($v, $k) use ($optionValue, $optionLabel) {
            // ถ้าเป็น object (Eloquent model หรือ stdClass)
            if (is_object($v)) {
                return [
                    'value' => $v->{$optionValue} ?? $v->id ?? $k,
                    'label' => $v->{$optionLabel} ?? $v->name ?? (string)$v
                ];
            }
            // ถ้าเป็น array
            if (is_array($v)) {
                return [
                    'value' => $v[$optionValue] ?? $k,
                    'label' => $v[$optionLabel] ?? $k
                ];
            }
            // ถ้าเป็น scalar (string, number)
            return ['value' => $k, 'label' => $v];
        })
        ->values();
@endphp

<div x-data="{
    // props
    required: @js($required),
    searchable: @js($searchable),

    // state
    open: false,
    q: '',
    value: @js($oldValue ?? ''),
    options: @js($norm),
    hi: -1,

    // getters
    get selected() { return this.options.find(o => String(o.value) === String(this.value)) || null },
    get filtered() {
        const t = this.q.trim().toLowerCase();
        if (!this.searchable || !t) return this.options;
        return this.options.filter(o => o.label.toLowerCase().includes(t));
    },

    // actions
    choose(o) {
        this.value = o.value;
        this.open = false;
        this.q = '';
        this.hi = -1;
    },
    clear() {
        this.value = '';
        this.q = '';
        this.hi = -1;
    },
    move(d) {
        const len = this.filtered.length;
        if (!len) return;
        this.hi = ((this.hi + d) % len + len) % len;
        this.$nextTick(() => this.$refs['opt-' + this.hi]?.scrollIntoView({ block: 'nearest' }));
    },
    submitKey(e) {
        if (this.open && e.key === 'Enter') {
            e.preventDefault();
            const o = this.filtered[this.hi] || this.filtered[0];
            if (o) this.choose(o);
        }
    },
    // client validation for required (เพราะ hidden input ไม่ถูก validate)
    hookFormValidation() {
        const form = this.$root.closest('form');
        if (!form) return;
        form.addEventListener('submit', (ev) => {
            if (this.required && (this.value === '' || this.value === null || this.value === undefined)) {
                ev.preventDefault();
                this.open = true;
                // โฟกัสไปที่ปุ่มเพื่อให้ผู้ใช้เห็นว่าต้องเลือกก่อน
                this.$nextTick(() => this.$refs.btn?.focus());
            }
        });
    }
}" x-init="hookFormValidation()" class="relative">
    <label class="block">
        @if ($label)
            <span class="text-sm font-medium text-slate-700">
                {{ $label }} @if ($required)
                    <span class="text-red-500">*</span>
                @endif
            </span>
        @endif

        <!-- ปุ่มเปิดปิด + แสดงค่าที่เลือก -->
        <button x-ref="btn" type="button" @click="open = !open; $nextTick(() => searchable && $refs.search?.focus())"
            @keydown.arrow-down.prevent="open=true; move(1)" @keydown.arrow-up.prevent="open=true; move(-1)"
            @keydown.enter.prevent="submitKey($event)"
            @class([
                'p-2 mt-1 w-full rounded-xl border text-left transition',
                'border-red-500 focus:border-red-500 focus:ring-red-200' => $errors->has($name),
                'border-slate-300 hover:shadow-md hover:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500' => !$errors->has($name),
            ])
            {{ $attributes }}>
            <span x-text="selected ? selected.label : '{{ $placeholder }}'"
                :class="selected ? '' : 'text-slate-400'"></span>
            <!-- caret -->
            <span class="float-right text-slate-400">
                <svg class="inline h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.25 8.29a.75.75 0 01-.02-1.06z"
                        clip-rule="evenodd" />
                </svg>
            </span>
        </button>
        <x-input-error :name="$name" />
    </label>

    <!-- แผงดรอปดาวน์ -->
    <div x-show="open" x-transition @click.outside="open=false"
        class="absolute z-50 left-0 right-0 mt-1 rounded-xl border border-slate-300 bg-white shadow-md overflow-hidden">
        <!-- กล่องค้นหา (โชว์เฉพาะเมื่อ searchable=true) -->
        @if ($searchable)
            <div class="p-2 border-b border-slate-200">
                <input x-ref="search" 
                    id="{{ $name }}_search" 
                    name="{{ $name }}_search"
                    x-model="q" 
                    @keydown.arrow-down.prevent="move(1)"
                    @keydown.arrow-up.prevent="move(-1)" 
                    @keydown.enter.prevent="submitKey($event)" 
                    type="text"
                    autocomplete="off"
                    class="p-2 mt-1 w-full bg-white rounded-xl border border-slate-300 
                placeholder-slate-400 text-sm md:text-base 
                hover:shadow-md hover:border-blue-400 transition
                focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                    placeholder="พิมพ์เพื่อค้นหา...">
            </div>
        @endif

        <!-- รายการตัวเลือก -->
        <ul class="max-h-60 overflow-auto py-1">
            <li class="px-3 py-2 text-slate-400 cursor-pointer hover:bg-slate-50" @click="clear()">{{ $placeholder }}
            </li>

            <template x-for="(o, i) in filtered" :key="o.value">
                <li :ref="'opt-' + i" @mouseenter="hi = i" @mouseleave="hi = -1" @click="choose(o)"
                    class="px-3 py-2 cursor-pointer"
                    :class="[
                        (selected && String(selected.value) === String(o.value)) ? 'bg-blue-50' : '',
                        (hi === i) ? 'bg-slate-50' : ''
                    ]"
                    x-text="o.label">
                </li>
            </template>

            <template x-if="filtered.length === 0">
                <li class="px-3 py-2 text-slate-500">ไม่พบข้อมูลที่ค้นหา</li>
            </template>
        </ul>
    </div>

    <!-- ค่าที่ส่งไปกับฟอร์ม (note: browser ไม่ validate hidden; เราเช็กด้วย Alpine แทน) -->
    <input type="hidden" id="{{ $name }}" name="{{ $name }}" :value="value" autocomplete="off">
</div>
