@extends('layouts.kiosk')

@section('title', 'จุดลงเวลา')

@section('content')
<!-- Load face-api.js -->
<script src="{{ asset('js/face-api.min.js') }}"></script>

<div x-data="kioskApp()" x-init="initKiosk()" class="min-h-screen flex flex-col relative overflow-hidden">
    
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-primary-500/15 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-primary-600/15 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-primary-500/5 rounded-full blur-3xl"></div>
    </div>

    <!-- Header -->
    <header class="relative z-10 p-6 lg:p-8">
        <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-center justify-between gap-6">
            <!-- Logo & Title -->
            <div class="flex items-center gap-5">
                <div class="relative">
                    <div class="absolute inset-0 bg-primary-500/30 rounded-2xl blur-xl animate-pulse"></div>
                    <div class="relative w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-lg overflow-hidden">
                        <img src="{{ asset('images/logonavy.png') }}" alt="Logo" class="w-14 h-14 object-contain">
                    </div>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-white tracking-tight">
                        ระบบลงเวลาด้วยใบหน้า
                    </h1>
                    <p class="text-slate-400 text-sm mt-0.5">Face Recognition Attendance System</p>
                </div>
            </div>
            
            <!-- Unified Scan Badge -->
            <div class="flex items-center gap-3 bg-slate-900/50 backdrop-blur-md rounded-2xl px-5 py-3 border border-slate-700/50">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-user-tie text-blue-400"></i>
                    <span class="text-slate-400">+</span>
                    <i class="fa-solid fa-user-graduate text-emerald-400"></i>
                </div>
                <span class="text-white font-medium">จุดสแกนรวม</span>
            </div>
            
            <!-- Clock Display -->
            <div class="glass-card px-8 py-4 rounded-2xl text-center">
                <div class="text-5xl font-bold text-white tracking-wider font-mono" x-text="currentTime">--:--:--</div>
                <div class="text-slate-400 text-sm mt-1" x-text="currentDate">-- -- --</div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="relative z-10 flex-1 px-6 lg:px-8 pb-8">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6 h-full">
            
            <!-- Left Column: Camera Feed -->
            <div class="lg:col-span-2 flex flex-col gap-6">
                
                <!-- Camera Container -->
                <div class="h-[550px] glass-card rounded-3xl p-6 flex flex-col relative overflow-hidden group">
                    
                    <!-- Decorative Corner Accents -->
                    <div class="absolute top-0 left-0 w-20 h-20 border-t-2 border-l-2 border-primary-500/30 rounded-tl-3xl"></div>
                    <div class="absolute top-0 right-0 w-20 h-20 border-t-2 border-r-2 border-primary-500/30 rounded-tr-3xl"></div>
                    <div class="absolute bottom-0 left-0 w-20 h-20 border-b-2 border-l-2 border-primary-500/30 rounded-bl-3xl"></div>
                    <div class="absolute bottom-0 right-0 w-20 h-20 border-b-2 border-r-2 border-primary-500/30 rounded-br-3xl"></div>
                    
                    <!-- Camera Feed -->
                    <div class="relative flex-1 bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl overflow-hidden border border-slate-700/50 shadow-2xl shadow-black/50">
                        <video x-ref="videoElement" autoplay playsinline muted class="w-full h-full object-cover transform -scale-x-100"></video>
                        
                        <!-- Canvas for Face Detection Overlay -->
                        <canvas x-ref="overlayElement" class="absolute inset-0 w-full h-full pointer-events-none transform -scale-x-100"></canvas>
                        
                        <!-- Scanning Animation Overlay -->
                        <div class="absolute inset-0 pointer-events-none overflow-hidden">
                            <div class="absolute inset-x-0 h-0.5 bg-gradient-to-r from-transparent via-primary-500 to-transparent animate-scan-line"></div>
                        </div>
                        
                        <!-- Status Badge -->
                        <div class="absolute top-4 left-4 z-20">
                            <div class="glass-card-dark px-4 py-2.5 rounded-full flex items-center gap-3">
                                <span class="relative flex h-3 w-3">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" 
                                        :class="isModelsLoading ? 'bg-yellow-400' : 'bg-emerald-400'"></span>
                                  <span class="relative inline-flex rounded-full h-3 w-3" 
                                        :class="isModelsLoading ? 'bg-yellow-400' : 'bg-emerald-400'"></span>
                                </span>
                                <span class="text-sm font-medium text-white" x-text="statusMessage">กำลังโหลด...</span>
                            </div>
                        </div>
                        
                        <!-- Blink Detection Overlay -->
                        <div x-show="livenessStatus === 'waiting_for_blink'" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 scale-90"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute inset-0 flex flex-col items-center justify-center z-30 pointer-events-none">
                            <div class="bg-black/70 backdrop-blur-md text-white px-10 py-8 rounded-[2rem] border border-amber-500/50 shadow-2xl flex flex-col items-center animate-pulse">
                                <i class="fa-solid fa-eye text-6xl mb-6 text-amber-400"></i>
                                <h3 class="text-3xl font-bold">กรุณากระพริบตา</h3>
                                <p class="text-slate-300 text-lg mt-3">เพื่อยืนยันตัวตน</p>
                            </div>
                        </div>

                        <!-- Success Overlay -->
                        <div x-show="livenessStatus === 'success'" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 scale-90"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute inset-0 flex flex-col items-center justify-center z-30 pointer-events-none">
                            <div class="bg-black/70 backdrop-blur-md text-white px-10 py-8 rounded-[2rem] border border-emerald-500/50 shadow-2xl flex flex-col items-center">
                                <i class="fa-solid fa-circle-check text-6xl mb-6 text-emerald-400"></i>
                                <h3 class="text-3xl font-bold">ยืนยันตัวตนสำเร็จ!</h3>
                                <p class="text-slate-300 text-lg mt-3">กำลังบันทึก...</p>
                            </div>
                        </div>
                        
                        <!-- Camera Quality Badge -->
                        <div class="absolute top-4 right-4 z-20">
                            <div class="glass-card-dark px-3 py-1.5 rounded-full flex items-center gap-2">
                                <i class="fa-solid fa-video text-xs text-emerald-400"></i>
                                <span class="text-xs text-slate-300">HD</span>
                            </div>
                        </div>

                        <!-- Face Detection Success Overlay -->
                        <div x-show="showFaceDetected" 
                             x-transition:enter="transition ease-out duration-500"
                             x-transition:enter-start="opacity-0 scale-75"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute inset-0 flex flex-col items-center justify-center z-30 pointer-events-none bg-black/40 backdrop-blur-sm">
                            <div class="relative">
                                <!-- Animated Rings -->
                                <div class="absolute inset-0 -m-8 border-4 border-emerald-400/30 rounded-full animate-ping"></div>
                                <div class="absolute inset-0 -m-4 border-2 border-emerald-400/50 rounded-full animate-pulse"></div>
                                
                                <div class="glass-card px-12 py-10 rounded-3xl flex flex-col items-center">
                                    <div class="w-24 h-24 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full flex items-center justify-center mb-6 shadow-lg shadow-emerald-500/30">
                                        <i class="fa-solid fa-check text-5xl text-white"></i>
                                    </div>
                                    <h3 class="text-3xl font-bold text-white">ตรวจพบใบหน้า!</h3>
                                    <p class="text-slate-300 text-lg mt-2">กำลังบันทึกข้อมูล...</p>
                                </div>
                            </div>
                        </div>

                        <!-- Loading Models Indicator -->
                        <div x-show="isModelsLoading" class="absolute inset-0 bg-slate-900/95 flex flex-col items-center justify-center z-20">
                            <div class="relative">
                                <div class="w-20 h-20 border-4 border-primary-500/30 rounded-full"></div>
                                <div class="absolute inset-0 w-20 h-20 border-4 border-transparent border-t-primary-500 rounded-full animate-spin"></div>
                            </div>
                            <p class="text-xl text-white mt-6 font-medium">กำลังโหลดโมเดล AI...</p>
                            <p class="text-slate-400 mt-2">กรุณารอสักครู่</p>
                        </div>
                    </div>

                    <!-- Manual Code Entry -->
                    <div class="mt-6 glass-card-dark p-6 rounded-2xl">
                        <div class="flex gap-4">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-id-badge text-slate-500"></i>
                                </div>
                                <input type="text" x-model="employeeCode" 
                                       @keyup.enter="submitScan()"
                                       placeholder="กรอกรหัสพนักงาน..." 
                                       class="w-full bg-slate-800/80 border-2 border-slate-600/50 text-white placeholder-slate-500 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-lg pl-12 pr-5 py-4 transition-all duration-300 focus:shadow-lg focus:shadow-primary-500/20">
                            </div>
                            
                            <button @click="submitScan()" 
                                    :disabled="isLoading || !employeeCode"
                                    class="relative overflow-hidden bg-gradient-to-r from-primary-600 to-primary-500 hover:from-primary-500 hover:to-primary-400 text-white px-10 py-4 rounded-xl font-semibold transition-all duration-300 shadow-lg shadow-primary-900/30 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none text-lg group">
                                <span x-show="!isLoading" class="flex items-center gap-2">
                                    <i class="fa-solid fa-fingerprint text-xl group-hover:scale-110 transition-transform"></i>
                                    <span>ลงเวลา</span>
                                </span>
                                <span x-show="isLoading" class="flex items-center gap-2">
                                    <i class="fa-solid fa-spinner fa-spin text-xl"></i>
                                </span>
                            </button>
                        </div>
                        
                        <div class="flex items-center justify-center gap-6 mt-5">
                            <div class="flex-1 h-px bg-gradient-to-r from-transparent via-slate-600 to-transparent"></div>
                            <p class="text-slate-500 text-sm">หรือมองหน้ากล้องเพื่อสแกนใบหน้าอัตโนมัติ</p>
                            <div class="flex-1 h-px bg-gradient-to-r from-transparent via-slate-600 to-transparent"></div>
                        </div>
                        
                        <!-- Config Toggle -->
                        <div class="mt-4 flex justify-center">
                            <button @click="showConfig = !showConfig" class="text-slate-500 hover:text-primary-400 transition-colors flex items-center gap-2 text-sm group">
                                <i class="fa-solid fa-gear group-hover:rotate-90 transition-transform duration-300"></i> 
                                <span>ตั้งค่าอุปกรณ์</span>
                                <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300" :class="showConfig ? 'rotate-180' : ''"></i>
                            </button>
                        </div>

                        <!-- Config Panel -->
                        <div x-show="showConfig" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 -translate-y-4"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="mt-4 pt-4 border-t border-slate-700/50 space-y-4">
                            <!-- Camera Selector -->
                            <div x-show="cameras.length > 0">
                                <label class="text-xs text-slate-400 block mb-2 font-medium">
                                    <i class="fa-solid fa-camera mr-1"></i> เลือกกล้อง
                                </label>
                                <select x-model="selectedCamera" @change="startCamera()" class="w-full bg-slate-800 border-2 border-slate-600/50 text-white text-sm rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                                    <template x-for="camera in cameras" :key="camera.deviceId">
                                        <option :value="camera.deviceId" x-text="camera.label || 'Camera ' + ($index + 1)"></option>
                                    </template>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs text-slate-400 block mb-2 font-medium">
                                        <i class="fa-solid fa-microchip mr-1"></i> รหัสอุปกรณ์
                                    </label>
                                    <input type="text" x-model="deviceCode" placeholder="เช่น DEV-001" class="w-full bg-slate-800 border-2 border-slate-600/50 text-white text-sm rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                                </div>
                                <div>
                                    <label class="text-xs text-slate-400 block mb-2 font-medium">
                                        <i class="fa-solid fa-key mr-1"></i> API Token
                                    </label>
                                    <input type="password" x-model="apiToken" placeholder="••••••••" class="w-full bg-slate-800 border-2 border-slate-600/50 text-white text-sm rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                                </div>
                            </div>
                            <button @click="saveConfig()" class="w-full bg-gradient-to-r from-slate-700 to-slate-600 hover:from-slate-600 hover:to-slate-500 text-white text-sm py-3.5 rounded-xl font-medium transition-all duration-300 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-floppy-disk"></i>
                                บันทึกการตั้งค่า
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Success Popup & Status -->
            <div class="flex flex-col gap-6 h-[550px]">
                
                <!-- Success Popup Card (Shown on Recent Scan with Auto-hide) -->
                <div x-show="showSuccessPopup" 
                     x-transition:enter="transition ease-out duration-700"
                     x-transition:enter-start="opacity-0 translate-x-8 scale-90"
                     x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                     x-transition:leave="transition ease-in duration-500"
                     x-transition:leave-start="opacity-100 translate-x-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-x-8 scale-90"
                     class="relative overflow-hidden glass-card rounded-3xl border-2 border-emerald-500/30 shadow-2xl shadow-emerald-500/20">
                    
                    <!-- Glowing Background Effect -->
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 via-transparent to-primary-500/10"></div>
                    <div class="absolute -top-20 -right-20 w-40 h-40 bg-emerald-500/20 rounded-full blur-3xl animate-pulse"></div>
                    <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-primary-500/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 0.5s;"></div>
                    
                    <div class="relative p-8">
                        <!-- Success Header -->
                        <div class="flex items-center justify-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-emerald-500/20 rounded-full flex items-center justify-center">
                                <i class="fa-solid fa-check text-emerald-400 text-lg animate-bounce"></i>
                            </div>
                            <h3 class="text-xl font-bold text-emerald-400">ลงเวลาสำเร็จ!</h3>
                        </div>
                        
                        <!-- Profile Section -->
                        <div class="flex flex-col items-center text-center">
                            <!-- Animated Ring Around Photo -->
                            <div class="relative mb-6">
                                <!-- Outer Animated Ring -->
                                <div class="absolute inset-0 -m-3 border-4 border-emerald-400/30 rounded-full animate-ping" style="animation-duration: 2s;"></div>
                                <div class="absolute inset-0 -m-2 border-2 border-emerald-400/50 rounded-full animate-pulse"></div>
                                
                                <!-- Glowing Effect -->
                                <div class="absolute inset-0 bg-emerald-400/30 rounded-full blur-xl animate-pulse"></div>
                                
                                <!-- Profile Photo -->
                                <div class="relative w-32 h-32 rounded-full bg-gradient-to-br from-slate-700 to-slate-800 overflow-hidden ring-4 ring-emerald-400/50 shadow-2xl shadow-emerald-500/30">
                                    <template x-if="lastScan?.photo_url || lastScan?.snapshot_url">
                                        <img :src="lastScan?.snapshot_url || lastScan?.photo_url" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!lastScan?.photo_url && !lastScan?.snapshot_url">
                                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-600 to-slate-700">
                                            <i class="fa-solid fa-user text-4xl text-slate-400"></i>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            
                            <!-- Name -->
                            <h2 class="text-2xl font-bold text-white mb-2" x-text="lastScan?.name || '-'"></h2>
                            
                            <!-- Time Badge -->
                            <div class="flex items-center gap-2 px-4 py-2 bg-slate-800/50 rounded-full border border-slate-700/50">
                                <i class="fa-regular fa-clock text-emerald-400"></i>
                                <span class="text-slate-300 font-medium" x-text="lastScan?.time || '--:--'"></span>
                            </div>
                            
                            <!-- Status Badge -->
                            <div class="mt-4">
                                <span class="px-4 py-2 rounded-full text-sm font-bold uppercase tracking-wide"
                                      :class="lastScan?.is_late ? 'bg-amber-500/20 text-amber-400 ring-2 ring-amber-500/30' : 'bg-emerald-500/20 text-emerald-400 ring-2 ring-emerald-500/30'"
                                      x-text="lastScan?.status_text || lastScan?.scan_type || lastScan?.type || 'เข้างาน'"></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Waiting State (Shown when no recent scan) -->
                <div x-show="!showSuccessPopup" 
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     class="flex-1 glass-card rounded-3xl flex flex-col items-center justify-center p-8">
                    
                    <!-- Animated Scanning Icon -->
                    <div class="relative mb-6">
                        <div class="w-24 h-24 bg-gradient-to-br from-primary-500/20 to-primary-700/20 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-face-smile text-5xl text-primary-400"></i>
                        </div>
                        <!-- Scanning Ring Animation -->
                        <div class="absolute inset-0 -m-2 border-2 border-primary-500/30 rounded-full animate-ping" style="animation-duration: 2s;"></div>
                    </div>
                    
                    <h3 class="text-xl font-bold text-white mb-2">พร้อมสแกน</h3>
                    <p class="text-slate-400 text-center">กรุณามองหน้ากล้องเพื่อสแกนใบหน้า</p>
                    
                    <!-- Live Indicator -->
                    <div class="mt-6 flex items-center gap-2 px-4 py-2 bg-slate-800/50 rounded-full border border-slate-700/50">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                        </span>
                        <span class="text-xs text-emerald-400 font-medium">ระบบพร้อมใช้งาน</span>
                    </div>
                </div>
                
                <!-- Footer Info -->
                <div class="glass-card-dark px-5 py-4 rounded-2xl flex items-center justify-between">
                    <div class="flex items-center gap-3 text-slate-300">
                        <i class="fa-solid fa-microchip text-primary-400"></i>
                        <span class="text-xs">ระบบ AI ตรวจจับใบหน้าอัตโนมัติ</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                        <span class="text-xs text-emerald-400 font-medium">Online</span>
                    </div>
                </div>
            </div>
        </div>
    </main>
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
            statusMessage: 'กำลังโหลดโมเดล...',
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
                    this.statusMessage = 'พร้อมใช้งาน';
                    console.log('Face API Models Loaded Successfully');
                    
                    await this.loadLabeledImages();

                } catch (error) {
                    console.error('Error loading models:', error);
                    this.statusMessage = 'โหลดโมเดลล้มเหลว';
                }
                
                this.startCamera();
            },
            
            updateClock() {
                const now = new Date();
                this.currentTime = now.toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                this.currentDate = now.toLocaleDateString('th-TH', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
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
                        this.statusMessage = `พร้อมใช้งาน (${validStaff} ข้าราชการ, ${validStudents} นักเรียน)`;
                    } else {
                        this.statusMessage = 'ไม่พบข้อมูลใบหน้าในระบบ';
                    }

                } catch (error) {
                    console.error('Error loading labeled images:', error);
                    this.statusMessage = 'โหลดข้อมูลใบหน้าล้มเหลว';
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
                            const landmarks = resizedDetections[i].landmarks;
                            
                            const drawBox = new faceapi.draw.DrawBox(box, { label: result.toString() });
                            drawBox.draw(canvas);

                            // Auto Scan Logic (ไม่ต้องกระพริบตา)
                            if (result.label !== 'unknown' && !this.isLoading) {
                                const now = Date.now();
                                
                                // Check cooldown (5 seconds)
                                if (now - this.lastScanTime > 5000) {
                                    // ยืนยันสำเร็จทันที
                                    this.livenessStatus = 'success';
                                    this.statusMessage = 'พบใบหน้า กำลังบันทึก...';
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
            
            calculateEAR(eye) {
                const distance = (p1, p2) => Math.sqrt(Math.pow(p1.x - p2.x, 2) + Math.pow(p1.y - p2.y, 2));
                const a = distance(eye[1], eye[5]);
                const b = distance(eye[2], eye[4]);
                const c = distance(eye[0], eye[3]);
                return (a + b) / (2.0 * c);
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
                    alert('กรุณาตั้งค่า Device Code และ API Token ก่อนใช้งาน');
                    this.showConfig = true;
                    return;
                }

                this.isLoading = true;
                this.statusMessage = 'กำลังบันทึก...';

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
                        const typeLabel = isStudent ? 'นักเรียน' : 'ข้าราชการ';
                        this.statusMessage = `บันทึกสำเร็จ! (${typeLabel})`;
                        this.successAudio.play().catch(e => console.log("Audio play failed:", e));
                        
                        setTimeout(() => { this.statusMessage = 'พร้อมใช้งาน'; }, 2000);
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
    /* Glass Card Styles */
    .glass-card {
        background: rgba(30, 41, 59, 0.6);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(148, 163, 184, 0.1);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }
    
    .glass-card-dark {
        background: rgba(15, 23, 42, 0.7);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(148, 163, 184, 0.08);
    }

    /* 🎄 Christmas Glass Card */
    .glass-card-christmas {
        background: linear-gradient(135deg, rgba(220, 38, 38, 0.15), rgba(30, 41, 59, 0.6), rgba(22, 163, 74, 0.15));
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 2px solid;
        border-image: linear-gradient(135deg, #dc2626, #fbbf24, #16a34a) 1;
        box-shadow: 0 8px 32px rgba(220, 38, 38, 0.2), 0 0 40px rgba(22, 163, 74, 0.1);
    }

    .christmas-icon-glow {
        animation: christmas-glow 2s ease-in-out infinite;
    }

    @keyframes christmas-glow {
        0%, 100% { box-shadow: 0 0 20px rgba(220, 38, 38, 0.5), 0 0 40px rgba(22, 163, 74, 0.3); }
        50% { box-shadow: 0 0 30px rgba(22, 163, 74, 0.5), 0 0 60px rgba(220, 38, 38, 0.3); }
    }

    /* 🎆 Happy New Year Glass Card */
    .glass-card-newyear {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.15), rgba(30, 41, 59, 0.6), rgba(138, 43, 226, 0.15));
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 2px solid;
        border-image: linear-gradient(135deg, #FFD700, #FF1493, #8B00FF) 1;
        box-shadow: 0 8px 32px rgba(255, 215, 0, 0.2), 0 0 40px rgba(138, 43, 226, 0.1);
    }

    .newyear-icon-glow {
        animation: newyear-glow 2s ease-in-out infinite;
    }

    @keyframes newyear-glow {
        0%, 100% { box-shadow: 0 0 20px rgba(255, 215, 0, 0.5), 0 0 40px rgba(138, 43, 226, 0.3); }
        50% { box-shadow: 0 0 30px rgba(255, 20, 147, 0.5), 0 0 60px rgba(255, 215, 0, 0.3); }
    }

    /* ❄️ Snowfall Effect */
    .snowfall-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 100;
        overflow: hidden;
    }

    .snowflake {
        position: absolute;
        top: -50px;
        color: white;
        font-size: 1.5rem;
        opacity: 0.8;
        animation: snowfall linear infinite;
        text-shadow: 0 0 10px rgba(255, 255, 255, 0.8);
    }

    .snowflake:nth-child(1) { left: 5%; animation-duration: 8s; animation-delay: 0s; font-size: 1.2rem; }
    .snowflake:nth-child(2) { left: 10%; animation-duration: 10s; animation-delay: 1s; font-size: 1.8rem; }
    .snowflake:nth-child(3) { left: 15%; animation-duration: 7s; animation-delay: 0.5s; font-size: 1rem; }
    .snowflake:nth-child(4) { left: 20%; animation-duration: 9s; animation-delay: 2s; font-size: 1.4rem; }
    .snowflake:nth-child(5) { left: 25%; animation-duration: 11s; animation-delay: 0.8s; font-size: 2rem; }
    .snowflake:nth-child(6) { left: 30%; animation-duration: 8s; animation-delay: 3s; font-size: 1.1rem; }
    .snowflake:nth-child(7) { left: 35%; animation-duration: 12s; animation-delay: 1.5s; font-size: 1.6rem; }
    .snowflake:nth-child(8) { left: 40%; animation-duration: 9s; animation-delay: 0.3s; font-size: 1.3rem; }
    .snowflake:nth-child(9) { left: 45%; animation-duration: 10s; animation-delay: 2.5s; font-size: 1.9rem; }
    .snowflake:nth-child(10) { left: 50%; animation-duration: 7s; animation-delay: 1.2s; font-size: 1.5rem; }
    .snowflake:nth-child(11) { left: 55%; animation-duration: 11s; animation-delay: 0.7s; font-size: 1.2rem; }
    .snowflake:nth-child(12) { left: 60%; animation-duration: 8s; animation-delay: 3.5s; font-size: 1.7rem; }
    .snowflake:nth-child(13) { left: 65%; animation-duration: 9s; animation-delay: 1.8s; font-size: 1.1rem; }
    .snowflake:nth-child(14) { left: 70%; animation-duration: 10s; animation-delay: 0.4s; font-size: 1.4rem; }
    .snowflake:nth-child(15) { left: 75%; animation-duration: 12s; animation-delay: 2.2s; font-size: 2.1rem; }
    .snowflake:nth-child(16) { left: 80%; animation-duration: 7s; animation-delay: 1s; font-size: 1.3rem; }
    .snowflake:nth-child(17) { left: 85%; animation-duration: 9s; animation-delay: 2.8s; font-size: 1.6rem; }
    .snowflake:nth-child(18) { left: 90%; animation-duration: 11s; animation-delay: 0.6s; font-size: 1.2rem; }
    .snowflake:nth-child(19) { left: 93%; animation-duration: 8s; animation-delay: 3.2s; font-size: 1.8rem; }
    .snowflake:nth-child(20) { left: 97%; animation-duration: 10s; animation-delay: 1.4s; font-size: 1.5rem; }

    @keyframes snowfall {
        0% {
            transform: translateY(-50px) rotate(0deg);
            opacity: 1;
        }
        100% {
            transform: translateY(100vh) rotate(360deg);
            opacity: 0.3;
        }
    }

    /* � Happy New Year Banner */
    .newyear-banner {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        display: flex;
        justify-content: space-around;
        z-index: 99;
        padding: 5px 10px;
        background: linear-gradient(90deg, rgba(255,215,0,0.1), rgba(255,20,147,0.1), rgba(138,43,226,0.1));
    }

    .sparkle {
        font-size: 1.5rem;
        animation: sparkle-dance 1.5s ease-in-out infinite;
        filter: drop-shadow(0 0 10px rgba(255, 215, 0, 0.8));
    }

    .sparkle:nth-child(odd) { animation-delay: 0s; }
    .sparkle:nth-child(even) { animation-delay: 0.75s; }

    @keyframes sparkle-dance {
        0%, 100% { opacity: 0.5; transform: scale(0.8) rotate(0deg); }
        50% { opacity: 1; transform: scale(1.3) rotate(180deg); }
    }

    /* 🎆 Fireworks Container */
    .fireworks-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 50%;
        pointer-events: none;
        z-index: 97;
    }

    .firework {
        position: absolute;
        font-size: 3rem;
        animation: firework-burst 3s ease-out infinite;
        filter: drop-shadow(0 0 20px rgba(255, 215, 0, 0.8));
    }

    @keyframes firework-burst {
        0% { 
            top: 80%;
            opacity: 0;
            transform: scale(0.3);
        }
        30% {
            top: 20%;
            opacity: 1;
            transform: scale(1);
        }
        50% {
            transform: scale(1.5);
            opacity: 1;
        }
        100% {
            top: 15%;
            opacity: 0;
            transform: scale(2);
        }
    }

    /* 🚀 Rocket Animation */
    .rocket-container {
        position: fixed;
        top: 15%;
        left: -300px;
        z-index: 98;
        animation: fly-rocket 15s linear infinite;
    }

    .rocket-trail {
        display: flex;
        align-items: center;
        font-size: 2.5rem;
        filter: drop-shadow(0 0 15px rgba(255, 215, 0, 0.8));
    }

    .rocket {
        animation: rocket-wobble 0.3s ease-in-out infinite alternate;
        transform: rotate(-45deg);
    }

    .trail-star {
        margin-left: -15px;
        animation: trail-fade 0.5s ease-in-out infinite alternate;
        opacity: 0.7;
    }

    .trail-star:nth-child(2) { animation-delay: 0.1s; font-size: 1.5rem; }
    .trail-star:nth-child(3) { animation-delay: 0.2s; font-size: 1.2rem; }
    .trail-star:nth-child(4) { animation-delay: 0.3s; font-size: 1rem; }

    .year-text {
        font-size: 2rem;
        font-weight: bold;
        color: #FFD700;
        text-shadow: 0 0 20px rgba(255, 215, 0, 0.8), 0 0 40px rgba(255, 215, 0, 0.5);
        margin-left: 10px;
        animation: year-glow 1s ease-in-out infinite alternate;
    }

    @keyframes fly-rocket {
        0% { left: -300px; top: 20%; }
        25% { top: 10%; }
        50% { top: 18%; }
        75% { top: 8%; }
        100% { left: 110%; top: 15%; }
    }

    @keyframes rocket-wobble {
        0% { transform: rotate(-48deg) translateY(0); }
        100% { transform: rotate(-42deg) translateY(-3px); }
    }

    @keyframes trail-fade {
        0% { opacity: 0.3; transform: scale(0.8); }
        100% { opacity: 1; transform: scale(1.2); }
    }

    @keyframes year-glow {
        0% { text-shadow: 0 0 20px rgba(255, 215, 0, 0.8), 0 0 40px rgba(255, 215, 0, 0.5); }
        100% { text-shadow: 0 0 30px rgba(255, 20, 147, 0.8), 0 0 60px rgba(138, 43, 226, 0.5); }
    }

    /* Floating Animation */
    @keyframes float {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(5deg); }
    }

    @keyframes float-slow {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-15px) rotate(-5deg); }
    }

    .animate-float {
        animation: float 4s ease-in-out infinite;
    }

    .animate-float-slow {
        animation: float-slow 6s ease-in-out infinite;
    }

    /* Twinkle Animation */
    @keyframes animate-twinkle {
        0%, 100% { opacity: 0.5; transform: scale(1); }
        50% { opacity: 1; transform: scale(1.1); }
    }

    .animate-twinkle {
        animation: animate-twinkle 3s ease-in-out infinite;
    }

    /* Original Animations */
    @keyframes scan-line {
        0% { top: 0; opacity: 0; }
        10% { opacity: 1; }
        90% { opacity: 1; }
        100% { top: 100%; opacity: 0; }
    }
    
    .animate-scan-line {
        animation: scan-line 3s ease-in-out infinite;
    }
    
    @keyframes bounce-slow {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    
    .animate-bounce-slow {
        animation: bounce-slow 2s ease-in-out infinite;
    }
    
    @keyframes fade-in-up {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in-up {
        animation: fade-in-up 0.4s ease-out forwards;
        opacity: 0;
    }

    /* Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(255,255,255,0.02); 
        border-radius: 3px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.1); 
        border-radius: 3px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(255,255,255,0.2); 
    }
    
    /* Input Focus Enhancement */
    input:focus, select:focus {
        outline: none;
    }

    /* 🎄 Christmas Success Card */
    .christmas-success-gradient {
        background: linear-gradient(135deg, #dc2626, #16a34a) !important;
    }

    /* Christmas Border Animation */
    @keyframes border-dance {
        0%, 100% { border-color: #dc2626; }
        33% { border-color: #16a34a; }
        66% { border-color: #fbbf24; }
    }

    .animate-border-dance {
        animation: border-dance 3s ease-in-out infinite;
    }

    /* Sparkle Effect */
    @keyframes sparkle {
        0%, 100% { opacity: 0; transform: scale(0); }
        50% { opacity: 1; transform: scale(1); }
    }

    /* Christmas Tree Icon Animation */
    @keyframes tree-sway {
        0%, 100% { transform: rotate(-2deg); }
        50% { transform: rotate(2deg); }
    }

    .animate-tree-sway {
        animation: tree-sway 2s ease-in-out infinite;
    }
</style>
@endsection
