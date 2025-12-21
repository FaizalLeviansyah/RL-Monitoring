<x-app-layout>
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
                
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md border-t-4 border-red-500">
                    <h3 class="text-lg font-extrabold text-gray-800 dark:text-white mb-4 flex items-center">
                        <span class="mr-2">🔥</span> Deadline Risk Analysis
                    </h3>
                    
                    <div class="flex flex-col md:flex-row gap-6">
                        <div class="relative h-48 w-full md:w-2/3">
                            <canvas id="priorityChart"></canvas>
                        </div>

                        <div class="w-full md:w-1/3 bg-red-50 dark:bg-gray-700/50 p-4 rounded-xl border border-red-100 dark:border-gray-600">
                            <h4 class="text-xs font-bold text-red-800 dark:text-red-300 mb-3 uppercase tracking-wider">ℹ️ Priority Rules</h4>
                            <ul class="space-y-3">
                                <li class="flex items-start text-[11px] text-gray-700 dark:text-gray-300 leading-tight">
                                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 mt-0.5 mr-2 flex-shrink-0"></span>
                                    <span><strong class="text-red-700">Top Urgent (≤ 2 Days):</strong> High priority. Needs immediate approval.</span>
                                </li>
                                <li class="flex items-start text-[11px] text-gray-700 dark:text-gray-300 leading-tight">
                                    <span class="w-2.5 h-2.5 rounded-full bg-orange-500 mt-0.5 mr-2 flex-shrink-0"></span>
                                    <span><strong class="text-orange-700">Urgent (3-5 Days):</strong> Moderate priority. Schedule for review.</span>
                                </li>
                                <li class="flex items-start text-[11px] text-gray-700 dark:text-gray-300 leading-tight">
                                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500 mt-0.5 mr-2 flex-shrink-0"></span>
                                    <span><strong class="text-blue-700">Normal (> 5 Days):</strong> Standard priority. Safe processing time.</span>
                                </li>
                                <li class="flex items-start text-[11px] text-gray-700 dark:text-gray-300 leading-tight">
                                    <span class="w-2.5 h-2.5 rounded-full bg-gray-800 mt-0.5 mr-2 flex-shrink-0"></span>
                                    <span><strong>Overdue:</strong> Request is delayed. Check bottleneck.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md border-t-4 border-teal-500">
                     <h3 class="text-lg font-extrabold text-gray-800 dark:text-white mb-4">
                        Workflow Bottleneck
                     </h3>
                     <p class="text-xs text-gray-500 mb-4">
                        Where requests are currently stuck in the process.
                     </p>
                     <div class="relative h-48 w-full"><canvas id="statusChart"></canvas></div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md border-t-4 border-blue-600">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-extrabold text-gray-800 dark:text-white flex items-center">
                            <span class="mr-2">📈</span> Monthly Request Trend
                        </h3>
                        <span class="text-xs font-bold bg-blue-100 text-blue-600 px-3 py-1 rounded-full">Last 6 Months</span>
                    </div>
                    <div class="relative h-56 w-full">
                        <canvas id="trendChart"></canvas>
                    </div>
                    <p class="text-xs text-gray-400 mt-4 text-center">
                        Analysis of request volume over time to identify peak periods.
                    </p>
                </div>

                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-md border-t-4 border-indigo-500 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-extrabold text-gray-800 dark:text-white">Incoming Requests</h3>
                        <a href="#" class="text-sm font-bold text-indigo-600">View All</a>
                    </div>
                    
                    @include('dashboard.partials.activity_table')
                </div>

            </div>

        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Chart.defaults.font.family = "'Figtree', sans-serif";
            
            // 1. Priority Chart
            const ctxPriority = document.getElementById('priorityChart').getContext('2d');
            new Chart(ctxPriority, {
                type: 'bar',
                data: {
                    labels: ['Top Urgent', 'Urgent', 'Normal', 'Overdue'],
                    datasets: [{
                        label: 'Requests',
                        data: [{{ $priorityStats['Top Urgent'] }}, {{ $priorityStats['Urgent'] }}, {{ $priorityStats['Normal'] }}, {{ $priorityStats['Outstanding'] }}],
                        backgroundColor: ['#ef4444', '#f97316', '#3b82f6', '#1f2937'],
                        borderRadius: 6, barThickness: 35
                    }]
                },
                options: { 
                    responsive: true, maintainAspectRatio: false, 
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true }, x: { grid: { display: false } } }
                }
            });

            // 2. Status/Bottleneck Chart
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

            // 3. NEW TREND CHART (Line Chart)
            const ctxTrend = document.getElementById('trendChart').getContext('2d');
            new Chart(ctxTrend, {
                type: 'line',
                data: {
                    labels: @json($trendData['labels']),
                    datasets: [{
                        label: 'Request Volume',
                        data: @json($trendData['data']),
                        borderColor: '#2563eb', // Blue 600
                        backgroundColor: 'rgba(37, 99, 235, 0.1)', // Blue transparent
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#2563eb',
                        pointRadius: 4,
                        fill: true,
                        tension: 0.4 // Curve smoothing
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { borderDash: [2, 4] } },
                        x: { grid: { display: false } }
                    }
                }
            });
        });
    </script>
</x-app-layout>