@extends('layouts.app')

@section('title', 'ลงทะเบียนใบหน้า')

@section('content')
<div x-data="faceRegisterApp()" x-init="init()" class="max-w-6xl mx-auto">
    
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg shadow-violet-500/25">
                <i class="fa-solid fa-user-plus text-white text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">ลงทะเบียนใบหน้า</h1>
                <p class="text-slate-500 text-sm">ถ่ายรูปใบหน้าพนักงานจากกล้องโดยตรง เพื่อใช้ในการสแกนลงเวลา</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Left: Camera Section -->
        <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
            
            <!-- Camera Header -->
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-slate-50 to-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-slate-900 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-camera text-white"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-slate-800">กล้อง</h2>
                        <p class="text-xs text-slate-400" x-text="cameraStatus"></p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75"
                              :class="isCameraReady ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3"
                              :class="isCameraReady ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                    </span>
                </div>
            </div>

            <!-- Camera Feed -->
            <div class="relative aspect-[4/3] bg-slate-900">
                <video x-ref="videoElement" autoplay playsinline muted 
                       class="w-full h-full object-cover transform -scale-x-100"></video>
                
                <!-- Camera Loading -->
                <div x-show="!isCameraReady" class="absolute inset-0 bg-slate-900 flex flex-col items-center justify-center text-white">
                    <i class="fa-solid fa-circle-notch fa-spin text-4xl mb-4 text-violet-400"></i>
                    <p class="text-sm text-slate-400">กำลังเปิดกล้อง...</p>
                </div>

                <!-- Face Guide Overlay -->
                <div x-show="isCameraReady && !capturedImage" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="w-56 h-72 border-4 border-dashed border-white/40 rounded-[4rem] relative">
                        <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-black/60 backdrop-blur-md text-white px-4 py-1.5 rounded-full text-xs whitespace-nowrap">
                            <i class="fa-solid fa-crosshairs mr-1.5"></i>วางใบหน้าให้อยู่ในกรอบ
                        </div>
                    </div>
                </div>

                <!-- Captured Image Overlay -->
                <div x-show="capturedImage" class="absolute inset-0">
                    <img :src="capturedImage" class="w-full h-full object-cover transform -scale-x-100" alt="Captured">
                    <div class="absolute inset-0 bg-black/20"></div>
                    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-emerald-500 text-white px-6 py-2 rounded-full text-sm font-medium shadow-lg">
                        <i class="fa-solid fa-check mr-2"></i>ถ่ายรูปแล้ว
                    </div>
                </div>
            </div>

            <!-- Camera Controls -->
            <div class="p-6 bg-slate-900 border-t border-slate-800">
                <div class="flex gap-3">
                    <template x-if="!capturedImage">
                        <button @click="capturePhoto()" 
                                :disabled="!isCameraReady || !selectedEmployee"
                                class="flex-1 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-500 hover:to-purple-500 text-white py-4 rounded-2xl font-bold shadow-lg shadow-violet-500/30 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none flex items-center justify-center gap-2 text-lg">
                            <i class="fa-solid fa-camera text-xl"></i>
                            <span>ถ่ายรูป</span>
                        </button>
                    </template>
                    <template x-if="capturedImage">
                        <div class="flex-1 flex gap-3">
                            <button @click="retakePhoto()" 
                                    class="flex-1 bg-slate-700 hover:bg-slate-600 text-white py-4 rounded-2xl font-bold transition-all flex items-center justify-center gap-2 border border-slate-500 text-lg">
                                <i class="fa-solid fa-rotate-left"></i>
                                <span>ถ่ายใหม่</span>
                            </button>
                            <button @click="savePhoto()" 
                                    :disabled="isLoading"
                                    class="flex-1 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white py-4 rounded-2xl font-bold shadow-lg shadow-emerald-500/30 transition-all disabled:opacity-50 flex items-center justify-center gap-2 text-lg">
                                <template x-if="!isLoading">
                                    <span class="flex items-center gap-2"><i class="fa-solid fa-floppy-disk"></i> บันทึก</span>
                                </template>
                                <template x-if="isLoading">
                                    <i class="fa-solid fa-circle-notch fa-spin"></i>
                                </template>
                            </button>
                        </div>
                    </template>
                </div>

                <!-- Camera Selector -->
                <div x-show="cameras.length > 1" class="mt-4">
                    <label class="text-sm text-white font-medium block mb-2">เลือกกล้อง</label>
                    <select x-model="selectedCamera" @change="startCamera()" 
                            class="w-full bg-slate-800 border border-slate-600 text-white text-sm rounded-xl px-4 py-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                        <template x-for="camera in cameras" :key="camera.deviceId">
                            <option :value="camera.deviceId" x-text="camera.label || 'Camera ' + ($index + 1)"></option>
                        </template>
                    </select>
                </div>
            </div>
        </div>

        <!-- Right: Employee Selection & Info -->
        <div class="space-y-6">
            
            <!-- Employee Selector Card -->
            <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                    <h2 class="font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-user-check text-violet-500"></i>
                        เลือกพนักงาน
                    </h2>
                </div>
                <div class="p-6">
                    <!-- Search Input -->
                    <div class="relative mb-4">
                        <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" x-model="searchQuery" 
                               @input="filterEmployees()"
                               placeholder="ค้นหาพนักงาน..." 
                               class="w-full bg-slate-50 border-0 text-slate-700 rounded-xl pl-11 pr-4 py-3 focus:ring-2 focus:ring-violet-500">
                    </div>

                    <!-- Employee List -->
                    <div class="max-h-80 overflow-y-auto space-y-2 custom-scrollbar">
                        <template x-for="emp in filteredEmployees" :key="emp.id">
                            <div @click="selectEmployee(emp)" 
                                 :class="selectedEmployee?.id === emp.id 
                                     ? 'bg-violet-50 border-violet-300 ring-2 ring-violet-500/20' 
                                     : 'bg-white border-slate-200 hover:border-violet-200 hover:bg-slate-50'"
                                 class="flex items-center gap-4 p-4 rounded-2xl border cursor-pointer transition-all">
                                
                                <!-- Photo -->
                                <div class="w-14 h-14 rounded-xl overflow-hidden flex-shrink-0 border-2 border-white shadow-sm"
                                     :class="emp.has_photo ? 'bg-slate-100' : 'bg-slate-200'">
                                    <template x-if="emp.has_photo">
                                        <img :src="emp.photo_url" class="w-full h-full object-cover" alt="">
                                    </template>
                                    <template x-if="!emp.has_photo">
                                        <div class="w-full h-full flex items-center justify-center text-slate-400">
                                            <i class="fa-solid fa-user text-xl"></i>
                                        </div>
                                    </template>
                                </div>

                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-slate-700 truncate" x-text="emp.name"></p>
                                    <p class="text-sm text-slate-400" x-text="emp.employee_code"></p>
                                </div>

                                <!-- Status Badge -->
                                <div class="flex-shrink-0">
                                    <span x-show="emp.has_photo" 
                                          class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-100 text-emerald-700">
                                        <i class="fa-solid fa-check mr-1"></i>มีรูป
                                    </span>
                                    <span x-show="!emp.has_photo" 
                                          class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-amber-100 text-amber-700">
                                        <i class="fa-solid fa-exclamation mr-1"></i>ไม่มีรูป
                                    </span>
                                </div>

                                <!-- Selection Indicator -->
                                <div x-show="selectedEmployee?.id === emp.id" 
                                     class="w-6 h-6 bg-violet-600 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-check text-white text-xs"></i>
                                </div>
                            </div>
                        </template>

                        <!-- Empty State -->
                        <div x-show="filteredEmployees.length === 0" class="text-center py-8 text-slate-400">
                            <i class="fa-solid fa-users-slash text-3xl mb-3"></i>
                            <p class="text-sm">ไม่พบพนักงาน</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Selected Employee Preview -->
            <div x-show="selectedEmployee" x-transition class="bg-slate-900 rounded-3xl shadow-2xl p-6 text-white border border-slate-700">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 rounded-2xl overflow-hidden bg-slate-800 border-2 border-slate-600 shadow-lg">
                        <template x-if="selectedEmployee?.has_photo">
                            <img :src="selectedEmployee?.photo_url" class="w-full h-full object-cover" alt="">
                        </template>
                        <template x-if="!selectedEmployee?.has_photo">
                            <div class="w-full h-full flex items-center justify-center text-slate-400">
                                <i class="fa-solid fa-user text-2xl"></i>
                            </div>
                        </template>
                    </div>
                    <div>
                        <p class="font-bold text-xl text-white" x-text="selectedEmployee?.name"></p>
                        <p class="text-slate-300 text-sm font-medium" x-text="selectedEmployee?.employee_code"></p>
                    </div>
                </div>
                <div class="bg-slate-800 rounded-xl p-4 border border-slate-700">
                    <p class="text-white text-sm font-medium">
                        <i class="fa-solid fa-info-circle mr-2 text-violet-400"></i>
                        <span x-show="selectedEmployee?.has_photo">รูปใบหน้าปัจจุบันจะถูกแทนที่ด้วยรูปใหม่</span>
                        <span x-show="!selectedEmployee?.has_photo">พนักงานคนนี้ยังไม่มีรูปใบหน้าในระบบ</span>
                    </p>
                </div>
            </div>

            <!-- Instructions -->
            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100">
                <h3 class="font-bold text-slate-700 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-lightbulb text-amber-500"></i>
                    คำแนะนำในการถ่ายรูป
                </h3>
                <ul class="space-y-3 text-sm text-slate-600">
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-violet-100 text-violet-600 rounded-lg flex items-center justify-center text-xs flex-shrink-0 mt-0.5">1</span>
                        <span>เลือกพนักงานจากรายการด้านบน</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-violet-100 text-violet-600 rounded-lg flex items-center justify-center text-xs flex-shrink-0 mt-0.5">2</span>
                        <span>วางใบหน้าให้อยู่ตรงกลางกรอบและแสงสว่างเพียงพอ</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-violet-100 text-violet-600 rounded-lg flex items-center justify-center text-xs flex-shrink-0 mt-0.5">3</span>
                        <span>กดปุ่ม "ถ่ายรูป" และตรวจสอบรูปก่อนบันทึก</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-violet-100 text-violet-600 rounded-lg flex items-center justify-center text-xs flex-shrink-0 mt-0.5">4</span>
                        <span>กดปุ่ม "บันทึก" เพื่อบันทึกรูปลงระบบ</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Success Toast -->
    <div x-show="showSuccess" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed bottom-8 right-8 bg-emerald-600 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4 z-50"
         style="display: none;">
        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
            <i class="fa-solid fa-check text-2xl"></i>
        </div>
        <div>
            <p class="font-bold">บันทึกสำเร็จ!</p>
            <p class="text-emerald-100 text-sm" x-text="successMessage"></p>
        </div>
    </div>

    <!-- Hidden Canvas for Snapshot -->
    <canvas x-ref="canvasElement" class="hidden"></canvas>
