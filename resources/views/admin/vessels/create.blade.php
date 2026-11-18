<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight flex items-center gap-2">
                    <svg class="w-6 h-6 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Add New Fleet Vessel</span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Register a new marine vessel, IMO registration number, and parent customer association</p>
            </div>
            <a href="{{ route('admin.vessels.index') }}" class="btn-secondary text-xs font-bold gap-1.5 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Back to Fleet</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6" x-data="vesselForm()">
        <div class="max-w-7xl mx-auto">
            <form action="{{ route('admin.vessels.store') }}" method="POST">
                @csrf

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
                                            <option :value="c.id" x-text="c.company_name"></option>
                                        </template>
                                    </select>
                                </div>

                                <div>
                                    <label class="theme-label mb-1.5">Assigned Contact (Engineer-in-Charge)</label>
                                    <select name="company_contact_id" class="theme-input text-xs font-bold dark:bg-slate-900 dark:text-white" :disabled="!customer_id">
                                        <option value="">Select Technical Contact...</option>
                                        <template x-for="contact in filteredContacts" :key="contact.id">
                                            <option :value="contact.id" x-text="contact.name + ' (' + (contact.designation || 'Staff') + ')'"></option>
                                        </template>
                                    </select>
                                    <p x-show="!customer_id" class="text-[11px] text-slate-400 mt-1">Select a customer company first.</p>
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
                                    <input type="text" name="name" class="theme-input text-xs font-bold" required placeholder="e.g. MV Al Rayyan">
                                </div>

                                <div>
                                    <label class="theme-label mb-1.5">IMO Registration Number</label>
                                    <input type="text" name="imo_number" class="theme-input text-xs font-mono uppercase" placeholder="e.g. IMO9876543">
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
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="maintenance">Under Maintenance</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- RIGHT COLUMN (4 Cols Sticky Panel): Actions & Information -->
                    <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">
                        
                        <!-- Actions Card -->
                        <div class="theme-card space-y-4">
                            <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider pb-3 border-b border-slate-200 dark:border-slate-800 flex items-center gap-2">
                                <svg class="w-4 h-4 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                <span>Publish New Vessel</span>
                            </h3>

                            <button type="submit" class="btn-primary w-full text-xs py-2.5 font-bold gap-2 shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Save & Register Vessel</span>
                            </button>

                            <a href="{{ route('admin.vessels.index') }}" class="btn-secondary w-full text-xs py-2 font-bold gap-1.5 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Cancel</span>
                            </a>
                        </div>

                        <!-- Guidance Card -->
                        <div class="theme-card space-y-3 bg-slate-50/50 dark:bg-slate-900/40">
                            <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider pb-2 border-b border-slate-200 dark:border-slate-800 flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Vessel Registry Guide</span>
                            </h4>

                            <ul class="text-xs text-slate-500 dark:text-slate-400 space-y-2">
                                <li class="flex items-start gap-2">
                                    <span class="text-brand-500 font-bold">•</span>
                                    <span><strong>IMO Number:</strong> Official IMO 7-digit identifier required for international certificates.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-brand-500 font-bold">•</span>
                                    <span><strong>Assigned Contact:</strong> Primary contact receiving service dispatch alerts & certificates.</span>
                                </li>
                            </ul>
                        </div>

                    </div>

                </div>
            </form>
        </div>
    </div>

    <script>
        function vesselForm() {
            return {
                customer_id: '',
                customers: @json($customers),
                vesselTypes: @json($vesselTypes ?? []),
                type: '',
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
