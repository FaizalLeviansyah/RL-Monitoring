<x-app-layout>
    <div class="pt-2 pb-8 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex flex-col md:flex-row justify-between items-end mb-6 animate-fade-in-down">
                <div>
                    <h2 class="text-4xl font-black text-slate-800 dark:text-white tracking-tight">
                        Approval Center
                    </h2>
                    <p class="text-slate-500 mt-1 text-lg font-light">
                        Welcome back, <span class="font-bold text-blue-600">{{ Auth::user()->full_name }}</span>.
                    </p>
                </div>

                <div class="mt-4 md:mt-0 p-1.5 bg-white/40 backdrop-blur-md rounded-2xl border border-white/50 shadow-lg flex items-center transform hover:scale-105 transition-transform">
                    <span class="px-5 py-2 rounded-xl text-xs font-bold bg-slate-800 text-white shadow-lg">
                        Approver View
                    </span>
                    <a href="{{ route('dashboard.select_role', 'requester') }}" class="px-5 py-2 rounded-xl text-xs font-bold text-slate-500 hover:text-blue-600 hover:bg-white/50 transition-all">
                        Switch to Requester
                    </a>
                </div>
            </div>

            @if($stats['my_actions'] > 0)
            <div class="mb-6 relative group animate-pulse-slow">
                <div class="absolute -inset-1 bg-gradient-to-r from-red-500 via-orange-500 to-red-500 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>
                <div class="relative bg-white/80 backdrop-blur-xl border border-red-100 rounded-2xl p-5 shadow-xl flex flex-col md:flex-row justify-between items-center">
                    <div class="flex items-center mb-4 md:mb-0">
                        <div class="p-3 bg-red-50 text-red-600 rounded-2xl mr-4 shadow-inner">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-slate-800 text-lg">Action Required</h4>
                            <p class="text-slate-500 text-sm">You have <strong class="text-red-600 text-base border-b border-red-200">{{ $stats['my_actions'] }} documents</strong> waiting for your signature.</p>
                        </div>
                    </div>
                    <a href="{{ route('requisitions.status', 'on_progress') }}" class="group/btn relative inline-flex items-center justify-center px-6 py-2.5 text-sm font-bold text-white transition-all duration-200 bg-red-600 font-pj rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600 hover:bg-red-700 shadow-lg shadow-red-500/30">
                        Review Now
                        <svg class="w-4 h-4 ml-2 -mr-1 transition-transform group-hover/btn:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
                <div class="group relative bg-white/70 backdrop-blur-xl border border-orange-100 rounded-3xl p-5 shadow-lg shadow-orange-500/5 transition-all duration-300 hover:-translate-y-1 hover:shadow-orange-500/20">
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-orange-400 to-red-500 rounded-t-3xl"></div>
                    <div class="flex justify-between items-start pt-2">
                        <div>
                            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1">Inbox</p>
                            <h3 class="text-3xl font-black text-slate-800 group-hover:text-orange-600 transition-colors">{{ $stats['waiting_approval'] }}</h3>
                        </div>
                        <div class="p-2.5 bg-orange-50 text-orange-500 rounded-2xl group-hover:bg-orange-500 group-hover:text-white transition-all shadow-sm">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                        </div>
                    </div>
                </div>
                <div class="group relative bg-white/70 backdrop-blur-xl border border-purple-100 rounded-3xl p-5 shadow-lg shadow-purple-500/5 transition-all duration-300 hover:-translate-y-1 hover:shadow-purple-500/20">
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-purple-400 to-indigo-500 rounded-t-3xl"></div>
                    <div class="flex justify-between items-start pt-2">
                        <div>
                            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1">Director</p>
                            <h3 class="text-3xl font-black text-slate-800 group-hover:text-purple-600 transition-colors">{{ $stats['waiting_director'] }}</h3>
                        </div>
                        <div class="p-2.5 bg-purple-50 text-purple-500 rounded-2xl group-hover:bg-purple-500 group-hover:text-white transition-all shadow-sm">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        </div>
                    </div>
                </div>
                <div class="group relative bg-white/70 backdrop-blur-xl border border-yellow-100 rounded-3xl p-5 shadow-lg shadow-yellow-500/5 transition-all duration-300 hover:-translate-y-1 hover:shadow-yellow-500/20">
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-t-3xl"></div>
                    <div class="flex justify-between items-start pt-2">
                        <div>
                            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1">Supply</p>
                            <h3 class="text-3xl font-black text-slate-800 group-hover:text-yellow-600 transition-colors">{{ $stats['waiting_supply'] }}</h3>
                        </div>
                        <div class="p-2.5 bg-yellow-50 text-yellow-500 rounded-2xl group-hover:bg-yellow-500 group-hover:text-white transition-all shadow-sm">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        </div>
                    </div>
                </div>
                <div class="group relative bg-white/70 backdrop-blur-xl border border-teal-100 rounded-3xl p-5 shadow-lg shadow-teal-500/5 transition-all duration-300 hover:-translate-y-1 hover:shadow-teal-500/20">
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-teal-400 to-emerald-500 rounded-t-3xl"></div>
                    <div class="flex justify-between items-start pt-2">
                        <div>
                            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1">Done</p>
                            <h3 class="text-3xl font-black text-slate-800 group-hover:text-teal-600 transition-colors">{{ $stats['completed'] }}</h3>
                        </div>
                        <div class="p-2.5 bg-teal-50 text-teal-500 rounded-2xl group-hover:bg-teal-500 group-hover:text-white transition-all shadow-sm">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                    </div>
                </div>
                <div class="group relative bg-white/70 backdrop-blur-xl border border-red-100 rounded-3xl p-5 shadow-lg shadow-red-500/5 transition-all duration-300 hover:-translate-y-1 hover:shadow-red-500/20">
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-red-400 to-pink-500 rounded-t-3xl"></div>
                    <div class="flex justify-between items-start pt-2">
                        <div>
                            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1">Reject</p>
                            <h3 class="text-3xl font-black text-slate-800 group-hover:text-red-600 transition-colors">{{ $stats['rejected'] }}</h3>
                        </div>
                        <div class="p-2.5 bg-red-50 text-red-500 rounded-2xl group-hover:bg-red-500 group-hover:text-white transition-all shadow-sm">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

                <div class="lg:col-span-2 bg-gradient-to-br from-white/80 to-rose-50/50 backdrop-blur-xl border border-rose-100 rounded-3xl shadow-[0_8px_30px_rgba(244,63,94,0.05)] p-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-rose-200/20 rounded-full blur-3xl -mr-10 -mt-10 pointer-events-none"></div>

                    <div class="flex items-center justify-between mb-6 relative z-10">
                        <div class="flex items-center">
                            <span class="flex items-center justify-center w-9 h-9 rounded-xl bg-rose-100 text-rose-600 mr-3 shadow-sm border border-rose-200">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </span>
                            <div>
                                <h3 class="text-lg font-extrabold text-slate-800 leading-tight">Critical Deadlines</h3>
                                <p class="text-[10px] font-bold text-rose-500 uppercase tracking-wide">Action Needed (Next 7 Days)</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 relative z-10">
                        @forelse($upcomingDeadlines as $rl)
                        <a href="{{ route('requisitions.show', $rl->id) }}" class="group flex flex-col bg-white border border-slate-100 rounded-2xl p-4 hover:border-rose-300 hover:shadow-lg hover:shadow-rose-500/10 transition-all duration-300 h-full">

                            <div class="flex justify-between items-start mb-4">
                                <div class="flex flex-col items-center justify-center w-12 h-12 bg-rose-50 rounded-xl border border-rose-100 text-rose-600 group-hover:bg-rose-600 group-hover:text-white transition-colors duration-300">
                                    <span class="text-[9px] font-bold uppercase leading-none mt-1">{{ \Carbon\Carbon::parse($rl->required_date)->format('M') }}</span>
                                    <span class="text-xl font-black leading-none">{{ \Carbon\Carbon::parse($rl->required_date)->format('d') }}</span>
                                </div>
                                <span class="px-2.5 py-1 text-[10px] font-bold bg-slate-50 text-slate-500 rounded-lg border border-slate-100 group-hover:bg-white group-hover:border-rose-200 group-hover:text-rose-600 transition-colors">
                                    {{ \Carbon\Carbon::parse($rl->required_date)->diffForHumans(null, true) }} left
                                </span>
                            </div>

                            <div class="flex-1">
                                <h4 class="text-sm font-bold text-slate-800 mb-1 truncate group-hover:text-rose-600 transition-colors" title="{{ $rl->rl_no }}">
                                    {{ $rl->rl_no }}
                                </h4>
                                <div class="flex items-center text-xs text-slate-500 mb-3">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    <span class="truncate">{{ $rl->requester->full_name }}</span>
                                </div>
                            </div>

                            <div class="pt-3 mt-auto border-t border-slate-50 flex items-center justify-between">
                                @php
                                    $statusColorBg = match($rl->status_flow) {
                                        'ON_PROGRESS' => 'bg-orange-50 text-orange-600 border-orange-100',
                                        'PARTIALLY_APPROVED' => 'bg-purple-50 text-purple-600 border-purple-100',
                                        default => 'bg-slate-50 text-slate-600 border-slate-100'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-[9px] font-bold border {{ $statusColorBg }}">
                                    {{ str_replace('_', ' ', $rl->status_flow) }}
                                </span>
                                <svg class="w-4 h-4 text-slate-300 group-hover:text-rose-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </a>
                        @empty
                        <div class="col-span-1 md:col-span-2 flex flex-col items-center justify-center py-10 text-center border-2 border-dashed border-rose-100 rounded-2xl bg-white/40">
                            <div class="p-3 bg-rose-50 rounded-full mb-3">
                                <svg class="w-8 h-8 text-rose-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <p class="text-rose-400 font-medium text-sm">No critical deadlines. Great job!</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <div class="bg-gradient-to-br from-white/90 to-blue-50/50 backdrop-blur-xl border border-blue-100 rounded-3xl shadow-[0_8px_30px_rgba(59,130,246,0.05)] p-6 flex flex-col justify-between relative overflow-hidden">
                    <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-blue-400 to-indigo-500 opacity-50"></div>

                    <div>
                        <div class="flex items-center mb-2">
                             <span class="flex items-center justify-center w-9 h-9 rounded-xl bg-blue-100 text-blue-600 mr-3 shadow-sm border border-blue-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                            </span>
                            <h3 class="text-lg font-extrabold text-slate-800">Workflow Analysis</h3>
                        </div>
                        <p class="text-xs text-slate-500 leading-relaxed ml-12">
                            Visual distribution of current document status.
                        </p>
                    </div>
                    <div class="relative h-48 w-full mt-4">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="mt-4 pt-4 border-t border-blue-100/50 flex justify-between items-center">
                        <span class="text-xs font-bold text-blue-400 uppercase tracking-widest">Total Active</span>
                        <span class="text-3xl font-black text-slate-800">{{ $stats['waiting_approval'] + $stats['waiting_director'] + $stats['waiting_supply'] }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white/80 backdrop-blur-xl border border-slate-100 rounded-3xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/30">
                    <h3 class="text-lg font-extrabold text-slate-800">Incoming Requests</h3>
                    <button class="text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors hover:underline">View All &rarr;</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-50/80 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4 font-bold tracking-wider">RL Number</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Requester</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Subject</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Status</th>
                                <th class="px-6 py-4 font-bold tracking-wider text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white/50">
                            @forelse($recentActivities as $rl)
                            <tr class="hover:bg-blue-50/40 transition duration-200">
                                <td class="px-6 py-4 font-bold text-slate-700">
                                    <span class="font-mono text-xs text-slate-400 mr-1 opacity-50">#</span>{{ $rl->rl_no }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-600 flex items-center justify-center text-[10px] font-extrabold mr-3 border border-blue-200">
                                            {{ substr($rl->requester->full_name, 0, 1) }}
                                        </div>
                                        <span class="font-medium text-slate-600 text-xs">{{ $rl->requester->full_name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-500 italic text-xs">"{{ Str::limit($rl->subject, 25) }}"</td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusColor = match($rl->status_flow) {
                                            'ON_PROGRESS' => 'bg-orange-100 text-orange-700 border-orange-200',
                                            'PARTIALLY_APPROVED' => 'bg-purple-100 text-purple-700 border-purple-200',
                                            'APPROVED' => 'bg-green-100 text-green-700 border-green-200',
                                            'WAITING_SUPPLY' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                            'REJECTED' => 'bg-red-100 text-red-700 border-red-200',
                                            'COMPLETED' => 'bg-teal-100 text-teal-700 border-teal-200',
                                            default => 'bg-slate-100 text-slate-600'
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-md text-[9px] font-bold border {{ $statusColor }} shadow-sm">
                                        {{ str_replace('_', ' ', $rl->status_flow) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('requisitions.show', $rl->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white border border-slate-200 text-slate-400 hover:text-blue-600 hover:border-blue-400 hover:shadow-md transition-all group">
                                        <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-10 text-slate-400 italic text-sm">No incoming requests found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Chart.defaults.font.family = "'Figtree', sans-serif";
            const ctxStatus = document.getElementById('statusChart').getContext('2d');

            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: ['Inbox', 'Director', 'Supply', 'Done', 'Rejected'],
                    datasets: [{
                        data: [{{ $stats['waiting_approval'] }}, {{ $stats['waiting_director'] }}, {{ $stats['waiting_supply'] }}, {{ $stats['completed'] }}, {{ $stats['rejected'] }}],
                        backgroundColor: ['#f97316', '#a855f7', '#eab308', '#14b8a6', '#ef4444'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { position: 'right', labels: { boxWidth: 8, usePointStyle: true, font: { size: 10, family: "'Figtree', sans-serif" } } }
                    },
                    layout: { padding: 10 },
                    animation: { animateScale: true, animateRotate: true }
                }
            });
        });
    </script>
</x-app-layout>





{{-- <x-app-layout>
    <div class="py-8 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex flex-col md:flex-row justify-between items-end mb-8 animate-fade-in-down">
                <div>
                    <h2 class="text-4xl font-black text-slate-800 dark:text-white tracking-tight">
                        Approval Center
                    </h2>
                    <p class="text-slate-500 mt-2 text-lg font-light">
                        Welcome back, <span class="font-bold text-blue-600">{{ Auth::user()->full_name }}</span>.
                    </p>
                </div>

                <div class="mt-6 md:mt-0 p-1.5 bg-white/40 backdrop-blur-md rounded-2xl border border-white/50 shadow-lg flex items-center">
                    <span class="px-5 py-2.5 rounded-xl text-xs font-bold bg-slate-800 text-white shadow-lg">
                        Approver View
                    </span>
                    <a href="{{ route('dashboard.select_role', 'requester') }}" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-500 hover:text-blue-600 hover:bg-white/50 transition-all">
                        Switch to Requester
                    </a>
                </div>
            </div>

            @if($stats['my_actions'] > 0)
            <div class="mb-10 relative group animate-pulse-slow">
                <div class="absolute -inset-1 bg-gradient-to-r from-red-500 via-orange-500 to-red-500 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>
                <div class="relative bg-white/80 backdrop-blur-xl border border-red-100 rounded-2xl p-6 shadow-xl flex flex-col md:flex-row justify-between items-center">
                    <div class="flex items-center mb-4 md:mb-0">
                        <div class="p-4 bg-red-50 text-red-600 rounded-2xl mr-5 shadow-inner">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-slate-800 text-xl">Action Required</h4>
                            <p class="text-slate-500">You have <strong class="text-red-600 text-lg border-b-2 border-red-200">{{ $stats['my_actions'] }} documents</strong> waiting for your signature.</p>
                        </div>
                    </div>
                    <a href="{{ route('requisitions.status', 'on_progress') }}" class="group/btn relative inline-flex items-center justify-center px-8 py-3 text-sm font-bold text-white transition-all duration-200 bg-red-600 font-pj rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600 hover:bg-red-700 shadow-lg shadow-red-500/30">
                        Review Now
                        <svg class="w-5 h-5 ml-2 -mr-1 transition-transform group-hover/btn:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-10">

                <div class="group relative bg-white/60 backdrop-blur-xl border border-white/60 rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgba(249,115,22,0.2)] transition-all duration-300 hover:-translate-y-1">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-orange-400 to-red-500 rounded-t-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1">Inbox</p>
                            <h3 class="text-4xl font-black text-slate-800 group-hover:text-orange-600 transition-colors">{{ $stats['waiting_approval'] }}</h3>
                        </div>
                        <div class="p-3 bg-orange-50 text-orange-500 rounded-2xl group-hover:bg-orange-500 group-hover:text-white transition-all shadow-sm">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                        </div>
                    </div>
                </div>

                <div class="group relative bg-white/60 backdrop-blur-xl border border-white/60 rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgba(147,51,234,0.2)] transition-all duration-300 hover:-translate-y-1">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-purple-400 to-indigo-500 rounded-t-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1">Director</p>
                            <h3 class="text-4xl font-black text-slate-800 group-hover:text-purple-600 transition-colors">{{ $stats['waiting_director'] }}</h3>
                        </div>
                        <div class="p-3 bg-purple-50 text-purple-500 rounded-2xl group-hover:bg-purple-500 group-hover:text-white transition-all shadow-sm">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        </div>
                    </div>
                </div>

                <div class="group relative bg-white/60 backdrop-blur-xl border border-white/60 rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgba(234,179,8,0.2)] transition-all duration-300 hover:-translate-y-1">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-t-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1">Supply</p>
                            <h3 class="text-4xl font-black text-slate-800 group-hover:text-yellow-600 transition-colors">{{ $stats['waiting_supply'] }}</h3>
                        </div>
                        <div class="p-3 bg-yellow-50 text-yellow-500 rounded-2xl group-hover:bg-yellow-500 group-hover:text-white transition-all shadow-sm">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        </div>
                    </div>
                </div>

                <div class="group relative bg-white/60 backdrop-blur-xl border border-white/60 rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgba(20,184,166,0.2)] transition-all duration-300 hover:-translate-y-1">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-teal-400 to-emerald-500 rounded-t-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1">Done</p>
                            <h3 class="text-4xl font-black text-slate-800 group-hover:text-teal-600 transition-colors">{{ $stats['completed'] }}</h3>
                        </div>
                        <div class="p-3 bg-teal-50 text-teal-500 rounded-2xl group-hover:bg-teal-500 group-hover:text-white transition-all shadow-sm">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                    </div>
                </div>

                <div class="group relative bg-white/60 backdrop-blur-xl border border-white/60 rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgba(239,68,68,0.2)] transition-all duration-300 hover:-translate-y-1">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-red-400 to-pink-500 rounded-t-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1">Reject</p>
                            <h3 class="text-4xl font-black text-slate-800 group-hover:text-red-600 transition-colors">{{ $stats['rejected'] }}</h3>
                        </div>
                        <div class="p-3 bg-red-50 text-red-500 rounded-2xl group-hover:bg-red-500 group-hover:text-white transition-all shadow-sm">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">

                <div class="lg:col-span-2 bg-white/70 backdrop-blur-xl border border-white/60 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-8">
                    <div class="flex items-center mb-6">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-600 mr-3 animate-pulse">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </span>
                        <h3 class="text-lg font-extrabold text-slate-800">Critical Deadlines <span class="text-slate-400 font-normal text-sm ml-2">(Next 7 Days)</span></h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($upcomingDeadlines as $rl)
                        <a href="{{ route('requisitions.show', $rl->id) }}" class="group block p-4 bg-white border border-slate-100 rounded-2xl hover:border-blue-300 hover:shadow-lg hover:shadow-blue-500/10 transition-all duration-300">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-16 text-center bg-slate-50 rounded-xl py-2 mr-4 border border-slate-100 group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
                                    <span class="block text-2xl font-black leading-none">{{ \Carbon\Carbon::parse($rl->required_date)->format('d') }}</span>
                                    <span class="block text-[10px] uppercase font-bold text-slate-400 group-hover:text-blue-400">{{ \Carbon\Carbon::parse($rl->required_date)->format('M') }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start">
                                        <p class="text-sm font-bold text-slate-800 truncate group-hover:text-blue-600 transition-colors">{{ $rl->rl_no }}</p>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500">
                                            {{ \Carbon\Carbon::parse($rl->required_date)->diffForHumans() }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1 truncate">Req: {{ $rl->requester->full_name }}</p>
                                    <div class="mt-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                            {{ str_replace('_', ' ', $rl->status_flow) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="col-span-2 flex flex-col items-center justify-center py-10 text-center border-2 border-dashed border-slate-200 rounded-2xl">
                            <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <p class="text-slate-400 font-medium">No critical deadlines. Good job!</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white/70 backdrop-blur-xl border border-white/60 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-8 flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-800 mb-2">Workflow Analysis</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Visual distribution of current document status.
                        </p>
                    </div>
                    <div class="relative h-56 w-full mt-4">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="mt-6 pt-6 border-t border-slate-100 flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Active</span>
                        <span class="text-2xl font-black text-slate-800">{{ $stats['waiting_approval'] + $stats['waiting_director'] + $stats['waiting_supply'] }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white/70 backdrop-blur-xl border border-white/60 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
                <div class="p-8 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-lg font-extrabold text-slate-800">Incoming Requests</h3>
                    <button class="text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors">View All &rarr;</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-50/50">
                            <tr>
                                <th class="px-8 py-4 font-bold">RL Number</th>
                                <th class="px-8 py-4 font-bold">Requester</th>
                                <th class="px-8 py-4 font-bold">Subject</th>
                                <th class="px-8 py-4 font-bold">Status</th>
                                <th class="px-8 py-4 font-bold text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($recentActivities as $rl)
                            <tr class="hover:bg-blue-50/30 transition duration-200">
                                <td class="px-8 py-5 font-bold text-slate-700">
                                    <span class="font-mono text-xs text-slate-400 mr-1">#</span>{{ $rl->rl_no }}
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 text-white flex items-center justify-center text-xs font-bold mr-3 shadow-md">
                                            {{ substr($rl->requester->full_name, 0, 1) }}
                                        </div>
                                        <span class="font-medium text-slate-600">{{ $rl->requester->full_name }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-slate-500 italic">"{{ Str::limit($rl->subject, 25) }}"</td>
                                <td class="px-8 py-5">
                                    @php
                                        $statusColor = match($rl->status_flow) {
                                            'ON_PROGRESS' => 'bg-orange-100 text-orange-700 border-orange-200',
                                            'PARTIALLY_APPROVED' => 'bg-purple-100 text-purple-700 border-purple-200',
                                            'APPROVED' => 'bg-green-100 text-green-700 border-green-200',
                                            'WAITING_SUPPLY' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                            'REJECTED' => 'bg-red-100 text-red-700 border-red-200',
                                            'COMPLETED' => 'bg-teal-100 text-teal-700 border-teal-200',
                                            default => 'bg-slate-100 text-slate-600'
                                        };
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold border {{ $statusColor }}">
                                        {{ str_replace('_', ' ', $rl->status_flow) }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <a href="{{ route('requisitions.show', $rl->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white border border-slate-200 text-slate-400 hover:text-blue-600 hover:border-blue-200 hover:shadow-md transition-all">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-10 text-slate-400 italic">No incoming requests found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Chart.defaults.font.family = "'Figtree', sans-serif";
            const ctxStatus = document.getElementById('statusChart').getContext('2d');

            // Gradient Colors for Chart
            const gradientOrange = ctxStatus.createLinearGradient(0, 0, 0, 400);
            gradientOrange.addColorStop(0, '#fb923c'); gradientOrange.addColorStop(1, '#ea580c');

            const gradientPurple = ctxStatus.createLinearGradient(0, 0, 0, 400);
            gradientPurple.addColorStop(0, '#c084fc'); gradientPurple.addColorStop(1, '#9333ea');

            const gradientYellow = ctxStatus.createLinearGradient(0, 0, 0, 400);
            gradientYellow.addColorStop(0, '#facc15'); gradientYellow.addColorStop(1, '#ca8a04');

            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: ['Inbox', 'Director', 'Supply', 'Done', 'Rejected'],
                    datasets: [{
                        data: [{{ $stats['waiting_approval'] }}, {{ $stats['waiting_director'] }}, {{ $stats['waiting_supply'] }}, {{ $stats['completed'] }}, {{ $stats['rejected'] }}],
                        backgroundColor: [
                            '#f97316', // Orange
                            '#a855f7', // Purple
                            '#eab308', // Yellow
                            '#14b8a6', // Teal
                            '#ef4444'  // Red
                        ],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: { position: 'right', labels: { boxWidth: 8, usePointStyle: true, font: { size: 11, family: "'Figtree', sans-serif" } } }
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                }
            });
        });
    </script>
</x-app-layout> --}}



{{-- <x-app-layout>
    <div class="py-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex flex-col md:flex-row justify-between items-end mb-8">
                <div>
                    <h2 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-orange-600 tracking-tight">
                        Approval Center
                    </h2>
                    <p class="text-sm text-gray-500 mt-2 font-medium">
                        Hello, {{ Auth::user()->full_name }}. Manage your team's requests and approvals.
                    </p>
                </div>
                <div class="mt-4 md:mt-0 p-1 bg-white rounded-xl shadow-md border flex items-center">
                    <span class="px-4 py-2 rounded-lg text-xs font-bold bg-gray-100 text-gray-800 shadow-inner">Approver View</span>
                    <a href="{{ route('dashboard.select_role', 'requester') }}" class="px-4 py-2 rounded-lg text-xs font-bold text-gray-500 hover:text-purple-600 transition">Requester View</a>
                </div>
            </div>

            @if($stats['my_actions'] > 0)
            <div class="mb-8 relative overflow-hidden rounded-2xl shadow-xl shadow-red-500/20 animate-pulse">
                <div class="absolute inset-0 bg-gradient-to-r from-red-600 via-orange-500 to-red-500"></div>
                <div class="relative bg-white m-[2px] rounded-2xl p-4 flex flex-col md:flex-row justify-between items-center">
                    <div class="flex items-center mb-3 md:mb-0">
                        <div class="p-3 bg-red-100 text-red-600 rounded-full mr-4"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
                        <div>
                            <h4 class="font-extrabold text-gray-800 text-lg">Action Required!</h4>
                            <p class="text-sm text-gray-600">You have <strong class="text-red-600">{{ $stats['my_actions'] }} requests</strong> awaiting your approval.</p>
                        </div>
                    </div>
                    <a href="{{ route('requisitions.status', 'on_progress') }}" class="bg-red-600 text-white font-bold py-2 px-6 rounded-xl hover:bg-red-700 transition">Review Now &rarr;</a>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
                <div class="relative overflow-hidden rounded-2xl shadow-lg shadow-orange-500/20 bg-gradient-to-br from-orange-400 to-red-500 p-5 text-white">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-orange-100 text-[10px] font-bold uppercase tracking-wider">Inbox (Your Task)</p>
                            <h3 class="text-3xl font-extrabold mt-1">{{ $stats['waiting_approval'] }}</h3>
                        </div>
                        <div class="p-2 bg-white/20 rounded-lg"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg></div>
                    </div>
                </div>
                <div class="relative overflow-hidden rounded-2xl shadow-lg shadow-purple-500/20 bg-gradient-to-br from-purple-500 to-indigo-600 p-5 text-white">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-purple-100 text-[10px] font-bold uppercase tracking-wider">Director Phase</p>
                            <h3 class="text-3xl font-extrabold mt-1">{{ $stats['waiting_director'] }}</h3>
                        </div>
                        <div class="p-2 bg-white/20 rounded-lg"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg></div>
                    </div>
                </div>
                 <div class="relative overflow-hidden rounded-2xl shadow-lg shadow-yellow-500/20 bg-gradient-to-br from-yellow-400 to-orange-500 p-5 text-white">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-yellow-100 text-[10px] font-bold uppercase tracking-wider">Procurement Phase</p>
                            <h3 class="text-3xl font-extrabold mt-1">{{ $stats['waiting_supply'] }}</h3>
                        </div>
                         <div class="p-2 bg-white/20 rounded-lg"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg></div>
                    </div>
                </div>
                <div class="relative overflow-hidden rounded-2xl shadow-lg shadow-teal-500/20 bg-gradient-to-br from-teal-400 to-emerald-600 p-5 text-white">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-teal-100 text-[10px] font-bold uppercase tracking-wider">Finished</p>
                            <h3 class="text-3xl font-extrabold mt-1">{{ $stats['completed'] }}</h3>
                        </div>
                         <div class="p-2 bg-white/20 rounded-lg"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></div>
                    </div>
                </div>
                <div class="relative overflow-hidden rounded-2xl shadow-lg shadow-red-500/20 bg-gradient-to-br from-red-500 to-pink-700 p-5 text-white">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-red-100 text-[10px] font-bold uppercase tracking-wider">Declined</p>
                            <h3 class="text-3xl font-extrabold mt-1">{{ $stats['rejected'] }}</h3>
                        </div>
                         <div class="p-2 bg-white/20 rounded-lg"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">

                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-md border-t-4 border-red-500 p-6">
                    <h3 class="text-lg font-extrabold text-gray-800 dark:text-white mb-4 flex items-center">
                        <span class="mr-2">🔥</span> Critical Deadlines (Upcoming 7 Days)
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                         @forelse($upcomingDeadlines as $rl)
                         <div class="flex items-center p-3 border border-gray-100 rounded-xl hover:shadow-md transition">
                            <div class="flex-shrink-0 w-16 text-center bg-red-50 text-red-600 rounded-lg py-2 mr-4">
                                <span class="block text-2xl font-black leading-none">{{ \Carbon\Carbon::parse($rl->required_date)->format('d') }}</span>
                                <span class="block text-[10px] uppercase font-bold">{{ \Carbon\Carbon::parse($rl->required_date)->format('M') }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-900 truncate">{{ $rl->rl_no }}</p>
                                <p class="text-xs text-gray-500 truncate mb-1">Req: {{ $rl->requester->full_name }}</p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-700">
                                    {{ str_replace('_', ' ', $rl->status_flow) }}
                                </span>
                            </div>
                         </div>
                         @empty
                         <div class="col-span-2 text-center py-8 text-gray-400 italic">No critical deadlines approaching.</div>
                         @endforelse
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md border-t-4 border-teal-500">
                     <h3 class="text-lg font-extrabold text-gray-800 dark:text-white mb-4">
                        Workflow Bottleneck Analysis
                     </h3>
                     <p class="text-xs text-gray-500 mb-4">
                        This chart shows where requests are currently stuck in the process.
                     </p>
                     <div class="relative h-48 w-full"><canvas id="statusChart"></canvas></div>
                     <div class="mt-4 text-center">
                        <div class="text-xs font-bold text-gray-600">Pending Actions: {{ $stats['waiting_approval'] + $stats['waiting_director'] + $stats['waiting_supply'] }}</div>
                     </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border-t-4 border-indigo-500 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-extrabold text-gray-800 dark:text-white">Incoming Requests</h3>
                    <a href="#" class="text-sm font-bold text-indigo-600">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-indigo-900 bg-indigo-50 dark:bg-gray-700 dark:text-gray-300 uppercase">
                            <tr>
                                <th class="px-6 py-4 font-bold">RL Number</th>
                                <th class="px-6 py-4 font-bold">Requester</th>
                                <th class="px-6 py-4 font-bold">Subject</th>
                                <th class="px-6 py-4 font-bold">Status</th>
                                <th class="px-6 py-4 font-bold text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($recentActivities as $rl)
                            <tr class="hover:bg-indigo-50/30 transition">
                                <td class="px-6 py-4 font-bold text-gray-800">{{ $rl->rl_no }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $rl->requester->full_name }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ Str::limit($rl->subject, 20) }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded text-[10px] font-bold bg-blue-100 text-blue-700">
                                        {{ str_replace('_', ' ', $rl->status_flow) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('requisitions.show', $rl->id) }}" class="text-indigo-600 font-bold hover:underline">Review</a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-6 text-gray-400">No incoming requests.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Chart.defaults.font.family = "'Figtree', sans-serif";
            const ctxStatus = document.getElementById('statusChart').getContext('2d');
            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: ['Inbox', 'Director', 'Supply', 'Done', 'Rejected'],
                    datasets: [{
                        data: [{{ $stats['waiting_approval'] }}, {{ $stats['waiting_director'] }}, {{ $stats['waiting_supply'] }}, {{ $stats['completed'] }}, {{ $stats['rejected'] }}],
                        backgroundColor: ['#f97316', '#9333ea', '#eab308', '#14b8a6', '#ef4444'],
                        borderWidth: 0
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'right', labels: { boxWidth: 10, font: { size: 10 } } } } }
            });
        });
    </script>
</x-app-layout> --}}
