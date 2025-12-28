<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <div id="previewModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="closePreview()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full border border-gray-100">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-4">
                        <h3 class="text-xl leading-6 font-bold text-gray-800" id="modal-title">📄 Document Preview</h3>
                        <button type="button" onclick="closePreview()" class="text-gray-400 hover:text-red-500 transition-colors">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="aspect-w-16 aspect-h-9 h-[600px] bg-slate-50 rounded-xl border border-slate-200">
                        <iframe name="pdf_preview_frame" class="w-full h-full rounded-xl"></iframe>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-4 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="closePreview()" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-2.5 bg-slate-800 text-base font-bold text-white hover:bg-slate-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-all">
                        Close Preview
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. CONTAINER UTAMA --}}
    <div class="pt-6 pb-12 min-h-screen bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header Section --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 animate-fade-in-down">
                <div>
                    <h2 class="text-3xl font-black text-slate-800 tracking-tight">Create New Requisition</h2>
                    <p class="text-slate-500 mt-1 text-sm">Submit your procurement request details.</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <span class="px-4 py-2 rounded-xl bg-blue-50 text-blue-700 font-bold border border-blue-100 shadow-sm flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        No: {{ $newNumber }}
                    </span>
                </div>
            </div>

            {{-- Form Card --}}
            <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden relative">
                <div class="h-2 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500"></div>

                <form action="{{ route('requisitions.store') }}" method="POST" class="p-8" id="rlForm">
                    @csrf
                    {{-- KIRIM NOMOR SURAT KE PREVIEW --}}
                    <input type="hidden" name="temp_rl_no" value="{{ $newNumber }}">

                    {{-- Section A: Header Info --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8 border-b border-slate-100 pb-8">

                        {{-- To Dept --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">To Department</label>
                            <input type="text" value="Purchasing / Procurement" readonly
                                class="w-full bg-slate-100 border-slate-200 rounded-xl text-slate-500 font-bold cursor-not-allowed py-3 px-4 shadow-inner">
                        </div>

                        {{-- Required Date --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Required Date <span class="text-red-500">*</span></label>
                            {{-- FIX: min="{{ date('Y-m-d') }}" Mencegah pilih tanggal kemarin --}}
                            <input type="date" name="required_date" id="required_date"
                                min="{{ date('Y-m-d') }}"
                                value="{{ old('required_date', $oldRl->required_date ?? '') }}"
                                class="w-full bg-white border-2 border-blue-100 rounded-xl text-blue-800 font-bold focus:ring-blue-500 focus:border-blue-500 py-3 px-4 shadow-sm" required>
                        </div>

                        {{-- Priority --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Priority Level <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="priority" id="priority" class="w-full bg-slate-50 border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-bold text-slate-700 py-3 px-4 appearance-none">
                                    {{-- FIX: Tambahkan opsi kosong agar user WAJIB memilih --}}
                                    <option value="" selected disabled>-- Select Level --</option>
                                    <option value="Normal">🟢 Normal (Routine)</option>
                                    <option value="Urgent">🟠 Urgent (Important)</option>
                                    <option value="Top Urgent">🔴 Top Urgent (Critical)</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </div>

                        {{-- Subject --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Subject / Purpose <span class="text-red-500">*</span></label>
                            <input type="text" name="subject"
                                value="{{ old('subject', $oldRl->subject ?? '') }}"
                                class="w-full bg-slate-50 border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-semibold text-slate-700 placeholder-slate-400 py-3 px-4"
                                placeholder="e.g. CCTV Installation Material for MT. Opus Point" required>
                        </div>

                        {{-- Request Date --}}
                        <div>
                             <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Request Date</label>
                             {{-- FIX: min="{{ date('Y-m-d') }}" --}}
                             <input type="date" name="request_date" id="request_date"
                                min="{{ date('Y-m-d') }}"
                                value="{{ old('request_date', date('Y-m-d')) }}"
                                class="w-full bg-slate-50 border-slate-200 rounded-xl text-slate-600 font-medium py-3 px-4 shadow-inner">
                        </div>

                        {{-- Remarks --}}
                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Justification / Remarks</label>
                            <textarea name="remark" rows="2"
                                class="w-full bg-slate-50 border-slate-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-slate-600 py-3 px-4"
                                placeholder="Explain why this request is needed...">{{ old('remark', $oldRl->remark ?? '') }}</textarea>
                        </div>
                    </div>

                    {{-- Section B: Items Table --}}
                    <div class="mb-8">
                        <div class="flex justify-between items-end mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">Requested Items</h3>
                                <p class="text-xs text-slate-400 mt-1">Select from master data to auto-fill details.</p>
                            </div>
                            <button type="button" onclick="addItemRow()"
                                class="px-4 py-2 bg-indigo-50 text-indigo-600 text-sm font-bold rounded-xl hover:bg-indigo-100 transition border border-indigo-200 flex items-center shadow-sm">
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
                                    {{-- Baris akan di-generate oleh JS --}}
                                </tbody>
                            </table>
                        </div>
                        <div id="emptyState" class="hidden text-center py-10 text-slate-400 bg-white rounded-b-2xl border-x border-b border-slate-200">
                            <p class="italic mb-2">No items added yet.</p>
                        </div>
                    </div>

                    {{-- Section C: Actions --}}
                    <div class="flex justify-end items-center pt-6 border-t border-slate-100 gap-4">
                        <a href="{{ route('requisitions.index') }}" class="px-6 py-3 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition shadow-sm">
                            Cancel
                        </a>

                        <button type="button" onclick="openPreview()" class="px-6 py-3 bg-slate-800 text-white font-bold rounded-xl hover:bg-slate-700 transition shadow-lg hover:shadow-slate-500/30 flex items-center group">
                            <svg class="w-5 h-5 mr-2 text-slate-300 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Preview PDF
                        </button>

                        <button type="button" onclick="submitForm()" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold rounded-xl shadow-lg hover:shadow-blue-500/30 transform hover:-translate-y-1 transition-all flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            Save Draft
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- === JAVASCRIPT LOGIC === --}}
    <script>
        const masterItems = @json($masterItems);
        let rowCount = 0;

        const uomOptions = [
            'PCS', 'UNIT', 'SET', 'BOX', 'PACK', 'KG', 'LTR', 'METER', 'ROLL', 'LOT', 'PAIL', 'DRUM', 'CAN', 'BTL', 'BAG', 'SHEET', 'PAIR'
        ];

        document.addEventListener('DOMContentLoaded', function() {
            const oldItems = @json($oldRl->items ?? []);
            if (oldItems.length > 0) {
                oldItems.forEach(item => {
                    addItemRow(item.item_name, item.qty, item.uom, item.description, item.part_number);
                });
            } else {
                addItemRow();
            }
        });

        function addItemRow(name = '', qty = '', uom = '', desc = '', partNo = '') {
            const container = document.getElementById('itemsContainer');
            const row = document.createElement('tr');
            row.className = 'hover:bg-blue-50/30 transition-colors group relative';

            let itemsHtml = `<option value="">-- Select Item --</option>`;
            masterItems.forEach(item => {
                const isSelected = (item.item_name === name) ? 'selected' : '';
                itemsHtml += `<option value="${item.id}" ${isSelected}>${item.item_name}</option>`;
            });

            // UOM Dropdown (Default value empty)
            let uomHtml = `<option value="">-Select-</option>`;
            uomOptions.forEach(opt => {
                const isSelected = (opt === uom) ? 'selected' : '';
                uomHtml += `<option value="${opt}" ${isSelected}>${opt}</option>`;
            });

            const formattedQty = qty ? Math.round(qty) : '';

            row.innerHTML = `
                <td class="px-4 py-3 align-top">
                    <input type="hidden" name="items[${rowCount}][item_name]" class="item-name-input" value="${name}">
                    <select onchange="autoFillItem(this)" class="item-select w-full bg-slate-50 border border-slate-200 text-slate-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 font-semibold shadow-sm">
                        ${itemsHtml}
                    </select>
                    <input type="hidden" name="items[${rowCount}][part_number]" class="part-number-input" value="${partNo}">
                </td>

                <td class="px-4 py-3 align-top">
                    <input type="number" step="1" min="1" name="items[${rowCount}][qty]" value="${formattedQty}" required
                        class="qty-input bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 text-center font-bold shadow-sm" placeholder="0">
                </td>

                <td class="px-4 py-3 align-top">
                    <select name="items[${rowCount}][uom]" class="uom-select bg-slate-50 border border-slate-200 text-slate-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 text-center shadow-sm" required>
                        ${uomHtml}
                    </select>
                </td>

                <td class="px-4 py-3 align-top">
                    <textarea name="items[${rowCount}][description]" rows="1"
                        class="desc-input block p-2.5 w-full text-sm text-slate-900 bg-slate-50 rounded-lg border border-slate-200 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                        placeholder="Specification...">${desc}</textarea>
                </td>

                <td class="px-4 py-3 align-middle text-center">
                    <button type="button" onclick="removeRow(this)" class="p-2 text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-full transition-all">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </td>
            `;
            container.appendChild(row);
            rowCount++;
            checkEmpty();
        }

        function autoFillItem(selectElement) {
            const selectedId = selectElement.value;
            const row = selectElement.closest('tr');
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
                row.querySelector('.part-number-input').value = itemData.part_number || '';

                row.querySelector('.desc-input').classList.add('bg-blue-50');
                setTimeout(() => row.querySelector('.desc-input').classList.remove('bg-blue-50'), 500);
            }
        }

        function removeRow(btn) {
            btn.closest('tr').remove();
            checkEmpty();
        }

        function checkEmpty() {
            const rows = document.querySelectorAll('#itemsContainer tr').length;
            const emptyState = document.getElementById('emptyState');
            if(rows === 0) emptyState.classList.remove('hidden');
            else emptyState.classList.add('hidden');
        }

        // --- 3. ADVANCED PREVIEW PROTECTION ---
        function openPreview() {
            const form = document.getElementById('rlForm');

            // 1. Ambil Value Header
            const subject = form.querySelector('input[name="subject"]').value.trim();
            const reqDate = form.querySelector('input[name="required_date"]').value;
            const requestDate = form.querySelector('input[name="request_date"]').value;
            const priority = form.querySelector('select[name="priority"]').value;

            // 2. Proteksi Data Header Kosong
            if (!subject || !reqDate || !priority) {
                Swal.fire({
                    icon: 'error',
                    title: 'Incomplete Header',
                    html: 'Please fill in <b>Subject</b>, <b>Required Date</b>, and <b>Priority</b> first!',
                    confirmButtonColor: '#1e293b'
                });
                return;
            }

            // 3. Proteksi Tanggal Lampau (Back-dated protection)
            const today = new Date().toISOString().split('T')[0];
            if (reqDate < today || requestDate < today) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Date',
                    text: 'Request Date & Required Date cannot be in the past.',
                    confirmButtonColor: '#1e293b'
                });
                return;
            }

            // 4. Proteksi Items (Looping Checking)
            const rows = document.querySelectorAll('#itemsContainer tr');
            if (rows.length === 0) {
                Swal.fire({icon: 'warning', title: 'Empty Items', text: 'Please add at least one item.', confirmButtonColor: '#1e293b'});
                return;
            }

            let itemError = null;
            rows.forEach((row, index) => {
                const itemSelect = row.querySelector('.item-select');
                const qtyInput = row.querySelector('.qty-input');
                const uomSelect = row.querySelector('.uom-select');

                if (!itemSelect.value) itemError = `Row ${index + 1}: Please select an Item.`;
                else if (!qtyInput.value || qtyInput.value <= 0) itemError = `Row ${index + 1}: Please enter a valid Quantity.`;
                else if (!uomSelect.value) itemError = `Row ${index + 1}: Please select a UoM.`;
            });

            if (itemError) {
                Swal.fire({
                    icon: 'error',
                    title: 'Incomplete Item Details',
                    text: itemError,
                    confirmButtonColor: '#1e293b'
                });
                return;
            }

            // 5. Jika Lolos Semua -> Buka Modal
            const modal = document.getElementById('previewModal');
            form.target = 'pdf_preview_frame';
            form.action = "{{ route('requisitions.preview') }}";
            modal.classList.remove('hidden');
            form.submit();
        }

        function closePreview() {
            document.getElementById('previewModal').classList.add('hidden');
            window.frames['pdf_preview_frame'].location.href = 'about:blank';
        }

        function submitForm() {
            const form = document.getElementById('rlForm');
            form.target = '_self';
            form.action = "{{ route('requisitions.store') }}";
            form.submit();
        }
    </script>
</x-app-layout> --}}





{{-- <x-app-layout>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">

        @if(isset($oldRl))
        <div class="mb-6 p-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 border border-yellow-300 dark:bg-gray-800 dark:text-yellow-300 flex items-start shadow-md animate-fade-in-down" role="alert">
            <svg class="w-5 h-5 inline mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"/></svg>
            <div>
                <span class="font-bold text-lg block mb-1">REVISION MODE</span>
                You are revising document <strong>{{ $oldRl->rl_no }}</strong>.<br>
                A new RL Number <strong>{{ $newNumber }}</strong> will be generated upon saving.
            </div>
        </div>
        @endif

        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">Create New RL</h1>
                <p class="text-sm text-gray-500">Fill in the details to generate the Requisition Letter.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-blue-600 font-medium transition flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Dashboard
            </a>
        </div>

        <form id="rlForm" action="{{ route('requisitions.store') }}" method="POST">
            @csrf

            <div class="p-6 mb-6 bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <h3 class="mb-4 text-lg font-bold text-gray-900 dark:text-white border-b pb-2 dark:border-gray-700">General Information</h3>

                <div class="grid gap-6 mb-6 md:grid-cols-2">
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">RL Number (Auto)</label>
                        <input type="text" value="{{ $newNumber }}" readonly class="bg-gray-100 border border-gray-300 text-gray-500 text-sm rounded-xl block w-full p-2.5 cursor-not-allowed font-mono">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">Request Date</label>
                            <input type="date" name="request_date" value="{{ $oldRl->request_date ?? date('Y-m-d') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">Required Date</label>
                            <input type="date" name="required_date" value="{{ $oldRl->required_date ?? '' }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">Subject / Title</label>
                        <input type="text" name="subject" value="{{ $oldRl->subject ?? '' }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Example: Procurement of Network Devices for CTP Branch" required>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">Priority</label>
                        <select name="priority" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @php $prio = $oldRl->priority ?? 'Normal'; @endphp
                            <option value="Normal" {{ $prio == 'Normal' ? 'selected' : '' }}>Normal</option>
                            <option value="Urgent" {{ $prio == 'Urgent' ? 'selected' : '' }}>Urgent</option>
                            <option value="Top Urgent" {{ $prio == 'Top Urgent' ? 'selected' : '' }}>Top Urgent</option>
                        </select>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">To Department</label>
                        <input type="text" name="to_department" value="Purchasing / Procurement" readonly class="bg-gray-100 border border-gray-300 text-gray-500 text-sm rounded-xl block w-full p-2.5 cursor-not-allowed">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">Remarks (Optional)</label>
                        <textarea name="remark" rows="2" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Additional notes...">{{ $oldRl->remark ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Items List</h3>
                    <button type="button" onclick="addItemRowWithData()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-500/30 transition transform hover:-translate-y-0.5">
                        + Add Item
                    </button>
                </div>

                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400" id="items_table">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-300">
                            <tr>
                                <th class="px-4 py-3 min-w-[200px]">Select Master / Input Name <span class="text-red-500">*</span></th>
                                <th class="px-4 py-3 min-w-[150px]">Part Number</th>
                                <th class="px-4 py-3 min-w-[200px]">Specification/Desc</th>
                                <th class="px-4 py-3 w-24 text-center">Qty <span class="text-red-500">*</span></th>
                                <th class="px-4 py-3 w-32">Unit <span class="text-red-500">*</span></th>
                                <th class="px-4 py-3 w-24 text-center">Stock</th>
                                <th class="px-4 py-3 w-16 text-center">Act</th>
                            </tr>
                        </thead>
                        <tbody id="items_container">
                            </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">

                <div id="preview-section-btn" class="flex justify-end gap-3">
                    <button type="button" onclick="generatePreview()" class="px-5 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold rounded-xl transition flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Preview Draft PDF
                    </button>

                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-xl shadow-lg flex items-center transform hover:-translate-y-0.5 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        Save & Generate Document
                    </button>
                </div>

                <div id="pdf-preview-container" class="hidden mt-6 animate-fade-in-up">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center">
                            <span class="bg-gray-100 text-gray-800 text-xs font-bold mr-2 px-2.5 py-0.5 rounded border border-gray-400">DRAFT PREVIEW</span>
                            Please check the content before saving.
                        </h3>
                        <button type="button" onclick="document.getElementById('pdf-preview-container').classList.add('hidden');" class="text-sm text-red-500 hover:underline">
                            Close Preview
                        </button>
                    </div>

                    <iframe id="pdf-frame" class="w-full h-[600px] border-2 border-gray-300 rounded-xl shadow-inner bg-gray-50"></iframe>
                </div>
            </div>
        </form>
    </div>

    <script>
        const masterItemsData = @json($masterItems);
        const oldItemsData = @json($oldRl->items ?? []);
        let rowIdx = 0;

        document.addEventListener('DOMContentLoaded', function() {
            if (oldItemsData.length > 0) {
                oldItemsData.forEach(item => addItemRowWithData(item));
            } else {
                addItemRowWithData();
            }
        });

        function addItemRowWithData(data = null) {
            const container = document.getElementById('items_container');
            const tr = document.createElement('tr');
            tr.className = "bg-white border-b dark:bg-gray-800 dark:border-gray-700 item-row transition hover:bg-gray-50 dark:hover:bg-gray-700";

            const vName = data ? data.item_name : '';
            const vPart = data ? (data.part_number || '') : '';
            const vSpec = data ? (data.description || '') : '';
            const vQty  = data ? data.qty : 1;
            const vUom  = data ? data.uom : '';
            const vStock= data ? (data.stock_on_hand || 0) : 0;

            let masterOptions = `<option value="">-- Manual / Select Master --</option>`;
            masterItemsData.forEach(m => {
                const selected = (vName === m.item_name) ? 'selected' : '';
                masterOptions += `<option value="${m.id}" data-name="${m.item_name}" data-unit="${m.unit}" data-spec="${m.specification || ''}" ${selected}>${m.item_code} - ${m.item_name}</option>`;
            });

            const units = ['Pcs','Unit','Box','Pack','Set','Rim','Roll','Kg','Liter','Meter','Sheet','Pair','Lot'];
            let unitOptions = `<option value="" disabled ${!vUom ? 'selected' : ''}>-Unit-</option>`;
            units.forEach(u => {
                const sel = (vUom === u) ? 'selected' : '';
                unitOptions += `<option value="${u}" ${sel}>${u}</option>`;
            });

            tr.innerHTML = `
                <td class="px-4 py-2 align-top">
                    <select class="w-full border-gray-300 rounded-lg dark:bg-gray-700 dark:text-white text-xs focus:ring-blue-500" onchange="fillItemData(this)">${masterOptions}</select>
                    <input type="text" name="items[${rowIdx}][item_name]" value="${vName}" class="mt-2 w-full border-gray-300 rounded-lg dark:bg-gray-700 dark:text-white text-xs item-name-input" placeholder="Item Name" required>
                </td>
                <td class="px-4 py-2 align-top">
                    <input type="text" name="items[${rowIdx}][part_number]" value="${vPart}" class="w-full border-gray-300 rounded-lg dark:bg-gray-700 dark:text-white text-xs" placeholder="-">
                </td>
                <td class="px-4 py-2 align-top">
                    <textarea name="items[${rowIdx}][description]" class="w-full border-gray-300 rounded-lg dark:bg-gray-700 dark:text-white text-xs item-spec-input" rows="2" placeholder="Spec...">${vSpec}</textarea>
                </td>
                <td class="px-4 py-2 align-top">
                    <input type="number" name="items[${rowIdx}][qty]" value="${vQty}" class="w-full border-gray-300 rounded-lg dark:bg-gray-700 dark:text-white text-xs text-center font-bold" required min="1">
                </td>
                <td class="px-4 py-2 align-top">
                    <select name="items[${rowIdx}][uom]" class="w-full border-gray-300 rounded-lg dark:bg-gray-700 dark:text-white text-xs item-unit-input" required>${unitOptions}</select>
                </td>
                <td class="px-4 py-2 align-top">
                    <input type="number" name="items[${rowIdx}][stock_on_hand]" value="${vStock}" class="w-full border-gray-300 rounded-lg dark:bg-gray-700 dark:text-white text-xs bg-gray-50" placeholder="0">
                </td>
                <td class="px-4 py-2 align-top text-center">
                    <button type="button" onclick="removeItemRow(this)" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-1.5 rounded-lg transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                </td>
            `;
            container.appendChild(tr);
            rowIdx++;
        }

        function fillItemData(selectEl) {
            const row = selectEl.closest('tr');
            const selectedOpt = selectEl.options[selectEl.selectedIndex];
            const nameInput = row.querySelector('.item-name-input');
            const specInput = row.querySelector('.item-spec-input');
            const unitInput = row.querySelector('.item-unit-input');

            if (selectEl.value) {
                nameInput.value = selectedOpt.getAttribute('data-name');
                specInput.value = selectedOpt.getAttribute('data-spec');
                const masterUnit = selectedOpt.getAttribute('data-unit');
                unitInput.value = masterUnit;
            } else {
                nameInput.value = '';
                specInput.value = '';
                unitInput.selectedIndex = 0;
            }
        }

        function removeItemRow(btn) {
            const container = document.getElementById('items_container');
            if (container.children.length > 1) {
                btn.closest('tr').remove();
            } else {
                alert("Request must have at least one item.");
            }
        }

        async function generatePreview() {
            const form = document.getElementById('rlForm');
            if (!form.checkValidity()) { form.reportValidity(); return; }

            const formData = new FormData(form);
            const btn = document.querySelector('#preview-section-btn button'); // Select Preview Button
            const originalContent = btn.innerHTML;

            btn.innerHTML = `Generating...`;
            btn.disabled = true;

            try {
                const response = await fetch("{{ route('requisitions.preview-temp') }}", {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                    body: formData
                });

                if (!response.ok) throw new Error("Server Error");

                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const iframe = document.getElementById('pdf-frame');
                iframe.src = url;
                document.getElementById('pdf-preview-container').classList.remove('hidden');
                document.getElementById('pdf-preview-container').scrollIntoView({ behavior: 'smooth' });

            } catch (error) {
                console.error(error);
                alert('Preview Failed.');
            } finally {
                btn.innerHTML = originalContent;
                btn.disabled = false;
            }
        }

        // Loading indicator for Save
        document.getElementById('rlForm').addEventListener('submit', function(e) {
            const btn = e.submitter;
            if(btn) {
                btn.innerHTML = 'Saving...';
                btn.classList.add('opacity-75', 'cursor-wait');
            }
        });
    </script>
</x-app-layout> --}}
