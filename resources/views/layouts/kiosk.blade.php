<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="ระบบลงเวลาปฏิบัติราชการด้วยใบหน้า - Face Attendance Kiosk">
    <title>@yield('title', 'จุดลงเวลา') - {{ config('app.name', 'Face Attendance') }}</title>

    <!-- Google Fonts: Kanit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Kanit', sans-serif;
        }

        .kiosk-gradient {
            background: linear-gradient(135deg, #020617 0%, #0F172A 50%, #020617 100%);
        }
    </style>
</head>

<body class="kiosk-gradient min-h-screen text-white antialiased">
    @yield('content')
</body>

</html>