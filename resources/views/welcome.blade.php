@extends('layouts.app')

@section('title', 'ภาพรวมระบบ')

@section('content')
<div class="space-y-6">
    
    <!-- Welcome Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="section-title">สวัสดี, {{ Auth::user()->name }}</h2>
            <p class="section-subtitle">ยินดีต้อนรับกลับสู่ระบบจัดการเวลาเข้างาน</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm font-medium text-muted bg-white px-4 py-2 rounded-xl border border-primary-100/60 flex items-center gap-2">
                <i data-lucide="calendar" class="w-4 h-4 text-primary-400"></i>
                {{ \Carbon\Carbon::now()->locale('th')->isoFormat('D MMMM YYYY') }}
            </span>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Employees -->
        <div class="card-hover p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">พนักงานทั้งหมด</p>
                    <h3 class="text-3xl font-bold text-text mt-1.5 font-mono">{{ $totalEmployees }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center">
                    <i data-lucide="users" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center text-xs text-muted">
                <span class="w-1.5 h-1.5 rounded-full bg-primary-500 mr-2"></span> Active Employees
            </div>
        </div>

        <!-- Card 2: Present Today -->
        <div class="card-hover p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">มาทำงานวันนี้</p>
                    <h3 class="text-3xl font-bold text-text mt-1.5 font-mono">{{ $presentToday }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="user-check" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2">
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $presentDiff >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }}">
                    {{ $presentDiff >= 0 ? '+' : '' }}{{ $presentDiff }}
                </span>
                <span class="text-xs text-muted">จากเมื่อวาน</span>
            </div>
        </div>

        <!-- Card 3: Late Arrivals -->
        <div class="card-hover p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">มาสาย</p>
                    <h3 class="text-3xl font-bold text-text mt-1.5 font-mono">{{ $lateToday }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i data-lucide="clock-alert" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center text-xs text-muted">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-2"></span> Needs Attention
            </div>
        </div>

        <!-- Card 4: Devices -->
        <div class="card-hover p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">อุปกรณ์ออนไลน์</p>
                    <h3 class="text-3xl font-bold text-text mt-1.5 font-mono">{{ $activeDevices }}<span class="text-lg text-muted/40 font-normal">/{{ $totalDevices }}</span></h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center">
                    <i data-lucide="tablet-smartphone" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center text-xs text-muted">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-2 animate-pulse"></span> System Healthy
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Recent Scans -->
        <div class="lg:col-span-2 card overflow-hidden">
            <div class="px-5 py-4 border-b border-primary-50 flex items-center justify-between">
                <h3 class="font-semibold text-text text-base">การสแกนล่าสุด</h3>
                <a href="{{ route('attendance.index') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium transition-colors duration-150 cursor-pointer">ดูทั้งหมด</a>
            </div>
            <div class="divide-y divide-primary-50/60">
                @forelse($recentScans as $scan)
                <div class="px-5 py-3.5 flex items-center justify-between hover:bg-surface-50/60 transition-colors duration-150 group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-surface-50 flex items-center justify-center overflow-hidden border border-primary-100/60">
                             @if($scan->employee && $scan->employee->photo_path)
                                <img src="{{ route('storage.file', ['path' => $scan->employee->photo_path]) }}" class="w-full h-full object-cover" alt="{{ $scan->employee->first_name }}">
                             @else
                                <i data-lucide="user" class="w-4 h-4 text-muted"></i>
                             @endif
                        </div>
                        <div>
                            <p class="font-semibold text-text text-sm group-hover:text-primary-700 transition-colors duration-150">
                                {{ $scan->employee ? $scan->employee->first_name . ' ' . $scan->employee->last_name : 'Unknown' }}
                            </p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-xs text-muted bg-surface-50 px-1.5 py-0.5 rounded font-mono">
                                    {{ $scan->employee ? $scan->employee->employee_code : '-' }}
                                </span>
                                <span class="text-xs text-muted flex items-center gap-1">
                                    <i data-lucide="map-pin" class="w-3 h-3"></i>
                                    {{ $scan->device ? $scan->device->location : 'Unknown' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="badge {{ $scan->scan_type == 'in' ? 'badge-success' : 'badge-warning' }} uppercase text-[10px] tracking-wide">
                            {{ $scan->scan_type }}
                        </span>
                        <p class="text-xs font-mono font-medium text-muted mt-1">
                            {{ $scan->scan_time->format('H:i') }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="px-5 py-12 text-center">
                    <div class="w-14 h-14 bg-surface-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <i data-lucide="scan-line" class="w-6 h-6 text-muted"></i>
                    </div>
                    <p class="text-text font-medium text-sm">ยังไม่มีข้อมูลการสแกน</p>
                    <p class="text-muted text-xs mt-1">ข้อมูลการเข้างานวันนี้จะแสดงที่นี่</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Right Column: Status & Quick Links -->
        <div class="space-y-4">
            
            <!-- Quick Actions -->
            <div>
                <h3 class="font-semibold text-text mb-3 text-sm">เมนูด่วน</h3>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('monitor') }}" class="card-hover p-4 text-center">
                        <div class="w-10 h-10 mx-auto bg-primary-50 text-primary-600 rounded-xl flex items-center justify-center mb-2.5">
                            <i data-lucide="monitor" class="w-5 h-5"></i>
                        </div>
                        <span class="text-sm font-semibold text-text">จอภาพ</span>
                    </a>
                    <a href="{{ route('employees.index') }}" class="card-hover p-4 text-center">
                        <div class="w-10 h-10 mx-auto bg-accent-50 text-accent-600 rounded-xl flex items-center justify-center mb-2.5">
                            <i data-lucide="user-plus" class="w-5 h-5"></i>
                        </div>
                        <span class="text-sm font-semibold text-text">เพิ่มพนักงาน</span>
                    </a>
                </div>
            </div>

            <!-- System Status -->
            <div class="card p-5">
                <h3 class="font-semibold text-text mb-4 text-sm">สถานะระบบ</h3>
                <div class="space-y-3.5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-surface-50 flex items-center justify-center text-muted">
                                <i data-lucide="database" class="w-4 h-4"></i>
                            </div>
                            <span class="text-sm text-text">Database</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span class="text-xs font-semibold text-emerald-600">ONLINE</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-surface-50 flex items-center justify-center text-muted">
                                <i data-lucide="radio" class="w-4 h-4"></i>
                            </div>
                            <span class="text-sm text-text">WebSocket</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span class="text-xs font-semibold text-emerald-600">ACTIVE</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection



