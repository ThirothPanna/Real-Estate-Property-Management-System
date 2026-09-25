<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sign in - {{ config('app.name', 'NEKJOUL IMANAGE') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased bg-white">
    <header class="w-full h-[72px] border-b border-gray-100 flex items-center justify-between px-6 lg:px-12 relative z-20 bg-white">
        <div class="flex items-center gap-2">
            <svg class="h-8 w-8 text-[#3f9c3a]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="text-[22px] font-bold text-gray-800 tracking-tight">{{ config('app.name', 'NEKJOUL IMANAGE') }}</span>
        </div>
        
        <div class="flex items-center gap-6">
            <span class="text-[15px] text-gray-600 hidden sm:block">Don't have an account?</span>
            <a href="{{ route('register') }}" class="bg-[#3f9c3a] hover:bg-[#34852f] text-white font-semibold py-2.5 px-6 rounded-md text-[15px] transition duration-200">
                Sign up
            </a>
        </div>
    </header>

    <div class="min-h-[calc(100vh-72px)] flex relative overflow-hidden bg-white">
        <div class="hidden lg:block absolute left-0 top-0 h-full w-1/3 z-0 pointer-events-none">
            <div class="absolute -top-32 -left-32 w-[700px] h-[700px] bg-[#eaf5ea] rounded-full"></div>
            <div class="absolute top-[55%] left-[15%] w-8 h-8 bg-[#d1e7d1] rounded-full"></div>
            <div class="absolute -bottom-40 -left-20 w-[500px] h-[500px] bg-[#eaf5ea] rounded-full"></div>
        </div>

        <div class="hidden lg:block absolute bottom-0 right-0 z-0 pointer-events-none">
             <div class="w-[600px] h-[600px] bg-[#eaf5ea] rounded-tl-full"></div>
        </div>

        <div class="flex-1 flex flex-col items-center justify-center z-10 px-4 py-12">
            {{ $slot }}
        </div>
    </div>
</body>
</html>