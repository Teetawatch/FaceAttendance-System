@extends('layouts.app')

@section('title', 'จอภาพ & จุดลงเวลา')

@section('content')
<!-- Load face-api.js -->
<script src="{{ asset('js/face-api.min.js') }}"></script>

<div x-data="monitorApp()" x-init="initMonitor()" class="h-[calc(100vh-8rem)]">
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 h-full">
        
        <!-- Left Column: Switchable between Display & Camera Scanner -->
        <div class="lg:col-span-1 flex flex-col gap-6 h-full">
            
            <!-- Toggle Mode Button -->
            <div class="card p-2 flex justify-between items-center px-4">
                <span class="text-sm font-medium text-muted">โหมดการทำงาน:</span>
                <div class="flex bg-surface-50 rounded-xl p-1 border border-primary-100/60">
                    <button @click="toggleMode('monitor')" 
                            :class="mode === 'monitor' ? 'bg-white shadow-sm text-primary-600' : 'text-muted hover:text-text'
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center gap-2">
                         จอภาพ
                    </button>
                    <button @click="toggleMode('kiosk')" 
                            :class="mode === 'kiosk' ? 'bg-white shadow-sm text-primary-600' : 'text-muted hover:text-text'
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center gap-2">
                         จุดลงเวลา
                    </button>
                </div>
            </div>

            <!-- Mode 1: MONITOR DISPLAY (Hero Section) -->
            <div x-show="mode === 'monitor'" class="flex-1 bg-white rounded-2xl border border-primary-100/60 p-8 flex flex-col items-center justify-center text-center relative overflow-hidden transition-all">
                <!-- Pulse Animation Background (Active when scan comes) -->
                <div x-show="justScanned" x-transition.opacity.duration.1000ms class="absolute inset-0 bg-emerald-50/50 z-0" style="display: none;"></div>
                
                <div class="relative z-10 w-full flex flex-col items-center">
                    <h3 class="text-muted font-medium uppercase tracking-widest text-xs mb-8 bg-surface-50 px-3 py-1 rounded-full border border-primary-100/60 font-mono">รายการล่าสุด</h3>
                    
                    <!-- Profile Image -->
                    <div class="relative mb-8 group">
                        <div class="w-56 h-56 rounded-full border-4 border-primary-100 overflow-hidden bg-surface-50 flex items-center justify-center relative z-10">
                            <!-- Prefer Snapshot, fallback to Profile Photo -->
                            <template x-if="latestScan.snapshot_url || latestScan.photo_url">
                                <img :src="latestScan.snapshot_url || latestScan.photo_url" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            </template>
                            <template x-if="!latestScan.snapshot_url && !latestScan.photo_url">
                                
                            </template>
                        </div>
                        <!-- Decorative Ring -->
                        <div class="absolute inset-0 rounded-full border border-primary-100/60 scale-110 -z-0"></div>
                        <div class="absolute inset-0 rounded-full border border-primary-50 scale-125 -z-0"></div>

                        <!-- Status Badge -->
                        <div class="absolute bottom-4 right-4 px-6 py-2 rounded-2xl text-white font-bold shadow-lg text-xl capitalize z-20 border-4 border-white transform transition-transform group-hover:scale-105"
                             :class="latestScan.type === 'IN' ? 'bg-emerald-500' : 'bg-amber-500'"
                             x-text="latestScan.type || '-'">
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="space-y-2 mb-8">
                        <h2 class="text-3xl font-bold text-text" x-text="latestScan.name || 'รอรับข้อมูล...'"></h2>
                        <p class="text-muted text-lg flex items-center justify-center gap-2">
                            
                            <span x-text="latestScan.device || 'ระบบพร้อมใช้งาน'"></span>
                        </p>
                    </div>

                    <!-- Time -->
                    <div class="text-6xl font-mono font-bold text-text tracking-tight bg-surface-50 px-8 py-4 rounded-2xl border border-primary-100/60" x-text="latestScan.time || '--:--:--'"></div>
                </div>
            </div>

            <!-- Mode 2: KIOSK CAMERA (Active Scanner) -->
            <div x-show="mode === 'kiosk'" class="flex-1 bg-slate-900 rounded-3xl shadow-lg border border-slate-800 p-6 flex flex-col relative overflow-hidden" style="display: none;">
                
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

                    <!-- Liveness Instruction Overlay -->
                    <div x-show="livenessStatus === 'waiting_for_blink'" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-90"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute inset-0 flex flex-col items-center justify-center z-30 pointer-events-none">
                        <div class="bg-black/70 backdrop-blur-md text-white px-8 py-6 rounded-3xl border border-white/20 shadow-2xl flex flex-col items-center animate-pulse">
                            
                            <h3 class="text-2xl font-bold font-mono">กรุณากระพริบตา</h3>
                            <p class="text-slate-300 text-sm mt-2">เพื่อยืนยันตัวตน</p>
                        </div>
                    </div>

                    <!-- Loading Models Indicator -->
                    <div x-show="isModelsLoading" class="absolute inset-0 bg-black/80 flex flex-col items-center justify-center z-20 text-white">
                        
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
                            <span x-show="!isLoading">สแกน</span>
                            <span x-show="isLoading"></span>
                        </button>
                    </div>
                    
                    <!-- Device Config Toggle -->
                    <div class="flex justify-between items-center text-xs text-muted pt-2">
                        <span>ระบบจดจำใบหน้าอัตโนมัติ</span>
                        <button @click="showConfig = !showConfig" class="hover:text-slate-300 transition-colors flex items-center gap-1">
                             ตั้งค่าอุปกรณ์
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

        <!-- Right: Recent Scans List -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-primary-100/60 flex flex-col overflow-hidden h-full">
            <div class="px-6 py-5 border-b border-primary-100/60 flex justify-between items-center bg-white sticky top-0 z-10">
                <div>
                    <h3 class="text-lg font-semibold text-text">ประวัติการเข้างานล่าสุด</h3>
                    <p class="text-muted text-sm mt-0.5">ข้อมูล Real-time จากทุกจุดลงเวลา</p>
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
                 <ul class="divide-y divide-primary-50/60">
                    <template x-for="scan in history" :key="scan.id">
                        <li class="px-6 py-4 hover:bg-surface-50 transition-colors duration-150 flex items-center justify-between animate-fade-in-down group cursor-default border-l-4 border-transparent hover:border-primary-300">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-surface-50 flex items-center justify-center overflow-hidden border border-primary-100/60 group-hover:border-primary-200 transition-colors duration-150">
                                    <!-- Prefer Snapshot, fallback to Profile Photo -->
                                    <template x-if="scan.snapshot_url || scan.photo_url">
                                        <img :src="scan.snapshot_url || scan.photo_url" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!scan.snapshot_url && !scan.photo_url">
                                        
                                    </template>
                                </div>
                                <div>
                                    <p class="font-semibold text-text text-sm group-hover:text-primary-700 transition-colors duration-150" x-text="scan.name"></p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-xs text-muted bg-surface-50 px-2 py-0.5 rounded-lg border border-primary-100/60 flex items-center gap-1">
                                            
                                            <span x-text="scan.device"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide mb-1 shadow-sm"
                                      :class="scan.type === 'IN' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'"
                                      x-text="scan.type"></span>
                                <p class="text-xs font-medium text-muted" 
                                   :class="scan.status_color"
                                   x-text="scan.datetime_th || scan.time"></p>
                            </div>
                        </li>
                    </template>
                 </ul>
                 
                 <!-- Empty State -->
                 <div x-show="history.length === 0" class="absolute inset-0 flex flex-col items-center justify-center bg-surface-50/30">
                     <div class="w-16 h-16 bg-white rounded-2xl border border-primary-100/60 flex items-center justify-center mb-4">
                        
                     </div>
                     <p class="font-medium text-muted text-sm">กำลังรอรับข้อมูล...</p>
                     <p class="text-xs text-muted mt-1">ข้อมูลการสแกนจะปรากฏที่นี่ทันที</p>
                 </div>
            </div>
        </div>
    </div>
