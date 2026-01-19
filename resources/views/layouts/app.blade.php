<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Face Attendance') }}</title>


    <!-- Google Fonts: Kanit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800">

    <!-- App Container -->
    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">

        <!-- 1. Sidebar --> 
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-slate-100 transition-transform duration-300 ease-in-out md:relative md:translate-x-0 flex flex-col">
            
            <!-- Logo -->
            <div class="h-20 flex items-center px-8 border-b border-slate-50">
                <div class="flex items-center gap-3 font-bold text-xl tracking-wide text-primary-600">
                    <div class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center text-primary-600">
                        <i class="fa-solid fa-face-viewfinder text-xl"></i>
                    </div>
                    <span class="text-slate-800">FaceSystem</span>
                </div>
            </div>

            <!-- Menu Items -->
            <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
                
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('dashboard') ? 'bg-primary-50 text-primary-700 font-medium shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i class="fa-solid fa-gauge-high w-5 text-center {{ request()->routeIs('dashboard') ? 'text-primary-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                    <span>แดชบอร์ด</span>
                </a>

                <!-- Management (Admin Only) -->
                @if(auth()->user()->role === 'admin')
                <div class="pt-6 pb-2 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">การจัดการข้อมูล</div>
                
                <a href="{{ route('employees.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('employees.*') ? 'bg-primary-50 text-primary-700 font-medium shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i class="fa-solid fa-users w-5 text-center {{ request()->routeIs('employees.*') ? 'text-primary-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                    <span>พนักงาน</span>
                </a>
                
                <a href="{{ route('devices.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('devices.*') ? 'bg-primary-50 text-primary-700 font-medium shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i class="fa-solid fa-tablet-screen-button w-5 text-center {{ request()->routeIs('devices.*') ? 'text-primary-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                    <span>อุปกรณ์</span>
                </a>

                <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('users.*') ? 'bg-primary-50 text-primary-700 font-medium shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i class="fa-solid fa-users-gear w-5 text-center {{ request()->routeIs('users.*') ? 'text-primary-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                    <span>ผู้ใช้งานระบบ</span>
                </a>

                <a href="{{ route('face.register') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('face.register') ? 'bg-primary-50 text-primary-700 font-medium shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i class="fa-solid fa-id-card w-5 text-center {{ request()->routeIs('face.register') ? 'text-primary-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                    <span>ลงทะเบียนใบหน้า</span>
                </a>

                <!-- Student Attendance Section -->
                <div class="pt-6 pb-2 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">🎓 นักเรียนหลักสูตร</div>
                
                <a href="{{ route('courses.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('courses.*') ? 'bg-primary-50 text-primary-700 font-medium shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i class="fa-solid fa-book w-5 text-center {{ request()->routeIs('courses.*') ? 'text-primary-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                    <span>หลักสูตร</span>
                </a>
                
                <a href="{{ route('students.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('students.*') ? 'bg-primary-50 text-primary-700 font-medium shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i class="fa-solid fa-user-graduate w-5 text-center {{ request()->routeIs('students.*') ? 'text-primary-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                    <span>นักเรียน</span>
                </a>
                
                <a href="{{ route('student-reports.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('student-reports.*') ? 'bg-primary-50 text-primary-700 font-medium shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i class="fa-solid fa-chart-column w-5 text-center {{ request()->routeIs('student-reports.*') ? 'text-primary-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                    <span>รายงานนักเรียน</span>
                </a>
                
                <a href="{{ route('student.face.register') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('student.face.*') ? 'bg-emerald-50 text-emerald-700 font-medium shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i class="fa-solid fa-camera w-5 text-center {{ request()->routeIs('student.face.*') ? 'text-emerald-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                    <span>ลงทะเบียนใบหน้านักเรียน</span>
                </a>
                @endif

                <!-- Monitoring & Attendance (Admin & HR) -->
                @if(in_array(auth()->user()->role, ['admin', 'hr']))
                <div class="pt-6 pb-2 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">การตรวจสอบ</div>
                
                <a href="{{ route('monitor.display') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('monitor.display') ? 'bg-primary-50 text-primary-700 font-medium shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                    <span class="relative flex items-center justify-center w-5">
                        <i class="fa-solid fa-desktop {{ request()->routeIs('monitor.display') ? 'text-primary-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                    </span>
                    <span>จอภาพเรียลไทม์</span>
                </a>

                <a href="{{ route('monitor.scan') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('monitor.scan') ? 'bg-primary-50 text-primary-700 font-medium shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                    <span class="relative flex items-center justify-center w-5">
                        <i class="fa-solid fa-camera {{ request()->routeIs('monitor.scan') ? 'text-amber-500' : 'text-slate-400 group-hover:text-amber-500' }}"></i>
                        <span class="absolute top-0 right-0 -mt-1 -mr-1 flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                        </span>
                    </span>
                    <span>จุดลงเวลา</span>
                </a>
                
                <a href="{{ route('attendance.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('attendance.index') ? 'bg-primary-50 text-primary-700 font-medium shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i class="fa-solid fa-clock-rotate-left w-5 text-center {{ request()->routeIs('attendance.index') ? 'text-primary-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                    <span>ประวัติการเข้างาน</span>
                </a>
                
                <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('reports.*') ? 'bg-primary-50 text-primary-700 font-medium shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i class="fa-solid fa-chart-pie w-5 text-center {{ request()->routeIs('reports.*') ? 'text-primary-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                    <span>รายงาน</span>
                </a>
                @endif

                <!-- Section: My Menu (ทุกคนเห็น) -->
                <div class="pt-6 pb-2 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">เมนูส่วนตัว</div>
                
                <a href="{{ route('attendance.my') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('attendance.my') ? 'bg-primary-50 text-primary-700 font-medium shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i class="fa-solid fa-calendar-check w-5 text-center {{ request()->routeIs('attendance.my') ? 'text-primary-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                    <span>ประวัติของฉัน</span>
                </a>
                
                <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group text-slate-500 hover:bg-slate-50 hover:text-slate-900">
                    <i class="fa-solid fa-gear w-5 text-center text-slate-400 group-hover:text-slate-600"></i>
                    <span>ตั้งค่า</span>
                </a>
            </nav>

            <!-- User Footer (Logout) -->
            <div class="p-4 border-t border-slate-50">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 w-full px-4 py-3 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all group">
                        <i class="fa-solid fa-right-from-bracket w-5 text-center group-hover:text-rose-600"></i>
                        <span>ออกจากระบบ</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Overlay for Mobile -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/20 backdrop-blur-sm z-40 md:hidden" style="display: none;"></div>

        <!-- 2. Main Content Wrapper -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50">
            
            <!-- Top Navbar -->
            <header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-100 flex items-center justify-between px-8 z-30 sticky top-0">
                <!-- Mobile Toggle -->
                <button @click="sidebarOpen = !sidebarOpen" class="text-slate-500 hover:text-slate-700 focus:outline-none md:hidden">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>

                <!-- Page Title -->
                <h1 class="hidden md:block text-xl font-bold text-slate-800">@yield('title', 'Dashboard')</h1>

                <!-- Right Actions -->
                <div class="flex items-center gap-6">
                    <!-- Notification -->
                    <button class="relative p-2 text-slate-400 hover:text-primary-600 transition-colors">
                        <i class="fa-solid fa-bell text-xl"></i>
                        <span class="absolute top-1.5 right-1.5 h-2.5 w-2.5 rounded-full bg-rose-500 border-2 border-white"></span>
                    </button>

                    <!-- User Profile Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-3 focus:outline-none group">
                            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold uppercase border border-slate-200 group-hover:border-primary-200 transition-colors">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="hidden md:block text-left">
                                <span class="block text-sm font-bold text-slate-700 leading-tight group-hover:text-primary-700 transition-colors">{{ Auth::user()->name }}</span>
                                <span class="block text-xs text-slate-400 capitalize leading-tight">{{ Auth::user()->role }}</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-xs text-slate-300 group-hover:text-slate-500 transition-colors"></i>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div x-show="open" @click.away="open = false" 
                             class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.1)] py-2 border border-slate-100 z-50 transform origin-top-right transition-all"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             style="display: none;">
                            
                            <div class="px-4 py-3 border-b border-slate-50 mb-1">
                                <p class="text-sm font-bold text-slate-800">บัญชีผู้ใช้</p>
                                <p class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</p>
                            </div>

                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-primary-600 transition-colors">
                                <i class="fa-regular fa-user w-5 text-center"></i> โปรไฟล์
                            </a>
                            <a href="#" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-primary-600 transition-colors">
                                <i class="fa-solid fa-sliders w-5 text-center"></i> ตั้งค่า
                            </a>
                            
                            <div class="border-t border-slate-50 mt-1 pt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-2 w-full text-left px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50 transition-colors">
                                        <i class="fa-solid fa-right-from-bracket w-5 text-center"></i> ออกจากระบบ
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