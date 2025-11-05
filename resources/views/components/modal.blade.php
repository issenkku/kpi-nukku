@props([
    'title' => null, // header text
    'size' => 'md', // sm | md | lg | xl | 2xl
    'closeOnBg' => true, // click backdrop to close
    'context' => null, // string to identify which modal opened
    'align' => 'center', // center | top
])

@php
    $panelWidth =
        [
            'sm' => 'max-w-sm sm:max-w-md',
            'md' => 'max-w-sm sm:max-w-md md:max-w-lg',
            'lg' => 'max-w-sm zsm:max-w-md md:max-w-lg lg:max-w-2xl',
            'xl' => 'max-w-sm sm:max-w-md md:max-w-lg lg:max-w-2xl xl:max-w-4xl',
            '2xl' => 'max-w-sm sm:max-w-md md:max-w-lg lg:max-w-2xl xl:max-w-4xl 2xl:max-w-6xl',
        ][$size] ?? 'max-w-sm sm:max-w-md md:max-w-lg';
    $panelFrame = 'bg-white border border-slate-200 shadow-xl flex flex-col overflow-hidden modal-panel';
    $containerAlign = match ($align) {
        'top' => 'items-start pt-6 sm:pt-10',
        default => 'items-center',
    };
@endphp

<div x-data="{ open: false }" x-init="$watch('open', v => {
    // lock page scroll while open
    document.documentElement.classList.toggle('modal-open', v);
    document.body.style.overflow = v ? 'hidden' : ''; // Prevent background scroll
    if (v) { $dispatch('modal:opened', { context: @js($context) }); }
})" @modal:close.window="open = false"
    @modal:open.window="(() => { const ctx = @js($context); const d = $event.detail || {}; if (!ctx || d.context === ctx) { open = true; } })()"
    class="inline">
    {{-- Trigger --}}
    <div @click="open = true">
        {{ $trigger ?? '' }}
    </div>

    {{-- Teleport to body to avoid parent overflow clipping --}}
    <template x-teleport="body">
        <div x-show="open" x-cloak class="fixed inset-0 z-[10050] flex {{ $containerAlign }} justify-center p-3 sm:p-6"
            @keydown.escape.window="open = false" role="dialog" aria-modal="true" aria-label="{{ $title ?? 'Modal' }}">
            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/10 opacity-50"
                @if ($closeOnBg) @click="open = false" @endif></div>

            {{-- Panel wrapper (centering + width)  *** {{ $mobileShell }} --}}
            <div class="relative mx-auto w-full {{ $panelWidth }} ">
                {{-- Panel --}}
                <div class="{{ $panelFrame }} rounded-xl sm:max-h-[85vh] max-h-screen">
                    {{-- Header (sticky) --}}
                    <div
                        class="flex items-center justify-between px-3 sm:px-5 py-3 border-b border-slate-200 bg-white sticky top-0 z-10">
                        <h3 class="text-balance text-sm sm:text-base font-semibold text-slate-800 truncate">
                            {{ $title }}
                        </h3>
                        <button type="button"
                            class="btn btn-delete !w-fit !text-gray-500 hover:!text-red-500 btn btn-xs hover:!shadow-none"
                            @click="open = false" aria-label="ปิด"><i data-lucide="x"></i></button>
                    </div>

                    {{-- Body (scrollable) --}}
                    <div class="px-3 sm:px-5 py-3 sm:py-4 overflow-auto modal-body grow">
                        {{ $slot }}
                    </div>

                    {{-- Footer (optional, sticky) --}}
                    @isset($footer)
                        <div class="px-3 sm:px-5 py-3 border-t border-slate-200 bg-slate-50 sticky bottom-0">
                            {{ $footer }}
                        </div>
                    @endisset
                </div>
            </div>
        </div>
    </template>
</div>

<style>
    .modal-open,
    .modal-open body {
        overflow: visible;
    }

    .modal-backdrop {
        /* Ensure backdrop covers entire viewport */
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        /* z-index: 1040; */
    }

    /* .modal { */
    /* position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%; */
    /* z-index: 1050; */

    /* } */
</style>
