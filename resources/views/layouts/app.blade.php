<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ระบบบริหารจัดการข้อมูลการรับรองสถาบันจากสภาการพยาบาล')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('uploads/logonuthaiS-2.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('uploads/logonuthaiS-2.png') }}">
    <link rel="shortcut icon" href="{{ asset('uploads/logonuthaiS-2.png') }}">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/css/layout.css', 'resources/css/components.css', 'resources/js/app.js'])

    <link href="https://fonts.googleapis.com/css2?family=Prompt&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Polyfill: crypto.randomUUID for non-secure contexts -->
    <script>
        (function () {
            try {
                if (!window.crypto) window.crypto = {};
                if (typeof window.crypto.randomUUID !== 'function') {
                    window.crypto.randomUUID = function () {
                        try {
                            if (window.crypto && window.crypto.getRandomValues) {
                                const buf = new Uint8Array(16);
                                window.crypto.getRandomValues(buf);
                                buf[6] = (buf[6] & 0x0f) | 0x40; // version 4
                                buf[8] = (buf[8] & 0x3f) | 0x80; // variant 10
                                const hex = Array.from(buf, b => b.toString(16).padStart(2, '0')).join('');
                                return `${hex.substring(0, 8)}-${hex.substring(8, 12)}-${hex.substring(12, 16)}-${hex.substring(16, 20)}-${hex.substring(20)}`;
                            }
                        } catch (e) { /* noop */ }
                        // Math.random fallback
                        let d = Date.now();
                        let d2 = (typeof performance !== 'undefined' && performance.now) ? performance.now() * 1000 : 0;
                        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
                            let r = Math.random() * 16;
                            if (d > 0) { r = (d + r) % 16 | 0; d = Math.floor(d / 16); }
                            else { r = (d2 + r) % 16 | 0; d2 = Math.floor(d2 / 16); }
                            return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
                        });
                    }
                }
            } catch (e) { /* noop */ }
        })();
    </script>
    <script src="//unpkg.com/alpinejs" defer></script>
    @stack('styles')

    <!-- Prevent Alpine.js flash of unstyled content -->
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body>
    <!-- Splash Loader -->
    <div id="splash-loader" aria-busy="true" aria-live="polite">
        <div class="splash-inner">
            <img src="{{ asset('uploads/logonuthaiS-2.png') }}" alt="กำลังโหลดระบบ" class="splash-logo">
            <div class="splash-spinner" aria-hidden="true"></div>
            <p class="splash-text">กำลังโหลด...</p>
        </div>
    </div>
    <x-navbar />
    <!-- Main Content Area -->
    <main class="main-content">
        <div class="container">
            @if (!empty($breadcrumbs))
                <ul class="breadcrumb">
                    <li><a href="{{ route('dashboard.index') }}"><i class="fas fa-home"></i></a></li>
                    @foreach ($breadcrumbs as $breadcrumb)
                        @if ($loop->last)
                            <li>{{ $breadcrumb['title'] }}</li>
                        @else
                            <li><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a></li>
                        @endif
                    @endforeach
                </ul>
            @endif
            @if (View::hasSection('header') || View::hasSection('subheader'))
                <div class="page-header">
                    <div class="page-header-content">
                        <h1 class="page-title">
                            @yield('header')
                        </h1>
                        @if (View::hasSection('subheader'))
                            <p class="page-subtitle">
                                @yield('subheader')
                            </p>
                        @endif
                    </div>
                </div>
            @endif

            <div class="page-content">
                @yield('content')
            </div>

            <x-toasts />
        </div>
    </main>

    <!-- Footer -->
    <footer class="main-footer">
        &copy; {{ date('Y') }} ระบบบริหารจัดการข้อมูลการรับรองสถาบันจากสภาการพยาบาล — Version 1.0.0
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        // รีเซ็ต dropdown เมื่อโหลดหน้าใหม่
        document.addEventListener("DOMContentLoaded", () => {
            // toggle dropdown
            document.querySelectorAll(".navbar .dropdown-toggle").forEach(btn => {
                btn.addEventListener("click", e => {
                    e.preventDefault();
                    e.stopPropagation();
                    const parent = btn.closest(".dropdown");
                    parent.classList.toggle("open");
                });
            });

            // close เมื่อกดที่อื่น
            window.addEventListener("click", () => {
                document.querySelectorAll(".navbar .dropdown.open").forEach(d => d.classList.remove(
                    "open"));
            });
            // close เมื่อคลิกลิงก์ใน dropdown
            document.querySelectorAll(".navbar .dropdown-menu a").forEach(link => {
                link.addEventListener("click", () => {
                    link.closest(".dropdown").classList.remove("open");
                });
            });
        });
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (window.lucide) {
                lucide.createIcons();
            }
        });
    </script>

    <!-- Splash Loader Hide -->
    <script>
        (function() {
            const el = document.getElementById('splash-loader');
            if (!el) return;
            const hide = () => {
                if (!el.classList.contains('splash-hidden')) {
                    el.classList.add('splash-hidden');
                    // Remove from DOM after transition
                    setTimeout(() => {
                        if (el && el.parentNode) {
                            el.parentNode.removeChild(el);
                        }
                    }, 100);
                }
            };
            // Hide when everything loaded
            window.addEventListener('load', hide);
            // Fallback in case load delays
            setTimeout(hide, 3000);
        })();
    </script>

    {{-- @yield('scripts') --}}
    @stack('scripts')

</body>

</html>
