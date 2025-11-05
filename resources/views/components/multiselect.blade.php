@props([
    'name',
    'label' => '',
    'options' => [],
    'placeholder' => 'กรุณาเลือก',
    'required' => false,
    'optionValue' => 'id',
    'optionLabel' => 'name',
    'searchable' => true,
    'selectAll' => true,
    'showChips' => true,
    'max' => null,
    'maxChips' => 6,
    'listBelow' => false,
    'listBelowThreshold' => 6,
    'buttonHeight' => null,
    'chipsWrap' => true,
    'value' => [],
])

@php
    $oldValues = (array) old($name, $value);

    $raw = collect($options)
        ->map(function ($v, $k) use ($optionValue, $optionLabel) {
            if (is_array($v)) return $v;
            return [$optionValue => $k, $optionLabel => $v];
        })
        ->values();
@endphp

<div
    {{ $attributes->merge(['class' => 'relative w-full']) }}
    x-data="multiSelect({
        name: @js($name),
        required: @js($required),
        max: @js($max),
        searchable: @js($searchable),
        optionValue: @js($optionValue),
        optionLabel: @js($optionLabel),
        initialOptions: @js($raw),
        initialSelected: @js(array_map('strval', $oldValues)),
        placeholder: @js($placeholder),
        selectAllEnabled: @js($selectAll),
        maxChipCount: @js($maxChips),
        listBelow: @js($listBelow),
        listBelowThreshold: @js($listBelowThreshold),
        buttonHeight: @js($buttonHeight),
        chipsWrap: @js($chipsWrap),
    })"
    x-init="init(); hookFormValidation();"

    {{-- 🧩 NEW: ฟัง event จากภายนอก --}}
    @multiselect-update-options.window="
        if ($event.detail?.name === name) {
            setOptions($event.detail.options || []);
            setSelected($event.detail.keep || []);
            if ($event.detail?.open) {
                open = true;
                if (searchable) $nextTick(() => $refs.search?.focus());
            }
        }
    "
>
    <label class="block">
        @if ($label)
            <span id="{{ $name }}_label" class="text-sm font-medium text-slate-700">
                {{ $label }} @if ($required)
                    <span class="text-red-500">*</span>
                @endif
            </span>
        @endif

        <!-- Trigger -->
        <button type="button" x-ref="btn"
            @click="toggle()"
            @keydown.arrow-down.prevent="open=true; move(1)"
            @keydown.arrow-up.prevent="open=true; move(-1)"
            @keydown.enter.prevent="submitKey($event)"
            :style="btnStyle"
            aria-haspopup="listbox"
            :aria-expanded="open ? 'true' : 'false'"
            :aria-labelledby="'{{ $name }}_label'"
            :aria-controls="name + '_listbox'"
            @class([
                'relative p-2 pr-8 mt-1 w-full rounded-xl border text-left transition min-h-[42px]',
                'border-red-500 focus:border-red-500 focus:ring-red-200' => $errors->has($name),
                'border-slate-300 hover:shadow-md hover:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500' => !$errors->has($name),
            ])>
            <template x-if="!selectedOptions.length">
                <span class="block text-slate-400 truncate" x-text="placeholder"></span>
            </template>

            <template x-if="selectedOptions.length">
                <div class="text-slate-700 text-sm">
                    <span x-text="selectedOptions.length + ' รายการที่เลือก'"></span>
                </div>
            </template>

            <!-- caret -->
            <span class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 text-slate-400">
                <svg class="inline h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.25 8.29a.75.75 0 01-.02-1.06z"
                        clip-rule="evenodd" />
                </svg>
            </span>
        </button>
        <x-input-error :name="$name" />
    </label>

    <!-- Below list -->
    <template x-if="selectedOptions.length > 0">
        <div class="mt-2 border border-slate-200 bg-slate-50 rounded-xl p-3 max-h-48 overflow-y-auto">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-slate-700">รายการที่เลือก:</span>
                <button type="button" @click="clear()" class="text-xs text-slate-600 hover:text-red-600 underline">ล้างทั้งหมด</button>
            </div>
            <div class="flex flex-col gap-2">
                <template x-for="o in selectedOptions" :key="'below-' + o.value">
                    <div class="flex items-start justify-between gap-2 bg-white border border-slate-200 rounded-lg px-3 py-2 hover:bg-slate-50 transition-colors">
                        <span class="text-sm break-words flex-1" x-text="o.label"></span>
                        <button type="button" @click="toggleValue(o.value)" class="text-slate-500 hover:text-red-600 text-sm font-medium ml-2 flex-shrink-0">ลบ</button>
                    </div>
                </template>
            </div>
        </div>
    </template>

    <!-- Dropdown -->
    <div x-show="open" x-transition @click.outside="open=false"
        class="absolute z-50 left-0 right-0 w-full rounded-xl border border-slate-300 bg-white shadow-lg overflow-hidden"
        :style="{ top: '75px' }">

        <!-- Search + actions -->
        <div class="p-2 border-b border-slate-200 flex items-center gap-2" x-show="searchable || selectAllEnabled">
            <input x-show="searchable" aria-label="ค้นหา"
                x-ref="search" :id="name + '_search'" :name="name + '_search'"
                x-model="q"
                @keydown.arrow-down.prevent="move(1)"
                @keydown.arrow-up.prevent="move(-1)"
                @keydown.enter.prevent="submitKey($event)"
                type="text" autocomplete="off"
                class="p-2 mt-1 w-full bg-white rounded-xl border border-slate-300 placeholder-slate-400 text-sm md:text-base hover:shadow-md hover:border-blue-400 transition focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                placeholder="พิมพ์เพื่อค้นหา...">
            <div class="flex items-center gap-2">
                <button x-show="selectAllEnabled" type="button" class="text-xs text-slate-600 hover:text-slate-900" @click="selectAll()">เลือกทั้งหมด</button>
                <button type="button" class="text-xs text-slate-600 hover:text-slate-900" @click="clear()">ล้าง</button>
            </div>
        </div>

        <!-- Options -->
        <ul :id="name + '_listbox'" role="listbox" aria-multiselectable="true" class="max-h-64 overflow-auto py-1 overflow-x-hidden">
            <template x-for="(o, i) in filtered" :key="o.value">
                <li :ref="'opt-' + i" role="option" :aria-selected="isSelected(o.value) ? 'true' : 'false'"
                    @mouseenter="hi = i" @mouseleave="hi = -1"
                    class="px-3 py-2 cursor-pointer flex items-start gap-2"
                    :class="[(isSelected(o.value) ? 'bg-blue-50' : ''), (hi === i) ? 'bg-slate-50' : '']"
                    @click="toggleValue(o.value)">
                    <input type="checkbox" class="mt-0.5 rounded text-blue-600" :checked="isSelected(o.value)">
                    <span class="text-sm break-words leading-snug" x-text="o.label"></span>
                </li>
            </template>

            <template x-if="filtered.length === 0">
                <li class="px-3 py-2 text-slate-500">ไม่พบข้อมูลที่ค้นหา</li>
            </template>
        </ul>
    </div>

    <!-- Hidden inputs -->
    <template x-for="(val, idx) in selected" :key="val">
        <input type="hidden" :name="name + '[]'" :value="val" autocomplete="off">
    </template>
