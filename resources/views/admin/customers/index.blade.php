<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight flex items-center gap-2">
                    <svg class="w-6 h-6 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Customer Directory</span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manage customer accounts, vessel associations, and contact details</p>
            </div>
            <div class="flex items-center gap-2" x-data>
                <a href="{{ route('admin.customers.export') }}" class="btn-secondary text-xs font-bold gap-1.5 shadow-sm" title="Export to CSV">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Export CSV</span>
                </a>
                
                <button @click="$dispatch('open-import-modal')" class="btn-secondary text-xs font-bold gap-1.5 shadow-sm" title="Import from CSV">
                    <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    <span>Import CSV</span>
                </button>

                <a href="{{ route('admin.customers.create') }}" class="btn-primary text-xs font-bold gap-1.5 shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Add New Customer</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        <div class="max-w-7xl mx-auto space-y-6">
            
            <!-- Filter Bar Card -->
            <div class="theme-card">
                <form method="GET" action="{{ route('admin.customers.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="theme-label mb-1">Company or Email</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search company, email..." class="theme-input text-xs dark:bg-slate-900 dark:text-white">
                    </div>
                    
                    <div>
                        <label class="theme-label mb-1">Customer Type</label>
                        <select name="type" class="theme-input text-xs dark:bg-slate-900 dark:text-white">
                            <option value="">All Customer Types</option>
                            <option value="Owner" {{ request('type') == 'Owner' ? 'selected' : '' }}>Ship Owner</option>
                            <option value="Operator" {{ request('type') == 'Operator' ? 'selected' : '' }}>Ship Operator</option>
                            <option value="Agent" {{ request('type') == 'Agent' ? 'selected' : '' }}>Agent</option>
                            <option value="Manager" {{ request('type') == 'Manager' ? 'selected' : '' }}>Manager</option>
                        </select>
                    </div>

                    <div>
                        <label class="theme-label mb-1">Account Status</label>
                        <select name="status" class="theme-input text-xs dark:bg-slate-900 dark:text-white">
                            <option value="">All Statuses</option>
                            <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit" class="btn-primary text-xs font-bold w-full py-2.5 shadow-sm">Filter</button>
                        <a href="{{ route('admin.customers.index') }}" class="btn-secondary text-xs font-bold py-2.5 px-3">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Customer Table Card -->
            <div class="table-container">
                <table class="theme-table">
                    <thead>
                        <tr class="theme-table-header">
                            <th class="px-6 py-3.5">Company Name</th>
                            <th class="px-6 py-3.5">Type</th>
                            <th class="px-6 py-3.5">Location</th>
                            <th class="px-6 py-3.5">Contacts</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse($customers as $customer)
                            <tr class="theme-table-row">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900 dark:text-white text-sm flex items-center gap-2">
                                        <a href="{{ route('admin.customers.show', $customer->id) }}" class="hover:text-brand-600 dark:hover:text-brand-400 transition-colors">
                                            {{ $customer->company_name }}
                                        </a>
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-mono">{{ $customer->email }}</div>
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold text-slate-700 dark:text-slate-300">
                                    {{ $customer->type ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-600 dark:text-slate-400">
                                    {{ $customer->city ?? '-' }}, {{ $customer->country }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="badge-neutral text-xs font-bold">
                                        {{ $customer->contacts->count() }} Contacts
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="{{ $customer->status == 'Active' ? 'badge-success' : 'badge-danger' }} text-xs font-bold">
                                        {{ $customer->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if(auth()->user()->hasRole(['admin', 'manager', 'accounts']))
                                        <a href="{{ route('admin.customers.ledger', $customer) }}" class="btn-secondary btn-sm text-emerald-600 dark:text-emerald-400 font-bold">Ledger</a>
                                        @endif
                                        <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn-secondary btn-sm text-brand-600 dark:text-brand-400 font-bold">View</a>
                                        <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn-secondary btn-sm font-bold">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500 text-sm font-medium">
                                    No customers found matching criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($customers->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/60">
                    {{ $customers->appends(request()->query())->links() }}
                </div>
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
            <div class="relative transform overflow-hidden rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <form action="{{ route('admin.customers.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-200 dark:border-slate-800">
                            <div class="w-10 h-10 rounded-lg bg-brand-50 dark:bg-brand-950/80 text-brand-600 dark:text-brand-400 flex items-center justify-center border border-brand-200 dark:border-brand-800/50">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Import Customer Accounts</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Upload a CSV file to bulk import customer records.</p>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">
                                Format requirement: CSV file with standard customer headers. 
                                <a href="{{ route('admin.customers.template') }}" class="text-brand-600 dark:text-brand-400 font-bold hover:underline">Download CSV Template</a>
                            </p>
                            
                            <label class="theme-label mb-1">Select CSV File</label>
                            <input type="file" name="file" accept=".csv" required class="theme-input text-xs dark:bg-slate-900 dark:text-white"/>
                        </div>
                    </div>
                    
                    <div class="p-4 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3">
                        <button type="button" @click="open = false" class="btn-secondary text-xs font-bold">Cancel</button>
                        <button type="submit" class="btn-primary text-xs font-bold">Upload & Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
