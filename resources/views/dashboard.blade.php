<x-app-layout>
    <div class="py-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex flex-col md:flex-row justify-between items-end mb-8">
                <div>
                    <h2 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400 tracking-tight">
                        Dashboard
                    </h2>
                    <p class="text-sm text-gray-500 mt-2 font-medium">
                        Halo, <span class="text-gray-800 dark:text-gray-200 font-bold">{{ Auth::user()->full_name }}</span>.
                        Total Volume: <span class="font-bold text-blue-600">{{ $stats['total_all'] }} Dokumen</span>
                    </p>
                </div>

                @if($isApprover)
                <div class="mt-4 md:mt-0 p-1 bg-white dark:bg-gray-800 rounded-full shadow-md border border-gray-200 dark:border-gray-700 flex">
                    <a href="{{ route('dashboard.select_role', 'approver') }}"
                       class="px-6 py-2 rounded-full text-sm font-bold transition-all transform duration-200 {{ $currentMode == 'approver' ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg scale-105' : 'text-gray-500 hover:text-blue-600' }}">
                       Approver
                    </a>
                    <a href="{{ route('dashboard.select_role', 'requester') }}"
                       class="px-6 py-2 rounded-full text-sm font-bold transition-all transform duration-200 {{ $currentMode == 'requester' ? 'bg-gradient-to-r from-indigo-600 to-purple-500 text-white shadow-lg scale-105' : 'text-gray-500 hover:text-purple-600' }}">
                       Requester
                    </a>
                </div>
                @endif
            </div>

            @if($stats['my_actions'] > 0)
            <div class="mb-8 relative overflow-hidden rounded-2xl shadow-xl shadow-red-500/20 animate-fade-in-down">
                <div class="absolute inset-0 bg-gradient-to-r from-red-600 via-orange-500 to-red-500 animate-gradient-x"></div>
                <div class="relative bg-white/95 dark:bg-gray-800/95 m-[2px] rounded-2xl p-4 flex flex-col md:flex-row justify-between items-center backdrop-blur-sm">
                    <div class="flex items-center mb-3 md:mb-0">
                        <div class="p-3 bg-red-100 text-red-600 rounded-full mr-4 shadow-inner">
                            <svg class="w-6 h-6 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-gray-800 dark:text-white text-lg">Action Required!</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Ada <strong class="text-red-600 text-lg">{{ $stats['my_actions'] }} dokumen</strong> menunggu respon Anda.</p>
                        </div>
                    </div>
                    <a href="{{ $currentMode == 'approver' ? route('requisitions.status', 'on_progress') : route('requisitions.status', 'draft') }}"
                       class="bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg transform transition hover:scale-105 hover:-translate-y-1">
                        Proses Sekarang &rarr;
                    </a>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">

                <div class="relative overflow-hidden rounded-2xl shadow-lg shadow-orange-500/20 group hover:-translate-y-1 transition duration-300 bg-gradient-to-br from-orange-400 to-red-500">
                    <div class="absolute -right-4 -bottom-4 opacity-20 transform rotate-12 scale-150 group-hover:scale-125 transition duration-500">
                        <svg class="w-24 h-24 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                    </div>

                    <div class="relative p-5 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-orange-100 text-[10px] font-bold uppercase tracking-wider">Waiting Approval</p>
                                <h3 class="text-3xl font-extrabold mt-1">{{ $stats['waiting_approval'] }}</h3>
                            </div>
                            <div class="p-2 bg-white/20 backdrop-blur-sm rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                        <div class="mt-3 text-[10px] font-medium bg-white/20 w-fit px-2 py-0.5 rounded backdrop-blur-sm">Needs Manager Review</div>
                    </div>
                </div>

                <div class="relative overflow-hidden rounded-2xl shadow-lg shadow-purple-500/20 group hover:-translate-y-1 transition duration-300 bg-gradient-to-br from-purple-500 to-indigo-600">
                    <div class="absolute -right-4 -bottom-4 opacity-20 transform rotate-12 scale-150 group-hover:scale-125 transition duration-500">
                        <svg class="w-24 h-24 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                    </div>

                    <div class="relative p-5 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-purple-100 text-[10px] font-bold uppercase tracking-wider">Waiting Director</p>
                                <h3 class="text-3xl font-extrabold mt-1">{{ $stats['waiting_director'] }}</h3>
                            </div>
                            <div class="p-2 bg-white/20 backdrop-blur-sm rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                        </div>
                        <div class="mt-3 text-[10px] font-medium bg-white/20 w-fit px-2 py-0.5 rounded backdrop-blur-sm">Partially Approved</div>
                    </div>
                </div>

                <div class="relative overflow-hidden rounded-2xl shadow-lg shadow-yellow-500/20 group hover:-translate-y-1 transition duration-300 bg-gradient-to-br from-yellow-400 to-orange-500">
                    <div class="absolute -right-4 -bottom-4 opacity-20 transform -rotate-12 scale-150 group-hover:scale-125 transition duration-500">
                        <svg class="w-24 h-24 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z" /><path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd" /></svg>
                    </div>

                    <div class="relative p-5 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-yellow-100 text-[10px] font-bold uppercase tracking-wider">Waiting Supply</p>
                                <h3 class="text-3xl font-extrabold mt-1">{{ $stats['waiting_supply'] }}</h3>
                            </div>
                            <div class="p-2 bg-white/20 backdrop-blur-sm rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                            </div>
                        </div>
                        <div class="mt-3 text-[10px] font-medium bg-white/20 w-fit px-2 py-0.5 rounded backdrop-blur-sm">Procurement Process</div>
                    </div>
                </div>

                <div class="relative overflow-hidden rounded-2xl shadow-lg shadow-teal-500/20 group hover:-translate-y-1 transition duration-300 bg-gradient-to-br from-teal-400 to-emerald-600">
                    <div class="absolute -right-4 -bottom-4 opacity-20 transform rotate-12 scale-150 group-hover:scale-125 transition duration-500">
                        <svg class="w-24 h-24 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                    </div>

                    <div class="relative p-5 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-teal-100 text-[10px] font-bold uppercase tracking-wider">Completed</p>
                                <h3 class="text-3xl font-extrabold mt-1">{{ $stats['completed'] }}</h3>
                            </div>
                            <div class="p-2 bg-white/20 backdrop-blur-sm rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                        <div class="mt-3 text-[10px] font-medium bg-white/20 w-fit px-2 py-0.5 rounded backdrop-blur-sm">Successfully Done</div>
                    </div>
                </div>

                <div class="relative overflow-hidden rounded-2xl shadow-lg shadow-red-500/20 group hover:-translate-y-1 transition duration-300 bg-gradient-to-br from-red-500 to-pink-700">
                    <div class="absolute -right-4 -bottom-4 opacity-20 transform -rotate-12 scale-150 group-hover:scale-125 transition duration-500">
                        <svg class="w-24 h-24 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                    </div>

                    <div class="relative p-5 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-red-100 text-[10px] font-bold uppercase tracking-wider">Rejected</p>
                                <h3 class="text-3xl font-extrabold mt-1">{{ $stats['rejected'] }}</h3>
                            </div>
                            <div class="p-2 bg-white/20 backdrop-blur-sm rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                        <div class="mt-3 text-[10px] font-medium bg-white/20 w-fit px-2 py-0.5 rounded backdrop-blur-sm">Declined Request</div>
                    </div>
                </div>

            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">

                <div class="lg:col-span-2 space-y-8">

                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md border-t-4 border-blue-500">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="text-lg font-extrabold text-gray-800 dark:text-white flex items-center">
                                    <span class="text-2xl mr-2">📊</span> Analysis by Priority
                                </h3>
                                <p class="text-xs text-gray-500 mt-1">Klasifikasi urgensi berdasarkan sisa hari menuju tenggat waktu.</p>
                            </div>
                            <span class="text-xs font-bold bg-blue-100 text-blue-600 px-3 py-1 rounded-full">Live Data</span>
                        </div>
                        <div class="relative h-64 w-full">
                            <canvas id="priorityChart"></canvas>
                        </div>
                        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-2 text-center">
                            <div class="bg-red-50 p-2 rounded-lg border border-red-100">
                                <div class="text-xs font-bold text-red-600">Top Urgent</div>
                                <div class="text-[10px] text-gray-500">Deadline ≤ 2 Hari</div>
                                <div class="font-black text-lg text-red-700 mt-1">{{ $priorityStats['Top Urgent'] }}</div>
                            </div>
                            <div class="bg-orange-50 p-2 rounded-lg border border-orange-100">
                                <div class="text-xs font-bold text-orange-600">Urgent</div>
                                <div class="text-[10px] text-gray-500">Deadline ≤ 5 Hari</div>
                                <div class="font-black text-lg text-orange-700 mt-1">{{ $priorityStats['Urgent'] }}</div>
                            </div>
                            <div class="bg-blue-50 p-2 rounded-lg border border-blue-100">
                                <div class="text-xs font-bold text-blue-600">Normal</div>
                                <div class="text-[10px] text-gray-500">Deadline > 5 Hari</div>
                                <div class="font-black text-lg text-blue-700 mt-1">{{ $priorityStats['Normal'] }}</div>
                            </div>
                            <div class="bg-gray-100 p-2 rounded-lg border border-gray-200">
                                <div class="text-xs font-bold text-gray-700">Outstanding</div>
                                <div class="text-[10px] text-gray-500">Melewati Deadline</div>
                                <div class="font-black text-lg text-gray-800 mt-1">{{ $priorityStats['Outstanding'] }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md overflow-hidden border-t-4 border-indigo-500">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-indigo-50/30 dark:bg-gray-800 flex justify-between items-center">
                            <h3 class="text-lg font-extrabold text-gray-800 dark:text-white flex items-center">
                                <span class="text-2xl mr-2">🚀</span> Recent Activity
                            </h3>
                            <a href="#" class="text-sm font-bold text-indigo-600 hover:text-indigo-800">Lihat Semua</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs text-indigo-900 bg-indigo-50 dark:bg-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    <tr>
                                        <th class="px-6 py-4 font-bold">RL Number</th>
                                        <th class="px-6 py-4 font-bold">Subject</th>
                                        <th class="px-6 py-4 font-bold">Status</th>
                                        <th class="px-6 py-4 font-bold text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @forelse($recentActivities as $rl)
                                    <tr class="hover:bg-indigo-50/30 transition-colors group">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-800 dark:text-white group-hover:text-indigo-600 transition">{{ $rl->rl_no }}</div>
                                            <div class="text-[10px] font-bold text-gray-400 mt-0.5 uppercase">{{ $rl->created_at->diffForHumans() }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300 font-medium">{{ Str::limit($rl->subject, 35) }}</td>
                                        <td class="px-6 py-4">
                                            @php
                                                $badges = [
                                                    'DRAFT' => 'bg-gray-100 text-gray-600',
                                                    'ON_PROGRESS' => 'bg-gradient-to-r from-orange-100 to-orange-200 text-orange-700',
                                                    'PARTIALLY_APPROVED' => 'bg-gradient-to-r from-purple-100 to-purple-200 text-purple-700',
                                                    'WAITING_SUPPLY' => 'bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-700',
                                                    'COMPLETED' => 'bg-gradient-to-r from-teal-100 to-teal-200 text-teal-700',
                                                    'REJECTED' => 'bg-gradient-to-r from-red-100 to-red-200 text-red-700',
                                                ];
                                            @endphp
                                            <span class="{{ $badges[$rl->status_flow] ?? 'bg-gray-100' }} px-3 py-1 rounded-full text-xs font-bold inline-block shadow-sm">
                                                {{ str_replace('_', ' ', $rl->status_flow) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('requisitions.show', $rl->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="4" class="text-center py-8 text-gray-400 italic font-medium">Belum ada aktivitas.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="space-y-8">

                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md border-t-4 border-red-500">
                        <h3 class="text-lg font-extrabold text-gray-800 dark:text-white mb-6 flex items-center">
                            <span class="text-2xl mr-2">📅</span> Deadlines
                        </h3>
                        <div class="space-y-4">
                            @forelse($upcomingDeadlines as $rl)
                            <div class="flex items-start p-3 rounded-xl bg-gray-50 hover:bg-white hover:shadow-md transition-all border border-transparent hover:border-gray-100">
                                <div class="flex-shrink-0 w-14 text-center bg-gradient-to-b from-red-500 to-red-600 text-white rounded-lg py-2 mr-3 shadow-lg shadow-red-500/30">
                                    <span class="block text-lg font-bold leading-none">{{ \Carbon\Carbon::parse($rl->required_date)->format('d') }}</span>
                                    <span class="block text-[10px] uppercase font-bold text-red-100">{{ \Carbon\Carbon::parse($rl->required_date)->format('M') }}</span>
                                </div>
                                <div class="flex-1 min-w-0 pt-1">
                                    <p class="text-sm font-bold text-gray-900 truncate dark:text-white">
                                        {{ $rl->rl_no }}
                                    </p>
                                    <p class="text-xs text-gray-500 truncate mb-1">
                                        {{ $rl->requester->full_name ?? '-' }}
                                    </p>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-white border border-gray-200 text-gray-600 shadow-sm">
                                        {{ str_replace('_', ' ', $rl->status_flow) }}
                                    </span>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-6 text-gray-400 text-sm font-medium">Tidak ada deadline mendesak.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md border-t-4 border-teal-500">
                        <h3 class="text-lg font-extrabold text-gray-800 dark:text-white mb-6 flex items-center">
                            <span class="text-2xl mr-2">🍩</span> Composition
                        </h3>

                        <div class="relative h-48 w-full mb-6">
                            <canvas id="statusChart"></canvas>
                        </div>

                        @php
                            $total = $stats['total_all'] > 0 ? $stats['total_all'] : 1;
                            $pctCompleted = round(($stats['completed'] / $total) * 100);
                            $pctProcess = round((($stats['waiting_approval'] + $stats['waiting_director'] + $stats['waiting_supply']) / $total) * 100);
                            $pctRejected = round(($stats['rejected'] / $total) * 100);
                        @endphp

                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between items-center text-xs font-bold mb-1">
                                    <span class="text-teal-700">Completed ({{ $pctCompleted }}%)</span>
                                    <span class="text-gray-500">{{ $stats['completed'] }}/{{ $stats['total_all'] }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="bg-teal-500 h-2 rounded-full transition-all duration-1000" style="width: {{ $pctCompleted }}%"></div>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between items-center text-xs font-bold mb-1">
                                    <span class="text-orange-600">On Progress ({{ $pctProcess }}%)</span>
                                    <span class="text-gray-500">{{ $stats['waiting_approval'] + $stats['waiting_director'] + $stats['waiting_supply'] }}/{{ $stats['total_all'] }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="bg-orange-500 h-2 rounded-full transition-all duration-1000" style="width: {{ $pctProcess }}%"></div>
                                </div>
                            </div>

                             <div>
                                <div class="flex justify-between items-center text-xs font-bold mb-1">
                                    <span class="text-red-600">Rejected ({{ $pctRejected }}%)</span>
                                    <span class="text-gray-500">{{ $stats['rejected'] }}/{{ $stats['total_all'] }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="bg-red-500 h-2 rounded-full transition-all duration-1000" style="width: {{ $pctRejected }}%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-gray-100 grid grid-cols-3 gap-2">
                            <div class="text-center">
                                <div class="inline-flex p-2 rounded-full bg-blue-50 text-blue-600 mb-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                                <span class="block text-xs font-bold text-gray-700">{{ $masterData['employees'] }} User</span>
                            </div>
                            <div class="text-center">
                                <div class="inline-flex p-2 rounded-full bg-purple-50 text-purple-600 mb-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                </div>
                                <span class="block text-xs font-bold text-gray-700">{{ $masterData['departments'] }} Dept</span>
                            </div>
                            <div class="text-center">
                                <div class="inline-flex p-2 rounded-full bg-teal-50 text-teal-600 mb-1">
                                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </div>
                                <span class="block text-xs font-bold text-gray-700">{{ $masterData['companies'] }} PT</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Chart.defaults.font.family = "'Figtree', sans-serif";
            Chart.defaults.color = '#6b7280';

            // 1. STATUS CHART (Doughnut)
            const ctxStatus = document.getElementById('statusChart').getContext('2d');
            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [{
                        data: @json($chartData['data']),
                        backgroundColor: @json($chartData['colors']),
                        borderWidth: 0,
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%', // Lebih tebal sedikit
                    plugins: { legend: { display: false } }
                }
            });

            // 2. PRIORITY CHART (Bar Chart)
            const ctxPriority = document.getElementById('priorityChart').getContext('2d');
            new Chart(ctxPriority, {
                type: 'bar',
                data: {
                    labels: ['Top Urgent', 'Urgent', 'Normal', 'Outstanding'],
                    datasets: [{
                        label: 'Requests',
                        data: [
                            {{ $priorityStats['Top Urgent'] }},
                            {{ $priorityStats['Urgent'] }},
                            {{ $priorityStats['Normal'] }},
                            {{ $priorityStats['Outstanding'] }}
                        ],
                        backgroundColor: [
                            '#ef4444', // Merah
                            '#f97316', // Orange
                            '#3b82f6', // Biru
                            '#1f2937'  // Hitam
                        ],
                        borderRadius: 8,
                        barThickness: 35,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f3f4f6', drawBorder: false },
                            ticks: { display: false }
                        },
                        x: { grid: { display: false, drawBorder: false } }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        });
    </script>
</x-app-layout>

{{-- <x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Dashboard {{ ucfirst($currentMode) }}
                    </h2>
                    <p class="text-sm text-gray-500">System Monitoring & Approval Center</p>
                </div>

                @if($isApprover)
                <div class="mt-4 md:mt-0 bg-white dark:bg-gray-800 p-1 rounded-lg border shadow-sm flex space-x-1">
                    <a href="{{ route('dashboard.select_role', 'approver') }}"
                       class="px-4 py-2 rounded-md text-sm font-medium transition {{ $currentMode == 'approver' ? 'bg-blue-600 text-white shadow' : 'text-gray-600 hover:bg-gray-50' }}">
                       Approver View
                    </a>
                    <a href="{{ route('dashboard.select_role', 'requester') }}"
                       class="px-4 py-2 rounded-md text-sm font-medium transition {{ $currentMode == 'requester' ? 'bg-blue-600 text-white shadow' : 'text-gray-600 hover:bg-gray-50' }}">
                       Requester View
                    </a>
                </div>
                @endif
            </div>

            @if($stats['my_actions'] > 0)
            <div class="mb-6 bg-gradient-to-r from-red-50 to-white border-l-4 border-red-500 p-4 rounded shadow-sm flex justify-between items-center animate-pulse">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-full text-red-600 mr-3">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <p class="font-bold text-red-700">Perhatian!</p>
                        <p class="text-sm text-red-600">Anda memiliki <strong>{{ $stats['my_actions'] }} dokumen</strong> yang membutuhkan tindakan segera.</p>
                    </div>
                </div>
                <a href="{{ $currentMode == 'approver' ? route('requisitions.status', 'on_progress') : route('requisitions.status', 'draft') }}"
                   class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm shadow">
                    Proses &rarr;
                </a>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border-t-4 border-purple-500 relative overflow-hidden">
                    <div class="absolute right-0 top-0 p-3 opacity-10">
                         <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Waiting Director</p>
                    <p class="text-3xl font-extrabold text-purple-600 mt-1">{{ $stats['waiting_director'] }}</p>
                    <p class="text-xs text-gray-400 mt-2">Partially Approved</p>
                </div>

                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border-t-4 border-yellow-500 relative overflow-hidden">
                    <div class="absolute right-0 top-0 p-3 opacity-10">
                        <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z" /><path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd" /></svg>
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Waiting Supply</p>
                    <p class="text-3xl font-extrabold text-yellow-600 mt-1">{{ $stats['waiting_supply'] }}</p>
                    <p class="text-xs text-gray-400 mt-2">Ready to Purchase</p>
                </div>

                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border-t-4 border-teal-500 relative overflow-hidden">
                    <div class="absolute right-0 top-0 p-3 opacity-10">
                        <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Completed</p>
                    <p class="text-3xl font-extrabold text-teal-600 mt-1">{{ $stats['completed'] }}</p>
                    <p class="text-xs text-gray-400 mt-2">All Done</p>
                </div>

                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border-t-4 border-gray-400">
                    <div class="flex justify-between items-end">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Volume</p>
                            <p class="text-3xl font-extrabold text-gray-700 mt-1">{{ $stats['total_all'] }}</p>
                        </div>
                        <div class="text-right">
                            <span class="block text-xs text-red-500 font-bold">{{ $stats['rejected'] }} Rejected</span>
                            <span class="block text-xs text-orange-500 font-bold">{{ $stats['waiting_approval'] }} Pending</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase mb-4 border-b pb-2 flex justify-between items-center">
                        <span>Analysis by Priority</span>
                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">Based on Time Diff</span>
                    </h3>
                    <div class="relative h-48 w-full">
                        <canvas id="priorityChart"></canvas>
                    </div>
                    <div class="mt-4 text-xs text-gray-500 space-y-1">
                        <p><span class="w-2 h-2 inline-block rounded-full bg-red-600 mr-1"></span> Top Urgent (≤ 2 Hari)</p>
                        <p><span class="w-2 h-2 inline-block rounded-full bg-orange-500 mr-1"></span> Urgent (≤ 5 Hari)</p>
                        <p><span class="w-2 h-2 inline-block rounded-full bg-blue-500 mr-1"></span> Normal (> 5 Hari)</p>
                        <p><span class="w-2 h-2 inline-block rounded-full bg-black mr-1"></span> Outstanding (Terlambat)</p>
                    </div>
                </div>

                <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase mb-4 border-b pb-2 flex justify-between">
                        <span>📅 Upcoming Deadlines (Required Date)</span>
                        <a href="#" class="text-xs text-blue-500 hover:underline normal-case">View Calendar</a>
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2">Due Date</th>
                                    <th class="px-3 py-2">RL Number</th>
                                    <th class="px-3 py-2">Requester</th>
                                    <th class="px-3 py-2">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($upcomingDeadlines as $rl)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-3 py-2">
                                        <div class="flex items-center">
                                            <div class="p-1 bg-red-100 text-red-600 rounded mr-2 font-bold text-xs text-center min-w-[30px]">
                                                {{ \Carbon\Carbon::parse($rl->required_date)->format('d') }}<br>
                                                {{ \Carbon\Carbon::parse($rl->required_date)->format('M') }}
                                            </div>
                                            <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($rl->required_date)->diffForHumans() }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 font-medium text-gray-900">{{ $rl->rl_no }}</td>
                                    <td class="px-3 py-2">{{ $rl->requester->full_name ?? '-' }}</td>
                                    <td class="px-3 py-2">
                                        <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2 py-0.5 rounded border border-gray-500">
                                            {{ str_replace('_', ' ', $rl->status_flow) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-4 text-center text-gray-400 italic">Tidak ada deadline mendesak.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase mb-4 border-b pb-2">
                        🚀 Recent Activity
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-3">RL Number</th>
                                    <th class="px-4 py-3">Subject</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentActivities as $rl)
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-900">
                                        {{ $rl->rl_no }}
                                        <div class="text-xs text-gray-400">{{ $rl->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-4 py-3">{{ Str::limit($rl->subject, 30) }}</td>
                                    <td class="px-4 py-3">
                                        @php
                                            $badges = [
                                                'DRAFT' => 'bg-gray-100 text-gray-800',
                                                'ON_PROGRESS' => 'bg-orange-100 text-orange-800',
                                                'PARTIALLY_APPROVED' => 'bg-purple-100 text-purple-800',
                                                'WAITING_SUPPLY' => 'bg-yellow-100 text-yellow-800',
                                                'COMPLETED' => 'bg-teal-100 text-teal-800',
                                                'REJECTED' => 'bg-red-100 text-red-800',
                                            ];
                                        @endphp
                                        <span class="{{ $badges[$rl->status_flow] ?? 'bg-gray-100' }} px-2 py-1 rounded text-xs font-bold">
                                            {{ str_replace('_', ' ', $rl->status_flow) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('requisitions.show', $rl->id) }}" class="text-blue-600 hover:underline">Detail</a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center py-4">Belum ada aktivitas.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase mb-4 border-b pb-2">
                        📊 Composition
                    </h3>
                    <div class="relative h-48 w-full">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="mt-4 grid grid-cols-3 gap-2 text-center text-xs text-gray-500">
                        <div class="p-2 bg-gray-50 rounded">
                            <span class="block font-bold text-lg text-gray-800">{{ $masterData['employees'] }}</span>
                            Users
                        </div>
                        <div class="p-2 bg-gray-50 rounded">
                            <span class="block font-bold text-lg text-gray-800">{{ $masterData['departments'] }}</span>
                            Depts
                        </div>
                        <div class="p-2 bg-gray-50 rounded">
                            <span class="block font-bold text-lg text-gray-800">{{ $masterData['companies'] }}</span>
                            Entities
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. STATUS CHART (Doughnut)
            const ctxStatus = document.getElementById('statusChart').getContext('2d');
            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [{
                        data: @json($chartData['data']),
                        backgroundColor: @json($chartData['colors']),
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { position: 'right', labels: { boxWidth: 10, font: { size: 10 } } }
                    }
                }
            });

            // 2. PRIORITY CHART (Bar Chart - BARU)
            const ctxPriority = document.getElementById('priorityChart').getContext('2d');
            new Chart(ctxPriority, {
                type: 'bar',
                data: {
                    labels: ['Top Urgent', 'Urgent', 'Normal', 'Outstanding'],
                    datasets: [{
                        label: 'Requests',
                        data: [
                            {{ $priorityStats['Top Urgent'] }},
                            {{ $priorityStats['Urgent'] }},
                            {{ $priorityStats['Normal'] }},
                            {{ $priorityStats['Outstanding'] }}
                        ],
                        backgroundColor: [
                            '#dc2626', // Merah (Top Urgent)
                            '#f97316', // Orange (Urgent)
                            '#3b82f6', // Biru (Normal)
                            '#000000'  // Hitam (Outstanding)
                        ],
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, grid: { display: false } },
                        x: { grid: { display: false } }
                    },
                    plugins: {
                        legend: { display: false } // Hide legend karena warna sudah jelas
                    }
                }
            });
        });
    </script>
</x-app-layout> --}}

{{-- TAMPILAN VERSI 3  --}}
{{-- <x-app-layout>

    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-4 gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Halo, <span class="font-semibold text-blue-600">{{ Auth::user()->full_name }}</span>
                <span class="bg-gray-100 text-gray-800 text-xs font-bold px-2 py-0.5 rounded ml-2 border border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600">
                    Mode: {{ $viewType ?? 'Requester' }}
                </span>
            </p>
        </div>

        <div class="text-right">
            <div class="text-xs text-gray-400 font-medium mb-1">{{ now()->format('l, d F Y') }}</div>

            @if(($viewType ?? '') == 'Approver')
            <div class="inline-flex items-center justify-center bg-gray-900 text-green-400 font-mono font-bold text-xl px-3 py-1.5 rounded-lg shadow-md border border-gray-700 tracking-widest digital-clock">
                <span id="clock-hours">00</span><span class="animate-pulse mx-1">:</span><span id="clock-minutes">00</span><span class="animate-pulse mx-1">:</span><span id="clock-seconds" class="text-green-600 text-lg">00</span>
            </div>
            @endif

            @if($isApprover)
            <div class="mt-2">
                <a href="{{ route('dashboard.select_role', 'reset') }}" class="text-xs font-medium text-blue-600 hover:text-blue-800 hover:underline flex items-center justify-end transition-colors">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    Ganti Mode
                </a>
            </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 mb-6">

        <div class="flex flex-col p-3 bg-white border border-gray-200 rounded-lg shadow-xs dark:bg-gray-800 dark:border-gray-700">
            <dt class="mb-1 text-gray-500 dark:text-gray-400 text-xs uppercase font-semibold tracking-wider">Total</dt>
            <dd class="text-2xl font-bold text-gray-900 dark:text-white">{{ $countTotal }}</dd>
        </div>

        <div class="flex flex-col p-3 bg-white border-l-4 border-gray-400 rounded-r-lg shadow-xs dark:bg-gray-800 dark:border-gray-700">
            <dt class="mb-1 text-gray-500 dark:text-gray-400 text-xs uppercase font-semibold tracking-wider">Draft</dt>
            <dd class="text-2xl font-bold text-gray-600 dark:text-gray-300">{{ $countDraft }}</dd>
        </div>

        <div class="flex flex-col p-3 bg-white border-l-4 border-orange-400 rounded-r-lg shadow-xs dark:bg-gray-800 dark:border-gray-700">
            <dt class="mb-1 text-orange-500 dark:text-orange-400 text-xs uppercase font-semibold tracking-wider">Waiting</dt>
            <dd class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $countPending }}</dd>
        </div>

        <div class="flex flex-col p-3 bg-white border-l-4 border-green-400 rounded-r-lg shadow-xs dark:bg-gray-800 dark:border-gray-700">
            <dt class="mb-1 text-green-500 dark:text-green-400 text-xs uppercase font-semibold tracking-wider">Approved</dt>
            <dd class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $countApproved }}</dd>
        </div>

        <div class="flex flex-col p-3 bg-white border-l-4 border-red-400 rounded-r-lg shadow-xs dark:bg-gray-800 dark:border-gray-700">
            <dt class="mb-1 text-red-500 dark:text-red-400 text-xs uppercase font-semibold tracking-wider">Rejected</dt>
            <dd class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $countRejected }}</dd>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6">

        @if(($viewType ?? '') == 'Approver')
        <div class="p-4 bg-blue-50 border border-blue-100 rounded-lg dark:bg-gray-800 dark:border-gray-700">
            <h3 class="text-sm font-bold text-blue-800 dark:text-blue-300 mb-3">Data Master</h3>
            <div class="space-y-2">
                <div class="flex justify-between text-xs">
                    <span class="text-gray-600 dark:text-gray-400">Employees</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $countEmployees }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-600 dark:text-gray-400">Departments</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $countDepartments }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-600 dark:text-gray-400">Companies</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $countCompanies }}</span>
                </div>
            </div>
        </div>
        @endif

        <div class="{{ ($viewType ?? '') == 'Approver' ? 'lg:col-span-3' : 'lg:col-span-4' }} grid grid-cols-2 sm:grid-cols-4 gap-3">

            @if(($viewType ?? '') == 'Requester')
            <a href="{{ route('requisitions.create') }}" class="flex items-center p-3 bg-white border border-gray-200 rounded-lg shadow-xs hover:bg-blue-50 hover:border-blue-300 transition dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 group">
                <div class="p-2 bg-blue-100 rounded-lg group-hover:bg-blue-200 dark:bg-blue-900">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Action</p>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">Buat RL Baru</p>
                </div>
            </a>
            <a href="{{ route('requisitions.status', 'draft') }}" class="flex items-center p-3 bg-white border border-gray-200 rounded-lg shadow-xs hover:bg-gray-50 transition dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 group">
                <div class="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-200 dark:bg-gray-700">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Go to</p>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">Draft Saya</p>
                </div>
            </a>
            @endif

            <a href="{{ route('requisitions.status', 'on_progress') }}" class="flex items-center p-3 bg-white border border-gray-200 rounded-lg shadow-xs hover:bg-orange-50 hover:border-orange-300 transition dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 group">
                <div class="p-2 bg-orange-100 rounded-lg group-hover:bg-orange-200 dark:bg-orange-900">
                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Check</p>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ ($viewType ?? '') == 'Approver' ? 'Antrian' : 'Pending' }}</p>
                </div>
            </a>

            <a href="{{ route('profile.edit') }}" class="flex items-center p-3 bg-white border border-gray-200 rounded-lg shadow-xs hover:bg-purple-50 hover:border-purple-300 transition dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 group">
                <div class="p-2 bg-purple-100 rounded-lg group-hover:bg-purple-200 dark:bg-purple-900">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Settings</p>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">Akun</p>
                </div>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800 h-fit">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Grafik Status</h3>
            <div id="status-chart" class="w-full"></div>
        </div>

        <div class="lg:col-span-2 p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Aktivitas Terbaru</h3>

                @if(($viewType ?? '') == 'Approver')
                     <a href="{{ route('requisitions.status', 'on_progress') }}" class="text-xs text-blue-600 hover:underline dark:text-blue-500">Lihat Antrian</a>
                @else
                     <a href="{{ route('requisitions.status', 'on_progress') }}" class="text-xs text-blue-600 hover:underline dark:text-blue-500">Lihat Semua</a>
                @endif
            </div>

            <div class="relative overflow-x-auto">
                <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-3 py-2">RL No</th>
                            <th class="px-3 py-2">Subject</th>
                            <th class="px-3 py-2 text-center">Status</th>
                            <th class="px-3 py-2 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_rls as $rl)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $rl->rl_no }}
                                <div class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($rl->request_date)->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-3 py-2">
                                {{ str($rl->subject)->limit(30) }}
                            </td>
                            <td class="px-3 py-2 text-center">
                                @php
                                    $status = $rl->status_flow;
                                    // Logic status khusus approver (lihat status antrian dia, bukan status global)
                                    if(($viewType ?? '') == 'Approver') {
                                        $myQ = $rl->approvalQueues->where('approver_id', Auth::user()->employee_id)->first();
                                        $status = $myQ ? $myQ->status : $status;
                                    }
                                @endphp

                                @if($status == 'PENDING' || $status == 'ON_PROGRESS')
                                    <span class="bg-orange-100 text-orange-800 text-[10px] font-medium px-2 py-0.5 rounded-full dark:bg-orange-900 dark:text-orange-300">Wait</span>
                                @elseif($status == 'APPROVED')
                                    <span class="bg-green-100 text-green-800 text-[10px] font-medium px-2 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">OK</span>
                                @elseif($status == 'REJECTED')
                                    <span class="bg-red-100 text-red-800 text-[10px] font-medium px-2 py-0.5 rounded-full dark:bg-red-900 dark:text-red-300">No</span>
                                @else
                                    <span class="bg-gray-100 text-gray-800 text-[10px] font-medium px-2 py-0.5 rounded-full dark:bg-gray-700 dark:text-gray-300">Draft</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-right">
                                <a href="{{ route('requisitions.show', $rl->id) }}" class="text-blue-600 hover:text-blue-900">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-3 py-4 text-center">No Data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Chart Config
            const chartData = {{ $chartData }};
            const options = {
                series: [{ name: 'Jumlah', data: chartData }],
                chart: { height: 250, type: 'bar', toolbar: { show: false }, fontFamily: 'Figtree, sans-serif' },
                plotOptions: { bar: { borderRadius: 2, columnWidth: '40%', distributed: true } },
                colors: ['#9CA3AF', '#F97316', '#22C55E', '#EF4444'],
                dataLabels: { enabled: false },
                legend: { show: false },
                xaxis: { categories: ['Draft', 'Pending', 'Approved', 'Rejected'], labels: { style: { fontSize: '10px' } } },
                grid: { borderColor: '#f1f1f1' }
            };
            new ApexCharts(document.querySelector("#status-chart"), options).render();

            // Realtime Clock
            function updateClock() {
                const now = new Date();
                const h = String(now.getHours()).padStart(2, '0');
                const m = String(now.getMinutes()).padStart(2, '0');
                const s = String(now.getSeconds()).padStart(2, '0');
                if(document.getElementById('clock-hours')) {
                    document.getElementById('clock-hours').innerText = h;
                    document.getElementById('clock-minutes').innerText = m;
                    document.getElementById('clock-seconds').innerText = s;
                }
            }
            setInterval(updateClock, 1000);
            updateClock();
        });
    </script>
