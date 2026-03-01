@extends('layouts.app')

@section('title', 'ข้อมูลส่วนตัว')

@section('content')
    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Header / Cover -->
        <div class="relative bg-primary-600 rounded-2xl p-8 pb-16 overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <svg class="w-full h-full" viewBox="0 0 400 200" fill="none"><circle cx="350" cy="50" r="120" fill="white"/><circle cx="50" cy="180" r="80" fill="white"/></svg>
            </div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-white tracking-tight">ข้อมูลส่วนตัว</h2>
                    <p class="text-primary-100 mt-1.5 text-sm">จัดการข้อมูลบัญชี รหัสผ่าน และลายเซ็นดิจิทัลของคุณ</p>
                </div>
                <div class="hidden sm:flex h-16 w-16 rounded-2xl bg-white/20 backdrop-blur-sm border border-white/30 items-center justify-center text-2xl font-bold text-white">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 -mt-10 relative z-20 px-4 sm:px-0">
            
            <div class="space-y-6">
                <!-- User Card -->
                <div class="card p-6 text-center">
                    <div class="w-20 h-20 bg-primary-100 rounded-2xl mx-auto flex items-center justify-center text-3xl font-bold text-primary-700 mb-4">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <h3 class="text-lg font-semibold text-text">{{ Auth::user()->name }}</h3>
                    <p class="text-xs text-primary-600 uppercase tracking-wider font-semibold mt-1">{{ Auth::user()->role }}</p>
                    <p class="text-muted text-sm mt-1">{{ Auth::user()->email }}</p>
                </div>

                <!-- Signature Section -->
                <div class="card p-6">
                    <div class="flex items-center gap-3 mb-4 text-text font-semibold border-b border-primary-50 pb-3">
                        <div class="w-8 h-8 rounded-lg bg-accent-50 flex items-center justify-center text-accent-600">
                            <i data-lucide="pen-tool" class="w-4 h-4"></i>
                        </div>
                        ลายเซ็นดิจิทัล
                    </div>
                    @include('profile.partials.update-signature-form')
                </div>
            </div>

            <!-- Right Column: Edit Forms -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Profile Information -->
                <div class="card p-6">
                    <div class="flex items-center gap-3 mb-5 text-text font-semibold border-b border-primary-50 pb-4">
                        <div class="w-8 h-8 rounded-lg bg-primary-50 flex items-center justify-center text-primary-600">
                            <i data-lucide="user-cog" class="w-4 h-4"></i>
                        </div>
                        แก้ไขข้อมูลพื้นฐาน
                    </div>
                    @include('profile.partials.update-profile-information-form')
                </div>

                <!-- Password -->
                <div class="card p-6">
                    <div class="flex items-center gap-3 mb-5 text-text font-semibold border-b border-primary-50 pb-4">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                        เปลี่ยนรหัสผ่าน
                    </div>
                    @include('profile.partials.update-password-form')
                </div>

                <!-- Delete Account -->
                <div class="card p-6 border-red-100">
                    <div class="flex items-center gap-3 mb-5 text-red-700 font-semibold border-b border-red-50 pb-4">
                        <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center text-red-600">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </div>
                        ลบบัญชีผู้ใช้
                    </div>
                    @include('profile.partials.delete-user-form')
                </div>

            </div>
        </div>
    </div>
@endsection




