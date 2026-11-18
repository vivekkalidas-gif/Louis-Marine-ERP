<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight flex items-center gap-2">
                        <svg class="w-6 h-6 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span>{{ isset($customer) ? $customer->company_name : 'New Customer Account' }}</span>
                    </h2>
                    @if(isset($customer))
                        <span class="badge-success text-xs font-bold">{{ $customer->status ?? 'Active' }}</span>
                        <span class="badge-neutral text-xs font-bold">{{ $customer->type }}</span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manage customer account details, commercial tax numbers, and primary location</p>
            </div>
            <a href="{{ route('admin.customers.index') }}" class="btn-secondary text-xs font-bold gap-1.5 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Back to Customers</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            <form method="POST" action="{{ isset($customer) ? route('admin.customers.update', $customer->id) : route('admin.customers.store') }}">
                @csrf
                @if(isset($customer))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    
                    <!-- LEFT COLUMN (8 Cols): Form Sections -->
                    <div class="lg:col-span-8 space-y-6">
                        
                        <!-- SECTION 1: Company Info -->
                        <div class="theme-card">
                            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-200 dark:border-slate-800">
                                <div class="w-9 h-9 rounded-xl bg-brand-50 dark:bg-brand-950/80 text-brand-600 dark:text-brand-400 border border-brand-200 dark:border-brand-800/50 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">1. Company Identity & Contact Info</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Primary business name, account classification, and corporate contact</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="theme-label mb-0">Company Name *</label>
                                        <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/50 px-1.5 py-0.5 rounded">Required</span>
                                    </div>
                                    <input type="text" name="company_name" class="theme-input text-xs font-bold" value="{{ old('company_name', $customer->company_name ?? '') }}" required placeholder="e.g. Qatar Shipping Lines WLL">
                                    <x-input-error :messages="$errors->get('company_name')" class="mt-1" />
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="theme-label mb-0">Customer Type *</label>
                                        <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/50 px-1.5 py-0.5 rounded">Required</span>
                                    </div>
                                    <select name="type" class="theme-input text-xs font-bold dark:bg-slate-900 dark:text-white">
                                        @foreach(['Operator', 'Owner', 'Agent', 'Manager'] as $type)
                                            <option value="{{ $type }}" {{ old('type', $customer->type ?? '') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="theme-label mb-1.5">Corporate Email</label>
                                    <input type="email" name="email" class="theme-input text-xs font-bold" value="{{ old('email', $customer->email ?? '') }}" placeholder="info@company.com">
                                </div>

                                <div>
                                    <label class="theme-label mb-1.5">Corporate Phone</label>
                                    <input type="text" name="phone" class="theme-input text-xs font-mono" value="{{ old('phone', $customer->phone ?? '') }}" placeholder="+974 4400 0000">
                                </div>

                                <div>
                                    <label class="theme-label mb-1.5">Account Status</label>
                                    <select name="status" class="theme-input text-xs font-bold dark:bg-slate-900 dark:text-white">
                                        <option value="Active" {{ old('status', $customer->status ?? '') == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ old('status', $customer->status ?? '') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 2: Commercial & Tax -->
                        <div class="theme-card">
                            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-200 dark:border-slate-800">
                                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/80 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">2. Commercial & Tax Registration</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Commercial Registration (CR) and Tax Registration Number (VAT)</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="theme-label mb-1.5">Commercial Registration (CR) Number</label>
                                    <input type="text" name="cr_number" class="theme-input text-xs font-mono uppercase" value="{{ old('cr_number', $customer->cr_number ?? '') }}" placeholder="CR-123456">
                                </div>

                                <div>
                                    <label class="theme-label mb-1.5">VAT / Tax Identification Number</label>
                                    <input type="text" name="vat_number" class="theme-input text-xs font-mono uppercase" value="{{ old('vat_number', $customer->vat_number ?? '') }}" placeholder="TAX-987654">
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 3: Location -->
                        <div class="theme-card">
                            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-200 dark:border-slate-800">
                                <div class="w-9 h-9 rounded-xl bg-sky-50 dark:bg-sky-950/80 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-800/50 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">3. Address & Corporate Location</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Headquarters street address, city, and country</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div class="md:col-span-2">
                                    <label class="theme-label mb-1.5">Street Address</label>
                                    <input type="text" name="address" class="theme-input text-xs" value="{{ old('address', $customer->address ?? '') }}" placeholder="Building 45, Street 890, West Bay">
                                </div>

                                <div>
                                    <label class="theme-label mb-1.5">City</label>
                                    <input type="text" name="city" class="theme-input text-xs" value="{{ old('city', $customer->city ?? '') }}" placeholder="Doha">
                                </div>

                                <div>
                                    <label class="theme-label mb-1.5">Country</label>
                                    <input type="text" name="country" class="theme-input text-xs" value="{{ old('country', $customer->country ?? 'Qatar') }}" placeholder="Qatar">
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- RIGHT COLUMN (4 Cols Sticky Panel): Actions & Summary -->
                    <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">
                        
                        <!-- Actions Card -->
                        <div class="theme-card space-y-4">
                            <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider pb-3 border-b border-slate-200 dark:border-slate-800 flex items-center gap-2">
                                <svg class="w-4 h-4 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                <span>Save & Actions</span>
                            </h3>

                            <button type="submit" class="btn-primary w-full text-xs py-2.5 font-bold gap-2 shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>{{ isset($customer) ? 'Save & Update Account' : 'Create Customer Account' }}</span>
                            </button>

                            <a href="{{ route('admin.customers.index') }}" class="btn-secondary w-full text-xs py-2 font-bold gap-1.5 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Cancel</span>
                            </a>
                        </div>

                        <!-- Audit Metadata Card (if editing) -->
                        @if(isset($customer))
                        <div class="theme-card space-y-3 bg-slate-50/50 dark:bg-slate-900/40">
                            <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider pb-2 border-b border-slate-200 dark:border-slate-800">
                                Account Audit Info
                            </h4>

                            <div class="flex items-center justify-between text-xs py-1">
                                <span class="text-slate-500 dark:text-slate-400">Account ID</span>
                                <span class="font-mono font-bold text-slate-900 dark:text-white">#{{ $customer->id }}</span>
                            </div>

                            <div class="flex items-center justify-between text-xs py-1">
                                <span class="text-slate-500 dark:text-slate-400">Created Date</span>
                                <span class="font-semibold text-slate-900 dark:text-white">{{ $customer->created_at ? \Carbon\Carbon::parse($customer->created_at)->format('d M, Y') : 'N/A' }}</span>
                            </div>
                        </div>

                        @can('delete', $customer)
                        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800/80 space-y-3">
                            <h4 class="text-xs font-bold text-rose-900 dark:text-rose-200 uppercase tracking-wider">Danger Zone</h4>
                            <p class="text-[11px] text-slate-600 dark:text-slate-300">Permanently delete this customer and disassociate linked records.</p>
                            <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this customer?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger w-full text-xs font-bold py-2 gap-1.5 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span>Delete Customer</span>
                                </button>
                            </form>
                        </div>
                        @endcan
                        @endif

                    </div>

                </div>
            </form>
        </div>
    </div>
</x-app-layout>
