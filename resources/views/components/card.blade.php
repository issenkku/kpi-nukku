@props(['title' => '', 'number' => null])

<style>
    /* Subtle animation for card appearance */
    .card {
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div {{-- hover:shadow-md transition --}}
    {{ $attributes->merge([
        'class' => 'bg-white rounded-2xl border border-slate-200 shadow-sm 
               p-4 sm:p-5 md:p-6 ',
    ]) }}>
    @if ($title)
        <div class="flex items-center gap-3 mb-3 md:mb-4">
            @if ($number !== null)
                <div
                    class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-semibold text-sm sm:text-base">
                    {{ $number }}
                </div>
            @endif
            <h2 class="text-base md:text-lg font-semibold text-slate-900">{{ $title }}</h2>
        </div>
    @endif

    {{ $slot }}
</div>
