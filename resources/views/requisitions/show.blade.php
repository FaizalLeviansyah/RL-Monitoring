<x-app-layout>
    <div class="max-w-7xl mx-auto p-4">

        @php
            $currentUser = Auth::user();

            // Cari antrian approval yang:
            // 1. Milik RL ini
            // 2. Statusnya PENDING
            // 3. Assigned ke User yang sedang login
            $myPendingApproval = $rl->approvalQueues
                                    ->where('status', 'PENDING')
                                    ->where('approver_id', $currentUser->employee_id)
                                    ->first();
        @endphp

        @if($rl->status_flow == 'REJECTED')
            @php
                $rejectLog = $rl->approvalQueues()
                                ->where('status', 'REJECTED')
                                ->orderBy('id', 'desc')
                                ->first();
            @endphp
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm dark:bg-gray-800 dark:border-red-600 animate-pulse">
                <div class="flex justify-between items-start">
                    <div class="flex">
                        <svg class="flex-shrink-0 w-6 h-6 text-red-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/></svg>
                        <div>
                            <h3 class="text-lg font-bold text-red-800 dark:text-red-400">DOKUMEN DITOLAK (REJECTED)</h3>
                            <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                <p><strong>Alasan Penolakan:</strong></p>
                                <p class="italic bg-red-100 p-2 rounded mt-1">"{{ $rejectLog->reason_rejection ?? '-' }}"</p>
                                <p class="mt-2 text-xs">Oleh: {{ $rejectLog->approver->full_name ?? 'Approver' }}</p>
                            </div>
                            <p class="mt-3 text-xs font-semibold text-red-600 uppercase tracking-wide">Tindakan Diperlukan: Silakan Revisi Dokumen.</p>
                        </div>
                    </div>

                    @if(Auth::user()->employee_id == $rl->requester_id)
                    <a href="{{ route('requisitions.revise', $rl->id) }}" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-bold rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center shadow-lg transition transform hover:-translate-y-1">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Revisi & Buat Baru
                    </a>
                    @endif
                </div>
            </div>
        @endif

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <nav class="flex mb-1" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li><a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-900">Dashboard</a></li>
                        <li><span class="text-gray-400">/</span></li>
                        <li class="text-gray-700 font-bold">Detail RL</li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white flex items-center gap-3">
                    {{ $rl->rl_no }}
                    @if($rl->status_flow == 'DRAFT')
                        <span class="bg-gray-200 text-gray-800 text-sm font-bold px-3 py-1 rounded-full">Draft</span>
                    @elseif($rl->status_flow == 'ON_PROGRESS' || $rl->status_flow == 'PARTIALLY_APPROVED')
                        <span class="bg-orange-100 text-orange-800 text-sm font-bold px-3 py-1 rounded-full border border-orange-200">Waiting Approval</span>
                    @elseif($rl->status_flow == 'APPROVED')
                        <span class="bg-purple-100 text-purple-800 text-sm font-bold px-3 py-1 rounded-full border border-purple-200">Final / Waiting Supply</span>
                    @elseif($rl->status_flow == 'COMPLETED')
                        <span class="bg-green-100 text-green-800 text-sm font-bold px-3 py-1 rounded-full border border-green-200">Completed</span>
                    @elseif($rl->status_flow == 'REJECTED')
                        <span class="bg-red-100 text-red-800 text-sm font-bold px-3 py-1 rounded-full border border-red-200">Rejected</span>
                    @endif
                </h1>
            </div>

            <div class="flex gap-3">
                @if($myPendingApproval)
                    <button type="button" onclick="openRejectModal()" class="flex items-center px-5 py-2.5 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 shadow-md transition transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Reject
                    </button>

                    <form action="{{ route('approvals.approve', $rl->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin dokumen fisik sudah valid?');">
                        @csrf
                        <button type="submit" class="flex items-center px-5 py-2.5 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 shadow-md transition transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Validasi / Approve
                        </button>
                    </form>
                @endif

                <a href="{{ route('requisitions.print', $rl->id) }}" target="_blank" class="flex items-center px-5 py-2.5 bg-gray-800 text-white font-semibold rounded-lg hover:bg-gray-700 shadow-md transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Print PDF
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">

                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-100">Document Information</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-sm">
                        <div>
                            <p class="text-gray-500 uppercase text-xs font-bold tracking-wider">Request Date</p>
                            <p class="font-bold text-gray-900 dark:text-white mt-1">{{ \Carbon\Carbon::parse($rl->request_date)->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 uppercase text-xs font-bold tracking-wider">Required Date</p>
                            <p class="font-bold text-red-600 mt-1">
                                {{ $rl->required_date ? \Carbon\Carbon::parse($rl->required_date)->format('d M Y') : '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500 uppercase text-xs font-bold tracking-wider">Priority</p>
                            <span class="font-bold text-gray-800 mt-1 block">{{ $rl->priority }}</span>
                        </div>
                        <div>
                            <p class="text-gray-500 uppercase text-xs font-bold tracking-wider">Requester</p>
                            <p class="font-bold text-gray-900 dark:text-white mt-1">{{ $rl->requester->full_name }}</p>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-100">
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Subject</p>
                        <p class="text-gray-900 dark:text-white font-medium text-base mt-1 italic">"{{ $rl->subject }}"</p>
                    </div>

                    @if($rl->remark)
                    <div class="mt-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200 text-yellow-800 text-sm">
                        <strong>Notes:</strong> {{ $rl->remark }}
                    </div>
                    @endif
                </div>

                <div class="p-6 bg-white border border-blue-200 rounded-xl shadow-md dark:bg-gray-800 dark:border-blue-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                        <span class="bg-blue-100 text-blue-600 p-2 rounded-lg mr-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </span>
                        📂 Digital Document Filing (Hybrid)
                    </h3>

                    @if($rl->status_flow == 'DRAFT' && Auth::id() == $rl->requester_id)
                        <div class="mb-4 p-4 bg-blue-50 text-blue-800 text-sm rounded-lg border border-blue-200">
                            <strong>Langkah 1:</strong> Download PDF -> Cetak -> Tanda Tangan Basah (Anda & Manager) -> Scan -> Upload Disini.
                        </div>
                        <form action="{{ route('requisitions.upload_partial', $rl->id) }}" method="POST" enctype="multipart/form-data" class="flex gap-3 items-end">
                            @csrf
                            <div class="flex-1">
                                <label class="block mb-2 text-sm font-bold text-gray-700">Upload Scan Tahap 1 (TTD Manager)</label>
                                <input type="file" name="file_partial" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" required accept="application/pdf">
                            </div>
                            <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow transition">
                                Upload & Ajukan
                            </button>
                        </form>
                    @endif

                    @if($rl->attachment_partial)
                        <div class="flex items-center justify-between p-3 bg-gray-50 border rounded-lg mb-3">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div>
                                    <p class="font-bold text-gray-700 text-sm">Dokumen Tahap 1 (TTD Manager)</p>
                                    <p class="text-xs text-gray-500">Telah diupload requester</p>
                                </div>
                            </div>
                            <a href="{{ asset('storage/' . $rl->attachment_partial) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm font-semibold underline">Lihat Dokumen</a>
                        </div>
                    @endif

                    @if($rl->status_flow == 'PARTIALLY_APPROVED' && Auth::id() == $rl->requester_id)
                        <hr class="my-4 border-gray-200">
                        <div class="mb-4 p-4 bg-orange-50 text-orange-800 text-sm rounded-lg border border-orange-200">
                            <strong>Langkah 2:</strong> Dokumen Tahap 1 Valid. Silakan minta TTD Basah Direktur -> Scan -> Upload Final Disini.
                        </div>
                        <form action="{{ route('requisitions.upload_final', $rl->id) }}" method="POST" enctype="multipart/form-data" class="flex gap-3 items-end">
                            @csrf
                            <div class="flex-1">
                                <label class="block mb-2 text-sm font-bold text-gray-700">Upload Scan Final (Lengkap)</label>
                                <input type="file" name="file_final" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" required accept="application/pdf">
                            </div>
                            <button type="submit" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-lg shadow transition">
                                Submit Final
                            </button>
                        </form>
                    @endif

                    @if($rl->attachment_final)
                        <div class="flex items-center justify-between p-3 bg-purple-50 border border-purple-100 rounded-lg mb-3">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div>
                                    <p class="font-bold text-purple-800 text-sm">Dokumen Final (TTD Lengkap)</p>
                                    <p class="text-xs text-purple-600">Approved by Director</p>
                                </div>
                            </div>
                            <a href="{{ asset('storage/' . $rl->attachment_final) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm font-semibold underline">Lihat Dokumen</a>
                        </div>
                    @endif

                    @if($rl->status_flow == 'APPROVED' && Auth::id() == $rl->requester_id)
                        <hr class="my-4 border-gray-200">
                        <div class="mb-4 p-4 bg-green-50 text-green-800 text-sm rounded-lg border border-green-200">
                            <strong>Closing Tiket:</strong> Barang sudah diterima? Upload foto bukti barang untuk menyelesaikan tiket.
                        </div>
                        <form action="{{ route('requisitions.upload_evidence', $rl->id) }}" method="POST" enctype="multipart/form-data" class="flex gap-3 items-end">
                            @csrf
                            <div class="flex-1">
                                <label class="block mb-2 text-sm font-bold text-gray-700">Upload Foto Barang</label>
                                <input type="file" name="evidence_photo" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" required accept="image/*">
                            </div>
                            <button type="submit" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg shadow transition">
                                Barang Diterima
                            </button>
                        </form>
                    @endif

                    @if($rl->evidence_photo)
                        <div class="mt-4 p-3 border rounded bg-gray-50">
                            <p class="font-bold text-sm mb-2">📸 Bukti Barang:</p>
                            <img src="{{ asset('storage/' . $rl->evidence_photo) }}" class="h-48 rounded border shadow-sm">
                        </div>
                    @endif
                </div>

                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-gray-800 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 dark:bg-gray-700 dark:border-gray-600 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-gray-700 dark:text-white uppercase tracking-wider flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            System Generated PDF
                        </h3>
                        <span class="text-xs text-gray-500 bg-gray-200 px-2 py-1 rounded">Preview Only</span>
                    </div>
                    <div class="w-full h-[700px] bg-gray-100">
                        <iframe src="{{ route('requisitions.print', $rl->id) }}#toolbar=0" class="w-full h-full" frameborder="0"></iframe>
                    </div>
                </div>

            </div>

            <div class="space-y-6">
                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 sticky top-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Approval Timeline</h3>
                    <ol class="relative border-l-2 border-gray-200 dark:border-gray-700 ml-3 space-y-8">
                        <li class="ml-6">
                            <span class="absolute flex items-center justify-center w-8 h-8 bg-blue-100 rounded-full -left-4 ring-4 ring-white dark:ring-gray-900 dark:bg-blue-900 shadow-sm">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
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
                                <span class="absolute flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full -left-4 ring-4 ring-white dark:ring-gray-900 dark:bg-gray-700 shadow-sm">
                                    <span class="w-2.5 h-2.5 rounded-full bg-gray-400 animate-pulse"></span>
                                </span>
                            @endif

                            <h3 class="mb-1 text-sm font-bold text-gray-900 dark:text-white">
                                {{ $approval->approver->position->position_name ?? 'Approver' }} <span class="text-xs text-gray-400 font-normal">(Lvl {{ $approval->level_order }})</span>
                            </h3>

                            @if($approval->status == 'APPROVED')
                                <time class="block mb-1 text-xs font-normal text-green-600">Approved on {{ \Carbon\Carbon::parse($approval->approved_at)->format('d M Y, H:i') }}</time>
                            @elseif($approval->status == 'REJECTED')
                                <time class="block mb-1 text-xs font-normal text-red-600">Rejected on {{ \Carbon\Carbon::parse($approval->updated_at)->format('d M Y') }}</time>
                                <div class="p-2 mt-1 bg-red-50 border border-red-100 rounded text-xs text-red-600 italic">"{{ $approval->reason_rejection }}"</div>
                            @else
                                <span class="bg-orange-100 text-orange-800 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wide">Pending Review</span>
                            @endif

                            <p class="text-sm font-medium text-gray-600 mt-1">{{ $approval->approver->full_name }}</p>
                        </li>
                        @endforeach
                    </ol>
                </div>

                <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4 border-b pb-2">Item Summary</h3>
                    <ul class="space-y-3">
                        @foreach($rl->items as $item)
                        <li class="flex justify-between items-center text-sm">
                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $item->item_name }}</span>
                            <span class="bg-gray-100 text-gray-800 text-xs font-bold px-2 py-1 rounded">{{ $item->qty }} {{ $item->uom }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

        </div>
    </div>

    @if($myPendingApproval)
    <div id="rejectModal" class="fixed inset-0 z-50 hidden bg-gray-900 bg-opacity-60 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md p-6">
            <h3 class="text-lg font-bold text-red-600 mb-4">Reject Requisition</h3>
            <form action="{{ route('approvals.reject', $myPendingApproval->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Alasan Penolakan (Wajib)</label>
                    <textarea name="reason" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required placeholder="Contoh: Stok barang masih tersedia..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700">Reject Dokument</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function openRejectModal() {
            document.getElementById('rejectModal').classList.remove('hidden');
        }
        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }
    </script>
    @endif
</x-app-layout>