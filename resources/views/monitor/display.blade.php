@extends('layouts.app')

@section('title', 'จอภาพเรียลไทม์')

@section('content')
<!-- Load face-api.js (Only if needed for display, otherwise can remove, but keeping for compatibility if we add features later) -->
<script src="{{ asset('js/face-api.min.js') }}"></script>

<div x-data="monitorApp()" x-init="initMonitor()" class="h-[calc(100vh-8rem)]">
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 h-full">
        
        <!-- Left Column: Display Hero Section -->
        <div class="lg:col-span-1 flex flex-col gap-6 h-full">
            
            <!-- Mode 1: MONITOR DISPLAY (Hero Section) -->
            <div class="flex-1 bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 p-8 flex flex-col items-center justify-center text-center relative overflow-hidden transition-all">
                <!-- Pulse Animation Background (Active when scan comes) -->
                <div x-show="justScanned" x-transition.opacity.duration.1000ms class="absolute inset-0 bg-emerald-50/50 z-0" style="display: none;"></div>
                
                <div class="relative z-10 w-full flex flex-col items-center">
                    <h3 class="text-slate-400 font-medium uppercase tracking-widest text-xs mb-8 bg-slate-50 px-3 py-1 rounded-full border border-slate-100">รายการล่าสุด</h3>
                    
                    <!-- Profile Image -->
                    <div class="relative mb-8 group">
                        <div class="w-56 h-56 rounded-full border-8 border-white shadow-2xl overflow-hidden bg-slate-50 flex items-center justify-center relative z-10">
                            <!-- Prefer Snapshot, fallback to Profile Photo -->
                            <template x-if="latestScan.snapshot_url || latestScan.photo_url">
                                <img :src="latestScan.snapshot_url || latestScan.photo_url" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            </template>
                            <template x-if="!latestScan.snapshot_url && !latestScan.photo_url">
                                <i class="fa-solid fa-user text-7xl text-slate-200"></i>
                            </template>
                        </div>
                        <!-- Decorative Ring -->
                        <div class="absolute inset-0 rounded-full border border-slate-100 scale-110 -z-0"></div>
                        <div class="absolute inset-0 rounded-full border border-slate-50 scale-125 -z-0"></div>

                        <!-- Status Badge -->
                        <div class="absolute bottom-4 right-4 px-6 py-2 rounded-2xl text-white font-bold shadow-lg text-xl capitalize z-20 border-4 border-white transform transition-transform group-hover:scale-105"
                             :class="latestScan.is_late ? 'bg-amber-500' : 'bg-emerald-500'"
                             x-text="latestScan.status_text || latestScan.scan_type || '-'">
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="space-y-2 mb-8">
                        <h2 class="text-3xl font-bold text-slate-800" x-text="latestScan.name || 'รอรับข้อมูล...'"></h2>
                        <p class="text-slate-500 text-lg flex items-center justify-center gap-2">
                            <i class="fa-solid fa-location-dot text-slate-300"></i>
                            <span x-text="latestScan.device || 'ระบบพร้อมใช้งาน'"></span>
                        </p>
                    </div>

                    <!-- Time -->
                    <div class="text-6xl font-mono font-bold text-slate-700 tracking-tight bg-slate-50 px-8 py-4 rounded-2xl border border-slate-100 shadow-inner" x-text="latestScan.time || '--:--:--'"></div>
                </div>
            </div>

        </div>

        <!-- Right: Recent Scans List -->
        <div class="lg:col-span-2 bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 flex flex-col overflow-hidden h-full">
            <div class="px-8 py-6 border-b border-slate-50 flex justify-between items-center bg-white sticky top-0 z-10">
                <div>
                    <h3 class="font-bold text-slate-800 text-xl">ประวัติการเข้างานล่าสุด</h3>
                    <p class="text-slate-400 text-sm mt-1">ข้อมูล Real-time จากทุกจุดลงเวลา</p>
                </div>
                <div class="flex items-center gap-2 text-xs font-bold text-emerald-600 px-4 py-2 bg-emerald-50 rounded-full border border-emerald-100 shadow-sm">
                    <span class="relative flex h-2.5 w-2.5">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-500 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    เชื่อมต่อแล้ว
                </div>
            </div>
            
            <div class="flex-1 overflow-y-auto p-0 relative custom-scrollbar">
                 <!-- List Items -->
                 <ul class="divide-y divide-slate-50">
                    <template x-for="scan in history" :key="scan.id">
                        <li class="px-8 py-5 hover:bg-slate-50/80 transition-all duration-300 flex items-center justify-between animate-fade-in-down group cursor-default border-l-4 border-transparent hover:border-primary-500">
                            <div class="flex items-center gap-5">
                                <div class="w-14 h-14 rounded-full bg-slate-50 flex items-center justify-center overflow-hidden border-2 border-white shadow-sm group-hover:border-primary-100 transition-colors">
                                    <!-- Prefer Snapshot, fallback to Profile Photo -->
                                    <template x-if="scan.snapshot_url || scan.photo_url">
                                        <img :src="scan.snapshot_url || scan.photo_url" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!scan.snapshot_url && !scan.photo_url">
                                        <i class="fa-solid fa-user text-slate-300 text-xl"></i>
                                    </template>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-700 text-lg group-hover:text-primary-700 transition-colors" x-text="scan.name"></p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-xs text-slate-400 bg-slate-100 px-2 py-0.5 rounded border border-slate-200 flex items-center gap-1">
                                            <i class="fa-solid fa-location-dot text-[10px]"></i>
                                            <span x-text="scan.device"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide mb-1 shadow-sm"
                                      :class="scan.is_late ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700'"
                                      x-text="scan.status_text || scan.scan_type || scan.type"></span>
                                <p class="text-sm font-medium text-slate-500" 
                                   :class="scan.status_color"
                                   x-text="scan.datetime_th || scan.time"></p>
                            </div>
                        </li>
                    </template>
                 </ul>
                 
                 <!-- Empty State -->
                 <div x-show="history.length === 0" class="absolute inset-0 flex flex-col items-center justify-center text-slate-300 bg-slate-50/30">
                     <div class="w-24 h-24 bg-white rounded-full shadow-sm flex items-center justify-center mb-4">
                        <i class="fa-solid fa-satellite-dish text-4xl text-slate-200"></i>
                     </div>
                     <p class="font-medium text-slate-400">กำลังรอรับข้อมูล...</p>
                     <p class="text-sm text-slate-300 mt-1">ข้อมูลการสแกนจะปรากฏที่นี่ทันที</p>
                 </div>
            </div>
        </div>
    </div>
</div>

<script>
    function monitorApp() {
        return {
            latestScan: {},
            history: [],
            justScanned: false,

            async initMonitor() {
                // 1. Subscribe to Pusher
                if (typeof window.Echo !== 'undefined') {
                    window.Echo.channel('scans')
                        .listen('.new-scan', (e) => {
                            this.handleNewScan(e.employee);
                        });
                }
            },

            handleNewScan(data) {
                this.latestScan = data;
                data.id = Date.now() + Math.random(); 
                this.history.unshift(data);
                if (this.history.length > 20) this.history.pop();

                this.justScanned = true;
                setTimeout(() => { this.justScanned = false; }, 2000);
            }
        }
    }
</script>

<style>
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translate3d(0, -20px, 0);
        }
        to {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }
    }
    .animate-fade-in-down {
        animation-name: fadeInDown;
        animation-duration: 0.5s;
        animation-fill-mode: both;
    }
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9; 
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1; 
        border-radius: 3px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8; 
    }
</style>
@endsection
