<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h2 class="text-xl font-bold mb-4 dark:text-white">Add New Master Item</h2>

                <form action="{{ route('admin.master-items.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300">Item Code</label>
                        <input type="text" name="item_code" class="w-full border-gray-300 rounded mt-1 dark:bg-gray-700 dark:text-white" required placeholder="e.g. IT-LPT-001">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300">Item Name</label>
                        <input type="text" name="item_name" class="w-full border-gray-300 rounded mt-1 dark:bg-gray-700 dark:text-white" required placeholder="e.g. MacBook Air M2">
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300">Category</label>
                            <input type="text" name="category" class="w-full border-gray-300 rounded mt-1 dark:bg-gray-700 dark:text-white" placeholder="e.g. Electronics">
                        </div>

                        <div>
                            <label class="block text-gray-700 dark:text-gray-300">Unit (Satuan)</label>
                            <select name="unit" class="w-full border-gray-300 rounded mt-1 dark:bg-gray-700 dark:text-white" required>
                                <option value="" disabled selected>-- Select Unit --</option>
                                <option value="Pcs">Pcs</option>
                                <option value="Unit">Unit</option>
                                <option value="Box">Box</option>
                                <option value="Pack">Pack</option>
                                <option value="Set">Set</option>
                                <option value="Rim">Rim</option>
                                <option value="Roll">Roll</option>
                                <option value="Kg">Kg</option>
                                <option value="Liter">Liter</option>
                                <option value="Meter">Meter</option>
                                <option value="Lembar">Lembar</option>
                                <option value="Pasang">Pasang</option>
                                <option value="Botol">Botol</option>
                                <option value="Kaleng">Kaleng</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300">Default Specification</label>
                        <textarea name="specification" class="w-full border-gray-300 rounded mt-1 dark:bg-gray-700 dark:text-white" rows="3"></textarea>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('admin.master-items.index') }}" class="px-4 py-2 bg-gray-300 rounded text-gray-800">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
