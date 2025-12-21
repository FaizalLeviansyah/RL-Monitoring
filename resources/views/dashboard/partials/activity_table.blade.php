<div class="overflow-x-auto">
    <table class="w-full text-sm text-left">
        <thead class="text-xs text-indigo-900 bg-indigo-50 dark:bg-gray-700 dark:text-gray-300 uppercase tracking-wider">
            <tr>
                <th class="px-6 py-4 font-bold">RL Number</th>
                <th class="px-6 py-4 font-bold">Subject</th>
                <th class="px-6 py-4 font-bold">Status</th>
                <th class="px-6 py-4 font-bold text-right">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($recentActivities as $rl)
            <tr class="hover:bg-indigo-50/30 transition-colors group">
                <td class="px-6 py-4">
                    <div class="font-bold text-gray-800 dark:text-white group-hover:text-indigo-600 transition">{{ $rl->rl_no }}</div>
                    <div class="text-[10px] font-bold text-gray-400 mt-0.5 uppercase">{{ $rl->created_at->diffForHumans() }}</div>
                </td>
                <td class="px-6 py-4 text-gray-600 dark:text-gray-300 font-medium">{{ Str::limit($rl->subject, 35) }}</td>
                <td class="px-6 py-4">
                    @php
                        $badges = [
                            'DRAFT' => 'bg-gray-100 text-gray-600',
                            'ON_PROGRESS' => 'bg-gradient-to-r from-orange-100 to-orange-200 text-orange-700',
                            'PARTIALLY_APPROVED' => 'bg-gradient-to-r from-purple-100 to-purple-200 text-purple-700',
                            'WAITING_SUPPLY' => 'bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-700',
                            'COMPLETED' => 'bg-gradient-to-r from-teal-100 to-teal-200 text-teal-700',
                            'REJECTED' => 'bg-gradient-to-r from-red-100 to-red-200 text-red-700',
                        ];
                    @endphp
                    <span class="{{ $badges[$rl->status_flow] ?? 'bg-gray-100' }} px-3 py-1 rounded-full text-xs font-bold inline-block shadow-sm">
                        {{ str_replace('_', ' ', $rl->status_flow) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-right">
                    <a href="{{ route('requisitions.show', $rl->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center py-8 text-gray-400 italic font-medium">Belum ada aktivitas.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
