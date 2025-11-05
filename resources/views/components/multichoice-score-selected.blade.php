@props(['index' => 1, 'namePrefix' => null, 'showControls' => true, 'options' => []])

@php $prefix = $namePrefix ?? 'multiSelected[' . $index . ']'; @endphp

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-200 bg-white shadow-sm p-4 sm:p-5 md:p-6']) }}
    x-data="{
        labels: @js(array_values($options)),
        selected: checklistData?.required_items || [],
        score: checklistData?.score || ''
    }" 
    x-init="
        labels = (window.__criteriaTitles && window.__criteriaTitles.length) ? window.__criteriaTitles.slice() : labels;
        if (checklistData) {
            selected = Array.isArray(checklistData.required_items) ? checklistData.required_items : [];
            score = checklistData.score || '';
        }
    "
    @criteria-updated.window="
        labels = ($event.detail?.name ?? []);
        selected = selected.filter(v => v <= labels.length);
    ">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3 md:mb-4">
        <h3 class="text-base md:text-lg font-semibold text-slate-900">
            เกณฑ์ที่ <span x-text="sequence ?? {{ $index }}">{{ $index }}</span>
        </h3>
        @if ($showControls)
            <div class="flex items-center gap-2">
                <button type="button" class="p-1 text-red-500 hover:text-red-600 text-sm md:text-base"
                    @click="$dispatch('criteria-remove', { index: sequence ?? {{ $index }} })"
                    title="ลบรายการนี้">✖</button>
            </div>
        @endif
    </div>

    {{-- Sequence field --}}
    <input type="hidden" :id="(prefix || '{{ $prefix }}') + '_sequence'"
        :name="(prefix || '{{ $prefix }}') + '[sequence]'" :value="sequence ?? {{ $index }}"
        autocomplete="off">

    {{-- Hidden input for existing checklist ID --}}
    <template x-if="checklistData?.id">
        <input type="hidden" :id="(prefix || '{{ $prefix }}') + '_id'"
            :name="(prefix || '{{ $prefix }}') + '[id]'" :value="checklistData.id" autocomplete="off">
    </template>

    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="text-slate-900 font-semibold border-l-4 border-blue-500 pl-3">รายการเกณฑ์</div>
            <div class="flex items-center gap-2">
                <button type="button"
                    class="px-3 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs"
                    @click="selected = Array.from({ length: labels.length }, (_, i) => i + 1)">เลือกทั้งหมด</button>
                <button type="button"
                    class="px-3 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs"
                    @click="selected = []">ล้างทั้งหมด</button>
            </div>
        </div>

        <!-- Checkboxes -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
            <template x-for="(label, i) in labels" :key="i">
                <label
                    class="flex items-start gap-3 rounded-2xl border border-slate-200 px-4 py-3 bg-white hover:bg-slate-50 transition cursor-pointer"
                    :for="(prefix || '{{ $prefix }}') + '_checkbox_' + i">
                    <input type="checkbox" :id="(prefix || '{{ $prefix }}') + '_checkbox_' + i"
                        :name="(prefix || '{{ $prefix }}') + '[required_items][]'" :value="i + 1"
                        x-model="selected" autocomplete="off"
                        class="mt-1 h-4 w-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500 flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start gap-2">
                            <span
                                class="inline-flex items-center justify-center min-w-[24px] h-6 px-2 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold flex-shrink-0"
                                x-text="i + 1"></span>
                            <span class="text-slate-800 text-sm md:text-base truncate" x-text="label"
                                :title="label"></span>
                        </div>
                    </div>
                </label>
            </template>

            <template x-if="!labels.length">
                <div class="text-slate-500 text-sm">ยังไม่มีรายการเกณฑ์ — เพิ่มที่ "รายการเกณฑ์การพิจารณา"</div>
            </template>
        </div>

        <hr class="my-2 border-slate-200">

        <div class="space-y-2">
            <label :for="(prefix || '{{ $prefix }}') + '_score'"
                class="block text-sm font-medium text-slate-700">
                คะแนน <span class="text-red-500">*</span>
            </label>
            <input type="number" step="1" required 
                :id="(prefix || '{{ $prefix }}') + '_score'"
                :name="(prefix || '{{ $prefix }}') + '[score]'" 
                x-model="score"
                placeholder="กรุณากรอกคะแนนเป็นตัวเลข เช่น 0" 
                autocomplete="off"
                class="w-full rounded-xl border bg-white py-1 px-3 border-slate-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm md:text-base">
        </div>
    </div>

    {{ $slot }}
</div>
