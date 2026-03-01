<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold font-mono text-2xl text-text leading-tight">
            {{ __('Overview Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Welcome Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-xl font-bold font-mono text-text">Welcome back, {{ Auth::user()->name }}! 👋</h3>
                    <p class="text-sm font-medium text-primary-500">Here's what's happening today.</p>
                </div>
                <div class="flex items-center gap-3">
                    <button
                        class="btn border border-primary-200 bg-card hover:bg-primary-50 text-primary-600 px-4 py-2 rounded-xl text-sm font-semibold transition-all shadow-sm flex items-center gap-2">
                        <i class="fa-solid fa-calendar-day"></i> Today
                    </button>
                    <button
                        class="btn bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-all shadow-md shadow-primary-500/20 flex items-center gap-2">
                        <i class="fa-solid fa-download"></i> Export Report
                    </button>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Stat 1 -->
                <div
                    class="bg-card rounded-2xl p-6 shadow-md border border-primary-50 hover:shadow-lg hover:-translate-y-1 transition-all cursor-pointer group">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 bg-primary-50 rounded-xl flex items-center justify-center text-primary-600 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-users text-xl"></i>
                        </div>
                        <span
                            class="inline-flex items-center gap-1 text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg text-xs font-bold font-mono">
                            <i class="fa-solid fa-arrow-trend-up"></i> +12%
                        </span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-primary-400 mb-1">Total Students</p>
                        <h4 class="text-3xl font-bold font-mono text-text">1,248</h4>
                    </div>
                </div>

                <!-- Stat 2 -->
                <div
                    class="bg-card rounded-2xl p-6 shadow-md border border-primary-50 hover:shadow-lg hover:-translate-y-1 transition-all cursor-pointer group">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-user-check text-xl"></i>
                        </div>
                        <span
                            class="inline-flex items-center gap-1 text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg text-xs font-bold font-mono">
                            <i class="fa-solid fa-arrow-trend-up"></i> +4.5%
                        </span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-primary-400 mb-1">Present Today</p>
                        <h4 class="text-3xl font-bold font-mono text-text">1,180</h4>
                    </div>
                </div>

                <!-- Stat 3 -->
                <div
                    class="bg-card rounded-2xl p-6 shadow-md border border-primary-50 hover:shadow-lg hover:-translate-y-1 transition-all cursor-pointer group">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 bg-rose-50 rounded-xl flex items-center justify-center text-rose-600 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-user-xmark text-xl"></i>
                        </div>
                        <span
                            class="inline-flex items-center gap-1 text-rose-600 bg-rose-50 px-2 py-1 rounded-lg text-xs font-bold font-mono">
                            <i class="fa-solid fa-arrow-trend-down"></i> -1.2%
                        </span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-primary-400 mb-1">Absent Today</p>
                        <h4 class="text-3xl font-bold font-mono text-text">68</h4>
                    </div>
                </div>

                <!-- Stat 4 -->
                <div
                    class="bg-card rounded-2xl p-6 shadow-md border border-primary-50 hover:shadow-lg hover:-translate-y-1 transition-all cursor-pointer group">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 bg-accent/20 rounded-xl flex items-center justify-center text-accent group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-clock-rotate-left text-xl"></i>
                        </div>
                        <span
                            class="inline-flex items-center gap-1 text-slate-500 bg-slate-50 px-2 py-1 rounded-lg text-xs font-bold font-mono">
                            ---
                        </span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-primary-400 mb-1">Pending Approval</p>
                        <h4 class="text-3xl font-bold font-mono text-text">15</h4>
                    </div>
                </div>
            </div>

            <!-- Main Grid content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Content: Chart Card -->
                <div class="lg:col-span-2 bg-card border border-primary-100 rounded-2xl shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-lg font-bold font-mono text-text">Attendance Trend</h4>
                        <div class="flex gap-2">
                            <span class="w-3 h-3 rounded-full bg-primary-500 mt-1"></span>
                            <span class="text-xs font-medium text-primary-400">Present</span>
                            <span class="w-3 h-3 rounded-full bg-rose-400 mt-1 ml-3"></span>
                            <span class="text-xs font-medium text-primary-400">Absent</span>
                        </div>
                    </div>
                    <!-- Mock line chart SVG based on design system request -->
                    <div class="h-64 w-full flex items-end justify-between relative pt-8 pb-4">
                        <!-- Horizontal Grid Lines -->
                        <div class="absolute inset-0 flex flex-col justify-between pt-8 pb-8 z-0">
                            <div class="border-b border-primary-100/50 w-full"></div>
                            <div class="border-b border-primary-100/50 w-full"></div>
                            <div class="border-b border-primary-100/50 w-full"></div>
                            <div class="border-b border-primary-50 w-full"></div>
                        </div>
                        <!-- Bars -->
                        @foreach([60, 80, 75, 95, 85, 100, 92] as $val)
                            <div class="w-8 md:w-12 flex flex-col items-center gap-2 z-10 group cursor-pointer">
                                <div
                                    class="w-full bg-primary-50 rounded-t-lg relative flex flex-col justify-end h-48 overflow-hidden group-hover:bg-primary-100 transition-colors">
                                    <div class="w-full bg-gradient-to-t from-primary-600 to-primary-400 rounded-t-sm transition-all duration-300 group-hover:bg-primary-500"
                                        style="height: {{ $val }}%"></div>
                                </div>
                                <span
                                    class="text-xs font-mono font-medium text-primary-400 group-hover:text-primary-600 transition-colors">{{ ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'][$loop->index] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Right Content: Recent Scans -->
                <div class="bg-card border border-primary-100 rounded-2xl shadow-md p-6 flex flex-col">
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-lg font-bold font-mono text-text">Live Scans</h4>
                        <span class="relative flex h-3 w-3">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                        </span>
                    </div>

                    <div class="flex flex-col gap-4 flex-1 overflow-y-auto pr-2">
                        @foreach(['Sarawut S.', 'Piyapong N.', 'Nattamon T.', 'Supachai P.'] as $name)
                            <div
                                class="flex items-center justify-between p-3 bg-background rounded-xl border border-primary-50 hover:border-primary-200 transition-colors cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center font-bold text-sm">
                                        {{ substr($name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-text">{{ $name }}</p>
                                        <p class="text-xs text-primary-400">Class 4/A</p>
                                    </div>
                                </div>
                                <span
                                    class="text-xs font-mono font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md">
                                    {{ \Carbon\Carbon::now()->subMinutes(rand(1, 45))->format('H:i') }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <button
                        class="w-full mt-4 py-2.5 bg-primary-50 hover:bg-primary-100 text-primary-700 font-semibold rounded-xl transition-colors text-sm border border-primary-100 border-dashed">
                        View All Scans
                    </button>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>