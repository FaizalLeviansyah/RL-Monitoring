<x-app-layout>
    <div class="max-w-7xl mx-auto p-4">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Detail Permintaan: <span class="text-blue-600">{{ $rl->rl_no }}</span>
            </h1>
            <div class="flex gap-2">
                <a href="{{ route('requisitions.print', $rl->id) }}" target="_blank" class="text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-gray-600 dark:hover:bg-gray-700 focus:outline-none flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Print PDF
                </a>
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-blue-600 flex items-center px-3">
                    Kembali
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <div class="lg:col-span-2 p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white border-b pb-2">Informasi Umum</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Pemohon</dt>
                        <dd class="mt-1 text-sm font-bold text-gray-900 dark:text-white">{{ $rl->requester->full_name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Departemen</dt>
                        <dd class="mt-1 text-sm font-bold text-gray-900 dark:text-white">{{ $rl->requester->department->department_name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal</dt>
                        <dd class="mt-1 text-sm font-bold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($rl->request_date)->format('d F Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                        <dd class="mt-1 font-bold text-blue-600">{{ $rl->status_flow }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Subject</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $rl->subject }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Remark</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white bg-gray-50 p-3 rounded">{{ $rl->remark ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <h3 class="mb-6 text-lg font-semibold text-gray-900 dark:text-white border-b pb-2">Timeline</h3>
                <ol class="relative border-l border-gray-200 dark:border-gray-700 ml-4">
                    <li class="mb-10 ml-6">
                        <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -left-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                            <svg class="w-2.5 h-2.5 text-blue-800" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/></svg>
                        </span>
                        <h3 class="mb-1 text-sm font-semibold text-gray-900 dark:text-white">Created</h3>
                        <time class="block mb-2 text-xs font-normal leading-none text-gray-400">{{ $rl->created_at->format('d M Y, H:i') }}</time>
                    </li>
                    @foreach($rl->approvalQueues as $queue)
                    <li class="mb-10 ml-6">
                        <span class="absolute flex items-center justify-center w-6 h-6 {{ $queue->status == 'APPROVED' ? 'bg-green-100' : 'bg-gray-100' }} rounded-full -left-3 ring-8 ring-white dark:ring-gray-900">
                            <svg class="w-2.5 h-2.5 text-gray-800" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/></svg>
                        </span>
                        <h3 class="mb-1 text-sm font-semibold text-gray-900 dark:text-white">Approval {{ $queue->level_order }}</h3>
                        <p class="text-sm font-normal text-gray-500">{{ $queue->approver->full_name ?? 'Waiting...' }}</p>
                        <span class="text-xs font-bold {{ $queue->status == 'APPROVED' ? 'text-green-600' : 'text-yellow-600' }}">{{ $queue->status }}</span>
                    </li>
                    @endforeach
                </ol>
            </div>
        </div>

        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 mb-6">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Items List</h3>
            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Name</th>
                            <th scope="col" class="px-6 py-3">Qty</th>
                            <th scope="col" class="px-6 py-3">UOM</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rl->items as $item)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-6 py-4">{{ $item->item_name }}</td>
                            <td class="px-6 py-4">{{ $item->qty }}</td>
                            <td class="px-6 py-4">{{ $item->uom }}</td>
                            <td class="px-6 py-4">{{ $item->status_item }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @php
            $currentUser = Auth::user();
            $myPendingApproval = $rl->approvalQueues->where('approver_id', $currentUser->employee_id)->where('status', 'PENDING')->first();
        @endphp

        @if($myPendingApproval)
        <div class="fixed bottom-0 left-0 z-50 w-full h-16 bg-white border-t border-gray-200 dark:bg-gray-700 dark:border-gray-600 shadow-lg flex items-center justify-center gap-4">
            <span class="text-sm text-gray-500 mr-4">Anda memiliki akses untuk memproses dokumen ini:</span>

            <form action="{{ route('approval.action') }}" method="POST" onsubmit="return confirm('Yakin Approve?');">
                @csrf
                <input type="hidden" name="queue_id" value="{{ $myPendingApproval->id }}">
                <input type="hidden" name="action" value="APPROVE">
                <button type="submit" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5">
                    Approve
                </button>
            </form>

            <form action="{{ route('approval.action') }}" method="POST" onsubmit="return confirm('Yakin Reject?');">
                @csrf
                <input type="hidden" name="queue_id" value="{{ $myPendingApproval->id }}">
                <input type="hidden" name="action" value="REJECT">
                <button type="submit" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5">
                    Reject
                </button>
            </form>
        </div>
        @endif
    </div>
</x-app-layout>
