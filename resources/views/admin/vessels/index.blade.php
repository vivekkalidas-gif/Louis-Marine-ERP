<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight flex items-center gap-2">
                    <svg class="w-6 h-6 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                    <span>Vessel Management</span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manage registered fleet vessels, IMO numbers, and customer assignments</p>
            </div>
            <div class="flex items-center gap-2" x-data>
                <a href="{{ route('admin.vessels.export') }}" class="btn-secondary text-xs font-bold gap-1.5 shadow-sm" title="Export Vessels to CSV">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Export CSV</span>
                </a>

                @can('create', \App\Models\Vessel::class)
                <button @click="$dispatch('open-import-modal')" class="btn-secondary text-xs font-bold gap-1.5 shadow-sm" title="Import Vessels from CSV">
                    <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    <span>Import CSV</span>
                </button>
                <a href="{{ route('admin.vessels.create') }}" class="btn-primary text-xs font-bold gap-1.5 shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Add New Vessel</span>
                </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="table-container">
                <table class="theme-table">
                    <thead>
                        <tr class="theme-table-header">
                            <th class="px-6 py-3.5">Vessel Name</th>
                            <th class="px-6 py-3.5">Customer / Company</th>
                            <th class="px-6 py-3.5">Assigned Contact</th>
                            <th class="px-6 py-3.5">IMO Number</th>
                            <th class="px-6 py-3.5">Type</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse($vessels as $vessel)
                        <tr class="theme-table-row">
                            <td class="px-6 py-4 font-bold text-slate-900 dark:text-white text-sm">
                                <a href="{{ route('admin.vessels.edit', $vessel->id) }}" class="hover:text-brand-600 dark:hover:text-brand-400 transition-colors">
                                    {{ $vessel->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-300">
                                {{ $vessel->customer->company_name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600 dark:text-slate-400">
                                @if($vessel->companyContact)
                                    <span class="font-bold text-slate-900 dark:text-white">{{ $vessel->companyContact->name }}</span>
                                    <span class="block text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">{{ $vessel->companyContact->designation }}</span>
                                @else
                                    <span class="italic text-slate-400 dark:text-slate-500">Unassigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-mono text-xs font-bold text-slate-800 dark:text-slate-200">
                                {{ $vessel->imo_number ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-400">
                                {{ $vessel->type ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="{{ $vessel->status === 'active' ? 'badge-success' : 'badge-neutral' }} text-xs font-bold capitalize">
                                    {{ ucfirst($vessel->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.vessels.edit', $vessel->id) }}" class="btn-secondary btn-sm font-bold">Edit</a>
                                    @can('delete', $vessel)
                                    <form action="{{ route('admin.vessels.destroy', $vessel->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger btn-sm font-bold" onclick="return confirm('Are you sure you want to delete this vessel?')">Delete</button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500 text-sm font-medium">
                                No vessels registered in fleet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($vessels->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/60">
                    {{ $vessels->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