</div>

<script>
    function monitorApp() {
        return {
            mode: 'monitor',
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

            // Liveness Detection State
            livenessStatus: 'idle', // idle, detecting, waiting_for_blink, success
            blinkCounter: 0,
            eyeClosedThreshold: 0.25, // EAR threshold for closed eye
            consecutiveClosedFrames: 0,
            requiredClosedFrames: 1, // Number of frames eye must be closed to count as blink start

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
                    const modelPath = "{{ asset('models') }}";
                    console.log('Loading models from:', modelPath);

                    await Promise.all([
                        faceapi.loadSsdMobilenetv1Model(modelPath),
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
            },

            async loadLabeledImages() {
                this.statusMessage = 'กำลังเรียนรู้ใบหน้า...';
                try {
                    const response = await axios.get('/api/v1/employees/faces');
                    const employees = response.data.data;

                    const labeledDescriptors = await Promise.all(
                        employees.map(async (employee) => {
                            try {
                                const img = await faceapi.fetchImage(employee.photo_url);
                                const detections = await faceapi.detectSingleFace(img).withFaceLandmarks().withFaceDescriptor();
                                
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

            toggleMode(newMode) {
                this.mode = newMode;
                if (newMode === 'kiosk') {
                    this.startCamera();
                } else {
                    this.stopCamera();
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

                    // Detect faces
                    const detections = await faceapi.detectAllFaces(video).withFaceLandmarks().withFaceDescriptors();
                    const resizedDetections = faceapi.resizeResults(detections, displaySize);

                    // Clear canvas
                    canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);

                    if (this.faceMatcher) {
                        const results = resizedDetections.map(d => this.faceMatcher.findBestMatch(d.descriptor));

                        // Reset liveness if no face found
                        if (results.length === 0) {
                            this.livenessStatus = 'idle';
                            this.consecutiveClosedFrames = 0;
                        }

                        results.forEach((result, i) => {
                            const box = resizedDetections[i].detection.box;
                            const landmarks = resizedDetections[i].landmarks;
                            
                            // Draw Box
                            const drawBox = new faceapi.draw.DrawBox(box, { label: result.toString() });
                            drawBox.draw(canvas);

                            // Auto Scan Logic with Liveness
                            if (result.label !== 'unknown' && !this.isLoading) {
                                const now = Date.now();
                                
                                // Only process if we haven't scanned this person recently
                                if (now - this.lastScanTime > 5000) {
                                    
                                    // State Machine for Liveness
                                    if (this.livenessStatus === 'idle') {
                                        this.livenessStatus = 'waiting_for_blink';
                                        this.statusMessage = 'กรุณากระพริบตา';
                                    }

                                    if (this.livenessStatus === 'waiting_for_blink') {
                                        const leftEye = landmarks.getLeftEye();
                                        const rightEye = landmarks.getRightEye();
                                        
                                        const leftEAR = this.calculateEAR(leftEye);
                                        const rightEAR = this.calculateEAR(rightEye);
                                        const avgEAR = (leftEAR + rightEAR) / 2;

                                        // Check for blink
                                        if (avgEAR < this.eyeClosedThreshold) {
                                            this.consecutiveClosedFrames++;
                                        } else {
                                            // If eyes were closed for enough frames and now open -> BLINK DETECTED!
                                            if (this.consecutiveClosedFrames >= this.requiredClosedFrames) {
                                                this.livenessStatus = 'success';
                                                this.statusMessage = 'ยืนยันตัวตนสำเร็จ!';
                                                
                                                // Submit Scan
                                                this.kiosk.employee_code = result.label;
                                                this.submitScan();
                                                this.lastScanTime = now;
                                                
                                                // Reset after success
                                                setTimeout(() => {
                                                    this.livenessStatus = 'idle';
                                                    this.consecutiveClosedFrames = 0;
                                                }, 2000);
                                            }
                                            this.consecutiveClosedFrames = 0;
                                        }
                                    }
                                }
                            }
                        });
                    }
                }, 100); // Check every 100ms
            },

            calculateEAR(eye) {
                // EAR = (|p2-p6| + |p3-p5|) / (2 * |p1-p4|)
                const p2_p6 = Math.hypot(eye[1].x - eye[5].x, eye[1].y - eye[5].y);
                const p3_p5 = Math.hypot(eye[2].x - eye[4].x, eye[2].y - eye[4].y);
                const p1_p4 = Math.hypot(eye[0].x - eye[3].x, eye[0].y - eye[3].y);
                return (p2_p6 + p3_p5) / (2 * p1_p4);
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
                    const response = await axios.post('/api/v1/scan', {
                        device_code: this.kiosk.device_code,
                        api_token: this.kiosk.api_token,
                        employee_code: this.kiosk.employee_code,
                        snapshot: snapshot
                    });

                    if (response.data.success) {
                        this.kiosk.employee_code = '';
                        this.statusMessage = 'บันทึกสำเร็จ!';
                        // Reset status message after delay
                        setTimeout(() => { this.statusMessage = 'พร้อมใช้งาน'; }, 2000);
                    }

                } catch (error) {
                    console.error(error);
                    this.statusMessage = 'เกิดข้อผิดพลาด';
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



