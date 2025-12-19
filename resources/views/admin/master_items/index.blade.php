<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold">Master Items Data</h2>
                        <a href="{{ route('admin.master-items.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            + Add New Item
                        </a>
                    </div>

                    <form method="GET" action="{{ route('admin.master-items.index') }}" class="mb-4">
                        <input type="text" name="search" placeholder="Cari nama atau kode barang..." value="{{ request('search') }}"
                               class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-gray-300">
                        <button type="submit" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Search</button>
                    </form>

                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @foreach($items as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $item->item_code }}</td>
                                <td class="px-6 py-4 font-bold">{{ $item->item_name }}</td>
                                <td class="px-6 py-4">{{ $item->category ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $item->unit }}</td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('admin.master-items.edit', $item->id) }}" class="text-yellow-500 hover:text-yellow-600">Edit</a>
                                    <form action="{{ route('admin.master-items.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus barang ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-600">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $items->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
