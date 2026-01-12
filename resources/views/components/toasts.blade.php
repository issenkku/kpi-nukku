{{-- resources/views/components/toasts.blade.php --}}
@php $errs = $errors->any() ? collect($errors->all()) : collect(); @endphp

<div id="global-toasts" x-data="{
    showSuccess: {{ session()->has('success') ? 'true' : 'false' }},
    showError: {{ session()->has('error') ? 'true' : 'false' }},
    showErrors: {{ $errors->any() ? 'true' : 'false' }},
    timers: {},
    hideAfter(ms, key) {
        if (!this[key]) return;
        let remaining = ms,
            step = 50;
        const tick = () => {
            if (!this[key]) return;
            remaining -= step;
            if (remaining <= 0) { this[key] = false; return; }
            this.timers[key] = setTimeout(tick, step);
        };
        this.timers[key] = setTimeout(tick, step);
    },
    pause(key) { if (this.timers[key]) { clearTimeout(this.timers[key]);
            this.timers[key] = null; } },
    resume(key, msDefault = 3000) {
        if (this[key] && !this.timers[key]) this.hideAfter(msDefault, key);
    }
}" x-init="hideAfter(4000, 'showSuccess');
hideAfter(6000, 'showError');
hideAfter(8000, 'showErrors');"
    class="fixed z-[200] bottom-6 right-6 w-[calc(100vw-2rem)] max-w-sm sm:max-w-md space-y-3 pointer-events-none"
    aria-live="polite" aria-atomic="true">
    {{-- SUCCESS --}}
    @if (session('success'))
        <div x-show="showSuccess" x-transition.opacity.scale.origin-bottom-right.duration-200
            @mouseenter="pause('showSuccess')" @mouseleave="resume('showSuccess', 2000)"
            class="pointer-events-auto relative flex gap-3 items-start rounded-xl border border-emerald-200 bg-emerald-50/90 backdrop-blur-sm text-emerald-900 shadow-lg px-4 py-3"
            role="alert" data-autoclose="4000">
            <div class="shrink-0 mt-0.5">
                {{-- check-circle icon --}}
                <svg class="h-5 w-5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="min-w-0">
                <div class="font-semibold leading-5">สำเร็จ</div>
                <div class="text-sm break-words">{{ session('success') }}</div>
            </div>
            <button type="button" @click="showSuccess=false"
                class="absolute top-2 right-2 rounded-md p-1 hover:bg-emerald-100/70" aria-label="ปิดการแจ้งเตือน"
                data-close>
                <svg class="h-4 w-4 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    {{-- ERROR (single) --}}
    @if (session('error'))
        <div x-show="showError" x-transition.opacity.scale.origin-bottom-right.duration-200
            @mouseenter="pause('showError')" @mouseleave="resume('showError', 3000)"
            class="pointer-events-auto relative flex gap-3 items-start rounded-xl border border-red-200 bg-red-50/90 backdrop-blur-sm text-red-900 shadow-lg px-4 py-3"
            role="alert" data-autoclose="6000">
            <div class="shrink-0 mt-0.5">
                {{-- x-circle icon --}}
                <svg class="h-5 w-5 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01M12 5a7 7 0 100 14 7 7 0 000-14z" />
                </svg>
            </div>
            <div class="min-w-0">
                <div class="font-semibold leading-5">เกิดข้อผิดพลาด</div>
                <div class="text-sm break-words">{{ session('error') }}</div>
            </div>
            <button type="button" @click="showError=false"
                class="absolute top-2 right-2 rounded-md p-1 hover:bg-red-100/70" aria-label="ปิดการแจ้งเตือน"
                data-close>
                <svg class="h-4 w-4 text-red-700" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    {{-- VALIDATION ERRORS (stacked summary) --}}
    @if ($errs->isNotEmpty())
        <div x-show="showErrors" x-transition.opacity.scale.origin-bottom-right.duration-200
            @mouseenter="pause('showErrors')" @mouseleave="resume('showErrors', 3000)"
            class="pointer-events-auto relative flex gap-3 items-start rounded-xl border border-amber-200 bg-amber-50/90 backdrop-blur-sm text-amber-900 shadow-lg px-4 py-3"
            role="alert" data-autoclose="8000">
            <div class="shrink-0 mt-0.5">
                {{-- exclamation-triangle --}}
                <svg class="h-5 w-5 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v3m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L14.71 3.86a2 2 0 00-3.42 0z" />
                </svg>
            </div>
            <div class="min-w-0">
                <div class="font-semibold leading-5">กรุณาตรวจสอบข้อมูล</div>
                <ul class="mt-1 text-sm list-disc list-inside space-y-0.5">
                    @foreach ($errs->take(3) as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                    @if ($errs->count() > 3)
                        <li>และข้อผิดพลาดอื่น ๆ อีก {{ $errs->count() - 3 }} รายการ</li>
                    @endif
                </ul>
            </div>
            <button type="button" @click="showErrors=false"
                class="absolute top-2 right-2 rounded-md p-1 hover:bg-amber-100/70" aria-label="ปิดการแจ้งเตือน"
                data-close>
                <svg class="h-4 w-4 text-amber-700" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif
</div>

{{-- Vanilla JS fallback: auto-dismiss + close works even if Alpine is not loaded --}}
<script>
    (function() {
        const root = document.currentScript.previousElementSibling;
        if (!root) return;
        // Close buttons
        root.querySelectorAll('[data-close]').forEach(btn => {
            btn.addEventListener('click', () => {
                const box = btn.closest('[role="alert"]');
                if (box) box.style.display = 'none';
            });
        });
        // Auto close
        root.querySelectorAll('[role="alert"][data-autoclose]').forEach(box => {
            const ms = parseInt(box.getAttribute('data-autoclose'), 10) || 4000;
            let timer = setTimeout(() => box.style.display = 'none', ms);
            // Pause on hover
            box.addEventListener('mouseenter', () => {
                clearTimeout(timer);
            });
            box.addEventListener('mouseleave', () => {
                clearTimeout(timer);
                timer = setTimeout(() => box.style.display = 'none', 2000);
            });
        });
    })();

    // Programmatic toast (for AJAX actions)
    (function() {
        const root = document.getElementById('global-toasts');
        if (!root) return;

        const toastClass = {
            success: 'border-emerald-200 bg-emerald-50/90 text-emerald-900',
            error: 'border-red-200 bg-red-50/90 text-red-900',
            warn: 'border-amber-200 bg-amber-50/90 text-amber-900',
        };
        const iconSvg = {
            success: '<svg class="h-5 w-5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            error: '<svg class="h-5 w-5 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 5a7 7 0 100 14 7 7 0 000-14z"/></svg>',
            warn: '<svg class="h-5 w-5 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L14.71 3.86a2 2 0 00-3.42 0z"/></svg>',
        };
        const titleText = { success: 'สำเร็จ', error: 'เกิดข้อผิดพลาด', warn: 'กรุณาตรวจสอบข้อมูล' };

        window.showToast = function(type, message, duration = 4000) {
            if (!message) return;
            const t = toastClass[type] ? type : 'success';
            const box = document.createElement('div');
            box.className = `pointer-events-auto relative flex gap-3 items-start rounded-xl border shadow-lg px-4 py-3 backdrop-blur-sm ${toastClass[t]}`;
            box.setAttribute('role', 'alert');
            box.dataset.autoclose = String(duration);
            box.innerHTML = `
                <div class="shrink-0 mt-0.5">${iconSvg[t]}</div>
                <div class="min-w-0">
                    <div class="font-semibold leading-5">${titleText[t]}</div>
                    <div class="text-sm break-words"></div>
                </div>
                <button type="button" class="absolute top-2 right-2 rounded-md p-1" aria-label="ปิดการแจ้งเตือน">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            `;
            box.querySelector('.text-sm').textContent = message;
            const closeBtn = box.querySelector('button');
            closeBtn.addEventListener('click', () => box.remove());
            root.appendChild(box);

            const ms = parseInt(box.dataset.autoclose, 10) || 4000;
            let timer = setTimeout(() => box.remove(), ms);
            box.addEventListener('mouseenter', () => clearTimeout(timer));
            box.addEventListener('mouseleave', () => {
                clearTimeout(timer);
                timer = setTimeout(() => box.remove(), 2000);
            });
        };
    })();
</script>
