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
                // Ambil queue rejection terakhir (paling baru)
                $rejectLog = $rl->approvalQueues()
                                ->where('status', 'REJECTED')
                                ->orderBy('id', 'desc') // Urutkan dari yg terbaru
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
                    @elseif($rl->status_flow == 'ON_PROGRESS')
                        <span class="bg-orange-100 text-orange-800 text-sm font-bold px-3 py-1 rounded-full border border-orange-200">Waiting Approval</span>
                    @elseif($rl->status_flow == 'APPROVED')
                        <span class="bg-green-100 text-green-800 text-sm font-bold px-3 py-1 rounded-full border border-green-200">Approved</span>
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

                    <form action="{{ route('approvals.approve', $myPendingApproval->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin menyetujui dokumen ini?');">
                        @csrf
                        <button type="submit" class="flex items-center px-5 py-2.5 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 shadow-md transition transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Approve
                        </button>
                    </form>
                @endif
                <a href="{{ route('requisitions.print', $rl->id) }}" target="_blank" class="flex items-center px-5 py-2.5 bg-gray-800 text-white font-semibold rounded-lg hover:bg-gray-700 shadow-md transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Print PDF
                </a>

                @if($rl->status_flow == 'DRAFT' && Auth::user()->employee_id == $rl->requester_id)
                <form action="{{ route('requisitions.submit-draft', $rl->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin data sudah benar? Dokumen akan dikirim ke atasan.');">
                    @csrf
                    <button type="submit" class="flex items-center px-5 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 shadow-md transition transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Submit Approval
                    </button>
                </form>
                @endif
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
                            @if($rl->priority == 'Top Urgent')
                                <span class="inline-flex items-center bg-red-100 text-red-800 text-xs font-bold px-2.5 py-0.5 rounded mt-1">
                                    <span class="w-2 h-2 mr-1 bg-red-500 rounded-full animate-pulse"></span> TOP URGENT
                                </span>
                            @elseif($rl->priority == 'Urgent')
                                <span class="inline-flex items-center bg-orange-100 text-orange-800 text-xs font-bold px-2.5 py-0.5 rounded mt-1">Urgent</span>
                            @else
                                <span class="inline-flex items-center bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded mt-1">Normal</span>
                            @endif
                        </div>

                        <div>
                            <p class="text-gray-500 uppercase text-xs font-bold tracking-wider">Requester</p>
                            <div class="flex items-center mt-1">
                                @if($rl->requester->profile_photo_path)
                                    <img class="w-6 h-6 rounded-full mr-2" src="{{ asset('storage/' . $rl->requester->profile_photo_path) }}">
                                @else
                                    <div class="w-6 h-6 rounded-full bg-gray-300 flex items-center justify-center text-xs font-bold mr-2">{{ substr($rl->requester->full_name, 0, 1) }}</div>
                                @endif
                                <div>
                                    <p class="font-bold text-gray-900 dark:text-white text-xs">{{ $rl->requester->full_name }}</p>
                                    <p class="text-[10px] text-gray-500">{{ $rl->requester->department->department_name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-100">
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Subject / Perihal</p>
                        <p class="text-gray-900 dark:text-white font-medium text-base mt-1 italic">"{{ $rl->subject }}"</p>
                    </div>

                    @if($rl->remark)
                    <div class="mt-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200 text-yellow-800 text-sm flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <strong class="block mb-1">Notes:</strong>
                            {{ $rl->remark }}
                        </div>
                    </div>
                    @endif
                </div>

                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-gray-800 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 dark:bg-gray-700 dark:border-gray-600 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-gray-700 dark:text-white uppercase tracking-wider flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Document Preview
                        </h3>
                        <span class="text-xs text-gray-500 bg-gray-200 px-2 py-1 rounded">Generated PDF</span>
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
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700">Reject Dokumen</button>
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
