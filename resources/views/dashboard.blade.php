<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard Overview</h1>
        <p class="text-gray-500 dark:text-gray-400">
            Welcome back, <span class="font-semibold text-blue-600">{{ Auth::user()->full_name }}</span>!
            <span class="text-xs bg-gray-200 text-gray-800 px-2 py-1 rounded ml-2">{{ $isApprover ? 'Approver View' : 'Requester View' }}</span>
        </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex flex-col">
                <dt class="text-3xl font-extrabold text-gray-900 dark:text-white">{{ $countTotal }}</dt>
                <dd class="text-gray-500 dark:text-gray-400 text-sm">Total Activity</dd>
            </div>
        </div>

        <div class="p-4 bg-white border-l-4 border-gray-400 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-500">
            <div class="flex justify-between items-center">
                <div>
                    <dt class="text-2xl font-bold text-gray-700 dark:text-gray-300">{{ $countDraft }}</dt>
                    <dd class="text-gray-500 dark:text-gray-400 text-sm">Drafts</dd>
                </div>
                <div class="p-2 bg-gray-100 rounded-full dark:bg-gray-700">
                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
            </div>
        </div>

        <div class="p-4 bg-white border-l-4 border-orange-400 rounded-lg shadow-sm dark:bg-gray-800 dark:border-orange-500">
            <div class="flex justify-between items-center">
                <div>
                    <dt class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $countPending }}</dt>
                    <dd class="text-gray-500 dark:text-gray-400 text-sm">Waiting</dd>
                </div>
                <div class="p-2 bg-orange-100 rounded-full dark:bg-orange-900">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="p-4 bg-white border-l-4 border-green-400 rounded-lg shadow-sm dark:bg-gray-800 dark:border-green-500">
            <div class="flex justify-between items-center">
                <div>
                    <dt class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $countApproved }}</dt>
                    <dd class="text-gray-500 dark:text-gray-400 text-sm">Approved</dd>
                </div>
                <div class="p-2 bg-green-100 rounded-full dark:bg-green-900">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
        </div>

        <div class="p-4 bg-white border-l-4 border-red-400 rounded-lg shadow-sm dark:bg-gray-800 dark:border-red-500">
            <div class="flex justify-between items-center">
                <div>
                    <dt class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $countRejected }}</dt>
                    <dd class="text-gray-500 dark:text-gray-400 text-sm">Rejected</dd>
                </div>
                <div class="p-2 bg-red-100 rounded-full dark:bg-red-900">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
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
                <a href="#" class="text-sm text-blue-600 hover:underline dark:text-blue-500">Lihat Semua</a>
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
                                    // Logic status untuk tampilan tabel ringkas
                                    $status = $rl->status_flow;
                                    if($isApprover) {
                                        // Kalau approver, cek status queue dia
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
            // Data dari Controller
            const chartData = {{ $chartData }}; // [Draft, Pending, Approved, Rejected]

            const options = {
                series: [{
                    name: 'Jumlah Dokumen',
                    data: chartData
                }],
                chart: {
                    height: 350,
                    type: 'bar', // Bisa diganti 'pie' atau 'donut'
                    toolbar: { show: false },
                    fontFamily: 'Figtree, sans-serif',
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: false,
                        columnWidth: '50%',
                        distributed: true // Agar warna beda tiap bar
                    }
                },
                colors: ['#9CA3AF', '#F97316', '#22C55E', '#EF4444'], // Abu, Orange, Hijau, Merah
                dataLabels: { enabled: false },
                legend: { show: false },
                xaxis: {
                    categories: ['Draft', 'Pending', 'Approved', 'Rejected'],
                    labels: {
                        style: { fontSize: '12px' }
                    }
                },
                grid: {
                    borderColor: '#f1f1f1',
                }
            };

            const chart = new ApexCharts(document.querySelector("#status-chart"), options);
            chart.render();
        });
    </script>

</x-app-layout>