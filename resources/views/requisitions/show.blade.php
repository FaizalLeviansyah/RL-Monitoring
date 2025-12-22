<x-app-layout>
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
</x-app-layout>
