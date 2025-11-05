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
                x-init="if (!criteriaData) criteriaData = { name: '', description: '' }"
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

    {{ $slot }}
</div>
