<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RL Monitoring - Executive Portal</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Background Mesh Terang */
        .bg-bright-neon {
            background-color: #f8fafc;
            background-image:
                radial-gradient(at 0% 0%, rgba(56, 189, 248, 0.1) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(139, 92, 246, 0.1) 0px, transparent 50%),
                linear-gradient(to bottom, #f1f5f9 0%, #ffffff 100%);
        }

        /* Animasi Glow Berdenyut */
        .animate-pulse-slow {
            animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>
<body class="antialiased bg-bright-neon font-sans text-slate-800 h-screen flex flex-col overflow-hidden relative">

    <div class="absolute top-[-20%] left-[-10%] w-[800px] h-[800px] rounded-full bg-cyan-200/40 blur-[120px] pointer-events-none animate-pulse-slow"></div>
    <div class="absolute bottom-[-20%] right-[-10%] w-[800px] h-[800px] rounded-full bg-purple-200/40 blur-[120px] pointer-events-none animate-pulse-slow" style="animation-delay: 2s"></div>

    <div class="relative z-10 flex flex-col items-center justify-center flex-grow px-6">

        <div class="text-center max-w-4xl space-y-4 mb-16 animate-fade-in-up">
            <p class="text-lg font-medium text-slate-500 tracking-wide uppercase">
                Welcome back
            </p>

            <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight leading-tight">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-700 via-indigo-600 to-purple-600 drop-shadow-sm">
                    {{ Auth::user()->full_name }}
                </span>
            </h1>

            <p class="text-xl text-slate-600 font-light mt-4">
                Please select your workspace to proceed.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 w-full max-w-5xl animate-fade-in-up delay-200">

            <a href="{{ route('dashboard.select_role', 'requester') }}" class="group relative block bg-white rounded-[2rem] p-1 shadow-[0_20px_50px_rgba(8,112,184,0.1)] hover:shadow-[0_20px_50px_rgba(8,112,184,0.25)] transition-all duration-300 transform hover:-translate-y-2">
                <div class="absolute inset-0 bg-gradient-to-br from-slate-100 to-slate-200 rounded-[2rem] group-hover:from-cyan-400 group-hover:to-blue-500 transition-colors duration-500"></div>

                <div class="relative bg-white/90 backdrop-blur-sm rounded-[30px] p-10 h-full flex flex-col items-start overflow-hidden">

                    <div class="h-20 w-20 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center mb-6 group-hover:bg-cyan-500 group-hover:text-white transition-all duration-300 shadow-inner group-hover:shadow-cyan-500/40">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>

                    <h3 class="text-3xl font-bold text-slate-800 mb-3 group-hover:text-cyan-700 transition-colors">Requester View</h3>
                    <p class="text-slate-500 text-base leading-relaxed mb-8">
                        Initiate new requests, track document progress, and manage your operational needs efficiently.
                    </p>

                    <span class="mt-auto inline-flex items-center text-sm font-bold text-cyan-600 uppercase tracking-wider group-hover:underline decoration-2 underline-offset-4">
                        Enter Operational Mode &rarr;
                    </span>

                    <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-cyan-50 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                </div>
            </a>

            <a href="{{ route('dashboard.select_role', 'approver') }}" class="group relative block bg-white rounded-[2rem] p-1 shadow-[0_20px_50px_rgba(124,58,237,0.1)] hover:shadow-[0_20px_50px_rgba(124,58,237,0.25)] transition-all duration-300 transform hover:-translate-y-2">
                <div class="absolute top-6 right-6 bg-gradient-to-r from-amber-400 to-orange-500 text-white text-[10px] font-bold px-3 py-1.5 rounded-full shadow-lg shadow-orange-500/30 z-20">
                    PRIMARY ROLE
                </div>

                <div class="absolute inset-0 bg-gradient-to-br from-slate-100 to-slate-200 rounded-[2rem] group-hover:from-purple-500 group-hover:to-indigo-600 transition-colors duration-500"></div>

                <div class="relative bg-white/90 backdrop-blur-sm rounded-[30px] p-10 h-full flex flex-col items-start overflow-hidden">

                    <div class="h-20 w-20 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center mb-6 group-hover:bg-purple-600 group-hover:text-white transition-all duration-300 shadow-inner group-hover:shadow-purple-500/40">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>

                    <h3 class="text-3xl font-bold text-slate-800 mb-3 group-hover:text-purple-700 transition-colors">Approver View</h3>
                    <p class="text-slate-500 text-base leading-relaxed mb-8">
                        Enter the Approval Center to review incoming requests, grant strategic approvals, and oversee workflows.
                    </p>

                    <span class="mt-auto inline-flex items-center text-sm font-bold text-purple-600 uppercase tracking-wider group-hover:underline decoration-2 underline-offset-4">
                        Enter Management Mode &rarr;
                    </span>

                    <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-purple-50 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                </div>
            </a>

        </div>
    </div>

    <div class="absolute bottom-0 w-full py-6 border-t border-slate-200/60 bg-white/40 backdrop-blur-md text-center z-20">
        <div class="flex justify-center items-center space-x-6 text-sm text-slate-500 font-medium">
            <span class="flex items-center">
                <span class="w-2 h-2 bg-emerald-400 rounded-full mr-2 shadow-[0_0_8px_rgba(52,211,153,0.6)]"></span>
                System Operational
            </span>
            <span class="text-slate-300">|</span>
            <span>Logged in as <strong class="text-slate-700">{{ Auth::user()->position->position_name ?? 'Executive' }}</strong></span>
            <span class="text-slate-300">|</span>
            <span>{{ Auth::user()->company->company_name ?? 'PT Amarin Ship Management' }}</span>
        </div>
    </div>

</body>
</html>

{{-- <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Executive Portal') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Subtle Glow Effect for Background */
        .bg-glow {
            background: radial-gradient(circle at 50% 50%, rgba(79, 70, 229, 0.15) 0%, transparent 50%);
        }
    </style>
</head>
<body class="font-sans antialiased bg-[#0f172a] text-white min-h-screen flex flex-col relative overflow-hidden">

    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-blue-900/30 blur-[120px] animate-pulse-slow"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-purple-900/30 blur-[120px] animate-pulse-slow" style="animation-delay: 2s;"></div>
    </div>

    <div class="flex-grow flex items-center justify-center z-10 px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-5xl w-full space-y-12 text-center relative">

            <div class="animate-fade-in-down">
                <div class="mb-6 flex justify-center">
                     <div class="p-3 bg-white/10 rounded-2xl backdrop-blur-lg shadow-lg shadow-blue-500/20">
                        <svg class="w-10 h-10 text-blue-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/></svg>
                     </div>
                </div>

                <h1 class="text-5xl md:text-6xl font-extrabold tracking-tight">
                    Welcome, <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-indigo-400 to-purple-400">{{ Auth::user()->full_name }}</span>
                </h1>
                <p class="mt-6 text-xl text-gray-400 max-w-2xl mx-auto leading-relaxed">
                    Welcome to the Executive Portal. Please select your role to commence today's operations.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-16 animate-fade-in-up delay-200">

                <a href="{{ route('dashboard.select_role', 'requester') }}" class="group relative block rounded-[2rem] p-[2px] bg-gradient-to-b from-blue-500/50 to-transparent hover:from-blue-400 hover:to-blue-600/30 transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl hover:shadow-blue-900/50">
                    <div class="relative h-full bg-gray-900/80 backdrop-blur-xl p-10 rounded-[30px] overflow-hidden group-hover:bg-gray-900/60 transition-all duration-500">
                        <div class="absolute right-0 top-0 opacity-5 group-hover:opacity-10 transition-opacity duration-500 transform translate-x-1/4 -translate-y-1/4">
                            <svg class="w-64 h-64 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                        </div>

                        <div class="flex items-center justify-between mb-8 relative z-10">
                            <div class="p-4 bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl shadow-lg shadow-blue-500/30 group-hover:shadow-blue-500/50 transition-all duration-500">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </div>
                            <span class="text-sm font-medium text-blue-400 bg-blue-400/10 px-4 py-1 rounded-full border border-blue-400/20">Operational Mode</span>
                        </div>

                        <div class="text-left relative z-10">
                            <h3 class="text-3xl font-bold text-white group-hover:text-blue-300 transition-colors">Requester View</h3>
                            <p class="mt-4 text-gray-400 text-base leading-relaxed">
                                Access as a requester to initiate new Requisition Letters (RL), track document status, and manage drafts.
                            </p>
                        </div>

                        <div class="mt-10 flex items-center text-blue-400 font-semibold group-hover:text-blue-300 transition-colors relative z-10">
                            <span>Proceed as Requester</span>
                            <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </div>
                    </div>
                </a>

                <a href="{{ route('dashboard.select_role', 'approver') }}" class="group relative block rounded-[2rem] p-[2px] bg-gradient-to-b from-purple-500/50 to-transparent hover:from-purple-400 hover:to-purple-600/30 transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl hover:shadow-purple-900/50">
                    <div class="absolute -top-3 -right-3 bg-gradient-to-r from-amber-400 to-orange-500 text-white text-xs font-bold px-4 py-2 rounded-full shadow-lg shadow-orange-500/20 z-20 animate-bounce-slow">
                        Primary Role
                    </div>

                    <div class="relative h-full bg-gray-900/80 backdrop-blur-xl p-10 rounded-[30px] overflow-hidden group-hover:bg-gray-900/60 transition-all duration-500">
                         <div class="absolute right-0 top-0 opacity-5 group-hover:opacity-10 transition-opacity duration-500 transform translate-x-1/4 -translate-y-1/4">
                             <svg class="w-64 h-64 text-purple-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>

                        <div class="flex items-center justify-between mb-8 relative z-10">
                            <div class="p-4 bg-gradient-to-br from-purple-500 to-indigo-700 rounded-2xl shadow-lg shadow-purple-500/30 group-hover:shadow-purple-500/50 transition-all duration-500">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <span class="text-sm font-medium text-purple-400 bg-purple-400/10 px-4 py-1 rounded-full border border-purple-400/20">Management Mode</span>
                        </div>

                        <div class="text-left relative z-10">
                            <h3 class="text-3xl font-bold text-white group-hover:text-purple-300 transition-colors">Approver View</h3>
                            <p class="mt-4 text-gray-400 text-base leading-relaxed">
                                Enter the Approval Center to review incoming requests, grant approvals, and oversee organizational activities.
                            </p>
                        </div>

                        <div class="mt-10 flex items-center text-purple-400 font-semibold group-hover:text-purple-300 transition-colors relative z-10">
                            <span>Enter Approval Center</span>
                            <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </div>
                    </div>
                </a>

            </div>

            <div class="mt-16 text-sm text-gray-500 animate-fade-in-up delay-300">
                <p>
                    Logged in as <span class="font-semibold text-gray-300">{{ Auth::user()->position->position_name ?? 'Executive' }}</span>
                    at <span class="font-semibold text-gray-300">{{ Auth::user()->company->company_name ?? 'Company' }}</span>.
                </p>
                <p class="mt-2 opacity-50">{{ date('l, d F Y') }}</p>
            </div>
        </div>
    </div>

    <style>
        .animate-pulse-slow {
            animation: pulse 6s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.5; }
        }
        .animate-bounce-slow {
             animation: bounce 3s infinite;
        }
    </style>
</body>
</html> --}}
