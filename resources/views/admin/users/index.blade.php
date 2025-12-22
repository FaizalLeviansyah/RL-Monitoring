<x-app-layout>
    <div class="p-6 bg-slate-50 dark:bg-gray-900 min-h-screen">

        <div class="mb-8">
            <nav class="flex mb-4 text-sm text-gray-500" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-blue-600 transition">Dashboard</a>
                    </li>
                    <li>
                        <span class="mx-2 text-gray-400">/</span>
                        <span class="font-medium text-gray-800 dark:text-gray-300">User Management</span>
                    </li>
                </ol>
            </nav>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">All Users</h1>
                <a href="{{ route('admin.users.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-purple-600 to-indigo-600 rounded-xl hover:from-purple-700 hover:to-indigo-700 shadow-lg shadow-purple-500/30 transition transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    Add New User
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 mb-6">
            <div class="flex flex-col md:flex-row gap-4 justify-between">
                <form class="flex-1 relative" action="{{ route('admin.users.index') }}" method="GET">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" class="block w-full pl-10 p-2.5 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition" placeholder="Search user name or email...">
                </form>
                <form action="{{ route('admin.users.index') }}" method="GET" class="w-full md:w-64">
                    <select name="company_id" onchange="this.form.submit()" class="block w-full p-2.5 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Filter by Company (All)</option>
                        @foreach($companies as $comp)
                            <option value="{{ $comp->company_id }}" {{ request('company_id') == $comp->company_id ? 'selected' : '' }}>
                                {{ $comp->company_code }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl shadow-gray-200/40 dark:shadow-none overflow-hidden border border-gray-100 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        @php
                            // Anda bisa memindahkan fungsi ini ke AppServiceProvider agar global,
                            // tapi untuk sekarang taruh disini agar aman tanpa ubah file core.
                            if (!function_exists('userSortLink')) {
                                function userSortLink($col, $label) {
                                    $currentSort = request('sort');
                                    $currentDir = request('dir', 'desc');
                                    $newDir = ($currentSort == $col && $currentDir == 'asc') ? 'desc' : 'asc';
                                    $active = $currentSort == $col;
                                    $icon = $active
                                        ? ($currentDir == 'asc'
                                            ? '<svg class="w-3 h-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>'
                                            : '<svg class="w-3 h-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>')
                                        : '<svg class="w-3 h-3 text-gray-400 opacity-50 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>';

                                    $url = request()->fullUrlWithQuery(['sort' => $col, 'dir' => $newDir]);
                                    return "<a href='{$url}' class='group flex items-center gap-1 cursor-pointer select-none hover:text-purple-600 transition'>{$label} {$icon}</a>";
                                }
                            }
                        @endphp

                        <th class="px-6 py-4 text-xs font-bold text-left text-gray-500 uppercase tracking-wider">
                            {!! userSortLink('name', 'User Profile') !!}
                        </th>
                        <th class="px-6 py-4 text-xs font-bold text-left text-gray-500 uppercase tracking-wider">
                            {!! userSortLink('company', 'Company') !!}
                        </th>
                        <th class="px-6 py-4 text-xs font-bold text-left text-gray-500 uppercase tracking-wider">
                            {!! userSortLink('department', 'Department') !!}
                        </th>
                        <th class="px-6 py-4 text-xs font-bold text-left text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-left text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($user->profile_photo_path)
                                            <img class="h-10 w-10 rounded-full object-cover border-2 border-white shadow-sm" src="{{ asset('storage/'.$user->profile_photo_path) }}" alt="">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-tr from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold shadow-sm">
                                                {{ substr($user->full_name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $user->full_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $user->email_work }}</div>
                                        <div class="text-xs text-blue-600 font-bold mt-0.5">{{ $user->department->department_name ?? '' }} {{ $user->position->position_name ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $cc = $user->company->company_code ?? '-';
                                    $badgeColor = match($cc) {
                                        'ASM' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'ACS' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'CTP' => 'bg-red-100 text-red-800 border-red-200',
                                        default => 'bg-gray-100 text-gray-800 border-gray-200'
                                    };
                                @endphp
                                <span class="{{ $badgeColor }} px-2.5 py-0.5 rounded-md text-xs font-bold border">
                                    {{ $cc }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                {{ $user->department->department_name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span> Active
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="{{ route('admin.users.edit', $user->employee_id) }}" class="text-blue-600 hover:text-blue-900 font-bold transition">Edit</a>
                                <span class="text-gray-300">|</span>
                                <form action="{{ route('admin.users.destroy', $user->employee_id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to deactivate this user? They will not be able to login.');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-bold transition">Deactivate</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
