{{-- Use a flat array at the top level by default --}}
{{-- Call with: <x-multichoice-score-count name-prefix="multiCounts" /> --}}
{{-- Optional: preload existing rules, e.g. [['count'=>1,'score'=>10], ...] --}}

@props([
    'namePrefix' => 'multiCounts',
    'initial' => [],
    'showControls' => true,
])

@php
    // Base prefix (string) used for the hidden inputs' name attributes
    $base = trim($namePrefix ?? 'multiCounts');
    // Support old input repopulation (after validation error) or given initial
    $initialRules = old($base, $initial);
@endphp

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-200 bg-white shadow-sm p-4 sm:p-5 md:p-6']) }}
    x-data="{
        rules: (@js(array_values($initialRules ?: [['count' => 1, 'score' => 0]]))).map(r => ({
            id: Date.now() + Math.random(),
            count: Number(r.count ?? 1),
            score: Number(r.score ?? 0),
        })),

        addRule() {
            const nextCount = (this.rules.at(-1)?.count ?? 0) + 1;
            this.rules = [
                ...this.rules,
                { id: Date.now() + Math.random(), count: nextCount, score: 0 }
            ];
        },

        removeRule(i) {
            const a = [...this.rules];
            a.splice(i, 1);
            this.rules = a.length ? a : [{ id: Date.now() + Math.random(), count: 1, score: 0 }];
        },
    }">
    <div class="space-y-3">
        <div class="text-slate-900 font-semibold border-l-4 border-blue-500 pl-3">
            กำหนดคะแนนตามจำนวนที่เลือก
        </div>

        <template x-for="(r, i) in rules" :key="r.id">
            <div class="space-y-2 sm:space-y-0 sm:grid sm:grid-cols-3 sm:items-center gap-3">
                {{-- จำนวนที่เลือก --}}
                <input type="number" aria-label="จำนวนรายการ"
                    min="0" 
                    step="1" 
                    x-model.number="r.count"
                    :id="'{{ $base }}' + '_count_' + i"
                    :name="'{{ $base }}' + '_count_' + i"
                    autocomplete="off"
                    class="rounded-xl border bg-white py-1 px-3 border-slate-300 focus:border-blue-500 focus:ring-blue-500 w-full text-sm md:text-base">

                {{-- คะแนนที่ได้ --}}
                <input type="number" aria-label="คะแนน"
                    step="1" 
                    x-model.number="r.score"
                    :id="'{{ $base }}' + '_score_' + i"
                    :name="'{{ $base }}' + '_score_' + i"
                    autocomplete="off"
                    class="rounded-xl border bg-white py-1 px-3 border-slate-300 focus:border-blue-500 focus:ring-blue-500 w-full text-sm md:text-base">

                <div class="flex sm:justify-end">
                    <button type="button" @click="removeRule(i)"
                        class="text-red-600 hover:underline px-3 py-2 text-sm md:text-base">ลบ</button>
                </div>

                {{-- Hidden fields for POST (flat array) --}}
                <input type="hidden" 
                    :id="'{{ $base }}' + '_count_hidden_' + i"
                    :name="'{{ $base }}' + `[${i}][count]`" 
                    :value="r.count"
                    autocomplete="off">
                <input type="hidden" 
                    :id="'{{ $base }}' + '_score_hidden_' + i"
                    :name="'{{ $base }}' + `[${i}][score]`" 
                    :value="r.score"
                    autocomplete="off">
            </div>
        </template>

        <div>
            <button type="button" @click="addRule()"
                class="btn btn-outline">
                เพิ่มเงื่อนไข <span class="text-xl leading-none">＋</span>
            </button>
        </div>
    </div>

    {{ $slot }}
</div>
