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
                        <input type="text" value="{{ $newNumber }}" readonly class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 cursor-not-allowed">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Request Date</label>
                            <input type="date" name="request_date" value="{{ date('Y-m-d') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Required Date</label>
                            <input type="date" name="required_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Subject / Perihal</label>
                        <input type="text" name="subject" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: Permintaan Sparepart Mesin Utama" required>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Priority</label>
                        <select name="priority" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="Normal">Normal</option>
                            <option value="Urgent">Urgent</option>
                            <option value="Top Urgent">Top Urgent</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">To Department</label>
                        <input type="text" name="to_department" value="Purchasing / Procurement" readonly class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Remarks / Catatan Tambahan (Optional)</label>
                        <textarea name="remark" rows="2" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: Mohon diproses segera karena stok kritis..."></textarea>
                    </div>
                </div>
            </div>

            <div class="p-6 mb-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Items List</h3>
                    <button type="button" onclick="addItemRow()" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Add Item
                    </button>
                </div>

                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-4 py-3 min-w-[200px]">Item Name</th>
                                <th scope="col" class="px-4 py-3 w-32">Part No.</th>
                                <th scope="col" class="px-4 py-3 min-w-[200px]">Description/Specs</th>
                                <th scope="col" class="px-4 py-3 w-24">Qty</th>
                                <th scope="col" class="px-4 py-3 w-32">UOM</th>
                                <th scope="col" class="px-4 py-3 w-24" title="Stok saat ini">Stock</th>
                                <th scope="col" class="px-4 py-3 w-16 text-center">Act</th>
                            </tr>
                        </thead>
                        <tbody id="items-container">
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-4 py-3">
                                    <input type="text" name="items[0][item_name]" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" name="items[0][part_number]" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="-">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" name="items[0][description]" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="items[0][qty]" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                </td>
                                <td class="px-4 py-3">
                                    <select name="items[0][uom]" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                        <option value="Pcs">Pcs</option>
                                        <option value="Unit">Unit</option>
                                        <option value="Set">Set</option>
                                        <option value="Box">Box</option>
                                        <option value="Pack">Pack</option>
                                        <option value="Kg">Kg</option>
                                        <option value="Ltr">Ltr</option>
                                        <option value="Mtr">Mtr</option>
                                        <option value="Roll">Roll</option>
                                        <option value="Sheet">Sheet</option>
                                        <option value="Pair">Pair</option>
                                        <option value="Lot">Lot</option>
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="items[0][stock_on_hand]" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" value="0">
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" class="text-red-600 hover:text-red-900 font-medium" disabled>Del</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-8 border-t border-gray-200 pt-6">
                
                <div id="preview-section-btn" class="flex justify-end">
                    <button type="button" onclick="generatePreview()" class="flex items-center px-5 py-3 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition shadow-lg transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Generate Preview RL (Wajib)
                    </button>
                </div>

                <div id="pdf-preview-container" class="hidden mt-6 animate-fade-in-down">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2 flex items-center">
                        <span class="bg-green-100 text-green-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded border border-green-400">Verified</span>
                        Document Preview:
                    </h3>
                    <iframe id="pdf-frame" class="w-full h-[600px] border-2 border-gray-300 rounded-lg shadow-inner bg-gray-50"></iframe>
                    
                    <div class="flex justify-end gap-4 mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                        <button type="submit" name="action" value="draft" class="px-5 py-2.5 bg-gray-500 text-white font-semibold rounded-lg hover:bg-gray-600 transition">
                            Simpan Sebagai Draft
                        </button>
                        <button type="submit" name="action" value="submit" class="px-5 py-2.5 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 shadow-md transition flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Ajukan Approval
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-2 text-right italic">* Pastikan data di preview PDF sudah benar sebelum mengajukan.</p>
                </div>
            </div>
        </form>
    </div>

    <script>
        let itemIndex = 1;

        // 1. ADD ROW (SUDAH DIPERBAIKI PAKE SELECT)
        function addItemRow() {
            const container = document.getElementById('items-container');
            const row = document.createElement('tr');
            row.className = 'bg-white border-b dark:bg-gray-800 dark:border-gray-700';

            row.innerHTML = `
                <td class="px-4 py-3"><input type="text" name="items[${itemIndex}][item_name]" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required></td>
                <td class="px-4 py-3"><input type="text" name="items[${itemIndex}][part_number]" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="-"></td>
                <td class="px-4 py-3"><input type="text" name="items[${itemIndex}][description]" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></td>
                <td class="px-4 py-3"><input type="number" name="items[${itemIndex}][qty]" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required></td>
                
                <td class="px-4 py-3">
                    <select name="items[${itemIndex}][uom]" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        <option value="Pcs">Pcs</option>
                        <option value="Unit">Unit</option>
                        <option value="Set">Set</option>
                        <option value="Box">Box</option>
                        <option value="Pack">Pack</option>
                        <option value="Kg">Kg</option>
                        <option value="Ltr">Ltr</option>
                        <option value="Mtr">Mtr</option>
                        <option value="Roll">Roll</option>
                        <option value="Sheet">Sheet</option>
                        <option value="Pair">Pair</option>
                        <option value="Lot">Lot</option>
                    </select>
                </td>

                <td class="px-4 py-3"><input type="number" name="items[${itemIndex}][stock_on_hand]" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" value="0"></td>
                <td class="px-4 py-3 text-center"><button type="button" onclick="this.closest('tr').remove()" class="text-red-600 hover:text-red-900 font-medium">Del</button></td>
            `;
            container.appendChild(row);
            itemIndex++;
        }

        // 2. GENERATE PREVIEW
        async function generatePreview() {
            const form = document.getElementById('rlForm');
            if (!form.checkValidity()) { form.reportValidity(); return; }

            const formData = new FormData(form);
            const btn = document.querySelector('#preview-section-btn button');
            const originalText = btn.innerHTML;
            
            btn.innerHTML = 'Processing PDF...';
            btn.disabled = true;

            try {
                const response = await fetch("{{ route('requisitions.preview-temp') }}", {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                    body: formData
                });

                if (!response.ok) throw new Error("Gagal generate PDF");

                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                document.getElementById('pdf-frame').src = url;
                document.getElementById('pdf-preview-container').classList.remove('hidden');
                document.getElementById('preview-section-btn').classList.add('hidden');

            } catch (error) {
                alert('Gagal membuat preview.');
                console.error(error);
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        document.getElementById('rlForm').addEventListener('submit', function(e) {
            const btn = e.submitter;
            setTimeout(() => { btn.disabled = true; btn.innerText = 'Sending...'; btn.classList.add('opacity-50'); }, 0);
        });
    </script>
</x-app-layout>
{{-- UOM nya versi input --}}
{{-- <x-app-layout>
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
                        <input type="text" value="{{ $newNumber }}" readonly class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 cursor-not-allowed">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Request Date</label>
                            <input type="date" name="request_date" value="{{ date('Y-m-d') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Required Date</label>
                            <input type="date" name="required_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Subject / Perihal</label>
                        <input type="text" name="subject" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: Permintaan Sparepart Mesin Utama" required>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Priority</label>
                        <select name="priority" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="Normal">Normal</option>
                            <option value="Urgent">Urgent</option>
                            <option value="Top Urgent">Top Urgent</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">To Department</label>
                        <input type="text" name="to_department" value="Purchasing / Procurement" readonly class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400">
                    </div>
                </div>
            </div>

            <div class="p-6 mb-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Items List</h3>
                    <button type="button" onclick="addItemRow()" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Add Item
                    </button>
                </div>

                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3 min-w-[200px]">Item Name</th>
                                <th class="px-4 py-3 w-32">Part No.</th>
                                <th class="px-4 py-3 min-w-[200px]">Description/Specs</th>
                                <th class="px-4 py-3 w-24">Qty</th>
                                <th class="px-4 py-3 w-24">UOM</th>
                                <th class="px-4 py-3 w-24" title="Stok saat ini">Stock</th>
                                <th class="px-4 py-3 w-16 text-center">Act</th>
                            </tr>
                        </thead>
                        <tbody id="items-container">
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-4 py-3">
                                    <input type="text" name="items[0][item_name]" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" name="items[0][part_number]" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="-">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" name="items[0][description]" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="items[0][qty]" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" name="items[0][uom]" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Pcs" required>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="items[0][stock_on_hand]" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" value="0">
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" class="text-red-600 hover:text-red-900" disabled>Del</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-8 border-t border-gray-200 pt-6">
                
                <div id="preview-section-btn" class="flex justify-end">
                    <button type="button" onclick="generatePreview()" class="flex items-center px-5 py-3 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition shadow-lg transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Generate Preview RL (Wajib)
                    </button>
                </div>

                <div id="pdf-preview-container" class="hidden mt-6 animate-fade-in-down">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2 flex items-center">
                        <span class="bg-green-100 text-green-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded border border-green-400">Verified</span>
                        Document Preview:
                    </h3>
                    
                    <iframe id="pdf-frame" class="w-full h-[600px] border-2 border-gray-300 rounded-lg shadow-inner bg-gray-50"></iframe>
                    
                    <div class="flex justify-end gap-4 mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                        <button type="submit" name="action" value="draft" class="px-5 py-2.5 bg-gray-500 text-white font-semibold rounded-lg hover:bg-gray-600 transition">
                            Simpan Sebagai Draft
                        </button>
                        <button type="submit" name="action" value="submit" class="px-5 py-2.5 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 shadow-md transition flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Ajukan Approval
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-2 text-right italic">* Pastikan data di preview PDF sudah benar sebelum mengajukan.</p>
                </div>

            </div>

        </form>
    </div>

    <script>
        let itemIndex = 1;

        // 1. ADD ROW
        function addItemRow() {
            const container = document.getElementById('items-container');
            const row = document.createElement('tr');
            row.className = 'bg-white border-b dark:bg-gray-800 dark:border-gray-700';

            row.innerHTML = `
                <td class="px-4 py-3"><input type="text" name="items[${itemIndex}][item_name]" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required></td>
                <td class="px-4 py-3"><input type="text" name="items[${itemIndex}][part_number]" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="-"></td>
                <td class="px-4 py-3"><input type="text" name="items[${itemIndex}][description]" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></td>
                <td class="px-4 py-3"><input type="number" name="items[${itemIndex}][qty]" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required></td>
                <td class="px-4 py-3"><input type="text" name="items[${itemIndex}][uom]" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Pcs" required></td>
                <td class="px-4 py-3"><input type="number" name="items[${itemIndex}][stock_on_hand]" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" value="0"></td>
                <td class="px-4 py-3 text-center"><button type="button" onclick="this.closest('tr').remove()" class="text-red-600 hover:text-red-900 font-medium">Del</button></td>
            `;
            container.appendChild(row);
            itemIndex++;
        }

        // 2. GENERATE PREVIEW (AJAX)
        async function generatePreview() {
            const form = document.getElementById('rlForm');
            
            // Validasi HTML5 Native
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            const btn = document.querySelector('#preview-section-btn button');
            const originalText = btn.innerHTML;
            
            btn.innerHTML = 'Processing PDF...';
            btn.disabled = true;

            try {
                // Fetch ke route preview-temp
                const response = await fetch("{{ route('requisitions.preview-temp') }}", {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                    body: formData
                });

                if (!response.ok) throw new Error("Gagal generate PDF");

                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                
                document.getElementById('pdf-frame').src = url;
                
                // Show PDF & Buttons, Hide Generate Button
                document.getElementById('pdf-preview-container').classList.remove('hidden');
                document.getElementById('preview-section-btn').classList.add('hidden');

            } catch (error) {
                alert('Gagal membuat preview. Cek koneksi atau inputan.');
                console.error(error);
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        // 3. Prevent Double Submit
        document.getElementById('rlForm').addEventListener('submit', function(e) {
            const btn = e.submitter;
            setTimeout(() => {
                btn.disabled = true;
                btn.innerText = 'Sending...';
                btn.classList.add('opacity-50');
            }, 0);
        });
    </script>
</x-app-layout> --}}