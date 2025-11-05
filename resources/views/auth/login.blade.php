@extends('layouts.app')

@section('title', 'เข้าสู่ระบบ')

@section('content')
    <div class="flex items-center justify-center">
        <div class="max-w-md w-full ">
            <!-- Main Card -->
            <div class="bg-white/80 backdrop-blur-sm shadow-2xl rounded-3xl p-8 border border-white/20 min-w-md">
                <!-- Logo Section -->
                <div class="flex justify-center mb-6">
                    <div class="relative">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full blur-lg opacity-30 animate-pulse">
                        </div>
                        <img src="{{ asset('uploads/logonuthaiS-2.png') }}" alt="MSU Logo"
                            class="relative h-24 w-24 object-contain drop-shadow-lg" />
                    </div>
                </div>

                <!-- Header -->
                <div class="text-center mb-6">
                    {{-- <h2 class="text-3xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent mb-2">
                    เข้าสู่ระบบ
                </h2> --}}
                    {{-- <p class="text-gray-500 text-sm">ยินดีต้อนรับสู่ระบบ KPI</p> --}}
                    <p class="text-gray-500 text-sm">เข้าสู่ระบบ KPI</p>
                </div>

                <form id="loginForm" class="space-y-6" method="POST" action="{{ url('/login') }}">
                    @csrf

                    <!-- Email Field -->
                    <div class="group">
                        <label for="email"
                            class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-blue-600 transition-colors duration-200">
                            อีเมล
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors duration-200"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207">
                                    </path>
                                </svg>
                            </div>
                            <input type="text" id="email" name="email" autocomplete="username"
                                class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50/50 focus:outline-none focus:ring-0 focus:border-blue-500 focus:bg-white transition-all duration-200 placeholder-gray-400"
                                placeholder="กรอกอีเมลของคุณ" required>
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="group">
                        <label for="password"
                            class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-blue-600 transition-colors duration-200">
                            รหัสผ่าน
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors duration-200"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                    </path>
                                </svg>
                            </div>
                            <input type="password" id="password" name="password" autocomplete="current-password"
                                class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50/50 focus:outline-none focus:ring-0 focus:border-blue-500 focus:bg-white transition-all duration-200 placeholder-gray-400"
                                placeholder="กรอกรหัสผ่านของคุณ" required>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div id="error" class="hidden">
                        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700 font-medium" id="error-text"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="submitBtn"
                        class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-blue-200 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                        <span id="btn-text">เข้าสู่ระบบ</span>
                        <svg id="loading-spinner" class="hidden animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </button>
                </form>
<div class="mt-6 text-center">
    <a href="{{ route('sso.login') }}"
       class="inline-flex items-center justify-center w-full px-4 py-2 text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
        <i class="fa-solid fa-right-to-bracket mr-2"></i>
        เข้าสู่ระบบด้วยบัญชี KKU (SSO)
    </a>
</div>

                <!-- Footer -->
                {{-- <div class="mt-8 text-center">
                <p class="text-xs text-gray-500">
                    Protected by security measures
                </p>
            </div> --}}
            </div>

            <!-- Decorative Elements -->
            {{-- <div class="absolute top-10 left-10 w-20 h-20 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
        <div class="absolute top-10 right-10 w-20 h-20 bg-purple-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-8 left-20 w-20 h-20 bg-pink-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div> --}}
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const form = document.getElementById('loginForm');
            const errorDiv = document.getElementById('error');
            const errorText = document.getElementById('error-text');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btn-text');
            const loadingSpinner = document.getElementById('loading-spinner');
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            // Add input animations
            const inputs = form.querySelectorAll('input[type="text"], input[type="password"]');
            inputs.forEach(input => {
                input.addEventListener('focus', () => {
                    input.parentElement.classList.add('group');
                });

                input.addEventListener('blur', () => {
                    if (!input.value) {
                        input.parentElement.classList.remove('group');
                    }
                });
            });

            form?.addEventListener('submit', async (e) => {
                e.preventDefault();

                // Hide error and show loading state
                errorDiv.classList.add('hidden');
                errorText.textContent = '';
                submitBtn.disabled = true;
                btnText.textContent = 'กำลังเข้าสู่ระบบ...';
                loadingSpinner.classList.remove('hidden');
                loadingSpinner.classList.add('inline');

                const payload = {
                    email: form.email.value,
                    password: form.password.value,
                    remember: true
                };

                try {
                    const res = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        credentials: 'include',
                        body: JSON.stringify(payload)
                    });

                    const data = await res.json().catch(() => ({}));

                    if (!res.ok) {
                        const msg = data.message || 'เข้าสู่ระบบไม่สำเร็จ';
                        throw new Error(msg);
                    }

                    // Success state
                    btnText.textContent = 'เข้าสู่ระบบสำเร็จ!';
                    loadingSpinner.classList.add('hidden');
                    loadingSpinner.classList.remove('inline');

                    // Store token if provided
                    if (data.token) {
                        try {
                            localStorage.setItem('token', data.token);
                        } catch (_) {}
                    }

                    // Redirect with a slight delay for better UX
                    setTimeout(() => {
                        const redirectTo = data.redirect || "{{ route('dashboard.index') }}";
                        window.location.assign(redirectTo);
                    }, 1000);

                } catch (err) {
                    // Reset button state
                    submitBtn.disabled = false;
                    btnText.textContent = 'เข้าสู่ระบบ';
                    loadingSpinner.classList.add('hidden');
                    loadingSpinner.classList.remove('inline');

                    // Show error with animation
                    errorText.textContent = err.message || 'เกิดข้อผิดพลาด';
                    errorDiv.classList.remove('hidden');

                    // Shake animation for error
                    form.classList.add('animate-pulse');
                    setTimeout(() => form.classList.remove('animate-pulse'), 600);
                }
            });

            // // Add custom CSS for animations
            // const style = document.createElement('style');
            // style.textContent = `
        //     @keyframes blob {
        //         0% { transform: translate(0px, 0px) scale(1); }
        //         33% { transform: translate(30px, -50px) scale(1.1); }
        //         66% { transform: translate(-20px, 20px) scale(0.9); }
        //         100% { transform: translate(0px, 0px) scale(1); }
        //     }
        //     .animate-blob { animation: blob 7s infinite; }
        //     .animation-delay-2000 { animation-delay: 2s; }
        //     .animation-delay-4000 { animation-delay: 4s; }
        // `;
            // document.head.appendChild(style);
        })();
    </script>
@endpush

@push('styles')
    <style>
        .container {
            display: flex;
            /* text-align: center; */
            align-items: center;
            justify-content: center;

        }
    </style>
@endpush
