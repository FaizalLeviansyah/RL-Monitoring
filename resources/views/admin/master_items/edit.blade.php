<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h2 class="text-xl font-bold mb-4 dark:text-white">Edit Master Item</h2>

                <form action="{{ route('admin.master-items.update', $masterItem->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300">Item Code</label>
                        <input type="text" name="item_code" value="{{ $masterItem->item_code }}" class="w-full border-gray-300 rounded mt-1 dark:bg-gray-700 dark:text-white" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300">Item Name</label>
                        <input type="text" name="item_name" value="{{ $masterItem->item_name }}" class="w-full border-gray-300 rounded mt-1 dark:bg-gray-700 dark:text-white" required>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300">Category</label>
                            <input type="text" name="category" value="{{ $masterItem->category }}" class="w-full border-gray-300 rounded mt-1 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300">Unit</label>
                            <input type="text" name="unit" value="{{ $masterItem->unit }}" class="w-full border-gray-300 rounded mt-1 dark:bg-gray-700 dark:text-white" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300">Default Specification</label>
                        <textarea name="specification" class="w-full border-gray-300 rounded mt-1 dark:bg-gray-700 dark:text-white" rows="3">{{ $masterItem->specification }}</textarea>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('admin.master-items.index') }}" class="px-4 py-2 bg-gray-300 rounded text-gray-800">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Update Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
