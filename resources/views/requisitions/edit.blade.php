<x-app-layout>
    <div class="pt-6 pb-12 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-8 animate-fade-in-down">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('requisitions.index') }}" class="p-2 bg-white rounded-xl shadow-sm border border-slate-200 text-slate-500 hover:text-blue-600 transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    <h2 class="text-3xl font-black text-slate-800 tracking-tight">
                        Edit Requisition <span class="text-blue-600">#{{ $requisition->rl_no }}</span>
                    </h2>
                </div>
                <p class="text-slate-500 mt-2 ml-12">
                    Revise your request details and items below. Submitting will restart the approval process.
                </p>
            </div>

            <div class="bg-white/80 backdrop-blur-xl border border-slate-100 rounded-[2rem] p-8 shadow-xl relative overflow-hidden">
                @if($requisition->status_flow == 'REJECTED')
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-8 rounded-r-xl">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-bold text-red-800">Previous Rejection Note:</h3>
                            <div class="mt-1 text-sm text-red-700 italic">
                                "{!! nl2br(e($requisition->remark)) !!}"
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <form action="{{ route('requisitions.update', $requisition->id) }}" method="POST" id="rlForm">
                    @csrf
                    @method('PUT') <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Subject / Purpose</label>
                            <input type="text" name="subject" value="{{ old('subject', $requisition->subject) }}" class="w-full bg-slate-50 border-slate-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 font-semibold text-slate-700" required>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Required Date</label>
                                <input type="date" name="required_date" value="{{ old('required_date', $requisition->required_date) }}" class="w-full bg-slate-50 border-slate-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 font-bold text-slate-700" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Priority</label>
                                <select name="priority" class="w-full bg-slate-50 border-slate-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 font-bold text-slate-700">
                                    <option value="Normal" {{ $requisition->priority == 'Normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="Urgent" {{ $requisition->priority == 'Urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-8">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Justification / Remark</label>
                        <textarea name="remark" rows="2" class="w-full bg-slate-50 border-slate-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-slate-600">{{ old('remark', $requisition->remark) }}</textarea>
                    </div>

                    <div class="mb-8">
                        <div class="flex justify-between items-end mb-3">
                            <label class="block text-sm font-extrabold text-slate-800">Request Items</label>
                            <button type="button" onclick="addItemRow()" class="px-4 py-2 bg-blue-50 text-blue-600 text-xs font-bold rounded-lg hover:bg-blue-100 transition border border-blue-100 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add Item
                            </button>
                        </div>

                        <div class="overflow-hidden rounded-2xl border border-slate-200 shadow-sm">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-slate-100 text-slate-500 font-bold uppercase text-xs">
                                    <tr>
                                        <th class="px-4 py-3">Item Name</th>
                                        <th class="px-4 py-3 w-24">Qty</th>
                                        <th class="px-4 py-3 w-32">UoM</th>
                                        <th class="px-4 py-3">Specs / Desc</th>
                                        <th class="px-4 py-3 w-10"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemsContainer" class="bg-white divide-y divide-slate-100">
                                    </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex justify-end pt-6 border-t border-slate-100">
                        <a href="{{ route('requisitions.index') }}" class="mr-4 px-6 py-3 bg-white border border-slate-200 text-slate-600 font-bold rounded-2xl hover:bg-slate-50 transition">Cancel</a>
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold rounded-2xl shadow-lg hover:shadow-blue-500/30 transform hover:-translate-y-1 transition-all">
                            Save Changes & Resubmit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let itemCount = 0;

        // Data dari Controller (PHP) dikirim ke JS
        const existingItems = @json($requisition->items);

        document.addEventListener('DOMContentLoaded', function() {
            // Jika ada existing items, loop dan tampilkan
            if (existingItems.length > 0) {
                existingItems.forEach(item => {
                    addItemRow(item.item_name, item.qty, item.uom, item.description);
                });
            } else {
                // Jika kosong (aneh), tambah 1 baris kosong
                addItemRow();
            }
        });

        function addItemRow(name = '', qty = '', uom = '', desc = '') {
            const container = document.getElementById('itemsContainer');
            const row = document.createElement('tr');
            row.className = 'hover:bg-blue-50/30 transition-colors group';
            row.innerHTML = `
                <td class="px-4 py-2">
                    <input type="text" name="items[${itemCount}][item_name]" value="${name}" class="w-full border-0 border-b border-transparent bg-transparent focus:border-blue-500 focus:ring-0 font-semibold text-slate-700 placeholder-slate-300 transition-all" placeholder="Enter item name..." required>
                </td>
                <td class="px-4 py-2">
                    <input type="number" step="0.01" name="items[${itemCount}][qty]" value="${qty}" class="w-full border-0 border-b border-transparent bg-transparent focus:border-blue-500 focus:ring-0 font-bold text-center text-slate-700" required>
                </td>
                <td class="px-4 py-2">
                    <input type="text" name="items[${itemCount}][uom]" value="${uom}" class="w-full border-0 border-b border-transparent bg-transparent focus:border-blue-500 focus:ring-0 text-center text-slate-600" required>
                </td>
                <td class="px-4 py-2">
                    <input type="text" name="items[${itemCount}][description]" value="${desc || ''}" class="w-full border-0 border-b border-transparent bg-transparent focus:border-blue-500 focus:ring-0 text-slate-500 italic text-xs" placeholder="Optional specs...">
                </td>
                <td class="px-4 py-2 text-center">
                    <button type="button" onclick="this.closest('tr').remove()" class="text-slate-300 hover:text-red-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </td>
            `;
            container.appendChild(row);
            itemCount++;
        }
    </script>
</x-app-layout>
<script>
        let itemCount = 0;

        // Load data existing dari Controller
        const existingItems = @json($requisition->items);

        document.addEventListener('DOMContentLoaded', function() {
            if (existingItems.length > 0) {
                existingItems.forEach(item => {
                    // Masukkan ID item ke parameter fungsi
                    addItemRow(item.item_name, item.qty, item.uom, item.description, item.id);
                });
            } else {
                addItemRow();
            }
        });

        // Tambahkan parameter 'id' (default null)
        function addItemRow(name = '', qty = '', uom = '', desc = '', id = null) {
            const container = document.getElementById('itemsContainer');
            const row = document.createElement('tr');
            row.className = 'hover:bg-blue-50/30 transition-colors group';

            // LOGIC PENTING:
            // Jika ID ada, buat hidden input. Jika tidak, jangan buat (nanti dianggap item baru)
            const idInput = id ? `<input type="hidden" name="items[${itemCount}][id]" value="${id}">` : '';

            row.innerHTML = `
                ${idInput} <td class="px-4 py-2">
                    <input type="text" name="items[${itemCount}][item_name]" value="${name}" class="w-full border-0 border-b border-transparent bg-transparent focus:border-blue-500 focus:ring-0 font-semibold text-slate-700 placeholder-slate-300 transition-all" placeholder="Enter item name..." required>
                </td>
                <td class="px-4 py-2">
                    <input type="number" step="0.01" name="items[${itemCount}][qty]" value="${qty}" class="w-full border-0 border-b border-transparent bg-transparent focus:border-blue-500 focus:ring-0 font-bold text-center text-slate-700" required>
                </td>
                <td class="px-4 py-2">
                    <input type="text" name="items[${itemCount}][uom]" value="${uom}" class="w-full border-0 border-b border-transparent bg-transparent focus:border-blue-500 focus:ring-0 text-center text-slate-600" required>
                </td>
                <td class="px-4 py-2">
                    <input type="text" name="items[${itemCount}][description]" value="${desc || ''}" class="w-full border-0 border-b border-transparent bg-transparent focus:border-blue-500 focus:ring-0 text-slate-500 italic text-xs" placeholder="Optional specs...">
                </td>
                <td class="px-4 py-2 text-center">
                    <button type="button" onclick="this.closest('tr').remove()" class="text-slate-300 hover:text-red-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </td>
            `;
            container.appendChild(row);
            itemCount++;
        }
    </script>
