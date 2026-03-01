<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Face Attendance') }}</title>


    <!-- Google Fonts: Kanit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 font-sans antialiased text-text">

    <!-- App Container -->
    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">

        <!-- 1. Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-72 bg-card border-r border-slate-200/60 transition-transform duration-300 ease-in-out md:relative md:translate-x-0 flex flex-col">

            <!-- Logo -->
            <div class="h-20 flex items-center px-8 border-b border-slate-50">
                <div class="flex items-center gap-3 font-bold text-xl tracking-wide text-indigo-600">
                    <div class="w-10 h-10 bg-indigo-50/50 rounded-xl flex items-center justify-center text-indigo-600">
                        <x-heroicon-o-viewfinder-circle class="text-xl w-5" />
                    </div>
                    <span class="text-text font-bold font-mono">FaceSystem</span>
                </div>
            </div>

            <!-- Menu Items -->
            <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">

                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('dashboard') ? 'bg-indigo-50/50 text-primary-700 font-medium shadow-sm' : 'text-indigo-600/70 hover:bg-slate-50 hover:text-slate-900' }}">
                    <x-heroicon-o-chart-pie
                        class="w-5 text-center {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-primary-400 group-hover:text-text/80' }}" />
                    <span>แดชบอร์ด</span>
                </a>

                <!-- Management (Admin Only) -->
                @if(auth()->user()->role === 'admin')
                    <div class="pt-6 pb-2 px-4 text-xs font-semibold text-primary-400 uppercase tracking-wider">
                        การจัดการข้อมูล</div>

                    <a href="{{ route('employees.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('employees.*') ? 'bg-indigo-50/50 text-primary-700 font-medium shadow-sm' : 'text-indigo-600/70 hover:bg-slate-50 hover:text-slate-900' }}">
                        <x-heroicon-o-users
                            class="w-5 text-center {{ request()->routeIs('employees.*') ? 'text-indigo-600' : 'text-primary-400 group-hover:text-text/80' }}" />
                        <span>พนักงาน</span>
                    </a>

                    <a href="{{ route('devices.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('devices.*') ? 'bg-indigo-50/50 text-primary-700 font-medium shadow-sm' : 'text-indigo-600/70 hover:bg-slate-50 hover:text-slate-900' }}">
                        <x-heroicon-o-device-tablet
                            class="w-5 text-center {{ request()->routeIs('devices.*') ? 'text-indigo-600' : 'text-primary-400 group-hover:text-text/80' }}" />
                        <span>อุปกรณ์</span>
                    </a>

                    <a href="{{ route('users.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('users.*') ? 'bg-indigo-50/50 text-primary-700 font-medium shadow-sm' : 'text-indigo-600/70 hover:bg-slate-50 hover:text-slate-900' }}">
                        <x-heroicon-o-cog-8-tooth
                            class="w-5 text-center {{ request()->routeIs('users.*') ? 'text-indigo-600' : 'text-primary-400 group-hover:text-text/80' }}" />
                        <span>ผู้ใช้งานระบบ</span>
                    </a>

                    <a href="{{ route('face.register') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('face.register') ? 'bg-indigo-50/50 text-primary-700 font-medium shadow-sm' : 'text-indigo-600/70 hover:bg-slate-50 hover:text-slate-900' }}">
                        <x-heroicon-o-identification
                            class="w-5 text-center {{ request()->routeIs('face.register') ? 'text-indigo-600' : 'text-primary-400 group-hover:text-text/80' }}" />
                        <span>ลงทะเบียนใบหน้า</span>
                    </a>

                    <!-- Student Attendance Section -->
                    <div class="pt-6 pb-2 px-4 text-xs font-semibold text-primary-400 uppercase tracking-wider">🎓
                        นักเรียนหลักสูตร</div>

                    <a href="{{ route('courses.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('courses.*') ? 'bg-indigo-50/50 text-primary-700 font-medium shadow-sm' : 'text-indigo-600/70 hover:bg-slate-50 hover:text-slate-900' }}">
                        <x-heroicon-o-book-open
                            class="w-5 text-center {{ request()->routeIs('courses.*') ? 'text-indigo-600' : 'text-primary-400 group-hover:text-text/80' }}" />
                        <span>หลักสูตร</span>
                    </a>

                    <a href="{{ route('students.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('students.*') ? 'bg-indigo-50/50 text-primary-700 font-medium shadow-sm' : 'text-indigo-600/70 hover:bg-slate-50 hover:text-slate-900' }}">
                        <x-heroicon-o-academic-cap
                            class="w-5 text-center {{ request()->routeIs('students.*') ? 'text-indigo-600' : 'text-primary-400 group-hover:text-text/80' }}" />
                        <span>นักเรียน</span>
                    </a>

                    <a href="{{ route('student-reports.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('student-reports.*') ? 'bg-indigo-50/50 text-primary-700 font-medium shadow-sm' : 'text-indigo-600/70 hover:bg-slate-50 hover:text-slate-900' }}">
                        <x-heroicon-o-chart-bar
                            class="w-5 text-center {{ request()->routeIs('student-reports.*') ? 'text-indigo-600' : 'text-primary-400 group-hover:text-text/80' }}" />
                        <span>รายงานนักเรียน</span>
                    </a>

                    <a href="{{ route('student.face.register') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('student.face.*') ? 'bg-emerald-50 text-emerald-700 font-medium shadow-sm' : 'text-indigo-600/70 hover:bg-slate-50 hover:text-slate-900' }}">
                        <x-heroicon-o-camera
                            class="w-5 text-center {{ request()->routeIs('student.face.*') ? 'text-emerald-600' : 'text-primary-400 group-hover:text-text/80' }}" />
                        <span>ลงทะเบียนใบหน้านักเรียน</span>
                    </a>
                @endif

                <!-- Monitoring & Attendance (Admin & HR) -->
                @if(in_array(auth()->user()->role, ['admin', 'hr']))
                    <div class="pt-6 pb-2 px-4 text-xs font-semibold text-primary-400 uppercase tracking-wider">การตรวจสอบ
                    </div>

                    <a href="{{ route('monitor.display') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('monitor.display') ? 'bg-indigo-50/50 text-primary-700 font-medium shadow-sm' : 'text-indigo-600/70 hover:bg-slate-50 hover:text-slate-900' }}">
                        <span class="relative flex items-center justify-center w-5">
                            <x-heroicon-o-computer-desktop
                                class="{{ request()->routeIs('monitor.display') ? 'text-indigo-600' : 'text-primary-400 group-hover:text-text/80' }} w-5" />
                        </span>
                        <span>จอภาพเรียลไทม์</span>
                    </a>

                    <a href="{{ route('monitor.scan') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('monitor.scan') ? 'bg-indigo-50/50 text-primary-700 font-medium shadow-sm' : 'text-indigo-600/70 hover:bg-slate-50 hover:text-slate-900' }}">
                        <span class="relative flex items-center justify-center w-5">
                            <x-heroicon-o-camera
                                class="{{ request()->routeIs('monitor.scan') ? 'text-amber-500' : 'text-primary-400 group-hover:text-amber-500' }} w-5" />
                            <span class="absolute top-0 right-0 -mt-1 -mr-1 flex h-2 w-2">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                            </span>
                        </span>
                        <span>จุดลงเวลา</span>
                    </a>

                    <a href="{{ route('attendance.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('attendance.index') ? 'bg-indigo-50/50 text-primary-700 font-medium shadow-sm' : 'text-indigo-600/70 hover:bg-slate-50 hover:text-slate-900' }}">
                        <x-heroicon-o-clock
                            class="w-5 text-center {{ request()->routeIs('attendance.index') ? 'text-indigo-600' : 'text-primary-400 group-hover:text-text/80' }}" />
                        <span>ประวัติการเข้างาน</span>
                    </a>

                    <a href="{{ route('reports.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('reports.*') ? 'bg-indigo-50/50 text-primary-700 font-medium shadow-sm' : 'text-indigo-600/70 hover:bg-slate-50 hover:text-slate-900' }}">
                        <x-heroicon-o-chart-pie
                            class="w-5 text-center {{ request()->routeIs('reports.*') ? 'text-indigo-600' : 'text-primary-400 group-hover:text-text/80' }}" />
                        <span>รายงาน</span>
                    </a>
                @endif

                <!-- Section: My Menu (ทุกคนเห็น) -->
                <div class="pt-6 pb-2 px-4 text-xs font-semibold text-primary-400 uppercase tracking-wider">เมนูส่วนตัว
                </div>

                <a href="{{ route('attendance.my') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('attendance.my') ? 'bg-indigo-50/50 text-primary-700 font-medium shadow-sm' : 'text-indigo-600/70 hover:bg-slate-50 hover:text-slate-900' }}">
                    <x-heroicon-o-calendar-days
                        class="w-5 text-center {{ request()->routeIs('attendance.my') ? 'text-indigo-600' : 'text-primary-400 group-hover:text-text/80' }}" />
                    <span>ประวัติของฉัน</span>
                </a>

                <a href="#"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group text-indigo-600/70 hover:bg-slate-50 hover:text-slate-900">
                    <x-heroicon-o-cog-6-tooth class="w-5 text-center text-primary-400 group-hover:text-text/80" />
                    <span>ตั้งค่า</span>
                </a>
            </nav>

            <!-- User Footer (Logout) -->
            <div class="p-4 border-t border-slate-50">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-3 w-full px-4 py-3 text-indigo-600/70 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all group">
                        <x-heroicon-o-arrow-right-on-rectangle class="w-5 text-center group-hover:text-rose-600" />
                        <span>ออกจากระบบ</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Overlay for Mobile -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false"
            x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-900/20 backdrop-blur-sm z-40 md:hidden" style="display: none;"></div>

        <!-- 2. Main Content Wrapper -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50">

            <!-- Top Navbar -->
            <header
                class="h-20 bg-card/80 backdrop-blur-md border-b border-slate-200/60 flex items-center justify-between px-8 z-30 sticky top-0">
                <!-- Mobile Toggle -->
                <button @click="sidebarOpen = !sidebarOpen"
                    class="text-indigo-600/70 hover:text-text focus:outline-none md:hidden">
                    <x-heroicon-o-bars-3 class="text-xl w-5" />
                </button>

                <!-- Page Title -->
                <h1 class="hidden md:block text-xl font-bold text-text font-bold font-mono">@yield('title', 'Dashboard')
                </h1>

                <!-- Right Actions -->
                <div class="flex items-center gap-6">
                    <!-- Notification -->
                    <button class="relative p-2 text-primary-400 hover:text-indigo-600 transition-colors">
                        <x-heroicon-o-bell class="text-xl w-5" />
                        <span
                            class="absolute top-1.5 right-1.5 h-2.5 w-2.5 rounded-full bg-rose-500 border-2 border-white"></span>
                    </button>

                    <!-- User Profile Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-3 focus:outline-none group">
                            <div
                                class="w-10 h-10 rounded-full bg-slate-100 text-text/80 flex items-center justify-center font-bold uppercase border border-slate-200 group-hover:border-primary-200 transition-colors">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="hidden md:block text-left">
                                <span
                                    class="block text-sm font-bold text-text leading-tight group-hover:text-primary-700 transition-colors">{{ Auth::user()->name }}</span>
                                <span
                                    class="block text-xs text-primary-400 capitalize leading-tight">{{ Auth::user()->role }}</span>
                            </div>
                            <x-heroicon-o-chevron-down
                                class="text-xs text-slate-300 group-hover:text-indigo-600/70 transition-colors w-5" />
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" @click.away="open = false"
                            class="absolute right-0 mt-3 w-56 bg-card rounded-2xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.1)] py-2 border border-slate-200/60 z-50 transform origin-top-right transition-all"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95" style="display: none;">

                            <div class="px-4 py-3 border-b border-slate-50 mb-1">
                                <p class="text-sm font-bold text-text font-bold font-mono">บัญชีผู้ใช้</p>
                                <p class="text-xs text-indigo-600/70 truncate">{{ Auth::user()->email }}</p>
                            </div>

                            <a href="{{ route('profile.edit') }}"
                                class="flex items-center gap-2 px-4 py-2.5 text-sm text-text/80 hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                                <x-heroicon-o-user class="w-5 text-center" /> โปรไฟล์
                            </a>
                            <a href="#"
                                class="flex items-center gap-2 px-4 py-2.5 text-sm text-text/80 hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                                <x-heroicon-o-star class="w-5 text-center" /> ตั้งค่า
                            </a>

                            <div class="border-t border-slate-50 mt-1 pt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="flex items-center gap-2 w-full text-left px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50 transition-colors">
                                        <x-heroicon-o-arrow-right-on-rectangle class="w-5 text-center" /> ออกจากระบบ
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- 3. Content Area -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-8">
                @yield('content')
            </main>
        </div>
    </div>
</body>

</html>