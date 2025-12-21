<x-app-layout>
    <div class="py-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex flex-col md:flex-row justify-between items-end mb-8">
                <div>
                    <h2 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 tracking-tight">
                        My Dashboard
                    </h2>
                    <p class="text-sm text-gray-500 mt-2 font-medium">
                        Welcome back, <span class="text-gray-800 dark:text-gray-200 font-bold">{{ Auth::user()->full_name }}</span>.
                        Here is your request overview.
                    </p>
                </div>
                
                <div class="flex flex-col md:flex-row gap-3 mt-4 md:mt-0">
                    <a href="{{ route('requisitions.create') }}" class="group relative inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white transition-all duration-200 bg-blue-600 font-pj rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-500/30">
                        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Create New Request
                    </a>
                    
                    @if($isApprover)
                    <div class="p-1 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center">
                        <a href="{{ route('dashboard.select_role', 'approver') }}" class="px-4 py-2 rounded-lg text-xs font-bold text-gray-500 hover:text-blue-600 transition">Approver View</a>
                        <span class="px-4 py-2 rounded-lg text-xs font-bold bg-gray-100 text-gray-800 shadow-inner">Requester View</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-5 mb-8">
                <div class="relative overflow-hidden rounded-2xl shadow-lg shadow-orange-500/20 group hover:-translate-y-1 transition duration-300 bg-gradient-to-br from-orange-400 to-red-500">
                    <div class="absolute -right-4 -bottom-4 opacity-20 transform rotate-12 scale-150">
                        <svg class="w-24 h-24 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div class="relative p-5 text-white">
                        <p class="text-orange-100 text-[10px] font-bold uppercase tracking-wider">Processing</p>
                        <h3 class="text-3xl font-extrabold mt-1">{{ $stats['waiting_approval'] }}</h3>
                        <div class="mt-2 text-[10px] font-medium bg-white/20 w-fit px-2 py-0.5 rounded backdrop-blur-sm">With Manager</div>
                    </div>
                </div>

                <div class="relative overflow-hidden rounded-2xl shadow-lg shadow-purple-500/20 group hover:-translate-y-1 transition duration-300 bg-gradient-to-br from-purple-500 to-indigo-600">
                    <div class="absolute -right-4 -bottom-4 opacity-20 transform -rotate-12 scale-150">
                        <svg class="w-24 h-24 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </div>
                    <div class="relative p-5 text-white">
                        <p class="text-purple-100 text-[10px] font-bold uppercase tracking-wider">Director Review</p>
                        <h3 class="text-3xl font-extrabold mt-1">{{ $stats['waiting_director'] }}</h3>
                        <div class="mt-2 text-[10px] font-medium bg-white/20 w-fit px-2 py-0.5 rounded backdrop-blur-sm">Final Approval</div>
                    </div>
                </div>

                <div class="relative overflow-hidden rounded-2xl shadow-lg shadow-yellow-500/20 group hover:-translate-y-1 transition duration-300 bg-gradient-to-br from-yellow-400 to-orange-500">
                    <div class="absolute -right-4 -bottom-4 opacity-20 transform rotate-6 scale-150">
                        <svg class="w-24 h-24 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                    </div>
                    <div class="relative p-5 text-white">
                        <p class="text-yellow-100 text-[10px] font-bold uppercase tracking-wider">Procurement</p>
                        <h3 class="text-3xl font-extrabold mt-1">{{ $stats['waiting_supply'] }}</h3>
                        <div class="mt-2 text-[10px] font-medium bg-white/20 w-fit px-2 py-0.5 rounded backdrop-blur-sm">Waiting Goods</div>
                    </div>
                </div>

                <div class="relative overflow-hidden rounded-2xl shadow-lg shadow-teal-500/20 group hover:-translate-y-1 transition duration-300 bg-gradient-to-br from-teal-400 to-emerald-600">
                    <div class="absolute -right-4 -bottom-4 opacity-20 transform rotate-12 scale-150">
                        <svg class="w-24 h-24 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div class="relative p-5 text-white">
                        <p class="text-teal-100 text-[10px] font-bold uppercase tracking-wider">Completed</p>
                        <h3 class="text-3xl font-extrabold mt-1">{{ $stats['completed'] }}</h3>
                        <div class="mt-2 text-[10px] font-medium bg-white/20 w-fit px-2 py-0.5 rounded backdrop-blur-sm">Success</div>
                    </div>
                </div>

                <div class="relative overflow-hidden rounded-2xl shadow-lg shadow-red-500/20 group hover:-translate-y-1 transition duration-300 bg-gradient-to-br from-red-500 to-pink-700">
                    <div class="absolute -right-4 -bottom-4 opacity-20 transform -rotate-12 scale-150">
                        <svg class="w-24 h-24 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div class="relative p-5 text-white">
                        <p class="text-red-100 text-[10px] font-bold uppercase tracking-wider">Rejected</p>
                        <h3 class="text-3xl font-extrabold mt-1">{{ $stats['rejected'] }}</h3>
                        <div class="mt-2 text-[10px] font-medium bg-white/20 w-fit px-2 py-0.5 rounded backdrop-blur-sm">Action Needed</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-md border-t-4 border-blue-500 p-6">
                     <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-extrabold text-gray-800 dark:text-white flex items-center">
                            <span class="mr-2">📂</span> My Recent Requests
                        </h3>
                        <a href="{{ route('requisitions.status', 'on_progress') }}" class="text-sm font-bold text-blue-600 hover:text-blue-800">View All</a>
                     </div>
                     
                     @include('dashboard.partials.activity_table')
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md border-t-4 border-indigo-500">
                     <h3 class="text-lg font-extrabold text-gray-800 dark:text-white mb-4 flex items-center">
                        <span class="mr-2">⏱️</span> Deadline Analysis
                     </h3>
                     
                     <div class="relative h-48 w-full mb-4">
                        <canvas id="priorityChart"></canvas>
                     </div>

                     <div class="grid grid-cols-2 gap-2 mb-4">
                        <div class="bg-red-50 p-2 rounded border border-red-100 text-center">
                            <span class="block text-xs font-bold text-red-600">Top Urgent</span>
                            <span class="block text-lg font-black text-gray-800">{{ $priorityStats['Top Urgent'] }}</span>
                        </div>
                        <div class="bg-orange-50 p-2 rounded border border-orange-100 text-center">
                            <span class="block text-xs font-bold text-orange-600">Urgent</span>
                            <span class="block text-lg font-black text-gray-800">{{ $priorityStats['Urgent'] }}</span>
                        </div>
                        <div class="bg-blue-50 p-2 rounded border border-blue-100 text-center">
                            <span class="block text-xs font-bold text-blue-600">Normal</span>
                            <span class="block text-lg font-black text-gray-800">{{ $priorityStats['Normal'] }}</span>
                        </div>
                        <div class="bg-gray-100 p-2 rounded border border-gray-200 text-center">
                            <span class="block text-xs font-bold text-gray-600">Overdue</span>
                            <span class="block text-lg font-black text-gray-800">{{ $priorityStats['Outstanding'] }}</span>
                        </div>
                     </div>

                     <div class="bg-indigo-50 dark:bg-gray-700/50 p-3 rounded-lg border border-indigo-100 dark:border-gray-600">
                        <h4 class="text-xs font-bold text-indigo-800 dark:text-indigo-300 mb-2 uppercase tracking-wider">ℹ️ Rules & Information</h4>
                        <ul class="space-y-1.5">
                            <li class="flex items-start text-[10px] text-gray-600 dark:text-gray-400">
                                <span class="w-2 h-2 rounded-full bg-red-500 mt-0.5 mr-2 flex-shrink-0"></span>
                                <span><strong>Top Urgent:</strong> Deadline is ≤ 2 Days. Immediate action required.</span>
                            </li>
                            <li class="flex items-start text-[10px] text-gray-600 dark:text-gray-400">
                                <span class="w-2 h-2 rounded-full bg-orange-500 mt-0.5 mr-2 flex-shrink-0"></span>
                                <span><strong>Urgent:</strong> Deadline is between 3 to 5 Days.</span>
                            </li>
                            <li class="flex items-start text-[10px] text-gray-600 dark:text-gray-400">
                                <span class="w-2 h-2 rounded-full bg-blue-500 mt-0.5 mr-2 flex-shrink-0"></span>
                                <span><strong>Normal:</strong> Deadline is > 5 Days. Safe processing time.</span>
                            </li>
                            <li class="flex items-start text-[10px] text-gray-600 dark:text-gray-400">
                                <span class="w-2 h-2 rounded-full bg-gray-800 mt-0.5 mr-2 flex-shrink-0"></span>
                                <span><strong>Overdue:</strong> Passed the required date.</span>
                            </li>
                        </ul>
                     </div>
                </div>
            </div>

        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Chart.defaults.font.family = "'Figtree', sans-serif";
            const ctxPriority = document.getElementById('priorityChart').getContext('2d');
            new Chart(ctxPriority, {
                type: 'bar',
                data: {
                    labels: ['Top Urgent', 'Urgent', 'Normal', 'Overdue'],
                    datasets: [{
                        label: 'Requests',
                        data: [{{ $priorityStats['Top Urgent'] }}, {{ $priorityStats['Urgent'] }}, {{ $priorityStats['Normal'] }}, {{ $priorityStats['Outstanding'] }}],
                        backgroundColor: ['#ef4444', '#f97316', '#3b82f6', '#1f2937'],
                        borderRadius: 6, barThickness: 30
                    }]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false, 
                    plugins: { legend: { display: false } },
                    scales: { y: { display: false }, x: { grid: { display: false } } }
                }
            });
        });
    </script>
</x-app-layout>