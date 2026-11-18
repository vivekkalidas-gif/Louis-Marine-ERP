<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight flex items-center gap-2">
                        <svg class="w-6 h-6 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                        <span>{{ $vessel->name }}</span>
                    </h2>
                    <span class="badge-success text-xs font-bold capitalize">{{ $vessel->status ?? 'Active' }}</span>
                    @if($vessel->type)
                        <span class="badge-neutral text-xs font-bold">{{ $vessel->type }}</span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Update vessel technical specifications, IMO number, and assigned engineer-in-charge</p>
            </div>
            <a href="{{ route('admin.vessels.index') }}" class="btn-secondary text-xs font-bold gap-1.5 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Back to Fleet</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6" x-data="vesselEditForm()">
        <div class="max-w-7xl mx-auto">
            <form action="{{ route('admin.vessels.update', $vessel->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    
                    <!-- LEFT COLUMN (8 Cols): Form Sections -->
                    <div class="lg:col-span-8 space-y-6">
                        
                        <!-- SECTION 1: Ownership & Assignment -->
                        <div class="theme-card">
                            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-200 dark:border-slate-800">
                                <div class="w-9 h-9 rounded-xl bg-brand-50 dark:bg-brand-950/80 text-brand-600 dark:text-brand-400 border border-brand-200 dark:border-brand-800/50 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">1. Customer & Technical Contact Assignment</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Select parent customer company and designated engineer-in-charge</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="theme-label mb-0">Customer Company *</label>
                                        <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/50 px-1.5 py-0.5 rounded">Required</span>
                                    </div>
                                    <select name="customer_id" x-model="customer_id" class="theme-input text-xs font-bold dark:bg-slate-900 dark:text-white" required>
                                        <option value="">Select Customer Company...</option>
                                        <template x-for="c in customers" :key="c.id">
                                            <option :value="c.id" x-text="c.company_name" :selected="c.id == customer_id"></option>
                                        </template>
                                    </select>
                                </div>

                                <div>
                                    <label class="theme-label mb-1.5">Assigned Contact (Engineer-in-Charge)</label>
                                    <select name="company_contact_id" x-model="company_contact_id" class="theme-input text-xs font-bold dark:bg-slate-900 dark:text-white" :disabled="!customer_id">
                                        <option value="">Select Technical Contact...</option>
                                        <template x-for="contact in filteredContacts" :key="contact.id">
                                            <option :value="contact.id" x-text="contact.name + ' (' + (contact.designation || 'Staff') + ')'" :selected="contact.id == company_contact_id"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 2: Technical Specifications -->
                        <div class="theme-card">
                            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-200 dark:border-slate-800">
                                <div class="w-9 h-9 rounded-xl bg-sky-50 dark:bg-sky-950/80 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-800/50 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">2. Vessel Identification & Registry</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Vessel title, maritime IMO number, and vessel category</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="theme-label mb-0">Vessel Name *</label>
                                        <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/50 px-1.5 py-0.5 rounded">Required</span>
                                    </div>
                                    <input type="text" name="name" value="{{ old('name', $vessel->name) }}" class="theme-input text-xs font-bold" required placeholder="e.g. MV Al Rayyan">
                                </div>

                                <div>
                                    <label class="theme-label mb-1.5">IMO Registration Number</label>
                                    <input type="text" name="imo_number" value="{{ old('imo_number', $vessel->imo_number) }}" class="theme-input text-xs font-mono uppercase" placeholder="e.g. IMO9876543">
                                </div>

                                <div>
                                    <label class="theme-label mb-1.5">Vessel Classification / Type</label>
                                    <div class="flex gap-2">
                                        <select name="type" x-model="type" class="theme-input text-xs font-bold dark:bg-slate-900 dark:text-white flex-1">
                                            <option value="">Select Type...</option>
                                            <template x-for="t in vesselTypes" :key="t">
                                                <option :value="t" x-text="t" :selected="type === t"></option>
                                            </template>
                                        </select>
                                        <button type="button" @click="addNewType()" class="btn-secondary text-xs px-3 font-bold" title="Add New Vessel Type">+</button>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="theme-label mb-0">Fleet Operational Status *</label>
                                        <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/50 px-1.5 py-0.5 rounded">Required</span>
                                    </div>
                                    <select name="status" class="theme-input text-xs font-bold dark:bg-slate-900 dark:text-white">
                                        <option value="active" {{ old('status', $vessel->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $vessel->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="maintenance" {{ old('status', $vessel->status) == 'maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- RIGHT COLUMN (4 Cols Sticky Panel): Actions & Audit -->
                    <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">
                        
                        <!-- Actions Card -->
                        <div class="theme-card space-y-4">
                            <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider pb-3 border-b border-slate-200 dark:border-slate-800 flex items-center gap-2">
                                <svg class="w-4 h-4 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                <span>Save & Actions</span>
                            </h3>

                            <button type="submit" class="btn-primary w-full text-xs py-2.5 font-bold gap-2 shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Save & Update Vessel</span>
                            </button>

                            <a href="{{ route('admin.vessels.index') }}" class="btn-secondary w-full text-xs py-2 font-bold gap-1.5 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Cancel</span>
                            </a>
                        </div>

                        <!-- Audit Card -->
                        <div class="theme-card space-y-3 bg-slate-50/50 dark:bg-slate-900/40">
                            <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider pb-2 border-b border-slate-200 dark:border-slate-800">
                                Vessel Record Audit
                            </h4>

                            <div class="flex items-center justify-between text-xs py-1">
                                <span class="text-slate-500 dark:text-slate-400">Vessel ID</span>
                                <span class="font-mono font-bold text-slate-900 dark:text-white">#{{ $vessel->id }}</span>
                            </div>

                            <div class="flex items-center justify-between text-xs py-1">
                                <span class="text-slate-500 dark:text-slate-400">Registered Date</span>
                                <span class="font-semibold text-slate-900 dark:text-white">{{ $vessel->created_at ? \Carbon\Carbon::parse($vessel->created_at)->format('d M, Y') : 'N/A' }}</span>
                            </div>
                        </div>

                        @can('delete', $vessel)
                        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800/80 space-y-3">
                            <h4 class="text-xs font-bold text-rose-900 dark:text-rose-200 uppercase tracking-wider">Danger Zone</h4>
                            <p class="text-[11px] text-slate-600 dark:text-slate-300">Permanently delete this vessel from the fleet registry.</p>
                            <form action="{{ route('admin.vessels.destroy', $vessel->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this vessel?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger w-full text-xs font-bold py-2 gap-1.5 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span>Delete Vessel</span>
                                </button>
                            </form>
                        </div>
                        @endcan

                    </div>

                </div>
            </form>
        </div>
    </div>

    <script>
        function vesselEditForm() {
            return {
                customer_id: '{{ old('customer_id', $vessel->customer_id) }}',
                company_contact_id: '{{ old('company_contact_id', $vessel->company_contact_id) }}',
                customers: @json($customers),
                vesselTypes: @json($vesselTypes ?? []),
                type: '{{ old('type', $vessel->type) }}',
                get filteredContacts() {
                    if (!this.customer_id) return [];
                    const customer = this.customers.find(c => c.id == this.customer_id);
                    return customer ? customer.contacts : [];
                },
                addNewType() {
                    const newType = prompt("Enter new vessel type:");
                    if (newType && newType.trim() !== "") {
                        const formatted = newType.trim();
                        if (!this.vesselTypes.includes(formatted)) {
                            this.vesselTypes.push(formatted);
                            this.vesselTypes.sort();
                        }
                        this.type = formatted;
                    }
                }
            }
        }
    </script>
</x-app-layout>
