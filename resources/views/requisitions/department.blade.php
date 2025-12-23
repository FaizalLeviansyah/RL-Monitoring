<x-app-layout>
    <div class="pt-2 pb-8 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-8 animate-fade-in-down">
                <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">
                    Department Activities
                </h2>
                <p class="text-slate-500 mt-2 text-lg font-light">
                    Monitoring seluruh aktivitas pengajuan dari <span class="font-bold text-indigo-600">{{ Auth::user()->department->department_name ?? 'My Department' }}</span>.
                </p>
            </div>

            <div class="bg-white/80 backdrop-blur-xl border border-slate-100 rounded-3xl p-4 shadow-sm mb-8 animate-fade-in-up">
                <form method="GET" action="{{ route('requisitions.department') }}" class="flex flex-col md:flex-row gap-4">

                    <div class="relative flex-grow group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="block w-full pl-11 pr-4 py-3 bg-slate-50 border-0 text-slate-700 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all placeholder-slate-400 font-medium"
                            placeholder="Search by RL Number, Subject...">
                    </div>

                    <div class="flex gap-2">
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-slate-400 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <input type="date" name="start_date" value="{{ request('start_date') }}"
                                class="pl-10 pr-4 py-3 bg-slate-50 border-0 text-slate-600 rounded-2xl focus:ring-2 focus:ring-indigo-500 text-sm font-bold cursor-pointer hover:bg-white transition-all">
                        </div>
                        <span class="self-center text-slate-400 font-medium">to</span>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-slate-400 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <input type="date" name="end_date" value="{{ request('end_date') }}"
                                class="pl-10 pr-4 py-3 bg-slate-50 border-0 text-slate-600 rounded-2xl focus:ring-2 focus:ring-indigo-500 text-sm font-bold cursor-pointer hover:bg-white transition-all">
                        </div>
                    </div>

                    <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-lg shadow-indigo-500/30 transition-all transform hover:-translate-y-0.5 active:scale-95 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filter
                    </button>
                </form>
            </div>

            <div class="bg-white/80 backdrop-blur-xl border border-slate-100 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden animate-fade-in-up delay-100 relative min-h-[400px]">

                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-400 via-purple-500 to-indigo-400"></div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left" id="deptTable">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-50/80 border-b border-slate-100">
                            <tr>
                                <th onclick="sortTable(0, 'deptTable')" class="px-6 py-5 font-bold tracking-wider cursor-pointer hover:text-indigo-600 transition group">
                                    RL Number <span class="sort-icon inline-block ml-1 opacity-50 group-hover:opacity-100">⇅</span>
                                </th>
                                <th onclick="sortTable(1, 'deptTable')" class="px-6 py-5 font-bold tracking-wider cursor-pointer hover:text-indigo-600 transition group">
                                    Date <span class="sort-icon inline-block ml-1 opacity-50 group-hover:opacity-100">⇅</span>
                                </th>
                                <th onclick="sortTable(2, 'deptTable')" class="px-6 py-5 font-bold tracking-wider cursor-pointer hover:text-indigo-600 transition group">
                                    Requester <span class="sort-icon inline-block ml-1 opacity-50 group-hover:opacity-100">⇅</span>
                                </th>
                                <th class="px-6 py-5 font-bold tracking-wider">Subject</th>
                                <th class="px-6 py-5 font-bold tracking-wider">Status</th>
                                <th class="px-6 py-5 font-bold tracking-wider text-center">Items</th>
                                <th class="px-6 py-5 font-bold tracking-wider text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white/50">
                            @forelse($requisitions as $rl)
                            <tr class="hover:bg-indigo-50/40 transition duration-200 group">
                                <td class="px-6 py-5">
                                    <span class="font-bold text-slate-700 group-hover:text-indigo-600 transition-colors">{{ $rl->rl_no }}</span>
                                </td>

                                <td class="px-6 py-5">
                                    <div class="flex items-center text-slate-500">
                                        <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span class="font-medium">{{ \Carbon\Carbon::parse($rl->request_date)->format('d M Y') }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-5">
                                    <div>
                                        <p class="font-bold text-slate-700">{{ $rl->requester->full_name }}</p>
                                        <p class="text-[10px] text-slate-400 uppercase tracking-wide mt-0.5">
                                            {{ $rl->requester->department->department_name ?? 'N/A' }}
                                        </p>
                                    </div>
                                </td>

                                <td class="px-6 py-5">
                                    <span class="text-slate-600 italic">"{{ Str::limit($rl->subject, 30) }}"</span>
                                </td>

                                <td class="px-6 py-5">
                                    @php
                                        $statusClass = match($rl->status_flow) {
                                            'ON_PROGRESS' => 'bg-orange-50 text-orange-600 border border-orange-200',
                                            'PARTIALLY_APPROVED' => 'bg-purple-50 text-purple-600 border border-purple-200',
                                            'APPROVED' => 'bg-green-50 text-green-600 border border-green-200',
                                            'WAITING_SUPPLY' => 'bg-yellow-50 text-yellow-600 border border-yellow-200',
                                            'REJECTED' => 'bg-red-50 text-red-600 border border-red-200',
                                            'COMPLETED' => 'bg-teal-50 text-teal-600 border border-teal-200',
                                            'DRAFT' => 'bg-slate-100 text-slate-600 border border-slate-200',
                                            default => 'bg-gray-50 text-gray-600'
                                        };
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wide {{ $statusClass }}">
                                        {{ str_replace('_', ' ', $rl->status_flow) }}
                                    </span>
                                </td>

                                <td class="px-6 py-5 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-600">
                                        {{ $rl->items->count() }} Items
                                    </span>
                                </td>

                                <td class="px-6 py-5 text-right">
                                    <a href="{{ route('requisitions.show', $rl->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md hover:shadow-blue-500/30 transition-all transform hover:-translate-y-0.5">
                                        View Details <span class="ml-1">&rarr;</span>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <p class="text-slate-500 font-medium">No department activities found.</p>
                                        <p class="text-slate-400 text-sm mt-1">Try adjusting the filters.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($requisitions->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $requisitions->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

{{-- <x-app-layout>
    <div class="max-w-7xl mx-auto p-4">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Daftar Dokumen:
                <span class="
                    {{ $statusUpper == 'APPROVED' ? 'text-green-600' : '' }}
                    {{ $statusUpper == 'REJECTED' ? 'text-red-600' : '' }}
                    {{ $statusUpper == 'ON_PROGRESS' ? 'text-orange-500' : '' }}
                    {{ $statusUpper == 'DRAFT' ? 'text-gray-500' : '' }}
                    {{ $statusUpper == 'ACTIVITIES' ? 'text-blue-600' : '' }}
                ">
                    @if($statusUpper == 'ACTIVITIES')
                        Department Activities
                    @else
                        {{ $statusUpper }}
                    @endif
                </span>
            </h1>
            <p class="text-gray-500 dark:text-gray-400">
                @if($statusUpper == 'ACTIVITIES')
                    Menampilkan seluruh aktivitas permintaan dari Departemen Anda.
                @else
                    Menampilkan seluruh data surat dengan status {{ strtolower($statusUpper) }}.
                @endif
            </p>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">No RL</th>
                        <th scope="col" class="px-6 py-3">Tanggal</th>
                        <th scope="col" class="px-6 py-3">Requester</th>
                        <th scope="col" class="px-6 py-3">Subject</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Items</th>
                        <th scope="col" class="px-6 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requisitions as $rl)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $rl->rl_no }}
                        </th>
                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::parse($rl->request_date)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900 dark:text-white">{{ $rl->requester->full_name ?? '-' }}</div>
                            <div class="text-xs">{{ $rl->requester->department->department_name ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            {{ str($rl->subject)->limit(30) }}
                        </td>
                        <td class="px-6 py-4">
                            @if($rl->status_flow == 'APPROVED')
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Approved</span>
                            @elseif($rl->status_flow == 'REJECTED')
                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Rejected</span>
                            @elseif($rl->status_flow == 'ON_PROGRESS')
                                <span class="bg-orange-100 text-orange-800 text-xs font-medium px-2.5 py-0.5 rounded">On Progress</span>
                            @else
                                <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">{{ $rl->status_flow }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            {{ $rl->items->count() }} Barang
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('requisitions.show', $rl->id) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                            Tidak ada aktivitas departemen ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4">
                {{ $requisitions->links() }}
            </div>
        </div>
    </div>
</x-app-layout> --}}
