@extends('layouts.app')

@section('title', 'เพิ่มนักเรียน')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('students.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface-50 hover:bg-primary-50 text-muted hover:text-primary-600 transition-colors duration-150 cursor-pointer border border-primary-100/60">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h2 class="section-title">เพิ่มนักเรียนใหม่</h2>
            <p class="section-subtitle">กรอกข้อมูลนักเรียน</p>
        </div>
    </div>

    <div class="card p-6">
        <form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-text mb-1.5">รหัสนักเรียน <span class="text-red-500">*</span></label>
                <input type="text" name="student_code" value="{{ old('student_code') }}" required
                       class="input-field font-mono @error('student_code') border-red-300 @enderror"
                       placeholder="เช่น STD001">
                @error('student_code')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text mb-1.5">ชื่อ <span class="text-red-500">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required
                           class="input-field @error('first_name') border-red-300 @enderror">
                    @error('first_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-text mb-1.5">นามสกุล <span class="text-red-500">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required
                           class="input-field @error('last_name') border-red-300 @enderror">
                    @error('last_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-text mb-1.5">หลักสูตร <span class="text-red-500">*</span></label>
                <select name="course_id" required class="input-field @error('course_id') border-red-300 @enderror">
                    <option value="">-- เลือกหลักสูตร --</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->name }} ({{ $course->start_date->format('d/m/Y') }} - {{ $course->end_date->format('d/m/Y') }})
                        </option>
                    @endforeach
                </select>
                @error('course_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-text mb-1.5">รูปถ่าย (สำหรับสแกนใบหน้า)</label>
                <div class="border-2 border-dashed border-primary-200 rounded-xl p-6 text-center hover:border-primary-400 transition-colors duration-150">
                    <input type="file" name="photo" accept="image/jpeg,image/png,image/jpg"
                           class="block w-full text-sm text-muted
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-full file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-primary-50 file:text-primary-700
                                  hover:file:bg-primary-100 cursor-pointer">
                    <p class="text-xs text-muted mt-2">รองรับไฟล์ .jpg, .jpeg, .png (ไม่เกิน 2MB)</p>
                </div>
                @error('photo')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked
                       class="w-5 h-5 rounded-lg border-primary-300 text-primary-600 focus:ring-primary-200 cursor-pointer">
                <label for="is_active" class="text-sm text-text cursor-pointer">เปิดใช้งาน</label>
            </div>

            <div class="flex gap-3 pt-4 border-t border-primary-100/60">
                <a href="{{ route('students.index') }}" class="btn-secondary flex-1 justify-center">ยกเลิก</a>
                <button type="submit" class="btn-primary flex-1 justify-center">
                    <i data-lucide="save" class="w-4 h-4"></i> บันทึกนักเรียน
                </button>
            </div>
        </form>
    </div>
</div>
@endsection




