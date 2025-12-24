<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RL Monitoring - Portal</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .bg-mesh-light {
            background-color: #f8fafc;
            background-image:
                radial-gradient(at 0% 0%, rgba(56, 189, 248, 0.1) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(139, 92, 246, 0.1) 0px, transparent 50%);
        }
        .text-gradient {
            background: linear-gradient(to right, #0f172a, #334155);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        /* Efek Neon Border Bergerak */
        @keyframes border-dance {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .neon-border:hover {
            background: linear-gradient(90deg, #0ea5e9, #6366f1, #0ea5e9);
            background-size: 200% 200%;
            animation: border-dance 2s linear infinite;
        }
    </style>
</head>
<body class="antialiased bg-mesh-light font-sans text-slate-800 h-screen flex flex-col overflow-hidden relative">

    <div class="absolute top-[-10%] right-[-5%] w-[600px] h-[600px] rounded-full bg-cyan-200/20 blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] left-[-5%] w-[600px] h-[600px] rounded-full bg-purple-200/20 blur-[120px] pointer-events-none"></div>

    <div class="relative z-10 flex flex-col items-center justify-center flex-grow px-6">

        <div class="mb-10 animate-fade-in-down">
            <div class="p-6 bg-white/80 backdrop-blur-xl rounded-[2rem] shadow-2xl shadow-blue-900/5 border border-white ring-1 ring-slate-100 transform hover:scale-105 transition duration-500">
                <img src="{{ asset('images/Logo_PT_ASM.jpg') }}" alt="PT Amarin Group Logo" class="h-20 w-auto mix-blend-multiply">
            </div>
        </div>

        <div class="text-center max-w-4xl space-y-4 mb-16 animate-fade-in-up">
            <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight leading-tight">
                <span class="block text-slate-900">Requisition Letter</span>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 drop-shadow-sm">
                    Monitoring System
                </span>
            </h1>
            <p class="text-xl text-slate-500 font-medium max-w-2xl mx-auto">
                Precision Procurement Control. <span class="text-blue-600 font-bold">PT Amarin Ship Management.</span>
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 w-full max-w-4xl animate-fade-in-up delay-200">

            @if (Route::has('login'))
                @auth
                    <div class="md:col-span-2 flex justify-center">
                        <a href="{{ url('/dashboard') }}" class="group relative px-10 py-5 rounded-full bg-white shadow-xl shadow-blue-500/20 border-2 border-transparent neon-border transition-all duration-300 hover:shadow-blue-500/40 transform hover:-translate-y-1">
                            <div class="absolute inset-[2px] bg-white rounded-full z-0"></div> <span class="relative z-10 flex items-center text-lg font-bold text-slate-800 group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-blue-600 group-hover:to-indigo-600 transition-colors">
                                ACCESS DASHBOARD
                                <svg class="w-6 h-6 ml-3 group-hover:translate-x-1 transition-transform text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </span>
                        </a>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="group relative block bg-white rounded-3xl p-1 shadow-xl shadow-slate-200/50 hover:shadow-cyan-500/20 transition-all duration-300 transform hover:-translate-y-2">
                        <div class="absolute inset-0 bg-gradient-to-br from-slate-100 to-slate-200 rounded-3xl group-hover:from-cyan-400 group-hover:to-blue-500 transition-colors duration-500"></div>

                        <div class="relative bg-white rounded-[20px] p-8 h-full flex flex-col items-center text-center overflow-hidden">
                            <div class="h-16 w-16 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center mb-4 group-hover:bg-cyan-600 group-hover:text-white transition-all duration-300 shadow-inner group-hover:shadow-cyan-500/50">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-slate-800 mb-2">Requester Login</h3>
                            <p class="text-slate-500 text-sm mb-6">Initiate requests and track document progress.</p>

                            <span class="mt-auto inline-flex items-center text-sm font-bold text-cyan-600 group-hover:underline">
                                ENTER PORTAL &rarr;
                            </span>
                        </div>
                    </a>

                    <a href="{{ route('login') }}" class="group relative block bg-white rounded-3xl p-1 shadow-xl shadow-slate-200/50 hover:shadow-purple-500/20 transition-all duration-300 transform hover:-translate-y-2">
                        <div class="absolute inset-0 bg-gradient-to-br from-slate-100 to-slate-200 rounded-3xl group-hover:from-purple-500 group-hover:to-pink-500 transition-colors duration-500"></div>

                        <div class="relative bg-white rounded-[20px] p-8 h-full flex flex-col items-center text-center overflow-hidden">
                            <div class="h-16 w-16 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center mb-4 group-hover:bg-purple-600 group-hover:text-white transition-all duration-300 shadow-inner group-hover:shadow-purple-500/50">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-slate-800 mb-2">Approver Login</h3>
                            <p class="text-slate-500 text-sm mb-6">Review, approve, and manage workflows.</p>

                            <span class="mt-auto inline-flex items-center text-sm font-bold text-purple-600 group-hover:underline">
                                ENTER PORTAL &rarr;
                            </span>
                        </div>
                    </a>
                @endauth
            @endif

        </div>
    </div>

    <div class="absolute bottom-0 w-full py-4 border-t border-slate-200 bg-white/60 backdrop-blur-md text-center z-20">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">
            &copy; {{ date('Y') }} PT Amarin Ship Management <span class="mx-2 text-slate-300">•</span> Authorized Access Only
        </p>
    </div>

</body>
</html>
