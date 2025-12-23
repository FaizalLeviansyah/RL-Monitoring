<x-app-layout>
    <div class="pt-2 pb-8 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex flex-col md:flex-row justify-between items-end mb-8 animate-fade-in-down">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">
                            Document List
                        </h2>
                        @php
                            $titleColor = match($statusUpper) {
                                'ON_PROGRESS' => 'bg-orange-100 text-orange-700 border-orange-200',
                                'PARTIALLY_APPROVED' => 'bg-purple-100 text-purple-700 border-purple-200',
                                'APPROVED' => 'bg-green-100 text-green-700 border-green-200',
                                'WAITING_SUPPLY' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                'REJECTED' => 'bg-red-100 text-red-700 border-red-200',
                                'COMPLETED' => 'bg-teal-100 text-teal-700 border-teal-200',
                                'DRAFT' => 'bg-slate-100 text-slate-600 border-slate-200',
                                default => 'bg-blue-100 text-blue-700 border-blue-200'
                            };
                        @endphp
                        <span class="px-4 py-1 rounded-xl text-xs font-extrabold uppercase tracking-wider border {{ $titleColor }}">
                            {{ str_replace('_', ' ', $statusUpper) }}
                        </span>
                    </div>
                    <p class="text-slate-500 text-lg font-light">
                        Managing requisitions currently in <span class="font-bold text-slate-700">{{ str_replace('_', ' ', $statusUpper) }}</span> stage.
                    </p>
                </div>

                @if($statusUpper == 'DRAFT' || $statusUpper == 'REJECTED')
                <a href="{{ route('requisitions.create') }}" class="group relative inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white transition-all duration-200 bg-blue-600 font-pj rounded-2xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-500/30">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Create New RL
                </a>
                @endif
            </div>

            <div class="bg-white/80 backdrop-blur-xl border border-slate-100 rounded-[2rem] p-4 shadow-sm mb-8 animate-fade-in-up">
                <form method="GET" action="{{ route('requisitions.status', ['status' => strtolower($statusUpper)]) }}" class="flex flex-col md:flex-row gap-4">

                    <div class="relative flex-grow group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="block w-full pl-11 pr-4 py-3 bg-slate-50 border-0 text-slate-700 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all placeholder-slate-400 font-medium"
                            placeholder="Search by RL Number, Subject...">
                    </div>

                    <div class="flex gap-2">
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-slate-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <input type="date" name="start_date" value="{{ request('start_date') }}"
                                class="pl-10 pr-4 py-3 bg-slate-50 border-0 text-slate-600 rounded-2xl focus:ring-2 focus:ring-blue-500 text-sm font-bold cursor-pointer hover:bg-white transition-all">
                        </div>
                        <span class="self-center text-slate-400 font-medium">to</span>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-slate-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <input type="date" name="end_date" value="{{ request('end_date') }}"
                                class="pl-10 pr-4 py-3 bg-slate-50 border-0 text-slate-600 rounded-2xl focus:ring-2 focus:ring-blue-500 text-sm font-bold cursor-pointer hover:bg-white transition-all">
                        </div>
                    </div>

                    <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg shadow-blue-500/30 transition-all transform hover:-translate-y-0.5 active:scale-95 flex items-center justify-center">
                        Filter
                    </button>
                </form>
            </div>

            <div class="bg-white/80 backdrop-blur-xl border border-slate-100 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden animate-fade-in-up delay-100 relative min-h-[400px]">

                @php
                    $lineGradient = match($statusUpper) {
                        'ON_PROGRESS' => 'from-orange-400 via-red-400 to-orange-400',
                        'PARTIALLY_APPROVED' => 'from-purple-400 via-indigo-400 to-purple-400',
                        'APPROVED' => 'from-green-400 via-emerald-400 to-green-400',
                        'WAITING_SUPPLY' => 'from-yellow-400 via-amber-400 to-yellow-400',
                        'REJECTED' => 'from-red-500 via-pink-500 to-red-500',
                        'COMPLETED' => 'from-teal-400 via-cyan-400 to-teal-400',
                        default => 'from-blue-400 via-slate-400 to-blue-400'
                    };
                @endphp
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r {{ $lineGradient }}"></div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left" id="mainTable">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-50/80 border-b border-slate-100">
                            <tr>
                                <th onclick="sortTable(0, 'mainTable')" class="px-6 py-5 font-bold tracking-wider cursor-pointer hover:text-blue-600 transition group">
                                    RL Number <span class="sort-icon inline-block ml-1 opacity-50 group-hover:opacity-100">⇅</span>
                                </th>
                                <th onclick="sortTable(1, 'mainTable')" class="px-6 py-5 font-bold tracking-wider cursor-pointer hover:text-blue-600 transition group">
                                    Date <span class="sort-icon inline-block ml-1 opacity-50 group-hover:opacity-100">⇅</span>
                                </th>
                                <th onclick="sortTable(2, 'mainTable')" class="px-6 py-5 font-bold tracking-wider cursor-pointer hover:text-blue-600 transition group">
                                    Requester <span class="sort-icon inline-block ml-1 opacity-50 group-hover:opacity-100">⇅</span>
                                </th>
                                <th class="px-6 py-5 font-bold tracking-wider">Subject</th>
                                <th class="px-6 py-5 font-bold tracking-wider text-center">Items</th>
                                <th class="px-6 py-5 font-bold tracking-wider text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white/50">
                            @forelse($requisitions as $rl)
                            <tr class="hover:bg-blue-50/40 transition duration-200 group">
                                <td class="px-6 py-5">
                                    <span class="font-bold text-blue-600 group-hover:underline decoration-2 underline-offset-4">{{ $rl->rl_no }}</span>
                                </td>

                                <td class="px-6 py-5">
                                    <div class="flex items-center text-slate-500">
                                        <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span class="font-medium">{{ \Carbon\Carbon::parse($rl->request_date)->format('d M Y') }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-5">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center text-xs font-bold mr-3 border border-slate-200">
                                            {{ substr($rl->requester->full_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-700">{{ $rl->requester->full_name }}</p>
                                            <p class="text-[10px] text-slate-400 uppercase tracking-wide">
                                                {{ $rl->requester->department->department_name ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-5">
                                    <span class="text-slate-600 italic">"{{ Str::limit($rl->subject, 30) }}"</span>
                                </td>

                                <td class="px-6 py-5 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        {{ $rl->items ? $rl->items->count() : 0 }} Items
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
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4 shadow-inner">
                                            <svg class="w-10 h-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <h3 class="text-lg font-bold text-slate-700">No documents found</h3>
                                        <p class="text-slate-500 text-sm mt-1 max-w-xs mx-auto">
                                            There are no requisition letters with status <span class="font-bold text-slate-700">{{ str_replace('_', ' ', $statusUpper) }}</span> at the moment.
                                        </p>
                                        @if($statusUpper == 'DRAFT')
                                        <a href="{{ route('requisitions.create') }}" class="mt-6 text-blue-600 font-bold hover:underline">Create a new one?</a>
                                        @endif
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
