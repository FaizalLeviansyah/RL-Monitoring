<x-app-layout>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">

        <div class="flex flex-col md:flex-row justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-gray-800 to-gray-600 dark:from-white dark:to-gray-300">
                    Document List
                </h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Viewing all documents with status:
                    <span class="px-2 py-0.5 rounded-md text-xs font-bold uppercase tracking-wider
                        {{ $statusUpper == 'APPROVED' ? 'bg-green-100 text-green-700 border border-green-200' : '' }}
                        {{ $statusUpper == 'REJECTED' ? 'bg-red-100 text-red-700 border border-red-200' : '' }}
                        {{ $statusUpper == 'ON_PROGRESS' ? 'bg-orange-100 text-orange-700 border border-orange-200' : '' }}
                        {{ $statusUpper == 'DRAFT' ? 'bg-gray-100 text-gray-700 border border-gray-200' : '' }}
                        {{ $statusUpper == 'WAITING_SUPPLY' ? 'bg-yellow-100 text-yellow-700 border border-yellow-200' : '' }}
                        {{ $statusUpper == 'PARTIALLY_APPROVED' ? 'bg-purple-100 text-purple-700 border border-purple-200' : '' }}">
                        {{ str_replace('_', ' ', $statusUpper) }}
                    </span>
                </p>
            </div>
            <div class="mt-4 md:mt-0">
                </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl shadow-gray-200/50 dark:shadow-none border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300 border-b border-gray-100 dark:border-gray-600">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-bold tracking-wider">RL Number</th>
                            <th scope="col" class="px-6 py-4 font-bold tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-4 font-bold tracking-wider">Requester</th>
                            <th scope="col" class="px-6 py-4 font-bold tracking-wider">Subject</th>
                            <th scope="col" class="px-6 py-4 font-bold tracking-wider">Items</th>
                            <th scope="col" class="px-6 py-4 font-bold tracking-wider text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($requisitions as $rl)
                        <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-bold text-blue-600 dark:text-blue-400">{{ $rl->rl_no }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    {{ \Carbon\Carbon::parse($rl->request_date)->format('d M Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $rl->requester->full_name ?? 'Unknown' }}</span>
                                    <span class="text-xs text-gray-500">{{ $rl->requester->department->department_name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                                {{ Str::limit($rl->subject, 35) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                    {{ $rl->items->count() }} Items
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('requisitions.show', $rl->id) }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-white transition-all duration-200 bg-gradient-to-r from-blue-600 to-blue-500 rounded-lg hover:from-blue-700 hover:to-blue-600 shadow-md shadow-blue-500/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    View Details &rarr;
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-full mb-3">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400 text-sm">No documents found with this status.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($requisitions->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                {{ $requisitions->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
