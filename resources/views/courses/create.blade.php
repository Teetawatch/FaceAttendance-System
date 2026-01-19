@extends('layouts.app')

@section('title', 'เพิ่มหลักสูตร')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('courses.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800">🎓 เพิ่มหลักสูตรใหม่</h2>
            <p class="text-slate-500 text-sm">กรอกข้อมูลหลักสูตร</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <form action="{{ route('courses.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Name -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">ชื่อหลักสูตร <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all @error('name') border-rose-300 @enderror"
                       placeholder="เช่น หลักสูตรพนักงานใหม่ รุ่นที่ 1">
                @error('name')
                    <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">รายละเอียด</label>
                <textarea name="description" rows="3"
                          class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                          placeholder="รายละเอียดเพิ่มเติม (ถ้ามี)">{{ old('description') }}</textarea>
            </div>

            <!-- Dates -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">วันเริ่มหลักสูตร <span class="text-rose-500">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all @error('start_date') border-rose-300 @enderror">
                    @error('start_date')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">วันสิ้นสุดหลักสูตร <span class="text-rose-500">*</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" required
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all @error('end_date') border-rose-300 @enderror">
                    @error('end_date')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Active Status -->
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked
                       class="w-5 h-5 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                <label for="is_active" class="text-sm text-slate-700">เปิดใช้งานหลักสูตร</label>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('courses.index') }}" 
                   class="flex-1 px-4 py-3 text-center border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 transition-colors font-medium">
                    ยกเลิก
                </a>
                <button type="submit" 
                        class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors font-medium flex items-center justify-center gap-2">
                    <i class="fa-solid fa-save"></i>
                    บันทึกหลักสูตร
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
