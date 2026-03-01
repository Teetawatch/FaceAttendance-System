@extends('layouts.app')

@section('title', 'แก้ไขข้อมูลผู้ใช้งาน')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('users.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface-50 hover:bg-primary-50 text-muted hover:text-primary-600 transition-colors duration-150 cursor-pointer border border-primary-100/60">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <h2 class="section-title">แก้ไขข้อมูลผู้ใช้งาน</h2>
    </div>

    <div class="card p-8">
        <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label for="name" class="block text-sm font-medium text-text mb-1.5">ชื่อ-นามสกุล <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="input-field">
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="col-span-2">
                    <label for="email" class="block text-sm font-medium text-text mb-1.5">อีเมล (สำหรับเข้าสู่ระบบ) <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="input-field">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="col-span-2">
                    <label for="role" class="block text-sm font-medium text-text mb-1.5">บทบาท (Role) <span class="text-red-500">*</span></label>
                    <select name="role" id="role" required class="input-field">
                        <option value="">-- เลือกบทบาท --</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin (ผู้ดูแลระบบสูงสุด)</option>
                        <option value="hr" {{ old('role', $user->role) == 'hr' ? 'selected' : '' }}>HR (เจ้าหน้าที่ฝ่ายบุคคล)</option>
                        <option value="employee" {{ old('role', $user->role) == 'employee' ? 'selected' : '' }}>Employee (พนักงานทั่วไป)</option>
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>

                <div class="col-span-2 my-2">
                    <div class="h-px bg-primary-50"></div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-text mb-1.5">รหัสผ่านใหม่ (เว้นว่างหากไม่ต้องการเปลี่ยน)</label>
                    <input type="password" name="password" id="password" class="input-field">
                    <p class="text-xs text-muted mt-1">กำหนดรหัสผ่านใหม่อย่างน้อย 8 ตัวอักษร</p>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-text mb-1.5">ยืนยันรหัสผ่านใหม่</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="input-field">
                </div>
            </div>

            <div class="pt-6 border-t border-primary-100/60 flex justify-end gap-3">
                <a href="{{ route('users.index') }}" class="btn-secondary">ยกเลิก</a>
                <button type="submit" class="btn-primary">
                    <i data-lucide="save" class="w-4 h-4"></i> บันทึกการแก้ไข
                </button>
            </div>
        </form>
    </div>
</div>
@endsection




