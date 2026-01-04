<x-app-layout>
    <div class="py-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- ============================================================ --}}
            {{-- 🚨 ACTION REQUIRED: ARRIVAL CONFIRMATION (DYNAMIC ALERT)   --}}
            {{-- ============================================================ --}}
            @if(isset($waitingConfirmation) && $waitingConfirmation->count() > 0)
                @php
                    $alertStatus = 'safe'; 
                    $alertColor = 'from-emerald-500 to-teal-600';
                    $shadowColor = 'shadow-emerald-500/20';
                    $iconColor = 'text-emerald-600';
                    $bgColor = 'bg-emerald-100';
                    
                    $titleText = "IN-TRANSIT / AWAITING DELIVERY";
                    $descText = "Requisitions have been approved and are currently in the procurement pipeline. Kindly confirm receipt once the goods are in your possession.";

                    foreach($waitingConfirmation as $rl) {
                        $dueDate = \Carbon\Carbon::parse($rl->required_date);
                        
                        if ($dueDate->isPast()) {
                            $alertStatus = 'overdue';
                            $alertColor = 'from-rose-600 to-red-700';
                            $shadowColor = 'shadow-rose-500/30';
                            $iconColor = 'text-rose-600';
                            $bgColor = 'bg-rose-100';
                            $titleText = "⚠️ CRITICAL: DELIVERY OVERDUE";
                            $descText = "Several items have exceeded their expected delivery date. Immediate physical verification and system confirmation are required to close these tickets.";
                            break; 
                        } 
                        elseif ($dueDate->diffInDays(now()) <= 3) {
                            if ($alertStatus != 'overdue') { 
                                $alertStatus = 'warning';
                                $alertColor = 'from-orange-400 to-amber-500';
                                $shadowColor = 'shadow-orange-500/30';
                                $iconColor = 'text-orange-600';
                                $bgColor = 'bg-orange-100';
                                $titleText = "⚡ ARRIVAL IMMINENT";
                                $descText = "Shipments are scheduled to arrive shortly. Please be on standby to inspect and confirm receipt upon arrival.";
                            }
                        }
                    }
                @endphp

                <div class="mb-10 bg-gradient-to-r {{ $alertColor }} rounded-3xl shadow-xl {{ $shadowColor }} p-1 relative overflow-hidden transform hover:scale-[1.01] transition duration-300 animate-fade-in-down">
                    <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-48 h-48 bg-white opacity-10 rounded-full blur-2xl"></div>
                    
                    <div class="bg-white dark:bg-gray-800 rounded-[1.4rem] p-6 md:p-8 relative z-10">
                        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-8">
                            <div class="flex items-start gap-5 max-w-2xl">
                                <div class="p-4 {{ $bgColor }} {{ $iconColor }} rounded-2xl shadow-sm relative">
                                    @if($alertStatus == 'overdue')
                                        <span class="absolute top-0 right-0 -mt-1 -mr-1 flex h-3 w-3">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                                        </span>
                                    @endif
                                    
                                    @if($alertStatus == 'safe')
                                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                                    @elseif($alertStatus == 'warning')
                                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @else
                                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="text-2xl font-black text-slate-800 dark:text-white uppercase">{{ $titleText }}</h3>
                                    <p class="text-slate-500 dark:text-slate-300 mt-2 text-sm leading-relaxed">
                                        {{ $descText }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex flex-col gap-3 w-full lg:w-[480px]">
                                @foreach($waitingConfirmation as $rl)
                                    @php
                                        $itemDate = \Carbon\Carbon::parse($rl->required_date);
                                        $isLate = $itemDate->isPast();
                                        $isNear = $itemDate->diffInDays(now()) <= 3;
                                        $borderColor = 'border-slate-100 hover:border-emerald-300';
                                        $tagColor = 'bg-emerald-100 text-emerald-700';
                                        $tagText = 'On Track';

                                        if ($isLate) {
                                            $borderColor = 'border-red-100 hover:border-red-400 bg-red-50/30';
                                            $tagColor = 'bg-red-100 text-red-700';
                                            $tagText = 'LATE';
                                        } elseif ($isNear) {
                                            $borderColor = 'border-orange-100 hover:border-orange-400 bg-orange-50/30';
                                            $tagColor = 'bg-orange-100 text-orange-700';
                                            $tagText = 'Due Soon';
                                        }
                                    @endphp

                                    <a href="{{ route('requisitions.show', $rl->id) }}" class="group relative flex items-center justify-between p-3 bg-white {{ $borderColor }} border-2 rounded-xl transition-all shadow-sm hover:shadow-md cursor-pointer">
                                        <div class="flex items-center gap-3 overflow-hidden">
                                            <div class="bg-slate-100 text-slate-600 font-mono font-bold text-[10px] px-2 py-1 rounded-lg border border-slate-200">
                                                {{ $rl->rl_no }}
                                            </div>
                                            <div class="flex flex-col truncate">
                                                <span class="text-sm font-bold text-slate-700 truncate">{{ $rl->subject }}</span>
                                                <div class="flex items-center gap-2 mt-0.5">
                                                    <span class="text-[10px] font-bold {{ $tagColor }} px-1.5 py-0.5 rounded">{{ $tagText }}</span>
                                                    <span class="text-[10px] text-slate-400">Due: {{ $itemDate->format('d M') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pl-2">
                                            <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-800 text-white rounded-lg text-xs font-bold group-hover:bg-slate-900 transition-all shadow-lg">
                                                Confirm
                                                <svg class="w-3 h-3 transition-transform transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- 2. DASHBOARD HEADER (ENGLISH) --}}
            <div class="flex flex-col md:flex-row justify-between items-end mb-8">
                <div>
                    <h2 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400 tracking-tight">
                        Dashboard
                    </h2>
                    <p class="text-sm text-gray-500 mt-2 font-medium">
                        Welcome back, <span class="text-gray-800 dark:text-gray-200 font-bold">{{ Auth::user()->full_name }}</span>.
                        Total Volume: <span class="font-bold text-blue-600">{{ $stats['total_all'] }} Documents</span>
                    </p>
                </div>

                @if($isApprover ?? false)
                <div class="mt-4 md:mt-0 p-1 bg-white dark:bg-gray-800 rounded-full shadow-md border border-gray-200 dark:border-gray-700 flex">
                    <a href="{{ route('dashboard.select_role', 'approver') }}"
                       class="px-6 py-2 rounded-full text-sm font-bold transition-all transform duration-200 {{ $currentMode == 'approver' ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg scale-105' : 'text-gray-500 hover:text-blue-600' }}">
                        Approver
                    </a>
                    <a href="{{ route('dashboard.select_role', 'requester') }}"
                       class="px-6 py-2 rounded-full text-sm font-bold transition-all transform duration-200 {{ $currentMode == 'requester' ? 'bg-gradient-to-r from-indigo-600 to-purple-500 text-white shadow-lg scale-105' : 'text-gray-500 hover:text-purple-600' }}">
                        Requester
                    </a>
                </div>
                @endif
            </div>

            {{-- 3. MY ACTION ALERT (ENGLISH - FOR DRAFTS/REJECTED) --}}
            @if($stats['my_actions'] > 0)
            <div class="mb-8 relative overflow-hidden rounded-2xl shadow-xl shadow-red-500/20 animate-fade-in-down">
                <div class="absolute inset-0 bg-gradient-to-r from-red-600 via-orange-500 to-red-500 animate-gradient-x"></div>
                <div class="relative bg-white/95 dark:bg-gray-800/95 m-[2px] rounded-2xl p-4 flex flex-col md:flex-row justify-between items-center backdrop-blur-sm">
                    <div class="flex items-center mb-3 md:mb-0">
                        <div class="p-3 bg-red-100 text-red-600 rounded-full mr-4 shadow-inner">
                            <svg class="w-6 h-6 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-gray-800 dark:text-white text-lg">Action Required!</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300">You have <strong class="text-red-600 text-lg">{{ $stats['my_actions'] }} documents</strong> (Draft/Rejected) pending your response.</p>
                        </div>
                    </div>
                    <a href="{{ $currentMode == 'approver' ? route('requisitions.status', 'on_progress') : route('requisitions.status', 'draft') }}"
                       class="bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg transform transition hover:scale-105 hover:-translate-y-1">
                        Process Now &rarr;
                    </a>
                </div>
            </div>
            @endif

{{-- 4. STATS GRID --}}
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
                
                {{-- CARD 1: WAITING APPROVAL --}}
                <div class="relative overflow-hidden rounded-2xl shadow-lg shadow-orange-500/20 group hover:-translate-y-1 transition duration-300 bg-gradient-to-br from-orange-400 to-red-500">
                    <div class="absolute -right-4 -bottom-4 opacity-20 transform rotate-12 scale-150 group-hover:scale-125 transition duration-500">
                        <svg class="w-24 h-24 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                    </div>
                    <div class="relative p-5 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-orange-100 text-[10px] font-bold uppercase tracking-wider">Waiting Approval</p>
                                <h3 class="text-3xl font-extrabold mt-1">{{ $stats['waiting_approval'] }}</h3>
                            </div>
                            <div class="p-2 bg-white/20 backdrop-blur-sm rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                        <div class="mt-3 text-[10px] font-medium bg-white/20 w-fit px-2 py-0.5 rounded backdrop-blur-sm">Needs Manager Review</div>
                    </div>
                </div>

                {{-- CARD 2: WAITING DIRECTOR --}}
                <div class="relative overflow-hidden rounded-2xl shadow-lg shadow-purple-500/20 group hover:-translate-y-1 transition duration-300 bg-gradient-to-br from-purple-500 to-indigo-600">
                    <div class="absolute -right-4 -bottom-4 opacity-20 transform rotate-12 scale-150 group-hover:scale-125 transition duration-500">
                        <svg class="w-24 h-24 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                    </div>
                    <div class="relative p-5 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-purple-100 text-[10px] font-bold uppercase tracking-wider">Waiting Director</p>
                                <h3 class="text-3xl font-extrabold mt-1">{{ $stats['waiting_director'] }}</h3>
                            </div>
                            <div class="p-2 bg-white/20 backdrop-blur-sm rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                        </div>
                        <div class="mt-3 text-[10px] font-medium bg-white/20 w-fit px-2 py-0.5 rounded backdrop-blur-sm">Partially Approved</div>
                    </div>
                </div>

                {{-- CARD 3: PROCUREMENT & DELIVERY (GABUNGAN) --}}
                <div class="relative overflow-hidden rounded-2xl shadow-lg shadow-yellow-500/20 group hover:-translate-y-1 transition duration-300 bg-gradient-to-br from-yellow-400 to-orange-500">
                    <div class="absolute -right-4 -bottom-4 opacity-20 transform -rotate-12 scale-150 group-hover:scale-125 transition duration-500">
                        <svg class="w-24 h-24 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z" /><path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd" /></svg>
                    </div>
                    <div class="relative p-5 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-yellow-100 text-[10px] font-bold uppercase tracking-wider">Procurement & Delivery</p>
                                <h3 class="text-3xl font-extrabold mt-1">
                                    {{ $stats['waiting_supply'] + $stats['approved'] }}
                                </h3>
                            </div>
                            <div class="p-2 bg-white/20 backdrop-blur-sm rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                            </div>
                        </div>
                        
                        {{-- Rincian Kecil: Buy vs Ship --}}
                        <div class="mt-3 flex items-center gap-2 text-[10px] font-medium bg-white/20 w-fit px-2 py-1 rounded backdrop-blur-sm">
                            <span class="flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-200"></span>
                                Buy: {{ $stats['waiting_supply'] }}
                            </span>
                            <span class="opacity-50">|</span>
                            <span class="flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-200"></span>
                                Ship: {{ $stats['approved'] }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- CARD 4: COMPLETED --}}
                <div class="relative overflow-hidden rounded-2xl shadow-lg shadow-teal-500/20 group hover:-translate-y-1 transition duration-300 bg-gradient-to-br from-teal-400 to-emerald-600">
                    <div class="absolute -right-4 -bottom-4 opacity-20 transform rotate-12 scale-150 group-hover:scale-125 transition duration-500">
                        <svg class="w-24 h-24 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                    </div>
                    <div class="relative p-5 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-teal-100 text-[10px] font-bold uppercase tracking-wider">Completed</p>
                                <h3 class="text-3xl font-extrabold mt-1">{{ $stats['completed'] }}</h3>
                            </div>
                            <div class="p-2 bg-white/20 backdrop-blur-sm rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                        <div class="mt-3 text-[10px] font-medium bg-white/20 w-fit px-2 py-0.5 rounded backdrop-blur-sm">Successfully Done</div>
                    </div>
                </div>

                {{-- CARD 5: REJECTED --}}
                <div class="relative overflow-hidden rounded-2xl shadow-lg shadow-red-500/20 group hover:-translate-y-1 transition duration-300 bg-gradient-to-br from-red-500 to-pink-700">
                    <div class="absolute -right-4 -bottom-4 opacity-20 transform -rotate-12 scale-150 group-hover:scale-125 transition duration-500">
                        <svg class="w-24 h-24 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                    </div>
                    <div class="relative p-5 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-red-100 text-[10px] font-bold uppercase tracking-wider">Rejected</p>
                                <h3 class="text-3xl font-extrabold mt-1">{{ $stats['rejected'] }}</h3>
                            </div>
                            <div class="p-2 bg-white/20 backdrop-blur-sm rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                        <div class="mt-3 text-[10px] font-medium bg-white/20 w-fit px-2 py-0.5 rounded backdrop-blur-sm">Action Needed</div>
                    </div>
                </div>
            </div>

            {{-- 5. BOTTOM GRID --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">

                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md border-t-4 border-blue-500">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="text-lg font-extrabold text-gray-800 dark:text-white flex items-center">
                                    <span class="text-2xl mr-2">📊</span> Analysis by Priority
                                </h3>
                                <p class="text-xs text-gray-500 mt-1">Urgency classification based on remaining days until deadline.</p>
                            </div>
                            <span class="text-xs font-bold bg-blue-100 text-blue-600 px-3 py-1 rounded-full">Live Data</span>
                        </div>
                        <div class="relative h-64 w-full">
                            <canvas id="priorityChart"></canvas>
                        </div>
                        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-2 text-center">
                            <div class="bg-red-50 p-2 rounded-lg border border-red-100">
                                <div class="text-xs font-bold text-red-600">Top Urgent</div>
                                <div class="text-[10px] text-gray-500">Deadline ≤ 2 Days</div>
                                <div class="font-black text-lg text-red-700 mt-1">{{ $priorityStats['Top Urgent'] }}</div>
                            </div>
                            <div class="bg-orange-50 p-2 rounded-lg border border-orange-100">
                                <div class="text-xs font-bold text-orange-600">Urgent</div>
                                <div class="text-[10px] text-gray-500">Deadline ≤ 5 Days</div>
                                <div class="font-black text-lg text-orange-700 mt-1">{{ $priorityStats['Urgent'] }}</div>
                            </div>
                            <div class="bg-blue-50 p-2 rounded-lg border border-blue-100">
                                <div class="text-xs font-bold text-blue-600">Normal</div>
                                <div class="text-[10px] text-gray-500">Deadline > 5 Days</div>
                                <div class="font-black text-lg text-blue-700 mt-1">{{ $priorityStats['Normal'] }}</div>
                            </div>
                            <div class="bg-gray-100 p-2 rounded-lg border border-gray-200">
                                <div class="text-xs font-bold text-gray-700">Outstanding</div>
                                <div class="text-[10px] text-gray-500">Overdue</div>
                                <div class="font-black text-lg text-gray-800 mt-1">{{ $priorityStats['Outstanding'] }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md overflow-hidden border-t-4 border-indigo-500">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-indigo-50/30 dark:bg-gray-800 flex justify-between items-center">
                            <h3 class="text-lg font-extrabold text-gray-800 dark:text-white flex items-center">
                                <span class="text-2xl mr-2">🚀</span> Recent Activity
                            </h3>
                            <a href="#" class="text-sm font-bold text-indigo-600 hover:text-indigo-800">View All</a>
                        </div>
                        
                        @include('dashboard.partials.activity_table')
                        
                    </div>
                </div>

{{-- KOLOM KANAN: PIE CHART & LEGEND --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md border-t-4 border-teal-500 flex flex-col h-full">
                    <h3 class="text-lg font-extrabold text-gray-800 dark:text-white mb-6 flex items-center">
                        <span class="text-2xl mr-2">🍩</span> Composition
                    </h3>

                    {{-- Chart Canvas --}}
                    <div class="relative h-48 w-full mb-6">
                        <canvas id="statusChart"></canvas>
                    </div>

                    @php
                        $total = $stats['total_all'] > 0 ? $stats['total_all'] : 1;
                        
                        // 1. Completed
                        $pctCompleted = round(($stats['completed'] / $total) * 100);
                        
                        // 2. Processing (Manager + Director + Supply) -> Kelompok "Paperwork"
                        $countProcessing = $stats['waiting_approval'] + $stats['waiting_director'] + $stats['waiting_supply'];
                        $pctProcessing = round(($countProcessing / $total) * 100);

                        // 3. In Delivery (Approved) -> Kelompok "Logistics" (FITUR BARU)
                        $countDelivery = $stats['approved'];
                        $pctDelivery = round(($countDelivery / $total) * 100);

                        // 4. Rejected
                        $pctRejected = round(($stats['rejected'] / $total) * 100);
                    @endphp

                    <div class="space-y-4 mb-6">
                        
                        {{-- A. Completed (Teal) --}}
                        <div>
                            <div class="flex justify-between items-center text-xs font-bold mb-1">
                                <span class="text-teal-700">Completed ({{ $pctCompleted }}%)</span>
                                <span class="text-gray-500">{{ $stats['completed'] }}/{{ $stats['total_all'] }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-teal-500 h-2 rounded-full" style="width: {{ $pctCompleted }}%"></div>
                            </div>
                        </div>

                        {{-- B. In Delivery (Blue) -> INI YANG BARU --}}
                        <div>
                            <div class="flex justify-between items-center text-xs font-bold mb-1">
                                <span class="text-blue-600 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                    In Delivery ({{ $pctDelivery }}%)
                                </span>
                                <span class="text-gray-500">{{ $countDelivery }}/{{ $stats['total_all'] }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full shadow-[0_0_10px_rgba(59,130,246,0.5)]" style="width: {{ $pctDelivery }}%"></div>
                            </div>
                        </div>

                        {{-- C. Processing (Orange) -> Diganti dari "On Progress" --}}
                        <div>
                            <div class="flex justify-between items-center text-xs font-bold mb-1">
                                <span class="text-orange-600">Processing ({{ $pctProcessing }}%)</span>
                                <span class="text-gray-500">{{ $countProcessing }}/{{ $stats['total_all'] }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-orange-400 h-2 rounded-full" style="width: {{ $pctProcessing }}%"></div>
                            </div>
                        </div>

                         {{-- D. Rejected (Red) --}}
                         <div>
                            <div class="flex justify-between items-center text-xs font-bold mb-1">
                                <span class="text-red-600">Rejected ({{ $pctRejected }}%)</span>
                                <span class="text-gray-500">{{ $stats['rejected'] }}/{{ $stats['total_all'] }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-red-500 h-2 rounded-full" style="width: {{ $pctRejected }}%"></div>
                            </div>
                        </div>
                    </div>

                    {{-- ON PROGRESS DETAIL BREAKDOWN (Hanya untuk Processing) --}}
                    <div class="mt-auto bg-slate-50 dark:bg-slate-700/50 rounded-xl p-4 border border-slate-100 dark:border-slate-600">
                        <h4 class="text-[10px] font-extrabold text-slate-400 uppercase mb-3 tracking-wider">Processing Stage Details</h4>
                        <div class="grid grid-cols-3 gap-2">
                            <div class="text-center p-2 bg-white rounded-lg shadow-sm border border-slate-100">
                                <span class="block text-[10px] text-slate-500">Manager</span>
                                <span class="font-bold text-orange-600">{{ $stats['waiting_approval'] }}</span>
                            </div>
                            <div class="text-center p-2 bg-white rounded-lg shadow-sm border border-slate-100">
                                <span class="block text-[10px] text-slate-500">Director</span>
                                <span class="font-bold text-purple-600">{{ $stats['waiting_director'] }}</span>
                            </div>
                            <div class="text-center p-2 bg-white rounded-lg shadow-sm border border-slate-100">
                                <span class="block text-[10px] text-slate-500">Supply</span>
                                <span class="font-bold text-yellow-600">{{ $stats['waiting_supply'] }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Footer Stats (Users/Depts/Corps) --}}
                    <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700 grid grid-cols-3 gap-2">
                        <div class="text-center">
                            <span class="block text-lg font-black text-gray-800 dark:text-white">{{ $masterData['employees'] }}</span>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase">Users</span>
                        </div>
                        <div class="text-center border-l border-r border-gray-100 dark:border-gray-700">
                            <span class="block text-lg font-black text-gray-800 dark:text-white">{{ $masterData['departments'] }}</span>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase">Depts</span>
                        </div>
                        <div class="text-center">
                            <span class="block text-lg font-black text-gray-800 dark:text-white">{{ $masterData['companies'] }}</span>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase">Corps</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Chart.defaults.font.family = "'Figtree', sans-serif";
            Chart.defaults.color = '#6b7280';

            const ctxStatus = document.getElementById('statusChart').getContext('2d');
            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [{
                        data: @json($chartData['data']),
                        backgroundColor: @json($chartData['colors']),
                        borderWidth: 0,
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: { legend: { display: false } }
                }
            });

            const ctxPriority = document.getElementById('priorityChart').getContext('2d');
            new Chart(ctxPriority, {
                type: 'bar',
                data: {
                    labels: ['Top Urgent', 'Urgent', 'Normal', 'Outstanding'],
                    datasets: [{
                        label: 'Requests',
                        data: [
                            {{ $priorityStats['Top Urgent'] }},
                            {{ $priorityStats['Urgent'] }},
                            {{ $priorityStats['Normal'] }},
                            {{ $priorityStats['Outstanding'] }}
                        ],
                        backgroundColor: [
                            '#ef4444', 
                            '#f97316', 
                            '#3b82f6', 
                            '#1f2937'
                        ],
                        borderRadius: 8,
                        barThickness: 35,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f3f4f6', drawBorder: false },
                            ticks: { display: false }
                        },
                        x: { grid: { display: false, drawBorder: false } }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        });
    </script>
</x-app-layout>