<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight">
                    {{ __('Field Service Jobs') }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manage field service dispatches, inspections, and completion status</p>
            </div>
            <a href="{{ route('admin.jobs.create') }}" class="btn-primary text-xs gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Create New Job</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="theme-card p-0 overflow-hidden shadow-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">

                <!-- Filter Bar -->
                <div class="p-3.5 border-b border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-950">
                    <form method="GET" action="{{ route('admin.jobs.index') }}" class="flex flex-col sm:flex-row gap-3 items-center justify-between">
                         <div class="flex items-center gap-3 w-full sm:w-auto">
                             <select name="status" class="w-full sm:w-48 text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 py-2 focus:ring-2 focus:ring-brand-500" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
                                <option value="Assigned" {{ request('status') == 'Assigned' ? 'selected' : '' }}>Assigned</option>
                                <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                         </div>
                    </form>
                </div>

                <div class="p-6 bg-white dark:bg-slate-900">
                    <div class="table-container shadow-sm border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden bg-white dark:bg-slate-900">
                        <table class="theme-table">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    <th class="px-6 py-3.5">Job #</th>
                                    <th class="px-6 py-3.5">Customer / Vessel</th>
                                    <th class="px-6 py-3.5">Description</th>
                                    <th class="px-6 py-3.5">Assigned To</th>
                                    <th class="px-6 py-3.5">Status</th>
                                    <th class="px-6 py-3.5 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                @forelse ($jobs as $job)
                                <tr class="theme-table-row hover:bg-slate-50 dark:hover:bg-slate-800/70 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="inline-block font-mono text-xs font-bold text-brand-700 dark:text-brand-300 bg-brand-100/80 dark:bg-brand-950/80 border border-brand-300 dark:border-brand-800/60 px-2.5 py-1 rounded-md tracking-wider">
                                            {{ $job->job_number }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        <div class="font-bold text-slate-900 dark:text-white capitalize">{{ $job->customer->company_name ?? 'N/A' }}</div>
                                        <div class="text-slate-500 dark:text-slate-400 mt-0.5 font-medium">Vessel: {{ $job->vessel->name ?? 'No Vessel' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-600 dark:text-slate-300 max-w-xs truncate font-medium">
                                        {{ Str::limit($job->scope_of_work, 50) }}
                                    </td>
                                    <td class="px-6 py-4 text-xs font-medium text-slate-700 dark:text-slate-300">
                                        {{ $job->engineer->name ?? 'Unassigned' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold
                                            {{ $job->status === 'Open' ? 'bg-blue-100 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300 border border-blue-300 dark:border-blue-800/60' : '' }}
                                            {{ $job->status === 'Assigned' ? 'bg-amber-100 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 border border-amber-300 dark:border-amber-800/60' : '' }}
                                            {{ $job->status === 'In Progress' ? 'bg-amber-100 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 border border-amber-300 dark:border-amber-800/60' : '' }}
                                            {{ $job->status === 'Completed' ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800/60' : '' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $job->status === 'Completed' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                            {{ $job->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.jobs.show', $job->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:border-brand-500/50 hover:text-brand-600 dark:hover:text-brand-400 transition-all shadow-xs">
                                                <span>View</span>
                                            </a>
                                            <a href="{{ route('admin.jobs.edit', $job->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-600/10 hover:bg-brand-600 text-brand-600 dark:text-brand-400 hover:text-white border border-brand-500/30 transition-all shadow-xs">
                                                <span>Edit</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500 text-sm">
                                        No service jobs found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 border-t border-slate-200 dark:border-slate-800 pt-4">
                        {{ $jobs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
