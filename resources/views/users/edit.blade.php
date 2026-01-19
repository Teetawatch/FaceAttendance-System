@extends('layouts.app')

@section('title', 'แก้ไขข้อมูลผู้ใช้งาน')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('users.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-slate-800 hover:border-slate-300 transition-all shadow-sm">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h2 class="text-2xl font-bold text-slate-800">แก้ไขข้อมูลผู้ใช้งาน</h2>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
        <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="col-span-2">
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-2">ชื่อ-นามสกุล <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required 
                           class="w-full rounded-xl border-slate-200 focus:border-primary-500 focus:ring-primary-500 text-slate-800 placeholder-slate-400">
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email -->
                <div class="col-span-2">
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-2">อีเมล (สำหรับเข้าสู่ระบบ) <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required 
                           class="w-full rounded-xl border-slate-200 focus:border-primary-500 focus:ring-primary-500 text-slate-800 placeholder-slate-400">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Role -->
                <div class="col-span-2">
                    <label for="role" class="block text-sm font-medium text-slate-700 mb-2">บทบาท (Role) <span class="text-red-500">*</span></label>
                    <select name="role" id="role" required class="w-full rounded-xl border-slate-200 focus:border-primary-500 focus:ring-primary-500 text-slate-800">
                        <option value="">-- เลือกบทบาท --</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin (ผู้ดูแลระบบสูงสุด)</option>
                        <option value="hr" {{ old('role', $user->role) == 'hr' ? 'selected' : '' }}>HR (เจ้าหน้าที่ฝ่ายบุคคล)</option>
                        <option value="employee" {{ old('role', $user->role) == 'employee' ? 'selected' : '' }}>Employee (พนักงานทั่วไป)</option>
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>

                <div class="col-span-2 my-2">
                    <div class="h-px bg-slate-100"></div>
                </div>

                <!-- Password (Optional) -->
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-2">รหัสผ่านใหม่ (เว้นว่างหากไม่ต้องการเปลี่ยน)</label>
                    <input type="password" name="password" id="password"  
                           class="w-full rounded-xl border-slate-200 focus:border-primary-500 focus:ring-primary-500 text-slate-800">
                    <p class="text-xs text-slate-400 mt-1">กำหนดรหัสผ่านใหม่อย่างน้อย 8 ตัวอักษร</p>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">ยืนยันรหัสผ่านใหม่</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"  
                           class="w-full rounded-xl border-slate-200 focus:border-primary-500 focus:ring-primary-500 text-slate-800">
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route('users.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 transition-all">ยกเลิก</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary-600 text-white font-medium hover:bg-primary-700 shadow-sm hover:shadow-md transition-all">
                    บันทึกการแก้ไข
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
