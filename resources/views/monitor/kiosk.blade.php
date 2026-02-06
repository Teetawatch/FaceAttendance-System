@extends('layouts.kiosk')

@section('title', 'จุดลงเวลาอัจฉริยะ (AI Kiosk)')

@section('content')
    <!-- Load face-api.js -->
    <script src="{{ asset('js/face-api.min.js') }}"></script>

    <div x-data="kioskApp()" x-init="initKiosk()"
        class="relative min-h-screen flex flex-col overflow-hidden bg-slate-950 font-sans selection:bg-indigo-500/30 text-slate-200">

        <!-- 🌌 Dynamic Aurora Background -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <!-- Gradient Blobs -->
            <div
                class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-indigo-500/20 rounded-full blur-[100px] mix-blend-screen animate-blob">
            </div>
            <div
                class="absolute top-[-10%] right-[-10%] w-[500px] h-[500px] bg-purple-500/20 rounded-full blur-[100px] mix-blend-screen animate-blob animation-delay-2000">
            </div>
            <div
                class="absolute bottom-[-10%] left-[20%] w-[500px] h-[500px] bg-emerald-500/20 rounded-full blur-[100px] mix-blend-screen animate-blob animation-delay-4000">
            </div>

            <!-- Grid Pattern Overlay -->
            <div
                class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTTAgNDBWMGg0MCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSJyZ2JhKDI1NSwyNTUsMjU1LDAuMDMpIiBzdHJva2Utd2lkdGg9IjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-30">
            </div>
        </div>

        <!-- 🟢 Header Section -->
        <header class="relative z-10 px-6 py-6 lg:px-12 lg:py-8">
            <div class="max-w-[1400px] mx-auto flex flex-col lg:flex-row items-center justify-between gap-6">

                <!-- Logo Area -->
                <div class="flex items-center gap-6 group cursor-default">
                    <div class="relative">
                        <div
                            class="absolute inset-0 bg-gradient-to-tr from-indigo-500 to-purple-500 rounded-2xl blur-lg opacity-40 group-hover:opacity-75 transition-opacity duration-500">
                        </div>
                        <div
                            class="relative w-16 h-16 bg-slate-900/90 backdrop-blur-md rounded-2xl border border-white/10 flex items-center justify-center shadow-2xl ring-1 ring-white/10 group-hover:scale-105 transition-transform duration-500">
                            <img src="{{ asset('images/logonavy.png') }}" alt="Logo"
                                class="w-10 h-10 object-contain drop-shadow-lg">
                        </div>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white tracking-tight flex items-center gap-3">
                            <span
                                class="bg-clip-text text-transparent bg-gradient-to-r from-white via-indigo-200 to-slate-400">ระบบลงเวลาปฏิบัติราชการด้วยใบหน้า</span>
                        </h1>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            <p class="text-slate-400 text-sm font-medium">Smart Face Recognition System</p>
                        </div>
                    </div>
                </div>

                <!-- Right Stats / Clock -->
                <div class="flex items-center gap-4">
                    <!-- Unified Badge -->
                    <div
                        class="hidden md:flex items-center gap-3 px-5 py-2.5 bg-slate-900/40 backdrop-blur-md rounded-full border border-white/5 hover:bg-slate-800/40 transition-colors">
                        <div class="flex -space-x-2">
                            <div
                                class="w-8 h-8 rounded-full bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center text-indigo-400 text-xs">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>
                            <div
                                class="w-8 h-8 rounded-full bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 text-xs">
                                <i class="fa-solid fa-user-graduate"></i>
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
                            class="relative px-6 py-3 bg-slate-900/60 backdrop-blur-xl rounded-2xl border border-white/10 text-center min-w-[140px]">
                            <div class="text-3xl font-bold font-mono text-white tracking-widest tabular-nums"
                                x-text="currentTime">--:--</div>
                            <div class="text-indigo-300/80 text-xs font-semibold uppercase tracking-wider mt-0.5"
                                x-text="currentDate">-- --- ----</div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- 🖥 Main Interface -->
        <main class="relative z-10 flex-1 px-6 lg:px-12 pb-12 flex flex-col h-full overflow-hidden">
            <div class="max-w-[1400px] mx-auto w-full grid grid-cols-1 lg:grid-cols-12 gap-8 h-full min-h-[600px]">

                <!-- 📷 Left: Camera Feed (Span 7) -->
                <div class="lg:col-span-7 flex flex-col h-full">
                    <div
                        class="relative flex-1 rounded-[2.5rem] overflow-hidden border border-white/10 shadow-2xl bg-slate-900/80 group">

                        <!-- Corner Accents -->
                        <div
                            class="absolute top-6 left-6 w-16 h-16 border-t-2 border-l-2 border-indigo-500/50 rounded-tl-2xl z-20 opacity-50">
                        </div>
                        <div
                            class="absolute top-6 right-6 w-16 h-16 border-t-2 border-r-2 border-indigo-500/50 rounded-tr-2xl z-20 opacity-50">
                        </div>
                        <div
                            class="absolute bottom-6 left-6 w-16 h-16 border-b-2 border-l-2 border-indigo-500/50 rounded-bl-2xl z-20 opacity-50">
                        </div>
                        <div
                            class="absolute bottom-6 right-6 w-16 h-16 border-b-2 border-r-2 border-indigo-500/50 rounded-br-2xl z-20 opacity-50">
                        </div>

                        <!-- Video Feed -->
                        <div class="absolute inset-2 rounded-[2rem] overflow-hidden bg-black relative">
                            <video x-ref="videoElement" autoplay playsinline muted
                                class="w-full h-full object-cover transform -scale-x-100 opacity-90"></video>

                            <!-- Overlay Canvas -->
                            <canvas x-ref="overlayElement"
                                class="absolute inset-0 w-full h-full pointer-events-none transform -scale-x-100"></canvas>

                            <!-- Scanner Effect -->
                            <div
                                class="absolute inset-0 bg-gradient-to-b from-indigo-500/0 via-indigo-500/10 to-indigo-500/0 h-full w-full animate-scan-line pointer-events-none">
                            </div>
                            <div class="absolute inset-0 box-shadow-inner pointer-events-none"></div>
                        </div>

                        <!-- Status Pill (Floating) -->
                        <div class="absolute top-8 left-1/2 -translate-x-1/2 z-30">
                            <div
                                class="px-5 py-2.5 bg-slate-950/70 backdrop-blur-md rounded-full border border-white/10 shadow-lg flex items-center gap-3">
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

                        <!-- Overlays: Blink & Success -->
                        <!-- Blink -->
                        <div x-show="livenessStatus === 'waiting_for_blink'"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                            class="absolute inset-0 flex items-center justify-center z-40 bg-slate-950/60 backdrop-blur-sm">
                            <div
                                class="text-center p-8 bg-slate-900/90 border border-amber-500/30 rounded-3xl shadow-2xl animate-float">
                                <div
                                    class="w-20 h-20 mx-auto bg-amber-500/20 rounded-2xl flex items-center justify-center mb-6 ring-1 ring-amber-500/40">
                                    <i class="fa-solid fa-eye text-4xl text-amber-400 animate-pulse"></i>
                                </div>
                                <h3 class="text-2xl font-bold text-white">กรุณากระพริบตา</h3>
                                <p class="text-slate-400 mt-2">เพื่อยืนยันตัวตน</p>
                            </div>
                        </div>

                        <!-- Success Overlay -->
                        <div x-show="showFaceDetected" x-transition:enter="transition ease-out duration-500"
                            x-transition:enter-start="opacity-0 scale-110" x-transition:enter-end="opacity-100 scale-100"
                            class="absolute inset-0 flex items-center justify-center z-40 bg-emerald-950/40 backdrop-blur-sm">
                            <div class="absolute inset-0 border-[6px] border-emerald-500/50 rounded-[2.5rem] animate-pulse">
                            </div>
                        </div>
                    </div>

                    <!-- Manual Input Bar -->
                    <div class="mt-6 flex gap-4">
                        <div class="flex-1 relative group">
                            <div
                                class="absolute -inset-0.5 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl opacity-50 group-hover:opacity-100 transition duration-500 blur">
                            </div>
                            <div class="relative flex items-center bg-slate-900 rounded-2xl p-1">
                                <div class="pl-4 pr-3 text-slate-500">
                                    <i class="fa-solid fa-keyboard text-lg"></i>
                                </div>
                                <input type="text" x-model="employeeCode" @keyup.enter="submitScan()"
                                    placeholder="กรอกรหัสพนักงาน..."
                                    class="w-full bg-transparent border-none text-white placeholder-slate-500 focus:ring-0 text-lg font-medium h-12">
                            </div>
                        </div>
                        <button @click="submitScan()" :disabled="isLoading || !employeeCode"
                            class="relative px-8 rounded-2xl font-bold text-white shadow-lg overflow-hidden group disabled:opacity-50 disabled:cursor-not-allowed">
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-violet-600 group-hover:from-indigo-500 group-hover:to-violet-500 transition-all duration-300">
                            </div>
                            <span class="relative flex items-center gap-2">
                                <span x-show="!isLoading">ตกลง</span>
                                <i x-show="!isLoading"
                                    class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                                <i x-show="isLoading" class="fa-solid fa-spinner fa-spin"></i>
                            </span>
                        </button>

                        <!-- Settings Toggle -->
                        <button @click="showConfig = !showConfig"
                            class="w-14 h-14 rounded-2xl bg-slate-800/50 hover:bg-slate-700/80 border border-white/10 flex items-center justify-center text-slate-400 hover:text-white transition-all cursor-pointer">
                            <i class="fa-solid fa-sliders text-xl" :class="showConfig ? 'text-indigo-400' : ''"></i>
                        </button>
                    </div>

                    <!-- Config Panel -->
                    <div x-show="showConfig" x-collapse
                        class="mt-4 p-6 bg-slate-900/90 backdrop-blur-xl rounded-3xl border border-white/10 shadow-2xl">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-indigo-400 uppercase tracking-wider">รหัสอุปกรณ์
                                    (Device ID)</label>
                                <input type="text" x-model="deviceCode"
                                    class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-white focus:border-indigo-500 focus:ring-indigo-500/20 transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-indigo-400 uppercase tracking-wider">API Token</label>
                                <input type="password" x-model="apiToken"
                                    class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-white focus:border-indigo-500 focus:ring-indigo-500/20 transition-all">
                            </div>
                            <div class="md:col-span-2 space-y-2">
                                <label class="text-xs font-bold text-indigo-400 uppercase tracking-wider">เลือกกล้อง</label>
                                <select x-model="selectedCamera" @change="startCamera()"
                                    class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-white focus:border-indigo-500 focus:ring-indigo-500/20 transition-all">
                                    <template x-for="camera in cameras" :key="camera.deviceId">
                                        <option :value="camera.deviceId" x-text="camera.label || 'Camera ' + ($index + 1)">
                                        </option>
                                    </template>
                                </select>
                            </div>
                        </div>
                        <button @click="saveConfig()"
                            class="mt-6 w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/20 transition-all">
                            บันทึกการตั้งค่า
                        </button>
                    </div>
                </div>

                <!-- 📊 Right: Info & Results (Span 5) -->
                <div class="lg:col-span-5 flex flex-col h-full relative">

                    <!-- Welcome / Idle State -->
                    <div x-show="!showSuccessPopup" x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="h-full bg-slate-900/40 backdrop-blur-xl rounded-[2.5rem] border border-white/5 p-8 flex flex-col items-center justify-center text-center relative overflow-hidden group">

                        <!-- Decor Background -->
                        <div
                            class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-purple-500/5 group-hover:from-indigo-500/10 group-hover:to-purple-500/10 transition-colors duration-1000">
                        </div>

                        <div
                            class="relative z-10 w-24 h-24 mb-8 rounded-3xl bg-gradient-to-br from-slate-800 to-slate-900 shadow-2xl flex items-center justify-center ring-1 ring-white/10 group-hover:scale-110 transition-transform duration-500">
                            <i
                                class="fa-solid fa-face-viewfinder text-5xl text-indigo-400 group-hover:text-indigo-300 transition-colors"></i>
                        </div>

                        <h2 class="text-3xl font-bold text-white mb-3">พร้อมสแกน</h2>
                        <p class="text-slate-400 max-w-xs mx-auto text-lg leading-relaxed">
                            กรุณามองที่กล้องเพื่อลงเวลา
                            <br class="hidden xl:block">
                            <span class="text-indigo-400 text-sm font-medium mt-2 block">ระบบ AI พร้อมทำงาน</span>
                        </p>

                        <!-- Indicators -->
                        <div class="mt-12 grid grid-cols-2 gap-4 w-full max-w-xs">
                            <div class="bg-slate-800/50 rounded-2xl p-4 border border-white/5 flex flex-col items-center">
                                <i class="fa-solid fa-shield-halved text-emerald-400 text-2xl mb-2"></i>
                                <span class="text-xs text-slate-400 font-medium tracking-wide">ปลอดภัย</span>
                            </div>
                            <div class="bg-slate-800/50 rounded-2xl p-4 border border-white/5 flex flex-col items-center">
                                <i class="fa-solid fa-bolt text-amber-400 text-2xl mb-2"></i>
                                <span class="text-xs text-slate-400 font-medium tracking-wide">รวดเร็ว</span>
                            </div>
                        </div>
                    </div>

                    <!-- 🎉 Success Card (Result) -->
                    <div x-show="showSuccessPopup"
                        x-transition:enter="transition cubic-bezier(0.34, 1.56, 0.64, 1) duration-500"
                        x-transition:enter-start="opacity-0 translate-x-12 scale-95"
                        x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute inset-0 h-full w-full bg-slate-900/80 backdrop-blur-2xl rounded-[2.5rem] border border-emerald-500/30 overflow-hidden flex flex-col shadow-2xl shadow-emerald-500/10">

                        <!-- Glow Header -->
                        <div
                            class="absolute top-0 left-0 right-0 h-40 bg-gradient-to-b from-emerald-500/20 to-transparent pointer-events-none">
                        </div>

                        <div class="flex-1 flex flex-col items-center justify-center p-8 relative z-10">

                            <!-- Success Check -->
                            <div class="relative mb-8">
                                <div class="absolute inset-0 bg-emerald-500/30 rounded-full blur-xl animate-pulse"></div>
                                <div
                                    class="w-20 h-20 bg-emerald-500 rounded-full flex items-center justify-center shadow-lg shadow-emerald-500/40 relative z-10">
                                    <i class="fa-solid fa-check text-4xl text-white"></i>
                                </div>
                            </div>

                            <!-- Profile Photo -->
                            <div class="relative w-40 h-40 mb-6 group">
                                <!-- Ring -->
                                <div
                                    class="absolute inset-0 -m-1.5 rounded-full border-2 border-emerald-500/50 border-dashed animate-spin-slow">
                                </div>
                                <div
                                    class="w-full h-full rounded-full overflow-hidden border-4 border-slate-900 bg-slate-800 shadow-2xl relative">
                                    <template x-if="lastScan?.photo_url || lastScan?.snapshot_url">
                                        <img :src="lastScan?.snapshot_url || lastScan?.photo_url"
                                            class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!lastScan?.photo_url && !lastScan?.snapshot_url">
                                        <div class="w-full h-full flex items-center justify-center bg-slate-700">
                                            <i class="fa-solid fa-user text-5xl text-slate-500"></i>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- User Info -->
                            <h2 class="text-3xl font-bold text-white text-center mb-1 leading-tight"
                                x-text="lastScan?.name || 'ไม่รู้จัก'"></h2>
                            <p class="text-slate-400 font-medium text-lg mb-6"
                                x-text="lastScan?.employee_code || lastScan?.student_code || '-'">ID: 12345</p>

                            <!-- Stats Grid -->
                            <div class="grid grid-cols-2 gap-4 w-full">
                                <div class="bg-slate-800/80 rounded-2xl p-4 text-center border border-white/5">
                                    <div class="text-slate-400 text-xs font-bold uppercase mb-1">เวลาที่สแกน</div>
                                    <div class="text-xl font-mono font-bold text-white" x-text="lastScan?.time || '--:--'">
                                        --:--</div>
                                </div>
                                <div class="bg-slate-800/80 rounded-2xl p-4 text-center border border-white/5">
                                    <div class="text-slate-400 text-xs font-bold uppercase mb-1">สถานะ</div>
                                    <div class="text-xl font-bold"
                                        :class="lastScan?.is_late ? 'text-amber-400' : 'text-emerald-400'"
                                        x-text="lastScan?.status_text || 'เข้างานปกติ'">เข้างานปกติ</div>
                                </div>
                            </div>

                        </div>

                        <!-- Bottom Decorative Bar -->
                        <div class="h-2 w-full bg-gradient-to-r from-emerald-600 via-teal-500 to-emerald-600"></div>
                    </div>

                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="relative z-10 py-6 text-center text-slate-500 text-sm">
            <p>&copy; {{ date('Y') }} ลงเวลาปฏิบัติราชการด้วยใบหน้า</p>
        </footer>

    </div>

    <script>
        function kioskApp() {
            return {
                // Clock
                currentTime: '--:--:--',
                currentDate: '--',

                // Camera & Face Recognition
                stream: null,
                cameras: [],
                selectedCamera: '',
                isModelsLoading: true,
                statusMessage: 'กำลังโหลดระบบ AI...',
                faceMatcher: null,
                detectionInterval: null,
                lastScanTime: 0,

                // Blink Detection & Liveness
                livenessStatus: 'idle', // idle, waiting_for_blink, success
                blinkState: 'open',
                earThreshold: 0.25,
                blinkDetected: false,
                pendingEmployeeCode: null,

                // Face Detection State
                showFaceDetected: false, // Show feedback when face is detected

                // Unified scan (both staff and student)
                detectedType: null, // 'staff' or 'student' - detected from face match

                // UI State
                employeeCode: '',
                deviceCode: localStorage.getItem('kiosk_device_code') || '',
                apiToken: localStorage.getItem('kiosk_api_token') || '',
                isLoading: false,
                showConfig: false,

                // Data
                lastScan: null,
                showSuccessPopup: false,
                popupTimer: null,

                // Audio
                successAudio: new Audio("{{ asset('success.wav') }}".replace(/^http:/, location.protocol)),
                errorAudio: new Audio("{{ asset('error.wav') }}".replace(/^http:/, location.protocol)),

                async initKiosk() {
                    // Start Clock
                    this.updateClock();
                    setInterval(() => this.updateClock(), 1000);

                    // Subscribe to Pusher for Real-time updates
                    if (typeof window.Echo !== 'undefined') {
                        window.Echo.channel('scans')
                            .listen('.new-scan', (e) => {
                                this.handleNewScan(e.employee);
                            });
                    }

                    // Load Face API Models
                    try {
                        let modelPath = "{{ asset('models') }}";
                        if (location.protocol === 'https:' && modelPath.startsWith('http:')) {
                            modelPath = modelPath.replace('http:', 'https:');
                        }
                        console.log('Loading models from:', modelPath);

                        await Promise.all([
                            faceapi.loadTinyFaceDetectorModel(modelPath),
                            faceapi.loadFaceLandmarkModel(modelPath),
                            faceapi.loadFaceRecognitionModel(modelPath)
                        ]);

                        this.isModelsLoading = false;
                        this.statusMessage = 'ระบบพร้อมใช้งาน';
                        console.log('Face API Models Loaded Successfully');

                        await this.loadLabeledImages();

                    } catch (error) {
                        console.error('Error loading models:', error);
                        this.statusMessage = 'โหลดโมเดลไม่สำเร็จ';
                    }

                    this.startCamera();
                },

                updateClock() {
                    const now = new Date();
                    this.currentTime = now.toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                    this.currentDate = now.toLocaleDateString('th-TH', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' });
                },

                async loadLabeledImages() {
                    this.statusMessage = 'กำลังเรียนรู้ใบหน้า...';
                    try {
                        // Load faces from both staff and students
                        const staffFacesUrl = "{{ route('api.employees.faces') }}".replace(/^http:/, location.protocol);
                        const studentFacesUrl = "{{ route('api.students.faces') }}".replace(/^http:/, location.protocol);

                        const [staffResponse, studentResponse] = await Promise.all([
                            axios.get(staffFacesUrl),
                            axios.get(studentFacesUrl)
                        ]);

                        const staffMembers = staffResponse.data.data || [];
                        const students = studentResponse.data.data || [];

                        console.log(`Loading ${staffMembers.length} staff and ${students.length} students`);

                        // Process staff faces with STAFF_ prefix
                        const staffDescriptors = await Promise.all(
                            staffMembers.map(async (employee) => {
                                try {
                                    let photoUrl = employee.photo_url;
                                    if (location.protocol === 'https:' && photoUrl.startsWith('http:')) {
                                        photoUrl = photoUrl.replace('http:', 'https:');
                                    }
                                    const img = await faceapi.fetchImage(photoUrl);
                                    const detections = await faceapi.detectSingleFace(img, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptor();

                                    if (!detections) {
                                        console.warn(`No face detected for staff ${employee.name}`);
                                        return null;
                                    }

                                    // Use STAFF_ prefix to identify staff
                                    return new faceapi.LabeledFaceDescriptors('STAFF_' + employee.employee_code, [detections.descriptor]);
                                } catch (err) {
                                    console.error(`Error processing staff ${employee.name}:`, err);
                                    return null;
                                }
                            })
                        );

                        // Process student faces with STUDENT_ prefix
                        const studentDescriptors = await Promise.all(
                            students.map(async (student) => {
                                try {
                                    let photoUrl = student.photo_url;
                                    if (location.protocol === 'https:' && photoUrl.startsWith('http:')) {
                                        photoUrl = photoUrl.replace('http:', 'https:');
                                    }
                                    const img = await faceapi.fetchImage(photoUrl);
                                    const detections = await faceapi.detectSingleFace(img, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptor();

                                    if (!detections) {
                                        console.warn(`No face detected for student ${student.name}`);
                                        return null;
                                    }

                                    // Use STUDENT_ prefix to identify student
                                    return new faceapi.LabeledFaceDescriptors('STUDENT_' + student.student_code, [detections.descriptor]);
                                } catch (err) {
                                    console.error(`Error processing student ${student.name}:`, err);
                                    return null;
                                }
                            })
                        );

                        // Combine all valid descriptors
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
                    if (this.stream) {
                        this.stopCamera();
                    }

                    try {
                        const constraints = {
                            video: this.selectedCamera ? { deviceId: { exact: this.selectedCamera } } : true
                        };

                        this.stream = await navigator.mediaDevices.getUserMedia(constraints);
                        const video = this.$refs.videoElement;
                        video.srcObject = this.stream;

                        video.onloadedmetadata = () => {
                            video.play();
                            this.startFaceDetection();
                        };

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

                            // Reset state if no face found
                            if (results.length === 0) {
                                if (this.livenessStatus === 'waiting_for_blink') {
                                    this.livenessStatus = 'idle';
                                    this.pendingEmployeeCode = null;
                                }
                                this.showFaceDetected = false;
                            }

                            results.forEach((result, i) => {
                                const box = resizedDetections[i].detection.box;

                                // Customized Box Drawing
                                const ctx = canvas.getContext('2d');
                                const { x, y, width, height } = box;

                                // Draw corner brackets instead of full box for "pro" look
                                const lineLen = 20;
                                const lineWidth = 4;
                                ctx.strokeStyle = '#6366f1'; // Indigo-500
                                ctx.lineWidth = lineWidth;
                                ctx.beginPath();

                                // Top Left
                                ctx.moveTo(x, y + lineLen); ctx.lineTo(x, y); ctx.lineTo(x + lineLen, y);
                                // Top Right
                                ctx.moveTo(x + width - lineLen, y); ctx.lineTo(x + width, y); ctx.lineTo(x + width, y + lineLen);
                                // Bottom Right
                                ctx.moveTo(x + width, y + height - lineLen); ctx.lineTo(x + width, y + height); ctx.lineTo(x + width - lineLen, y + height);
                                // Bottom Left
                                ctx.moveTo(x + lineLen, y + height); ctx.lineTo(x, y + height); ctx.lineTo(x, y + height - lineLen);

                                ctx.stroke();

                                // Auto Scan Logic (No Blink for now, fast mode)
                                if (result.label !== 'unknown' && !this.isLoading) {
                                    const now = Date.now();

                                    // Check cooldown (5 seconds)
                                    if (now - this.lastScanTime > 5000) {
                                        // Feedback
                                        this.livenessStatus = 'success';
                                        this.statusMessage = 'พบใบหน้า กำลังประมวลผล...';
                                        this.showFaceDetected = true;

                                        // Submit Scan
                                        this.employeeCode = result.label;
                                        this.submitScan();
                                        this.lastScanTime = now;

                                        // Cleanup
                                        setTimeout(() => {
                                            this.livenessStatus = 'idle';
                                            this.showFaceDetected = false;
                                        }, 2000);
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
                    } catch (err) {
                        console.error("Error listing cameras:", err);
                    }
                },

                stopCamera() {
                    if (this.stream) {
                        this.stream.getTracks().forEach(track => track.stop());
                        this.stream = null;
                    }
                    if (this.detectionInterval) {
                        clearInterval(this.detectionInterval);
                        this.detectionInterval = null;
                    }
                },

                saveConfig() {
                    localStorage.setItem('kiosk_device_code', this.deviceCode);
                    localStorage.setItem('kiosk_api_token', this.apiToken);
                    this.showConfig = false;
                    alert('บันทึกการตั้งค่าเรียบร้อยแล้ว!');
                },

                async submitScan() {
                    if (!this.employeeCode) return;
                    if (!this.deviceCode || !this.apiToken) {
                        alert('กรุณาตั้งค่ารหัสอุปกรณ์และ API Token ก่อนใช้งาน');
                        this.showConfig = true;
                        return;
                    }

                    this.isLoading = true;
                    this.statusMessage = 'กำลังบันทึกข้อมูล...';

                    try {
                        const canvas = document.createElement('canvas');
                        const video = this.$refs.videoElement;
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        canvas.getContext('2d').drawImage(video, 0, 0);
                        const snapshot = canvas.toDataURL('image/jpeg');

                        // Detect type from prefix (STAFF_ or STUDENT_)
                        const isStudent = this.employeeCode.startsWith('STUDENT_');
                        const isStaff = this.employeeCode.startsWith('STAFF_');

                        // Extract actual code by removing prefix
                        let actualCode = this.employeeCode;
                        if (isStudent) {
                            actualCode = this.employeeCode.replace('STUDENT_', '');
                            this.detectedType = 'student';
                        } else if (isStaff) {
                            actualCode = this.employeeCode.replace('STAFF_', '');
                            this.detectedType = 'staff';
                        }

                        // Choose API endpoint based on detected type
                        const scanUrl = isStudent
                            ? "{{ route('api.student.scan.store') }}".replace(/^http:/, location.protocol)
                            : "{{ route('api.scan.store') }}".replace(/^http:/, location.protocol);

                        const scanPayload = isStudent
                            ? {
                                device_code: this.deviceCode,
                                api_token: this.apiToken,
                                student_code: actualCode,
                                snapshot: snapshot
                            }
                            : {
                                device_code: this.deviceCode,
                                api_token: this.apiToken,
                                employee_code: actualCode,
                                snapshot: snapshot
                            };

                        console.log(`Submitting scan for ${isStudent ? 'student' : 'staff'}: ${actualCode}`);
                        const response = await axios.post(scanUrl, scanPayload);

                        if (response.data.success) {

                            this.employeeCode = '';
                            const typeLabel = isStudent ? 'นักเรียน' : 'บุคลากร';
                            this.statusMessage = `บันทึกสำเร็จ! (${typeLabel})`;
                            this.successAudio.play().catch(e => console.log("Audio play failed:", e));

                            setTimeout(() => { this.statusMessage = 'ระบบพร้อมใช้งาน'; }, 2000);
                        }

                    } catch (error) {
                        console.error(error);
                        this.statusMessage = 'เกิดข้อผิดพลาด';
                        this.errorAudio.play().catch(e => console.log("Audio play failed:", e));
                    } finally {
                        this.isLoading = false;
                    }
                },

                handleNewScan(data) {
                    this.lastScan = data;

                    // Clear existing timer if any
                    if (this.popupTimer) {
                        clearTimeout(this.popupTimer);
                    }

                    // Show success popup
                    this.showSuccessPopup = true;

                    // Auto-hide after 5 seconds
                    this.popupTimer = setTimeout(() => {
                        this.showSuccessPopup = false;
                    }, 5000);
                }
            }
        }
    </script>

    <style>
        /* Custom Animations */
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
            animation: blob 10s infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }

        .animation-delay-4000 {
            animation-delay: 4s;
        }

        @keyframes scan-line {
            0% {
                top: -100%;
            }

            50% {
                top: 100%;
            }

            100% {
                top: -100%;
            }
        }

        .animate-scan-line {
            animation: scan-line 4s linear infinite;
            background: linear-gradient(to bottom, transparent, rgba(99, 102, 241, 0.5), transparent);
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

        /* Input autofill fix for dark mode */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px #020617 inset !important;
            -webkit-text-fill-color: white !important;
        }
    </style>
@endsection