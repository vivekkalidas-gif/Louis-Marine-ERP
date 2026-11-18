<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight">
                    {{ __('Engineer Operational Hub') }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Field service dispatches, today's schedule, equipment calibration, and active jobs</p>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('engineer.jobs.create') }}" class="btn-primary text-xs gap-1.5 py-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>New Job Order</span>
                </a>
                <a href="{{ route('engineer.certificates.create') }}" class="btn-secondary text-xs gap-1.5 py-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                    <span>Issue Cert</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        <div class="max-w-7xl mx-auto space-y-6">

            <!-- Mobile Quick Action Shortcuts (PWA / Touch Friendly) -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <a href="{{ route('engineer.jobs.index', ['status' => 'today']) }}" class="p-3.5 rounded-xl border border-blue-200 dark:border-blue-800/60 bg-blue-50/50 dark:bg-blue-950/40 hover:bg-blue-100/50 dark:hover:bg-blue-900/40 transition flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold shadow-xs group-hover:scale-105 transition-transform shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-slate-900 dark:text-white">Today's Jobs</span>
                        <span class="text-[11px] font-bold text-blue-600 dark:text-blue-400">{{ $todaysCount }} Scheduled</span>
                    </div>
                </a>

                <a href="{{ route('engineer.jobs.index', ['status' => 'overdue']) }}" class="p-3.5 rounded-xl border border-rose-200 dark:border-rose-800/60 bg-rose-50/50 dark:bg-rose-950/40 hover:bg-rose-100/50 dark:hover:bg-rose-900/40 transition flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-lg bg-rose-600 text-white flex items-center justify-center font-bold shadow-xs group-hover:scale-105 transition-transform shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-slate-900 dark:text-white">Overdue Jobs</span>
                        <span class="text-[11px] font-bold text-rose-600 dark:text-rose-400">{{ $overdueCount }} Action Needed</span>
                    </div>
                </a>

                <a href="{{ route('engineer.equipment.index', ['status' => 'expiring_soon']) }}" class="p-3.5 rounded-xl border border-amber-200 dark:border-amber-800/60 bg-amber-50/50 dark:bg-amber-950/40 hover:bg-amber-100/50 dark:hover:bg-amber-900/40 transition flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-lg bg-amber-500 text-white flex items-center justify-center font-bold shadow-xs group-hover:scale-105 transition-transform shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-slate-900 dark:text-white">Calibrations</span>
                        <span class="text-[11px] font-bold text-amber-600 dark:text-amber-400">{{ $expiringEquipmentCount }} Due Soon</span>
                    </div>
                </a>

                <a href="{{ route('engineer.tools.index') }}" class="p-3.5 rounded-xl border border-teal-200 dark:border-teal-800/60 bg-teal-50/50 dark:bg-teal-950/40 hover:bg-teal-100/50 dark:hover:bg-teal-900/40 transition flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-lg bg-teal-600 text-white flex items-center justify-center font-bold shadow-xs group-hover:scale-105 transition-transform shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-slate-900 dark:text-white">My Tools</span>
                        <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">Equipment Locker</span>
                    </div>
                </a>
            </div>

            <!-- Calibration Action Banner (If Equipment Expiring) -->
            @if(isset($expiringEquipmentCount) && $expiringEquipmentCount > 0)
            <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/80 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-amber-100 dark:bg-amber-900/60 rounded-xl text-amber-700 dark:text-amber-300 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm">Equipment Calibration Due</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5">
                            You have <span class="font-bold text-amber-700 dark:text-amber-300">{{ $expiringEquipmentCount }}</span> tool(s) requiring calibration within 30 days.
                        </p>
                    </div>
                </div>
                <div class="flex gap-2 shrink-0">
                    <a href="{{ route('admin.dispatch.create', ['equipment_ids' => $expiringEquipmentIds, 'type' => 'Calibration']) }}" class="btn-ghost text-xs whitespace-nowrap">
                        Prepare Dispatch
                    </a>
                    <a href="{{ route('engineer.equipment.index', ['status' => 'expiring_soon']) }}" class="btn-primary text-xs whitespace-nowrap bg-amber-600 hover:bg-amber-700">
                        View Equipment
                    </a>
                </div>
            </div>
            @endif

            <!-- KPI Metric Summary Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <a href="{{ route('engineer.jobs.index') }}" class="stat-card flex items-center justify-between group">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Jobs</p>
                        <p class="text-2xl font-black text-slate-900 dark:text-white mt-1 group-hover:text-brand-600 transition-colors">{{ $myJobsCount ?? 0 }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-brand-50 dark:bg-brand-950/60 text-brand-600 dark:text-brand-400 border border-brand-200 dark:border-brand-800/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                </a>

                <a href="{{ route('engineer.jobs.index', ['status' => 'in-progress']) }}" class="stat-card flex items-center justify-between group">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">In Progress</p>
                        <p class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ $inProgressCount ?? 0 }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </a>

                <a href="{{ route('engineer.jobs.index', ['status' => 'completed']) }}" class="stat-card flex items-center justify-between group">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Completed</p>
                        <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $completedCount ?? 0 }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </a>

                <div class="stat-card flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Customers</p>
                        <p class="text-2xl font-black text-purple-600 dark:text-purple-400 mt-1">{{ $customersCount ?? 0 }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-800/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Quick Job Search & Filter Bar -->
            <div class="theme-card">
                <form action="{{ route('engineer.jobs.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" name="search" placeholder="Quick Search Job #, Vessel Name, Customer..." class="theme-input pl-10 text-xs py-2.5">
                    </div>
                    <button type="submit" class="btn-primary text-xs py-2.5 px-5">Search Jobs</button>
                </form>
            </div>

            <!-- Recent Service Jobs Queue Table -->
            <div class="table-container">
                <div class="p-5 border-b border-slate-200 dark:border-slate-700/80 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Active Service Job Dispatches</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Live view of recent service dispatches and status updates</p>
                    </div>
                    <a href="{{ route('engineer.jobs.index') }}" class="btn-ghost text-xs">View All Jobs</a>
                </div>

                @if(isset($recentJobs) && $recentJobs->count() > 0)
                <table class="theme-table">
                    <thead>
                        <tr class="theme-table-header">
                            <th class="px-6 py-3.5">Job Ref #</th>
                            <th class="px-6 py-3.5">Customer Company</th>
                            <th class="px-6 py-3.5">Vessel Name</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5">Scheduled Date</th>
                            <th class="px-6 py-3.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700/60">
                        @foreach($recentJobs as $job)
                        <tr class="theme-table-row">
                            <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">
                                {{ $job->job_number }}
                            </td>
                            <td class="px-6 py-4 text-slate-700 dark:text-slate-300">
                                {{ $job->customer->company_name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-slate-700 dark:text-slate-300">
                                {{ $job->vessel->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($job->status === 'completed')
                                    <span class="badge-success text-xs">Completed</span>
                                @elseif($job->status === 'in-progress')
                                    <span class="badge-warning text-xs">In Progress</span>
                                @elseif($job->status === 'requested')
                                    <span class="badge-info text-xs">Requested</span>
                                @else
                                    <span class="badge-neutral text-xs">{{ ucfirst($job->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500 dark:text-slate-400">
                                {{ $job->scheduled_at ? \Carbon\Carbon::parse($job->scheduled_at)->format('d M Y') : $job->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('engineer.jobs.show', $job->id) }}" class="btn-ghost btn-sm text-brand-600 dark:text-brand-400 font-bold">
                                    Open Job
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="text-center py-12 text-slate-400 dark:text-slate-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <p class="font-bold text-slate-900 dark:text-white text-base">No active jobs in queue</p>
                    <p class="text-xs mt-1">Get started by creating a new service job order.</p>
                    <div class="mt-4">
                        <a href="{{ route('engineer.jobs.create') }}" class="btn-primary text-xs">
                            Create First Job
                        </a>
                    </div>
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
