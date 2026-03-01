@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-background">
    <div class="flex w-full max-w-5xl bg-white rounded-2xl overflow-hidden m-4 border border-primary-100/60 shadow-lg">
        
        <!-- Left Side: Branding -->
        <div class="hidden lg:flex w-1/2 bg-primary-600 relative items-center justify-center p-12">
            <!-- Subtle Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-10 left-10 w-32 h-32 border-2 border-white rounded-3xl rotate-12"></div>
                <div class="absolute bottom-20 right-10 w-24 h-24 border-2 border-white rounded-full"></div>
                <div class="absolute top-1/2 left-1/3 w-16 h-16 border-2 border-white rounded-2xl -rotate-6"></div>
            </div>

            <div class="relative z-10 text-center">
                <div class="w-20 h-20 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center mx-auto mb-8 border border-white/20">
                    <i data-lucide="scan-face" class="w-10 h-10 text-white"></i>
                </div>
                <h2 class="text-3xl font-bold text-white mb-4 tracking-tight">ระบบบันทึกเวลา<br>ด้วยใบหน้า</h2>
                <p class="text-primary-200 text-base leading-relaxed">
                    สะดวก รวดเร็ว และแม่นยำ<br>
                    ยกระดับการจัดการองค์กรของคุณ
                </p>
                <div class="mt-8 flex items-center justify-center gap-6 text-primary-200 text-sm">
                    <div class="flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        <span>ปลอดภัย</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="zap" class="w-4 h-4"></i>
                        <span>รวดเร็ว</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                        <span>แม่นยำ</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full lg:w-1/2 p-8 md:p-12 lg:p-14 bg-white flex flex-col justify-center">
            
            <!-- Mobile Header -->
            <div class="lg:hidden text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-primary-600 mb-4">
                    <i data-lucide="scan-face" class="w-7 h-7 text-white"></i>
                </div>
                <h2 class="text-xl font-bold text-text">Face Attendance</h2>
            </div>

            <div class="mb-8">
                <h3 class="text-2xl font-bold text-text mb-1.5 tracking-tight">ยินดีต้อนรับกลับ</h3>
                <p class="text-muted text-sm">กรุณาเข้าสู่ระบบเพื่อเริ่มต้นใช้งาน</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center gap-3 text-emerald-700 text-sm font-medium">
                    <i data-lucide="check-circle" class="w-4 h-4 flex-shrink-0"></i>
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email Address -->
                <div class="space-y-1.5">
                    <label for="email" class="text-sm font-medium text-text">อีเมล</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-muted">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                            class="pl-10 block w-full rounded-xl border-primary-200 bg-surface-50 focus:bg-white focus:border-primary-400 focus:ring-2 focus:ring-primary-100 py-3 transition-all duration-150 text-sm placeholder:text-muted/50" 
                            placeholder="name@company.com">
                    </div>
                    @error('email')
                        <p class="text-red-600 text-xs flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="space-y-1.5">
                    <label for="password" class="text-sm font-medium text-text">รหัสผ่าน</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-muted">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="pl-10 block w-full rounded-xl border-primary-200 bg-surface-50 focus:bg-white focus:border-primary-400 focus:ring-2 focus:ring-primary-100 py-3 transition-all duration-150 text-sm placeholder:text-muted/50"
                            placeholder="••••••••">
                    </div>
                    @error('password')
                        <p class="text-red-600 text-xs flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between pt-1">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer">
                        <input id="remember_me" type="checkbox" class="rounded border-primary-300 text-primary-600 focus:ring-primary-500 cursor-pointer" name="remember">
                        <span class="ml-2 text-sm text-muted hover:text-text transition-colors duration-150">จดจำฉันไว้</span>
                    </label>
                    
                    @if (Route::has('password.request'))
                        <a class="text-sm text-primary-600 hover:text-primary-700 font-medium transition-colors duration-150" href="{{ route('password.request') }}">
                            ลืมรหัสผ่าน?
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3 px-4 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-primary-500 transition-colors duration-150 flex items-center justify-center gap-2 cursor-pointer">
                    <span>เข้าสู่ระบบ</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>

            <!-- Footer -->
            <div class="mt-8 text-center">
                <p class="text-xs text-muted">
                    &copy; {{ date('Y') }} Face Attendance System
                </p>
            </div>
        </div>
    </div>
</div>
@endsection



