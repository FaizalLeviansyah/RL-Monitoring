<x-app-layout>
    <div class="pt-2 pb-12 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

<div class="flex items-center justify-between mb-6 animate-fade-in-down">

                <div class="flex items-center space-x-4">
                    <a href="{{ url()->previous() }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-blue-600 hover:border-blue-300 transition-all shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    <div>
                        <h2 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight">
                            Requisition Details
                        </h2>
                        <p class="text-slate-500 text-sm">Reference No: <span class="font-mono font-bold text-slate-700">{{ $requisition->rl_no }}</span></p>
                    </div>
                </div>

                <div class="flex items-center gap-3">

                    @if(in_array($requisition->status_flow, ['DRAFT', 'REJECTED']) && Auth::id() == $requisition->requester_id)
                    <a href="{{ route('requisitions.edit', $requisition->id) }}" class="flex items-center px-4 py-2.5 bg-white border border-slate-200 text-slate-700 text-sm font-bold rounded-xl hover:bg-slate-50 hover:text-blue-600 transition-all shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </a>
                    @endif

                    <a href="{{ route('requisitions.print', $requisition->id) }}" target="_blank" class="group flex items-center px-5 py-2.5 bg-slate-800 text-white text-sm font-bold rounded-xl hover:bg-slate-700 transition-all shadow-lg hover:shadow-slate-500/30">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Print PDF
                    </a>
                </div>
            </div>


            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-2 space-y-6 animate-fade-in-up">

                    <div class="bg-white relative rounded-[1.5rem] shadow-xl shadow-slate-200/50 overflow-hidden border border-slate-100">

                        @php
                            $statusColor = match($requisition->status_flow) {
                                'ON_PROGRESS' => 'bg-orange-500',
                                'PARTIALLY_APPROVED' => 'bg-purple-500',
                                'APPROVED' => 'bg-green-500',
                                'REJECTED' => 'bg-red-500',
                                'WAITING_SUPPLY' => 'bg-yellow-500',
                                'COMPLETED' => 'bg-teal-500',
                                default => 'bg-slate-500'
                            };
                        @endphp
                        <div class="h-1.5 w-full {{ $statusColor }}"></div>

                        <div class="p-8 md:p-10">

                            <div class="flex justify-between items-start border-b border-slate-100 pb-6 mb-6">
                                <div class="flex items-center">
                                    <img src="{{ asset('images/Logo_PT_ASM.jpg') }}" alt="Logo" class="h-12 w-auto mr-4 mix-blend-multiply">
                                    <div>
                                        <h3 class="text-lg font-extrabold text-slate-800">PT. Amarin Ship Management</h3>
                                        <p class="text-xs text-slate-400 font-medium tracking-wide">INTERNAL REQUISITION FORM</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="block text-xs text-slate-400 uppercase tracking-wider font-bold">Request Date</span>
                                    <span class="block text-lg font-bold text-slate-700">{{ \Carbon\Carbon::parse($requisition->request_date)->format('d F Y') }}</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-6 mb-8">
                                <div>
                                    <label class="text-[10px] uppercase tracking-widest font-bold text-slate-400">Requester</label>
                                    <div class="flex items-center mt-1">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-600 mr-2">
                                            {{ substr($requisition->requester->full_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-800">{{ $requisition->requester->full_name }}</p>
                                            <p class="text-xs text-slate-500">{{ $requisition->requester->position->position_name ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-[10px] uppercase tracking-widest font-bold text-slate-400">Department</label>
                                    <p class="text-sm font-bold text-slate-800 mt-2">{{ $requisition->requester->department->department_name ?? '-' }}</p>
                                </div>
                                <div>
                                    <label class="text-[10px] uppercase tracking-widest font-bold text-slate-400">Subject / Purpose</label>
                                    <p class="text-sm font-bold text-slate-800 mt-2">{{ $requisition->subject }}</p>
                                </div>
                                <div>
                                    <label class="text-[10px] uppercase tracking-widest font-bold text-slate-400">Required Date</label>
                                    <p class="text-sm font-bold text-red-600 mt-2 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ \Carbon\Carbon::parse($requisition->required_date)->format('d M Y') }}
                                    </p>
                                </div>
                            </div>

                            <div class="mb-8">
                                <label class="text-[10px] uppercase tracking-widest font-bold text-slate-400 mb-2 block">Requested Items</label>
                                <div class="overflow-hidden rounded-xl border border-slate-200">
                                    <table class="w-full text-sm text-left">
                                        <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-200">
                                            <tr>
                                                <th class="px-4 py-3 w-10 text-center">#</th>
                                                <th class="px-4 py-3">Item Description</th>
                                                <th class="px-4 py-3 text-center">Qty</th>
                                                <th class="px-4 py-3 text-center">UoM</th>
                                                <th class="px-4 py-3">Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            @foreach($requisition->items as $index => $item)
                                            <tr class="hover:bg-blue-50/50 transition-colors">
                                                <td class="px-4 py-3 text-center text-slate-400">{{ $index + 1 }}</td>
                                                <td class="px-4 py-3 font-semibold text-slate-700">
                                                    {{ $item->item_name }}
                                                    @if($item->part_number)
                                                        <span class="block text-xs text-slate-400 font-normal">P/N: {{ $item->part_number }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-center font-bold text-slate-800">{{ $item->qty + 0 }}</td>
                                                <td class="px-4 py-3 text-center text-slate-500 text-xs">{{ $item->uom }}</td>
                                                <td class="px-4 py-3 text-slate-500 text-xs italic">{{ $item->description ?? '-' }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <label class="text-[10px] uppercase tracking-widest font-bold text-slate-400">Additional Remarks</label>
                                    <div class="mt-2 p-4 bg-slate-50 rounded-xl border border-slate-100 text-sm text-slate-600 italic">
                                        "{{ $requisition->remark ?? 'No additional remarks provided.' }}"
                                    </div>
                                </div>
                                <div>
                                    <label class="text-[10px] uppercase tracking-widest font-bold text-slate-400">Attachments</label>
                                    <div class="mt-2 space-y-2">
                                        @if($requisition->attachment_partial)
                                        <a href="{{ asset('storage/' . $requisition->attachment_partial) }}" target="_blank" class="flex items-center p-3 bg-blue-50 border border-blue-100 rounded-xl hover:bg-blue-100 transition-colors group cursor-pointer">
                                            <div class="p-2 bg-white rounded-lg text-blue-600 mr-3 shadow-sm">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                            </div>
                                            <div class="overflow-hidden">
                                                <p class="text-xs font-bold text-blue-800 truncate">Supporting Document</p>
                                                <p class="text-[10px] text-blue-500">Click to view file</p>
                                            </div>
                                        </a>
                                        @else
                                        <div class="text-xs text-slate-400 italic">No attachments found.</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1 space-y-6">

                    <div class="bg-white/80 backdrop-blur-xl border border-slate-100 rounded-[2rem] p-6 shadow-lg animate-fade-in-up delay-100">
                        <h4 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider mb-4">Current Status</h4>

                        <div class="flex flex-col items-center text-center">
                            @php
                                $badgeStyle = match($requisition->status_flow) {
                                    'ON_PROGRESS' => 'bg-orange-50 text-orange-600 border-orange-200',
                                    'PARTIALLY_APPROVED' => 'bg-purple-50 text-purple-600 border-purple-200',
                                    'APPROVED' => 'bg-green-50 text-green-600 border-green-200',
                                    'REJECTED' => 'bg-red-50 text-red-600 border-red-200',
                                    default => 'bg-slate-50 text-slate-600 border-slate-200'
                                };
                                $iconStyle = match($requisition->status_flow) {
                                    'ON_PROGRESS' => 'text-orange-500 bg-orange-100',
                                    'PARTIALLY_APPROVED' => 'text-purple-500 bg-purple-100',
                                    'APPROVED' => 'text-green-500 bg-green-100',
                                    'REJECTED' => 'text-red-500 bg-red-100',
                                    default => 'text-slate-500 bg-slate-100'
                                };
                            @endphp

                            <div class="w-20 h-20 rounded-full flex items-center justify-center mb-4 {{ $iconStyle }} shadow-inner">
                                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    @if($requisition->status_flow == 'APPROVED')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    @elseif($requisition->status_flow == 'REJECTED')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    @endif
                                </svg>
                            </div>

                            <span class="px-4 py-1.5 rounded-full text-xs font-bold border {{ $badgeStyle }} mb-2">
                                {{ str_replace('_', ' ', $requisition->status_flow) }}
                            </span>
                            <p class="text-xs text-slate-400">Last updated: {{ $requisition->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    {{-- Logic: Anda bisa menyesuaikan 'if' ini sesuai logic controller Anda --}}
                    @if(in_array(Auth::user()->position->position_name, ['Manager', 'Director', 'General Manager']) && in_array($requisition->status_flow, ['ON_PROGRESS', 'PARTIALLY_APPROVED']))
                    <div class="bg-gradient-to-br from-indigo-600 to-blue-700 rounded-[2rem] p-6 shadow-xl shadow-blue-500/30 text-white animate-fade-in-up delay-200">
                        <h4 class="text-sm font-extrabold uppercase tracking-wider mb-4 border-b border-white/20 pb-2">Your Action</h4>
                        <p class="text-xs text-blue-100 mb-6 leading-relaxed">
                            Please review the items carefully. Your approval will move this document to the next stage.
                        </p>

                        <div class="grid grid-cols-2 gap-3">
                            <form action="{{ route('requisitions.approve', $requisition->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full py-3 bg-emerald-500 hover:bg-emerald-400 text-white font-bold rounded-xl shadow-lg transition-all transform hover:-translate-y-1 flex flex-col items-center justify-center">
                                    <svg class="w-6 h-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    <span class="text-xs">APPROVE</span>
                                </button>
                            </form>

                            <button onclick="document.getElementById('rejectModal').classList.remove('hidden')" class="w-full py-3 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold rounded-xl shadow-lg transition-all transform hover:-translate-y-1 flex flex-col items-center justify-center">
                                <svg class="w-6 h-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                <span class="text-xs">REJECT</span>
                            </button>
                        </div>
                    </div>
                    @endif

                    <div class="bg-white/80 backdrop-blur-xl border border-slate-100 rounded-[2rem] p-6 shadow-lg animate-fade-in-up delay-300">
                        <h4 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider mb-6">Approval History</h4>

                        <div class="relative pl-4 border-l-2 border-slate-100 space-y-8">
                            <div class="relative">
                                <div class="absolute -left-[21px] bg-blue-500 h-3 w-3 rounded-full border-4 border-white shadow-sm"></div>
                                <p class="text-xs font-bold text-slate-400 uppercase">Created By</p>
                                <p class="text-sm font-bold text-slate-800">{{ $requisition->requester->full_name }}</p>
                                <p class="text-[10px] text-slate-400">{{ $requisition->created_at->format('d M Y, H:i') }}</p>
                            </div>

                            @if(isset($requisition->approvalQueues)) @foreach($requisition->approvalQueues as $queue)
                                <div class="relative">
                                    @php
                                        $dotColor = $queue->status == 'APPROVED' ? 'bg-green-500' : ($queue->status == 'REJECTED' ? 'bg-red-500' : 'bg-slate-300');
                                    @endphp
                                    <div class="absolute -left-[21px] {{ $dotColor }} h-3 w-3 rounded-full border-4 border-white shadow-sm"></div>
                                    <p class="text-xs font-bold text-slate-400 uppercase">Approver (Lvl {{ $queue->level_order }})</p>

                                    <p class="text-sm font-bold text-slate-800">
                                        {{-- Ganti ini sesuai nama kolom relasi user di model ApprovalQueue --}}
                                        {{ \App\Models\User::find($queue->approver_id)->full_name ?? 'Approver' }}
                                    </p>

                                    @if($queue->status == 'APPROVED')
                                        <p class="text-[10px] text-green-600 font-bold bg-green-50 inline-block px-1.5 rounded mt-1">Approved</p>
                                        <p class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($queue->updated_at)->format('d M Y, H:i') }}</p>
                                    @elseif($queue->status == 'REJECTED')
                                        <p class="text-[10px] text-red-600 font-bold bg-red-50 inline-block px-1.5 rounded mt-1">Rejected</p>
                                        <p class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($queue->updated_at)->format('d M Y, H:i') }}</p>
                                    @else
                                        <p class="text-[10px] text-orange-500 font-bold bg-orange-50 inline-block px-1.5 rounded mt-1">Pending</p>
                                    @endif
                                </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('rejectModal').classList.add('hidden')"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form action="{{ route('requisitions.reject', $requisition->id) }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-bold text-slate-900" id="modal-title">Reject Requisition</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-slate-500 mb-4">Are you sure you want to reject this request? Please provide a reason.</p>
                                    <textarea name="note" rows="3" class="w-full border-slate-300 rounded-xl shadow-sm focus:border-red-500 focus:ring-red-500 text-sm" placeholder="Reason for rejection (Required)..." required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Confirm Reject
                        </button>
                        <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
