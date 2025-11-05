@props(['prefix' => 'scoring', 'initial' => []])

@php
    // Existing props:
    // $prefix (e.g., 'scoring'), $initial (may contain resources/collections)

    // 1) Variables
    $initialVarsRaw = old($prefix . '.variables', $initial['variables'] ?? []);

    if ($initialVarsRaw instanceof \Illuminate\Http\Resources\Json\ResourceCollection) {
        $initialVarsRaw = $initialVarsRaw->jsonSerialize();
    } elseif ($initialVarsRaw instanceof \Illuminate\Support\Collection) {
        $initialVarsRaw = $initialVarsRaw->values()->toArray();
    } elseif (is_object($initialVarsRaw) && method_exists($initialVarsRaw, 'toArray')) {
        try {
            $initialVarsRaw = $initialVarsRaw->toArray(request());
        } catch (\Throwable $e) {
            $initialVarsRaw = $initialVarsRaw->toArray();
        }
    }

    // ensure plain, indexed array
    $initialVars = array_values((array) $initialVarsRaw);

    // 2) Condition / formula
    $initialCondition = old(
        $prefix . '.condition',
        old($prefix . '.formula', $initial['condition'] ?? ($initial['formula'] ?? '')),
    );

    // Check if we have existing formula data with ID (for updates)
    $hasExistingFormula = !empty($initial['condition']) && !empty($initial['formula_id'] ?? null);
    $formulaId = $initial['formula_id'] ?? null;
@endphp

