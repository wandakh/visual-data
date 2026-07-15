<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Visual Data | {{ $title ?? '' }}</title>
    <link rel="icon" href="{{ asset('images/kisel.png') }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-[#0f1729] px-4 font-sans">
    <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl">
        <div class="bg-gradient-to-br from-[#0f1729] via-[#1a2540] to-indigo-900 px-8 pb-8 pt-10 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center">
                <img src="{{ asset('images/kisel.png') }}" alt="Logo" class="h-full w-full object-contain"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <span class="hidden h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 text-lg font-bold text-white">VD</span>
            </div>
            <h1 class="font-display mt-4 text-xl font-bold tracking-tight text-white">@yield('heading')</h1>
            <p class="mt-1 text-sm text-indigo-300">Visual Data &middot; Sales Monitoring</p>
        </div>
        <div class="p-8">
            @yield('content')
        </div>
    </div>
</body>
</html>
