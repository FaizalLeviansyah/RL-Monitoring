<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="pt-6 pb-12 min-h-screen bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 animate-fade-in-down">
                <div>
                    <h2 class="text-3xl font-black text-slate-800 tracking-tight">Edit Requisition</h2>
                    <p class="text-slate-500 mt-1 text-sm">
                        Revise request <span class="font-bold text-blue-600 font-mono">{{ $requisition->rl_no }}</span>
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden relative">
                <div class="h-2 bg-gradient-to-r from-orange-400 via-pink-500 to-red-500"></div>

                <form action="{{ route('requisitions.update', $requisition->id) }}" method="POST" class="p-8" id="editForm">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8 border-b border-slate-100 pb-8">

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">To Department</label>
                            <input type="text" value="Purchasing / Procurement" readonly
                                class="w-full bg-slate-100 border-slate-200 rounded-xl text-slate-500 font-bold cursor-not-allowed py-3 px-4 shadow-inner">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Required Date <span class="text-red-500">*</span></label>
                            <input type="date" name="required_date" id="required_date"
                                min="{{ date('Y-m-d') }}"
                                value="{{ old('required_date', $requisition->required_date) }}"
                                class="w-full bg-white border-2 border-orange-100 rounded-xl text-slate-800 font-bold focus:ring-orange-500 focus:border-orange-500 py-3 px-4 shadow-sm" required>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Priority Level <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="priority" id="priority" class="w-full bg-slate-50 border-slate-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 font-bold text-slate-700 py-3 px-4 appearance-none">
                                    <option value="Normal" {{ $requisition->priority == 'Normal' ? 'selected' : '' }}>🟢 Normal (Routine)</option>
                                    <option value="Urgent" {{ $requisition->priority == 'Urgent' ? 'selected' : '' }}>🟠 Urgent (Important)</option>
                                    <option value="Top Urgent" {{ $requisition->priority == 'Top Urgent' ? 'selected' : '' }}>🔴 Top Urgent (Critical)</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Subject / Purpose <span class="text-red-500">*</span></label>
                            <input type="text" name="subject"
                                value="{{ old('subject', $requisition->subject) }}"
                                class="w-full bg-slate-50 border-slate-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all font-semibold text-slate-700 placeholder-slate-400 py-3 px-4"
                                required>
                        </div>

                        <div>
                             <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Request Date</label>
                             <input type="date" value="{{ $requisition->request_date }}" readonly
                                class="w-full bg-slate-100 border-slate-200 rounded-xl text-slate-500 font-bold py-3 px-4 cursor-not-allowed">
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Justification / Remarks</label>
                            <textarea name="remark" rows="2"
                                class="w-full bg-slate-50 border-slate-200 rounded-xl focus:ring-orange-500 focus:border-orange-500 text-slate-600 py-3 px-4">{{ old('remark', $requisition->remark) }}</textarea>
                        </div>
                    </div>

                    <div class="mb-8">
                        <div class="flex justify-between items-end mb-4">
                            <h3 class="text-lg font-bold text-slate-800">Requested Items</h3>
                            <button type="button" onclick="addItemRow()"
                                class="px-4 py-2 bg-orange-50 text-orange-600 text-sm font-bold rounded-xl hover:bg-orange-100 transition border border-orange-200 flex items-center shadow-sm">
                                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Add Row
                            </button>
                        </div>

                        <div class="overflow-hidden rounded-2xl border border-slate-200 shadow-sm bg-slate-50/50">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-slate-100 text-slate-500 font-bold uppercase text-xs tracking-wider">
                                    <tr>
                                        <th class="px-4 py-4 w-[35%]">Item Name <span class="text-red-500">*</span></th>
                                        <th class="px-4 py-4 w-[10%] text-center">Qty <span class="text-red-500">*</span></th>
                                        <th class="px-4 py-4 w-[15%] text-center">UoM <span class="text-red-500">*</span></th>
                                        <th class="px-4 py-4">Specification / Desc</th>
                                        <th class="px-4 py-4 w-12"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemsContainer" class="bg-white divide-y divide-slate-100">
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex justify-end items-center pt-6 border-t border-slate-100 gap-4">
                        <a href="{{ route('requisitions.show', $requisition->id) }}" class="px-6 py-3 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition shadow-sm">
                            Cancel
                        </a>

                        <button type="button" onclick="submitEdit()" class="px-8 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white font-bold rounded-xl shadow-lg hover:shadow-orange-500/30 transform hover:-translate-y-1 transition-all flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            Save Changes
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        const masterItems = @json($masterItems);
        const existingItems = @json($requisition->items);
        let rowCount = 0;

        const uomOptions = [
            'PCS', 'UNIT', 'SET', 'BOX', 'PACK', 'KG', 'LTR', 'METER', 'ROLL', 'LOT', 'PAIL', 'DRUM', 'CAN', 'BTL', 'BAG', 'SHEET', 'PAIR'
        ];

        document.addEventListener('DOMContentLoaded', function() {
            if (existingItems.length > 0) {
                existingItems.forEach(item => {
                    // Load item lama
                    addItemRow(item.item_name, item.qty, item.uom, item.description, item.id);
                });
            } else {
                addItemRow();
            }
        });

        function addItemRow(name = '', qty = '', uom = '', desc = '', id = null) {
            const container = document.getElementById('itemsContainer');
            const row = document.createElement('tr');
            row.className = 'hover:bg-orange-50/30 transition-colors group relative';

            // --- LOGIC ITEM NAME ---
            let itemsHtml = `<option value="">-- Select Item --</option>`;
            let isMatchFound = false;
            masterItems.forEach(item => {
                const dbName = (name || '').toUpperCase().trim();
                const masterName = (item.item_name || '').toUpperCase().trim();
                const isSelected = (dbName === masterName);
                if (isSelected) isMatchFound = true;
                itemsHtml += `<option value="${item.id}" ${isSelected ? 'selected' : ''}>${item.item_name}</option>`;
            });
            if (name && !isMatchFound) {
                itemsHtml += `<option value="custom" selected>${name} (Legacy/Custom)</option>`;
            }

            // --- LOGIC UOM (PERBAIKAN UTAMA) ---
            let uomHtml = `<option value="">-Select-</option>`;
            let isUomFound = false;

            // Bersihkan UoM dari database agar cocok
            const currentUom = (uom || '').toUpperCase().trim();

            uomOptions.forEach(opt => {
                const isSelected = (opt === currentUom);
                if (isSelected) isUomFound = true;
                uomHtml += `<option value="${opt}" ${isSelected ? 'selected' : ''}>${opt}</option>`;
            });

            // JIKA UOM LAMA TIDAK ADA DI LIST, TAMBAHKAN PAKSA (Supaya tidak Null)
            if (currentUom && !isUomFound) {
                uomHtml += `<option value="${currentUom}" selected>${currentUom} (Legacy)</option>`;
            }

            const formattedQty = qty ? Math.round(qty) : '';
            const idInput = id ? `<input type="hidden" name="items[${rowCount}][id]" value="${id}">` : '';

            row.innerHTML = `
                <td class="px-4 py-3 align-top">
                    ${idInput}
                    <input type="hidden" name="items[${rowCount}][item_name]" class="item-name-input" value="${name}">
                    <select onchange="autoFillItem(this)" class="item-select w-full bg-slate-50 border border-slate-200 text-slate-700 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block p-2.5 font-semibold shadow-sm">
                        ${itemsHtml}
                    </select>
                </td>
                <td class="px-4 py-3 align-top">
                    <input type="number" step="1" min="1" name="items[${rowCount}][qty]" value="${formattedQty}" required
                        class="qty-input bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 text-center font-bold shadow-sm">
                </td>
                <td class="px-4 py-3 align-top">
                    {{-- Tambahkan class 'uom-select' agar bisa dicek validasinya --}}
                    <select name="items[${rowCount}][uom]" class="uom-select bg-slate-50 border border-slate-200 text-slate-700 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 text-center shadow-sm" required>
                        ${uomHtml}
                    </select>
                </td>
                <td class="px-4 py-3 align-top">
                    <textarea name="items[${rowCount}][description]" rows="1"
                        class="desc-input block p-2.5 w-full text-sm text-slate-900 bg-slate-50 rounded-lg border border-slate-200 focus:ring-orange-500 focus:border-orange-500 shadow-sm"
                        placeholder="Specs...">${desc}</textarea>
                </td>
                <td class="px-4 py-3 align-middle text-center">
                    <button type="button" onclick="removeRow(this)" class="p-2 text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-full transition-all">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </td>
            `;
            container.appendChild(row);
            rowCount++;
        }

        // --- UPDATE VALIDASI TOMBOL SUBMIT ---
        function submitEdit() {
            const form = document.getElementById('editForm');
            const subject = form.querySelector('input[name="subject"]').value.trim();
            const reqDate = document.getElementById('required_date').value;

            // 1. Cek Tanggal
            const today = new Date().toISOString().split('T')[0];
            if (reqDate < today) {
                Swal.fire({icon: 'warning', title: 'Invalid Date', text: 'Required Date cannot be in the past.', confirmButtonColor: '#ea580c'});
                return;
            }

            // 2. Cek Subject
            if (!subject) {
                Swal.fire({icon: 'warning', title: 'Missing Subject', confirmButtonColor: '#ea580c'});
                return;
            }

            // 3. Cek Kelengkapan Item (Nama & UoM)
            const itemSelects = document.querySelectorAll('.item-select');
            const uomSelects = document.querySelectorAll('.uom-select'); // Ambil semua dropdown UoM
            let itemEmpty = false;
            let uomEmpty = false;

            itemSelects.forEach(sel => { if(!sel.value) itemEmpty = true; });
            uomSelects.forEach(sel => { if(!sel.value) uomEmpty = true; }); // Cek apakah UoM kosong

            if (itemSelects.length === 0) {
                Swal.fire({icon: 'warning', title: 'No Items', text: 'Please add at least one item.', confirmButtonColor: '#ea580c'});
                return;
            }

            if (itemEmpty) {
                Swal.fire({icon: 'warning', title: 'Incomplete Item', text: 'Please select an item name for all rows.', confirmButtonColor: '#ea580c'});
                return;
            }

            // PESAN ERROR KHUSUS JIKA UOM KOSONG
            if (uomEmpty) {
                Swal.fire({icon: 'warning', title: 'Missing UoM', text: 'Please select Unit of Measure (UoM) for all items.', confirmButtonColor: '#ea580c'});
                return;
            }

            form.submit();
        }


        function autoFillItem(selectElement) {
            const selectedId = selectElement.value;
            const row = selectElement.closest('tr');

            if (selectedId === 'custom') {
                return;
            }

            const itemData = masterItems.find(i => i.id == selectedId);

            if (itemData) {
                row.querySelector('.item-name-input').value = itemData.item_name;


                const uomSelect = row.querySelector('.uom-select');
                const targetUom = (itemData.uom || '').toUpperCase().trim();
                for (let i = 0; i < uomSelect.options.length; i++) {
                    if (uomSelect.options[i].value === targetUom) {
                        uomSelect.selectedIndex = i;
                        break;
                    }
                }

                const specs = itemData.description || itemData.specification || itemData.specs || '';
                row.querySelector('.desc-input').value = specs;
            } else {
                row.querySelector('.item-name-input').value = '';
                row.querySelector('.desc-input').value = '';
                row.querySelector('.uom-select').selectedIndex = 0;
            }
        }

        function removeRow(btn) {
            btn.closest('tr').remove();
        }
    </script>
</x-app-layout>
