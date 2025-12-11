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
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Request</dt>
                        <dd class="mt-1 text-sm font-bold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($rl->request_date)->format('d F Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status Terkini</dt>
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

            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 h-fit">
                <h3 class="mb-6 text-lg font-semibold text-gray-900 dark:text-white border-b pb-4">Posisi Dokumen</h3>
                
                <ol class="relative border-l-2 border-gray-200 dark:border-gray-700 ms-3">                  
                    <li class="mb-10 ms-6">
                        <span class="absolute flex items-center justify-center w-8 h-8 bg-blue-100 rounded-full -start-4 ring-4 ring-white dark:ring-gray-900 dark:bg-blue-900">
                            <svg class="w-4 h-4 text-blue-800 dark:text-blue-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/></svg>
                        </span>
                        <h3 class="flex items-center mb-1 text-base font-semibold text-gray-900 dark:text-white">Dibuat oleh Requester</h3>
                        <div class="p-3 text-xs italic font-normal text-gray-500 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                            {{ $rl->created_at->format('d M Y, H:i') }}
                        </div>
                    </li>

                    @foreach($rl->approvalQueues as $queue)
                    <li class="mb-10 ms-6">
                        <span class="absolute flex items-center justify-center w-8 h-8 {{ $queue->status == 'APPROVED' ? 'bg-green-100 dark:bg-green-900' : ($queue->status == 'REJECTED' ? 'bg-red-100 dark:bg-red-900' : 'bg-gray-100 dark:bg-gray-700') }} rounded-full -start-4 ring-4 ring-white dark:ring-gray-900">
                            @if($queue->status == 'APPROVED')
                                <svg class="w-4 h-4 text-green-800 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @elseif($queue->status == 'REJECTED')
                                <svg class="w-4 h-4 text-red-800 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            @else
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @endif
                        </span>
                        
                        <h3 class="mb-1 text-base font-semibold text-gray-900 dark:text-white">
                            Approval {{ $queue->level_order == 1 ? 'Manager' : 'Director' }}
                        </h3>
                        
                        <div class="p-3 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600">
                            <div class="flex items-center justify-between mb-2">
                                <div class="text-sm font-normal text-gray-500 dark:text-gray-400">Penyetuju:</div>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $queue->approver->full_name ?? 'Menunggu...' }}</div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="text-sm font-normal text-gray-500 dark:text-gray-400">Status:</div>
                                <span class="text-xs font-medium px-2.5 py-0.5 rounded border {{ $queue->status == 'APPROVED' ? 'bg-green-100 text-green-800 border-green-400' : ($queue->status == 'REJECTED' ? 'bg-red-100 text-red-800 border-red-400' : 'bg-yellow-100 text-yellow-800 border-yellow-400') }}">
                                    {{ $queue->status }}
                                </span>
                            </div>
                            @if($queue->approved_at)
                            <div class="mt-2 text-xs text-gray-400 text-right">
                                {{ \Carbon\Carbon::parse($queue->approved_at)->format('d M Y, H:i') }}
                            </div>
                            @endif
                        </div>
                    </li>
                    @endforeach
                </ol>
            </div>
        </div>

        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 mb-6">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Tracking Barang</h3>
            <div class="relative overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Nama Barang</th>
                            <th scope="col" class="px-6 py-3 text-center">Request</th>
                            <th scope="col" class="px-6 py-3 text-center">Diterima</th>
                            <th scope="col" class="px-6 py-3 text-center">Sisa</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rl->items as $item)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                {{ $item->item_name }}
                                <div class="text-xs text-gray-500">{{ $item->description }}</div>
                            </td>
                            <td class="px-6 py-4 text-center font-bold">{{ $item->qty }} {{ $item->uom }}</td>
                            
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-green-600 font-bold text-lg">{{ $item->supplied_qty }}</span>
                                    
                                    @if($item->supplied_qty > 0)
                                        <button type="button" onclick="toggleModal('history-modal-{{ $item->id }}')" class="text-xs text-blue-600 hover:text-blue-800 hover:underline mt-1 flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Riwayat
                                        </button>

                                        <div id="history-modal-{{ $item->id }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-[60] justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-gray-900/50 backdrop-blur-sm">
                                            <div class="relative p-4 w-full max-w-2xl max-h-full">
                                                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                                                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                                            Riwayat: {{ $item->item_name }}
                                                        </h3>
                                                        <button type="button" onclick="toggleModal('history-modal-{{ $item->id }}')" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                                                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                                                        </button>
                                                    </div>
                                                    <div class="p-4 md:p-5 space-y-4">
                                                        <div class="relative overflow-x-auto">
                                                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                                                    <tr>
                                                                        <th class="px-4 py-2">Tanggal</th>
                                                                        <th class="px-4 py-2">Penerima</th>
                                                                        <th class="px-4 py-2 text-center">Qty</th>
                                                                        <th class="px-4 py-2 text-center">Bukti</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($item->supplyHistories as $history)
                                                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                                                        <td class="px-4 py-2">{{ $history->created_at->format('d M Y H:i') }}</td>
                                                                        <td class="px-4 py-2">{{ $history->receiver->full_name ?? '-' }}</td>
                                                                        <td class="px-4 py-2 text-center font-bold">{{ $history->qty_received }}</td>
                                                                        <td class="px-4 py-2 text-center">
                                                                            @if($history->photo_proof)
                                                                                <a href="{{ asset('storage/' . $history->photo_proof) }}" target="_blank" class="text-blue-600 hover:underline text-xs">Foto</a>
                                                                            @else
                                                                                -
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 text-center text-red-600 font-bold">
                                {{ $item->remaining_qty }}
                            </td>

                            <td class="px-6 py-4">
                                @if($item->status_item == 'SUPPLIED')
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Lengkap</span>
                                @else
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">Menunggu</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if($rl->status_flow == 'APPROVED' && $item->remaining_qty > 0)
                                    <button onclick="openSupplyModal({{ $item->id }}, '{{ $item->item_name }}', {{ $item->remaining_qty }})" 
                                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-xs px-3 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none">
                                        Terima
                                    </button>
                                @elseif($item->status_item == 'SUPPLIED')
                                    <svg class="w-6 h-6 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                @else
                                    <span class="text-xs text-gray-400">Locked</span>
                                @endif
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

        @if($rl->status_flow == 'DRAFT' && $currentUser->employee_id == $rl->requester_id)
        <div class="fixed bottom-0 left-0 z-50 w-full h-20 bg-white border-t border-gray-200 dark:bg-gray-800 dark:border-gray-600 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] flex items-center justify-center gap-4 animate-bounce-up">
            <span class="text-sm font-medium text-gray-600 dark:text-gray-300 mr-2 hidden sm:inline">
                Dokumen ini masih <strong>DRAFT</strong>. Silakan ajukan jika sudah siap:
            </span>
            
            <form action="{{ route('requisitions.submit-draft', $rl->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin data sudah benar dan ingin mengajukan approval?');">
                @csrf
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-8 py-3 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none flex items-center shadow-md transition-transform transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Ajukan Sekarang (Submit)
                </button>
            </form>
        </div>
        @endif

        @if($myPendingApproval)
        <div class="fixed bottom-0 left-0 z-50 w-full h-20 bg-white border-t border-gray-200 dark:bg-gray-800 dark:border-gray-600 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] flex items-center justify-center gap-4 animate-bounce-up">
            <span class="text-sm font-medium text-gray-600 dark:text-gray-300 mr-2 hidden sm:inline">
                Anda memiliki akses untuk memproses dokumen ini:
            </span>
            
            <form action="{{ route('approval.action') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin menyetujui dokumen ini?');">
                @csrf
                <input type="hidden" name="queue_id" value="{{ $myPendingApproval->id }}">
                <input type="hidden" name="action" value="APPROVE">
                
                <button type="submit" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-6 py-3 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none flex items-center shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Setujui (Approve)
                </button>
            </form>

            <form action="{{ route('approval.action') }}" method="POST" onsubmit="return confirm('Yakin ingin MENOLAK dokumen ini?');">
                @csrf
                <input type="hidden" name="queue_id" value="{{ $myPendingApproval->id }}">
                <input type="hidden" name="action" value="REJECT">
                
                <button type="submit" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-6 py-3 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none flex items-center shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Tolak (Reject)
                </button>
            </form>
        </div>
        @endif

    </div>

    <div id="supply-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-gray-900/50 backdrop-blur-sm">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Input Penerimaan Barang
                    </h3>
                    <button type="button" onclick="closeSupplyModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                    </button>
                </div>
                
                <form action="{{ route('supply.store') }}" method="POST" enctype="multipart/form-data" class="p-4 md:p-5">
                    @csrf
                    <input type="hidden" name="item_id" id="modal_item_id">
                    
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Barang</label>
                        <input type="text" id="modal_item_name" class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed" disabled>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jumlah Diterima (Sisa: <span id="modal_max_qty">0</span>)</label>
                        <input type="number" name="qty_received" id="modal_qty_input" min="1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Bukti Foto (Opsional)</label>
                        <input type="file" name="photo_proof" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                    </div>

                    <div class="mt-8 bg-white border border-gray-200 rounded-lg shadow-sm p-6 dark:bg-gray-800 dark:border-gray-700">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Document Preview</h3>
                        <a href="{{ route('requisitions.print', $rl->id) }}" target="_blank" class="text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-4 py-2 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Download PDF
                        </a>
                    </div>
                    
                    <div class="aspect-w-16 aspect-h-9 w-full h-[600px] border rounded-lg bg-gray-100">
                        <iframe src="{{ route('requisitions.print', $rl->id) }}" class="w-full h-full rounded-lg" frameborder="0"></iframe>
                    </div>
                </div>
                    <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Simpan Penerimaan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Fungsi untuk Modal Supply
        function openSupplyModal(id, name, maxQty) {
            document.getElementById('modal_item_id').value = id;
            document.getElementById('modal_item_name').value = name;
            document.getElementById('modal_max_qty').innerText = maxQty;
            document.getElementById('modal_qty_input').max = maxQty;
            document.getElementById('modal_qty_input').value = maxQty; 

            const modal = document.getElementById('supply-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeSupplyModal() {
            const modal = document.getElementById('supply-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Fungsi Universal Buka/Tutup Modal berdasarkan ID (Untuk History Log)
        function toggleModal(modalID) {
            const modal = document.getElementById(modalID);
            if (modal) {
                modal.classList.toggle('hidden');
                modal.classList.toggle('flex');
            }
        }
    </script>
</x-app-layout>