<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight flex items-center gap-2">
                        <svg class="w-6 h-6 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span>{{ $customer->company_name }}</span>
                    </h2>
                    <span class="{{ $customer->status == 'Active' ? 'badge-success' : 'badge-danger' }} text-xs font-bold">{{ $customer->status ?? 'Active' }}</span>
                    <span class="badge-neutral text-xs font-bold">{{ $customer->type }}</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Customer Profile, Fleet Vessels, Technical Contacts, and Linked Documents</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn-primary text-xs font-bold gap-1.5 shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Edit Account</span>
                </a>
                <a href="{{ route('admin.customers.index') }}" class="btn-secondary text-xs font-bold gap-1.5 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Back to Customers</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6" x-data="{ tab: 'overview' }">
        <div class="max-w-7xl mx-auto space-y-6">
            
            <!-- Tabs Header Card -->
            <div class="theme-card p-2">
                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar">
                    @foreach(['overview' => 'Overview', 'contacts' => 'Contacts', 'vessels' => 'Vessels', 'documents' => 'Documents', 'history' => 'History', 'notes' => 'Notes'] as $key => $label)
                        <button @click="tab = '{{ $key }}'" 
                            :class="tab === '{{ $key }}' ? 'bg-brand-600 text-white font-bold shadow-md shadow-brand-600/20' : 'bg-slate-100 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 hover:text-white hover:bg-slate-700/80'"
                            class="px-4 py-2 text-xs rounded-xl transition-all whitespace-nowrap">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- TAB CONTENTS CONTAINER -->
            <div class="theme-card min-h-[400px]">
                
                <!-- OVERVIEW TAB -->
                <div x-show="tab === 'overview'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2 space-y-6">
                             <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider pb-3 border-b border-slate-200 dark:border-slate-800">Company Overview</h3>
                             <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-xs font-bold text-slate-400 uppercase tracking-wider">Industry Classification</dt>
                                    <dd class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $customer->type }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-bold text-slate-400 uppercase tracking-wider">Account Status</dt>
                                    <dd class="mt-1 text-sm font-semibold">
                                         <span class="{{ $customer->status == 'Active' ? 'badge-success' : 'badge-danger' }} text-xs font-bold">
                                            {{ $customer->status }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-bold text-slate-400 uppercase tracking-wider">Corporate Email</dt>
                                    <dd class="mt-1 text-sm font-mono font-semibold text-slate-900 dark:text-white">{{ $customer->email ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-bold text-slate-400 uppercase tracking-wider">Corporate Phone</dt>
                                    <dd class="mt-1 text-sm font-mono font-semibold text-slate-900 dark:text-white">{{ $customer->phone ?? 'N/A' }}</dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-xs font-bold text-slate-400 uppercase tracking-wider">Headquarters Address</dt>
                                    <dd class="mt-1 text-sm font-medium text-slate-900 dark:text-white">{{ $customer->address ?? 'No street address specified' }}<br><span class="text-xs text-slate-500 dark:text-slate-400">{{ $customer->city ?? '-' }}, {{ $customer->country }}</span></dd>
                                </div>
                             </dl>
                        </div>
                        <div class="theme-card bg-slate-50/60 dark:bg-slate-900/60 space-y-4">
                            <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider pb-2 border-b border-slate-200 dark:border-slate-800">Linked Fleet Stats</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center text-xs py-1">
                                    <span class="text-slate-500 dark:text-slate-400 font-medium">Registered Fleet Vessels</span>
                                    <span class="font-bold font-mono text-slate-900 dark:text-white bg-slate-200 dark:bg-slate-800 px-2 py-0.5 rounded">{{ $customer->vessels->count() }}</span>
                                </div>
                                <div class="flex justify-between items-center text-xs py-1">
                                    <span class="text-slate-500 dark:text-slate-400 font-medium">Registered Contacts</span>
                                    <span class="font-bold font-mono text-slate-900 dark:text-white bg-slate-200 dark:bg-slate-800 px-2 py-0.5 rounded">{{ $customer->contacts->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CONTACTS TAB -->
                <div x-show="tab === 'contacts'" style="display: none;">
                    <div class="flex justify-between items-center pb-4 mb-4 border-b border-slate-200 dark:border-slate-800">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Contact Persons</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Designated company personnel, engineers-in-charge, and account contacts</p>
                        </div>
                        <button onclick="document.getElementById('addContactModal').classList.remove('hidden')" class="btn-primary text-xs font-bold gap-1.5 shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Add Contact</span>
                        </button>
                    </div>

                    <div class="table-container">
                        <table class="theme-table">
                            <thead>
                                <tr class="theme-table-header">
                                    <th class="px-6 py-3.5">Contact Person</th>
                                    <th class="px-6 py-3.5">Designation & Role</th>
                                    <th class="px-6 py-3.5">Contact Info</th>
                                    <th class="px-6 py-3.5">Permissions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                @forelse($customer->contacts as $contact)
                                    <tr class="theme-table-row">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-slate-900 dark:text-white text-sm flex items-center gap-2">
                                                {{ $contact->name }} 
                                                @if($contact->is_primary) 
                                                    <span class="badge-success text-[10px] font-bold">Primary Contact</span> 
                                                @endif
                                            </div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $contact->status }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-xs">
                                            <div class="font-bold text-slate-800 dark:text-slate-200">{{ $contact->contact_type }}</div>
                                            <div class="text-slate-500 dark:text-slate-400">{{ $contact->designation ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-xs font-mono">
                                            <div class="font-semibold text-slate-900 dark:text-white">{{ $contact->email }}</div>
                                            <div class="text-slate-500 dark:text-slate-400">{{ $contact->phone }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-xs">
                                            <div class="flex items-center gap-1.5">
                                                @if($contact->can_request_job)
                                                    <span class="badge-neutral text-[10px] font-bold">Job Req</span>
                                                @endif
                                                @if($contact->can_receive_invoice)
                                                    <span class="badge-neutral text-[10px] font-bold">Invoice</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500 text-xs font-medium">
                                            No contact persons added yet. Click "Add Contact" to create one.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- VESSELS TAB -->
                <div x-show="tab === 'vessels'" style="display: none;">
                    <div class="flex justify-between items-center pb-4 mb-4 border-b border-slate-200 dark:border-slate-800">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Managed Fleet Vessels</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Vessels assigned under {{ $customer->company_name }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($customer->vessels as $vessel)
                            <div class="theme-card p-4 flex flex-col justify-between space-y-3">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-bold text-base text-slate-900 dark:text-white">{{ $vessel->name }}</h4>
                                        <span class="{{ $vessel->status === 'active' ? 'badge-success' : 'badge-neutral' }} text-[10px] font-bold capitalize">{{ $vessel->status }}</span>
                                    </div>
                                    <div class="text-xs font-semibold text-slate-600 dark:text-slate-400 mt-1">{{ $vessel->type ?? 'General Vessel' }}</div>
                                    <div class="text-xs font-mono text-slate-400 dark:text-slate-500 mt-1">IMO: {{ $vessel->imo_number ?? 'N/A' }}</div>
                                </div>
                                <div class="pt-2 border-t border-slate-200 dark:border-slate-800 flex justify-end">
                                    <a href="{{ route('admin.vessels.edit', $vessel->id) }}" class="btn-secondary text-xs py-1 px-3 font-bold">Edit Vessel</a> 
                                </div>
                            </div>
                        @empty
                            <div class="col-span-3 text-center py-8 text-xs text-slate-400">No vessels linked to this customer account.</div>
                        @endforelse
                    </div>
                </div>

                <!-- DOCUMENTS TAB -->
                <div x-show="tab === 'documents'" style="display: none;">
                    <div class="flex justify-between items-center pb-4 mb-4 border-b border-slate-200 dark:border-slate-800">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Corporate Documents</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Uploaded commercial registrations, CR files, and contracts</p>
                        </div>
                        <button onclick="document.getElementById('addDocModal').classList.remove('hidden')" class="btn-primary text-xs font-bold gap-1.5 shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Upload Document</span>
                        </button>
                    </div>
                    <ul class="divide-y divide-slate-200 dark:divide-slate-800">
                         @forelse($customer->documents as $doc)
                            <li class="py-3 flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-brand-50 dark:bg-brand-950/80 text-brand-600 dark:text-brand-400 flex items-center justify-center">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-slate-900 dark:text-white">{{ $doc->title }}</div>
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400">Expires: {{ $doc->expiry_date ? $doc->expiry_date->format('d M Y') : 'N/A' }}</div>
                                    </div>
                                </div>
                                <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="btn-secondary text-xs font-bold">Download</a>
                            </li>
                         @empty
                            <li class="py-8 text-center text-xs text-slate-400">No documents uploaded yet.</li>
                         @endforelse
                    </ul>
                </div>

                <!-- NOTES TAB -->
                <div x-show="tab === 'notes'" style="display: none;">
                    <div class="mb-5">
                        <form action="{{ route('admin.customers.notes.store', $customer->id) }}" method="POST" class="space-y-3">
                            @csrf
                            <textarea name="content" rows="3" class="theme-input text-xs font-medium dark:bg-slate-900 dark:text-white" placeholder="Add an internal corporate note..."></textarea>
                            <div class="flex justify-end">
                                <button type="submit" class="btn-primary text-xs font-bold py-2 px-5">Add Note</button>
                            </div>
                        </form>
                    </div>
                    <div class="space-y-3">
                        @foreach($customer->customerNotes->sortByDesc('created_at') as $note)
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 space-y-2">
                                <p class="text-xs font-medium text-slate-800 dark:text-slate-200">{{ $note->content }}</p>
                                <div class="text-[10px] text-slate-400 flex justify-between font-mono">
                                    <span>{{ $note->author->name ?? 'Unknown' }}</span>
                                    <span>{{ $note->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- HISTORY TAB -->
                <div x-show="tab === 'history'" style="display: none;">
                    <p class="text-slate-400 dark:text-slate-500 text-xs font-medium text-center py-10">Service and Certificate history will appear here once linked.</p>
                </div>

            </div>
        </div>
    </div>

    <!-- MODALS -->
    <!-- Add Contact Modal -->
    <div id="addContactModal" class="hidden fixed inset-0 bg-slate-900/70 backdrop-blur-xs overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-6 border border-slate-200 dark:border-slate-800 w-[440px] shadow-2xl rounded-2xl bg-white dark:bg-slate-900">
            <div class="flex items-center gap-3 pb-3 mb-4 border-b border-slate-200 dark:border-slate-800">
                <div class="w-9 h-9 rounded-xl bg-brand-50 dark:bg-brand-950/80 text-brand-600 dark:text-brand-400 border border-brand-200 dark:border-brand-800/50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Add Technical Contact</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Register new contact person for {{ $customer->company_name }}</p>
                </div>
            </div>

            <form action="{{ route('admin.customers.contacts.store', $customer->id) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                         <div class="col-span-2">
                             <label class="theme-label mb-1">Full Name *</label>
                             <input type="text" name="name" placeholder="e.g. Eng. Ahmed Al-Kuwari" class="theme-input text-xs font-bold dark:bg-slate-900 dark:text-white" required>
                         </div>
                         <div>
                             <label class="theme-label mb-1">Designation</label>
                             <input type="text" name="designation" placeholder="e.g. Marine Engineer" class="theme-input text-xs font-bold dark:bg-slate-900 dark:text-white">
                         </div>
                         <div>
                             <label class="theme-label mb-1">Contact Role</label>
                             <select name="contact_type" class="theme-input text-xs font-bold dark:bg-slate-900 dark:text-white">
                                 <option value="General">General</option>
                                 <option value="Engineer">Engineer</option>
                                 <option value="Accounts">Accounts</option>
                                 <option value="Manager">Manager</option>
                                 <option value="Purchase">Purchase</option>
                             </select>
                         </div>
                         <div class="col-span-2">
                             <label class="theme-label mb-1">Email Address</label>
                             <input type="email" name="email" placeholder="ahmed@company.qa" class="theme-input text-xs font-bold dark:bg-slate-900 dark:text-white">
                         </div>
                         <div class="col-span-2">
                             <label class="theme-label mb-1">Phone Number</label>
                             <input type="text" name="phone" placeholder="+974 5500 0000" class="theme-input text-xs font-mono" >
                         </div>
                    </div>
                    
                    <div class="flex flex-col space-y-2 pt-2 border-t border-slate-200 dark:border-slate-800">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_primary" value="1" class="rounded border-slate-300 dark:border-slate-700 text-brand-600 shadow-sm focus:ring-brand-500 dark:bg-slate-900">
                            <span class="text-xs text-slate-700 dark:text-slate-300 font-bold">Primary Contact</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="can_request_job" value="1" class="rounded border-slate-300 dark:border-slate-700 text-brand-600 shadow-sm focus:ring-brand-500 dark:bg-slate-900">
                            <span class="text-xs text-slate-700 dark:text-slate-300 font-semibold">Can Request Jobs</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="can_receive_invoice" value="1" class="rounded border-slate-300 dark:border-slate-700 text-brand-600 shadow-sm focus:ring-brand-500 dark:bg-slate-900">
                            <span class="text-xs text-slate-700 dark:text-slate-300 font-semibold">Receives Invoices</span>
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('addContactModal').classList.add('hidden')" class="btn-secondary text-xs font-bold py-2 px-4">Cancel</button>
                    <button type="submit" class="btn-primary text-xs font-bold py-2 px-5 shadow-md">Save Contact</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Document Modal -->
    <div id="addDocModal" class="hidden fixed inset-0 bg-slate-900/70 backdrop-blur-xs overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-6 border border-slate-200 dark:border-slate-800 w-[420px] shadow-2xl rounded-2xl bg-white dark:bg-slate-900">
             <div class="flex items-center gap-3 pb-3 mb-4 border-b border-slate-200 dark:border-slate-800">
                <div class="w-9 h-9 rounded-xl bg-brand-50 dark:bg-brand-950/80 text-brand-600 dark:text-brand-400 border border-brand-200 dark:border-brand-800/50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Upload Document</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Add corporate file for {{ $customer->company_name }}</p>
                </div>
            </div>

             <form action="{{ route('admin.customers.documents.store', $customer->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="theme-label mb-1">Document Title *</label>
                        <input type="text" name="title" placeholder="e.g. Commercial Registration CR 2026" class="theme-input text-xs font-bold dark:bg-slate-900 dark:text-white" required>
                    </div>
                    <div>
                        <label class="theme-label mb-1">Expiry Date</label>
                        <input type="date" name="expiry_date" class="theme-input text-xs font-bold dark:bg-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="theme-label mb-1">File Attachment *</label>
                        <input type="file" name="file" class="theme-input text-xs dark:bg-slate-900 dark:text-white" required>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('addDocModal').classList.add('hidden')" class="btn-secondary text-xs font-bold py-2 px-4">Cancel</button>
                    <button type="submit" class="btn-primary text-xs font-bold py-2 px-5 shadow-md">Upload File</button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>
