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

    // Cek Role
    $isSuperAdmin = $userPos === 'Super Admin';
    $isApprover = in_array($userPos, ['Manager', 'Director']);
    $isRequester = !$isSuperAdmin && !$isApprover;
    $currentMode = session('active_role');

    $showRequesterMenu = !$isSuperAdmin && ($isRequester || $currentMode == 'requester');
    $showMonitoringMenu = !$isSuperAdmin;

    // --- LOGIC COUNTER (Tetap Sama) ---
    $countDraft = 0; $countRejected = 0; $countPendingApprove = 0;
    $countWaitingDirector = 0; $countWaitingSupply = 0;

    if (!$isSuperAdmin) {
        $countDraft = \App\Models\RequisitionLetter::where('requester_id', $user->employee_id)->where('status_flow', 'DRAFT')->count();
        $countRejected = \App\Models\RequisitionLetter::where('requester_id', $user->employee_id)->where('status_flow', 'REJECTED')->count();

        if ($isApprover || $currentMode == 'approver') {
            $countPendingApprove = \App\Models\ApprovalQueue::where('approver_id', $user->employee_id)->where('status', 'PENDING')->count();
        }

        $queryWD = \App\Models\RequisitionLetter::where('status_flow', 'PARTIALLY_APPROVED');
        if ($isApprover) { $queryWD->where('company_id', $user->company_id); } else { $queryWD->where('requester_id', $user->employee_id); }
        $countWaitingDirector = $queryWD->count();

        $queryWS = \App\Models\RequisitionLetter::where('status_flow', 'WAITING_SUPPLY');
        if ($isApprover) { $queryWS->where('company_id', $user->company_id); } else { $queryWS->where('requester_id', $user->employee_id); }
        $countWaitingSupply = $queryWS->count();
    }
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
                            @if($countDraft > 0)
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
                    <a href="{{ route('requisitions.status', 'on_progress') }}" class="flex items-center p-3 rounded-xl transition-all duration-200 group {{ request()->is('requisitions/status/on_progress') ? 'bg-gradient-to-r from-orange-500 to-orange-400 text-white shadow-md shadow-orange-500/30' : 'text-gray-600 hover:bg-orange-50 hover:text-orange-600 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white' }}">
                        <svg class="w-5 h-5 transition duration-75 {{ request()->is('requisitions/status/on_progress') ? 'text-white' : 'text-orange-500 group-hover:text-orange-600' }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="flex-1 ms-3 font-medium whitespace-nowrap">Waiting Approval</span>
                        @if($countPendingApprove > 0)
                            <span class="inline-flex items-center justify-center px-2 py-0.5 ms-3 text-xs font-bold text-orange-800 bg-orange-100 rounded-full group-hover:bg-white group-hover:text-orange-600">{{ $countPendingApprove }}</span>
                        @endif
                    </a>
                </li>

                <li>
                    <a href="{{ route('requisitions.status', 'partially_approved') }}" class="flex items-center p-3 rounded-xl transition-all duration-200 group {{ request()->is('requisitions/status/partially_approved') ? 'bg-gradient-to-r from-purple-600 to-purple-500 text-white shadow-md shadow-purple-500/30' : 'text-gray-600 hover:bg-purple-50 hover:text-purple-600 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white' }}">
                        <svg class="w-5 h-5 transition duration-75 {{ request()->is('requisitions/status/partially_approved') ? 'text-white' : 'text-purple-500 group-hover:text-purple-600' }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"></path></svg>
                        <span class="flex-1 ms-3 font-medium whitespace-nowrap">Waiting Director</span>
                        @if($countWaitingDirector > 0)
                            <span class="inline-flex items-center justify-center px-2 py-0.5 ms-3 text-xs font-bold text-purple-800 bg-purple-100 rounded-full group-hover:bg-white group-hover:text-purple-600">{{ $countWaitingDirector }}</span>
                        @endif
                    </a>
                </li>

                <li>
                    <a href="{{ route('requisitions.status', 'approved') }}" class="flex items-center p-3 rounded-xl transition-all duration-200 group {{ request()->is('requisitions/status/approved') ? 'bg-gradient-to-r from-green-600 to-green-500 text-white shadow-md shadow-green-500/30' : 'text-gray-600 hover:bg-green-50 hover:text-green-600 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white' }}">
                        <svg class="w-5 h-5 transition duration-75 {{ request()->is('requisitions/status/approved') ? 'text-white' : 'text-green-500 group-hover:text-green-600' }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="ms-3 font-medium">Final Approved</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('requisitions.status', 'waiting_supply') }}" class="flex items-center p-3 rounded-xl transition-all duration-200 group {{ request()->is('requisitions/status/waiting_supply') ? 'bg-gradient-to-r from-yellow-500 to-yellow-400 text-white shadow-md shadow-yellow-500/30' : 'text-gray-600 hover:bg-yellow-50 hover:text-yellow-600 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white' }}">
                        <svg class="w-5 h-5 transition duration-75 {{ request()->is('requisitions/status/waiting_supply') ? 'text-white' : 'text-yellow-500 group-hover:text-yellow-600' }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"></path></svg>
                        <span class="flex-1 ms-3 font-medium whitespace-nowrap">Waiting Supply</span>
                        @if($countWaitingSupply > 0)
                            <span class="inline-flex items-center justify-center px-2 py-0.5 ms-3 text-xs font-bold text-yellow-800 bg-yellow-100 rounded-full group-hover:bg-white group-hover:text-yellow-700">{{ $countWaitingSupply }}</span>
                        @endif
                    </a>
                </li>

                <li>
                    <a href="{{ route('requisitions.status', 'rejected') }}" class="flex items-center p-3 rounded-xl transition-all duration-200 group {{ request()->is('requisitions/status/rejected') ? 'bg-gradient-to-r from-red-600 to-red-500 text-white shadow-md shadow-red-500/30' : 'text-gray-600 hover:bg-red-50 hover:text-red-600 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white' }}">
                        <svg class="w-5 h-5 transition duration-75 {{ request()->is('requisitions/status/rejected') ? 'text-white' : 'text-red-500 group-hover:text-red-600' }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="flex-1 ms-3 font-medium whitespace-nowrap">Rejected</span>
                        @if($countRejected > 0)
                            <span class="inline-flex items-center justify-center px-2 py-0.5 ms-3 text-xs font-bold text-red-800 bg-red-100 rounded-full group-hover:bg-white group-hover:text-red-600">{{ $countRejected }}</span>
                        @endif
                    </a>
                </li>

                <li>
                    <a href="{{ route('requisitions.status', 'completed') }}" class="flex items-center p-3 rounded-xl transition-all duration-200 group {{ request()->is('requisitions/status/completed') ? 'bg-gradient-to-r from-teal-600 to-teal-500 text-white shadow-md shadow-teal-500/30' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-600 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white' }}">
                        <svg class="w-5 h-5 transition duration-75 {{ request()->is('requisitions/status/completed') ? 'text-white' : 'text-teal-500 group-hover:text-teal-600' }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
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

</body>
</html>