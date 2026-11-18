<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight">
                    {{ __('Engineer Tool Locker') }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Track active tool assignments, pending storekeeper requests, and return history</p>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('engineer.tools.create') }}" class="btn-primary text-xs gap-1.5 py-2 font-bold shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Request Tool Allocation</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        <div class="max-w-7xl mx-auto space-y-6">

            <!-- KPI STAT SUMMARY STRIP -->
            @php
                $activeCount = $myRequests->where('status', 'issued')->count();
                $pendingCount = $myRequests->where('status', 'pending')->count();
                $returnedCount = $myRequests->where('status', 'returned')->count();
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="stat-card flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">In Possession</p>
                        <p class="text-2xl font-black text-brand-600 dark:text-brand-400 mt-1">{{ $activeCount }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-brand-50 dark:bg-brand-950/60 text-brand-600 dark:text-brand-400 border border-brand-200 dark:border-brand-800/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                </div>

                <div class="stat-card flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Pending Approval</p>
                        <p class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ $pendingCount }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>

                <div class="stat-card flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Returned History</p>
                        <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $returnedCount }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>
            </div>

            <!-- Active Assigned Tools & Pending Requests -->
            <div class="table-container">
                <div class="p-5 border-b border-slate-200 dark:border-slate-700/80 flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                            <span>Active Assigned Tools & Pending Requests</span>
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Equipment currently in your possession or awaiting storekeeper approval</p>
                    </div>

                    <a href="{{ route('engineer.tools.create') }}" class="btn-ghost text-xs gap-1">
                        <span>+ New Request</span>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="theme-table text-xs">
                        <thead>
                            <tr class="theme-table-header">
                                <th class="px-5 py-3.5">Tool Name & Serial Code</th>
                                <th class="px-5 py-3.5">Brand / Model</th>
                                <th class="px-5 py-3.5">Request Date</th>
                                <th class="px-5 py-3.5">Issued Date</th>
                                <th class="px-5 py-3.5">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
                            @forelse($myRequests->whereIn('status', ['pending', 'issued', 'approved']) as $request)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-brand-50 dark:bg-brand-950/60 text-brand-600 dark:text-brand-400 border border-brand-200 dark:border-brand-800/50 flex items-center justify-center font-bold shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 dark:text-white text-xs">{{ $request->tool->name }}</div>
                                            <span class="font-mono text-[10px] font-bold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded mt-0.5 inline-block">{{ $request->tool->code ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $request->tool->brand ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400">{{ $request->tool->model ?? '' }}</div>
                                </td>

                                <td class="px-5 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400">
                                    {{ optional($request->requested_at)->format('d M Y, H:i') }}
                                </td>

                                <td class="px-5 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400">
                                    {{ $request->issued_at ? $request->issued_at->format('d M Y, H:i') : '-' }}
                                </td>

                                <td class="px-5 py-4">
                                    @if($request->status == 'pending')
                                        <span class="badge-amber text-[10px]">Pending Storekeeper Approval</span>
                                    @elseif($request->status == 'approved')
                                        <span class="badge-info text-[10px]">Approved - Ready to Pick Up</span>
                                    @else
                                        <span class="badge-emerald text-[10px]">In Possession</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <svg class="w-10 h-10 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                                        <p class="text-xs font-bold text-slate-600 dark:text-slate-400">You currently have no active tools or pending requests.</p>
                                        <a href="{{ route('engineer.tools.create') }}" class="btn-primary btn-sm text-xs mt-1">Request Tool Allocation</a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tool Allocation History Card -->
            <div class="table-container">
                <div class="p-5 border-b border-slate-200 dark:border-slate-700/80">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Tool History & Returns</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Log of previously returned or rejected tool requests</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="theme-table text-xs">
                        <thead>
                            <tr class="theme-table-header">
                                <th class="px-5 py-3.5">Tool Name & Code</th>
                                <th class="px-5 py-3.5">Issued Date</th>
                                <th class="px-5 py-3.5">Returned Date</th>
                                <th class="px-5 py-3.5">Status & Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
                            @forelse($myRequests->whereIn('status', ['returned', 'rejected']) as $request)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-slate-900 dark:text-white text-xs">{{ $request->tool->name }}</div>
                                    <span class="font-mono text-[10px] text-slate-500 dark:text-slate-400">{{ $request->tool->code }}</span>
                                </td>
                                <td class="px-5 py-4 text-xs text-slate-600 dark:text-slate-400">
                                    {{ $request->issued_at ? $request->issued_at->format('d M Y') : '-' }}
                                </td>
                                <td class="px-5 py-4 text-xs text-slate-600 dark:text-slate-400">
                                    {{ $request->returned_at ? $request->returned_at->format('d M Y') : '-' }}
                                </td>
                                <td class="px-5 py-4">
                                    @if($request->status == 'returned')
                                        <span class="badge-neutral text-[10px]">Returned</span>
                                    @else
                                        <span class="badge-rose text-[10px]">Rejected</span>
                                        @if($request->rejection_reason)
                                            <p class="text-[11px] text-rose-500 font-semibold mt-0.5">{{ $request->rejection_reason }}</p>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500 text-xs">
                                    No past tool allocation history.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
