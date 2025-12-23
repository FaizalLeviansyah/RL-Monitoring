<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Executive Portal') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .bg-executive-neon {
                background-color: #f8fafc;
                background-image:
                    radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.15) 0px, transparent 50%),
                    radial-gradient(at 100% 0%, rgba(14, 165, 233, 0.15) 0px, transparent 50%),
                    linear-gradient(to bottom, #f1f5f9 0%, #f8fafc 100%);
            }
            .glass-panel {
                background: rgba(255, 255, 255, 0.7);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.5);
            }
        </style>
    </head>
    <body class="font-sans text-slate-900 antialiased bg-executive-neon relative min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 overflow-hidden">

        <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-cyan-400/20 rounded-full blur-[100px] animate-pulse"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[500px] h-[500px] bg-indigo-400/20 rounded-full blur-[100px] animate-pulse" style="animation-delay: 2s"></div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 z-10 relative">
            {{ $slot }}
        </div>
    </body>
</html>
