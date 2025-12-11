<x-app-layout>
    <div class="max-w-7xl mx-auto p-4">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <nav class="flex mb-1" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li><a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-900">Dashboard</a></li>
                        <li><span class="text-gray-400">/</span></li>
                        <li class="text-gray-700 font-bold">Detail RL</li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                    {{ $rl->rl_no }}
                    @if($rl->status_flow == 'DRAFT')
                        <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded border border-gray-500">Draft</span>
                    @elseif($rl->status_flow == 'ON_PROGRESS')
                        <span class="bg-orange-100 text-orange-800 text-xs font-medium px-2.5 py-0.5 rounded border border-orange-400">Waiting Approval</span>
                    @elseif($rl->status_flow == 'APPROVED')
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded border border-green-400">Approved</span>
                    @elseif($rl->status_flow == 'REJECTED')
                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded border border-red-400">Rejected</span>
                    @endif
                </h1>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('requisitions.print', $rl->id) }}" target="_blank" class="flex items-center px-4 py-2 bg-gray-800 text-white font-semibold rounded-lg hover:bg-gray-700 shadow transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Print / Download PDF
                </a>

                @if($rl->status_flow == 'DRAFT' && Auth::user()->employee_id == $rl->requester_id)
                <form action="{{ route('requisitions.submit-draft', $rl->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin data sudah benar?');">
                    @csrf
                    <button type="submit" class="flex items-center px-4 py-2 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 shadow transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Submit Approval
                    </button>
                </form>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">

                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b pb-2">General Information</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">

                        <div>
                            <p class="text-gray-500">Request Date</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($rl->request_date)->format('d M Y') }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500">Required Date</p>
                            <p class="font-semibold text-red-600 dark:text-red-400">
                                {{ $rl->required_date ? \Carbon\Carbon::parse($rl->required_date)->format('d M Y') : '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-500">Priority</p>
                            @if($rl->priority == 'Top Urgent')
                                <span class="text-red-600 font-bold uppercase animate-pulse">TOP URGENT</span>
                            @elseif($rl->priority == 'Urgent')
                                <span class="text-orange-600 font-bold uppercase">Urgent</span>
                            @else
                                <span class="text-gray-900 font-semibold">Normal</span>
                            @endif
                        </div>

                        <div>
                            <p class="text-gray-500">Requester</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $rl->requester->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $rl->requester->department->department_name }}</p>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-gray-500 text-xs uppercase font-bold">Subject / Perihal</p>
                        <p class="text-gray-900 dark:text-white font-medium text-base mt-1">"{{ $rl->subject }}"</p>
                    </div>

                    @if($rl->remark)
                    <div class="mt-4 p-3 bg-yellow-50 rounded-lg border border-yellow-200 text-yellow-800 text-sm">
                        <strong>Note:</strong> {{ $rl->remark }}
                    </div>
                    @endif
                </div>

                <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden dark:bg-gray-800 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 dark:bg-gray-700 dark:border-gray-600 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-gray-700 dark:text-white uppercase tracking-wider">Document Preview</h3>
                        <span class="text-xs text-gray-500">Generated System PDF</span>
                    </div>
                    <div class="w-full h-[600px] bg-gray-200">
                        <iframe src="{{ route('requisitions.print', $rl->id) }}#toolbar=0" class="w-full h-full" frameborder="0"></iframe>
                    </div>
                </div>

            </div>

            <div class="space-y-6">

                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Approval Status</h3>

                    <ol class="relative border-l border-gray-200 dark:border-gray-700 ml-2">
                        <li class="mb-6 ml-6">
                            <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -left-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                                <svg class="w-3 h-3 text-blue-800 dark:text-blue-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/></svg>
                            </span>
                            <h3 class="flex items-center mb-1 text-sm font-semibold text-gray-900 dark:text-white">Created</h3>
                            <time class="block mb-2 text-xs font-normal leading-none text-gray-400 dark:text-gray-500">
                                {{ \Carbon\Carbon::parse($rl->created_at)->format('d M Y, H:i') }}
                            </time>
                            <p class="text-sm font-normal text-gray-500 dark:text-gray-400">by {{ $rl->requester->full_name }}</p>
                        </li>

                        @foreach($rl->approvalQueues as $approval)
                        <li class="mb-6 ml-6">
                            @if($approval->status == 'APPROVED')
                                <span class="absolute flex items-center justify-center w-6 h-6 bg-green-100 rounded-full -left-3 ring-8 ring-white dark:ring-gray-900 dark:bg-green-900">
                                    <svg class="w-3 h-3 text-green-800 dark:text-green-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5"/></svg>
                                </span>
                            @elseif($approval->status == 'REJECTED')
                                <span class="absolute flex items-center justify-center w-6 h-6 bg-red-100 rounded-full -left-3 ring-8 ring-white dark:ring-gray-900 dark:bg-red-900">
                                    <svg class="w-3 h-3 text-red-800 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </span>
                            @else
                                <span class="absolute flex items-center justify-center w-6 h-6 bg-gray-100 rounded-full -left-3 ring-8 ring-white dark:ring-gray-900 dark:bg-gray-700">
                                    <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                </span>
                            @endif

                            <h3 class="mb-1 text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $approval->approver->position->position_name ?? 'Approver' }} (Level {{ $approval->level_order }})
                            </h3>

                            @if($approval->status == 'APPROVED')
                                <time class="block mb-2 text-xs font-normal text-green-600">Approved on {{ \Carbon\Carbon::parse($approval->approved_at)->format('d M Y, H:i') }}</time>
                            @elseif($approval->status == 'REJECTED')
                                <time class="block mb-2 text-xs font-normal text-red-600">Rejected on {{ \Carbon\Carbon::parse($approval->updated_at)->format('d M Y') }}</time>
                                <p class="text-xs text-red-500 italic">"{{ $approval->reason_rejection }}"</p>
                            @else
                                <span class="bg-orange-100 text-orange-800 text-[10px] font-medium px-2 py-0.5 rounded">Pending Review</span>
                            @endif

                            <p class="text-sm text-gray-500">{{ $approval->approver->full_name }}</p>
                        </li>
                        @endforeach
                    </ol>
                </div>

                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Item Summary</h3>
                    <ul class="max-w-md space-y-2 text-sm text-gray-500 list-disc list-inside dark:text-gray-400">
                        @foreach($rl->items as $item)
                        <li>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $item->item_name }}</span>
                            ({{ $item->qty }} {{ $item->uom }})
                        </li>
                        @endforeach
                    </ul>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
