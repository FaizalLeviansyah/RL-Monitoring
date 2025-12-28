<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @php
        $user = Auth::user();
        $isRequester = $user->employee_id == $requisition->requester_id;

        // Cek apakah User ini adalah Approver yang sedang gilirannya?
        $currentApprover = $requisition->approvalQueues()
                            ->where('approver_id', $user->employee_id)
                            ->where('status', 'PENDING')
                            ->first();

        $isPendingApprover = $currentApprover ? true : false;

        // Cek Status Edit (Hanya Draft/Rejected & Pemilik)
        $canEdit = in_array($requisition->status_flow, ['DRAFT', 'REJECTED']) && $isRequester;
    @endphp

    <div class="pt-6 pb-12 min-h-screen bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- 1. HEADER & ACTION BUTTONS --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">

                <div class="flex items-center gap-4">
                    <a href="{{ route('requisitions.index') }}" class="p-3 bg-white border border-slate-200 rounded-xl text-slate-500 hover:text-blue-600 shadow-sm transition-all">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    <div>
                        <div class="flex items-center gap-3">
                            <h2 class="text-3xl font-black text-slate-800 tracking-tight">Requisition Details</h2>
                            <span class="px-3 py-1 rounded-full text-xs font-bold
                                @if($requisition->status_flow == 'APPROVED') bg-green-100 text-green-700
                                @elseif($requisition->status_flow == 'COMPLETED') bg-teal-100 text-teal-700
                                @elseif($requisition->status_flow == 'REJECTED') bg-red-100 text-red-700
                                @elseif($requisition->status_flow == 'DRAFT') bg-gray-100 text-gray-600
                                @else bg-blue-100 text-blue-700 animate-pulse @endif">
                                {{ str_replace('_', ' ', $requisition->status_flow) }}
                            </span>
                        </div>
                        <p class="text-slate-500 text-sm mt-1">Ref No: <span class="font-mono font-bold text-blue-600">{{ $requisition->rl_no }}</span></p>
                    </div>
                </div>

                {{-- ACTION GROUP (KANAN ATAS) --}}
                <div class="mt-4 md:mt-0 flex gap-3">

                    {{-- [1] SMART PREVIEW BUTTON --}}
                    @if($requisition->attachment_partial)
                        <a href="{{ asset('storage/' . $requisition->attachment_partial) }}" target="_blank" class="px-5 py-2.5 bg-indigo-50 text-indigo-700 border border-indigo-200 font-bold rounded-xl hover:bg-indigo-100 transition shadow-sm flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            View Signed Doc
                        </a>
                    @else
                        <a href="{{ route('requisitions.print', $requisition->id) }}" target="_blank" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 font-bold rounded-xl hover:bg-slate-50 hover:text-blue-600 transition shadow-sm flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Print System PDF
                        </a>
                    @endif

                    {{-- [2] ADMIN ROLLBACK --}}
                    @can('super_admin')
                        @if(in_array($requisition->status_flow, ['ON_PROGRESS', 'PARTIALLY_APPROVED']))
                            <button type="button" onclick="confirmRollback('{{ $requisition->status_flow }}')" class="px-5 py-2.5 bg-red-600 text-white font-bold rounded-xl shadow-lg hover:bg-red-700 transition flex items-center border border-red-500">
                                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                Admin Rollback
                            </button>

                            <form id="rollbackForm" action="{{ route('requisitions.rollback', $requisition->id) }}" method="POST" class="hidden">
                                @csrf
                                <input type="hidden" name="reason" id="rollback_reason">
                                <input type="hidden" name="target" id="rollback_target">
                            </form>
                        @endif
                    @endcan

                    {{-- [3] EDIT DATA --}}
                    @if($canEdit)
                        <a href="{{ route('requisitions.edit', $requisition->id) }}" class="px-5 py-2.5 bg-orange-50 text-orange-600 font-bold rounded-xl border border-orange-200 hover:bg-orange-100 transition shadow-sm flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit Data
                        </a>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- LEFT COLUMN: DETAIL CONTENT --}}
                <div class="lg:col-span-2 space-y-8">

                    {{-- INFO CARD --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div>
                                <p class="text-xs text-slate-400 font-bold uppercase">Requester</p>
                                <p class="text-slate-700 font-bold mt-1">{{ $requisition->requester->full_name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 font-bold uppercase">Required Date</p>
                                <p class="text-blue-600 font-bold mt-1">{{ \Carbon\Carbon::parse($requisition->required_date)->format('d M Y') }}</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-xs text-slate-400 font-bold uppercase">Subject</p>
                                <p class="text-slate-800 font-bold mt-1">{{ $requisition->subject }}</p>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h3 class="text-sm font-bold text-slate-400 uppercase mb-3">Items Requested</h3>
                            <div class="overflow-hidden rounded-xl border border-slate-200">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-xs">
                                        <tr>
                                            <th class="px-4 py-3">Item</th>
                                            <th class="px-4 py-3 text-center">Qty</th>
                                            <th class="px-4 py-3">Specs</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 bg-white">
                                        @foreach($requisition->items as $item)
                                        <tr>
                                            <td class="px-4 py-3 font-bold text-slate-700">{{ $item->item_name }}</td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="px-2 py-1 bg-indigo-50 text-indigo-700 rounded-lg font-bold">{{ $item->qty + 0 }} {{ $item->uom }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-slate-500 italic">{{ $item->description ?? '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- 🟢 APPROVER ZONE: KHUSUS UNTUK MANAGER/DIREKTUR --}}
                    @if($isPendingApprover)
                        <div class="bg-slate-800 rounded-2xl shadow-xl overflow-hidden border border-slate-700 relative">
                            <div class="p-4 bg-slate-900 border-b border-slate-700 flex justify-between items-center">
                                <h3 class="text-white font-bold flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    Review & Decision Required
                                </h3>
                                <span class="text-xs text-slate-400">You are the current approver</span>
                            </div>

                            {{-- SMART PREVIEW --}}
                            <div class="aspect-w-16 aspect-h-9 h-[500px] bg-white">
                                @if($requisition->attachment_partial)
                                    <iframe src="{{ asset('storage/' . $requisition->attachment_partial) }}" class="w-full h-full"></iframe>
                                @else
                                    <iframe src="{{ route('requisitions.print', $requisition->id) }}" class="w-full h-full"></iframe>
                                @endif
                            </div>

                            <div class="p-6 bg-slate-800 border-t border-slate-700 flex flex-col md:flex-row gap-4 justify-end items-center">
                                <form action="{{ route('requisitions.reject', $requisition->id) }}" method="POST" class="w-full md:w-auto">
                                    @csrf
                                    <div class="flex gap-2">
                                        <input type="text" name="note" placeholder="Reason for rejection..." class="w-full md:w-64 px-4 py-2.5 bg-slate-700 border border-slate-600 rounded-xl text-white placeholder-slate-400 focus:ring-red-500 focus:border-red-500 text-sm" required>
                                        <button type="submit" onclick="return confirm('Reject this request?')" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition shadow-lg shadow-red-500/30">
                                            Reject
                                        </button>
                                    </div>
                                </form>

                                <form action="{{ route('requisitions.approve', $requisition->id) }}" method="POST" class="w-full md:w-auto">
                                    @csrf
                                    <button type="submit" class="w-full px-8 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold rounded-xl transition shadow-lg shadow-green-500/30 flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Approve Document
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                </div>

                {{-- RIGHT COLUMN: ACTIONS & TRACKING --}}
                <div class="space-y-6">

                    {{-- 🔵 REQUESTER ZONE: UPLOAD & SUBMIT (DRAFT) --}}
                    @if($isRequester && $requisition->status_flow == 'DRAFT')
                        <div class="bg-white rounded-2xl shadow-xl border border-blue-100 p-6 relative overflow-hidden">
                            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-indigo-500"></div>

                           {{-- BAGIAN FILE UPLOAD DENGAN TOMBOL HAPUS (X) --}}
                            @if($requisition->attachment_partial)
                                <div class="flex items-center justify-between bg-green-50 p-3 rounded-lg border border-green-200 mb-4 group transition-all hover:bg-green-100">
                                    <div class="flex items-center gap-3 overflow-hidden">
                                        {{-- Ikon Dokumen --}}
                                        <div class="bg-white p-1.5 rounded-md shadow-sm border border-green-100">
                                            <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>

                                        <div class="flex flex-col">
                                            <span class="text-xs font-bold text-green-800">Document Uploaded</span>
                                            <a href="{{ asset('storage/' . $requisition->attachment_partial) }}" target="_blank" class="text-[10px] text-blue-600 hover:text-blue-800 hover:underline font-medium truncate max-w-[150px]">
                                                View / Check File
                                            </a>
                                        </div>
                                    </div>
                                    {{-- TOMBOL SILANG (HAPUS FILE) DENGAN SWEETALERT --}}
                                    <form id="removeFileForm" action="{{ route('requisitions.remove_partial', $requisition->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        {{-- Ubah type="submit" jadi type="button" dan tambah onclick --}}
                                        <button type="button" onclick="confirmRemoveFile()" class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-full transition-colors" title="Remove File">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </form>
                                </div>
                            @endif

                            {{-- PDF PREVIEW --}}
                            @if($requisition->attachment_partial)
                                <div class="mb-6">
                                    <h4 class="text-xs font-bold text-slate-500 uppercase mb-2">Uploaded Document Preview</h4>
                                    <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                                        <iframe src="{{ asset('storage/' . $requisition->attachment_partial) }}" class="w-full h-64" frameborder="0"></iframe>
                                    </div>
                                    <div class="text-right mt-1">
                                        <a href="{{ asset('storage/' . $requisition->attachment_partial) }}" target="_blank" class="text-xs text-blue-600 hover:underline font-bold">
                                            Open in Full Window ↗
                                        </a>
                                    </div>
                                </div>
                            @endif

                            <div class="mb-6 p-4 bg-slate-50 rounded-xl border border-dashed border-slate-300">
                                <p class="text-xs font-bold text-slate-500 uppercase mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    Step 1: Upload Signed Document <span class="text-red-600 font-bold">*REQUIRED</span>
                                </p>

                                {{-- INSTRUKSI DETAIL UNTUK REQUESTER --}}
                                <div class="mb-3 text-xs text-slate-500 leading-relaxed">
                                    <ol class="list-decimal list-inside space-y-1">
                                        <li>Print the PDF (Click button above).</li>
                                        <li><strong>Sign it yourself</strong> (Requester).</li>
                                        <li><strong>Request Manager's Wet Signature</strong> (Offline/Physical).</li>
                                        <li>Scan the document (Contains <strong>2 signatures</strong>).</li>
                                        <li>Upload the scanned file here.</li>
                                    </ol>
                                </div>

                                @if($requisition->attachment_partial)
                                    <div class="flex items-center justify-between bg-green-50 p-3 rounded-lg border border-green-200 mb-2">
                                        <span class="text-xs font-bold text-green-700 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            Document Uploaded
                                        </span>
                                        <a href="{{ asset('storage/' . $requisition->attachment_partial) }}" target="_blank" class="text-xs text-blue-600 hover:underline">Check File</a>
                                    </div>
                                @endif

                                <form action="{{ route('requisitions.upload_partial', $requisition->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="file" name="file_partial" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                                    <button type="submit" class="mt-2 w-full py-2 bg-white border border-slate-300 text-slate-600 text-xs font-bold rounded-lg hover:bg-slate-50">
                                        {{ $requisition->attachment_partial ? 'Replace File' : 'Upload File' }}
                                    </button>
                                </form>
                            </div>

                            <div>
                                <p class="text-xs font-bold text-slate-500 uppercase mb-2">Step 2: Submit to Manager</p>
                                <form action="{{ route('requisitions.submit', $requisition->id) }}" method="POST" id="submitForm">
                                    @csrf
                                    <button type="button" onclick="confirmSubmit({{ $requisition->attachment_partial ? 'true' : 'false' }})" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-500/30 transition transform hover:-translate-y-1 flex justify-center items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                        Submit for Approval
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                    {{-- 🟢 NEW: KONFIRMASI BARANG DATANG (Hanya muncul jika sudah APPROVED) --}}
                    @if($isRequester && $requisition->status_flow == 'APPROVED')
                        <div class="bg-white rounded-2xl shadow-xl border border-green-100 p-6 relative overflow-hidden">
                            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-green-400 to-emerald-500"></div>

                            <h3 class="text-lg font-black text-slate-800 mb-2">📦 Item Arrival Confirmation</h3>
                            <p class="text-sm text-slate-500 mb-4">Has the item arrived? Please upload a photo/delivery note to close this ticket.</p>

                            <form action="{{ route('requisitions.upload_evidence', $requisition->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Evidence / Photo</label>
                                    <input type="file" name="evidence_photo" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100" required>
                                </div>

                                <button type="submit" class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-lg shadow-green-500/30 transition flex justify-center items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Confirm Received & Complete Ticket
                                </button>
                            </form>
                        </div>
                    @endif

                    {{-- 🏁 TAMPILAN JIKA SUDAH COMPLETED --}}
                    @if($requisition->status_flow == 'COMPLETED')
                        <div class="bg-slate-100 rounded-xl p-6 border border-slate-200 mt-6 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                                <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="text-xl font-bold text-slate-800">Transaction Completed</h3>
                            <p class="text-slate-500 text-sm mb-4">This requisition has been fulfilled and closed.</p>

                            @if($requisition->evidence_photo)
                                <div class="mt-2">
                                    <p class="text-xs font-bold text-slate-400 uppercase mb-2">Evidence:</p>
                                    <img src="{{ asset('storage/' . $requisition->evidence_photo) }}" class="h-32 mx-auto rounded-lg border border-slate-300 shadow-sm">
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- TRACKING HISTORY --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                        <h3 class="text-sm font-bold text-slate-800 mb-4 uppercase">History</h3>
                        <div class="space-y-6 border-l-2 border-slate-100 ml-3 pl-6 relative">
                            @forelse($requisition->approvalQueues as $queue)
                                <div class="relative">
                                    <span class="absolute -left-[31px] top-1 w-4 h-4 rounded-full border-2 border-white shadow-sm
                                        {{ $queue->status == 'APPROVED' ? 'bg-green-500' : ($queue->status == 'REJECTED' ? 'bg-red-500' : 'bg-yellow-400') }}">
                                    </span>
                                    <p class="text-sm font-bold text-slate-800">{{ $queue->level_order == 1 ? 'Manager' : 'Director' }}</p>
                                    <p class="text-xs font-bold {{ $queue->status == 'APPROVED' ? 'text-green-600' : 'text-yellow-600' }}">{{ $queue->status }}</p>
                                    <p class="text-xs text-slate-400 mt-1">{{ $queue->approver->full_name }}</p>
                                </div>
                            @empty
                                <div class="relative">
                                    <span class="absolute -left-[31px] top-1 w-4 h-4 rounded-full bg-slate-200 border-2 border-white"></span>
                                    <p class="text-sm font-bold text-slate-400">Waiting submission...</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JAVASCRIPT --}}
    <script>
        // --- 1. LOGIC SUBMIT (Requester) ---
        function confirmSubmit(hasFile) {
            if (typeof Swal === 'undefined') {
                alert('Error: SweetAlert2 resource not loaded.');
                return;
            }

            if (!hasFile) {
                Swal.fire({
                    title: 'Upload Required!',
                    text: '⛔ You MUST upload the signed document (Step 1) before submitting.',
                    icon: 'error',
                    confirmButtonColor: '#1e293b',
                    confirmButtonText: 'OK, I will upload'
                });
                return;
            }

            // MODAL WARNING UPDATE: Tanya apakah sudah ada 2 TTD?
            Swal.fire({
                title: '⚠️ Final Check!',
                html: "Before submitting, please confirm:<br><br><strong>Does the uploaded file contain BOTH your signature AND your Manager's signature?</strong>",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Yes, Both Signatures are Present!',
                cancelButtonText: 'No, let me check'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('submitForm').submit();
                }
            });
        }

        // --- 2. LOGIC ROLLBACK (Admin) ---
        function confirmRollback(statusRaw) {
            if (typeof Swal === 'undefined') {
                alert('Error: SweetAlert2 resource not loaded.');
                return;
            }

            let currentStatus = String(statusRaw).trim().toUpperCase();
            let inputOptions = {};
            let defaultVal = 'TO_DRAFT';

            if (currentStatus === 'PARTIALLY_APPROVED') {
                inputOptions = {
                    'TO_MANAGER': '⬅️ Back to Manager (Step 1)',
                    'TO_DRAFT':   '⏮️ Reset to Draft (Requester)'
                };
                defaultVal = 'TO_MANAGER';
            }
            else if (currentStatus === 'ON_PROGRESS') {
                inputOptions = {
                    'TO_DRAFT': '⏮️ Reset to Draft (Requester)'
                };
            } else {
                Swal.fire('Error', 'Current status ('+currentStatus+') is not valid for rollback.', 'error');
                return;
            }

            Swal.fire({
                title: '⚠️ Admin Rollback',
                text: "Choose rollback target level:",
                icon: 'warning',
                input: 'radio',
                inputOptions: inputOptions,
                inputValue: defaultVal,
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Next ➡️',
                inputValidator: (value) => {
                    if (!value) return 'You need to choose a target!'
                }
            }).then((step1) => {
                if (step1.isConfirmed) {
                    const targetLevel = step1.value;
                    Swal.fire({
                        title: '📝 Reason Required',
                        text: 'Please explain why this rollback is necessary:',
                        input: 'textarea',
                        inputPlaceholder: 'e.g. Wrong attachment, Budget revision...',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Execute Rollback 🚀',
                        inputValidator: (value) => {
                            if (!value || value.length < 5) return 'Reason is too short (min 5 chars)!'
                        }
                    }).then((step2) => {
                        if (step2.isConfirmed) {
                            document.getElementById('rollback_target').value = targetLevel;
                            document.getElementById('rollback_reason').value = step2.value;
                            document.getElementById('rollbackForm').submit();
                        }
                    });
                }
            });
        }

        // --- 3. LOGIC REMOVE FILE (Requester) ---
        function confirmRemoveFile() {
            Swal.fire({
                title: 'Remove File?',
                text: "Are you sure you want to delete this uploaded document? You will need to upload again.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33', // Merah untuk bahaya
                cancelButtonColor: '#3085d6', // Biru untuk batal
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form secara manual via JS
                    document.getElementById('removeFileForm').submit();
                }
            });
        }
    </script>
</x-app-layout>

{{-- <x-app-layout>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">

        @php
            $currentUser = Auth::user();
            $isRequester = ($currentUser->employee_id == $rl->requester_id);

            // Check Current Approver
            $myPendingApproval = $rl->approvalQueues
                                    ->where('status', 'PENDING')
                                    ->where('approver_id', $currentUser->employee_id)
                                    ->first();
        @endphp

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <nav class="flex mb-1" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li><a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-900">Dashboard</a></li>
                        <li><span class="text-gray-400">/</span></li>
                        <li class="text-gray-700 font-bold">RL Detail</li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white flex items-center gap-3">
                    {{ $rl->rl_no }}

                    @if($rl->status_flow == 'DRAFT')
                        <span class="bg-gray-200 text-gray-800 text-sm font-bold px-3 py-1 rounded-full">Draft (Action Required)</span>
                    @elseif($rl->status_flow == 'ON_PROGRESS')
                        <span class="bg-orange-100 text-orange-800 text-sm font-bold px-3 py-1 rounded-full border border-orange-200">Waiting Approval (Manager)</span>
                    @elseif($rl->status_flow == 'PARTIALLY_APPROVED')
                        <span class="bg-purple-100 text-purple-800 text-sm font-bold px-3 py-1 rounded-full border border-purple-200">Waiting Director</span>
                    @elseif($rl->status_flow == 'WAITING_SUPPLY')
                        <span class="bg-green-100 text-green-800 text-sm font-bold px-3 py-1 rounded-full border border-green-200">Final Approved / Waiting Supply</span>
                    @elseif($rl->status_flow == 'REJECTED')
                        <span class="bg-red-100 text-red-800 text-sm font-bold px-3 py-1 rounded-full border border-red-200">Rejected</span>
                    @endif
                </h1>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('requisitions.print', $rl->id) }}" target="_blank" class="flex items-center px-5 py-2.5 bg-gray-800 text-white font-semibold rounded-lg hover:bg-gray-700 shadow-md transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Download System PDF
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">

                <div class="p-6 bg-white border border-blue-200 rounded-xl shadow-md dark:bg-gray-800 dark:border-blue-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                        <span class="bg-blue-100 text-blue-600 p-2 rounded-lg mr-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </span>
                        Workflow Actions
                    </h3>

                    @if($rl->attachment_partial)
                        <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                <div>
                                    <p class="font-bold text-gray-700 text-sm">Signed Document (Partial/Full)</p>
                                    <p class="text-xs text-gray-500">Uploaded by {{ $rl->requester->full_name }}</p>
                                </div>
                            </div>
                            <a href="{{ asset('storage/' . $rl->attachment_partial) }}" target="_blank" class="text-blue-600 font-bold text-sm hover:underline">View Document</a>
                        </div>
                    @endif

                    @if($isRequester)

                        @if($rl->status_flow == 'DRAFT')
                            <div class="mt-4 border-t pt-4">
                                <div class="bg-yellow-50 text-yellow-800 text-sm p-4 rounded-lg mb-4 border border-yellow-200">
                                    <strong>Step 1 Instructions:</strong>
                                    <ul class="list-disc ml-5 mt-1 space-y-1">
                                        <li>Download the System PDF (Top Right Button).</li>
                                        <li>Print the PDF.</li>
                                        <li><strong>Sign it (Wet Signature)</strong> and ask your <strong>Manager</strong> to sign it too.</li>
                                        <li>Scan the document and <strong>Upload</strong> it below.</li>
                                    </ul>
                                </div>

                                @if(!$rl->attachment_partial)
                                    <form action="{{ route('requisitions.upload_partial', $rl->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="block mb-2 text-sm font-bold text-gray-700">Upload Scanned Document</label>
                                            <input type="file" name="file_partial" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50" required accept="application/pdf">
                                        </div>
                                        <button type="submit" class="w-full px-4 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition">Upload File</button>
                                    </form>
                                @else
                                    <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-lg">
                                        <div>
                                            <p class="font-bold text-green-800">Ready to Submit</p>
                                            <p class="text-xs text-green-600">Document uploaded. You can now submit to Manager.</p>
                                        </div>
                                        <form action="{{ route('requisitions.submit-draft', $rl->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Submit to Manager?')" class="px-5 py-2.5 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 shadow-md flex items-center">
                                                Submit to Manager <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if($rl->status_flow == 'PARTIALLY_APPROVED')
                            <div class="mt-4 border-t pt-4">
                                <div class="bg-purple-50 text-purple-800 text-sm p-4 rounded-lg mb-4 border border-purple-200">
                                    <strong>Step 2 Instructions:</strong>
                                    <ul class="list-disc ml-5 mt-1 space-y-1">
                                        <li>Manager has approved digitally.</li>
                                        <li>Please bring the physical document to the <strong>Director</strong> for signature.</li>
                                        <li>Once signed by Director, <strong>Scan & Upload Final</strong> below.</li>
                                    </ul>
                                </div>

                                <form action="{{ route('requisitions.upload_final', $rl->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="block mb-2 text-sm font-bold text-gray-700">Upload Final Document (All Signatures)</label>
                                        <input type="file" name="file_final" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50" required accept="application/pdf">
                                    </div>
                                    <button type="submit" class="w-full px-4 py-2.5 bg-purple-600 text-white font-bold rounded-lg hover:bg-purple-700 transition">Submit Final to Director</button>
                                </form>
                            </div>
                        @endif

                    @endif

                    @if($myPendingApproval)
                        <div class="mt-6 pt-4 border-t border-gray-200 bg-gray-50 p-4 rounded-xl">
                            <h4 class="font-bold text-gray-800 mb-2">Action Required: {{ $currentUser->position->position_name }}</h4>

                            @if(!$rl->attachment_partial && $myPendingApproval->level_order == 1)
                                <p class="text-sm text-red-600 mb-3 font-bold">⚠️ Alert: Requester has not uploaded the signed document yet.</p>
                            @else
                                <p class="text-sm text-gray-600 mb-4">Please verify the document uploaded above. If the wet signature is present and valid, click <b>Approve</b>.</p>
                            @endif

                            <div class="flex gap-3">
                                <button type="button" onclick="openRejectModal()" class="flex-1 px-4 py-2.5 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition shadow">
                                    Reject
                                </button>

                                <form action="{{ route('approvals.approve', $rl->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Confirm Approval?');">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-2.5 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 transition shadow">
                                        Approve
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                </div>

                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Document Details</h3>
                    <div class="grid grid-cols-2 md:grid-cols-2 gap-6 text-sm">
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase">Subject</p>
                            <p class="font-medium text-gray-900 dark:text-white mt-1">{{ $rl->subject }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase">Priority</p>
                            <span class="font-bold text-gray-800 mt-1 block">{{ $rl->priority }}</span>
                        </div>
                    </div>
                    @if($rl->remark)
                    <div class="mt-4 p-3 bg-yellow-50 rounded border border-yellow-100 text-yellow-800 text-xs">
                        <strong>Note:</strong> {{ $rl->remark }}
                    </div>
                    @endif
                </div>

            </div>

            <div class="space-y-6">
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 sticky top-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Approval Timeline</h3>
                    <ol class="relative border-l-2 border-gray-200 dark:border-gray-700 ml-3 space-y-8">
                        <li class="ml-6">
                            <span class="absolute flex items-center justify-center w-8 h-8 bg-blue-100 rounded-full -left-4 ring-4 ring-white dark:ring-gray-900 dark:bg-blue-900 shadow-sm">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            </span>
                            <h3 class="flex items-center mb-1 text-sm font-bold text-gray-900 dark:text-white">Created</h3>
                            <time class="block mb-1 text-xs font-normal text-gray-500">{{ \Carbon\Carbon::parse($rl->created_at)->format('d M Y, H:i') }}</time>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">by {{ $rl->requester->full_name }}</p>
                        </li>

                        @foreach($rl->approvalQueues as $approval)
                        <li class="ml-6">
                            @if($approval->status == 'APPROVED')
                                <span class="absolute flex items-center justify-center w-8 h-8 bg-green-100 rounded-full -left-4 ring-4 ring-white dark:ring-gray-900 dark:bg-green-900 shadow-sm">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </span>
                            @elseif($approval->status == 'REJECTED')
                                <span class="absolute flex items-center justify-center w-8 h-8 bg-red-100 rounded-full -left-4 ring-4 ring-white dark:ring-gray-900 dark:bg-red-900 shadow-sm">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </span>
                            @else
                                <span class="absolute flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full -left-4 ring-4 ring-white dark:ring-gray-700 shadow-sm">
                                    <span class="w-2.5 h-2.5 rounded-full bg-gray-400 animate-pulse"></span>
                                </span>
                            @endif

                            <h3 class="mb-1 text-sm font-bold text-gray-900 dark:text-white">
                                {{ $approval->approver->position->position_name ?? 'Approver' }}
                            </h3>

                            @if($approval->status == 'APPROVED')
                                <time class="block mb-1 text-xs font-normal text-green-600">Approved on {{ \Carbon\Carbon::parse($approval->approved_at)->format('d M Y, H:i') }}</time>
                            @elseif($approval->status == 'REJECTED')
                                <time class="block mb-1 text-xs font-normal text-red-600">Rejected</time>
                                <div class="p-2 mt-1 bg-red-50 border border-red-100 rounded text-xs text-red-600 italic">"{{ $approval->reason_rejection }}"</div>
                            @else
                                <span class="bg-orange-100 text-orange-800 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wide">Pending Review</span>
                            @endif

                            <p class="text-sm font-medium text-gray-600 mt-1">{{ $approval->approver->full_name }}</p>
                        </li>
                        @endforeach
                    </ol>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-gray-800 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                        <h3 class="text-sm font-bold text-gray-700 dark:text-white uppercase tracking-wider">System Generated PDF</h3>
                    </div>
                    <div class="w-full h-[400px] bg-gray-100">
                        <iframe src="{{ route('requisitions.print', $rl->id) }}#toolbar=0" class="w-full h-full" frameborder="0"></iframe>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div id="rejectModal" class="fixed inset-0 z-50 hidden bg-gray-900 bg-opacity-60 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md p-6">
            <h3 class="text-lg font-bold text-red-600 mb-4">Reject Requisition</h3>
            @if($myPendingApproval)
            <form action="{{ route('approvals.reject', $myPendingApproval->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reason (Required)</label>
                    <textarea name="reason" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" required placeholder="E.g. Document incomplete..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700">Confirm Reject</button>
                </div>
            </form>
            @endif
        </div>
    </div>
    <script>
        function openRejectModal() { document.getElementById('rejectModal').classList.remove('hidden'); }
        function closeRejectModal() { document.getElementById('rejectModal').classList.add('hidden'); }
    </script>
</x-app-layout> --}}
