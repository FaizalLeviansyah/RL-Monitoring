<x-app-layout>
    <div class="max-w-7xl mx-auto p-4">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Detail Permintaan: <span class="text-blue-600">{{ $rl->rl_no }}</span>
            </h1>
            <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-blue-600 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Dashboard
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <div class="lg:col-span-2 p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white border-b pb-2">Informasi Umum</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Pemohon (Requester)</dt>
                        <dd class="mt-1 text-sm font-bold text-gray-900 dark:text-white">{{ $rl->requester->full_name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Departemen</dt>
                        <dd class="mt-1 text-sm font-bold text-gray-900 dark:text-white">{{ $rl->requester->department->department_name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Request</dt>
                        <dd class="mt-1 text-sm font-bold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($rl->request_date)->format('d F Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status Saat Ini</dt>
                        <dd class="mt-1">
                            @if($rl->status_flow == 'ON_PROGRESS')
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">Menunggu Approval</span>
                            @elseif($rl->status_flow == 'APPROVED')
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Disetujui</span>
                            @elseif($rl->status_flow == 'REJECTED')
                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Ditolak</span>
                            @else
                                <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">Draft</span>
                            @endif
                        </dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Perihal / Subject</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $rl->subject }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Catatan / Remark</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white bg-gray-50 p-3 rounded-md dark:bg-gray-700">{{ $rl->remark ?? 'Tidak ada catatan' }}</dd>
                    </div>
                </dl>
            </div>

<div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
    <h3 class="mb-6 text-lg font-semibold text-gray-900 dark:text-white border-b pb-2">Posisi Dokumen</h3>

    <ol class="relative border-l border-gray-200 dark:border-gray-700 ml-6">

        <li class="mb-10 pl-10 relative">

            <span class="absolute flex items-center justify-center w-8 h-8 bg-blue-100 rounded-full -left-4 ring-4 ring-white dark:ring-gray-900 dark:bg-blue-900">
                <svg class="w-4 h-4 text-blue-800 dark:text-blue-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                </svg>
            </span>

            <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 dark:bg-gray-700 dark:border-gray-600">
                <div class="flex flex-wrap items-center justify-between mb-2">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                        Dibuat oleh Requester
                    </h3>
                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                        Created
                    </span>
                </div>

                <time class="block mb-2 text-xs font-normal text-gray-400 dark:text-gray-500">
                    {{ $rl->created_at->format('d M Y, H:i') }}
                </time>
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    Permintaan diajukan oleh <strong>{{ $rl->requester->full_name ?? '-' }}</strong>.
                </p>
            </div>
        </li>

        @foreach($rl->approvalQueues as $queue)
        <li class="mb-10 pl-10 relative">

            <span class="absolute flex items-center justify-center w-8 h-8 {{ $queue->status == 'APPROVED' ? 'bg-green-100 dark:bg-green-900' : ($queue->status == 'REJECTED' ? 'bg-red-100 dark:bg-red-900' : 'bg-gray-100 dark:bg-gray-700') }} rounded-full -left-4 ring-4 ring-white dark:ring-gray-900">
                @if($queue->status == 'APPROVED')
                    <svg class="w-4 h-4 text-green-800 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                @elseif($queue->status == 'REJECTED')
                    <svg class="w-4 h-4 text-red-800 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                @else
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @endif
            </span>

            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600">
                <div class="flex flex-wrap items-center justify-between mb-2">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                        Approval {{ $queue->level_order == 1 ? 'Manager' : 'Director' }}
                    </h3>

                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium border {{ $queue->status == 'APPROVED' ? 'bg-green-100 text-green-800 border-green-400' : ($queue->status == 'REJECTED' ? 'bg-red-100 text-red-800 border-red-400' : 'bg-yellow-100 text-yellow-800 border-yellow-400') }}">
                        @if($queue->status == 'PENDING')
                            <svg class="w-3 h-3 mr-1 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        @endif
                        {{ $queue->status }}
                    </span>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Approver:</p>
                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                    {{ $queue->approver->full_name ?? 'Menunggu System Assign...' }}
                </p>
            </div>
        </li>
        @endforeach
    </ol>
</div>
        </div>

        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 mb-6">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Daftar Barang</h3>
            <div class="relative overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">No</th>
                            <th scope="col" class="px-6 py-3">Nama Barang</th>
                            <th scope="col" class="px-6 py-3">Deskripsi/Spek</th>
                            <th scope="col" class="px-6 py-3 text-center">Qty</th>
                            <th scope="col" class="px-6 py-3">Satuan</th>
                            <th scope="col" class="px-6 py-3">Status Item</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rl->items as $index => $item)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-6 py-4 font-medium">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ $item->item_name }}</td>
                            <td class="px-6 py-4">{{ $item->description ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">{{ $item->qty }}</td>
                            <td class="px-6 py-4">{{ $item->uom }}</td>
                            <td class="px-6 py-4">
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                    {{ $item->status_item }}
                                </span>
                            </td>
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
        <div class="p-6 bg-yellow-50 border border-yellow-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-yellow-900">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Tindakan Diperlukan</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Halo <strong>{{ $currentUser->full_name }}</strong>, dokumen ini menunggu persetujuan Anda sebagai
                <strong>{{ $myPendingApproval->level_order == 1 ? 'Manager' : 'Director' }}</strong>.
            </p>

            <div class="flex gap-4">
                <form action="#" method="POST">
                    @csrf
                    <input type="hidden" name="queue_id" value="{{ $myPendingApproval->id }}">
                    <input type="hidden" name="action" value="APPROVE">
                    <button type="submit" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Setujui (Approve)
                    </button>
                </form>

                <form action="#" method="POST">
                    @csrf
                    <input type="hidden" name="queue_id" value="{{ $myPendingApproval->id }}">
                    <input type="hidden" name="action" value="REJECT">
                    <button type="submit" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Tolak (Reject)
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
