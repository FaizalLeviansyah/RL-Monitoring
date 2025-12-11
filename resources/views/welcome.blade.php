<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RL Monitoring - PT Amarin Group</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 dark:bg-gray-900 font-sans">

    <div class="relative flex flex-col items-center justify-center min-h-screen overflow-hidden selection:bg-blue-500 selection:text-white">
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
            <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] rounded-full bg-blue-400/10 blur-[100px]"></div>
            <div class="absolute top-[40%] -right-[10%] w-[40%] h-[40%] rounded-full bg-cyan-300/10 blur-[100px]"></div>
            <div class="absolute -bottom-[20%] left-[20%] w-[60%] h-[40%] rounded-full bg-blue-600/10 blur-[100px]"></div>
        </div>
        <div class="relative z-10 w-full max-w-4xl px-6 text-center">

            <div class="flex justify-center mb-10 transform hover:scale-105 transition duration-500">
                <img src="{{ asset('images/Logo_PT_ASM.jpg') }}"
                     alt="PT Amarin Group Logo"
                     class="h-16 md:h-20 w-auto object-contain drop-shadow-xl rounded-lg bg-transparent mix-blend-multiply dark:mix-blend-normal">
            </div>

            <div class="space-y-4 mb-12">
                <h1 class="text-4xl font-extrabold tracking-tight leading-tight text-gray-900 md:text-5xl lg:text-6xl dark:text-white">
                    Requisition Letter (RL)
                </h1>
                <p class="text-lg font-medium text-gray-600 lg:text-2xl sm:px-16 dark:text-gray-300 max-w-2xl mx-auto">
                    Monitoring System
                </p>
                <div class="w-24 h-1 bg-blue-600 mx-auto rounded-full mt-6"></div>
            </div>

            <div class="flex flex-col items-center space-y-4 sm:flex-row sm:justify-center sm:space-y-0 sm:space-x-6">

                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="group relative inline-flex justify-center items-center py-3.5 px-8 text-base font-semibold text-center text-white rounded-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-900 shadow-lg hover:shadow-xl transition-all duration-200">
                            Masuk ke Dashboard
                            <svg class="w-4 h-4 ms-2 group-hover:translate-x-1 transition-transform" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="group relative inline-flex justify-center items-center py-3.5 px-10 text-base font-semibold text-center text-white rounded-full bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-900 shadow-lg hover:shadow-xl transition-all duration-200">
                            Login
                            <svg class="w-4 h-4 ms-2 group-hover:translate-x-1 transition-transform" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                            </svg>
                        </a>
                    @endauth
                @endif
            </div>

            <div class="mt-16 pt-8 border-t border-gray-200 dark:border-gray-800">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    &copy; {{ date('Y') }} IT Department - PT Amarin Ship Management. All rights reserved.
                </p>
                {{-- <p class="text-xs text-gray-400 mt-2">Internal Use Only</p> --}}
            </div>
        </div>
    </div>
</body>
</html>
