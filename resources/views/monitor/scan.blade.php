@extends('layouts.app')

@section('title', 'จุดลงเวลา (Kiosk)')

@section('content')
<!-- Load face-api.js -->
<script src="{{ asset('js/face-api.min.js') }}"></script>

<div x-data="monitorApp()" x-init="initMonitor()" class="h-[calc(100vh-8rem)]">
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 h-full">
        
        <!-- Left Column: Camera Scanner -->
        <div class="lg:col-span-2 flex flex-col gap-6 h-full">
            
            <!-- Mode 2: KIOSK CAMERA (Active Scanner) -->
            <div class="flex-1 bg-slate-900 rounded-3xl shadow-lg border border-slate-800 p-6 flex flex-col relative overflow-hidden">
                
                <!-- Camera Feed -->
                <div class="relative flex-1 bg-black rounded-2xl overflow-hidden mb-6 border border-slate-700 shadow-2xl">
                    <video x-ref="videoElement" autoplay playsinline muted class="w-full h-full object-cover transform -scale-x-100"></video>
                    <!-- Canvas for Face Detection Overlay -->
                    <canvas x-ref="overlayElement" class="absolute inset-0 w-full h-full pointer-events-none transform -scale-x-100"></canvas>
                    
                    <!-- Overlay Text -->
                    <div class="absolute top-4 left-4 bg-black/60 text-white px-4 py-1.5 rounded-full text-xs backdrop-blur-md border border-white/10 flex items-center gap-2 z-10">
                        <span class="relative flex h-2 w-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-500 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-red-600"></span>
                        </span>
                        <span x-text="statusMessage">กล้องทำงาน</span>
                    </div>

                    <!-- Blink Detection Overlay -->
                    <div x-show="livenessStatus === 'waiting_for_blink'" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-90"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute inset-0 flex flex-col items-center justify-center z-30 pointer-events-none">
                        <div class="bg-black/70 backdrop-blur-md text-white px-8 py-6 rounded-3xl border border-amber-500/50 shadow-2xl flex flex-col items-center">
                            <x-heroicon-o-eye class="text-5xl mb-4 text-amber-400 animate-pulse w-5"/>
                            <h3 class="text-2xl font-bold font-mono">กรุณากระพริบตา</h3>
                            <p class="text-slate-300 text-sm mt-2">เพื่อยืนยันว่าคุณเป็นคนจริง</p>
                        </div>
                    </div>

                    <!-- Success Overlay -->
                    <div x-show="livenessStatus === 'success'" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-90"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute inset-0 flex flex-col items-center justify-center z-30 pointer-events-none">
                        <div class="bg-black/70 backdrop-blur-md text-white px-8 py-6 rounded-3xl border border-emerald-500/50 shadow-2xl flex flex-col items-center">
                            <x-heroicon-o-check-circle class="text-5xl mb-4 text-emerald-400 w-5"/>
                            <h3 class="text-2xl font-bold font-mono">ยืนยันตัวตนสำเร็จ!</h3>
                            <p class="text-slate-300 text-sm mt-2">กำลังบันทึก...</p>
                        </div>
                    </div>

                    <!-- Loading Models Indicator -->
                    <div x-show="isModelsLoading" class="absolute inset-0 bg-black/80 flex flex-col items-center justify-center z-20 text-white">
                        <x-heroicon-o-arrow-path class="text-4xl mb-3 text-primary-500 w-5"/>
                        <p>กำลังโหลดโมเดล AI...</p>
                    </div>
                </div>

                <!-- Manual Input Form (Simulating Face Rec) -->
                <div class="bg-slate-800/50 p-6 rounded-2xl border border-slate-700/50 backdrop-blur-sm space-y-4">
                    <div class="flex gap-3">
                        <input type="text" x-model="kiosk.employee_code" 
                               @keyup.enter="submitScan()"
                               placeholder="กรอกรหัสพนักงาน..." 
                               class="flex-1 bg-slate-900/80 border-slate-600 text-white placeholder-slate-500 rounded-xl focus:ring-primary-500 focus:border-slate-200/600 text-sm px-4 py-3">
                        
                        <button @click="submitScan()" 
                                :disabled="isLoading || !kiosk.employee_code"
                                class="bg-primary-600 hover:bg-indigo-50/500 text-white px-6 py-3 rounded-xl font-medium transition-all shadow-lg shadow-primary-900/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none">
                            <span x-show="!isLoading"><x-heroicon-o-camera class="mr-2 w-5"/>สแกน</span>
                            <span x-show="isLoading"><x-heroicon-o-arrow-path class="w-5"/></span>
                        </button>
                    </div>
                    
                    <!-- Device Config Toggle -->
                    <div class="flex justify-between items-center text-xs text-indigo-600/70 pt-2">
                        <span>ระบบจดจำใบหน้าอัตโนมัติ</span>
                        <button @click="showConfig = !showConfig" class="hover:text-slate-300 transition-colors flex items-center gap-1">
                            <x-heroicon-o-cog-6-tooth class="w-5"/> ตั้งค่าอุปกรณ์
                        </button>
                    </div>

                    <!-- Config Panel -->
                    <div x-show="showConfig" class="pt-4 border-t border-slate-700 mt-2 space-y-3" x-transition>
                        <!-- Camera Selector -->
                        <div x-show="cameras.length > 0">
                            <label class="text-xs text-primary-400 block mb-1.5">เลือกกล้อง</label>
                            <select x-model="selectedCamera" @change="startCamera()" class="w-full bg-slate-900 border-slate-600 text-white text-xs rounded-lg px-3 py-2 focus:ring-primary-500 focus:border-slate-200/600">
                                <template x-for="camera in cameras" :key="camera.deviceId">
                                    <option :value="camera.deviceId" x-text="camera.label || 'Camera ' + ($index + 1)"></option>
                                </template>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <input type="text" x-model="kiosk.device_code" placeholder="รหัสอุปกรณ์ (เช่น DEV-001)" class="bg-slate-900 border-slate-600 text-white text-xs rounded-lg px-3 py-2 focus:ring-primary-500 focus:border-slate-200/600">
                            <input type="password" x-model="kiosk.api_token" placeholder="API Token" class="bg-slate-900 border-slate-600 text-white text-xs rounded-lg px-3 py-2 focus:ring-primary-500 focus:border-slate-200/600">
                        </div>
                        <button @click="saveConfig()" class="w-full bg-slate-700 hover:bg-slate-600 text-white text-xs py-2.5 rounded-lg font-medium transition-colors">บันทึกการตั้งค่า</button>
                    </div>
                </div>

                <!-- Hidden Canvas for Snapshot -->
                <canvas x-ref="canvasElement" class="hidden"></canvas>
            </div>

        </div>

        <!-- Right: Recent Scans List (Smaller) -->
        <div class="lg:col-span-1 bg-card rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-200/60 flex flex-col overflow-hidden h-full">
            <div class="px-6 py-4 border-b border-slate-50 flex justify-between items-center bg-card sticky top-0 z-10">
                <div>
                    <h3 class="font-bold text-text font-bold font-mono text-lg font-mono">ประวัติล่าสุด</h3>
                </div>
                <div class="flex items-center gap-2 text-[10px] font-bold text-emerald-600 px-3 py-1 bg-emerald-50 rounded-full border border-emerald-100 shadow-sm">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-500 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    Live
                </div>
            </div>
            
            <div class="flex-1 overflow-y-auto p-0 relative custom-scrollbar">
                 <!-- List Items -->
                 <ul class="divide-y divide-slate-50">
                    <template x-for="scan in history" :key="scan.id">
                        <li class="px-6 py-4 hover:bg-slate-50/80 transition-all duration-300 flex items-center justify-between animate-fade-in-down group cursor-default border-l-4 border-transparent hover:border-slate-200/600">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center overflow-hidden border-2 border-white shadow-sm group-hover:border-slate-200 transition-colors">
                                    <!-- Prefer Snapshot, fallback to Profile Photo -->
                                    <template x-if="scan.snapshot_url || scan.photo_url">
                                        <img :src="scan.snapshot_url || scan.photo_url" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!scan.snapshot_url && !scan.photo_url">
                                        <x-heroicon-o-user class="text-slate-300 text-sm w-5"/>
                                    </template>
                                </div>
                                <div>
                                    <p class="font-bold text-text text-sm group-hover:text-primary-700 transition-colors" x-text="scan.name"></p>
                                    <p class="text-xs text-primary-400" x-text="scan.time"></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide shadow-sm"
                                      :class="scan.is_late ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700'"
                                      x-text="scan.status_text || scan.scan_type || scan.type"></span>
                            </div>
                        </li>
                    </template>
                 </ul>
                 
                 <!-- Empty State -->
                 <div x-show="history.length === 0" class="absolute inset-0 flex flex-col items-center justify-center text-slate-300 bg-slate-50/30">
                     <div class="w-16 h-16 bg-card rounded-full shadow-sm flex items-center justify-center mb-3">
                        <x-heroicon-o-signal class="text-2xl text-slate-200 w-5"/>
                     </div>
                     <p class="text-xs text-primary-400">รอรับข้อมูล...</p>
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
            
            // Kiosk Data
            stream: null,
            isLoading: false,
            showConfig: false,
            kiosk: {
                employee_code: '',
                device_code: localStorage.getItem('kiosk_device_code') || '',
                api_token: localStorage.getItem('kiosk_api_token') || ''
            },
            
            // Camera & Face Recognition
            cameras: [],
            selectedCamera: '',
            isModelsLoading: true,
            statusMessage: 'กำลังโหลดโมเดล...',
            faceMatcher: null,
            detectionInterval: null,
            lastScanTime: 0,

            // Blink Detection State (Liveness)
            livenessStatus: 'idle', // idle, waiting_for_blink, success
            blinkState: 'open', // open, closed
            earThreshold: 0.25, // Eye Aspect Ratio threshold for blink
            blinkDetected: false,
            pendingEmployeeCode: null, // Store employee code while waiting for blink
            
            async initMonitor() {
                // 1. Subscribe to Pusher
                if (typeof window.Echo !== 'undefined') {
                    window.Echo.channel('scans')
                        .listen('.new-scan', (e) => {
                            this.handleNewScan(e.employee);
                        });
                }

                // 2. Load Face API Models
                try {
                    let modelPath = "{{ asset('models') }}";
                    if (location.protocol === 'https:' && modelPath.startsWith('http:')) {
                        modelPath = modelPath.replace('http:', 'https:');
                    }
                    console.log('Loading models from:', modelPath);

                    await Promise.all([
                        // Use Tiny Face Detector for speed
                        faceapi.loadTinyFaceDetectorModel(modelPath), 
                        faceapi.loadFaceLandmarkModel(modelPath),
                        faceapi.loadFaceRecognitionModel(modelPath)
                    ]);
                    
                    this.isModelsLoading = false;
                    this.statusMessage = 'พร้อมใช้งาน';
                    console.log('Face API Models Loaded Successfully');
                    
                    // 3. Load Labeled Images
                    await this.loadLabeledImages();

                } catch (error) {
                    console.error('Error loading models:', error);
                    this.statusMessage = 'โหลดโมเดลล้มเหลว: ' + (error.message || 'Unknown error');
                }
                
                // Start Camera Automatically
                this.startCamera();
            },

            async loadLabeledImages() {
                this.statusMessage = 'กำลังเรียนรู้ใบหน้า...';
                try {
                    const response = await axios.get("{{ route('api.employees.faces') }}".replace(/^http:/, location.protocol));
                    const employees = response.data.data;

                    const labeledDescriptors = await Promise.all(
                        employees.map(async (employee) => {
                            try {
                                let photoUrl = employee.photo_url;
                                // Fix Mixed Content (HTTP images on HTTPS site)
                                if (location.protocol === 'https:' && photoUrl.startsWith('http:')) {
                                    photoUrl = photoUrl.replace('http:', 'https:');
                                }
                                const img = await faceapi.fetchImage(photoUrl);
                                // Use TinyFaceDetectorOptions
                                const detections = await faceapi.detectSingleFace(img, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptor();
                                
                                if (!detections) {
                                    console.warn(`No face detected for ${employee.name}`);
                                    return null;
                                }
                                
                                return new faceapi.LabeledFaceDescriptors(employee.employee_code, [detections.descriptor]);
                            } catch (err) {
                                console.error(`Error processing ${employee.name}:`, err);
                                return null;
                            }
                        })
                    );

                    const validDescriptors = labeledDescriptors.filter(d => d !== null);
                    if (validDescriptors.length > 0) {
                        this.faceMatcher = new faceapi.FaceMatcher(validDescriptors, 0.35);
                        this.statusMessage = `เรียนรู้ ${validDescriptors.length} ใบหน้าแล้ว`;
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

                    // Wait for video to play
                    video.onloadedmetadata = () => {
                        video.play();
                        this.startFaceDetection();
                    };

                    await this.getCameras();

                } catch (err) {
                    console.error("Error accessing camera:", err);
                    alert("Cannot access camera. Please allow permissions.");
                }
            },

            startFaceDetection() {
                const video = this.$refs.videoElement;
                const canvas = this.$refs.overlayElement;
                
                if (this.detectionInterval) clearInterval(this.detectionInterval);

                this.detectionInterval = setInterval(async () => {
                    if (!video.videoWidth) return;

                    // Match canvas size to video
                    const displaySize = { width: video.videoWidth, height: video.videoHeight };
                    faceapi.matchDimensions(canvas, displaySize);

                    // Detect faces using Tiny Model
                    const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptors();
                    const resizedDetections = faceapi.resizeResults(detections, displaySize);

                    // Clear canvas
                    canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);

                    if (this.faceMatcher) {
                        const results = resizedDetections.map(d => this.faceMatcher.findBestMatch(d.descriptor));

                        // Reset state if no face found
                        if (results.length === 0) {
                            // Reset ถ้าไม่เจอหน้าและกำลังรอกระพริบตา
                            if (this.livenessStatus === 'waiting_for_blink') {
                                this.livenessStatus = 'idle';
                                this.pendingEmployeeCode = null;
                            }
                        }

                        results.forEach((result, i) => {
                            const box = resizedDetections[i].detection.box;
                            const landmarks = resizedDetections[i].landmarks;
                            
                            // Draw Box
                            const drawBox = new faceapi.draw.DrawBox(box, { label: result.toString() });
                            drawBox.draw(canvas);

                            // Auto Scan Logic (ไม่ต้องกระพริบตา)
                            if (result.label !== 'unknown' && !this.isLoading) {
                                const now = Date.now();
                                
                                // Only process if we haven't scanned this person recently (5 seconds cooldown)
                                if (now - this.lastScanTime > 5000) {
                                    // ยืนยันสำเร็จทันที
                                    this.livenessStatus = 'success';
                                    this.statusMessage = 'พบใบหน้า กำลังบันทึก...';
                                    
                                    // Submit Scan
                                    this.kiosk.employee_code = result.label;
                                    this.submitScan();
                                    this.lastScanTime = now;
                                    
                                    // Reset หลังจาก 2 วินาที
                                    setTimeout(() => {
                                        this.livenessStatus = 'idle';
                                    }, 2000);
                                }
                            }
                        });
                    }
                }, 100); // Check every 100ms
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
                localStorage.setItem('kiosk_device_code', this.kiosk.device_code);
                localStorage.setItem('kiosk_api_token', this.kiosk.api_token);
                this.showConfig = false;
                alert('บันทึกการตั้งค่าเรียบร้อยแล้ว!');
            },

            calculateEAR(eye) {
                const distance = (p1, p2) => Math.sqrt(Math.pow(p1.x - p2.x, 2) + Math.pow(p1.y - p2.y, 2));
                const a = distance(eye[1], eye[5]);
                const b = distance(eye[2], eye[4]);
                const c = distance(eye[0], eye[3]);
                return (a + b) / (2.0 * c);
            },

            // Audio Objects
            successAudio: new Audio("{{ asset('success.wav') }}".replace(/^http:/, location.protocol)),
            errorAudio: new Audio("{{ asset('error.wav') }}".replace(/^http:/, location.protocol)),

            async submitScan() {
                if (!this.kiosk.employee_code) return;
                if (!this.kiosk.device_code || !this.kiosk.api_token) {
                    alert('กรุณาตั้งค่า Device Code และ API Token ก่อนใช้งาน');
                    this.showConfig = true;
                    return;
                }

                this.isLoading = true;
                this.statusMessage = 'กำลังบันทึก...';

                try {
                    // 1. Capture Snapshot
                    const canvas = document.createElement('canvas'); // Use temp canvas
                    const video = this.$refs.videoElement;
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    canvas.getContext('2d').drawImage(video, 0, 0);
                    const snapshot = canvas.toDataURL('image/jpeg');

                    // 2. Call API
                    const response = await axios.post("{{ route('api.scan.store') }}".replace(/^http:/, location.protocol), {
                        device_code: this.kiosk.device_code,
                        api_token: this.kiosk.api_token,
                        employee_code: this.kiosk.employee_code,
                        snapshot: snapshot
                    });

                    if (response.data.success) {
                        this.kiosk.employee_code = '';
                        this.statusMessage = 'บันทึกสำเร็จ!';
                        this.successAudio.play().catch(e => console.log("Audio play failed:", e)); // Play Success Sound
                        
                        // Reset status message after delay
                        setTimeout(() => { this.statusMessage = 'พร้อมใช้งาน'; }, 2000);
                    }

                } catch (error) {
                    console.error(error);
                    this.statusMessage = 'เกิดข้อผิดพลาด';
                    this.errorAudio.play().catch(e => console.log("Audio play failed:", e)); // Play Error Sound
                    // alert(error.response?.data?.message || 'การสแกนล้มเหลว');
                } finally {
                    this.isLoading = false;
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
