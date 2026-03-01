@extends('layouts.app')

@section('title', 'เพิ่มหลักสูตร')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('courses.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface-50 hover:bg-primary-50 text-muted hover:text-primary-600 transition-colors duration-150 cursor-pointer border border-primary-100/60">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h2 class="section-title">เพิ่มหลักสูตรใหม่</h2>
            <p class="section-subtitle">กรอกข้อมูลหลักสูตร</p>
        </div>
    </div>

    <div class="card p-6">
        <form action="{{ route('courses.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-text mb-1.5">ชื่อหลักสูตร <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="input-field @error('name') border-red-300 @enderror"
                       placeholder="เช่น หลักสูตรพนักงานใหม่ รุ่นที่ 1">
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-text mb-1.5">รายละเอียด</label>
                <textarea name="description" rows="3" class="input-field"
                          placeholder="รายละเอียดเพิ่มเติม (ถ้ามี)">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text mb-1.5">วันเริ่มหลักสูตร <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required
                           class="input-field @error('start_date') border-red-300 @enderror">
                    @error('start_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-text mb-1.5">วันสิ้นสุดหลักสูตร <span class="text-red-500">*</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" required
                           class="input-field @error('end_date') border-red-300 @enderror">
                    @error('end_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked
                       class="w-5 h-5 rounded-lg border-primary-300 text-primary-600 focus:ring-primary-200 cursor-pointer">
                <label for="is_active" class="text-sm text-text cursor-pointer">เปิดใช้งานหลักสูตร</label>
            </div>

            <div class="flex gap-3 pt-4 border-t border-primary-100/60">
                <a href="{{ route('courses.index') }}" class="btn-secondary flex-1 justify-center">ยกเลิก</a>
                <button type="submit" class="btn-primary flex-1 justify-center">
                    <i data-lucide="save" class="w-4 h-4"></i> บันทึกหลักสูตร
                </button>
            </div>
        </form>
    </div>
</div>
@endsection




