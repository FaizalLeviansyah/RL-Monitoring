<x-app-layout>
    <div class="py-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Master Items Data</h2>
                    <p class="text-sm text-gray-500 mt-1">Manage standard items, units, and categories.</p>
                </div>
                <a href="{{ route('admin.master-items.create') }}" class="group inline-flex items-center px-5 py-2.5 text-sm font-bold text-white transition-all duration-200 bg-gradient-to-r from-blue-600 to-cyan-500 rounded-xl hover:from-blue-700 hover:to-cyan-600 shadow-lg shadow-blue-500/30 transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add New Item
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl shadow-gray-200/40 dark:shadow-none rounded-2xl border border-gray-100 dark:border-gray-700">
                <div class="p-6">

                    <form method="GET" action="{{ route('admin.master-items.index') }}" class="mb-6 relative max-w-lg">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" placeholder="Search by name or item code..." value="{{ request('search') }}"
                               class="block w-full p-3 pl-10 text-sm text-gray-900 border border-gray-200 rounded-xl bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition shadow-sm">
                        <button type="submit" class="absolute right-2.5 bottom-2 bg-gray-900 hover:bg-gray-700 text-white font-medium rounded-lg text-xs px-4 py-1.5 dark:bg-blue-600 dark:hover:bg-blue-700 transition">Search</button>
                    </form>

                    <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-700">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Code</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Item Name</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Unit</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                @foreach($items as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-blue-600 dark:text-blue-400">{{ $item->item_code }}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">{{ $item->item_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                            {{ $item->category ?? 'General' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->unit }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-3">
                                            <a href="{{ route('admin.master-items.edit', $item->id) }}" class="text-yellow-500 hover:text-yellow-600 transition p-1 hover:bg-yellow-50 rounded">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>
                                            <form action="{{ route('admin.master-items.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this item? This action cannot be undone.');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 transition p-1 hover:bg-red-50 rounded">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $items->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
