<x-app-layout>
    <div class="mb-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard Overview</h1>
        <p class="text-gray-500 dark:text-gray-400">Welcome back, {{ Auth::user()->full_name }}!</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">

        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex flex-row items-center justify-between">
                <div class="flex flex-col">
                    <dt class="mb-2 text-3xl font-extrabold text-blue-600 dark:text-blue-400">{{ $myTotal }}</dt>
                    <dd class="text-gray-500 dark:text-gray-400">Total Permintaan</dd>
                </div>
                <div class="p-2 bg-blue-100 rounded-lg dark:bg-blue-900">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/></svg>
                </div>
            </div>
        </div>

        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex flex-row items-center justify-between">
                <div class="flex flex-col">
                    <dt class="mb-2 text-3xl font-extrabold text-orange-500 dark:text-orange-400">{{ $myPending }}</dt>
                    <dd class="text-gray-500 dark:text-gray-400">Menunggu Approval</dd>
                </div>
                <div class="p-2 bg-orange-100 rounded-lg dark:bg-orange-900">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-300" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg>
                </div>
            </div>
        </div>

        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex flex-row items-center justify-between">
                <div class="flex flex-col">
                    <dt class="mb-2 text-3xl font-extrabold text-green-500 dark:text-green-400">{{ $myApproved }}</dt>
                    <dd class="text-gray-500 dark:text-gray-400">Disetujui</dd>
                </div>
                <div class="p-2 bg-green-100 rounded-lg dark:bg-green-900">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                </div>
            </div>
        </div>

        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex flex-row items-center justify-between">
                <div class="flex flex-col">
                    <dt class="mb-2 text-3xl font-extrabold text-red-500 dark:text-red-400">{{ $myRejected }}</dt>
                    <dd class="text-gray-500 dark:text-gray-400">Ditolak</dd>
                </div>
                <div class="p-2 bg-red-100 rounded-lg dark:bg-red-900">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Recent Activities</h3>
        <p class="text-sm text-gray-500">Belum ada aktivitas.</p>
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Recent Requisitions</h3>
            <a href="#" class="text-sm text-blue-600 hover:underline dark:text-blue-500">View All</a>
        </div>

        <div class="relative overflow-x-auto sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">RL Number</th>
                        <th scope="col" class="px-6 py-3">Date</th>
                        <th scope="col" class="px-6 py-3">Subject</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent_rls as $rl)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $rl->rl_no }}
                        </th>

                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::parse($rl->request_date)->format('d M Y') }}
                        </td>

                        <td class="px-6 py-4">
                            {{ Str::limit($rl->subject, 30) }}
                        </td>

                        <td class="px-6 py-4">
                            @if($rl->status_flow == 'ON_PROGRESS')
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">Pending</span>
                            @elseif($rl->status_flow == 'APPROVED')
                                <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Approved</span>
                            @elseif($rl->status_flow == 'REJECTED')
                                <span class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Rejected</span>
                            @else
                                <span class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">Draft</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center">Tidak ada data permintaan terbaru.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
    </div>
</x-app-layout>
