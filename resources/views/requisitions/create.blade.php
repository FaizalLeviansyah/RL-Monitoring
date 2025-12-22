<x-app-layout>
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
</x-app-layout>
