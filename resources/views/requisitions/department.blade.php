<x-app-layout>
    <div class="pt-6 pb-12 min-h-screen bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- HEADER --}}
            <div class="flex justify-between items-center mb-8 animate-fade-in-down">
                <div>
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight">Department Activity</h2>
                    <p class="text-slate-500 text-sm mt-1">
                        Monitoring requests from <span class="font-bold text-blue-600">{{ Auth::user()->department->department_name }}</span> at {{ Auth::user()->company->company_code }}
                    </p>
                </div>
                <div class="hidden md:block">
                    <span class="px-4 py-2 bg-white border border-slate-200 rounded-full text-xs font-bold text-slate-500 shadow-sm">
                        Showing last 10 activities
                    </span>
                </div>
            </div>

            {{-- LIST CARD --}}
            <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden">
                @if($requisitions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-xs">
                                <tr>
                                    <th class="px-6 py-4">RL Number</th>
                                    <th class="px-6 py-4">Requester</th>
                                    <th class="px-6 py-4">Subject</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                    <th class="px-6 py-4 text-center">Date</th>
                                    <th class="px-6 py-4 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($requisitions as $rl)
                                <tr class="hover:bg-blue-50/30 transition-colors group">
                                    <td class="px-6 py-4 font-mono font-bold text-blue-600">
                                        {{ $rl->rl_no }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs mr-3">
                                                {{ substr($rl->requester->full_name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-700">{{ $rl->requester->full_name }}</p>
                                                <p class="text-xs text-slate-400">{{ $rl->requester->position->position_name ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-slate-600 truncate max-w-xs">
                                        {{ $rl->subject }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold
                                            @if($rl->status_flow == 'APPROVED') bg-green-100 text-green-700
                                            @elseif($rl->status_flow == 'REJECTED') bg-red-100 text-red-700
                                            @elseif($rl->status_flow == 'ON_PROGRESS') bg-blue-100 text-blue-700
                                            @else bg-gray-100 text-gray-500 @endif">
                                            {{ str_replace('_', ' ', $rl->status_flow) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-slate-500 text-xs">
                                        {{ $rl->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('requisitions.show', $rl->id) }}" class="text-blue-600 hover:text-blue-800 font-bold text-xs border border-blue-200 px-3 py-1.5 rounded-lg hover:bg-blue-50 transition">
                                            View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="p-6 border-t border-slate-100">
                        {{ $requisitions->links() }}
                    </div>
                @else
                    <div class="text-center py-20">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                            <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800">No Activities Found</h3>
                        <p class="text-slate-500 mt-1">Your department hasn't made any requests recently.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

{{-- <x-app-layout>
    <div class="max-w-7xl mx-auto p-4">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Daftar Dokumen:
                <span class="
                    {{ $statusUpper == 'APPROVED' ? 'text-green-600' : '' }}
                    {{ $statusUpper == 'REJECTED' ? 'text-red-600' : '' }}
                    {{ $statusUpper == 'ON_PROGRESS' ? 'text-orange-500' : '' }}
                    {{ $statusUpper == 'DRAFT' ? 'text-gray-500' : '' }}
                    {{ $statusUpper == 'ACTIVITIES' ? 'text-blue-600' : '' }}
                ">
                    @if($statusUpper == 'ACTIVITIES')
                        Department Activities
                    @else
                        {{ $statusUpper }}
                    @endif
                </span>
            </h1>
            <p class="text-gray-500 dark:text-gray-400">
                @if($statusUpper == 'ACTIVITIES')
                    Menampilkan seluruh aktivitas permintaan dari Departemen Anda.
                @else
                    Menampilkan seluruh data surat dengan status {{ strtolower($statusUpper) }}.
                @endif
            </p>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">No RL</th>
                        <th scope="col" class="px-6 py-3">Tanggal</th>
                        <th scope="col" class="px-6 py-3">Requester</th>
                        <th scope="col" class="px-6 py-3">Subject</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Items</th>
                        <th scope="col" class="px-6 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requisitions as $rl)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $rl->rl_no }}
                        </th>
                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::parse($rl->request_date)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900 dark:text-white">{{ $rl->requester->full_name ?? '-' }}</div>
                            <div class="text-xs">{{ $rl->requester->department->department_name ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            {{ str($rl->subject)->limit(30) }}
                        </td>
                        <td class="px-6 py-4">
                            @if($rl->status_flow == 'APPROVED')
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Approved</span>
                            @elseif($rl->status_flow == 'REJECTED')
                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Rejected</span>
                            @elseif($rl->status_flow == 'ON_PROGRESS')
                                <span class="bg-orange-100 text-orange-800 text-xs font-medium px-2.5 py-0.5 rounded">On Progress</span>
                            @else
                                <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">{{ $rl->status_flow }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            {{ $rl->items->count() }} Barang
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('requisitions.show', $rl->id) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                            Tidak ada aktivitas departemen ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4">
                {{ $requisitions->links() }}
            </div>
        </div>
    </div>
</x-app-layout> --}}
