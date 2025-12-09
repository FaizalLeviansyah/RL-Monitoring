<x-app-layout>
    <div class="max-w-7xl mx-auto p-4">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Aktivitas Departemen
            </h1>
            <p class="text-gray-500 text-sm">
                @if(Auth::user()->position->position_name == 'Director')
                    Menampilkan seluruh aktivitas di <strong>{{ Auth::user()->company->company_name }}</strong>.
                @else
                    Menampilkan aktivitas di <strong>{{ Auth::user()->department->department_name }}</strong> - {{ Auth::user()->company->company_code }}.
                @endif
            </p>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">Waktu</th>
                            <th class="px-6 py-3">RL No</th>
                            <th class="px-6 py-3">Requester</th>
                            <th class="px-6 py-3">Subject</th>
                            <th class="px-6 py-3 text-center">Status</th>
                            <th class="px-6 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $rl)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50">
                            <td class="px-6 py-4">
                                {{ $rl->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                {{ $rl->rl_no }}
                            </td>
                            <td class="px-6 py-4">
                                <div>{{ $rl->requester->full_name }}</div>
                                <div class="text-xs text-gray-400">{{ $rl->requester->department->department_name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                {{ str($rl->subject)->limit(40) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $rl->status_flow == 'APPROVED' ? 'bg-green-100 text-green-800' :
                                      ($rl->status_flow == 'REJECTED' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ $rl->status_flow }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('requisitions.show', $rl->id) }}" class="text-blue-600 hover:underline">Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="p-4 text-center">Belum ada aktivitas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">
                {{ $activities->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
