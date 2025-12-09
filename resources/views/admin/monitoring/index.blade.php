<x-app-layout>
    <div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <div class="mb-4">
                <nav class="flex mb-5" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
                        <li class="inline-flex items-center">
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">
                                <svg class="w-5 h-5 mr-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                                Command Center
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500">Global Monitoring</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Live Requisition Monitor</h1>
            </div>

            <div class="sm:flex">
                <div class="items-center hidden mb-3 sm:flex sm:divide-x sm:divide-gray-100 sm:mb-0 dark:divide-gray-700">
                    <form class="lg:pr-3" action="{{ route('admin.monitoring.index') }}" method="GET">
                        <div class="relative mt-1 lg:w-64 xl:w-96">
                            <input type="text" name="search" value="{{ request('search') }}" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Search RL No or Subject">
                        </div>
                    </form>
                    <div class="flex pl-0 mt-3 space-x-3 sm:pl-2 sm:mt-0">
                        <form id="filterForm" action="{{ route('admin.monitoring.index') }}" method="GET" class="flex gap-2">
                            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif

                            <select name="company_id" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                <option value="">All Companies</option>
                                @foreach($companies as $comp)
                                    <option value="{{ $comp->company_id }}" {{ request('company_id') == $comp->company_id ? 'selected' : '' }}>
                                        {{ $comp->company_code }}
                                    </option>
                                @endforeach
                            </select>

                            <select name="status" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                <option value="">All Status</option>
                                <option value="DRAFT" {{ request('status') == 'DRAFT' ? 'selected' : '' }}>Draft</option>
                                <option value="ON_PROGRESS" {{ request('status') == 'ON_PROGRESS' ? 'selected' : '' }}>Pending</option>
                                <option value="APPROVED" {{ request('status') == 'APPROVED' ? 'selected' : '' }}>Approved</option>
                                <option value="REJECTED" {{ request('status') == 'REJECTED' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Timestamp</th>
                                <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">RL Details</th>
                                <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Requester</th>
                                <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Company</th>
                                <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Status</th>
                                <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @foreach($activities as $rl)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                                    <div class="text-base font-semibold text-gray-900 dark:text-white">{{ $rl->created_at->format('H:i') }}</div>
                                    <div class="text-xs text-gray-500">{{ $rl->created_at->format('d M Y') }}</div>
                                </td>
                                <td class="p-4 flex flex-col">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $rl->rl_no }}</div>
                                    <div class="text-xs text-gray-500 truncate w-48" title="{{ $rl->subject }}">{{ $rl->subject }}</div>
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($rl->requester->profile_photo_path)
                                            <img class="w-8 h-8 rounded-full mr-3" src="{{ asset('storage/'.$rl->requester->profile_photo_path) }}">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold mr-3 text-xs">
                                                {{ substr($rl->requester->full_name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $rl->requester->full_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $rl->requester->department->department_name ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <span class="bg-primary-100 text-primary-800 text-xs font-medium px-2.5 py-0.5 rounded border border-primary-400">
                                        {{ $rl->company->company_code }}
                                    </span>
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    @if($rl->status_flow == 'ON_PROGRESS')
                                        <span class="bg-orange-100 text-orange-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-orange-900 dark:text-orange-300">Pending</span>
                                    @elseif($rl->status_flow == 'APPROVED')
                                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Approved</span>
                                    @elseif($rl->status_flow == 'REJECTED')
                                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Rejected</span>
                                    @elseif($rl->status_flow == 'DRAFT')
                                        <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">Draft</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">{{ $rl->status_flow }}</span>
                                    @endif
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <a href="{{ route('requisitions.show', $rl->id) }}" target="_blank" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-xs px-3 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none">
                                        Audit Detail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="p-4">
            {{ $activities->links() }}
        </div>
    </div>
</x-app-layout>
