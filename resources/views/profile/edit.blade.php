@extends('layouts.app')

@section('title', 'ข้อมูลส่วนตัว')

@section('content')
    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Header / Cover -->
        <div class="relative bg-gradient-to-r from-cyan-500 to-blue-600 rounded-3xl p-8 pb-16 shadow-xl shadow-blue-100 overflow-hidden">
            <div class="absolute inset-0 bg-grid-white/[0.2] [mask-image:linear-gradient(0deg,white,transparent)]"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-white tracking-tight text-shadow-sm font-mono">ข้อมูลส่วนตัว</h2>
                    <p class="text-blue-50 mt-2 font-medium">จัดการข้อมูลบัญชี รหัสผ่าน และลายเซ็นดิจิทัลของคุณ</p>
                </div>
                <!-- Avatar Circle -->
                <div class="hidden sm:flex h-20 w-20 rounded-full bg-card/20 backdrop-blur-md border-2 border-white/30 items-center justify-center text-3xl font-bold text-white shadow-2xl">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 -mt-10 relative z-20 px-4 sm:px-0">
            
            <!-- Left Column: Quick Profile & Navigation (Optional future expansion) or just Signature -->
            <div class="space-y-6">
                <!-- User Card -->
                <div class="bg-card rounded-2xl shadow-sm border border-slate-200/60 p-6 text-center">
                    <div class="w-24 h-24 bg-slate-100 rounded-full mx-auto flex items-center justify-center text-4xl font-bold text-text/80 mb-4 border-4 border-white shadow-sm">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <h3 class="text-xl font-bold text-text font-bold font-mono font-mono">{{ Auth::user()->name }}</h3>
                    <p class="text-sm text-indigo-600/70 uppercase tracking-wider font-semibold mt-1">{{ Auth::user()->role }}</p>
                    <p class="text-primary-400 text-sm mt-1">{{ Auth::user()->email }}</p>
                </div>

                <!-- Signature Section (Moved to side for better visibility) -->
                <div class="bg-card rounded-2xl shadow-sm border border-slate-200/60 p-6">
                    <div class="flex items-center gap-3 mb-4 text-text font-bold font-mono font-semibold border-b border-slate-50 pb-3">
                        <div class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center text-rose-600">
                            <x-heroicon-o-pencil-square class="w-5"/>
                        </div>
                        ลายเซ็นดิจิทัล
                    </div>
                    @include('profile.partials.update-signature-form')
                </div>
            </div>

            <!-- Right Column: Edit Forms -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Profile Information -->
                <div class="bg-card rounded-2xl shadow-sm border border-slate-200/60 p-8">
                    <div class="flex items-center gap-3 mb-6 text-text font-bold font-mono font-semibold border-b border-slate-50 pb-4">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                            <x-heroicon-o-identification class="w-5"/>
                        </div>
                        แก้ไขข้อมูลพื้นฐาน
                    </div>
                    @include('profile.partials.update-profile-information-form')
                </div>

                <!-- Password -->
                <div class="bg-card rounded-2xl shadow-sm border border-slate-200/60 p-8">
                    <div class="flex items-center gap-3 mb-6 text-text font-bold font-mono font-semibold border-b border-slate-50 pb-4">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                            <x-heroicon-o-key class="w-5"/>
                        </div>
                        เปลี่ยนรหัสผ่าน
                    </div>
                    @include('profile.partials.update-password-form')
                </div>

                <!-- Delete Account -->
                <div class="bg-card rounded-2xl shadow-sm border border-red-100 p-8 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                        <x-heroicon-o-exclamation-triangle class="text-8xl text-red-600 transform rotate-12 w-5"/>
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-6 text-red-700 font-semibold border-b border-red-50 pb-4">
                            <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center text-red-600">
                                <x-heroicon-o-x-circle class="w-5"/>
                            </div>
                            ลบบัญชีผู้ใช้
                        </div>
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