</div>

<script>
function multiSelect(config) {
    return {
        name: config.name,
        placeholder: config.placeholder || 'กรุณาเลือก',
        required: !!config.required,
        max: config.max ?? null,
        searchable: !!config.searchable,
        selectAllEnabled: !!config.selectAllEnabled,
        optionValue: config.optionValue || 'id',
        optionLabel: config.optionLabel || 'name',
        maxChipCount: config.maxChipCount ?? 6,
        listBelow: !!config.listBelow,
        listBelowThreshold: Number.isFinite(config.listBelowThreshold) ? config.listBelowThreshold : 6,
        buttonHeight: config.buttonHeight || null,
        chipsWrap: !!config.chipsWrap,

        // state
        open: false,
        q: '',
        hi: -1,
        base: [],
        selected: [],

        get btnStyle() {
            return this.buttonHeight ? `min-height: ${this.buttonHeight}` : '';
        },
        get options() {
            return this.base;
        },
        get filtered() {
            const t = this.q.trim().toLowerCase();
            if (!this.searchable || !t) return this.options;
            return this.options.filter(o => o.label.toLowerCase().includes(t));
        },
        get selectedOptions() {
            const set = new Set(this.selected);
            return this.options.filter(o => set.has(String(o.value)));
        },

        init() {
            this.setOptions(config.initialOptions || []);
            this.setSelected(config.initialSelected || []);
        },

        toggle() {
            this.open = !this.open;
            if (this.open && this.searchable) this.$nextTick(() => this.$refs.search?.focus());
        },

        normalize(arr) {
            return (arr || []).map(it => {
                const v = it[this.optionValue] ?? it.value ?? it.id;
                const l = it[this.optionLabel] ?? it.label ?? it.name ?? String(v);
                return { ...it, value: String(v), label: String(l) };
            });
        },

        setOptions(newOptions) {
            this.base = this.normalize(newOptions);
            const avail = new Set(this.base.map(o => String(o.value)));
            this.selected = this.selected.filter(v => avail.has(v));
            this.q = '';
            this.hi = -1;
            this.emitChange();
        },

    setSelected(values) {
    const avail = new Set(this.base.map(o => String(o.value)));
    const uniq = Array.from(new Set((values || []).map(String))).filter(v => avail.has(v));
    this.selected = this.max ? uniq.slice(0, this.max) : uniq;
    this.emitChange();
},

isSelected(val) {
    return this.selected.includes(String(val));
},

toggleValue(val) {
    const s = String(val);
    const idx = this.selected.indexOf(s);
    if (idx >= 0) this.selected.splice(idx, 1);
    else {
        if (this.max && this.selected.length >= this.max) return;
        this.selected.push(s);
    }
    this.emitChange();
},


        clear() {
            this.selected = [];
            this.q = '';
            this.hi = -1;
            this.emitChange();
        },

        selectAll() {
            const all = this.filtered.map(o => String(o.value));
            this.selected = this.max ? all.slice(0, this.max) : all;
            this.emitChange();
        },

        emitChange() {
            const payload = { name: this.name, values: this.selected.slice() };
            this.$dispatch('multiselect-change', payload);
            window.dispatchEvent(new CustomEvent('multiselect-change', { detail: payload }));
        },

        hookFormValidation() {
            const form = this.$root.closest('form');
            if (!form) return;
            form.addEventListener('submit', ev => {
                if (this.required && this.selected.length === 0) {
                    ev.preventDefault();
                    this.open = true;
                    this.$nextTick(() => this.$refs.btn?.focus());
                }
            });
        },
    };
}
</script>
