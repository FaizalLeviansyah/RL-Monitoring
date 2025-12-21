<x-app-layout>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">

        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-blue-600 dark:from-indigo-400 dark:to-blue-400">
                Department Activity
            </h1>
            <div class="mt-2 flex items-center p-3 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl border border-indigo-100 dark:border-indigo-800 text-sm text-indigo-800 dark:text-indigo-300 w-fit">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                @if(Auth::user()->position->position_name == 'Director')
                    Showing all activities in <strong>{{ Auth::user()->company->company_name }}</strong>.
                @else
                    Showing activities in <strong>{{ Auth::user()->department->department_name }}</strong> - {{ Auth::user()->company->company_code }}.
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-lg shadow-indigo-500/10 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-indigo-900 uppercase bg-indigo-50 dark:bg-gray-700 dark:text-gray-300 border-b border-indigo-100 dark:border-gray-600">
                        <tr>
                            <th class="px-6 py-4 font-bold tracking-wider">Timestamp</th>
                            <th class="px-6 py-4 font-bold tracking-wider">RL No</th>
                            <th class="px-6 py-4 font-bold tracking-wider">Requester</th>
                            <th class="px-6 py-4 font-bold tracking-wider">Subject</th>
                            <th class="px-6 py-4 font-bold tracking-wider text-center">Status</th>
                            <th class="px-6 py-4 font-bold tracking-wider text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($activities as $rl)
                        <tr class="hover:bg-indigo-50/30 dark:hover:bg-gray-700/50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                {{ $rl->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                {{ $rl->rl_no }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $rl->requester->full_name }}</div>
                                <div class="text-xs text-gray-400">{{ $rl->requester->department->department_name }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                {{ Str::limit($rl->subject, 35) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $badge = match($rl->status_flow) {
                                        'APPROVED' => 'bg-green-100 text-green-800 border-green-200',
                                        'REJECTED' => 'bg-red-100 text-red-800 border-red-200',
                                        default => 'bg-yellow-100 text-yellow-800 border-yellow-200'
                                    };
                                @endphp
                                <span class="px-2.5 py-0.5 text-xs font-bold rounded-full border {{ $badge }}">
                                    {{ $rl->status_flow }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('requisitions.show', $rl->id) }}" class="text-indigo-600 hover:text-indigo-800 font-bold hover:underline text-xs">
                                    View Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="p-8 text-center text-gray-400">No activity recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $activities->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
