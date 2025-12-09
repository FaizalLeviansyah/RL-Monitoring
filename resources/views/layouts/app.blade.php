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
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans antialiased">

    <nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start rtl:justify-end">
                    <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"><path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path></svg>
                    </button>
                    <a href="{{ route('dashboard') }}" class="flex ms-2 md:me-24">
                        <div class="bg-blue-600 text-white p-1 rounded mr-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="self-center text-xl font-bold sm:text-2xl whitespace-nowrap dark:text-white">RL System</span>
                    </a>
                </div>

                <div class="flex items-center">
                    <div class="flex items-center ms-3">
                        <button type="button" class="flex items-center text-sm bg-transparent rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600" aria-expanded="false" data-dropdown-toggle="dropdown-user">
                            <span class="sr-only">Open user menu</span>
                            <div class="hidden md:flex flex-col items-end mr-3">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ Auth::user()->full_name }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->position->position_name ?? 'Staff' }}</span>
                            </div>
                            @if(Auth::user()->profile_photo_path)
                                <img class="w-9 h-9 rounded-full object-cover shadow-md border-2 border-white dark:border-gray-700" src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" alt="User Photo">
                            @else
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white font-bold shadow-md border-2 border-white dark:border-gray-700">{{ substr(Auth::user()->full_name, 0, 1) }}</div>
                            @endif
                        </button>
                        <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow dark:bg-gray-700 dark:divide-gray-600" id="dropdown-user">
                            <div class="px-4 py-3" role="none">
                                <p class="text-sm text-gray-900 dark:text-white font-bold" role="none">{{ Auth::user()->full_name }}</p>
                                <p class="text-xs font-medium text-gray-500 truncate dark:text-gray-300" role="none">{{ Auth::user()->email_work }}</p>
                            </div>
                            <ul class="py-1" role="none">
                                <li><a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">Settings Akun</a></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">@csrf <a href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">Sign out</a></form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    @php
        $userPos = Auth::user()->position->position_name ?? '';
        $isSuperAdmin = $userPos === 'Super Admin';

        // Cek Jabatan Asli
        $isRealApprover = in_array($userPos, ['Manager', 'Director']);

        // Cek Mode yang sedang aktif (dari Session)
        $currentMode = session('active_role');

        // TENTUKAN MENU APA YANG MUNCUL
        // 1. Menu Requester (Create, Draft) muncul jika:
        //    - Dia Staff Biasa (Bukan Approver)
        //    - ATAU Dia Manager TAPI sedang mode 'requester'
        $showRequesterMenu = !$isSuperAdmin && (!$isRealApprover || $currentMode == 'requester');

        // 2. Menu Monitoring (Waiting, Approved) muncul jika:
        //    - Bukan Super Admin (karena super admin punya menu sendiri)
        //    - Defaultnya muncul untuk semua, tapi nanti Controller yang filter datanya
        $showMonitoringMenu = !$isSuperAdmin;
    @endphp

    <aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700 shadow-lg" aria-label="Sidebar">
        <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
            <ul class="space-y-2 font-medium">

                <li>
                    <a href="{{ route('dashboard') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-blue-50 dark:hover:bg-gray-700 group {{ request()->routeIs('dashboard') ? 'bg-blue-100 text-blue-600 dark:bg-gray-700' : '' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 21"><path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z"/><path d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z"/></svg>
                        <span class="ms-3">Dashboard</span>
                    </a>
                </li>

                @if($isSuperAdmin)
                    <li class="pt-4 mt-4 space-y-2 font-medium border-t border-gray-200 dark:border-gray-700">
                        <div class="px-2 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Management Console</div>
                    </li>
                    <li>
                        <a href="{{ route('admin.users.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-purple-50 dark:hover:bg-gray-700 group {{ request()->routeIs('admin.users.*') ? 'bg-purple-100 text-purple-600' : '' }}">
                            <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-purple-600" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                            <span class="ms-3">Manage Users</span>
                        </a>
                    </li>
                @endif

                @if($showRequesterMenu)
                    <li class="pt-4 mt-4 space-y-2 font-medium border-t border-gray-200 dark:border-gray-700">
                        <div class="px-2 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Actions</div>
                    </li>
                    <li>
                        <a href="{{ route('requisitions.create') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-blue-50 dark:hover:bg-gray-700 group {{ request()->routeIs('requisitions.create') ? 'bg-blue-100 text-blue-600' : '' }}">
                            <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M5 5V.13a2.96 2.96 0 0 0-1.293.749L.879 3.707A2.96 2.96 0 0 0 .13 5H5Z"/><path d="M6.737 11.061a2.961 2.961 0 0 1 .81-1.515l6.117-6.116A4.839 4.839 0 0 1 16 2.141V2a1.97 1.97 0 0 0-1.933-2H7v5a2 2 0 0 1-2 2H0v11a1.969 1.969 0 0 0 1.933 2h12.134A1.97 1.97 0 0 0 16 18v-3.093l-1.546 1.546c-.413.413-.94.695-1.513.81l-3.4.679a2.947 2.947 0 0 1-1.85-.227 2.96 2.96 0 0 1-1.635-3.257l.681-3.397Z"/><path d="M8.961 16a.93.93 0 0 0 .189-.019l3.4-.679a.961.961 0 0 0 .53-.271l6.119-6.117a2.937 2.937 0 0 0-4.152-4.152l-6.117 6.119a.96.96 0 0 0-.271.53l-.679 3.4a.939.939 0 0 0 1.28 1.28l-.3.303Z"/></svg>
                            <span class="ms-3">Create New RL</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('requisitions.status', 'draft') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 group {{ request()->is('requisitions/status/draft') ? 'bg-blue-100 text-blue-600' : '' }}">
                            <span class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"><svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path></svg></span>
                            <span class="ms-3">Drafts</span>
                        </a>
                    </li>
                @endif

                @if($showMonitoringMenu)
                <li class="pt-4 mt-4 space-y-2 font-medium border-t border-gray-200 dark:border-gray-700">
                    <div class="px-2 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Monitoring</div>
                </li>
                <li>
                    <a href="{{ route('requisitions.status', 'on_progress') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 group {{ request()->is('requisitions/status/on_progress') ? 'bg-orange-100 text-orange-600' : '' }}">
                        <span class="flex-shrink-0 w-5 h-5 text-orange-500 transition duration-75 group-hover:text-orange-600"><svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></span>
                        <span class="ms-3">Waiting Approval</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('requisitions.status', 'approved') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 group {{ request()->is('requisitions/status/approved') ? 'bg-green-100 text-green-600' : '' }}">
                        <span class="flex-shrink-0 w-5 h-5 text-green-500 transition duration-75 group-hover:text-green-600"><svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></span>
                        <span class="ms-3">Approved</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('requisitions.status', 'rejected') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 group {{ request()->is('requisitions/status/rejected') ? 'bg-red-100 text-red-600' : '' }}">
                        <span class="flex-shrink-0 w-5 h-5 text-red-500 transition duration-75 group-hover:text-red-600"><svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></span>
                        <span class="ms-3">Rejected</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('activities.department') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-blue-50 dark:hover:bg-gray-700 group {{ request()->routeIs('activities.department') ? 'bg-blue-100 text-blue-600' : '' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <span class="ms-3">Aktivitas Dept.</span>
                    </a>
                </li>
                @endif

                <li class="pt-4 mt-4 space-y-2 font-medium border-t border-gray-200 dark:border-gray-700"></li>
                <li>
                    <a href="{{ route('profile.edit') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Settings Akun</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <div class="p-4 sm:ml-64">
        <div class="p-4 mt-14">
            {{ $slot }}
        </div>
    </div>

</body>
</html>