<div x-data="{
    prefix: '{{ $prefix }}',
    vars: (@js($initialVars)).map(v => {
        const variable = {
            id: Date.now() + Math.random(),
            dbId: v.id ?? null,
            variable_name: v.variable_name ?? '',
            label_name: v.label_name ?? '',
            type: (v.type ?? 'defined'),
            value: ((v.type ?? 'defined') === 'defined') ? (v.value ?? '') : ''
        };

        const typePattern = new RegExp(`^${variable.type}_\\d+$`);
        if (!typePattern.test(variable.variable_name)) {
            variable.needsRename = true; // mark for post-init regeneration
        }
        return variable;
    }),
    condition: @js($initialCondition),

    // inputs for adding a new var
    newName: '',
    newLabel: '',
    newType: 'defined',
    newValue: '',
    
    // Validation errors
    labelError: '',
    outputError: '',

    get hasOutputVariable() {
        return this.vars.some(v => v.type === 'output');
    },

    generateVariableName(type) {
        const typePrefix = type || 'defined';
        const sameTypeVars = this.vars.filter(v => v.type === typePrefix);
        const nextNumber = sameTypeVars.length + 1;
        return `${typePrefix}_${nextNumber}`;
    },

    add() {
        // Reset errors
        this.labelError = '';
        this.outputError = '';
        
        const label = (this.newLabel || '').trim();
        if (!label) { 
            this.labelError = 'กรุณากรอกป้ายชื่อ (label_name)';
            this.$nextTick(() => this.$refs.newLabel?.focus());
            return; 
        }
        if (this.newType === 'output' && this.hasOutputVariable) {
            this.outputError = 'สามารถมีตัวแปรประเภท Output ได้เพียง 1 ตัวเท่านั้น';
            return;
        }

        const generatedName = this.generateVariableName(this.newType);
        this.vars = [...this.vars, {
            id: Date.now() + Math.random(),
            dbId: null,
            variable_name: generatedName,
            label_name: label,
            type: this.newType,
            value: this.newType === 'defined' ? (this.newValue ?? '') : ''
        }];

        this.newName = '';
        this.newLabel = '';
        this.newType = 'defined';
        this.newValue = '';
        this.$nextTick(() => this.$refs.newLabel?.focus());
    },

    remove(i) {
        const a = [...this.vars];
        a.splice(i, 1);
        this.vars = a;
        this.regenerateVariableNames();
    },

    regenerateVariableNames() {
        const typeCounters = {};
        this.vars.forEach(v => {
            const type = v.type || 'defined';
            typeCounters[type] = (typeCounters[type] || 0) + 1;
            v.variable_name = `${type}_${typeCounters[type]}`;
        });
    },

    initializeVariableNames() {
        let hasChanged = false;
        this.vars.forEach(v => {
            if (v.needsRename) { hasChanged = true;
                delete v.needsRename; }
        });
        if (hasChanged) this.regenerateVariableNames();
    },

    insert(text) {
        const el = this.$refs.condition;
        const s = el.selectionStart ?? el.value.length;
        const e = el.selectionEnd ?? el.value.length;
        const before = el.value.slice(0, s),
            after = el.value.slice(e);
        this.condition = before + text + after;
        this.$nextTick(() => { el.focus(); const pos = s + text.length;
            el.setSelectionRange(pos, pos); });
    },

    reserved() { return new Set(['IF', 'AND', 'OR', 'NOR', 'XOR', 'XNOR', 'NAND', 'NOT', 'TRUE', 'FALSE', 'ELSE', 'THEN']); },
    extractVars(text) {
        const tokens = (text || '').match(/[A-Za-z_][A-Za-z0-9_]*/g) || [];
        const set = new Set(),
            r = this.reserved();
        tokens.forEach(t => { if (!r.has(t) && isNaN(Number(t))) set.add(t); });
        return Array.from(set);
    },
    get declaredVars() { return this.vars.map(v => (v.variable_name || '').trim()).filter(Boolean); },
    get referencedVars() { return this.extractVars(this.condition); },
    get unknownVars() { const d = new Set(this.declaredVars); return this.referencedVars.filter(n => !d.has(n)); },

    initSubmitGuard() {
        const form = this.$el.closest('form');
        if (!form) return;
        form.addEventListener('submit', (e) => {
            if (this.unknownVars.length) { 
                e.preventDefault();
                // Scroll to condition textarea
                this.$refs.condition?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                this.$refs.condition?.focus();
            }
        });
    }
}" x-init="initSubmitGuard();
initializeVariableNames();" class="space-y-5">

    {{-- ROW: Add Variable (mobile-first, stacks; spreads at md+) --}}
    <div class="grid grid-cols-1 gap-3 md:grid-cols-[1fr,minmax(200px,380px),auto] md:items-center">
        <p class="block text-slate-800 font-medium">สร้างตัวแปร</p>

        <div class="flex flex-col gap-2 min-w-0">
            <div class="flex flex-col gap-1">
        <input x-ref="newLabel" aria-label="ป้ายแปรผัน"
                    x-model="newLabel" 
                    type="text" 
                    id="variable_new_label"
                    name="variable_new_label"
                    placeholder="ป้ายชื่อ (เช่น statA, input1)"
                    autocomplete="off"
                    :class="labelError ? 'border-red-500 focus:border-red-500 focus:ring-red-200' : 'border-slate-300 hover:border-blue-400'"
                    class="p-2 w-full bg-white rounded-xl border placeholder-slate-400 text-sm md:text-base transition"
                    @input="labelError = ''" />
                <div x-show="labelError" 
                    x-transition
                    class="text-red-600 text-xs mt-1 flex items-center gap-1">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span x-text="labelError"></span>
                </div>
            </div>

            {{-- Type + (Value when defined) — inline on md+, stacked on mobile --}}
            <div class="flex flex-col sm:flex-row gap-2">
                <div class="flex flex-col gap-1 w-full sm:w-48">
        <select x-model="newType" aria-label="ชนิดแปรผัน"
                        id="variable_new_type"
                        name="variable_new_type"
                        autocomplete="off"
                        :class="outputError ? 'border-red-500 focus:border-red-500 focus:ring-red-200' : 'border-slate-300 hover:border-blue-400'"
                        class="p-2 w-full bg-white rounded-xl border text-sm md:text-base transition"
                        @change="outputError = ''">
                        <option value="defined">Defined</option>
                        <option value="output" x-show="!hasOutputVariable || newType === 'output'">Output</option>
                        <option value="input">Input</option>
                    </select>
                    <div x-show="outputError" 
                        x-transition
                        class="text-red-600 text-xs mt-1 flex items-center gap-1">
                        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <span x-text="outputError"></span>
                    </div>
                </div>

                <template x-if="newType === 'defined'">
        <input x-model="newValue" aria-label="ค่าตั้งต้น"
                        type="number" 
                        inputmode="decimal" 
                        id="variable_new_value"
                        name="variable_new_value"
                        placeholder="ค่า"
                        autocomplete="off"
                        class="p-2 w-full bg-white rounded-xl border border-slate-300 text-sm md:text-base hover:border-blue-400 transition">
                </template>
            </div>
        </div>

        <button type="button" @click="add()"
            class="btn btn-outline">
            เพิ่มตัวแปร <span class="text-xl leading-none">＋</span>
        </button>
    </div>

    {{-- LIST: Variables (consistent layout with defined widths) --}}
    <template x-for="(v, i) in vars" :key="v.id">
        <div class="bg-white rounded-2xl border border-slate-200 p-4">
            {{-- Mobile: Stack vertically --}}
            <div class="flex flex-col gap-3 md:hidden">
                <div class="flex flex-col gap-1">
                    <div class="text-xs font-medium text-slate-500">Variable Name (Auto-generated)</div>
                    <div class="text-blue-700 font-semibold text-sm px-3 py-2 bg-blue-50 rounded-lg" x-text="v.variable_name"></div>
                </div>

                <div class="flex flex-col gap-1">
                    <label :for="`${prefix}_var_${i}_label_mobile`" class="text-xs font-medium text-slate-700">Label Name</label>
        <input x-model="v.label_name" aria-label="ชื่อแสดงผล"
                        :id="`${prefix}_var_${i}_label_mobile`"
                        :name="`${prefix}_var_${i}_label_display`"
                        autocomplete="off"
                        class="w-full rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 text-slate-700 text-sm p-2"
                        placeholder="ป้ายชื่อ" />
                </div>

                <div class="flex items-center gap-3">
                    <div class="flex-1">
                        <div class="text-xs font-medium text-slate-500 mb-1">Type</div>
                        <div class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium"
                            :class="{
                                'bg-purple-100 text-purple-700': v.type === 'defined',
                                'bg-blue-100 text-blue-700': v.type === 'input',
                                'bg-green-100 text-green-700': v.type === 'output'
                            }"
                            x-text="v.type === 'defined' ? 'Defined' : (v.type === 'input' ? 'Input' : 'Output')">
                        </div>
                    </div>

                    <div class="flex-1" x-show="v.type === 'defined'">
                        <label :for="`${prefix}_var_${i}_value_mobile`" class="text-xs font-medium text-slate-700 block mb-1">Value</label>
        <input x-model.number="v.value" aria-label="ค่า"
                            type="number" 
                            inputmode="decimal"
                            :id="`${prefix}_var_${i}_value_mobile`"
                            :name="`${prefix}_var_${i}_value_display`"
                            autocomplete="off"
                            class="w-full rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 text-sm p-2" />
                    </div>

                    <button type="button" @click="remove(i)" 
                        class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition"
                        aria-label="ลบตัวแปร" title="ลบตัวแปร">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="text-xs text-slate-400" x-show="v.type === 'input'">
                    ℹ️ ค่านี้จะถูกกรอกโดยผู้ใช้
                </div>
                <div class="text-xs text-slate-400" x-show="v.type === 'output'">
                    ℹ️ ค่านี้จะถูกคำนวณจากสูตร
                </div>
            </div>

            {{-- Desktop: Grid layout with consistent columns --}}
            <div class="hidden md:grid md:grid-cols-[180px_1fr_100px_140px_40px] md:gap-4 md:items-center">
                {{-- Variable Name --}}
                <div class="flex flex-col gap-1">
                    <div class="text-xs font-medium text-slate-500">Variable Name</div>
                    <div class="text-blue-700 font-semibold text-sm px-3 py-2 bg-blue-50 rounded-lg truncate" 
                        x-text="v.variable_name" 
                        :title="v.variable_name"></div>
                </div>

                {{-- Label Name --}}
                <div class="flex flex-col gap-1">
                    <label :for="`${prefix}_var_${i}_label_desktop`" class="text-xs font-medium text-slate-700">Label Name</label>
        <input x-model="v.label_name" aria-label="ชื่อแสดงผล"
                        :id="`${prefix}_var_${i}_label_desktop`"
                        :name="`${prefix}_var_${i}_label_display`"
                        autocomplete="off"
                        class="w-full rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 text-slate-700 text-sm p-2"
                        placeholder="ป้ายชื่อ" />
                </div>

                {{-- Type Badge --}}
                <div class="flex flex-col gap-1">
                    <div class="text-xs font-medium text-slate-500">Type</div>
                    <div class="inline-flex items-center justify-center px-3 py-1.5 rounded-full text-xs font-medium"
                        :class="{
                            'bg-purple-100 text-purple-700': v.type === 'defined',
                            'bg-blue-100 text-blue-700': v.type === 'input',
                            'bg-green-100 text-green-700': v.type === 'output'
                        }"
                        x-text="v.type === 'defined' ? 'Defined' : (v.type === 'input' ? 'Input' : 'Output')">
                    </div>
                </div>

                {{-- Value / Info --}}
                <div class="flex flex-col gap-1">
                    <div x-show="v.type === 'defined'" class="flex flex-col gap-1">
                        <label :for="`${prefix}_var_${i}_value_desktop`" class="text-xs font-medium text-slate-700">Value</label>
        <input x-model.number="v.value" aria-label="ค่า"
                            type="number" 
                            inputmode="decimal"
                            :id="`${prefix}_var_${i}_value_desktop`"
                            :name="`${prefix}_var_${i}_value_display`"
                            autocomplete="off"
                            class="w-full rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 text-sm p-2" />
                    </div>
                    <div x-show="v.type === 'input'" class="flex flex-col gap-1">
                        <div class="text-xs font-medium text-slate-500">Value</div>
                        <div class="text-slate-400 text-xs px-3 py-2 bg-slate-50 rounded-lg">ผู้ใช้กรอก</div>
                    </div>
                    <div x-show="v.type === 'output'" class="flex flex-col gap-1">
                        <div class="text-xs font-medium text-slate-500">Value</div>
                        <div class="text-slate-400 text-xs px-3 py-2 bg-slate-50 rounded-lg">คำนวณจากสูตร</div>
                    </div>
                </div>

                {{-- Delete Button --}}
                <div class="flex items-center justify-center">
                    <button type="button" @click="remove(i)" 
                        class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                        aria-label="ลบตัวแปร" title="ลบตัวแปร">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Hidden fields for POST --}}
            <input type="hidden" 
                :id="`${prefix}_variables_${i}_variable_name`"
                :name="`${prefix}[variables][${i}][variable_name]`" 
                :value="v.variable_name"
                autocomplete="off">
            <input type="hidden" 
                :id="`${prefix}_variables_${i}_label_name`"
                :name="`${prefix}[variables][${i}][label_name]`" 
                :value="v.label_name"
                autocomplete="off">
            <input type="hidden" 
                :id="`${prefix}_variables_${i}_type`"
                :name="`${prefix}[variables][${i}][type]`" 
                :value="v.type"
                autocomplete="off">
            <input type="hidden" 
                :id="`${prefix}_variables_${i}_value`"
                :name="`${prefix}[variables][${i}][value]`" 
                :value="v.value ?? ''"
                autocomplete="off">
            <input type="hidden" 
                x-show="v.dbId"
                :id="`${prefix}_variables_${i}_id`"
                :name="`${prefix}[variables][${i}][id]`" 
                :value="v.dbId"
                autocomplete="off">
        </div>
    </template>

    {{-- CONDITION: Editor + helpers --}}
    <div class="space-y-3">
        <label for="{{ $prefix }}_condition" class="block text-slate-800 font-medium">สร้างเงื่อนไขการคำนวณ</label>

        <textarea x-ref="condition" aria-label="สูตรคำนวณ"
            x-model="condition" 
            rows="5"
            id="{{ $prefix }}_condition"
            name="{{ $prefix }}_condition_display"
            autocomplete="off"
            class="w-full bg-white rounded-2xl border border-blue-200 focus:border-blue-500 focus:ring-blue-500 p-3 text-sm md:text-base"
            placeholder="ตัวอย่าง: defined_1 * input_1"></textarea>

        <template x-if="unknownVars.length">
            <div class="rounded-xl border border-red-200 bg-red-50 text-red-700 p-3 text-sm">
                พบตัวแปรที่ยังไม่ได้ประกาศ: <span x-text="unknownVars.join(', ')"></span>
            </div>
        </template>
        <template x-if="!unknownVars.length && condition && condition.trim().length">
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 p-3 text-sm">
                ✓ ตัวแปรทั้งหมดที่ใช้งานในเงื่อนไขถูกประกาศแล้ว
            </div>
        </template>

        {{-- Toolbars become horizontally scrollable on small screens --}}
        <div class="space-y-2">
            <div class="text-slate-700 font-medium">เครื่องหมาย</div>
            <div class="flex gap-2 overflow-x-auto no-scrollbar py-1 -mx-1 px-1">
                <template x-for="op in ['+','-','*','/','<','>','<=','>=','==','!=','IF(']">
                    <button type="button" @click="insert(op)"
                        class="px-3 py-1 rounded-xl bg-white text-slate-800 hover:bg-blue-100 text-sm shrink-0">
                        <span x-text="op"></span>
                    </button>
                </template>
            </div>

            <div class="text-slate-700 font-medium mt-2">คำสั่ง</div>
            <div class="flex gap-2 overflow-x-auto no-scrollbar py-1 -mx-1 px-1">
                <template x-for="op in ['AND','OR','NOR','XOR','XNOR','NAND','NOT']">
                    <button type="button" @click="insert(op)"
                        class="px-3 py-1 rounded-xl bg-white text-slate-800 hover:bg-blue-100 text-sm shrink-0">
                        <span x-text="op"></span>
                    </button>
                </template>
            </div>

            <div class="text-slate-700 font-medium mt-2">ตัวแปร</div>
            <div class="flex gap-2 flex-wrap sm:flex-nowrap overflow-x-auto no-scrollbar py-1 -mx-1 px-1">
                <template x-for="(v, i) in vars" :key="'chip' + v.id">
                    <button type="button" @click="insert(v.variable_name)"
                        class="px-3 py-1 rounded-full bg-slate-300 hover:bg-slate-200 text-slate-800 text-sm shrink-0">
                        <span x-text="v.label_name || ('var' + (i+1))"></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- Hidden field for POST --}}
        <input type="hidden" 
            id="{{ $prefix }}_condition_hidden"
            :name="`${prefix}[condition]`" 
            :value="condition"
            autocomplete="off">

        @if ($formulaId)
            <input type="hidden" 
                id="{{ $prefix }}_formula_id"
                name="{{ $prefix }}[formula_id]" 
                value="{{ $formulaId }}"
                autocomplete="off">
        @endif
    </div>
</div>

{{-- Optional: hide scrollbar utility (add once in a global CSS or layout) --}}
<style>
    /* Hide scrollbars on helper toolbars (keeps touch scroll) */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
