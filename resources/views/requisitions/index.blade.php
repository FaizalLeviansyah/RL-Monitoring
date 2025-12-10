<x-app-layout>
    <div class="max-w-7xl mx-auto p-4">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Daftar Dokumen:
                <span class="
                    {{ $statusUpper == 'APPROVED' ? 'text-green-600' : '' }}
                    {{ $statusUpper == 'REJECTED' ? 'text-red-600' : '' }}
                    {{ $statusUpper == 'ON_PROGRESS' ? 'text-orange-500' : '' }}
                    {{ $statusUpper == 'DRAFT' ? 'text-gray-500' : '' }}
                ">
                    {{ $statusUpper }}
                </span>
            </h1>
            <p class="text-gray-500 dark:text-gray-400">Menampilkan seluruh data surat dengan status {{ strtolower($statusUpper) }}.</p>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">No RL</th>
                        <th scope="col" class="px-6 py-3">Tanggal</th>
                        <th scope="col" class="px-6 py-3">Requester</th>
                        <th scope="col" class="px-6 py-3">Subject</th>
                        <th scope="col" class="px-6 py-3">Items</th>
                        <th scope="col" class="px-6 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requisitions as $rl)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $rl->rl_no }}
                        </th>
                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::parse($rl->request_date)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900 dark:text-white">{{ $rl->requester->full_name ?? '-' }}</div>
                            <div class="text-xs">{{ $rl->requester->department->department_name ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            {{ str($rl->subject)->limit(40) }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $rl->items->count() }} Barang
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('requisitions.show', $rl->id) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                            Tidak ada dokumen dengan status ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4">
                {{ $requisitions->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
