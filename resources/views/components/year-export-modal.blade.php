@props([
    'years' => [],
    'context' => 'year-export',
    'title' => 'เลือกปีที่ต้องการ Export',
    // Absolute URL to navigate on confirm; defaults to route('sar_reports.create')
    'url' => null,
])

@php
    $targetUrl = $url ?? route('sar_reports.create');
    $yearsList = collect($years)->filter()->unique()->sortDesc()->values();
@endphp

<x-modal :title="$title" size="sm" :context="$context">
    <x-slot:trigger>
        <button type="button"
            class="btn btn-success">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12" />
            </svg>
            <span class="hidden sm:inline">EXPORT</span>
            <span class="sm:hidden">EXP</span>
        </button>
    </x-slot:trigger>
    <div class="space-y-3 sm:space-y-4">
        <div>
            <label for="yearSelectGlobal" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">ปีที่ต้องการ
                Export</label>
            <select id="yearSelectGlobal" x-ref="year"
                class="w-full border rounded px-2 py-1 text-xs sm:text-sm focus:outline-none focus:ring focus:ring-green-200">
                <option value="">-- กรุณาเลือกปี --</option>
                @foreach ($yearsList as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <x-slot:footer>
        <div class="flex flex-col sm:flex-row justify-end gap-2">
            <button type="button"
                class="px-3 py-1 bg-gray-300 rounded text-xs sm:text-sm font-medium order-2 sm:order-1"
                @click="open = false">ยกเลิก</button>
            <button type="button"
                class="px-3 py-1 bg-blue-500 text-white rounded text-xs sm:text-sm font-medium order-1 sm:order-2"
                @click="const y = $refs.year.value; if (!y) { alert('กรุณาเลือกปี'); return; } open = false; window.location.href = '{{ $targetUrl }}' + ({{ Str::contains($targetUrl, '?') ? 'true' : 'false' }} ? '&' : '?') + 'year=' + y;">
                ยืนยัน
            </button>
        </div>
    </x-slot:footer>
</x-modal>
