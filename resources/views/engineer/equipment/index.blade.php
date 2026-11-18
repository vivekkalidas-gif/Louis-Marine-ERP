<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight">
                    {{ __('Equipment Locker & Tools') }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manage master equipment, calibration status, and tool dispatches</p>
            </div>
            
            <div class="flex items-center gap-2" x-data>
                <a href="{{ route('engineer.equipment.export') }}" class="btn-ghost text-xs gap-1.5" title="Export to CSV">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>Export CSV</span>
                </a>

                <a href="{{ route('engineer.equipment.pdf') }}" class="btn-ghost text-xs gap-1.5" title="Download PDF">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    <span>Export PDF</span>
                </a>

                <button @click="$dispatch('open-import-modal')" class="btn-ghost text-xs gap-1.5" title="Import from CSV">
                    <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    <span>Import CSV</span>
                </button>

                <a href="{{ route('engineer.equipment.create') }}" class="btn-primary text-xs gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Add Equipment</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        <div class="max-w-7xl mx-auto space-y-6">
            
            <!-- Warning Banner for Expiring Calibration -->
            @if(isset($expiringCount) && $expiringCount > 0)
            <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/80 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-amber-100 dark:bg-amber-900/60 rounded-xl text-amber-700 dark:text-amber-300 shrink-0">
                        <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-900 dark:text-white">
                            Calibration Action Required: <span class="text-amber-700 dark:text-amber-300">{{ $expiringCount }}</span> item(s) due for calibration within 30 days.
                        </p>
                    </div>
                </div>
                <a href="{{ route('engineer.equipment.index', ['status' => 'expiring_soon']) }}" class="btn-primary text-xs bg-amber-600 hover:bg-amber-700 whitespace-nowrap">
                    Filter Expiring Items
                </a>
            </div>
            @endif

            <!-- Filter Card -->
            <div class="theme-card">
                <form action="{{ route('engineer.equipment.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <label class="theme-label">Search Equipment</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input class="theme-input pl-10 text-xs py-2" type="search" name="search" placeholder="Search by name, serial no, brand..." value="{{ request('search') }}" />
                        </div>
                    </div>

                    <div>
                        <label class="theme-label">Equipment Category / Type</label>
                        <select name="type_id" class="theme-input text-xs py-2" onchange="this.form.submit()">
                            <option value="all">All Types</option>
                            @foreach($types as $type)
                            <option value="{{ $type->id }}" {{ request('type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="theme-label">Calibration Status</label>
                        <select name="status" class="theme-input text-xs py-2" onchange="this.form.submit()">
                            <option value="all">All Statuses</option>
                            <option value="valid" {{ request('status') == 'valid' ? 'selected' : '' }}>Valid</option>
                            <option value="expiring_soon" {{ request('status') == 'expiring_soon' ? 'selected' : '' }}>Expiring Soon (< 30d)</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="not_required" {{ request('status') == 'not_required' ? 'selected' : '' }}>Not Required</option>
                        </select>
                    </div>
                </form>
            </div>

            <!-- Table Container -->
            <div class="table-container">
                @if($equipment->isEmpty())
                    <div class="flex flex-col items-center justify-center py-16 px-4 text-center">
                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">No Equipment Found</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mt-1">Get started by adding your first equipment record to track calibration & maintenance dispatches.</p>
                        <div class="mt-4">
                            <a href="{{ route('engineer.equipment.create') }}" class="btn-primary text-xs">
                                Add First Equipment Item
                            </a>
                        </div>
                    </div>
                @else
                    <table class="theme-table">
                        <thead>
                            <tr class="theme-table-header">
                                <th class="px-6 py-3.5">Equipment Item</th>
                                <th class="px-6 py-3.5">Type</th>
                                <th class="px-6 py-3.5">Serial / Code</th>
                                <th class="px-6 py-3.5">Calibration Expiry</th>
                                <th class="px-6 py-3.5">Status</th>
                                <th class="px-6 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700/60">
                            @foreach($equipment as $item)
                            <tr class="theme-table-row">
                                <td class="px-6 py-4 font-bold text-slate-900 dark:text-white text-sm">
                                    {{ $item->name }}
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold text-slate-700 dark:text-slate-300">
                                    {{ $item->type->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-slate-700 dark:text-slate-300">
                                    {{ $item->code ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-xs font-medium text-slate-700 dark:text-slate-300">
                                    @if($item->calibration_required)
                                        {{ $item->calibration_expiry_date ? \Carbon\Carbon::parse($item->calibration_expiry_date)->format('d M, Y') : 'Not Set' }}
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if(!$item->calibration_required)
                                        <span class="badge-neutral text-xs">Not Required</span>
                                    @elseif($item->status === 'valid')
                                        <span class="badge-success text-xs">Valid</span>
                                    @elseif($item->status === 'expiring_soon')
                                        <span class="badge-warning text-xs">Expiring Soon</span>
                                    @elseif($item->status === 'expired')
                                        <span class="badge-danger text-xs">Expired</span>
                                    @else 
                                        <span class="badge-neutral text-xs">Pending</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('engineer.equipment.edit', $item->id) }}" class="btn-secondary btn-sm">Edit</a>
                                        @if($item->calibration_required)
                                        <a href="{{ route('admin.dispatch.create', ['equipment_id' => $item->id, 'type' => 'calibration']) }}" class="btn-ghost btn-sm text-amber-600 dark:text-amber-400 font-bold" title="Prepare Calibration Dispatch">
                                            Dispatch
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    @if($equipment->hasPages())
                    <div class="p-4 border-t border-slate-200 dark:border-slate-700/80 bg-slate-50 dark:bg-slate-800">
                        {{ $equipment->links() }}
                    </div>
                    @endif
                @endif
            </div>

        </div>
    </div>

    <!-- Import Modal -->
    <div x-data="{ open: false }"
         @open-import-modal.window="open = true"
         x-show="open" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-xs transition-opacity" @click="open = false"></div>

        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <form action="{{ route('engineer.equipment.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-200 dark:border-slate-700">
                            <div class="w-10 h-10 rounded-lg bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center border border-sky-200 dark:border-sky-800/50">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Import Equipment Items</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Bulk import equipment definitions via CSV file.</p>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">
                                CSV requirement: Must include equipment name, brand, code, and category. 
                                <a href="{{ route('engineer.equipment.template') }}" class="text-brand-600 dark:text-brand-400 font-bold hover:underline">Download CSV Template</a>
                            </p>
                            
                            <label class="theme-label">Select CSV File</label>
                            <input type="file" name="file" accept=".csv,.txt" required class="theme-input text-xs"/>
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
