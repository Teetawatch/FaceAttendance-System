@extends('layouts.app')

@section('title', 'ภาพรวมระบบ')

@section('content')
<div class="space-y-8">
    
    <!-- Welcome Section (Minimal) -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-bold text-text font-bold font-mono tracking-tight font-mono">สวัสดี, {{ Auth::user()->name }} </h2>
            <p class="text-primary-600/70 mt-1">ยินดีต้อนรับกลับสู่ระบบจัดการเวลาเข้างาน</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm font-medium text-primary-600/70 bg-card px-4 py-2 rounded-full shadow-sm border border-primary-50">
                <i class="fa-regular fa-calendar mr-2"></i> {{ \Carbon\Carbon::now()->locale('th')->isoFormat('D MMMM YYYY') }}
            </span>
        </div>
    </div>

    <!-- Stats Grid (Light & Clean) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1: Total Employees -->
        <div class="bg-card rounded-2xl p-6 border border-primary-50 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] hover:shadow-[0_8px_30px_-4px_rgba(6,81,237,0.1)] transition-all duration-300 group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-primary-400">พนักงานทั้งหมด</p>
                    <h3 class="text-3xl font-bold text-text font-bold font-mono mt-2 font-mono">{{ $totalEmployees }}</h3>
                </div>
                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-lg group-hover:scale-110 transition-transform duration-300">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-primary-400">
                <span class="w-2 h-2 rounded-full bg-blue-500 mr-2"></span> Active Employees
            </div>
        </div>

        <!-- Card 2: Present Today -->
        <div class="bg-card rounded-2xl p-6 border border-primary-50 shadow-[0_2px_10px_-3px_rgba(16,185,129,0.1)] hover:shadow-[0_8px_30px_-4px_rgba(16,185,129,0.1)] transition-all duration-300 group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-primary-400">มาทำงานวันนี้</p>
                    <h3 class="text-3xl font-bold text-text font-bold font-mono mt-2 font-mono">{{ $presentToday }}</h3>
                </div>
                <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg group-hover:scale-110 transition-transform duration-300">
                    <i class="fa-solid fa-user-check"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-2">
                <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $presentDiff >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                    {{ $presentDiff >= 0 ? '+' : '' }}{{ $presentDiff }}
                </span>
                <span class="text-xs text-primary-400">จากเมื่อวาน</span>
            </div>
        </div>

        <!-- Card 3: Late Arrivals -->
        <div class="bg-card rounded-2xl p-6 border border-primary-50 shadow-[0_2px_10px_-3px_rgba(245,158,11,0.1)] hover:shadow-[0_8px_30px_-4px_rgba(245,158,11,0.1)] transition-all duration-300 group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-primary-400">มาสาย</p>
                    <h3 class="text-3xl font-bold text-text font-bold font-mono mt-2 font-mono">{{ $lateToday }}</h3>
                </div>
                <div class="w-10 h-10 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-lg group-hover:scale-110 transition-transform duration-300">
                    <i class="fa-solid fa-user-clock"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-primary-400">
                <span class="w-2 h-2 rounded-full bg-amber-500 mr-2"></span> Needs Attention
            </div>
        </div>

        <!-- Card 4: Devices -->
        <div class="bg-card rounded-2xl p-6 border border-primary-50 shadow-[0_2px_10px_-3px_rgba(139,92,246,0.1)] hover:shadow-[0_8px_30px_-4px_rgba(139,92,246,0.1)] transition-all duration-300 group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-primary-400">อุปกรณ์ออนไลน์</p>
                    <h3 class="text-3xl font-bold text-text font-bold font-mono mt-2 font-mono">{{ $activeDevices }}<span class="text-lg text-slate-300 font-normal">/{{ $totalDevices }}</span></h3>
                </div>
                <div class="w-10 h-10 rounded-full bg-violet-50 text-violet-600 flex items-center justify-center text-lg group-hover:scale-110 transition-transform duration-300">
                    <i class="fa-solid fa-server"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-primary-400">
                <span class="w-2 h-2 rounded-full bg-violet-500 mr-2 animate-pulse"></span> System Healthy
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Recent Scans (Clean List) -->
        <div class="lg:col-span-2 bg-card rounded-2xl border border-primary-50 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-50 flex items-center justify-between">
                <h3 class="font-bold text-text font-bold font-mono text-lg font-mono">การสแกนล่าสุด</h3>
                <a href="{{ route('attendance.index') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium transition-colors">ดูทั้งหมด</a>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($recentScans as $scan)
                <div class="px-6 py-4 flex items-center justify-between hover:bg-background/50 transition-colors group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-background flex items-center justify-center overflow-hidden border-2 border-white shadow-sm group-hover:border-primary-100 transition-colors">
                             @if($scan->employee && $scan->employee->photo_path)
                                <img src="{{ route('storage.file', ['path' => $scan->employee->photo_path]) }}" class="w-full h-full object-cover">
                             @else
                                <i class="fa-solid fa-user text-slate-300"></i>
                             @endif
                        </div>
                        <div>
                            <p class="font-bold text-text group-hover:text-primary-600 transition-colors">
                                {{ $scan->employee ? $scan->employee->first_name . ' ' . $scan->employee->last_name : 'Unknown' }}
                            </p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-xs text-primary-400 bg-background px-2 py-0.5 rounded border border-primary-50">
                                    {{ $scan->employee ? $scan->employee->employee_code : '-' }}
                                </span>
                                <span class="text-xs text-primary-400 flex items-center">
                                    <i class="fa-solid fa-location-dot text-[10px] mr-1"></i> {{ $scan->device ? $scan->device->location : 'Unknown' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wide {{ $scan->scan_type == 'in' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                            {{ $scan->scan_type }}
                        </span>
                        <p class="text-sm font-medium text-primary-600/70 mt-1 font-mono">
                            {{ $scan->scan_time->format('H:i') }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="px-6 py-12 text-center">
                    <div class="w-16 h-16 bg-background rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-regular fa-clock text-slate-300 text-2xl"></i>
                    </div>
                    <p class="text-primary-600/70 font-medium">ยังไม่มีข้อมูลการสแกน</p>
                    <p class="text-primary-400 text-sm mt-1">ข้อมูลการเข้างานวันนี้จะแสดงที่นี่</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Right Column: Status & Quick Links -->
        <div class="space-y-6">
            
            <!-- Quick Actions (Minimal Cards) -->
            <div>
                <h3 class="font-bold text-text font-bold font-mono mb-4 px-1 font-mono">เมนูด่วน</h3>
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('monitor') }}" class="bg-card p-4 rounded-2xl border border-primary-50 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 text-center group">
                        <div class="w-12 h-12 mx-auto bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl mb-3 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-desktop"></i>
                        </div>
                        <span class="text-sm font-bold text-text">จอภาพ</span>
                    </a>
                    <a href="{{ route('employees.index') }}" class="bg-card p-4 rounded-2xl border border-primary-50 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 text-center group">
                        <div class="w-12 h-12 mx-auto bg-pink-50 text-pink-600 rounded-xl flex items-center justify-center text-xl mb-3 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-user-plus"></i>
                        </div>
                        <span class="text-sm font-bold text-text">เพิ่มพนักงาน</span>
                    </a>
                </div>
            </div>

            <!-- System Status (Compact) -->
            <div class="bg-card rounded-2xl border border-primary-50 shadow-sm p-6">
                <h3 class="font-bold text-text font-bold font-mono mb-4 font-mono">สถานะระบบ</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-background flex items-center justify-center text-primary-400">
                                <i class="fa-solid fa-database"></i>
                            </div>
                            <span class="text-sm font-medium text-text/80">Database</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="text-xs font-bold text-emerald-600">ONLINE</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-background flex items-center justify-center text-primary-400">
                                <i class="fa-solid fa-network-wired"></i>
                            </div>
                            <span class="text-sm font-medium text-text/80">WebSocket</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="text-xs font-bold text-emerald-600">ACTIVE</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection