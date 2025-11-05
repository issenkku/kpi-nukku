<style>
    /* Navbar (scoped) */
    /* เพิ่ม class root .app-navbar เพื่อกัน style ภายนอก (เช่น .card, a, button global) มากระทบ */
    .app-navbar.navbar {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        background: #fff;
        padding: 6px 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        position: sticky;
        top: 0;
        z-index: 1000;
        width: 100%;
        box-sizing: border-box;
        flex-wrap: wrap;
        font-family: "Prompt", system-ui, sans-serif;
        font-size: 14px;
        line-height: 1.4;
    }

    /* ป้องกัน scale แต่ไม่ override font-family ของไอคอน (Font Awesome / Lucide) */
    .app-navbar.navbar * {
        font-size: inherit;
        line-height: inherit;
        box-sizing: border-box;
    }

    /* คืนค่า font-family สำหรับ font awesome (fa-* ใช้ pseudo + font-face ของตัวเอง) */
    .app-navbar.navbar i[class^="fa-"],
    .app-navbar.navbar i[class*=" fa-"] {
        font-family: var(--fa-style-family-classic, "Font Awesome 6 Free") !important;
        font-weight: 900;
        font-style: normal;
        speak: none;
    }

    /* lucide (ถ้าใช้ data-lucide) จะถูกแทนด้วย SVG ไม่ต้องแก้ แต่กันกรณีมี font icon อื่น */

    .navbar-menu {
        display: flex;
        align-items: center;
        width: auto;
        gap: 5px;
        transition: all 0.3s ease;
    }

    .app-navbar .navbar-menu a {
        color: #374151;
        text-decoration: none;
        padding: 8px 16px;
        border-radius: 8px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        /* line-height: 1.4; */
        white-space: nowrap;
        /* min-height: 30px; */
        box-sizing: border-box;
    }

    .app-navbar .navbar-menu a:hover,
    .app-navbar .navbar-menu a.active {
        background: #e5e7eb;
        color: #1f2937;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .app-navbar .navbar-brand {
        display: flex;
        align-items: center;
        font-weight: 600;
        color: #1f2937;
        text-decoration: none;
        gap: 8px;
        max-width: 50%;
        margin: 10px 0 10px 0;
    }

    .app-navbar .navbar-brand img {
        height: 36px;
        width: auto;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .app-navbar .navbar-brand span {
        font-size: 14px;
        font-weight: 600;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .app-navbar .dropdown {
        position: relative;
    }

    .app-navbar .dropdown-toggle {
        cursor: pointer;
        padding: 8px 16px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        font-size: 14px;
        /* min-height: 44px; */
        box-sizing: border-box;
        user-select: none;
    }

    .app-navbar .dropdown-toggle:hover {
        background: #f3f4f6;
        transform: translateY(-1px);
    }

    .app-navbar .dropdown-menu-navbar {
        display: none;
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        /* left: 0; */
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        min-width: fit-content;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        z-index: 1050;
        padding: 8px 0;
    }

    .app-navbar .dropdown.open .dropdown-menu-navbar {
        display: block !important;
    }

    .app-navbar .dropdown-menu-navbar a {
        padding: 8px 16px;
        color: #374151;
        font-size: 14px;
        transition: all 0.15s ease;
        border-radius: 0;
        margin: 0 8px;
        border-radius: 6px;
    }

    .app-navbar .dropdown-menu-navbar a:hover {
        background: #f3f4f6;
        color: #1f2937;
    }

    /* .app-navbar .user-avatar {
        height: 35px;
        width: 35px;
        border-radius: 50%;
        border: 2px solid #ddd;
    } */

    .app-navbar .navbar-toggle {
        display: none;
        font-size: 1.25rem;
        cursor: pointer;
        background: none;
        border: none;
        color: #374151;
        padding: 8px;
        border-radius: 6px;
        min-height: 44px;
        min-width: 44px;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .app-navbar .navbar-toggle:hover {
        background: #f3f4f6;
        color: #1f2937;
    }

    /* User Info in Dropdown */
    .app-navbar .dropdown-menu-navbar .user-info {
        padding: 12px 18px;
        border-bottom: 1px solid #e5e7eb;
        margin-bottom: 8px;
    }

    .app-navbar .dropdown-menu-navbar .user-info .user-name {
        font-weight: 600;
        color: #1f2937;
        font-size: 14px;
        margin-bottom: 2px;
    }

    .app-navbar .dropdown-menu-navbar .user-info .user-role {
        font-size: 12px;
        color: #6b7280;
    }

   

    /* Dropdown Menu Items */
    .app-navbar .dropdown-menu-navbar a {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 18px;
        font-size: 14px;
        color: #374151;
        margin: 0 8px;
        border-radius: 6px;
        transition: all 0.15s ease;
    }

    .app-navbar .dropdown-menu-navbar a:hover {
        background: #f3f4f6;
        color: #1f2937;
    }

    .app-navbar .dropdown-menu-navbar a i {
        width: 16px;
        text-align: center;
        font-size: 14px;
    }

    /* Button Styling */
    .app-navbar .buttonNav {
        font-size: 14px;
        font-weight: 500;
        position: relative;
    }

    .app-navbar .buttonNav-item {
        font-size: 14px;
        font-weight: 400;
    }

    /* Icon spacing */
    .app-navbar .navbar-menu i,
    .app-navbar .dropdown-menu-navbar i {
        flex-shrink: 0;
    }

    /* Active dropdown indicator */
    .app-navbar .dropdown.open .dropdown-toggle {
        background: #f3f4f6;
    }

    /* Dropdown caret animation */
    .app-navbar .dropdown-toggle .fa-caret-down {
        transition: transform 0.2s ease;
    }

    .app-navbar .dropdown.open .dropdown-toggle .fa-caret-down {
        transform: rotate(180deg);
    }

    /* Mobile First Responsive */
    @media (max-width: 1024px) {
        /* .app-navbar.navbar {
            padding: 0.75rem 1rem;
        } */

        .app-navbar .navbar-menu {
            gap: 5px;
        }

        .app-navbar .navbar-menu a {
            padding: 8px 12px;
            font-size: 13px;
        }

        .dropdown .dropdown-toggle .dropdown-toggle-span {
            display: none;
        }
    }

    @media (max-width: 930px) {
        .dropdown .dropdown-toggle .dropdown-toggle-span {
            display: none;
        }
    }

    @media (max-width: 820px) {
        .app-navbar .dropdown {
            /* position: relative; */
            width: 100%;
        }

        .app-navbar .dropdown .dropdown-toggle .dropdown-toggle-span {
            display: initial;
        }

        .app-navbar.navbar {
            flex-wrap: nowrap;
            align-items: center;
        }

        .app-navbar .navbar-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #fff;
            border-top: 1px solid #e5e7eb;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            flex-direction: column;
            padding: 1rem;
            gap: 0.5rem;
            /* max-height: calc(100vh - 80px); */
            overflow-y: auto;
        }

        .app-navbar .navbar-menu.show {
            display: flex;
        }

        .app-navbar .navbar-menu a {
            width: 100%;
            justify-content: flex-start;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
        }

        .app-navbar .navbar-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .app-navbar .dropdown.open .dropdown-menu-navbar {
            position: static !important;
            display: block !important;
            box-shadow: none;
            border: none;
            background: #f9fafb;
            margin-top: 0.5rem;
            border-radius: 8px;
        }

        .app-navbar .dropdown-menu-navbar {
            display: none;
        }
    }

    @media (max-width: 640px) {
        .app-navbar .navbar-brand span {
            display: none;
        }

        .app-navbar.navbar {
            padding: 0.5rem 0.75rem;
        }
    }

    @media (max-width: 480px) {
        .app-navbar .navbar-menu a {
            font-size: 15px;
            padding: 14px 16px;
        }

        .app-navbar .dropdown-menu-navbar a {
            font-size: 14px;
            padding: 12px 16px;
        }
    }
</style>

<!-- Navbar Component -->
<nav class="navbar app-navbar" id="main-navbar">
    <a href="{{ auth()->check() ? (auth()->user()->hasRole('user') ? route('dashboardkpi.index') : route('dashboard.index')) : route('dashboard.index') }}"
        class="navbar-brand">
        <img src="/uploads/logonuthaiS-2.png" alt="Logo">
        <span>ระบบบริหาร KPI เพื่อการรับรองสถาบันจากสภาการพยาบาล</span>
    </a>

    <!-- ปุ่ม Hamburger -->
    <button class="navbar-toggle" id="navbar-toggle" aria-label="Toggle navigation">
        <i class="fa-solid fa-bars"></i>
    </button>

    @auth
        <div class="navbar-menu" id="navbar-menu">
            @can('view-dashboard')
                <a href="{{ route('dashboard.index') }}"
                    class="{{ request()->routeIs('dashboard.index') ? 'active' : '' }} buttonNav">
                    <i class="fa-solid fa-house"></i> หน้าหลัก
                </a>
                <a href="{{ route('dashboard.getData') }}"
                    class="{{ request()->routeIs('dashboard.getData') ? 'active' : '' }} buttonNav">
                    <i class="fa-solid fa-chart-column"></i> กราฟแสดงผลลัพธ์ตัวบ่งชี้
                </a>
            @endcan

            @hasanyrole('user')
                <a href="{{ route('dashboardkpi.index') }}"
                    class="{{ request()->is('dashboardkpi*') ? 'active' : '' }} buttonNav">
                    <i class="fa-solid fa-house"></i> หน้าหลัก
                </a>

                <a href="{{ route('evidences.index') }}" class="{{ request()->is('evidences*') ? 'active' : '' }} buttonNav">
                    <i class="fa-solid fa-file"></i> หลักฐานของฉัน
                </a>
            @endhasanyrole
            @hasanyrole('super_admin|system_admin|qa_admin')
                <a href="{{ route('dashboardkpi.index') }}"
                    class="{{ request()->routeIs('dashboardkpi.*') ? 'active' : '' }} buttonNav">
                    <i class="fa-solid fa-chart-line"></i> ตรวจสอบตัวบ่งชี้
                </a>
            @endhasanyrole
            @hasanyrole('administration_admin')
                <a href="{{ route('indicator.index') }}"
                    class="buttonNav-item {{ request()->is('indicator*') ? 'active' : '' }}">
                    <i class="fa-solid fa-sliders"></i> จัดการตัวบ่งชี้
                </a>
            @endhasanyrole
            @hasanyrole('super_admin|system_admin|qa_admin')
                @canany(['view-indicator-dashboard', 'view-evidence', 'view-sar_report'])
                    <div class="dropdown">
                        <div class="dropdown-toggle justify-between" role="button" tabindex="0" onclick="toggleDropdown(this)">
                            <div><i class="fa-solid fa-database"></i>
                                <span class="dropdown-toggle-span">จัดการข้อมูล</span>
                            </div>
                            <i class="fa-solid fa-caret-down"></i>
                        </div>
                        <div class="dropdown-menu-navbar">
                            @can('view-indicator-dashboard')
                                <a href="{{ route('indicator.index') }}"
                                    class="buttonNav-item {{ request()->is('indicator*') ? 'active' : '' }}">
                                    <i class="fa-solid fa-sliders"></i> จัดการตัวบ่งชี้
                                </a>
                            @endcan
                            @can('view-sar_report')
                                <a href="{{ route('sar_reports.index') }}"
                                    class="buttonNav-item {{ request()->is('sar_reports*') ? 'active' : '' }}">
                                    <i class="fa-solid fa-file-lines"></i> จัดการเอกสาร
                                </a>
                            @endcan
                            @can('view-evidence')
                                <a href="{{ route('evidences.index') }}"
                                    class="buttonNav-item {{ request()->is('evidences*') ? 'active' : '' }}">
                                    <i class="fa-solid fa-folder-open"></i> จัดการหลักฐาน
                                </a>
                            @endcan

                        </div>
                    </div>
                @endcanany
            @endhasanyrole

            @hasanyrole('super_admin|system_admin')
                <div class="dropdown">
                    <div class="dropdown-toggle justify-between" role="button" tabindex="0" onclick="toggleDropdown(this)">
                        <div><i class="fa-solid fa-gear"></i>
                            <span class="dropdown-toggle-span">ตั้งค่าระบบ</span>
                        </div>
                        <i class="fa-solid fa-caret-down"></i>
                    </div>
                    <div class="dropdown-menu-navbar">
                        @can('view-settings')
                            <a href="{{ route('settings.index') }}" class="buttonNav-item">
                                <i class="fa-solid fa-bell"></i> กำหนดวันแจ้งเตือน
                            </a>
                        @endcan
                        @can('view-departments')
                            <a href="{{ route('departments.index') }}" class="buttonNav-item">
                                <i class="fa-solid fa-sitemap"></i> จัดการหน่วยงาน
                            </a>
                        @endcan
                        @can('view-standards')
                            <a href="{{ route('standards.index') }}" class="buttonNav-item">
                                <i class="fa-solid fa-layer-group"></i> จัดการข้อมูลมาตรฐาน/ด้านต่างๆ
                            </a>
                        @endcan
                        @can('view-users')
                            <a href="{{ route('users.index') }}" class="buttonNav-item">
                                <i class="fa-solid fa-users"></i> จัดการผู้ใช้งาน
                            </a>
                        @endcan
                        {{-- @can('view-evidence')
                            <a href="{{ route('evidences.index') }}" class="buttonNav-item">
                                <i class="fa-solid fa-folder-open"></i> จัดการหลักฐาน
                            </a>
                        @endcan
                        @can('view-sar_report')
                            <a href="{{ route('sar_reports.index') }}" class="buttonNav-item">
                                <i class="fa-solid fa-file-lines"></i> จัดการเอกสาร
                            </a>
                        @endcan --}}
                    </div>
                </div>
            @endhasanyrole
     
            <div class="dropdown">
                <div class="dropdown-toggle justify-between" role="button" tabindex="0" onclick="toggleDropdown(this)">
                    <div><i class="fa-solid fa-user-circle"></i>
                        <span class="dropdown-toggle-span">{{ auth()->user()->name ?? 'ผู้ใช้' }}</span>
                    </div>
                    <i class="fa-solid fa-caret-down"></i>
                </div>
                <div class="dropdown-menu-navbar">
                    <div class="user-info">
                        <div class="user-name">{{ auth()->user()->display_name ?? 'ผู้ใช้' }}</div>
                        @if (auth()->user()->roles->isNotEmpty())
                            <div class="user-role">บทบาท: {{ auth()->user()->roles->pluck('name')->join(', ') }}</div>
                        @endif
                       
                    </div>
                     <div class="user-info2">
                            @hasrole('super_admin')
                                <a href="{{ asset('manuals/Super_admin.pdf') }}" target="_blank"
                                    class="buttonNav-item">
                                    <i class="fa-solid fa-file-pdf"></i> คู่มือ Super Admin
                                </a>
                            @endhasrole
                            @hasrole('system_admin')
                                <a href="{{ asset('manuals/System_admin.pdf') }}" target="_blank"
                                    class="buttonNav-item">
                                    <i class="fa-solid fa-file-pdf"></i> คู่มือ System Admin
                                </a>
                            @endhasrole
                            @hasrole('administration_admin')
                                <a href="{{ asset('manuals/Administration_Admin.pdf') }}" target="_blank"
                                    class="buttonNav-item">
                                    <i class="fa-solid fa-file-pdf"></i> คู่มือ Administration Admin
                                </a>
                            @endhasrole
                            @hasrole('qa_admin')
                                <a href="{{ asset('manuals/qa_admin.pdf') }}" target="_blank"
                                    class="buttonNav-item">
                                    <i class="fa-solid fa-file-pdf"></i> คู่มือ QA Admin
                                </a>
                            @endhasrole
                            @hasrole('user')
                                <a href="{{ asset('manuals/User.pdf') }}" target="_blank" class="buttonNav-item">
                                    <i class="fa-solid fa-file-pdf"></i> คู่มือผู้ใช้งาน (User)
                                </a>
                            @endhasrole
                        </div>
                   
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="buttonNav-item">
                        <i class="fa-solid fa-right-from-bracket"></i> ออกจากระบบ
                    </a>
                </div>

            </div>
        </div>
    @endauth
</nav>

<!-- Logout Form -->
@auth
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
        @csrf
    </form>
@endauth

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile menu toggle
        const navbarToggle = document.getElementById('navbar-toggle');
        const navbarMenu = document.getElementById('navbar-menu');

        if (navbarToggle && navbarMenu) {
            navbarToggle.addEventListener('click', function() {
                navbarMenu.classList.toggle('show');

                // Update aria-expanded for accessibility
                const isExpanded = navbarMenu.classList.contains('show');
                navbarToggle.setAttribute('aria-expanded', isExpanded);

                // Change icon
                const icon = navbarToggle.querySelector('i');
                if (icon) {
                    icon.className = isExpanded ? 'fa-solid fa-times' : 'fa-solid fa-bars';
                }
            });
        }

        // Dropdown functionality
        const dropdowns = document.querySelectorAll('.app-navbar .dropdown');
        console.log('Found dropdowns:', dropdowns.length);

        dropdowns.forEach((dropdown, index) => {
            const toggle = dropdown.querySelector('.dropdown-toggle');
            console.log(`Dropdown ${index} toggle found:`, !!toggle);

            if (toggle) {
                // Click handler
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Dropdown clicked, current state:', dropdown.classList.contains(
                        'open'));

                    // Close other dropdowns
                    dropdowns.forEach(otherDropdown => {
                        if (otherDropdown !== dropdown) {
                            otherDropdown.classList.remove('open');
                        }
                    });

                    // Toggle current dropdown
                    dropdown.classList.toggle('open');
                    console.log('Dropdown new state:', dropdown.classList.contains('open'));
                });

                // Keyboard handler
                toggle.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        toggle.click();
                    }
                    if (e.key === 'Escape') {
                        dropdown.classList.remove('open');
                    }
                });
            }
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.app-navbar .dropdown')) {
                dropdowns.forEach(dropdown => {
                    dropdown.classList.remove('open');
                });
            }
        });

        // Close mobile menu when clicking on links
        const navLinks = document.querySelectorAll('.navbar-menu a');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 820) {
                    navbarMenu.classList.remove('show');
                    navbarToggle.setAttribute('aria-expanded', 'false');
                    const icon = navbarToggle.querySelector('i');
                    if (icon) {
                        icon.className = 'fa-solid fa-bars';
                    }
                }
            });
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 820) {
                navbarMenu.classList.remove('show');
                navbarToggle.setAttribute('aria-expanded', 'false');
                const icon = navbarToggle.querySelector('i');
                if (icon) {
                    icon.className = 'fa-solid fa-bars';
                }

                // Close all dropdowns on desktop
                dropdowns.forEach(dropdown => {
                    dropdown.classList.remove('open');
                });
            }
        });

        // Unified global dropdown toggler (safe for both navbar + filter multiselects)
        (function() {
            function unifiedToggle(arg) {
                // If string id -> treat as container id
                if (typeof arg === 'string') {
                    const target = document.getElementById(arg);
                    if (target) {
                        target.classList.toggle('open');
                        return true;
                    }
                    console.warn('toggleDropdown: id not found', arg);
                    return false;
                }
                // If element-like
                if (arg && typeof arg === 'object') {
                    const isEl = (arg instanceof Element) || typeof arg.closest === 'function';
                    if (isEl) {
                        // Direct multiselect container
                        if (arg.classList && arg.classList.contains('dropdown-multiselect')) {
                            arg.classList.toggle('open');
                            return true;
                        }
                        const wrapper = arg.closest && arg.closest('.dropdown, .dropdown-multiselect');
                        if (wrapper) {
                            wrapper.classList.toggle('open');
                            return true;
                        }
                    }
                }
                console.warn('toggleDropdown: unhandled argument', arg);
                return false;
            }
            // Only replace if not already our unified version
            if (!window.toggleDropdown || !window.toggleDropdown.__unified) {
                window.toggleDropdown = function(arg) {
                    try {
                        return unifiedToggle(arg);
                    } catch (e) {
                        console.error('toggleDropdown error', e);
                    }
                };
                window.toggleDropdown.__unified = true;
            }
        })();
    });
</script>
