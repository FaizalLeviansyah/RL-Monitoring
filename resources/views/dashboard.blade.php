{{-- TAMPILAN VERSI 3  --}}
<x-app-layout>

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

            @if($isApprover)
            <div class="inline-flex items-center justify-center bg-gray-900 text-green-400 font-mono font-bold text-xl px-3 py-1.5 rounded-lg shadow-md border border-gray-700 tracking-widest digital-clock">
                <span id="clock-hours">00</span>
                <span class="animate-pulse mx-1">:</span>
                <span id="clock-minutes">00</span>
                <span class="animate-pulse mx-1">:</span>
                <span id="clock-seconds" class="text-green-600 text-lg">00</span>
            </div>
            @endif

            @if($isApprover)
            <div class="mt-2">
                <a href="{{ route('dashboard.select_role', 'reset') }}" class="text-xs font-medium text-blue-600 hover:text-blue-800 hover:underline flex items-center justify-end transition-colors">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    Ganti Mode (Switch Role)
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

    @if($isApprover)
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
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800 h-fit">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Grafik Status</h3>
            <div id="status-chart" class="w-full"></div>
        </div>

        <div class="lg:col-span-2 p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Aktivitas Terbaru</h3>

                @if($isApprover)
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
            // 1. CHART CONFIG
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

            // 2. REALTIME CLOCK (UPDATE KEREN)
            function updateClock() {
                const now = new Date();
                const h = String(now.getHours()).padStart(2, '0');
                const m = String(now.getMinutes()).padStart(2, '0');
                const s = String(now.getSeconds()).padStart(2, '0');

                // Update elemen ID jika ada
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
</x-app-layout>

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