</x-app-layout> --}}

{{-- TAMPILAN VERSI 2 --}}
{{-- <x-app-layout>
    <div class="flex justify-between items-end mb-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Halo, <span class="font-semibold text-blue-600">{{ Auth::user()->full_name }}</span>
                ({{ $isApprover ? 'Approver' : 'Requester' }})
            </p>
        </div>
        <div class="text-right">
            <span class="text-xs text-gray-400 font-mono">{{ now()->format('d M Y') }}</span>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 mb-6">

        <div class="flex flex-col p-3 bg-white border border-gray-200 rounded-lg shadow-xs dark:bg-gray-800 dark:border-gray-700">
            <dt class="mb-1 text-gray-500 dark:text-gray-400 text-xs uppercase font-semibold tracking-wider">Total</dt>
            <dd class="text-2xl font-bold text-gray-900 dark:text-white">{{ $countTotal }}</dd>
        </div>

        <div class="flex flex-col p-3 bg-white border-l-4 border-gray-400 rounded-r-lg shadow-xs dark:bg-gray-800 dark:border-gray-700">
            <dt class="mb-1 text-gray-500 dark:text-gray-400 text-xs uppercase font-semibold tracking-wider">Draft</dt>
            <dd class="text-2xl font-bold text-gray-600 dark:text-gray-300">{{ $countDraft }}</dd>
        </div>

        <div class="flex flex-col p-3 bg-white border-l-4 border-orange-400 rounded-r-lg shadow-xs dark:bg-gray-800 dark:border-gray-700">
            <dt class="mb-1 text-orange-500 dark:text-orange-400 text-xs uppercase font-semibold tracking-wider">Waiting</dt>
            <dd class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $countPending }}</dd>
        </div>

        <div class="flex flex-col p-3 bg-white border-l-4 border-green-400 rounded-r-lg shadow-xs dark:bg-gray-800 dark:border-gray-700">
            <dt class="mb-1 text-green-500 dark:text-green-400 text-xs uppercase font-semibold tracking-wider">Approved</dt>
            <dd class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $countApproved }}</dd>
        </div>

        <div class="flex flex-col p-3 bg-white border-l-4 border-red-400 rounded-r-lg shadow-xs dark:bg-gray-800 dark:border-gray-700">
            <dt class="mb-1 text-red-500 dark:text-red-400 text-xs uppercase font-semibold tracking-wider">Rejected</dt>
            <dd class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $countRejected }}</dd>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6">

        <div class="p-4 bg-blue-50 border border-blue-100 rounded-lg dark:bg-gray-800 dark:border-gray-700">
            <h3 class="text-sm font-bold text-blue-800 dark:text-blue-300 mb-3">Data Master</h3>
            <div class="space-y-2">
                <div class="flex justify-between text-xs">
                    <span class="text-gray-600 dark:text-gray-400">Employees</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $countEmployees }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-600 dark:text-gray-400">Departments</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $countDepartments }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-600 dark:text-gray-400">Companies</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $countCompanies }}</span>
                </div>
            </div>
        </div>

        <div class="lg:col-span-3 grid grid-cols-2 sm:grid-cols-4 gap-3">
            <a href="{{ route('requisitions.create') }}" class="flex items-center p-3 bg-white border border-gray-200 rounded-lg shadow-xs hover:bg-blue-50 hover:border-blue-300 transition dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 group">
                <div class="p-2 bg-blue-100 rounded-lg group-hover:bg-blue-200 dark:bg-blue-900">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Action</p>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">Buat RL Baru</p>
                </div>
            </a>

            <a href="{{ route('requisitions.status', 'draft') }}" class="flex items-center p-3 bg-white border border-gray-200 rounded-lg shadow-xs hover:bg-gray-50 transition dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 group">
                <div class="p-2 bg-gray-100 rounded-lg group-hover:bg-gray-200 dark:bg-gray-700">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Go to</p>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">Draft Saya</p>
                </div>
            </a>

            <a href="{{ route('requisitions.status', 'on_progress') }}" class="flex items-center p-3 bg-white border border-gray-200 rounded-lg shadow-xs hover:bg-orange-50 hover:border-orange-300 transition dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 group">
                <div class="p-2 bg-orange-100 rounded-lg group-hover:bg-orange-200 dark:bg-orange-900">
                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Check</p>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">Pending</p>
                </div>
            </a>

            <a href="{{ route('profile.edit') }}" class="flex items-center p-3 bg-white border border-gray-200 rounded-lg shadow-xs hover:bg-purple-50 hover:border-purple-300 transition dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 group">
                <div class="p-2 bg-purple-100 rounded-lg group-hover:bg-purple-200 dark:bg-purple-900">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Settings</p>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">Akun</p>
                </div>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Grafik Status</h3>
            <div id="status-chart" class="w-full"></div>
        </div>

        <div class="lg:col-span-2 p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Aktivitas Terbaru</h3>
                <a href="#" class="text-xs text-blue-600 hover:underline dark:text-blue-500">Lihat Semua</a>
            </div>

            <div class="relative overflow-x-auto">
                <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-3 py-2">RL No</th>
                            <th class="px-3 py-2">Subject</th>
                            <th class="px-3 py-2 text-center">Status</th>
                            <th class="px-3 py-2 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_rls as $rl)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $rl->rl_no }}
                                <div class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($rl->request_date)->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-3 py-2">
                                {{ str($rl->subject)->limit(30) }}
                            </td>
                            <td class="px-3 py-2 text-center">
                                @php
                                    $status = $rl->status_flow;
                                    if($isApprover) {
                                        $myQ = $rl->approvalQueues->where('approver_id', Auth::user()->employee_id)->first();
                                        $status = $myQ ? $myQ->status : $status;
                                    }
                                @endphp

                                @if($status == 'PENDING' || $status == 'ON_PROGRESS')
                                    <span class="bg-orange-100 text-orange-800 text-[10px] font-medium px-2 py-0.5 rounded-full dark:bg-orange-900 dark:text-orange-300">Wait</span>
                                @elseif($status == 'APPROVED')
                                    <span class="bg-green-100 text-green-800 text-[10px] font-medium px-2 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">OK</span>
                                @elseif($status == 'REJECTED')
                                    <span class="bg-red-100 text-red-800 text-[10px] font-medium px-2 py-0.5 rounded-full dark:bg-red-900 dark:text-red-300">No</span>
                                @else
                                    <span class="bg-gray-100 text-gray-800 text-[10px] font-medium px-2 py-0.5 rounded-full dark:bg-gray-700 dark:text-gray-300">Draft</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-right">
                                <a href="{{ route('requisitions.show', $rl->id) }}" class="text-blue-600 hover:text-blue-900">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-3 py-4 text-center">No Data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartData = {{ $chartData }};
            const options = {
                series: [{ name: 'Jumlah', data: chartData }],
                chart: { height: 250, type: 'bar', toolbar: { show: false }, fontFamily: 'Figtree, sans-serif' }, // Height dikecilkan
                plotOptions: { bar: { borderRadius: 2, columnWidth: '40%', distributed: true } },
                colors: ['#9CA3AF', '#F97316', '#22C55E', '#EF4444'],
                dataLabels: { enabled: false },
                legend: { show: false },
                xaxis: { categories: ['Draft', 'Pending', 'Approved', 'Rejected'], labels: { style: { fontSize: '10px' } } },
                grid: { borderColor: '#f1f1f1' }
            };
            new ApexCharts(document.querySelector("#status-chart"), options).render();
        });
    </script>
