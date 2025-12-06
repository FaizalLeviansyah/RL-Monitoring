<x-app-layout>
    <div class="max-w-7xl mx-auto p-4">

        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create Requisition Letter</h1>
            <a href="{{ route('dashboard') }}" class="text-gray-500 hover:underline">Back to Dashboard</a>
        </div>

        <form id="rlForm" action="{{ route('requisitions.store') }}" method="POST">
            @csrf

            <div class="p-6 mb-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">General Information</h3>

                <div class="grid gap-6 mb-6 md:grid-cols-2">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">RL Number (Auto)</label>
                        <input type="text" value="{{ $newNumber }}" readonly class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date</label>
                        <input type="date" name="request_date" value="{{ date('Y-m-d') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Subject / Perihal</label>
                        <input type="text" name="subject" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="e.g. Permintaan Laptop Baru untuk Staff IT">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">To Department (Optional)</label>
                        <input type="text" name="to_department" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="e.g. Purchasing">
                    </div>
                </div>
            </div>

            <div class="p-6 mb-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Items List</h3>
                    <button type="button" onclick="addItemRow()" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Add Item
                    </button>
                </div>

                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Item Name</th>
                                <th scope="col" class="px-6 py-3 w-32">Qty</th>
                                <th scope="col" class="px-6 py-3 w-32">UOM</th>
                                <th scope="col" class="px-6 py-3">Description/Specs</th>
                                <th scope="col" class="px-6 py-3 w-20">Action</th>
                            </tr>
                        </thead>
                        <tbody id="items-container">
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-4">
                                    <input type="text" name="items[0][item_name]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                </td>
                                <td class="px-6 py-4">
                                    <input type="number" name="items[0][qty]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                </td>
                                <td class="px-6 py-4">
                                    <input type="text" name="items[0][uom]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Pcs/Unit" required>
                                </td>
                                <td class="px-6 py-4">
                                    <input type="text" name="items[0][description]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button type="button" class="font-medium text-red-600 dark:text-red-500 hover:underline" disabled>Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="submit" name="action" value="draft" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 transition-all shadow-sm">
                    Simpan sebagai Draft
                </button>
                <button type="submit" name="action" value="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none transition-all shadow-md">
                    Ajukan Approval (Submit)
                </button>
            </div>
        </form>
    </div>

    <script>
        let itemIndex = 1;

        function addItemRow() {
            const container = document.getElementById('items-container');
            const row = document.createElement('tr');
            row.className = 'bg-white border-b dark:bg-gray-800 dark:border-gray-700';

            row.innerHTML = `
                <td class="px-6 py-4">
                    <input type="text" name="items[${itemIndex}][item_name]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                </td>
                <td class="px-6 py-4">
                    <input type="number" name="items[${itemIndex}][qty]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                </td>
                <td class="px-6 py-4">
                    <input type="text" name="items[${itemIndex}][uom]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                </td>
                <td class="px-6 py-4">
                    <input type="text" name="items[${itemIndex}][description]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </td>
                <td class="px-6 py-4 text-center">
                    <button type="button" onclick="this.closest('tr').remove()" class="font-medium text-red-600 dark:text-red-500 hover:underline">Delete</button>
                </td>
            `;

            container.appendChild(row);
            itemIndex++;
        }
    </script>
    <script>
    // Script Item (yang sudah ada biarkan saja) ...
    let itemIndex = 1;
    function addItemRow() { ... }

    // --- TAMBAHAN BARU: CEGAH DOUBLE SUBMIT ---
    document.getElementById('rlForm').addEventListener('submit', function(e) {
        // Cari tombol submit yang ditekan
        const submitter = e.submitter;
        
        // Ubah text tombol jadi "Processing..." dan disable biar gak bisa diklik lagi
        // Kita gunakan setTimeout 0 agar data form (value=draft/submit) sempat terkirim dulu
        setTimeout(() => {
            submitter.disabled = true;
            submitter.innerText = 'Processing...';
            submitter.classList.add('opacity-50', 'cursor-not-allowed');
        }, 0);
    });
</script>
</x-app-layout>
