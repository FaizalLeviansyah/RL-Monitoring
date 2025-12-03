<x-app-layout>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        
        <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:p-6 dark:border-gray-700 dark:bg-gray-800">
            <div class="w-full">
                <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Total Permintaan</h3>
                <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">{{ $myTotal }}</span>
                <p class="text-base font-normal text-gray-500 dark:text-gray-400">RL Document</p>
            </div>
        </div>

        <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:p-6 dark:border-gray-700 dark:bg-gray-800">
            <div class="w-full">
                <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Menunggu Approval</h3>
                <span class="text-2xl font-bold leading-none text-orange-500 sm:text-3xl dark:text-orange-400">{{ $myPending }}</span>
                <p class="text-base font-normal text-gray-500 dark:text-gray-400">Need Action</p>
            </div>
        </div>

        <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:p-6 dark:border-gray-700 dark:bg-gray-800">
            <div class="w-full">
                <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Disetujui</h3>
                <span class="text-2xl font-bold leading-none text-green-500 sm:text-3xl dark:text-green-400">{{ $myApproved }}</span>
                <p class="text-base font-normal text-gray-500 dark:text-gray-400">Approved</p>
            </div>
        </div>

        <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:p-6 dark:border-gray-700 dark:bg-gray-800">
            <div class="w-full">
                <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Ditolak</h3>
                <span class="text-2xl font-bold leading-none text-red-500 sm:text-3xl dark:text-red-400">{{ $myRejected }}</span>
                <p class="text-base font-normal text-gray-500 dark:text-gray-400">Rejected</p>
            </div>
        </div>

    </div>

    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Aktivitas Terakhir</h3>
        <p class="text-gray-500 dark:text-gray-400">Belum ada data permintaan.</p>
    </div>
</x-app-layout>