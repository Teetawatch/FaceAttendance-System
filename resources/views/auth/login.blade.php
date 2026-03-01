@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-background">
    <div class="flex w-full max-w-5xl bg-card rounded-3xl shadow-2xl overflow-hidden m-4">
        
        <!-- Left Side: Branding & Abstract Art -->
        <div class="hidden lg:flex w-1/2 bg-gradient-to-br from-blue-50 to-indigo-50 relative items-center justify-center p-12">
            <!-- Decorative Elements -->
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden">
                <div class="absolute top-[-10%] left-[-10%] w-64 h-64 bg-blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
                <div class="absolute top-[-10%] right-[-10%] w-64 h-64 bg-indigo-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
                <div class="absolute bottom-[-20%] left-[20%] w-80 h-80 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>
            </div>

            <div class="relative z-10 text-center">
                <div class="w-28 h-28 bg-card rounded-2xl flex items-center justify-center mx-auto mb-8 shadow-xl shadow-blue-100 transform rotate-3 hover:rotate-6 transition-all duration-500">
                    <i class="fa-solid fa-face-smile-wink text-6xl text-indigo-600"></i>
                </div>
                <h2 class="text-3xl font-bold text-text font-bold font-mono mb-4 tracking-tight font-mono">ระบบบันทึกเวลาด้วยใบหน้า</h2>
                <p class="text-primary-600/70 text-lg leading-relaxed font-light">
                    สะดวก รวดเร็ว และแม่นยำ<br>
                    ยกระดับการจัดการองค์กรของคุณ
                </p>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full lg:w-1/2 p-8 md:p-12 lg:p-16 bg-card flex flex-col justify-center">
            
            <!-- Mobile Header -->
            <div class="lg:hidden text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-xl bg-indigo-50 text-indigo-600 mb-4">
                    <i class="fa-solid fa-face-smile-wink text-3xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-text font-bold font-mono font-mono">Face Attendance</h2>
            </div>

            <div class="mb-10">
                <h3 class="text-2xl font-bold text-text font-bold font-mono mb-2 font-mono">ยินดีต้อนรับกลับ! 👋</h3>
                <p class="text-primary-600/70 font-light">กรุณาเข้าสู่ระบบเพื่อเริ่มต้นใช้งาน</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-100 flex items-center gap-3 text-green-700 text-sm font-medium">
                    <i class="fa-solid fa-circle-check"></i>
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email Address -->
                <div class="space-y-2">
                    <label for="email" class="text-sm font-medium text-text">อีเมล</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-envelope text-primary-400 group-focus-within:text-indigo-500 transition-colors"></i>
                        </div>
                        <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                            class="pl-11 block w-full rounded-xl border-primary-100 bg-background focus:bg-card focus:border-indigo-500 focus:ring-indigo-500 py-3 transition-all text-sm placeholder:text-primary-400" 
                            placeholder="name@company.com">
                    </div>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label for="password" class="text-sm font-medium text-text">รหัสผ่าน</label>
                    </div>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-primary-400 group-focus-within:text-indigo-500 transition-colors"></i>
                        </div>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="pl-11 block w-full rounded-xl border-primary-100 bg-background focus:bg-card focus:border-indigo-500 focus:ring-indigo-500 py-3 transition-all text-sm placeholder:text-primary-400"
                            placeholder="••••••••">
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between pt-2">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer">
                        <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500 cursor-pointer" name="remember">
                        <span class="ml-2 text-sm text-text/80 hover:text-text font-bold font-mono transition-colors">จดจำฉันไว้</span>
                    </label>
                    
                    @if (Route::has('password.request'))
                        <a class="text-sm text-indigo-600 hover:text-indigo-700 font-medium transition-colors" href="{{ route('password.request') }}">
                            ลืมรหัสผ่าน?
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3.5 px-4 bg-primary-600 hover:bg-primary-700 text-white shadow-md shadow-primary-500/20 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2">
                    <span>เข้าสู่ระบบ</span>
                    <i class="fa-solid fa-arrow-right-long"></i>
                </button>
            </form>

            <!-- Footer -->
            <div class="mt-8 text-center">
                <p class="text-xs text-primary-400 font-light">
                    &copy; {{ date('Y') }} Face Attendance System.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes blob {
        0% { transform: translate(0px, 0px) scale(1); }
        33% { transform: translate(30px, -50px) scale(1.1); }
        66% { transform: translate(-20px, 20px) scale(0.9); }
        100% { transform: translate(0px, 0px) scale(1); }
    }
    .animate-blob {
        animation: blob 7s infinite;
    }
    .animation-delay-2000 {
        animation-delay: 2s;
    }
    .animation-delay-4000 {
        animation-delay: 4s;
    }
</style>
@endsection