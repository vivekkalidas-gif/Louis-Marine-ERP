<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight">
                    {{ __('Field Service Jobs') }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manage field service dispatches, inspections, and completion status</p>
            </div>
            <a href="{{ route('engineer.jobs.create') }}" class="btn-primary text-xs gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Create New Job</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="theme-card p-0 overflow-hidden shadow-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
                
                <!-- Status Filter Navigation Bar (Solid Dark Mode Styling) -->
                <div class="flex flex-wrap border-b border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-950 p-2 gap-1.5">
                    <a href="{{ route('engineer.jobs.index') }}" class="px-3.5 py-2 rounded-lg text-xs font-bold transition-all {{ !request('status') ? 'bg-white dark:bg-slate-800 text-brand-600 dark:text-brand-400 shadow-md border border-slate-200 dark:border-slate-700' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/60 dark:hover:bg-slate-800/60' }}">
                        All Jobs
                    </a>
                    <a href="{{ route('engineer.jobs.index', ['status' => 'today']) }}" class="px-3.5 py-2 rounded-lg text-xs font-bold transition-all {{ request('status') == 'today' ? 'bg-white dark:bg-slate-800 text-brand-600 dark:text-brand-400 shadow-md border border-slate-200 dark:border-slate-700' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/60 dark:hover:bg-slate-800/60' }}">
                        Today's Schedule
                    </a>
                    <a href="{{ route('engineer.jobs.index', ['status' => 'overdue']) }}" class="px-3.5 py-2 rounded-lg text-xs font-bold transition-all {{ request('status') == 'overdue' ? 'bg-white dark:bg-slate-800 text-rose-600 dark:text-rose-400 shadow-md border border-slate-200 dark:border-slate-700' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/60 dark:hover:bg-slate-800/60' }}">
                        Overdue
                    </a>
                    <a href="{{ route('engineer.jobs.index', ['status' => 'requested']) }}" class="px-3.5 py-2 rounded-lg text-xs font-bold transition-all {{ request('status') == 'requested' ? 'bg-white dark:bg-slate-800 text-sky-600 dark:text-sky-400 shadow-md border border-slate-200 dark:border-slate-700' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/60 dark:hover:bg-slate-800/60' }}">
                        Requested
                    </a>
                    <a href="{{ route('engineer.jobs.index', ['status' => 'in-progress']) }}" class="px-3.5 py-2 rounded-lg text-xs font-bold transition-all {{ request('status') == 'in-progress' ? 'bg-white dark:bg-slate-800 text-amber-600 dark:text-amber-400 shadow-md border border-slate-200 dark:border-slate-700' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/60 dark:hover:bg-slate-800/60' }}">
                        In Progress
                    </a>
                    <a href="{{ route('engineer.jobs.index', ['status' => 'completed']) }}" class="px-3.5 py-2 rounded-lg text-xs font-bold transition-all {{ request('status') == 'completed' ? 'bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 shadow-md border border-slate-200 dark:border-slate-700' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/60 dark:hover:bg-slate-800/60' }}">
                        Completed
                    </a>
                </div>

                <!-- Search Filter Form (Solid Dark Container) -->
                <div class="p-3.5 border-b border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-950">
                    <form action="{{ route('engineer.jobs.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                        @if(request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Job #, Title, Customer Company, Vessel..." class="w-full pl-10 pr-4 py-2 text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all">
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="submit" class="btn-primary text-xs py-2 px-4 shadow-xs">Search</button>
                            @if(request('search'))
                                <a href="{{ route('engineer.jobs.index', request()->only('status')) }}" class="btn-ghost text-xs py-2 px-3">Clear</a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Table Content -->
                <div class="p-6 bg-white dark:bg-slate-900">
                    <div class="table-container shadow-sm border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden bg-white dark:bg-slate-900">
                        <table class="theme-table">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    <th class="px-6 py-3.5">Job Ref # & Title</th>
                                    <th class="px-6 py-3.5">Customer & Vessel</th>
                                    <th class="px-6 py-3.5">Lead Engineer</th>
                                    <th class="px-6 py-3.5">Status</th>
                                    <th class="px-6 py-3.5 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                @forelse($jobs as $job)
                                <tr class="theme-table-row hover:bg-slate-50 dark:hover:bg-slate-800/70 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="inline-block font-mono text-xs font-bold text-brand-700 dark:text-brand-300 bg-brand-100/80 dark:bg-brand-950/80 border border-brand-300 dark:border-brand-800/60 px-2.5 py-1 rounded-md tracking-wider">
                                            {{ $job->job_number }}
                                        </div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 truncate max-w-xs mt-1.5 font-medium">{{ $job->title ?? 'Field Service Job' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        <div class="font-bold text-slate-900 dark:text-white capitalize">{{ $job->customer->company_name ?? 'N/A' }}</div>
                                        <div class="text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-1 font-medium">
                                            <span>Vessel: {{ $job->vessel->name ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-medium text-slate-700 dark:text-slate-300">
                                        {{ $job->engineer->name ?? 'Unassigned' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($job->status === 'completed')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800/60">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Completed
                                            </span>
                                        @elseif($job->status === 'in-progress')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 border border-amber-300 dark:border-amber-800/60">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                In Progress
                                            </span>
                                        @elseif($job->status === 'requested')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-sky-100 dark:bg-sky-950/80 text-sky-700 dark:text-sky-300 border border-sky-300 dark:border-sky-800/60">
                                                <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                                Requested
                                            </span>
                                        @elseif($job->status === 'overdue')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 border border-rose-300 dark:border-rose-800/60">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-bounce"></span>
                                                Overdue
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                {{ ucfirst($job->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            @if($job->status !== 'completed')
                                                <a href="{{ route('engineer.jobs.report.create', $job->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-600/10 hover:bg-emerald-600 text-emerald-600 dark:text-emerald-400 hover:text-white border border-emerald-500/30 transition-all shadow-xs" title="Create Field Service Report">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    <span>+ Report</span>
                                                </a>
                                            @endif
                                            <a href="{{ route('engineer.jobs.show', $job->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:border-brand-500/50 hover:text-brand-600 dark:hover:text-brand-400 transition-all shadow-xs">
                                                <svg class="w-3.5 h-3.5 text-brand-500 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>View Job</span>
                                            </a>
                                            @can('update', $job)
                                                <a href="{{ route('engineer.assignments.create', $job->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-600/10 hover:bg-brand-600 text-brand-600 dark:text-brand-400 hover:text-white border border-brand-500/30 transition-all shadow-xs">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                                    <span>Assign Tech</span>
                                                </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500 text-sm">
                                        No service jobs found matching criteria.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($jobs->hasPages())
                    <div class="mt-4 border-t border-slate-200 dark:border-slate-800 pt-4">
                        {{ $jobs->appends(request()->query())->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
