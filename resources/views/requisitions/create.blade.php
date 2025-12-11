<x-app-layout>
    <div class="max-w-7xl mx-auto p-4">

        @if(isset($oldRl))
        <div class="mb-6 p-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 border border-yellow-300 dark:bg-gray-800 dark:text-yellow-300 flex items-start shadow-md" role="alert">
            <svg class="w-5 h-5 inline mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"/></svg>
            <div>
                <span class="font-bold text-lg block mb-1">MODE REVISI</span>
                Anda sedang memperbaiki dokumen <strong>{{ $oldRl->rl_no }}</strong> yang ditolak.<br>
                Data lama telah diisi otomatis. Silakan edit bagian yang perlu diperbaiki.<br>
                <span class="text-xs text-yellow-600 mt-1 block">Nomor baru <strong>{{ $newNumber }}</strong> akan digunakan setelah disimpan.</span>
            </div>
        </div>
        @endif

        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ isset($oldRl) ? 'Revisi Requisition Letter' : 'Create Requisition Letter' }}
            </h1>
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
                            <input type="date" name="request_date" value="{{ $oldRl->request_date ?? date('Y-m-d') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Required Date</label>
                            <input type="date" name="required_date" value="{{ $oldRl->required_date ?? '' }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Subject / Perihal</label>
                        <input type="text" name="subject" value="{{ $oldRl->subject ?? '' }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: Permintaan Sparepart Mesin Utama" required>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Priority</label>
                        <select name="priority" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @php $prio = $oldRl->priority ?? 'Normal'; @endphp
                            <option value="Normal" {{ $prio == 'Normal' ? 'selected' : '' }}>Normal</option>
                            <option value="Urgent" {{ $prio == 'Urgent' ? 'selected' : '' }}>Urgent</option>
                            <option value="Top Urgent" {{ $prio == 'Top Urgent' ? 'selected' : '' }}>Top Urgent</option>
                        </select>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">To Department</label>
                        <input type="text" name="to_department" value="Purchasing / Procurement" readonly class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Remarks / Catatan Tambahan (Optional)</label>
                        <textarea name="remark" rows="2" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ $oldRl->remark ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <div class="p-6 mb-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Items List</h3>
                    <button type="button" onclick="addItemRowWithData()" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none flex items-center">
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
        let itemIndex = 0;

        // AMBIL DATA LAMA DARI CONTROLLER (Jika ada)
        const oldItems = @json($oldRl->items ?? []);

        // Saat halaman load, isi tabel
        document.addEventListener('DOMContentLoaded', function() {
            if (oldItems.length > 0) {
                oldItems.forEach(item => {
                    addItemRowWithData(item);
                });
            } else {
                // Default 1 baris kosong jika bukan revisi
                addItemRowWithData();
            }
        });

        // FUNGSI TAMBAH BARIS (Bisa terima data)
        function addItemRowWithData(data = null) {
            const container = document.getElementById('items-container');
            const row = document.createElement('tr');
            row.className = 'bg-white border-b dark:bg-gray-800 dark:border-gray-700';

            // Siapkan Value (Kosong atau Isi)
            const vName = data ? data.item_name : '';
            const vPart = data ? (data.part_number || '') : '';
            const vDesc = data ? (data.description || '') : '';
            const vQty  = data ? data.qty : '';
            const vUom  = data ? data.uom : 'Pcs'; // Default Pcs
            const vStock= data ? data.stock_on_hand : 0;

            row.innerHTML = `
                <td class="px-4 py-3"><input type="text" name="items[${itemIndex}][item_name]" value="${vName}" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required></td>
                <td class="px-4 py-3"><input type="text" name="items[${itemIndex}][part_number]" value="${vPart}" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="-"></td>
                <td class="px-4 py-3"><input type="text" name="items[${itemIndex}][description]" value="${vDesc}" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></td>
                <td class="px-4 py-3"><input type="number" name="items[${itemIndex}][qty]" value="${vQty}" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required></td>

                <td class="px-4 py-3">
                    <select name="items[${itemIndex}][uom]" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        <option value="Pcs" ${vUom == 'Pcs' ? 'selected' : ''}>Pcs</option>
                        <option value="Unit" ${vUom == 'Unit' ? 'selected' : ''}>Unit</option>
                        <option value="Set" ${vUom == 'Set' ? 'selected' : ''}>Set</option>
                        <option value="Box" ${vUom == 'Box' ? 'selected' : ''}>Box</option>
                        <option value="Pack" ${vUom == 'Pack' ? 'selected' : ''}>Pack</option>
                        <option value="Kg" ${vUom == 'Kg' ? 'selected' : ''}>Kg</option>
                        <option value="Ltr" ${vUom == 'Ltr' ? 'selected' : ''}>Ltr</option>
                        <option value="Mtr" ${vUom == 'Mtr' ? 'selected' : ''}>Mtr</option>
                        <option value="Roll" ${vUom == 'Roll' ? 'selected' : ''}>Roll</option>
                        <option value="Sheet" ${vUom == 'Sheet' ? 'selected' : ''}>Sheet</option>
                        <option value="Pair" ${vUom == 'Pair' ? 'selected' : ''}>Pair</option>
                        <option value="Lot" ${vUom == 'Lot' ? 'selected' : ''}>Lot</option>
                    </select>
                </td>

                <td class="px-4 py-3"><input type="number" name="items[${itemIndex}][stock_on_hand]" value="${vStock}" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></td>
                <td class="px-4 py-3 text-center"><button type="button" onclick="this.closest('tr').remove()" class="text-red-600 hover:text-red-900 font-medium">Del</button></td>
            `;
            container.appendChild(row);
            itemIndex++;
        }

        // GENERATE PREVIEW (AJAX)
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

        // Double Submit Protect
        document.getElementById('rlForm').addEventListener('submit', function(e) {
            const btn = e.submitter;
            setTimeout(() => { btn.disabled = true; btn.innerText = 'Sending...'; btn.classList.add('opacity-50'); }, 0);
        });
    </script>
</x-app-layout>
