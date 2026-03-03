@extends('layouts.kiosk')

@section('title', 'จุดลงเวลาอัจฉริยะ (AI Kiosk)')

@section('content')
    <!-- Load face-api.js -->
    <script src="{{ asset('js/face-api.min.js') }}"></script>

    <div x-data="kioskApp()" x-init="initKiosk()"
        class="relative min-h-screen flex flex-col overflow-hidden bg-[#020617] selection:bg-indigo-500/30 text-slate-200"
        style="font-family: 'Kanit', sans-serif;">

        <!-- Dynamic Aurora Background -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div
                class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-indigo-500/15 rounded-full blur-[120px] mix-blend-screen animate-blob">
            </div>
            <div
                class="absolute top-[-10%] right-[-10%] w-[500px] h-[500px] bg-purple-500/15 rounded-full blur-[120px] mix-blend-screen animate-blob animation-delay-2000">
            </div>
            <div
                class="absolute bottom-[-10%] left-[20%] w-[500px] h-[500px] bg-emerald-500/10 rounded-full blur-[120px] mix-blend-screen animate-blob animation-delay-4000">
            </div>
            <!-- Grid Pattern -->
            <div
                class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTTAgNDBWMGg0MCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSJyZ2JhKDI1NSwyNTUsMjU1LDAuMDMpIiBzdHJva2Utd2lkdGg9IjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-30">
            </div>
        </div>

        <!-- Header -->
        <header class="relative z-10 px-6 py-5 lg:px-12 lg:py-6">
            <div class="max-w-[1400px] mx-auto flex flex-col lg:flex-row items-center justify-between gap-5">
                <!-- Logo -->
                <div class="flex items-center gap-5 group cursor-default">
                    <div class="relative">
                        <div
                            class="absolute inset-0 bg-gradient-to-tr from-indigo-500 to-purple-500 rounded-2xl blur-lg opacity-40 group-hover:opacity-70 transition-opacity duration-500">
                        </div>
                        <div
                            class="relative w-14 h-14 bg-[#0F172A]/90 backdrop-blur-md rounded-2xl border border-white/10 flex items-center justify-center shadow-2xl ring-1 ring-white/10 group-hover:scale-105 transition-transform duration-500">
                            <img src="{{ asset('images/logonavy.png') }}" alt="Logo"
                                class="w-9 h-9 object-contain drop-shadow-lg">
                        </div>
                    </div>
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-white tracking-tight">
                            <span
                                class="bg-clip-text text-transparent bg-gradient-to-r from-white via-indigo-200 to-slate-400">ระบบลงเวลาด้วยไบโอเมตริก
                                (Biometric Attendance System)</span>
                        </h1>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            <p class="text-slate-400 text-sm font-medium">Smart Face Recognition System</p>
                        </div>
                    </div>
                </div>

                <!-- Right: Stats + Clock -->
                <div class="flex items-center gap-4">
                    <!-- Unified Badge -->
                    <div
                        class="hidden md:flex items-center gap-3 px-5 py-2.5 bg-[#0F172A]/60 backdrop-blur-md rounded-full border border-white/5 hover:bg-slate-800/40 transition-colors duration-200 cursor-default">
                        <div class="flex -space-x-2">
                            <div
                                class="w-8 h-8 rounded-full bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center">
                                <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                </svg>
                            </div>
                            <div
                                class="w-8 h-8 rounded-full bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                                </svg>
                            </div>
                        </div>
                        <span class="text-slate-300 text-sm font-medium">จุดสแกนรวม</span>
                    </div>

                    <!-- Clock -->
                    <div class="group relative">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-indigo-500/20 to-purple-500/20 rounded-2xl blur-md opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                        </div>
                        <div
                            class="relative px-6 py-3 bg-[#0F172A]/70 backdrop-blur-xl rounded-2xl border border-white/10 text-center min-w-[140px]">
                            <div class="text-3xl font-bold font-mono text-white tracking-widest tabular-nums"
                                x-text="currentTime">--:--</div>
                            <div class="text-indigo-300/80 text-xs font-semibold uppercase tracking-wider mt-0.5"
                                x-text="currentDate">-- --- ----</div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Interface -->
        <main class="relative z-10 flex-1 px-6 lg:px-12 pb-8 flex flex-col h-full overflow-hidden">
            <div class="max-w-[1400px] mx-auto w-full grid grid-cols-1 lg:grid-cols-12 gap-8 h-full min-h-[600px]">

                <!-- Left: Camera Feed (Span 7) -->
                <div class="lg:col-span-7 flex flex-col h-full">
                    <div
                        class="relative flex-1 rounded-[2rem] overflow-hidden border border-white/10 shadow-2xl bg-[#0F172A]/80 group">

                        <!-- Corner Accents -->
                        <div
                            class="absolute top-5 left-5 w-14 h-14 border-t-2 border-l-2 border-indigo-500/40 rounded-tl-xl z-20">
                        </div>
                        <div
                            class="absolute top-5 right-5 w-14 h-14 border-t-2 border-r-2 border-indigo-500/40 rounded-tr-xl z-20">
                        </div>
                        <div
                            class="absolute bottom-5 left-5 w-14 h-14 border-b-2 border-l-2 border-indigo-500/40 rounded-bl-xl z-20">
                        </div>
                        <div
                            class="absolute bottom-5 right-5 w-14 h-14 border-b-2 border-r-2 border-indigo-500/40 rounded-br-xl z-20">
                        </div>

                        <!-- Video Feed -->
                        <div class="absolute inset-2 rounded-[1.5rem] overflow-hidden bg-black relative">
                            <video x-ref="videoElement" autoplay playsinline muted
                                class="w-full h-full object-cover transform -scale-x-100 opacity-90"></video>
                            <canvas x-ref="overlayElement"
                                class="absolute inset-0 w-full h-full pointer-events-none transform -scale-x-100"></canvas>
                            <!-- Scanner Effect -->
                            <div class="absolute inset-0 animate-scan-line pointer-events-none"></div>
                        </div>

                        <!-- Status Pill -->
                        <div class="absolute top-7 left-1/2 -translate-x-1/2 z-30">
                            <div
                                class="px-5 py-2.5 bg-[#020617]/80 backdrop-blur-md rounded-full border border-white/10 shadow-lg flex items-center gap-3">
                                <span class="relative flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75"
                                        :class="isModelsLoading ? 'bg-amber-400' : 'bg-emerald-500'"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3"
                                        :class="isModelsLoading ? 'bg-amber-400' : 'bg-emerald-500'"></span>
                                </span>
                                <span class="text-sm font-medium text-white tracking-wide"
                                    x-text="statusMessage">กำลังโหลดระบบ...</span>
                            </div>
                        </div>

                        <!-- Blink Overlay -->
                        <div x-show="livenessStatus === 'waiting_for_blink'"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                            class="absolute inset-0 flex items-center justify-center z-40 bg-[#020617]/70 backdrop-blur-sm">
                            <div
                                class="text-center p-8 bg-[#0F172A]/90 border border-amber-500/30 rounded-3xl shadow-2xl animate-float">
                                <div
                                    class="w-20 h-20 mx-auto bg-amber-500/20 rounded-2xl flex items-center justify-center mb-6 ring-1 ring-amber-500/40">
                                    <svg class="w-10 h-10 text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-white">กรุณากระพริบตา</h3>
                                <p class="text-slate-400 mt-2">เพื่อยืนยันตัวตน</p>
                            </div>
                        </div>

                        <!-- Success Overlay -->
                        <div x-show="showFaceDetected" x-transition:enter="transition ease-out duration-500"
                            x-transition:enter-start="opacity-0 scale-110" x-transition:enter-end="opacity-100 scale-100"
                            class="absolute inset-0 flex items-center justify-center z-40 bg-emerald-950/40 backdrop-blur-sm">
                            <div class="absolute inset-0 border-[6px] border-emerald-500/50 rounded-[2rem] animate-pulse">
                            </div>
                        </div>
                    </div>

                    <!-- Camera Toggle -->
                    <div class="mt-5 flex justify-end">
                        <button @click="showConfig = !showConfig; if(showConfig) getCameras()"
                            class="flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-[#1E293B]/60 hover:bg-slate-700/80 border border-white/10 text-slate-400 hover:text-white transition-all duration-200 cursor-pointer"
                            title="เลือกกล้อง">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                            <span class="text-sm font-medium">เลือกกล้อง</span>
                        </button>
                    </div>

                    <!-- Camera Selection Panel -->
                    <div x-show="showConfig" x-collapse
                        class="mt-3 p-5 bg-[#0F172A]/90 backdrop-blur-xl rounded-2xl border border-white/10 shadow-2xl">
                        <label
                            class="text-xs font-bold text-indigo-400 uppercase tracking-wider mb-3 block">เลือกกล้อง</label>
                        <div class="space-y-2">
                            <template x-for="(camera, index) in cameras" :key="camera.deviceId">
                                <button @click="switchCamera(camera.deviceId)"
                                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl border transition-all duration-200 cursor-pointer text-left"
                                    :class="selectedCamera === camera.deviceId
                                            ? 'bg-indigo-600/20 border-indigo-500/50 text-white'
                                            : 'bg-[#020617] border-slate-700 text-slate-300 hover:border-slate-500 hover:bg-slate-800/50'">
                                    <svg class="w-5 h-5 shrink-0"
                                        :class="selectedCamera === camera.deviceId ? 'text-indigo-400' : 'text-slate-500'"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                                    </svg>
                                    <span class="text-sm font-medium truncate"
                                        x-text="camera.label || 'Camera ' + (index + 1)"></span>
                                    <span x-show="selectedCamera === camera.deviceId" class="ml-auto shrink-0">
                                        <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                    </span>
                                </button>
                            </template>
                            <p x-show="cameras.length === 0" class="text-slate-500 text-sm text-center py-3">
                                กำลังโหลดรายการกล้อง...</p>
                        </div>
                    </div>
                </div>

                <!-- Right: Info & Results (Span 5) -->
                <div class="lg:col-span-5 flex flex-col h-full relative">

                    <!-- Welcome / Idle State -->
                    <div x-show="!showSuccessPopup" x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="h-full bg-[#0F172A]/50 backdrop-blur-xl rounded-[2rem] border border-white/5 p-8 flex flex-col items-center justify-center text-center relative overflow-hidden group">

                        <div
                            class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-purple-500/5 group-hover:from-indigo-500/10 group-hover:to-purple-500/10 transition-colors duration-700">
                        </div>

                        <div
                            class="relative z-10 w-24 h-24 mb-8 rounded-3xl bg-gradient-to-br from-[#1E293B] to-[#0F172A] shadow-2xl flex items-center justify-center ring-1 ring-white/10 group-hover:scale-110 transition-transform duration-500 overflow-hidden">
                            <template x-if="lastScan">
                                <img :src="lastScan.snapshot_url || lastScan.photo_url" class="w-full h-full object-cover"
                                    alt="Last scan">
                            </template>
                            <template x-if="!lastScan">
                                <svg class="w-12 h-12 text-indigo-400/60" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                            </template>
                        </div>

                        <h2 class="text-3xl font-bold text-white mb-3 relative z-10">พร้อมสแกน</h2>
                        <p class="text-slate-400 max-w-xs mx-auto text-lg leading-relaxed relative z-10">
                            กรุณามองที่กล้องเพื่อลงเวลา
                            <span class="text-indigo-400 text-sm font-medium mt-2 block">ระบบ AI พร้อมทำงาน</span>
                        </p>

                        <!-- Indicators -->
                        <div class="mt-10 grid grid-cols-2 gap-4 w-full max-w-xs relative z-10">
                            <div
                                class="bg-[#1E293B]/50 rounded-2xl p-4 border border-white/5 flex flex-col items-center gap-2">
                                <svg class="w-6 h-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                                </svg>
                                <span class="text-xs text-slate-400 font-medium tracking-wide">ปลอดภัย</span>
                            </div>
                            <div
                                class="bg-[#1E293B]/50 rounded-2xl p-4 border border-white/5 flex flex-col items-center gap-2">
                                <svg class="w-6 h-6 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                                </svg>
                                <span class="text-xs text-slate-400 font-medium tracking-wide">รวดเร็ว</span>
                            </div>
                        </div>
                    </div>

                    <!-- Success Card -->
                    <div x-show="showSuccessPopup"
                        x-transition:enter="transition cubic-bezier(0.34, 1.56, 0.64, 1) duration-500"
                        x-transition:enter-start="opacity-0 translate-x-12 scale-95"
                        x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute inset-0 h-full w-full bg-[#0F172A]/90 backdrop-blur-2xl rounded-[2rem] border border-emerald-500/30 overflow-hidden flex flex-col shadow-2xl shadow-emerald-500/10">

                        <!-- Glow Header -->
                        <div
                            class="absolute top-0 left-0 right-0 h-40 bg-gradient-to-b from-emerald-500/20 to-transparent pointer-events-none">
                        </div>

                        <div class="flex-1 flex flex-col items-center justify-center p-8 relative z-10">
                            <!-- Success Check -->
                            <div class="relative mb-6">
                                <div class="absolute inset-0 bg-emerald-500/30 rounded-full blur-xl animate-pulse"></div>
                                <div
                                    class="w-16 h-16 bg-emerald-500 rounded-full flex items-center justify-center shadow-lg shadow-emerald-500/40 relative z-10">
                                    <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Profile Photo -->
                            <div class="relative w-36 h-36 mb-5 group">
                                <div
                                    class="absolute inset-0 -m-1.5 rounded-full border-2 border-emerald-500/50 border-dashed animate-spin-slow">
                                </div>
                                <div
                                    class="w-full h-full rounded-full overflow-hidden border-4 border-[#0F172A] bg-[#1E293B] shadow-2xl relative">
                                    <template x-if="lastScan?.photo_url || lastScan?.snapshot_url">
                                        <img :src="lastScan?.snapshot_url || lastScan?.photo_url"
                                            class="w-full h-full object-cover" alt="Profile">
                                    </template>
                                    <template x-if="!lastScan?.photo_url && !lastScan?.snapshot_url">
                                        <div class="w-full h-full flex items-center justify-center bg-[#1E293B]">
                                            <svg class="w-16 h-16 text-slate-500" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                            </svg>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- User Info -->
                            <h2 class="text-3xl font-bold text-white text-center mb-1 leading-tight"
                                x-text="lastScan?.name || 'ไม่รู้จัก'"></h2>
                            <p class="text-slate-400 font-medium text-lg mb-6"
                                x-text="lastScan?.employee_code || lastScan?.student_code || '-'"></p>

                            <!-- Stats Grid -->
                            <div class="grid grid-cols-2 gap-4 w-full">
                                <div class="bg-[#1E293B]/80 rounded-2xl p-4 text-center border border-white/5">
                                    <div class="text-slate-400 text-xs font-bold uppercase mb-1">เวลาที่สแกน</div>
                                    <div class="text-xl font-mono font-bold text-white" x-text="lastScan?.time || '--:--'">
                                        --:--</div>
                                </div>
                                <div class="bg-[#1E293B]/80 rounded-2xl p-4 text-center border border-white/5">
                                    <div class="text-slate-400 text-xs font-bold uppercase mb-1">สถานะ</div>
                                    <div class="text-xl font-bold"
                                        :class="lastScan?.is_late ? 'text-amber-400' : 'text-emerald-400'"
                                        x-text="lastScan?.status_text || 'เข้างานปกติ'">เข้างานปกติ</div>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Bar -->
                        <div class="h-1.5 w-full bg-gradient-to-r from-emerald-600 via-teal-500 to-emerald-600"></div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="relative z-10 py-5 text-center text-slate-500 text-sm">
            <p>&copy; {{ date('Y') }} ลงเวลาปฏิบัติราชการด้วยใบหน้า</p>
        </footer>
    </div>

    <script>
        function kioskApp() {
            return {
                currentTime: '--:--:--',
                currentDate: '--',
                stream: null,
                cameras: [],
                selectedCamera: '',
                isModelsLoading: true,
                statusMessage: 'กำลังโหลดระบบ AI...',
                faceMatcher: null,
                detectionInterval: null,
                lastScanTime: 0,
                livenessStatus: 'idle',
                blinkState: 'open',
                earThreshold: 0.25,
                blinkDetected: false,
                pendingEmployeeCode: null,
                showFaceDetected: false,
                detectedType: null,
                employeeCode: '',
                isLoading: false,
                showConfig: false,
                lastScan: null,
                showSuccessPopup: false,
                popupTimer: null,
                successAudio: new Audio("{{ asset('success.wav') }}".replace(/^http:/, location.protocol)),
                errorAudio: new Audio("{{ asset('error.wav') }}".replace(/^http:/, location.protocol)),

                async initKiosk() {
                    this.updateClock();
                    setInterval(() => this.updateClock(), 1000);

                    if (typeof window.Echo !== 'undefined') {
                        window.Echo.channel('scans')
                            .listen('.new-scan', (e) => {
                                this.handleNewScan(e.employee);
                            });
                    }

                    try {
                        let modelPath = "{{ asset('models') }}";
                        if (location.protocol === 'https:' && modelPath.startsWith('http:')) {
                            modelPath = modelPath.replace('http:', 'https:');
                        }
                        await Promise.all([
                            faceapi.loadTinyFaceDetectorModel(modelPath),
                            faceapi.loadFaceLandmarkModel(modelPath),
                            faceapi.loadFaceRecognitionModel(modelPath)
                        ]);
                        this.isModelsLoading = false;
                        this.statusMessage = 'ระบบพร้อมใช้งาน';
                        await this.loadLabeledImages();
                    } catch (error) {
                        console.error('Error loading models:', error);
                        this.statusMessage = 'โหลดโมเดลไม่สำเร็จ';
                    }
                    await this.startCamera();
                    // Enumerate cameras after stream is acquired
                    await this.getCameras();
                },

                updateClock() {
                    const now = new Date();
                    this.currentTime = now.toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                    this.currentDate = now.toLocaleDateString('th-TH', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' });
                },

                async loadLabeledImages() {
                    this.statusMessage = 'กำลังเรียนรู้ใบหน้า...';
                    try {
                        const staffFacesUrl = "{{ route('api.employees.faces') }}".replace(/^http:/, location.protocol);
                        const studentFacesUrl = "{{ route('api.students.faces') }}".replace(/^http:/, location.protocol);

                        const [staffResponse, studentResponse] = await Promise.all([
                            axios.get(staffFacesUrl),
                            axios.get(studentFacesUrl)
                        ]);

                        const staffMembers = staffResponse.data.data || [];
                        const students = studentResponse.data.data || [];

                        const staffDescriptors = await Promise.all(
                            staffMembers.map(async (employee) => {
                                try {
                                    let photoUrl = employee.photo_url;
                                    if (location.protocol === 'https:' && photoUrl.startsWith('http:')) {
                                        photoUrl = photoUrl.replace('http:', 'https:');
                                    }
                                    const img = await faceapi.fetchImage(photoUrl);
                                    const detections = await faceapi.detectSingleFace(img, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptor();
                                    if (!detections) return null;
                                    return new faceapi.LabeledFaceDescriptors('STAFF_' + employee.employee_code, [detections.descriptor]);
                                } catch (err) { return null; }
                            })
                        );

                        const studentDescriptors = await Promise.all(
                            students.map(async (student) => {
                                try {
                                    let photoUrl = student.photo_url;
                                    if (location.protocol === 'https:' && photoUrl.startsWith('http:')) {
                                        photoUrl = photoUrl.replace('http:', 'https:');
                                    }
                                    const img = await faceapi.fetchImage(photoUrl);
                                    const detections = await faceapi.detectSingleFace(img, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptor();
                                    if (!detections) return null;
                                    return new faceapi.LabeledFaceDescriptors('STUDENT_' + student.student_code, [detections.descriptor]);
                                } catch (err) { return null; }
                            })
                        );

                        const allDescriptors = [...staffDescriptors, ...studentDescriptors].filter(d => d !== null);
                        if (allDescriptors.length > 0) {
                            this.faceMatcher = new faceapi.FaceMatcher(allDescriptors, 0.35);
                            const validStaff = staffDescriptors.filter(d => d !== null).length;
                            const validStudents = studentDescriptors.filter(d => d !== null).length;
                            this.statusMessage = `พร้อมใช้งาน (${validStaff} บุคลากร, ${validStudents} นักเรียน)`;
                        } else {
                            this.statusMessage = 'ไม่พบข้อมูลใบหน้าในระบบ';
                        }
                    } catch (error) {
                        console.error('Error loading labeled images:', error);
                        this.statusMessage = 'โหลดข้อมูลใบหน้าไม่สำเร็จ';
                    }
                },

                async startCamera() {
                    if (this.stream) this.stopCamera();
                    try {
                        const constraints = { video: this.selectedCamera ? { deviceId: { exact: this.selectedCamera } } : true };
                        this.stream = await navigator.mediaDevices.getUserMedia(constraints);
                        const video = this.$refs.videoElement;
                        video.srcObject = this.stream;
                        video.onloadedmetadata = () => { video.play(); this.startFaceDetection(); };
                        await this.getCameras();
                    } catch (err) {
                        console.error("Error accessing camera:", err);
                        this.statusMessage = 'ไม่สามารถเข้าถึงกล้องได้';
                    }
                },

                startFaceDetection() {
                    const video = this.$refs.videoElement;
                    const canvas = this.$refs.overlayElement;
                    if (this.detectionInterval) clearInterval(this.detectionInterval);

                    this.detectionInterval = setInterval(async () => {
                        if (!video.videoWidth) return;
                        const displaySize = { width: video.videoWidth, height: video.videoHeight };
                        faceapi.matchDimensions(canvas, displaySize);
                        const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptors();
                        const resizedDetections = faceapi.resizeResults(detections, displaySize);
                        canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);

                        if (this.faceMatcher) {
                            const results = resizedDetections.map(d => this.faceMatcher.findBestMatch(d.descriptor));
                            if (results.length === 0) {
                                if (this.livenessStatus === 'waiting_for_blink') {
                                    this.livenessStatus = 'idle';
                                    this.pendingEmployeeCode = null;
                                }
                                this.showFaceDetected = false;
                            }

                            results.forEach((result, i) => {
                                const box = resizedDetections[i].detection.box;
                                const ctx = canvas.getContext('2d');
                                const { x, y, width, height } = box;
                                const lineLen = 20, lineWidth = 4;
                                ctx.strokeStyle = '#6366f1';
                                ctx.lineWidth = lineWidth;
                                ctx.beginPath();
                                ctx.moveTo(x, y + lineLen); ctx.lineTo(x, y); ctx.lineTo(x + lineLen, y);
                                ctx.moveTo(x + width - lineLen, y); ctx.lineTo(x + width, y); ctx.lineTo(x + width, y + lineLen);
                                ctx.moveTo(x + width, y + height - lineLen); ctx.lineTo(x + width, y + height); ctx.lineTo(x + width - lineLen, y + height);
                                ctx.moveTo(x + lineLen, y + height); ctx.lineTo(x, y + height); ctx.lineTo(x, y + height - lineLen);
                                ctx.stroke();

                                if (result.label !== 'unknown' && !this.isLoading) {
                                    const now = Date.now();
                                    if (now - this.lastScanTime > 5000) {
                                        this.livenessStatus = 'success';
                                        this.statusMessage = 'พบใบหน้า กำลังประมวลผล...';
                                        this.showFaceDetected = true;
                                        this.employeeCode = result.label;
                                        this.submitScan();
                                        this.lastScanTime = now;
                                        setTimeout(() => { this.livenessStatus = 'idle'; this.showFaceDetected = false; }, 2000);
                                    }
                                }
                            });
                        }
                    }, 100);
                },

                async getCameras() {
                    try {
                        const devices = await navigator.mediaDevices.enumerateDevices();
                        this.cameras = devices.filter(device => device.kind === 'videoinput');
                        if (this.cameras.length > 0 && !this.selectedCamera) {
                            const videoTrack = this.stream?.getVideoTracks()[0];
                            this.selectedCamera = videoTrack?.getSettings().deviceId || this.cameras[0].deviceId;
                        }
                    } catch (err) { console.error("Error listing cameras:", err); }
                },

                stopCamera() {
                    if (this.stream) { this.stream.getTracks().forEach(track => track.stop()); this.stream = null; }
                    if (this.detectionInterval) { clearInterval(this.detectionInterval); this.detectionInterval = null; }
                },

                saveConfig() {
                    this.showConfig = false;
                },

                switchCamera(deviceId) {
                    this.selectedCamera = deviceId;
                    this.startCamera();
                },

                async submitScan() {
                    if (!this.employeeCode) return;
                    this.isLoading = true;
                    this.statusMessage = 'กำลังบันทึกข้อมูล...';

                    try {
                        const canvas = document.createElement('canvas');
                        const video = this.$refs.videoElement;
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        canvas.getContext('2d').drawImage(video, 0, 0);
                        const snapshot = canvas.toDataURL('image/jpeg');

                        const isStudent = this.employeeCode.startsWith('STUDENT_');
                        const isStaff = this.employeeCode.startsWith('STAFF_');
                        let actualCode = this.employeeCode;
                        if (isStudent) { actualCode = this.employeeCode.replace('STUDENT_', ''); this.detectedType = 'student'; }
                        else if (isStaff) { actualCode = this.employeeCode.replace('STAFF_', ''); this.detectedType = 'staff'; }

                        const scanUrl = isStudent
                            ? "{{ route('api.kiosk.student.scan.store') }}".replace(/^http:/, location.protocol)
                            : "{{ route('api.kiosk.scan.store') }}".replace(/^http:/, location.protocol);

                        const scanPayload = isStudent
                            ? { student_code: actualCode, snapshot }
                            : { employee_code: actualCode, snapshot };

                        const response = await axios.post(scanUrl, scanPayload);
                        if (response.data.success) {
                            this.employeeCode = '';
                            const typeLabel = isStudent ? 'นักเรียน' : 'บุคลากร';
                            this.statusMessage = `บันทึกสำเร็จ! (${typeLabel})`;
                            this.successAudio.play().catch(() => { });
                            if (response.data.data) this.handleNewScan(response.data.data);
                            setTimeout(() => { this.statusMessage = 'ระบบพร้อมใช้งาน'; }, 2000);
                        }
                    } catch (error) {
                        console.error(error);
                        this.statusMessage = 'เกิดข้อผิดพลาด';
                        this.errorAudio.play().catch(() => { });
                    } finally {
                        this.isLoading = false;
                    }
                },

                handleNewScan(data) {
                    this.lastScan = data;
                    if (this.popupTimer) clearTimeout(this.popupTimer);
                    this.showSuccessPopup = true;
                    this.popupTimer = setTimeout(() => { this.showSuccessPopup = false; }, 5000);
                }
            }
        }
    </script>

    <style>
        @keyframes blob {
            0% {
                transform: translate(0px, 0px) scale(1);
            }

            33% {
                transform: translate(30px, -50px) scale(1.1);
            }

            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }

            100% {
                transform: translate(0px, 0px) scale(1);
            }
        }

        .animate-blob {
            animation: blob 10s ease-in-out infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }

        .animation-delay-4000 {
            animation-delay: 4s;
        }

        @keyframes scan-line {
            0% {
                transform: translateY(-100%);
            }

            50% {
                transform: translateY(100%);
            }

            100% {
                transform: translateY(-100%);
            }
        }

        .animate-scan-line {
            animation: scan-line 4s ease-in-out infinite;
            background: linear-gradient(to bottom, transparent, rgba(99, 102, 241, 0.15), transparent);
            height: 100%;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes spin-slow {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin-slow {
            animation: spin-slow 12s linear infinite;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Input autofill fix */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px #020617 inset !important;
            -webkit-text-fill-color: white !important;
        }

        /* Reduced motion */
        @media (prefers-reduced-motion: reduce) {

            .animate-blob,
            .animate-scan-line,
            .animate-float,
            .animate-spin-slow,
            .animate-pulse,
            .animate-ping {
                animation: none !important;
            }
        }
    </style>
@endsection