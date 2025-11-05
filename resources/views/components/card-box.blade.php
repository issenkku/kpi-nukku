@props(['title' => '', 'icon' => ''])

<div
    {{-- hover:shadow-md transition --}}
    {{ $attributes->merge(['class' => 'bg-gray-100 rounded-2xl border border-slate-200 shadow-sm 
   p-4 sm:p-5 md:p-6 ']) }}>
    @if ($title)
        <div class="flex items-center gap-3 mb-3 md:mb-4">
            @if ($icon)
                <div class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center font-semibold text-base sm:text-lg">
                    {{ $icon }}
                </div>
            @endif
            <h2 class="text-sm sm:text-base font-semibold text-slate-900">{{ $title }}</h2>
        </div>
    @endif
    {{ $slot }}
</div>
