<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-text leading-tight tracking-tight">
            {{ __('Overview Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Welcome Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="section-title">Welcome back, {{ Auth::user()->name }}</h3>
                    <p class="section-subtitle">Here's what's happening today.</p>
                </div>
                <div class="flex items-center gap-3">
                    <button class="btn-secondary">
                        <i data-lucide="calendar" class="w-4 h-4"></i> Today
                    </button>
                    <button class="btn-primary">
                        <i data-lucide="download" class="w-4 h-4"></i> Export Report
                    </button>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Stat 1 -->
                <div class="card-hover p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center text-primary-600">
                            <i data-lucide="users" class="w-5 h-5"></i>
                        </div>
                        <span class="badge-success font-mono">+12%</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-muted mb-0.5">Total Students</p>
                        <h4 class="text-3xl font-bold font-mono text-text">1,248</h4>
                    </div>
                </div>

                <!-- Stat 2 -->
                <div class="card-hover p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600">
                            <i data-lucide="user-check" class="w-5 h-5"></i>
                        </div>
                        <span class="badge-success font-mono">+4.5%</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-muted mb-0.5">Present Today</p>
                        <h4 class="text-3xl font-bold font-mono text-text">1,180</h4>
                    </div>
                </div>

                <!-- Stat 3 -->
                <div class="card-hover p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center text-red-600">
                            <i data-lucide="user-x" class="w-5 h-5"></i>
                        </div>
                        <span class="badge-danger font-mono">-1.2%</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-muted mb-0.5">Absent Today</p>
                        <h4 class="text-3xl font-bold font-mono text-text">68</h4>
                    </div>
                </div>

                <!-- Stat 4 -->
                <div class="card-hover p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600">
                            <i data-lucide="clock" class="w-5 h-5"></i>
                        </div>
                        <span class="badge-neutral font-mono">---</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-muted mb-0.5">Pending Approval</p>
                        <h4 class="text-3xl font-bold font-mono text-text">15</h4>
                    </div>
                </div>
            </div>

            <!-- Main Grid content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Content: Chart Card -->
                <div class="lg:col-span-2 card p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-base font-semibold text-text">Attendance Trend</h4>
                        <div class="flex gap-3 items-center">
                            <span class="flex items-center gap-1.5 text-xs font-medium text-muted">
                                <span class="w-2.5 h-2.5 rounded bg-primary-500"></span> Present
                            </span>
                            <span class="flex items-center gap-1.5 text-xs font-medium text-muted">
                                <span class="w-2.5 h-2.5 rounded bg-red-400"></span> Absent
                            </span>
                        </div>
                    </div>
                    <div class="h-64 w-full flex items-end justify-between relative pt-8 pb-4 gap-1">
                        <!-- Horizontal Grid Lines -->
                        <div class="absolute inset-0 flex flex-col justify-between pt-8 pb-8 z-0">
                            <div class="border-b border-primary-100/50 w-full"></div>
                            <div class="border-b border-primary-100/50 w-full"></div>
                            <div class="border-b border-primary-100/50 w-full"></div>
                            <div class="border-b border-primary-100/60 w-full"></div>
                        </div>
                        <!-- Bars -->
                        @foreach([60, 80, 75, 95, 85, 100, 92] as $val)
                            <div class="flex-1 flex flex-col items-center gap-2 z-10 group cursor-pointer">
                                <div class="w-full max-w-[48px] bg-surface-50 rounded-t-lg relative flex flex-col justify-end h-48 overflow-hidden group-hover:bg-primary-50 transition-colors duration-150">
                                    <div class="w-full bg-primary-500 rounded-t-md transition-all duration-200 group-hover:bg-primary-600"
                                        style="height: {{ $val }}%"></div>
                                </div>
                                <span class="text-xs font-mono font-medium text-muted group-hover:text-primary-600 transition-colors duration-150">{{ ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'][$loop->index] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Right Content: Recent Scans -->
                <div class="card p-5 flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-base font-semibold text-text">Live Scans</h4>
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                        </span>
                    </div>

                    <div class="flex flex-col gap-3 flex-1 overflow-y-auto pr-1">
                        @foreach(['Sarawut S.', 'Piyapong N.', 'Nattamon T.', 'Supachai P.'] as $name)
                            <div class="flex items-center justify-between p-3 bg-surface-50 rounded-xl border border-primary-100/60 hover:border-primary-200 transition-colors duration-150 cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-primary-100 text-primary-700 flex items-center justify-center font-semibold text-sm">
                                        {{ substr($name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-text">{{ $name }}</p>
                                        <p class="text-xs text-muted">Class 4/A</p>
                                    </div>
                                </div>
                                <span class="badge-success font-mono text-xs">
                                    {{ \Carbon\Carbon::now()->subMinutes(rand(1, 45))->format('H:i') }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <button class="btn-secondary w-full mt-4 border-dashed">
                        View All Scans
                    </button>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>



