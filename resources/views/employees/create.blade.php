@extends('layouts.app')

@section('title', 'เพิ่มพนักงานใหม่')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="section-title">เพิ่มพนักงานใหม่</h2>
            <p class="section-subtitle">กรอกข้อมูลพนักงานเพื่อลงทะเบียนในระบบ</p>
        </div>
        <a href="{{ route('employees.index') }}" class="btn-secondary">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> ย้อนกลับ
        </a>
    </div>

    <!-- Form Card -->
    <div class="card p-8">
        <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            
            <!-- Section 1: ข้อมูลพื้นฐาน -->
            <div>
                <h3 class="text-base font-semibold text-text mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center">
                        <i data-lucide="user" class="w-4 h-4"></i>
                    </div>
                    ข้อมูลส่วนตัว
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- First Name -->
                    <div>
                        <label class="block text-sm font-medium text-text mb-1.5">ชื่อจริง <span class="text-rose-500">*</span></label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" placeholder="ระบุชื่อจริง" 
                               class="input-field" required>
                        @error('first_name') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label class="block text-sm font-medium text-text mb-1.5">นามสกุล <span class="text-rose-500">*</span></label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="ระบุนามสกุล" 
                               class="input-field" required>
                        @error('last_name') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="border-t border-primary-50"></div>

            <!-- Section 2: ข้อมูลการทำงาน -->
            <div>
                <h3 class="text-base font-semibold text-text mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center">
                        <i data-lucide="briefcase" class="w-4 h-4"></i>
                    </div>
                    ข้อมูลการทำงาน
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Employee Code -->
                    <div>
                        <label class="block text-sm font-medium text-text mb-1.5">รหัสพนักงาน <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            
                            <input type="text" name="employee_code" value="{{ old('employee_code') }}" placeholder="เช่น EMP001" 
                                   class="w-full pl-9 input-field font-mono" required>
                        </div>
                        @error('employee_code') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Department -->
                    <div>
                        <label class="block text-sm font-medium text-text mb-1.5">แผนก</label>
                        <input type="text" name="department" value="{{ old('department') }}" placeholder="เช่น IT, HR, Sales" 
                               class="input-field">
                    </div>
                    
                    <!-- Position -->
                    <div>
                        <label class="block text-sm font-medium text-text mb-1.5">ตำแหน่ง</label>
                        <input type="text" name="position" value="{{ old('position') }}" placeholder="เช่น Developer, Manager" 
                               class="input-field">
                    </div>
                </div>
            </div>

            <div class="border-t border-primary-50"></div>

            <!-- Section 3: รูปถ่าย -->
            <div x-data="{ fileName: null, filePreview: null }">
                <h3 class="text-base font-semibold text-text mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i data-lucide="camera" class="w-4 h-4"></i>
                    </div>
                    รูปถ่ายพนักงาน
                </h3>
                <div class="flex items-start gap-6">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-text mb-1.5">อัปโหลดรูปภาพ (ถ้ามี)</label>
                        
                        <!-- File Input Area -->
                        <label for="file-upload" 
                               class="mt-1 flex flex-col items-center justify-center px-6 pt-5 pb-6 border-2 border-primary-200 border-dashed rounded-xl hover:bg-surface-50 hover:border-primary-400 transition-colors duration-150 cursor-pointer relative group"
                               :class="{'bg-primary-50 border-primary-400': fileName}">
                            
                            <!-- Preview -->
                            <template x-if="filePreview">
                                <div class="mb-3 relative">
                                    <img :src="filePreview" class="h-32 w-32 object-cover rounded-lg shadow-sm border-2 border-white">
                                </div>
                            </template>

                            <!-- Default Icon -->
                            <template x-if="!filePreview">
                                
                            </template>

                            <div class="space-y-1 text-center">
                                <div class="flex text-sm text-text/80 justify-center">
                                    <span class="font-medium text-primary-600 hover:text-primary-700">
                                        <span x-text="fileName ? 'เปลี่ยนรูปภาพ' : 'คลิกเพื่อเลือกไฟล์'"></span>
                                    </span>
                                    <span class="pl-1" x-show="!fileName">หรือลากไฟล์มาวางที่นี่</span>
                                </div>
                                <p class="text-xs text-muted" x-text="fileName || 'PNG, JPG, GIF ไม่เกิน 2MB'"></p>
                            </div>

                            <input id="file-upload" name="photo" type="file" class="sr-only" accept="image/*"
                                   @change="fileName = $event.target.files[0].name; 
                                            const file = $event.target.files[0];
                                            if(file) {
                                                const reader = new FileReader();
                                                reader.onload = (e) => filePreview = e.target.result;
                                                reader.readAsDataURL(file);
                                            }">
                        </label>
                        @error('photo') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="pt-6 border-t border-primary-100/60 flex items-center justify-end gap-3">
                <a href="{{ route('employees.index') }}" class="btn-secondary">ยกเลิก</a>
                <button type="submit" class="btn-primary">
                    <i data-lucide="save" class="w-4 h-4"></i> บันทึกข้อมูล
                </button>
            </div>
        </form>
    </div>
</div>
@endsection



