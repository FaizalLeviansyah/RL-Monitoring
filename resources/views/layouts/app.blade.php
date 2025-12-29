<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'RL Tracker') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        .bg-executive-dashboard {
            background-color: #f8fafc;
            background-image:
                radial-gradient(at 0% 0%, rgba(56, 189, 248, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(139, 92, 246, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(14, 165, 233, 0.05) 0px, transparent 50%),
                linear-gradient(to bottom, #f8fafc 0%, #f1f5f9 100%);
            background-attachment: fixed;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-right: 1px solid rgba(255, 255, 255, 0.6);
            border-bottom: 1px solid rgba(255, 255, 255, 0.6);
        }

        .sidebar-transition { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }

        .sidebar-item { position: relative; overflow: hidden; transition: all 0.3s ease; white-space: nowrap; }
        .sidebar-item::before {
            content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px;
            border-radius: 0 4px 4px 0; opacity: 0; transition: opacity 0.3s ease;
        }
        .sidebar-item:hover::before, .sidebar-item.active::before { opacity: 1; }

        /* Warna Neon */
        .neon-blue:hover, .neon-blue.active { background: rgba(59, 130, 246, 0.1); color: #2563eb; } .neon-blue::before { background: #2563eb; box-shadow: 0 0 10px #2563eb; }
        .neon-orange:hover, .neon-orange.active { background: rgba(249, 115, 22, 0.1); color: #ea580c; } .neon-orange::before { background: #ea580c; box-shadow: 0 0 10px #ea580c; }
        .neon-purple:hover, .neon-purple.active { background: rgba(147, 51, 234, 0.1); color: #7e22ce; } .neon-purple::before { background: #7e22ce; box-shadow: 0 0 10px #7e22ce; }
        .neon-green:hover, .neon-green.active { background: rgba(22, 163, 74, 0.1); color: #15803d; } .neon-green::before { background: #15803d; box-shadow: 0 0 10px #15803d; }
        .neon-yellow:hover, .neon-yellow.active { background: rgba(234, 179, 8, 0.1); color: #ca8a04; } .neon-yellow::before { background: #ca8a04; box-shadow: 0 0 10px #ca8a04; }
        .neon-red:hover, .neon-red.active { background: rgba(220, 38, 38, 0.1); color: #b91c1c; } .neon-red::before { background: #b91c1c; box-shadow: 0 0 10px #b91c1c; }
        .neon-teal:hover, .neon-teal.active { background: rgba(13, 148, 136, 0.1); color: #0f766e; } .neon-teal::before { background: #0f766e; box-shadow: 0 0 10px #0f766e; }
        .neon-indigo:hover, .neon-indigo.active { background: rgba(79, 70, 229, 0.1); color: #4338ca; } .neon-indigo::before { background: #4338ca; box-shadow: 0 0 10px #4338ca; }
        .neon-pink:hover, .neon-pink.active { background: rgba(236, 72, 153, 0.1); color: #db2777; } .neon-pink::before { background: #db2777; box-shadow: 0 0 10px #db2777; }

        #mobile-backdrop { background-color: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); }
        .sidebar-collapsed .sidebar-text, .sidebar-collapsed .section-title, .sidebar-collapsed .sidebar-badge { display: none; }
        .sidebar-collapsed .sidebar-item { justify-content: center; padding-left: 0; padding-right: 0; }
        .sidebar-collapsed .sidebar-item svg { margin-right: 0; }
    </style>
</head>
<body class="bg-executive-dashboard font-sans antialiased text-slate-600 dark:text-slate-300 relative">

    <div class="fixed top-[-20%] left-[-10%] w-[600px] h-[600px] bg-cyan-400/10 rounded-full blur-[100px] pointer-events-none z-0"></div>
    <div class="fixed bottom-[-20%] right-[-10%] w-[600px] h-[600px] bg-purple-400/10 rounded-full blur-[100px] pointer-events-none z-0"></div>

@php
    $user = Auth::user();
    $userPos = $user->position->position_name ?? '';
    $approverRoles = ['Manager', 'Director', 'Managing Director', 'Deputy Managing Director', 'General Manager', 'President Director'];
    $isSuperAdmin = $userPos === 'Super Admin';
    $isApprover = in_array($userPos, $approverRoles);
    $isRequester = !$isSuperAdmin && !$isApprover;
    $currentMode = session('active_role');
    $showRequesterMenu = !$isSuperAdmin && ($isRequester || $currentMode == 'requester');
    $showMonitoringMenu = $isSuperAdmin || $isApprover;
@endphp

    {{-- NAVBAR --}}
    <nav class="fixed top-0 z-50 w-full glass-panel h-16 transition-all duration-300">
        <div class="px-3 lg:px-5 lg:pl-3 h-full">
            <div class="flex items-center justify-between h-full">
                <div class="flex items-center justify-start rtl:justify-end">
                    <button id="toggle-sidebar-btn" type="button" class="inline-flex items-center p-2 text-sm text-slate-500 rounded-lg hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-200 transition-transform active:scale-95 mr-2">
                        <span class="sr-only">Toggle sidebar</span>
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path></svg>
                    </button>
                    <a href="{{ route('dashboard') }}" class="flex ms-1 md:me-24 group items-center">
                        <div class="bg-gradient-to-br from-blue-600 to-indigo-600 text-white p-1.5 rounded-lg mr-3 shadow-lg shadow-blue-500/30 group-hover:rotate-6 transition-transform duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="self-center text-lg font-extrabold whitespace-nowrap text-slate-800 tracking-tight">
                            RL <span class="text-blue-600">Monitoring</span>
                        </span>
                    </a>
                </div>
                <div class="flex items-center">
                    <div class="flex items-center ms-3">
                        <div class="hidden md:flex flex-col items-end mr-4">
                            <span class="text-sm font-bold text-slate-700">{{ Auth::user()->full_name }}</span>
                            <span class="text-[10px] uppercase font-bold tracking-wider text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100">{{ Auth::user()->position->position_name ?? 'Staff' }}</span>
                        </div>
                        <button type="button" class="flex text-sm bg-slate-800 rounded-full focus:ring-4 focus:ring-blue-100 transition shadow-lg border-2 border-white" aria-expanded="false" data-dropdown-toggle="dropdown-user">
                            <span class="sr-only">Open user menu</span>
                            @if(Auth::user()->profile_photo_path)
                                <img class="w-9 h-9 rounded-full object-cover" src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" alt="User Photo">
                            @else
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-inner">{{ substr(Auth::user()->full_name, 0, 1) }}</div>
                            @endif
                        </button>
                        <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.1)] border border-slate-100 w-60 animate-fade-in-up" id="dropdown-user">
                            <div class="px-4 py-4 bg-slate-50 rounded-t-2xl">
                                <p class="text-sm text-slate-900 font-bold">{{ Auth::user()->full_name }}</p>
                                <p class="text-xs font-medium text-slate-500 truncate">{{ Auth::user()->email_work }}</p>
                            </div>
                            <ul class="py-2">
                                <li>
                                    <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2.5 text-sm text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition">
                                        <svg class="w-4 h-4 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg> Settings
                                    </a>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <a href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="flex items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition">
                                            <svg class="w-4 h-4 mr-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg> Sign out
                                        </a>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div id="mobile-backdrop" class="fixed inset-0 z-30 hidden lg:hidden transition-opacity duration-300 opacity-0"></div>

    {{-- SIDEBAR --}}
    <aside id="main-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 glass-panel sidebar-transition transform -translate-x-full lg:translate-x-0 shadow-2xl lg:shadow-none" aria-label="Sidebar">
        <div class="h-full px-3 pb-4 overflow-y-auto overflow-x-hidden">
            <ul class="space-y-2 font-medium">

                <li>
                    <a href="{{ route('dashboard') }}" class="sidebar-item neon-blue flex items-center p-3 rounded-xl text-slate-600 {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition duration-200" fill="currentColor" viewBox="0 0 22 21"><path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z"/><path d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z"/></svg>
                        <span class="ms-3 font-semibold sidebar-text">Dashboard</span>
                    </a>
                </li>

                {{-- === MENU ADMIN (SUPER ADMIN) === --}}
                @if($isSuperAdmin)
                    <li class="pt-4 mt-2 mb-2 section-title">
                        <div class="px-2 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Admin Control</div>
                    </li>

                    <li>
                        <a href="{{ route('admin.master-items.index') }}" class="sidebar-item neon-teal flex items-center p-3 rounded-xl text-slate-600 {{ request()->routeIs('admin.master-items.*') ? 'active' : '' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            <span class="ms-3 font-medium sidebar-text">Master Items</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.users.index') }}" class="sidebar-item neon-indigo flex items-center p-3 rounded-xl text-slate-600 {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                            <span class="ms-3 font-medium sidebar-text">Manage Users</span>
                        </a>
                    </li>

                    {{-- FIX HOVER GLOBAL DATA --}}
                    <li>
                        <a href="{{ route('admin.monitoring.index') }}"
                           class="sidebar-item neon-pink flex items-center p-3 rounded-xl text-slate-600 {{ request()->routeIs('admin.monitoring*') ? 'active' : '' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            <span class="ms-3 font-medium sidebar-text">Global Data</span>
                        </a>
                    </li>
                @endif

                {{-- === MENU REQUESTER === --}}
                @if($showRequesterMenu)
                    <li class="pt-4 mt-2 mb-2 section-title">
                        <div class="px-2 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">My Requests</div>
                    </li>
                    <li>
                        <a href="{{ route('requisitions.create') }}" class="sidebar-item neon-red flex items-center p-3 rounded-xl text-slate-600 {{ request()->routeIs('requisitions.create') ? 'active' : '' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M5 5V.13a2.96 2.96 0 0 0-1.293.749L.879 3.707A2.96 2.96 0 0 0 .13 5H5Z"/><path d="M6.737 11.061a2.961 2.961 0 0 1 .81-1.515l6.117-6.116A4.839 4.839 0 0 1 16 2.141V2a1.97 1.97 0 0 0-1.933-2H7v5a2 2 0 0 1-2 2H0v11a1.969 1.969 0 0 0 1.933 2h12.134A1.97 1.97 0 0 0 16 18v-3.093l-1.546 1.546c-.413.413-.94.695-1.513.81l-3.4.679a2.947 2.947 0 0 1-1.85-.227 2.96 2.96 0 0 1-1.635-3.257l.681-3.397Z"/></svg>
                            <span class="ms-3 font-medium sidebar-text">Create New RL</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('requisitions.status', 'draft') }}" class="sidebar-item neon-blue flex items-center p-3 rounded-xl text-slate-600 {{ request()->is('requisitions/status/draft') ? 'active' : '' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path></svg>
                            <span class="flex-1 ms-3 font-medium sidebar-text">Drafts</span>
                            @if(isset($countDraft) && $countDraft > 0) <span class="sidebar-badge bg-slate-100 text-slate-600 rounded-full font-bold text-xs px-2 py-0.5 ml-2">{{ $countDraft }}</span> @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('requisitions.status', 'on_progress') }}" class="sidebar-item neon-orange flex items-center p-3 rounded-xl text-slate-600 {{ request()->is('requisitions/status/on_progress') ? 'active' : '' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="flex-1 ms-3 font-medium sidebar-text">Waiting Approval</span>
                            @if(isset($countMyOnProgress) && $countMyOnProgress > 0)
                                <span class="sidebar-badge bg-orange-100 text-orange-700 rounded-full font-bold text-xs px-2 py-0.5 ml-2">{{ $countMyOnProgress }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('requisitions.status', 'partially_approved') }}" class="sidebar-item neon-purple flex items-center p-3 rounded-xl text-slate-600 {{ request()->is('requisitions/status/partially_approved') ? 'active' : '' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"></path></svg>
                            <span class="flex-1 ms-3 font-medium sidebar-text">Waiting Director</span>
                            @if(isset($countMyWaitingDirector) && $countMyWaitingDirector > 0)
                                <span class="sidebar-badge bg-purple-100 text-purple-700 rounded-full font-bold text-xs px-2 py-0.5 ml-2">{{ $countMyWaitingDirector }}</span>
                            @endif
                        </a>
                    </li>
                   {{-- 5. Approved --}}
                    <li>
                        <a href="{{ route('requisitions.status', 'approved') }}" 
                           class="sidebar-item neon-green flex items-center p-3 rounded-xl text-slate-600 
                           {{-- LOGIKA BARU: Cek URL ATAU Cek Variabel activeMenu --}}
                           {{ (request()->is('requisitions/status/approved') || (isset($activeMenu) && $activeMenu == 'approved')) ? 'active' : '' }}">
                            
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="ms-3 font-medium sidebar-text">Approved</span>
                            
                            {{-- Badge Counter --}}
                            @if(isset($countMyApprovals) && $countMyApprovals > 0) 
                                <span class="sidebar-badge bg-green-100 text-green-700 rounded-full font-bold text-xs px-2 py-0.5 ml-2 animate-pulse">{{ $countMyApprovals }}</span> 
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('requisitions.status', 'rejected') }}" class="sidebar-item neon-red flex items-center p-3 rounded-xl text-slate-600 {{ request()->is('requisitions/status/rejected') ? 'active' : '' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="flex-1 ms-3 font-medium sidebar-text">Rejected</span>
                            @if(isset($countRejected) && $countRejected > 0) <span class="sidebar-badge bg-red-100 text-red-700 rounded-full font-bold text-xs px-2 py-0.5 ml-2">{{ $countRejected }}</span> @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('requisitions.status', 'completed') }}" class="sidebar-item neon-teal flex items-center p-3 rounded-xl text-slate-600 {{ request()->is('requisitions/status/completed') ? 'active' : '' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span class="ms-3 font-medium sidebar-text">Completed</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('requisitions.department') }}" class="sidebar-item neon-indigo flex items-center p-3 rounded-xl text-slate-600 {{ request()->routeIs('requisitions.department') ? 'active' : '' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span class="flex-1 ms-3 font-medium sidebar-text">Department Activity</span>
                        </a>
                    </li>
                @endif

                {{-- === MENU MONITORING === --}}
                @if($showMonitoringMenu)
                    <li class="pt-4 mt-2 mb-2 section-title">
                        <div class="px-2 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Live Monitoring</div>
                    </li>

                    <li>
                        <a href="{{ route('requisitions.status', 'on_progress') }}" class="sidebar-item neon-orange flex items-center p-3 rounded-xl text-slate-600 {{ (request()->is('requisitions/status/on_progress') || request('ref') == 'on_progress') ? 'active' : '' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="flex-1 ms-3 font-medium sidebar-text">Waiting Approval</span>
                            @if(isset($countGlobalOnProgress) && $countGlobalOnProgress > 0)
                                <span class="sidebar-badge bg-orange-100 text-orange-700 rounded-full font-bold text-xs px-2 py-0.5 ml-2">{{ $countGlobalOnProgress }}</span>
                            @endif
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('requisitions.status', 'partially_approved') }}" class="sidebar-item neon-purple flex items-center p-3 rounded-xl text-slate-600 {{ (request()->is('requisitions/status/partially_approved') || request('ref') == 'partially_approved') ? 'active' : '' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"></path></svg>
                            <span class="flex-1 ms-3 font-medium sidebar-text">Waiting Director</span>
                            @if(isset($countGlobalWaitingDirector) && $countGlobalWaitingDirector > 0)
                                <span class="sidebar-badge bg-purple-100 text-purple-700 rounded-full font-bold text-xs px-2 py-0.5 ml-2">{{ $countGlobalWaitingDirector }}</span>
                            @endif
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('requisitions.status', 'approved') }}" class="sidebar-item neon-green flex items-center p-3 rounded-xl text-slate-600 {{ (request()->is('requisitions/status/approved') || request('ref') == 'approved') ? 'active' : '' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="ms-3 font-medium sidebar-text">Final Approved</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('requisitions.status', 'waiting_supply') }}" class="sidebar-item neon-yellow flex items-center p-3 rounded-xl text-slate-600 {{ (request()->is('requisitions/status/waiting_supply') || request('ref') == 'waiting_supply') ? 'active' : '' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"></path></svg>
                            <span class="flex-1 ms-3 font-medium sidebar-text">Waiting Supply</span>
                            @if(isset($countGlobalWaitingSupply) && $countGlobalWaitingSupply > 0)
                                <span class="sidebar-badge bg-yellow-100 text-yellow-700 rounded-full font-bold text-xs px-2 py-0.5 ml-2">{{ $countGlobalWaitingSupply }}</span>
                            @endif
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('requisitions.status', 'rejected') }}" class="sidebar-item neon-red flex items-center p-3 rounded-xl text-slate-600 {{ (request()->is('requisitions/status/rejected') || request('ref') == 'rejected') ? 'active' : '' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="flex-1 ms-3 font-medium sidebar-text">Rejected</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('requisitions.status', 'completed') }}" class="sidebar-item neon-teal flex items-center p-3 rounded-xl text-slate-600 {{ (request()->is('requisitions/status/completed') || request('ref') == 'completed') ? 'active' : '' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="ms-3 font-medium sidebar-text">Completed</span>
                        </a>
                    </li>

                    {{-- FIX: Department Activity Disembunyikan untuk Super Admin --}}
                    @if(!$isSuperAdmin)
                        <li>
                            <a href="{{ route('requisitions.department') }}" class="sidebar-item neon-indigo flex items-center p-3 rounded-xl text-slate-600 {{ request()->routeIs('requisitions.department') ? 'active' : '' }}">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                                <span class="flex-1 ms-3 font-medium sidebar-text">Department Activity</span>
                            </a>
                        </li>
                    @endif
                @endif

                <li class="pt-6 mt-2 mb-2 border-t border-slate-100"></li>
                <li><a href="{{ route('profile.edit') }}" class="sidebar-item neon-blue flex items-center p-3 rounded-xl text-slate-600 {{ request()->routeIs('profile.edit') ? 'active' : '' }}"><svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg><span class="flex-1 ms-3 font-medium sidebar-text">Settings</span></a></li>
            </ul>
        </div>
    </aside>

    <div id="main-content" class="p-4 sm:ml-64 mt-16 lg:mt-16 sidebar-transition relative z-10">
        {{ $slot }}
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('main-sidebar');
            const mainContent = document.getElementById('main-content');
            const toggleBtn = document.getElementById('toggle-sidebar-btn');
            const backdrop = document.getElementById('mobile-backdrop');

            const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
            if (isCollapsed && window.innerWidth >= 1024) { collapseSidebar(); }

            toggleBtn.addEventListener('click', function() {
                if (window.innerWidth < 1024) {
                    sidebar.classList.toggle('-translate-x-full');
                    backdrop.classList.toggle('hidden');
                    setTimeout(() => backdrop.classList.toggle('opacity-0'), 10);
                } else {
                    if (sidebar.classList.contains('w-64')) {
                        collapseSidebar();
                        localStorage.setItem('sidebar-collapsed', 'true');
                    } else {
                        expandSidebar();
                        localStorage.setItem('sidebar-collapsed', 'false');
                    }
                }
            });

            backdrop.addEventListener('click', function() {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('opacity-0');
                setTimeout(() => backdrop.classList.add('hidden'), 300);
            });

            function collapseSidebar() {
                sidebar.classList.remove('w-64');
                sidebar.classList.add('w-20', 'sidebar-collapsed');
                mainContent.classList.remove('sm:ml-64');
                mainContent.classList.add('sm:ml-20');
            }

            function expandSidebar() {
                sidebar.classList.remove('w-20', 'sidebar-collapsed');
                sidebar.classList.add('w-64');
                mainContent.classList.remove('sm:ml-20');
                mainContent.classList.add('sm:ml-64');
            }
        });
    </script>
</body>
</html>

{{-- <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'RL Tracker') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Custom Neon Utilities */
        .glass-nav {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.5);
        }

        /* Sidebar Transitions */
        .sidebar-transition {
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-item {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            white-space: nowrap; /* Mencegah teks turun saat dikecilkan */
        }

        /* Efek Garis Neon di Kiri */
        .sidebar-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            border-radius: 0 4px 4px 0;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .sidebar-item:hover::before, .sidebar-item.active::before {
            opacity: 1;
        }

        /* Variasi Warna Neon (Preserved) */
        .neon-blue:hover, .neon-blue.active { background: rgba(59, 130, 246, 0.08); color: #2563eb; }
        .neon-blue::before { background: #2563eb; box-shadow: 0 0 12px #2563eb; }

        .neon-orange:hover, .neon-orange.active { background: rgba(249, 115, 22, 0.08); color: #ea580c; }
        .neon-orange::before { background: #ea580c; box-shadow: 0 0 12px #ea580c; }

        .neon-purple:hover, .neon-purple.active { background: rgba(147, 51, 234, 0.08); color: #7e22ce; }
        .neon-purple::before { background: #7e22ce; box-shadow: 0 0 12px #7e22ce; }

        .neon-green:hover, .neon-green.active { background: rgba(22, 163, 74, 0.08); color: #15803d; }
        .neon-green::before { background: #15803d; box-shadow: 0 0 12px #15803d; }

        .neon-yellow:hover, .neon-yellow.active { background: rgba(234, 179, 8, 0.08); color: #ca8a04; }
        .neon-yellow::before { background: #ca8a04; box-shadow: 0 0 12px #ca8a04; }

        .neon-red:hover, .neon-red.active { background: rgba(220, 38, 38, 0.08); color: #b91c1c; }
        .neon-red::before { background: #b91c1c; box-shadow: 0 0 12px #b91c1c; }

        .neon-teal:hover, .neon-teal.active { background: rgba(13, 148, 136, 0.08); color: #0f766e; }
        .neon-teal::before { background: #0f766e; box-shadow: 0 0 12px #0f766e; }

        .neon-indigo:hover, .neon-indigo.active { background: rgba(79, 70, 229, 0.08); color: #4338ca; }
        .neon-indigo::before { background: #4338ca; box-shadow: 0 0 12px #4338ca; }

        /* Logic Collapsed Sidebar */
        .sidebar-collapsed .sidebar-text {
            opacity: 0;
            width: 0;
            display: none;
        }
        .sidebar-collapsed .section-title {
            display: none;
        }
        .sidebar-collapsed .sidebar-badge {
            position: absolute;
            top: 4px;
            right: 4px;
            padding: 2px 4px;
            font-size: 8px;
            min-width: 14px;
            height: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        /* Memusatkan icon saat collapsed */
        .sidebar-collapsed .sidebar-item {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }
        .sidebar-collapsed .sidebar-item svg {
            margin-right: 0;
            width: 24px;
            height: 24px;
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-gray-900 font-sans antialiased text-slate-600 dark:text-slate-300">

@php
    $user = Auth::user();
    $userPos = $user->position->position_name ?? '';

    $approverRoles = [
        'Manager', 'Director', 'Managing Director',
        'Deputy Managing Director', 'General Manager', 'President Director'
    ];

    $isSuperAdmin = $userPos === 'Super Admin';
    $isApprover = in_array($userPos, $approverRoles);
    $isRequester = !$isSuperAdmin && !$isApprover;
    $currentMode = session('active_role');

    $showRequesterMenu = !$isSuperAdmin && ($isRequester || $currentMode == 'requester');
    $showMonitoringMenu = $isSuperAdmin || $isApprover;
@endphp

    <nav class="fixed top-0 z-50 w-full glass-nav shadow-[0_4px_30px_rgba(0,0,0,0.03)] transition-all duration-300">
        <div class="absolute bottom-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-blue-200 to-transparent opacity-50"></div>

        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">

                <div class="flex items-center justify-start rtl:justify-end">
                    <button id="toggle-sidebar-btn" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600 mr-2 transition-transform active:scale-95">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path></svg>
                    </button>

                    <a href="{{ route('dashboard') }}" class="flex ms-1 md:me-24 group items-center">
                        <div class="bg-gradient-to-br from-blue-600 to-indigo-600 text-white p-2 rounded-xl mr-3 shadow-lg shadow-blue-500/30 group-hover:rotate-6 transition-transform duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-lg font-extrabold text-slate-800 dark:text-white tracking-tight leading-none">
                                RL <span class="text-blue-600">Monitoring</span>
                            </span>
                            <span class="text-[10px] text-slate-400 font-medium tracking-widest uppercase">Executive System</span>
                        </div>
                    </a>
                </div>

                <div class="flex items-center">
                    <div class="flex items-center ms-3">
                        <div class="hidden md:flex flex-col items-end mr-4">
                            <span class="text-sm font-bold text-slate-700 dark:text-white">{{ Auth::user()->full_name }}</span>
                            <span class="text-[10px] uppercase font-bold tracking-wider text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100">{{ Auth::user()->position->position_name ?? 'Staff' }}</span>
                        </div>

                        <button type="button" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-blue-100 dark:focus:ring-gray-600 transition shadow-lg border-2 border-white dark:border-gray-600" aria-expanded="false" data-dropdown-toggle="dropdown-user">
                            @if(Auth::user()->profile_photo_path)
                                <img class="w-10 h-10 rounded-full object-cover" src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" alt="User Photo">
                            @else
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-inner">{{ substr(Auth::user()->full_name, 0, 1) }}</div>
                            @endif
                        </button>

                        <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.1)] border border-slate-100 w-60 animate-fade-in-up" id="dropdown-user">
                            <div class="px-4 py-4 bg-slate-50 rounded-t-2xl">
                                <p class="text-sm text-slate-900 font-bold">{{ Auth::user()->full_name }}</p>
                                <p class="text-xs font-medium text-slate-500 truncate">{{ Auth::user()->email_work }}</p>
                            </div>
                            <ul class="py-2">
                                <li>
                                    <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2.5 text-sm text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition group">
                                        <svg class="w-4 h-4 mr-3 text-slate-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        Account Settings
                                    </a>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <a href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="flex items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition group">
                                            <svg class="w-4 h-4 mr-3 text-red-400 group-hover:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                            Sign out
                                        </a>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <aside id="main-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-24 bg-white border-r border-slate-100 dark:bg-gray-800 dark:border-gray-700 shadow-[4px_0_24px_rgba(0,0,0,0.02)] sidebar-transition transform -translate-x-full sm:translate-x-0" aria-label="Sidebar">

        <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800 overflow-x-hidden">
            <ul class="space-y-2 font-medium">

                <li>
                    <a href="{{ route('dashboard') }}" class="sidebar-item neon-blue flex items-center p-3 rounded-xl text-slate-600 dark:text-slate-400 {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0 transition duration-200" fill="currentColor" viewBox="0 0 22 21"><path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z"/><path d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z"/></svg>
                        <span class="ms-3 font-semibold sidebar-text">Dashboard</span>
                    </a>
                </li>

                @if($isSuperAdmin)
                    <li class="pt-4 mt-2 mb-2 section-title">
                        <div class="px-2 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Admin Control</div>
                    </li>
                    <li>
                        <a href="{{ route('admin.master-items.index') }}" class="sidebar-item neon-teal flex items-center p-3 rounded-xl text-slate-600 {{ request()->routeIs('admin.master-items.*') ? 'active' : '' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            <span class="ms-3 font-medium sidebar-text">Master Items</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.users.index') }}" class="sidebar-item neon-indigo flex items-center p-3 rounded-xl text-slate-600 {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                            <span class="ms-3 font-medium sidebar-text">Manage Users</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.monitoring.index') }}" class="sidebar-item neon-purple flex items-center p-3 rounded-xl text-slate-600 {{ request()->routeIs('admin.monitoring.*') ? 'active' : '' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            <span class="ms-3 font-medium sidebar-text">Global Monitoring</span>
                        </a>
                    </li>
                @endif

                @if($showRequesterMenu)
                    <li class="pt-4 mt-2 mb-2 section-title">
                        <div class="px-2 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">My Requests</div>
                    </li>
                    <li>
                        <a href="{{ route('requisitions.create') }}" class="sidebar-item neon-red flex items-center p-3 rounded-xl text-slate-600 {{ request()->routeIs('requisitions.create') ? 'active' : '' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M5 5V.13a2.96 2.96 0 0 0-1.293.749L.879 3.707A2.96 2.96 0 0 0 .13 5H5Z"/><path d="M6.737 11.061a2.961 2.961 0 0 1 .81-1.515l6.117-6.116A4.839 4.839 0 0 1 16 2.141V2a1.97 1.97 0 0 0-1.933-2H7v5a2 2 0 0 1-2 2H0v11a1.969 1.969 0 0 0 1.933 2h12.134A1.97 1.97 0 0 0 16 18v-3.093l-1.546 1.546c-.413.413-.94.695-1.513.81l-3.4.679a2.947 2.947 0 0 1-1.85-.227 2.96 2.96 0 0 1-1.635-3.257l.681-3.397Z"/><path d="M8.961 16a.93.93 0 0 0 .189-.019l3.4-.679a.961.961 0 0 0 .53-.271l6.119-6.117a2.937 2.937 0 0 0-4.152-4.152l-6.117 6.119a.96.96 0 0 0-.271.53l-.679 3.4a.939.939 0 0 0 1.28 1.28l-.3.303Z"/></svg>
                            <span class="ms-3 font-medium sidebar-text">Create New RL</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('requisitions.status', 'draft') }}" class="sidebar-item neon-blue flex items-center p-3 rounded-xl text-slate-600 {{ request()->is('requisitions/status/draft') ? 'active' : '' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path></svg>
                            <span class="flex-1 ms-3 font-medium sidebar-text">Drafts</span>
                            @if(isset($countDraft) && $countDraft > 0)
                                <span class="sidebar-badge bg-slate-100 text-slate-600 rounded-full">{{ $countDraft }}</span>
                            @endif
                        </a>
                    </li>
                @endif

                @if($showMonitoringMenu)
                <li class="pt-4 mt-2 mb-2 section-title">
                    <div class="px-2 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Live Monitoring</div>
                </li>

                <li>
                    <a href="{{ route('requisitions.status', 'on_progress') }}" class="sidebar-item neon-orange flex items-center p-3 rounded-xl text-slate-600 {{ (request()->is('requisitions/status/on_progress') || request('ref') == 'on_progress') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="flex-1 ms-3 font-medium sidebar-text">Waiting Approval</span>
                        @if(isset($countPendingApprove) && $countPendingApprove > 0)
                            <span class="sidebar-badge bg-orange-100 text-orange-700 rounded-full">{{ $countPendingApprove }}</span>
                        @endif
                    </a>
                </li>

                <li>
                    <a href="{{ route('requisitions.status', 'partially_approved') }}" class="sidebar-item neon-purple flex items-center p-3 rounded-xl text-slate-600 {{ (request()->is('requisitions/status/partially_approved') || request('ref') == 'partially_approved') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"></path></svg>
                        <span class="flex-1 ms-3 font-medium sidebar-text">Waiting Director</span>
                        @if($countWaitingDirector > 0)
                            <span class="sidebar-badge bg-purple-100 text-purple-700 rounded-full">{{ $countWaitingDirector }}</span>
                        @endif
                    </a>
                </li>

                <li>
                    <a href="{{ route('requisitions.status', 'approved') }}" class="sidebar-item neon-green flex items-center p-3 rounded-xl text-slate-600 {{ (request()->is('requisitions/status/approved') || request('ref') == 'approved') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="ms-3 font-medium sidebar-text">Final Approved</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('requisitions.status', 'waiting_supply') }}" class="sidebar-item neon-yellow flex items-center p-3 rounded-xl text-slate-600 {{ (request()->is('requisitions/status/waiting_supply') || request('ref') == 'waiting_supply') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"></path></svg>
                        <span class="flex-1 ms-3 font-medium sidebar-text">Waiting Supply</span>
                        @if($countWaitingSupply > 0)
                            <span class="sidebar-badge bg-yellow-100 text-yellow-700 rounded-full">{{ $countWaitingSupply }}</span>
                        @endif
                    </a>
                </li>

                <li>
                    <a href="{{ route('requisitions.status', 'rejected') }}" class="sidebar-item neon-red flex items-center p-3 rounded-xl text-slate-600 {{ (request()->is('requisitions/status/rejected') || request('ref') == 'rejected') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="flex-1 ms-3 font-medium sidebar-text">Rejected</span>
                        @if($countRejected > 0)
                            <span class="sidebar-badge bg-red-100 text-red-700 rounded-full">{{ $countRejected }}</span>
                        @endif
                    </a>
                </li>

                <li>
                    <a href="{{ route('requisitions.status', 'completed') }}" class="sidebar-item neon-teal flex items-center p-3 rounded-xl text-slate-600 {{ (request()->is('requisitions/status/completed') || request('ref') == 'completed') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="ms-3 font-medium sidebar-text">Completed</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('requisitions.department') }}" class="sidebar-item neon-indigo flex items-center p-3 rounded-xl text-slate-600 {{ request()->routeIs('requisitions.department') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                        <span class="flex-1 ms-3 font-medium sidebar-text">Department Activity</span>
                    </a>
                </li>
                @endif

                <li class="pt-6 mt-2 mb-2 border-t border-slate-100"></li>

                <li>
                    <a href="{{ route('profile.edit') }}" class="sidebar-item neon-blue flex items-center p-3 rounded-xl text-slate-600 {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span class="flex-1 ms-3 font-medium sidebar-text">Settings</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <div id="main-content" class="p-4 sm:ml-64 mt-20 sidebar-transition">
        {{ $slot }}
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('main-sidebar');
            const mainContent = document.getElementById('main-content');
            const toggleBtn = document.getElementById('toggle-sidebar-btn');

            // Cek LocalStorage untuk status terakhir (Persistensi)
            const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';

            // Fungsi Set State Awal
            if (isCollapsed && window.innerWidth >= 640) {
                collapseSidebar();
            }

            // Event Listener Tombol
            toggleBtn.addEventListener('click', function() {
                if (window.innerWidth < 640) {
                    // Mobile: Toggle Translate (Buka Tutup Laci)
                    sidebar.classList.toggle('-translate-x-full');
                } else {
                    // Desktop: Toggle Width (Kecil Besar)
                    if (sidebar.classList.contains('w-64')) {
                        collapseSidebar();
                        localStorage.setItem('sidebar-collapsed', 'true');
                    } else {
                        expandSidebar();
                        localStorage.setItem('sidebar-collapsed', 'false');
                    }
                }
            });

            // Fungsi Kecilkan Sidebar (Desktop Only)
            function collapseSidebar() {
                sidebar.classList.remove('w-64');
                sidebar.classList.add('w-20', 'sidebar-collapsed'); // w-20 = 5rem
                mainContent.classList.remove('sm:ml-64');
                mainContent.classList.add('sm:ml-20');
            }

            // Fungsi Besarkan Sidebar
            function expandSidebar() {
                sidebar.classList.remove('w-20', 'sidebar-collapsed');
                sidebar.classList.add('w-64');
                mainContent.classList.remove('sm:ml-20');
                mainContent.classList.add('sm:ml-64');
            }

            // Sort Table Helper (Biarkan Saja)
            window.sortTable = function(n, tableId = null) {
                // ... (Logic sort table lama Anda) ...
            }
        });
    </script>
</body>
</html> --}}

{{-- <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'RL Tracker') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-gray-900 font-sans antialiased text-slate-600 dark:text-slate-300">

@php
    $user = Auth::user();
    $userPos = $user->position->position_name ?? '';

    // --- [FIX] PERLUAS DEFINISI APPROVER ---
    // Tambahkan semua variasi jabatan Pimpinan di sini
    $approverRoles = [
        'Manager',
        'Director',
        'Managing Director',
        'Deputy Managing Director',
        'General Manager',
        'President Director'
    ];

    $isSuperAdmin = $userPos === 'Super Admin';
    $isApprover = in_array($userPos, $approverRoles);

    $isRequester = !$isSuperAdmin && !$isApprover;
    $currentMode = session('active_role');

    // Menu Logic
    $showRequesterMenu = !$isSuperAdmin && ($isRequester || $currentMode == 'requester');
    // Super Admin & Approver melihat menu monitoring
    $showMonitoringMenu = $isSuperAdmin || $isApprover;
@endphp

    <nav class="fixed top-0 z-50 w-full bg-white/90 dark:bg-gray-800/90 backdrop-blur-lg border-b border-gray-200 dark:border-gray-700 shadow-sm transition-all duration-300">
        <div class="absolute bottom-0 left-0 w-full h-[2px] bg-gradient-to-r from-transparent via-blue-500 to-transparent opacity-50"></div>

        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">

                <div class="flex items-center justify-start rtl:justify-end">
                    <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"><path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path></svg>
                    </button>
                    <a href="{{ route('dashboard') }}" class="flex ms-2 md:me-24 group items-center">
                        <div class="bg-blue-600 text-white p-1.5 rounded-lg mr-2.5 shadow-lg shadow-blue-500/40 group-hover:rotate-6 transition-transform duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="self-center text-xl font-bold whitespace-nowrap dark:text-white tracking-tight">
                            <span class="text-blue-700 dark:text-blue-400">RL</span> Monitoring
                        </span>
                    </a>
                </div>

                <div class="flex items-center">
                    <div class="flex items-center ms-3">
                        <div class="hidden md:flex flex-col items-end mr-4">
                            <span class="text-sm font-bold text-gray-800 dark:text-white">{{ Auth::user()->full_name }}</span>
                            <span class="text-[10px] uppercase font-bold tracking-wider text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full">{{ Auth::user()->position->position_name ?? 'Staff' }}</span>
                        </div>

                        <button type="button" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-600 transition shadow-md border-2 border-white dark:border-gray-600" aria-expanded="false" data-dropdown-toggle="dropdown-user">
                            <span class="sr-only">Open user menu</span>
                            @if(Auth::user()->profile_photo_path)
                                <img class="w-9 h-9 rounded-full object-cover" src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" alt="User Photo">
                            @else
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg">{{ substr(Auth::user()->full_name, 0, 1) }}</div>
                            @endif
                        </button>

                        <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-xl shadow-xl border border-gray-100 dark:bg-gray-700 dark:divide-gray-600 dark:border-gray-600 w-56 animate-fade-in-up" id="dropdown-user">
                            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 rounded-t-xl">
                                <p class="text-sm text-gray-900 dark:text-white font-bold">{{ Auth::user()->full_name }}</p>
                                <p class="text-xs font-medium text-gray-500 truncate dark:text-gray-300">{{ Auth::user()->email_work }}</p>
                            </div>
                            <ul class="py-2" role="none">
                                <li>
                                    <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white transition">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        Account Settings
                                    </a>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <a href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-gray-600 dark:hover:text-white transition">
                                            <svg class="w-4 h-4 mr-2 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                            Sign out
                                        </a>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-24 transition-transform -translate-x-full bg-white border-r border-gray-100 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700 shadow-[4px_0_24px_rgba(0,0,0,0.02)]" aria-label="Sidebar">
        <div class="h-full px-4 pb-4 overflow-y-auto bg-white dark:bg-gray-800">

            <ul class="space-y-1.5 font-medium">

                <li>
                    <a href="{{ route('dashboard') }}" class="flex items-center p-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-md shadow-blue-500/30' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white' }}">
                        <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-blue-500 group-hover:text-blue-600' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 21"><path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z"/><path d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z"/></svg>
                        <span class="ms-3 font-semibold">Dashboard</span>
                    </a>
                </li>

                @if($isSuperAdmin)
                    <li class="pt-6 mt-2 mb-2 space-y-2 font-medium border-t border-gray-100 dark:border-gray-700">
                        <div class="px-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest dark:text-gray-500">System Management</div>
                    </li>
                    <li>
                        <a href="{{ route('admin.master-items.index') }}" class="flex items-center p-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.master-items.*') ? 'bg-gradient-to-r from-cyan-600 to-cyan-500 text-white shadow-md shadow-cyan-500/30' : 'text-gray-600 hover:bg-cyan-50 hover:text-cyan-600 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white' }}">
                            <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('admin.master-items.*') ? 'text-white' : 'text-cyan-500 group-hover:text-cyan-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            <span class="ms-3 font-medium">Master Items</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.users.index') }}" class="flex items-center p-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.users.*') ? 'bg-gradient-to-r from-purple-600 to-purple-500 text-white shadow-md shadow-purple-500/30' : 'text-gray-600 hover:bg-purple-50 hover:text-purple-600 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white' }}">
                            <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-purple-500 group-hover:text-purple-600' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                            <span class="ms-3 font-medium">Manage Users</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.monitoring.index') }}" class="flex items-center p-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.monitoring.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-500 text-white shadow-md shadow-emerald-500/30' : 'text-gray-600 hover:bg-emerald-50 hover:text-emerald-600 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white' }}">
                            <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('admin.monitoring.*') ? 'text-white' : 'text-emerald-500 group-hover:text-emerald-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            <span class="ms-3 font-medium">Global Monitoring</span>
                        </a>
                    </li>
                @endif

                @if($showRequesterMenu)
                    <li class="pt-6 mt-2 mb-2 space-y-2 font-medium border-t border-gray-100 dark:border-gray-700">
                        <div class="px-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest dark:text-gray-500">My Requests</div>
                    </li>
                    <li>
                        <a href="{{ route('requisitions.create') }}" class="flex items-center p-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('requisitions.create') ? 'bg-gradient-to-r from-rose-600 to-rose-500 text-white shadow-md shadow-rose-500/30' : 'text-gray-600 hover:bg-rose-50 hover:text-rose-600 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white' }}">
                            <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('requisitions.create') ? 'text-white' : 'text-rose-500 group-hover:text-rose-600' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M5 5V.13a2.96 2.96 0 0 0-1.293.749L.879 3.707A2.96 2.96 0 0 0 .13 5H5Z"/><path d="M6.737 11.061a2.961 2.961 0 0 1 .81-1.515l6.117-6.116A4.839 4.839 0 0 1 16 2.141V2a1.97 1.97 0 0 0-1.933-2H7v5a2 2 0 0 1-2 2H0v11a1.969 1.969 0 0 0 1.933 2h12.134A1.97 1.97 0 0 0 16 18v-3.093l-1.546 1.546c-.413.413-.94.695-1.513.81l-3.4.679a2.947 2.947 0 0 1-1.85-.227 2.96 2.96 0 0 1-1.635-3.257l.681-3.397Z"/><path d="M8.961 16a.93.93 0 0 0 .189-.019l3.4-.679a.961.961 0 0 0 .53-.271l6.119-6.117a2.937 2.937 0 0 0-4.152-4.152l-6.117 6.119a.96.96 0 0 0-.271.53l-.679 3.4a.939.939 0 0 0 1.28 1.28l-.3.303Z"/></svg>
                            <span class="ms-3 font-medium">Create New RL</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('requisitions.status', 'draft') }}" class="flex items-center p-3 rounded-xl transition-all duration-200 group {{ request()->is('requisitions/status/draft') ? 'bg-gradient-to-r from-slate-600 to-slate-500 text-white shadow-md shadow-slate-500/30' : 'text-gray-600 hover:bg-slate-100 hover:text-slate-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white' }}">
                            <svg class="w-5 h-5 transition duration-75 {{ request()->is('requisitions/status/draft') ? 'text-white' : 'text-slate-500 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path></svg>
                            <span class="flex-1 ms-3 font-medium whitespace-nowrap">Drafts</span>
                            @if(isset($countDraft) && $countDraft > 0)
                                <span class="inline-flex items-center justify-center px-2 py-0.5 ms-3 text-xs font-bold text-slate-800 bg-slate-200 rounded-full group-hover:bg-white group-hover:text-slate-600">{{ $countDraft }}</span>
                            @endif
                        </a>
                    </li>
                @endif

                @if($showMonitoringMenu)
                <li class="pt-6 mt-2 mb-2 space-y-2 font-medium border-t border-gray-100 dark:border-gray-700">
                    <div class="px-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest dark:text-gray-500">Live Monitoring</div>
                </li>

                <li>
                    <a href="{{ route('requisitions.status', 'on_progress') }}"
                    class="flex items-center p-3 rounded-xl transition-all duration-200 group {{ (request()->is('requisitions/status/on_progress') || request('ref') == 'on_progress') ? 'bg-gradient-to-r from-orange-500 to-orange-400 text-white shadow-md shadow-orange-500/30' : 'text-gray-600 hover:bg-orange-50 hover:text-orange-600 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white' }}">
                        <svg class="w-5 h-5 transition duration-75 {{ (request()->is('requisitions/status/on_progress') || request('ref') == 'on_progress') ? 'text-white' : 'text-orange-500 group-hover:text-orange-600' }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="flex-1 ms-3 font-medium whitespace-nowrap">Waiting Approval</span>
                    @if(isset($countPendingApprove) && $countPendingApprove > 0)
                        <span class="inline-flex items-center justify-center px-2 py-0.5 ms-3 text-xs font-bold text-orange-800 bg-orange-100 rounded-full group-hover:bg-white group-hover:text-orange-600">
                            {{ $countPendingApprove }}
                        </span>
                    @endif
                    </a>
                </li>

                <li>
                    <a href="{{ route('requisitions.status', 'partially_approved') }}"
                    class="flex items-center p-3 rounded-xl transition-all duration-200 group {{ (request()->is('requisitions/status/partially_approved') || request('ref') == 'partially_approved') ? 'bg-gradient-to-r from-purple-600 to-purple-500 text-white shadow-md shadow-purple-500/30' : 'text-gray-600 hover:bg-purple-50 hover:text-purple-600 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white' }}">
                        <svg class="w-5 h-5 transition duration-75 {{ (request()->is('requisitions/status/partially_approved') || request('ref') == 'partially_approved') ? 'text-white' : 'text-purple-500 group-hover:text-purple-600' }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"></path></svg>
                        <span class="flex-1 ms-3 font-medium whitespace-nowrap">Waiting Director</span>
                        @if($countWaitingDirector > 0)
                            <span class="inline-flex items-center justify-center px-2 py-0.5 ms-3 text-xs font-bold text-purple-800 bg-purple-100 rounded-full group-hover:bg-white group-hover:text-purple-600">{{ $countWaitingDirector }}</span>
                        @endif
                    </a>
                </li>

                <li>
                    <a href="{{ route('requisitions.status', 'approved') }}"
                    class="flex items-center p-3 rounded-xl transition-all duration-200 group {{ (request()->is('requisitions/status/approved') || request('ref') == 'approved') ? 'bg-gradient-to-r from-green-600 to-green-500 text-white shadow-md shadow-green-500/30' : 'text-gray-600 hover:bg-green-50 hover:text-green-600 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white' }}">
                        <svg class="w-5 h-5 transition duration-75 {{ (request()->is('requisitions/status/approved') || request('ref') == 'approved') ? 'text-white' : 'text-green-500 group-hover:text-green-600' }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="ms-3 font-medium">Final Approved</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('requisitions.status', 'waiting_supply') }}"
                    class="flex items-center p-3 rounded-xl transition-all duration-200 group {{ (request()->is('requisitions/status/waiting_supply') || request('ref') == 'waiting_supply') ? 'bg-gradient-to-r from-yellow-500 to-yellow-400 text-white shadow-md shadow-yellow-500/30' : 'text-gray-600 hover:bg-yellow-50 hover:text-yellow-600 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white' }}">
                        <svg class="w-5 h-5 transition duration-75 {{ (request()->is('requisitions/status/waiting_supply') || request('ref') == 'waiting_supply') ? 'text-white' : 'text-yellow-500 group-hover:text-yellow-600' }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"></path></svg>
                        <span class="flex-1 ms-3 font-medium whitespace-nowrap">Waiting Supply</span>
                        @if($countWaitingSupply > 0)
                            <span class="inline-flex items-center justify-center px-2 py-0.5 ms-3 text-xs font-bold text-yellow-800 bg-yellow-100 rounded-full group-hover:bg-white group-hover:text-yellow-700">{{ $countWaitingSupply }}</span>
                        @endif
                    </a>
                </li>

                <li>
                    <a href="{{ route('requisitions.status', 'rejected') }}"
                    class="flex items-center p-3 rounded-xl transition-all duration-200 group {{ (request()->is('requisitions/status/rejected') || request('ref') == 'rejected') ? 'bg-gradient-to-r from-red-600 to-red-500 text-white shadow-md shadow-red-500/30' : 'text-gray-600 hover:bg-red-50 hover:text-red-600 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white' }}">
                        <svg class="w-5 h-5 transition duration-75 {{ (request()->is('requisitions/status/rejected') || request('ref') == 'rejected') ? 'text-white' : 'text-red-500 group-hover:text-red-600' }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="flex-1 ms-3 font-medium whitespace-nowrap">Rejected</span>
                        @if($countRejected > 0)
                            <span class="inline-flex items-center justify-center px-2 py-0.5 ms-3 text-xs font-bold text-red-800 bg-red-100 rounded-full group-hover:bg-white group-hover:text-red-600">{{ $countRejected }}</span>
                        @endif
                    </a>
                </li>

                <li>
                    <a href="{{ route('requisitions.status', 'completed') }}"
                    class="flex items-center p-3 rounded-xl transition-all duration-200 group {{ (request()->is('requisitions/status/completed') || request('ref') == 'completed') ? 'bg-gradient-to-r from-teal-600 to-teal-500 text-white shadow-md shadow-teal-500/30' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-600 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white' }}">
                        <svg class="w-5 h-5 transition duration-75 {{ (request()->is('requisitions/status/completed') || request('ref') == 'completed') ? 'text-white' : 'text-teal-500 group-hover:text-teal-600' }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="ms-3 font-medium">Completed</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('requisitions.department') }}" class="flex items-center p-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('requisitions.department') ? 'bg-gradient-to-r from-indigo-600 to-indigo-500 text-white shadow-md shadow-indigo-500/30' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white' }}">
                        <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('requisitions.department') ? 'text-white' : 'text-indigo-500 group-hover:text-indigo-600' }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                        <span class="flex-1 ms-3 font-medium whitespace-nowrap">Department Activity</span>
                    </a>
                </li>
                @endif

                <li class="pt-6 mt-2 mb-2 space-y-2 font-medium border-t border-gray-100 dark:border-gray-700"></li>

                <li>
                    <a href="{{ route('profile.edit') }}" class="flex items-center p-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('profile.edit') ? 'bg-gradient-to-r from-gray-600 to-gray-500 text-white shadow-md shadow-gray-500/30' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white' }}">
                        <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('profile.edit') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span class="flex-1 ms-3 font-medium whitespace-nowrap">Settings</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <div class="p-4 sm:ml-64 mt-20">
        {{ $slot }}
    </div>
    <script>
        function sortTable(n, tableId = null) {
            var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
            table = tableId ? document.getElementById(tableId) : document.querySelector("table");
            if (!table) return;

            switching = true;
            dir = "asc";

            table.querySelectorAll('th span.sort-icon').forEach(icon => {
                icon.innerHTML = `<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>`;
                icon.parentElement.classList.remove('text-blue-600', 'dark:text-blue-400');
            });

            while (switching) {
                switching = false;
                rows = table.rows;

                for (i = 1; i < (rows.length - 1); i++) {
                    shouldSwitch = false;
                    x = rows[i].getElementsByTagName("TD")[n];
                    y = rows[i + 1].getElementsByTagName("TD")[n];

                    if (!x || !y) continue;

                    if (dir == "asc") {
                        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                            shouldSwitch = true;
                            break;
                        }
                    } else if (dir == "desc") {
                        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                            shouldSwitch = true;
                            break;
                        }
                    }
                }

                if (shouldSwitch) {
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                    switchcount++;
                } else {
                    if (switchcount == 0 && dir == "asc") {
                        dir = "desc";
                        switching = true;
                    }
                }
            }

            const clickedTh = table.rows[0].getElementsByTagName("TH")[n];
            const iconSpan = clickedTh.querySelector('.sort-icon');
            if(iconSpan) {
                clickedTh.classList.add('text-blue-600', 'dark:text-blue-400');
                if (dir == "asc") {
                    iconSpan.innerHTML = `<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>`;
                } else {
                    iconSpan.innerHTML = `<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>`;
                }
            }
        }
    </script>
</body>
</html> --}}
