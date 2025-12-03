<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RL Monitoring - PT Amarin Group</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased font-sans">

    <div class="relative min-h-screen bg-gray-900 flex flex-col justify-center items-center bg-[url('https://images.unsplash.com/photo-1500916434205-0c77489c6cf7?q=80&w=2574&auto=format&fit=crop')] bg-cover bg-center bg-no-repeat">

        <div class="absolute inset-0 bg-gradient-to-br from-blue-900/90 to-gray-900/90 mix-blend-multiply"></div>
        <div class="absolute inset-0 bg-black/40"></div>

        <div class="relative z-10 w-full max-w-md px-6 py-10 bg-white/95 backdrop-blur-sm rounded-xl shadow-2xl dark:bg-gray-800/95 border-t-4 border-blue-600 animate-fade-in-up mx-4">

            <div class="text-center mb-8">
                <div class="flex justify-center mb-6">
                    <img src="{{ asset('images/Logo_PT_ASM.jpg') }}"
                         alt="PT Amarin Group"
                         class="h-20 w-auto object-contain drop-shadow-md mix-blend-multiply dark:mix-blend-normal">
                </div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white tracking-tight uppercase">
                    PT Amarin Group
                </h2>
                <div class="w-16 h-1 bg-blue-600 mx-auto my-3 rounded-full"></div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 tracking-wide">
                    REQUISITION LETTER SYSTEM
                </p>
            </div>

            <div class="space-y-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="flex items-center justify-center w-full px-5 py-4 text-sm font-semibold text-white transition-all duration-200 bg-blue-700 rounded-lg hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 shadow-lg hover:shadow-blue-500/30 group">
                            <span>Buka Dashboard</span>
                            <svg class="w-4 h-4 ml-2 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>

                        <div class="text-center mt-4">
                            <span class="text-xs text-gray-500">Anda sudah login sebagai <strong>{{ Auth::user()->full_name ?? 'User' }}</strong></span>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="flex items-center justify-center w-full px-5 py-4 text-sm font-semibold text-white transition-all duration-200 bg-blue-700 rounded-lg hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 shadow-lg hover:shadow-blue-500/30 group">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            <span>Masuk ke Portal</span>
                        </a>

                        <div class="mt-6 text-center border-t border-gray-100 pt-4 dark:border-gray-700">
                            <p class="text-xs text-gray-400">
                                Mengalami kendala akses? <br>
                                <a href="#" class="text-blue-600 hover:underline dark:text-blue-400">Hubungi IT Support (ITSM)</a>
                            </p>
                        </div>
                    @endauth
                @endif
            </div>
        </div>
        <div class="absolute bottom-6 w-full text-center">
            <p class="text-xs text-blue-200/60 font-light tracking-widest uppercase">
                &copy; {{ date('Y') }} PT Amarin Ship Management. Internal Use Only.
            </p>
        </div>
    </div>
</body>
</html>
