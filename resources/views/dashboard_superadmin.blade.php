<x-app-layout>
    <div class="px-6 py-8">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">COMMAND CENTER</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex items-center">
                    <span class="w-2 h-2 rounded-full bg-green-500 mr-2 animate-pulse"></span>
                    System Operational & Global Monitoring
                </p>
            </div>
            
            <div class="flex flex-wrap gap-3">
                <button class="flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:text-white transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Export Report
                </button>
                
                <a href="{{ route('admin.users.index') }}" class="flex items-center px-5 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg hover:from-blue-700 hover:to-blue-800 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-900 shadow-lg shadow-blue-500/30 transform hover:-translate-y-0.5 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Manage Users
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="relative p-6 bg-gradient-to-br from-purple-600 to-indigo-700 rounded-2xl shadow-xl text-white overflow-hidden group hover:shadow-2xl transition-all duration-300">
                <div class="absolute right-0 top-0 h-full w-full bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-purple-200 text-xs font-bold uppercase tracking-wider">Total Personnel</p>
                            <h3 class="text-4xl font-extrabold mt-2">{{ $totalEmployees }}</h3>
                        </div>
                        <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm shadow-inner">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                    </div>
                    <div class="mt-4 text-sm text-purple-200 flex items-center font-medium">
                        <span class="bg-white/20 text-white px-2 py-0.5 rounded text-xs mr-2">Active</span>
                        Registered Accounts
                    </div>
                </div>
            </div>

            <div class="relative p-6 bg-gradient-to-br from-blue-600 to-cyan-600 rounded-2xl shadow-xl text-white overflow-hidden group hover:shadow-2xl transition-all duration-300">
                <div class="absolute right-0 top-0 h-full w-full bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-blue-100 text-xs font-bold uppercase tracking-wider">Total Requisitions</p>
                            <h3 class="text-4xl font-extrabold mt-2">{{ $totalRL }}</h3>
                        </div>
                        <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm shadow-inner">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                    </div>
                    <div class="mt-4 text-sm text-blue-100 font-medium">
                        All Time Transactions
                    </div>
                </div>
            </div>

            <div class="relative p-6 bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl shadow-xl text-white overflow-hidden group hover:shadow-2xl transition-all duration-300">
                <div class="absolute right-0 top-0 h-full w-full bg-[url('https://www.transparenttextures.com/patterns/diagonal-stripes.png')] opacity-10"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-orange-100 text-xs font-bold uppercase tracking-wider">Pending Approval</p>
                            <h3 class="text-4xl font-extrabold mt-2">{{ $totalPending }}</h3>
                        </div>
                        <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm shadow-inner animate-pulse">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <div class="mt-4 text-sm text-orange-100 font-medium">
                        Need Immediate Action
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
            
            <div class="xl:col-span-2 p-6 bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Distribusi Permintaan per Perusahaan</h3>
                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">Live Data</span>
                </div>
                <div id="company-chart" class="w-full h-80"></div>
            </div>
            
            <div class="p-6 bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700 flex flex-col h-full">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                    <span class="relative flex h-3 w-3 mr-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                    </span>
                    Live Activity Feed
                </h3>
                
                <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar">
                    <ul role="list" class="space-y-4">
                        @foreach($globalActivities as $act)
                        <li class="relative pl-6 border-l-2 border-gray-200 dark:border-gray-700 last:border-0 pb-4">
                            <span class="absolute -left-[9px] top-0 flex items-center justify-center w-4 h-4 bg-white rounded-full ring-4 ring-white dark:ring-gray-800 dark:bg-gray-800">
                                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                            </span>
                            <div class="flex flex-col">
                                <div class="flex justify-between items-start">
                                    <span class="text-xs font-semibold text-gray-900 dark:text-white">{{ $act->requester->full_name }}</span>
                                    <span class="text-[10px] text-gray-400">{{ $act->created_at->diffForHumans(null, true) }}</span>
                                </div>
                                <span class="text-xs font-bold text-blue-600 mt-0.5">{{ $act->company->company_code }}</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">
                                    Created RL: "{{ str($act->subject)->limit(40) }}"
                                </p>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 text-center">
                    <a href="#" class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-500">View All History</a>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // FIX: Gunakan JSON.parse atau blade echo tanpa kutip agar aman
            const options = {
                series: [{
                    name: 'Total RL',
                    data: @json($chartSeries)
                }],
                chart: { type: 'bar', height: 320, toolbar: { show: false }, fontFamily: 'Figtree, sans-serif' },
                xaxis: { 
                    categories: @json($chartLabels),
                    labels: { style: { colors: '#9CA3AF', fontSize: '12px' } }
                },
                plotOptions: {
                    bar: { borderRadius: 4, horizontal: true, barHeight: '50%', distributed: true }
                },
                colors: ['#3B82F6', '#10B981', '#F59E0B'],
                dataLabels: { enabled: true, textAnchor: 'start', style: { colors: ['#fff'] }, offsetX: 0 },
                grid: { show: true, borderColor: '#f3f4f6', strokeDashArray: 4 },
                tooltip: { theme: 'light' }
            };
            new ApexCharts(document.querySelector("#company-chart"), options).render();
        });
    </script>
</x-app-layout>