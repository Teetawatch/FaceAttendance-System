@extends('layouts.app')

@section('title', 'เพิ่มผู้ใช้งานใหม่')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('users.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface-50 hover:bg-primary-50 text-muted hover:text-primary-600 transition-colors duration-150 cursor-pointer border border-primary-100/60">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <h2 class="section-title">เพิ่มผู้ใช้งานใหม่</h2>
    </div>

    <div class="card p-8">
        <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label for="name" class="block text-sm font-medium text-text mb-1.5">ชื่อ-นามสกุล <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required class="input-field">
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="col-span-2">
                    <label for="email" class="block text-sm font-medium text-text mb-1.5">อีเมล (สำหรับเข้าสู่ระบบ) <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required class="input-field">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="col-span-2">
                    <label for="role" class="block text-sm font-medium text-text mb-1.5">บทบาท (Role) <span class="text-red-500">*</span></label>
                    <select name="role" id="role" required class="input-field">
                        <option value="">-- เลือกบทบาท --</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin (ผู้ดูแลระบบสูงสุด)</option>
                        <option value="hr" {{ old('role') == 'hr' ? 'selected' : '' }}>HR (เจ้าหน้าที่ฝ่ายบุคคล)</option>
                        <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>Employee (พนักงานทั่วไป)</option>
                    </select>
                    <p class="text-xs text-muted mt-1.5">
                        * <span class="font-semibold text-text">Admin/HR</span> สามารถจัดการข้อมูลและเซ็นชื่อได้
                    </p>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-text mb-1.5">รหัสผ่าน <span class="text-red-500">*</span></label>
                    <input type="password" name="password" id="password" required class="input-field">
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-text mb-1.5">ยืนยันรหัสผ่าน <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required class="input-field">
                </div>
            </div>

            <div class="pt-6 border-t border-primary-100/60 flex justify-end gap-3">
                <a href="{{ route('users.index') }}" class="btn-secondary">ยกเลิก</a>
                <button type="submit" class="btn-primary">
                    <i data-lucide="save" class="w-4 h-4"></i> บันทึกข้อมูล
                </button>
            </div>
        </form>
    </div>
</div>
@endsection




