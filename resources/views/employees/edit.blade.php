@extends('layouts.app')

@section('title', 'แก้ไขข้อมูลพนักงาน')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-text font-bold font-mono font-mono">แก้ไขข้อมูลพนักงาน</h2>
            <p class="text-primary-600/70 text-sm mt-1">ปรับปรุงข้อมูลส่วนตัวหรือสถานะการทำงาน</p>
        </div>
        <a href="{{ route('employees.index') }}" class="inline-flex items-center gap-2 text-primary-600/70 hover:text-text bg-card hover:bg-background px-4 py-2 rounded-xl border border-primary-100 transition-all text-sm font-medium shadow-sm">
            <i class="fa-solid fa-arrow-left"></i> ย้อนกลับ
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-card rounded-2xl shadow-sm border border-primary-50 p-8">
        <form action="{{ route('employees.update', $employee) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')
            
            <!-- Section 1: ข้อมูลพื้นฐาน -->
            <div>
                <h3 class="text-lg font-bold text-text mb-4 flex items-center gap-2 font-mono">
                    <div class="w-8 h-8 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    ข้อมูลส่วนตัว
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- First Name -->
                    <div>
                        <label class="block text-sm font-medium text-text mb-1.5">ชื่อจริง <span class="text-rose-500">*</span></label>
                        <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name) }}" placeholder="ระบุชื่อจริง" 
                               class="w-full rounded-xl border-primary-100 focus:border-primary-500 focus:ring-primary-500 text-sm shadow-sm placeholder-slate-400" required>
                        @error('first_name') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label class="block text-sm font-medium text-text mb-1.5">นามสกุล <span class="text-rose-500">*</span></label>
                        <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name) }}" placeholder="ระบุนามสกุล" 
                               class="w-full rounded-xl border-primary-100 focus:border-primary-500 focus:ring-primary-500 text-sm shadow-sm placeholder-slate-400" required>
                        @error('last_name') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-50"></div>

            <!-- Section 2: ข้อมูลการทำงาน -->
            <div>
                <h3 class="text-lg font-bold text-text mb-4 flex items-center gap-2 font-mono">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                    ข้อมูลการทำงาน
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Employee Code -->
                    <div>
                        <label class="block text-sm font-medium text-text mb-1.5">รหัสพนักงาน <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <i class="fa-solid fa-id-card absolute left-3 top-1/2 -translate-y-1/2 text-primary-400 text-sm"></i>
                            <input type="text" name="employee_code" value="{{ old('employee_code', $employee->employee_code) }}" placeholder="เช่น EMP001" 
                                   class="w-full pl-9 rounded-xl border-primary-100 focus:border-primary-500 focus:ring-primary-500 text-sm shadow-sm placeholder-slate-400 font-mono" required>
                        </div>
                        @error('employee_code') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Department -->
                    <div>
                        <label class="block text-sm font-medium text-text mb-1.5">แผนก</label>
                        <input type="text" name="department" value="{{ old('department', $employee->department) }}" placeholder="เช่น IT, HR, Sales" 
                               class="w-full rounded-xl border-primary-100 focus:border-primary-500 focus:ring-primary-500 text-sm shadow-sm placeholder-slate-400">
                    </div>
                    
                    <!-- Position -->
                    <div>
                        <label class="block text-sm font-medium text-text mb-1.5">ตำแหน่ง</label>
                        <input type="text" name="position" value="{{ old('position', $employee->position) }}" placeholder="เช่น Developer, Manager" 
                               class="w-full rounded-xl border-primary-100 focus:border-primary-500 focus:ring-primary-500 text-sm shadow-sm placeholder-slate-400">
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-text mb-1.5">สถานะ</label>
                        <select name="is_active" class="w-full rounded-xl border-primary-100 focus:border-primary-500 focus:ring-primary-500 text-sm shadow-sm">
                            <option value="1" {{ old('is_active', $employee->is_active) ? 'selected' : '' }}>ปกติ (Active)</option>
                            <option value="0" {{ !old('is_active', $employee->is_active) ? 'selected' : '' }}>ระงับ (Inactive)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-50"></div>

            <!-- Section 3: รูปถ่าย -->
            <div x-data="{ fileName: null, filePreview: null }">
                <h3 class="text-lg font-bold text-text mb-4 flex items-center gap-2 font-mono">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-camera"></i>
                    </div>
                    รูปถ่ายพนักงาน
                </h3>
                <div class="flex flex-col md:flex-row gap-8">
                    <!-- Current Photo -->
                    @if($employee->photo_path)
                    <div class="flex-shrink-0 text-center">
                        <p class="text-xs font-medium text-primary-600/70 mb-2">รูปปัจจุบัน</p>
                        <div class="w-32 h-32 rounded-2xl overflow-hidden border-4 border-slate-50 shadow-sm mx-auto">
                            <img src="{{ asset('storage/' . $employee->photo_path) }}" class="w-full h-full object-cover">
                        </div>
                    </div>
                    @endif

                    <div class="flex-1">
                        <label class="block text-sm font-medium text-text mb-1.5">อัปโหลดรูปใหม่ (ถ้าต้องการเปลี่ยน)</label>
                        
                        <!-- File Input Area -->
                        <label for="file-upload" 
                               class="mt-1 flex flex-col items-center justify-center px-6 pt-5 pb-6 border-2 border-primary-100 border-dashed rounded-xl hover:bg-background hover:border-primary-300 transition-all cursor-pointer relative group"
                               :class="{'bg-primary-50 border-primary-300': fileName}">
                            
                            <!-- Preview -->
                            <template x-if="filePreview">
                                <div class="mb-3 relative">
                                    <img :src="filePreview" class="h-32 w-32 object-cover rounded-lg shadow-sm border-2 border-white">
                                </div>
                            </template>

                            <!-- Default Icon -->
                            <template x-if="!filePreview">
                                <i class="fa-solid fa-cloud-arrow-up text-slate-300 text-4xl mb-3 group-hover:text-primary-500 transition-colors"></i>
                            </template>

                            <div class="space-y-1 text-center">
                                <div class="flex text-sm text-text/80 justify-center">
                                    <span class="font-medium text-primary-600 hover:text-primary-500">
                                        <span x-text="fileName ? 'เปลี่ยนรูปภาพ' : 'คลิกเพื่อเลือกไฟล์'"></span>
                                    </span>
                                    <span class="pl-1" x-show="!fileName">หรือลากไฟล์มาวางที่นี่</span>
                                </div>
                                <p class="text-xs text-primary-600/70" x-text="fileName || 'PNG, JPG, GIF ไม่เกิน 2MB'"></p>
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
                        @error('photo') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="pt-6 border-t border-primary-50 flex items-center justify-end gap-3">
                <a href="{{ route('employees.index') }}" class="px-5 py-2.5 rounded-xl border border-primary-100 text-text/80 hover:bg-background font-medium transition-colors text-sm">ยกเลิก</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary-600 text-white hover:bg-primary-700 font-medium transition-all shadow-lg shadow-primary-600/20 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> บันทึกการแก้ไข
                </button>
            </div>
        </form>
    </div>
</div>
@endsection