</div>

<script>
function faceRegisterApp() {
    return {
        // Camera
        stream: null,
        cameras: [],
        selectedCamera: '',
        isCameraReady: false,
        cameraStatus: 'กำลังเริ่มต้น...',
        
        // Employees
        employees: @json($employeeList),
        filteredEmployees: [],
        searchQuery: '',
        selectedEmployee: null,
        
        // Capture State
        capturedImage: null,
        isLoading: false,
        
        // Toast
        showSuccess: false,
        successMessage: '',

        async init() {
            this.filteredEmployees = this.employees;
            await this.startCamera();
            await this.getCameras();
        },

        filterEmployees() {
            const query = this.searchQuery.toLowerCase();
            this.filteredEmployees = this.employees.filter(emp => 
                emp.name.toLowerCase().includes(query) || 
                emp.employee_code.toLowerCase().includes(query)
            );
        },

        selectEmployee(emp) {
            this.selectedEmployee = emp;
            this.capturedImage = null; // Reset captured image when changing employee
        },

        async startCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
            }

            this.isCameraReady = false;
            this.cameraStatus = 'กำลังเปิดกล้อง...';

            try {
                const constraints = {
                    video: this.selectedCamera 
                        ? { deviceId: { exact: this.selectedCamera } } 
                        : { facingMode: 'user' }
                };

                this.stream = await navigator.mediaDevices.getUserMedia(constraints);
                const video = this.$refs.videoElement;
                video.srcObject = this.stream;

                video.onloadedmetadata = () => {
                    video.play();
                    this.isCameraReady = true;
                    this.cameraStatus = 'กล้องพร้อมใช้งาน';
                };
            } catch (err) {
                console.error('Error accessing camera:', err);
                this.cameraStatus = 'ไม่สามารถเข้าถึงกล้องได้';
                alert('ไม่สามารถเข้าถึงกล้องได้ กรุณาอนุญาตการใช้งานกล้อง');
            }
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
                console.error('Error listing cameras:', err);
            }
        },

        capturePhoto() {
            const video = this.$refs.videoElement;
            const canvas = this.$refs.canvasElement;
            
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            
            const ctx = canvas.getContext('2d');
            // Mirror the image to match the video display
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
            ctx.drawImage(video, 0, 0);
            
            this.capturedImage = canvas.toDataURL('image/jpeg', 0.9);
        },

        retakePhoto() {
            this.capturedImage = null;
        },

        async savePhoto() {
            if (!this.capturedImage || !this.selectedEmployee) return;

            this.isLoading = true;

            try {
                const response = await axios.post("{{ route('face.register.store') }}", {
                    employee_id: this.selectedEmployee.id,
                    photo: this.capturedImage
                });

                if (response.data.success) {
                    // Update local data
                    const emp = this.employees.find(e => e.id === this.selectedEmployee.id);
                    if (emp) {
                        emp.has_photo = true;
                        emp.photo_url = response.data.data.photo_url;
                    }
                    this.selectedEmployee.has_photo = true;
                    this.selectedEmployee.photo_url = response.data.data.photo_url;

                    this.capturedImage = null;
                    this.successMessage = `บันทึกใบหน้าของ ${response.data.data.name} เรียบร้อยแล้ว`;
                    this.showSuccess = true;
                    
                    setTimeout(() => {
                        this.showSuccess = false;
                    }, 4000);
                }
            } catch (error) {
                console.error('Error saving photo:', error);
                alert(error.response?.data?.message || 'เกิดข้อผิดพลาดในการบันทึก');
            } finally {
                this.isLoading = false;
            }
        }
    }
}
</script>

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
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
