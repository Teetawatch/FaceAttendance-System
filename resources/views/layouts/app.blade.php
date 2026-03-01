<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Face Attendance') }}</title>

    <!-- Google Fonts: Poppins + Noto Sans Thai + JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Noto+Sans+Thai:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">

    <!-- App Container -->
    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">

        <!-- 1. Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-[270px] bg-white border-r border-primary-100/60 transition-transform duration-200 ease-out md:relative md:translate-x-0 flex flex-col">

            <!-- Logo -->
            <div class="h-16 flex items-center px-6 border-b border-primary-50">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 cursor-pointer">
                    <div class="w-9 h-9 bg-primary-600 rounded-xl flex items-center justify-center">
                        <i data-lucide="scan-face" class="w-5 h-5 text-white"></i>
                    </div>
                    <span class="text-lg font-bold text-text tracking-tight">Face<span class="text-primary-600">System</span></span>
                </a>
            </div>

            <!-- Menu Items -->
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5">

                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors duration-150 cursor-pointer {{ request()->routeIs('dashboard') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-muted hover:bg-surface-50 hover:text-text' }}">
                    <i data-lucide="layout-dashboard" class="w-[18px] h-[18px] flex-shrink-0"></i>
                    <span class="text-sm">แดชบอร์ด</span>
                </a>

                <!-- Management (Admin Only) -->
                @if (auth()->user()->role === 'admin')
                    <div class="pt-5 pb-1.5 px-3 text-[11px] font-semibold text-muted/60 uppercase tracking-widest">
                        การจัดการข้อมูล</div>

                    <a href="{{ route('employees.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors duration-150 cursor-pointer {{ request()->routeIs('employees.*') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-muted hover:bg-surface-50 hover:text-text' }}">
                        <i data-lucide="users" class="w-[18px] h-[18px] flex-shrink-0"></i>
                        <span class="text-sm">พนักงาน</span>
                    </a>

                    <a href="{{ route('devices.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors duration-150 cursor-pointer {{ request()->routeIs('devices.*') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-muted hover:bg-surface-50 hover:text-text' }}">
                        <i data-lucide="tablet-smartphone" class="w-[18px] h-[18px] flex-shrink-0"></i>
                        <span class="text-sm">อุปกรณ์</span>
                    </a>

                    <a href="{{ route('users.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors duration-150 cursor-pointer {{ request()->routeIs('users.*') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-muted hover:bg-surface-50 hover:text-text' }}">
                        <i data-lucide="shield-check" class="w-[18px] h-[18px] flex-shrink-0"></i>
                        <span class="text-sm">ผู้ใช้งานระบบ</span>
                    </a>

                    <a href="{{ route('face.register') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors duration-150 cursor-pointer {{ request()->routeIs('face.register') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-muted hover:bg-surface-50 hover:text-text' }}">
                        <i data-lucide="user-round-check" class="w-[18px] h-[18px] flex-shrink-0"></i>
                        <span class="text-sm">ลงทะเบียนใบหน้า</span>
                    </a>

                    <!-- Student Attendance Section -->
                    <div class="pt-5 pb-1.5 px-3 text-[11px] font-semibold text-muted/60 uppercase tracking-widest">
                        นักเรียนหลักสูตร</div>

                    <a href="{{ route('courses.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors duration-150 cursor-pointer {{ request()->routeIs('courses.*') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-muted hover:bg-surface-50 hover:text-text' }}">
                        <i data-lucide="book-open" class="w-[18px] h-[18px] flex-shrink-0"></i>
                        <span class="text-sm">หลักสูตร</span>
                    </a>

                    <a href="{{ route('students.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors duration-150 cursor-pointer {{ request()->routeIs('students.*') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-muted hover:bg-surface-50 hover:text-text' }}">
                        <i data-lucide="graduation-cap" class="w-[18px] h-[18px] flex-shrink-0"></i>
                        <span class="text-sm">นักเรียน</span>
                    </a>

                    <a href="{{ route('student-reports.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors duration-150 cursor-pointer {{ request()->routeIs('student-reports.*') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-muted hover:bg-surface-50 hover:text-text' }}">
                        <i data-lucide="file-bar-chart" class="w-[18px] h-[18px] flex-shrink-0"></i>
                        <span class="text-sm">รายงานนักเรียน</span>
                    </a>

                    <a href="{{ route('student.face.register') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors duration-150 cursor-pointer {{ request()->routeIs('student.face.*') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-muted hover:bg-surface-50 hover:text-text' }}">
                        <i data-lucide="scan-face" class="w-[18px] h-[18px] flex-shrink-0"></i>
                        <span class="text-sm">ลงทะเบียนใบหน้านักเรียน</span>
                    </a>
                @endif

                <!-- Monitoring & Attendance (Admin & HR) -->
                @if(in_array(auth()->user()->role, ['admin', 'hr']))
                    <div class="pt-5 pb-1.5 px-3 text-[11px] font-semibold text-muted/60 uppercase tracking-widest">การตรวจสอบ</div>

                    <a href="{{ route('monitor.display') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors duration-150 cursor-pointer {{ request()->routeIs('monitor.display') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-muted hover:bg-surface-50 hover:text-text' }}">
                        <i data-lucide="monitor" class="w-[18px] h-[18px] flex-shrink-0"></i>
                        <span class="text-sm">จอภาพเรียลไทม์</span>
                    </a>

                    <a href="{{ route('monitor.scan') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors duration-150 cursor-pointer {{ request()->routeIs('monitor.scan') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-muted hover:bg-surface-50 hover:text-text' }}">
                        <i data-lucide="scan-line" class="w-[18px] h-[18px] flex-shrink-0"></i>
                        <span class="text-sm">จุดลงเวลา</span>
                    </a>

                    <a href="{{ route('attendance.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors duration-150 cursor-pointer {{ request()->routeIs('attendance.index') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-muted hover:bg-surface-50 hover:text-text' }}">
                        <i data-lucide="clock" class="w-[18px] h-[18px] flex-shrink-0"></i>
                        <span class="text-sm">ประวัติการเข้างาน</span>
                    </a>

                    <a href="{{ route('reports.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors duration-150 cursor-pointer {{ request()->routeIs('reports.*') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-muted hover:bg-surface-50 hover:text-text' }}">
                        <i data-lucide="bar-chart-3" class="w-[18px] h-[18px] flex-shrink-0"></i>
                        <span class="text-sm">รายงาน</span>
                    </a>
                @endif

                <!-- Section: My Menu (ทุกคนเห็น) -->
                <div class="pt-5 pb-1.5 px-3 text-[11px] font-semibold text-muted/60 uppercase tracking-widest">เมนูส่วนตัว</div>

                <a href="{{ route('attendance.my') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors duration-150 cursor-pointer {{ request()->routeIs('attendance.my') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-muted hover:bg-surface-50 hover:text-text' }}">
                    <i data-lucide="calendar-check" class="w-[18px] h-[18px] flex-shrink-0"></i>
                    <span class="text-sm">ประวัติของฉัน</span>
                </a>

                <a href="#"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors duration-150 cursor-pointer text-muted hover:bg-surface-50 hover:text-text">
                    <i data-lucide="settings" class="w-[18px] h-[18px] flex-shrink-0"></i>
                    <span class="text-sm">ตั้งค่า</span>
                </a>
            </nav>

            <!-- User Footer (Logout) -->
            <div class="p-3 border-t border-primary-50">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-3 w-full px-3 py-2.5 text-muted hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors duration-150 cursor-pointer">
                        <i data-lucide="log-out" class="w-[18px] h-[18px]"></i>
                        <span class="text-sm">ออกจากระบบ</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Overlay for Mobile -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false"
            x-transition:enter="transition-opacity ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-primary-950/20 backdrop-blur-sm z-40 md:hidden" style="display: none;"></div>

        <!-- 2. Main Content Wrapper -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden bg-background">

            <!-- Top Navbar -->
            <header class="h-16 bg-white/80 backdrop-blur-md border-b border-primary-100/40 flex items-center justify-between px-4 md:px-8 z-30 sticky top-0">
                <!-- Mobile Toggle -->
                <button @click="sidebarOpen = !sidebarOpen"
                    class="p-2 text-muted hover:text-text hover:bg-surface-50 rounded-xl transition-colors duration-150 focus:outline-none md:hidden cursor-pointer">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>

                <!-- Page Title -->
                <h1 class="hidden md:block text-lg font-semibold text-text">@yield('title', 'Dashboard')</h1>

                <!-- Right Actions -->
                <div class="flex items-center gap-3">
                    <!-- Notification -->
                    <button class="relative p-2 text-muted hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-colors duration-150 cursor-pointer" aria-label="Notifications">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        <span class="absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-accent-500 ring-2 ring-white"></span>
                    </button>

                    <!-- User Profile Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-3 focus:outline-none group cursor-pointer pl-3 border-l border-primary-100/60">
                            <div class="w-9 h-9 rounded-xl bg-primary-100 text-primary-700 flex items-center justify-center font-semibold text-sm uppercase group-hover:bg-primary-200 transition-colors duration-150">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="hidden md:block text-left">
                                <span class="block text-sm font-semibold text-text leading-tight group-hover:text-primary-700 transition-colors duration-150">{{ Auth::user()->name }}</span>
                                <span class="block text-xs text-muted capitalize leading-tight">{{ Auth::user()->role }}</span>
                            </div>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-muted hidden md:block"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" @click.away="open = false"
                            class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg py-1.5 border border-primary-100/60 z-50"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95" style="display: none;">

                            <div class="px-4 py-3 border-b border-primary-50">
                                <p class="text-sm font-semibold text-text">บัญชีผู้ใช้</p>
                                <p class="text-xs text-muted truncate mt-0.5">{{ Auth::user()->email }}</p>
                            </div>

                            <a href="{{ route('profile.edit') }}"
                                class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-muted hover:bg-surface-50 hover:text-primary-700 transition-colors duration-150 cursor-pointer">
                                <i data-lucide="user" class="w-4 h-4"></i>
                                โปรไฟล์
                            </a>
                            <a href="#"
                                class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-muted hover:bg-surface-50 hover:text-primary-700 transition-colors duration-150 cursor-pointer">
                                <i data-lucide="settings" class="w-4 h-4"></i>
                                ตั้งค่า
                            </a>

                            <div class="border-t border-primary-50 mt-1 pt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="flex items-center gap-2.5 w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors duration-150 cursor-pointer">
                                        <i data-lucide="log-out" class="w-4 h-4"></i>
                                        ออกจากระบบ
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- 3. Content Area -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto px-4 py-6 md:px-8 md:py-8">
                @yield('content')
            </main>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</body>

</html>