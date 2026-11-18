<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight">
                    {{ __('Service Types') }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manage marine inspection, refilling, hydrotest, and maintenance service definitions</p>
            </div>
            
            @php
                $routePrefix = request()->routeIs('engineer.*') ? 'engineer' : 'admin';
            @endphp
            <div class="flex items-center gap-2" x-data>
                <a href="{{ route('admin.service-types.export') }}" class="btn-ghost text-xs gap-1.5" title="Export Service Types to CSV">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Export CSV</span>
                </a>
                
                <button @click="$dispatch('open-import-modal')" class="btn-ghost text-xs gap-1.5" title="Import Service Types from CSV">
                    <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    <span>Import CSV</span>
                </button>

                <a href="{{ route($routePrefix . '.service-types.create') }}" class="btn-primary text-xs gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Add New Service Type</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="table-container">
                <table class="theme-table">
                    <thead>
                        <tr class="theme-table-header">
                            <th class="px-6 py-3.5">Service Name</th>
                            <th class="px-6 py-3.5">Code</th>
                            <th class="px-6 py-3.5">Quantity</th>
                            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager') || auth()->user()->hasRole('accountant'))
                                <th class="px-6 py-3.5">Default Price</th>
                            @endif
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700/60">
                        @forelse($services as $service)
                        <tr class="theme-table-row">
                            <td class="px-6 py-4 font-bold text-slate-900 dark:text-white text-sm">
                                {{ $service->name }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="badge-neutral font-mono text-xs font-bold tracking-wide uppercase px-2.5 py-1">
                                    {{ $service->code }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600 dark:text-slate-400">
                                {{ $service->quantity ?? '-' }}
                            </td>
                            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager') || auth()->user()->hasRole('accountant'))
                                <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white text-sm">
                                    QAR {{ number_format($service->default_price, 2) }}
                                </td>
                            @endif
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route($routePrefix . '.service-types.edit', $service) }}" class="btn-secondary btn-sm">Edit</a>
                                    <form action="{{ route($routePrefix . '.service-types.destroy', $service) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this service type?')">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager') || auth()->user()->hasRole('accountant')) ? 5 : 4 }}" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500 text-sm">
                                No service types configured yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($services->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-700/80 bg-slate-50 dark:bg-slate-800">
                    {{ $services->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Import CSV Modal -->
    <div x-data="{ open: false }"
         @open-import-modal.window="open = true"
         x-show="open" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-xs transition-opacity" @click="open = false"></div>

        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <form action="{{ route('admin.service-types.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-200 dark:border-slate-700">
                            <div class="w-10 h-10 rounded-lg bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center border border-sky-200 dark:border-sky-800/50">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Import Service Types</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Bulk import service type definitions via CSV file.</p>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">
                                CSV requirement: Must include service name, code, quantity, and default price. 
                                <a href="{{ route('admin.service-types.template') }}" class="text-brand-600 dark:text-brand-400 font-bold hover:underline">Download CSV Template</a>
                            </p>
                            
                            <label class="theme-label">Select CSV File</label>
                            <input type="file" name="file" accept=".csv" required class="theme-input text-xs"/>
                        </div>
                    </div>
                    
                    <div class="p-4 bg-slate-50 dark:bg-slate-900/60 border-t border-slate-200 dark:border-slate-700/80 flex justify-end gap-3">
                        <button type="button" @click="open = false" class="btn-secondary text-xs">Cancel</button>
                        <button type="submit" class="btn-primary text-xs">Upload & Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
