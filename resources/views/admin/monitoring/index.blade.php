<x-app-layout>
    <div class="p-6 bg-slate-50 dark:bg-gray-900 min-h-screen">

        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-teal-600 to-blue-600 dark:from-teal-400 dark:to-blue-400">
                Command Center
            </h1>
            <p class="text-gray-500 mt-2">Live Global Monitoring of all Requisition Letters across companies.</p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 mb-6">
            <div class="flex flex-col lg:flex-row gap-4 justify-between items-center">
                <form class="w-full lg:w-96 relative" action="{{ route('admin.monitoring.index') }}" method="GET">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" class="block w-full pl-10 p-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-700 dark:text-white transition" placeholder="Search RL Number or Subject...">
                </form>

                <div class="flex w-full lg:w-auto gap-2">
                    <form id="filterForm" action="{{ route('admin.monitoring.index') }}" method="GET" class="flex flex-1 gap-2">
                        @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif

                        <select name="company_id" onchange="this.form.submit()" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-2.5 dark:bg-gray-700 dark:text-white cursor-pointer hover:bg-gray-100 transition">
                            <option value="">All Companies</option>
                            @foreach($companies as $comp)
                                <option value="{{ $comp->company_id }}" {{ request('company_id') == $comp->company_id ? 'selected' : '' }}>
                                    {{ $comp->company_code }}
                                </option>
                            @endforeach
                        </select>

                        <select name="status" onchange="this.form.submit()" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-2.5 dark:bg-gray-700 dark:text-white cursor-pointer hover:bg-gray-100 transition">
                            <option value="">All Status</option>
                            <option value="DRAFT" {{ request('status') == 'DRAFT' ? 'selected' : '' }}>Draft</option>
                            <option value="ON_PROGRESS" {{ request('status') == 'ON_PROGRESS' ? 'selected' : '' }}>Pending</option>
                            <option value="APPROVED" {{ request('status') == 'APPROVED' ? 'selected' : '' }}>Approved</option>
                            <option value="REJECTED" {{ request('status') == 'REJECTED' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl shadow-teal-500/10 border border-teal-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-teal-50/50 dark:bg-gray-700/50">
                        <tr>
                            <th class="p-4 text-xs font-bold text-left text-teal-800 dark:text-teal-400 uppercase tracking-wider cursor-pointer group" onclick="sortTable(0)">
                                <div class="flex items-center gap-1">Timestamp <span class="sort-icon"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg></span></div>
                            </th>
                            <th class="p-4 text-xs font-bold text-left text-teal-800 dark:text-teal-400 uppercase tracking-wider cursor-pointer group" onclick="sortTable(1)">
                                <div class="flex items-center gap-1">RL No <span class="sort-icon"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg></span></div>
                            </th>
                            <th class="p-4 text-xs font-bold text-left text-teal-800 dark:text-teal-400 uppercase tracking-wider cursor-pointer group" onclick="sortTable(2)">
                                <div class="flex items-center gap-1">Requester <span class="sort-icon"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg></span></div>
                            </th>
                            <th class="p-4 text-xs font-bold text-left text-teal-800 dark:text-teal-400 uppercase tracking-wider cursor-pointer group" onclick="sortTable(3)">
                                <div class="flex items-center gap-1">Entity <span class="sort-icon"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg></span></div>
                            </th>
                            <th class="p-4 text-xs font-bold text-left text-teal-800 dark:text-teal-400 uppercase tracking-wider cursor-pointer group" onclick="sortTable(4)">
                                <div class="flex items-center gap-1">Status <span class="sort-icon"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg></span></div>
                            </th>
                            <th class="p-4 text-xs font-bold text-left text-teal-800 dark:text-teal-400 uppercase tracking-wider">Audit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($activities as $rl)
                        <tr class="hover:bg-teal-50/30 dark:hover:bg-gray-700/30 transition">
                            <td class="p-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $rl->created_at->format('H:i') }}</div>
                                <div class="text-xs text-gray-500">{{ $rl->created_at->format('d M Y') }}</div>
                            </td>
                            <td class="p-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-teal-700 dark:text-teal-400">{{ $rl->rl_no }}</span>
                                    <span class="text-xs text-gray-600 dark:text-gray-400 truncate w-48" title="{{ $rl->subject }}">{{ $rl->subject }}</span>
                                </div>
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($rl->requester->profile_photo_path)
                                        <img class="w-8 h-8 rounded-full mr-3 border border-gray-200" src="{{ asset('storage/'.$rl->requester->profile_photo_path) }}">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center font-bold mr-3 text-xs border border-gray-200">
                                            {{ substr($rl->requester->full_name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $rl->requester->full_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $rl->requester->department->department_name ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                <span class="bg-white border border-gray-200 text-gray-700 text-xs font-bold px-2 py-1 rounded-md shadow-sm">
                                    {{ $rl->company->company_code }}
                                </span>
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                @php
                                    $statusColor = match($rl->status_flow) {
                                        'ON_PROGRESS' => 'bg-orange-100 text-orange-700 border-orange-200',
                                        'APPROVED' => 'bg-green-100 text-green-700 border-green-200',
                                        'REJECTED' => 'bg-red-100 text-red-700 border-red-200',
                                        'DRAFT' => 'bg-gray-100 text-gray-700 border-gray-200',
                                        default => 'bg-blue-100 text-blue-700 border-blue-200'
                                    };
                                @endphp
                                <span class="{{ $statusColor }} border text-xs font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wide">
                                    {{ str_replace('_', ' ', $rl->status_flow) }}
                                </span>
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                <a href="{{ route('requisitions.show', $rl->id) }}" target="_blank" class="text-teal-600 hover:text-teal-800 bg-teal-50 hover:bg-teal-100 px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center w-fit">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    View Log
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $activities->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
