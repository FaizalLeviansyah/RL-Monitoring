<x-app-layout>
    <div class="px-4 pt-6">
        
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Command Center (Super Admin)</h1>
            <button class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">Generate Global Report</button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex justify-between mb-4">
                    <div>
                        <div class="mb-2 text-sm font-medium text-gray-500 dark:text-gray-400">Total User (All PT)</div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalEmployees }}</div>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full dark:bg-purple-900">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                </div>
                <div class="text-sm text-green-500 font-bold">+12% <span class="text-gray-500 font-normal">vs last month</span></div>
            </div>
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex justify-between mb-4">
                    <div>
                        <div class="mb-2 text-sm font-medium text-gray-500 dark:text-gray-400">Global RL Transactions</div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalRL }}</div>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full dark:bg-blue-900">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                </div>
            </div>
             <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex justify-between mb-4">
                    <div>
                        <div class="mb-2 text-sm font-medium text-gray-500 dark:text-gray-400">Global Pending Approval</div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalPending }}</div>
                    </div>
                    <div class="p-3 bg-orange-100 rounded-full dark:bg-orange-900">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 mb-4">
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h3 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Permintaan per Perusahaan (PT)</h3>
                <div id="company-chart"></div>
            </div>
            
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h3 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Live Activity Feed</h3>
                <ul class="relative border-l border-gray-200 dark:border-gray-700 ml-2">
                    @foreach($globalActivities as $act)
                    <li class="mb-4 ml-6">
                        <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -left-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                            <span class="text-xs font-bold text-blue-600">{{ substr($act->company->company_code, 0, 1) }}</span>
                        </span>
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 dark:bg-gray-700 dark:border-gray-600">
                            <div class="justify-between items-center mb-1 sm:flex">
                                <time class="mb-1 text-xs font-normal text-gray-400 sm:order-last sm:mb-0">{{ $act->created_at->diffForHumans() }}</time>
                                <div class="text-sm font-normal text-gray-500 dark:text-gray-300">
                                    New RL from <span class="font-semibold text-gray-900 dark:text-white">{{ $act->company->company_code }}</span>
                                </div>
                            </div>
                            <div class="p-2 text-xs italic border border-gray-200 rounded-md bg-white dark:bg-gray-800 dark:border-gray-600 text-gray-500">
                                "{{ Str::limit($act->subject, 50) }}" - by {{ $act->requester->full_name }}
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        const options = {
            series: [{
                name: 'Total RL',
                data: {{ json_encode($chartSeries) }}
            }],
            chart: { type: 'bar', height: 350 },
            xaxis: { categories: {!! json_encode($chartLabels) !!} },
            colors: ['#1C64F2', '#16BDCA', '#FDBA8C'],
            plotOptions: { bar: { borderRadius: 4, horizontal: true } }
        };
        new ApexCharts(document.querySelector("#company-chart"), options).render();
    </script>
</x-app-layout>