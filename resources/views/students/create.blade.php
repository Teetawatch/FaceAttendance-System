@extends('layouts.app')

@section('title', 'เพิ่มนักเรียน')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('students.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100 hover:bg-slate-200 text-text/80 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-text font-bold font-mono font-mono">เพิ่มนักเรียนใหม่</h2>
            <p class="text-primary-600/70 text-sm">กรอกข้อมูลนักเรียน</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-card rounded-2xl shadow-sm border border-primary-50 p-6">
        <form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Student Code -->
            <div>
                <label class="block text-sm font-medium text-text mb-2">รหัสนักเรียน <span class="text-rose-500">*</span></label>
                <input type="text" name="student_code" value="{{ old('student_code') }}" required
                       class="w-full px-4 py-3 rounded-xl border border-primary-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all @error('student_code') border-rose-300 @enderror"
                       placeholder="เช่น STD001">
                @error('student_code')
                    <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Name -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text mb-2">ชื่อ <span class="text-rose-500">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required
                           class="w-full px-4 py-3 rounded-xl border border-primary-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all @error('first_name') border-rose-300 @enderror">
                    @error('first_name')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-text mb-2">นามสกุล <span class="text-rose-500">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required
                           class="w-full px-4 py-3 rounded-xl border border-primary-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all @error('last_name') border-rose-300 @enderror">
                    @error('last_name')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Course -->
            <div>
                <label class="block text-sm font-medium text-text mb-2">หลักสูตร <span class="text-rose-500">*</span></label>
                <select name="course_id" required class="w-full px-4 py-3 rounded-xl border border-primary-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all @error('course_id') border-rose-300 @enderror">
                    <option value="">-- เลือกหลักสูตร --</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->name }} ({{ $course->start_date->format('d/m/Y') }} - {{ $course->end_date->format('d/m/Y') }})
                        </option>
                    @endforeach
                </select>
                @error('course_id')
                    <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Photo -->
            <div>
                <label class="block text-sm font-medium text-text mb-2">รูปถ่าย (สำหรับสแกนใบหน้า)</label>
                <div class="border-2 border-dashed border-primary-100 rounded-xl p-6 text-center hover:border-primary-400 transition-colors">
                    <input type="file" name="photo" accept="image/jpeg,image/png,image/jpg"
                           class="block w-full text-sm text-primary-600/70
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-full file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-blue-50 file:text-blue-700
                                  hover:file:bg-blue-100 cursor-pointer">
                    <p class="text-xs text-primary-400 mt-2">รองรับไฟล์ .jpg, .jpeg, .png (ไม่เกิน 2MB)</p>
                </div>
                @error('photo')
                    <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Active Status -->
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked
                       class="w-5 h-5 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                <label for="is_active" class="text-sm text-text">เปิดใช้งาน</label>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 pt-4 border-t border-primary-50">
                <a href="{{ route('students.index') }}" 
                   class="flex-1 px-4 py-3 text-center border border-primary-100 text-text/80 rounded-xl hover:bg-background transition-colors font-medium">
                    ยกเลิก
                </a>
                <button type="submit" 
                        class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors font-medium flex items-center justify-center gap-2">
                    <i class="fa-solid fa-save"></i>
                    บันทึกนักเรียน
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
