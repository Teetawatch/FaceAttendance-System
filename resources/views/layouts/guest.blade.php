<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Face Attendance') }}</title>

    <!-- Google Fonts: Poppins + Noto Sans Thai + JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Noto+Sans+Thai:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    @if (isset($slot))
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-background">
            <div>
                <a href="/" class="flex items-center gap-3 cursor-pointer">
                    <div class="w-12 h-12 bg-primary-600 rounded-2xl flex items-center justify-center">
                        <i data-lucide="scan-face" class="w-7 h-7 text-white"></i>
                    </div>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-white overflow-hidden sm:rounded-2xl border border-primary-100/60 shadow-card">
                {{ $slot }}
            </div>
        </div>
    @else
        @yield('content')
    @endif

    <script>lucide.createIcons();</script>
</body>

</html>



