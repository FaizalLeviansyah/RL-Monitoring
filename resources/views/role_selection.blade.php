<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Select Workspace - PT Amarin Group</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 dark:bg-gray-900 font-sans antialiased h-screen flex items-center justify-center">

    <div class="max-w-4xl w-full px-6 text-center">

        <div class="mb-10">
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-2">Selamat Datang, {{ Auth::user()->full_name }}</h1>
            <p class="text-gray-500 dark:text-gray-400">Pilih mode akses dashboard Anda hari ini.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            <a href="{{ route('dashboard.select_role', 'requester') }}" class="group relative p-8 bg-white border border-gray-200 rounded-2xl shadow-lg hover:shadow-2xl hover:border-blue-500 transition-all duration-300 dark:bg-gray-800 dark:border-gray-700 dark:hover:border-blue-500 cursor-pointer transform hover:-translate-y-2">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-24 h-24 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/></svg>
                </div>
                <div class="flex flex-col items-center">
                    <div class="p-4 bg-blue-100 rounded-full text-blue-600 mb-4 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">My Requests</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Masuk sebagai <strong>Requester</strong>. Buat surat permintaan baru atau pantau status surat saya.
                    </p>
                    <span class="mt-6 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        Masuk Workspace
                    </span>
                </div>
            </a>

            <a href="{{ route('dashboard.select_role', 'approver') }}" class="group relative p-8 bg-white border border-gray-200 rounded-2xl shadow-lg hover:shadow-2xl hover:border-green-500 transition-all duration-300 dark:bg-gray-800 dark:border-gray-700 dark:hover:border-green-500 cursor-pointer transform hover:-translate-y-2">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-24 h-24 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                </div>
                <div class="flex flex-col items-center">
                    <div class="p-4 bg-green-100 rounded-full text-green-600 mb-4 group-hover:bg-green-600 group-hover:text-white transition-colors">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">My Approvals</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Masuk sebagai <strong>Approver</strong>. Tinjau dokumen masuk dan berikan persetujuan.
                    </p>
                    <span class="mt-6 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg group-hover:bg-green-600 group-hover:text-white transition-colors">
                        Masuk Workspace
                    </span>
                </div>
            </a>

        </div>

        <div class="mt-12 text-sm text-gray-400">
            &copy; {{ date('Y') }} PT Amarin Group. Anda login sebagai {{ Auth::user()->position->position_name }}.
        </div>
    </div>
</body>
</html>
