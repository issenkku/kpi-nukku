@props(['index' => 1, 'namePrefix' => null, 'showControls' => true])

@php $prefix = $namePrefix ?? 'criteria[' . $index . ']'; @endphp

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-200 bg-white shadow-sm p-4 sm:p-5 md:p-6']) }}>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3 md:mb-4">
        <h3 class="text-base md:text-lg font-semibold text-slate-900">
            ลำดับ <span x-text="sequence ?? {{ $index }}">{{ $index }}</span>
        </h3>
        @if ($showControls)
            <div class="flex items-center gap-2">
                <button type="button" class="p-1 text-slate-500 hover:text-slate-700 text-sm md:text-base"
                    @click="$dispatch('criteria-move-up',   { index: sequence ?? {{ $index }} })"
                    name="ย้ายขึ้น">▲</button>
                <button type="button" class="p-1 text-slate-500 hover:text-slate-700 text-sm md:text-base"
                    @click="$dispatch('criteria-move-down', { index: sequence ?? {{ $index }} })"
                    name="ย้ายลง">▼</button>
                <button type="button" class="p-1 text-red-500 hover:text-red-600 text-sm md:text-base"
                    @click="$dispatch('criteria-remove',    { index: sequence ?? {{ $index }} })"
                    name="ลบรายการนี้">✖</button>
            </div>
        @endif
    </div>

    <input type="hidden" 
        :id="(prefix || '{{ $prefix }}') + '_sequence'"
        :name="(prefix || '{{ $prefix }}') + '[sequence]'"
        :value="sequence ?? {{ $index }}"
        autocomplete="off">
    
    <!-- Hidden input for existing criteria ID -->
    <template x-if="criteriaData?.id">
        <input type="hidden" 
            :id="(prefix || '{{ $prefix }}') + '_id'"
            :name="(prefix || '{{ $prefix }}') + '[id]'" 
            :value="criteriaData.id"
            autocomplete="off">
    </template>

    <div class="space-y-4">
        <div>
            <label :for="(prefix || '{{ $prefix }}') + '_name'" class="block text-sm font-medium text-slate-700 mb-1">
                ชื่อเกณฑ์ <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                data-criteria-name 
                :id="(prefix || '{{ $prefix }}') + '_name'"
                :name="(prefix || '{{ $prefix }}') + '[name]'" 
                x-model="criteriaData.name"
                x-init="if (!criteriaData) criteriaData = { name: '', description: '', evidence_requirements: [], required_evidence_total: null }; if (!criteriaData.evidence_requirements) criteriaData.evidence_requirements = []"
                required
                placeholder="กรุณากรอกชื่อเกณฑ์"
                autocomplete="off"
                class="p-2 mt-1 w-full rounded-xl border border-slate-300 
        placeholder-slate-400 text-sm md:text-base 
        hover:shadow-md hover:border-blue-400 transition
                focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                @input.debounce.200ms="$dispatch('criteria-name-change', { idx: (sequence ?? {{ $index }}) - 1, name: $event.target.value })">
        </div>

        <div>
            <label :for="(prefix || '{{ $prefix }}') + '_description'" class="block text-sm font-medium text-slate-700 mb-1">
                รายละเอียด
            </label>
            <textarea rows="3" 
                :id="(prefix || '{{ $prefix }}') + '_description'"
                :name="(prefix || '{{ $prefix }}') + '[description]'"
                x-model="criteriaData.description"
                placeholder="กรุณากรอกรายละเอียด เช่น แสดงรายชื่ออาจารย์"
                autocomplete="off"
                class="p-2 mt-1 w-full rounded-xl border border-slate-300 
        placeholder-slate-400 text-sm md:text-base 
        hover:shadow-md hover:border-blue-400 transition
                focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500"
                @input.debounce.200ms="$dispatch('criteria-description-change', { idx: (sequence ?? {{ $index }}) - 1, description: $event.target.value })"></textarea>
        </div>
    </div>

    <div class="mt-4" x-data="{
        initRequirements() {
            if (!criteriaData) {
                criteriaData = { name: '', description: '', evidence_requirements: [], required_evidence_total: null };
            }
            if (!Array.isArray(criteriaData.evidence_requirements)) {
                criteriaData.evidence_requirements = [];
            }
            if (!criteriaData.evidence_requirements.length) {
                criteriaData.evidence_requirements = [{
                    id: null,
                    name: '',
                    sequence: 1
                }];
            } else {
                criteriaData.evidence_requirements = criteriaData.evidence_requirements.map((req, idx) => ({
                    id: req.id ?? null,
                    name: req.name ?? '',
                    sequence: req.sequence ?? (idx + 1)
                }));
            }
        },
        resequence() {
            criteriaData.evidence_requirements.forEach((req, idx) => {
                req.sequence = idx + 1;
            });
        },
        addRequirement() {
            criteriaData.evidence_requirements.push({
                id: null,
                name: '',
                sequence: criteriaData.evidence_requirements.length + 1
            });
        },
        removeRequirement(index) {
            if (criteriaData.evidence_requirements.length <= 1) {
                criteriaData.evidence_requirements = [{
                    id: null,
                    name: '',
                    sequence: 1
                }];
                return;
            }
            criteriaData.evidence_requirements.splice(index, 1);
            this.resequence();
        }
    }" x-init="initRequirements()">
        <div class="mb-2 text-sm font-medium text-slate-700">รายการหลักฐานที่ต้องส่ง</div>
        <div class="mb-3">
            <label class="block text-sm font-medium text-slate-700 mb-1">
                จำนวนหลักฐานที่ต้องมีทั้งหมดสำหรับเกณฑ์ย่อย
            </label>
            <input type="number"
                min="0"
                :name="(prefix || '{{ $prefix }}') + '[required_evidence_total]'"
                x-model.number="criteriaData.required_evidence_total"
                placeholder="เช่น 2"
                class="p-2 w-full rounded-xl border border-slate-300 placeholder-slate-400 text-sm md:text-base
                hover:shadow-md hover:border-blue-400 transition focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
            <div class="text-xs text-slate-500 mt-1">
                ปล่อยว่างหรือใส่ 0 เพื่อไม่จำกัดจำนวนรวม
            </div>
        </div>
        <div class="space-y-3">
            <template x-for="(req, rIdx) in criteriaData.evidence_requirements" :key="req.id ?? rIdx">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-end">
                    <input type="hidden"
                        :name="(prefix || '{{ $prefix }}') + '[evidence_requirements][' + rIdx + '][id]'"
                        x-model="req.id">
                    <input type="hidden"
                        :name="(prefix || '{{ $prefix }}') + '[evidence_requirements][' + rIdx + '][sequence]'"
                        x-model="req.sequence">
                    <div class="md:col-span-10">
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            ชื่อหลักฐาน
                        </label>
                        <input type="text"
                            :name="(prefix || '{{ $prefix }}') + '[evidence_requirements][' + rIdx + '][name]'"
                            x-model="req.name"
                            placeholder="กรุณากรอกชื่อหลักฐาน"
                            autocomplete="off"
                            class="p-2 w-full rounded-xl border border-slate-300 placeholder-slate-400 text-sm md:text-base
                            hover:shadow-md hover:border-blue-400 transition focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                    </div>
                    <div class="md:col-span-2 flex md:justify-end">
                        <button type="button" class="btn btn-outline text-sm" @click="removeRequirement(rIdx)">
                            ลบ
                        </button>
                    </div>
                </div>
            </template>
        </div>
        <div class="pt-3">
            <button type="button" class="btn btn-outline" @click="addRequirement()">
                เพิ่มรายการหลักฐาน
            </button>
        </div>
    </div>

    {{ $slot }}
</div>
