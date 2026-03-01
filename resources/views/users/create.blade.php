@extends('layouts.app')

@section('title', 'เพิ่มผู้ใช้งานใหม่')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('users.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-card border border-slate-200 text-indigo-600/70 hover:text-text font-bold font-mono hover:border-slate-300 transition-all shadow-sm">
            
        </a>
        <h2 class="text-2xl font-bold text-text font-bold font-mono font-mono">เพิ่มผู้ใช้งานใหม่</h2>
    </div>

    <!-- Form Card -->
    <div class="bg-card rounded-2xl shadow-sm border border-slate-200/60 p-8">
        <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="col-span-2">
                    <label for="name" class="block text-sm font-medium text-text mb-2">ชื่อ-นามสกุล <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required 
                           class="w-full rounded-xl border-slate-200 focus:border-slate-200/600 focus:ring-primary-500 text-text font-bold font-mono placeholder-slate-400">
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email -->
                <div class="col-span-2">
                    <label for="email" class="block text-sm font-medium text-text mb-2">อีเมล (สำหรับเข้าสู่ระบบ) <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required 
                           class="w-full rounded-xl border-slate-200 focus:border-slate-200/600 focus:ring-primary-500 text-text font-bold font-mono placeholder-slate-400">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Role -->
                <div class="col-span-2">
                    <label for="role" class="block text-sm font-medium text-text mb-2">บทบาท (Role) <span class="text-red-500">*</span></label>
                    <select name="role" id="role" required class="w-full rounded-xl border-slate-200 focus:border-slate-200/600 focus:ring-primary-500 text-text font-bold font-mono">
                        <option value="">-- เลือกบทบาท --</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin (ผู้ดูแลระบบสูงสุด)</option>
                        <option value="hr" {{ old('role') == 'hr' ? 'selected' : '' }}>HR (เจ้าหน้าที่ฝ่ายบุคคล)</option>
                        <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>Employee (พนักงานทั่วไป)</option>
                    </select>
                    <p class="text-xs text-indigo-600/70 mt-2">
                        * <span class="font-semibold">Admin/HR</span> สามารถจัดการข้อมูลและเซ็นชื่อได้
                    </p>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-text mb-2">รหัสผ่าน <span class="text-red-500">*</span></label>
                    <input type="password" name="password" id="password" required 
                           class="w-full rounded-xl border-slate-200 focus:border-slate-200/600 focus:ring-primary-500 text-text font-bold font-mono">
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-text mb-2">ยืนยันรหัสผ่าน <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required 
                           class="w-full rounded-xl border-slate-200 focus:border-slate-200/600 focus:ring-primary-500 text-text font-bold font-mono">
                </div>
            </div>

            <div class="pt-6 border-t border-slate-200/60 flex justify-end gap-3">
                <a href="{{ route('users.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-text/80 font-medium hover:bg-slate-50 transition-all">ยกเลิก</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary-600 text-white font-medium hover:bg-primary-700 shadow-sm hover:shadow-md transition-all">
                    บันทึกข้อมูล
                </button>
            </div>
        </form>
    </div>
</div>
@endsection