</x-app-layout> --}}

{{-- TAMPILAN VERSI 1  --}}
{{-- <x-app-layout>
    <div class="flex justify-between items-end mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard Overview</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">
                Halo, <span class="font-semibold text-blue-600">{{ Auth::user()->full_name }}</span>!
                Anda login sebagai <span class="badge bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">{{ Auth::user()->position->position_name ?? 'Staff' }}</span>
            </p>
        </div>
        <div class="text-right">
            <span class="text-xs text-gray-400">{{ now()->format('l, d F Y') }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="flex items-center p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="p-3 mr-4 text-blue-500 bg-blue-100 rounded-full dark:text-blue-100 dark:bg-blue-900">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
            </div>
            <div>
                <p class="mb-1 text-sm font-medium text-gray-500 dark:text-gray-400">Total Karyawan</p>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $countEmployees }}</h3>
            </div>
        </div>
        <div class="flex items-center p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="p-3 mr-4 text-purple-500 bg-purple-100 rounded-full dark:text-purple-100 dark:bg-purple-900">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"></path></svg>
            </div>
            <div>
                <p class="mb-1 text-sm font-medium text-gray-500 dark:text-gray-400">Departemen</p>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $countDepartments }}</h3>
            </div>
        </div>
        <div class="flex items-center p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="p-3 mr-4 text-teal-500 bg-teal-100 rounded-full dark:text-teal-100 dark:bg-teal-900">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10.496 2.132a1 1 0 00-.992 0l-7 4A1 1 0 003 8v7a1 1 0 100 2h14a1 1 0 100-2V8a1 1 0 00.504-1.868l-7-4zM6 14.641V13a1 1 0 112 0v1.641l2-1.142V13a1 1 0 112 0v1.641l2-1.142V11.75a1 1 0 00-.504-1.868L10 8.75l-3.496 1.132A1 1 0 006 11.75v1.749l2 1.142z" clip-rule="evenodd"></path></svg>
            </div>
            <div>
                <p class="mb-1 text-sm font-medium text-gray-500 dark:text-gray-400">Perusahaan (PT)</p>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $countCompanies }}</h3>
            </div>
        </div>
    </div>

    <div class="mb-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">Quick Access</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('requisitions.create') }}" class="flex flex-col items-center justify-center p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 transition duration-200 group">
                <div class="p-3 mb-2 bg-blue-100 rounded-full group-hover:bg-blue-200 dark:bg-blue-900 dark:group-hover:bg-blue-800">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <span class="text-sm font-medium text-gray-900 dark:text-white">Buat Permintaan</span>
            </a>

            <a href="{{ route('requisitions.status', 'draft') }}" class="flex flex-col items-center justify-center p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 transition duration-200 group">
                <div class="p-3 mb-2 bg-gray-100 rounded-full group-hover:bg-gray-200 dark:bg-gray-700 dark:group-hover:bg-gray-600">
                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <span class="text-sm font-medium text-gray-900 dark:text-white">Draft Saya</span>
            </a>

            <a href="{{ route('requisitions.status', 'on_progress') }}" class="flex flex-col items-center justify-center p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 transition duration-200 group">
                <div class="p-3 mb-2 bg-orange-100 rounded-full group-hover:bg-orange-200 dark:bg-orange-900 dark:group-hover:bg-orange-800">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-sm font-medium text-gray-900 dark:text-white">Menunggu Approval</span>
            </a>

            <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 transition duration-200 group">
                <div class="p-3 mb-2 bg-purple-100 rounded-full group-hover:bg-purple-200 dark:bg-purple-900 dark:group-hover:bg-purple-800">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <span class="text-sm font-medium text-gray-900 dark:text-white">Pengaturan Akun</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex flex-col">
                <dt class="text-3xl font-extrabold text-gray-900 dark:text-white">{{ $countTotal }}</dt>
                <dd class="text-gray-500 dark:text-gray-400 text-sm">Total Aktivitas</dd>
            </div>
        </div>

        <div class="p-4 bg-white border-l-4 border-gray-400 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-500">
            <div class="flex justify-between items-center">
                <div>
                    <dt class="text-2xl font-bold text-gray-700 dark:text-gray-300">{{ $countDraft }}</dt>
                    <dd class="text-gray-500 dark:text-gray-400 text-sm">Drafts</dd>
                </div>
            </div>
        </div>

        <div class="p-4 bg-white border-l-4 border-orange-400 rounded-lg shadow-sm dark:bg-gray-800 dark:border-orange-500">
            <div class="flex justify-between items-center">
                <div>
                    <dt class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $countPending }}</dt>
                    <dd class="text-gray-500 dark:text-gray-400 text-sm">Waiting</dd>
                </div>
            </div>
        </div>

        <div class="p-4 bg-white border-l-4 border-green-400 rounded-lg shadow-sm dark:bg-gray-800 dark:border-green-500">
            <div class="flex justify-between items-center">
                <div>
                    <dt class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $countApproved }}</dt>
                    <dd class="text-gray-500 dark:text-gray-400 text-sm">Approved</dd>
                </div>
            </div>
        </div>

        <div class="p-4 bg-white border-l-4 border-red-400 rounded-lg shadow-sm dark:bg-gray-800 dark:border-red-500">
            <div class="flex justify-between items-center">
                <div>
                    <dt class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $countRejected }}</dt>
                    <dd class="text-gray-500 dark:text-gray-400 text-sm">Rejected</dd>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h3 class="mb-4 text-lg font-bold text-gray-900 dark:text-white">Analisis Status</h3>
            <div id="status-chart" class="w-full"></div>
        </div>

        <div class="lg:col-span-2 p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Aktivitas Terbaru</h3>
                <div class="relative">
                    <input type="text" id="table-search" class="block p-2 text-xs text-gray-900 border border-gray-300 rounded-lg w-50 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Cari No RL...">
                </div>
            </div>

            <div class="relative overflow-x-auto rounded-lg">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3">RL No</th>
                            <th class="px-4 py-3">Subject</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_rls as $rl)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $rl->rl_no }}
                                <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($rl->request_date)->format('d M') }}</div>
                            </td>
                            <td class="px-4 py-3">
                                {{ str($rl->subject)->limit(25) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $status = $rl->status_flow;
                                    if($isApprover) {
                                        $myQ = $rl->approvalQueues->where('approver_id', Auth::user()->employee_id)->first();
                                        $status = $myQ ? $myQ->status : $status;
                                    }
                                @endphp

                                @if($status == 'PENDING' || $status == 'ON_PROGRESS')
                                    <span class="bg-orange-100 text-orange-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-orange-900 dark:text-orange-300">Wait</span>
                                @elseif($status == 'APPROVED')
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">OK</span>
                                @elseif($status == 'REJECTED')
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-red-900 dark:text-red-300">No</span>
                                @else
                                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-gray-700 dark:text-gray-300">Draft</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('requisitions.show', $rl->id) }}" class="text-blue-600 hover:text-blue-900">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-4 py-4 text-center">No Data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartData = {{ $chartData }};
            const options = {
                series: [{ name: 'Jumlah', data: chartData }],
                chart: { height: 300, type: 'bar', toolbar: { show: false }, fontFamily: 'Figtree, sans-serif' },
                plotOptions: { bar: { borderRadius: 4, columnWidth: '50%', distributed: true } },
                colors: ['#9CA3AF', '#F97316', '#22C55E', '#EF4444'],
                dataLabels: { enabled: false },
                legend: { show: false },
                xaxis: { categories: ['Draft', 'Pending', 'Approved', 'Rejected'], labels: { style: { fontSize: '12px' } } },
                grid: { borderColor: '#f1f1f1' }
            };
            new ApexCharts(document.querySelector("#status-chart"), options).render();
        });
    </script>
</x-app-layout> --}}
