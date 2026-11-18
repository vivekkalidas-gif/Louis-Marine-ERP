<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight flex items-center gap-2">
                    <svg class="w-6 h-6 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>System Settings & Branding</span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manage company details, branding assets, numbering sequences, and security preferences</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6" x-data="{ activeTab: 'company' }">
        
        @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/80 text-emerald-800 dark:text-emerald-200 text-xs font-semibold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800/80 text-rose-800 dark:text-rose-200 text-xs font-semibold space-y-1">
            <div class="flex items-center gap-2 font-bold text-rose-900 dark:text-rose-100">
                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Unable to update settings:</span>
            </div>
            <ul class="list-disc list-inside pl-6 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- High-Density SaaS Tab Navigation Bar -->
        <div class="p-1.5 rounded-2xl bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center gap-1 overflow-x-auto">
            <button @click="activeTab = 'company'" 
                    :class="activeTab === 'company' ? 'bg-brand-600 text-white shadow-md font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'" 
                    class="px-4 py-2 text-xs rounded-xl transition whitespace-nowrap flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>Company Profile</span>
            </button>

            <button @click="activeTab = 'branding'" 
                    :class="activeTab === 'branding' ? 'bg-brand-600 text-white shadow-md font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'" 
                    class="px-4 py-2 text-xs rounded-xl transition whitespace-nowrap flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                <span>Branding & Theme</span>
            </button>

            <button @click="activeTab = 'email'" 
                    :class="activeTab === 'email' ? 'bg-brand-600 text-white shadow-md font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'" 
                    class="px-4 py-2 text-xs rounded-xl transition whitespace-nowrap flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span>Email & Notifications</span>
            </button>

            <button @click="activeTab = 'numbering'" 
                    :class="activeTab === 'numbering' ? 'bg-brand-600 text-white shadow-md font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'" 
                    class="px-4 py-2 text-xs rounded-xl transition whitespace-nowrap flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                <span>Numbering Format</span>
            </button>

            <button @click="activeTab = 'security'" 
                    :class="activeTab === 'security' ? 'bg-brand-600 text-white shadow-md font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'" 
                    class="px-4 py-2 text-xs rounded-xl transition whitespace-nowrap flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span>Security & System</span>
            </button>
        </div>

        <!-- Main Settings Form -->
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Company Profile Tab -->
            <div x-show="activeTab === 'company'" class="theme-card space-y-6">
                <div class="pb-3 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Company Information</h3>
                    <span class="text-xs text-slate-500">Corporate details on invoices & certificates</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="theme-label mb-1.5">Company Name</label>
                        <input type="text" name="company_name" value="{{ $settings->get('company')?->where('key', 'company_name')->first()->value ?? 'Louis Marine Qatar' }}" class="theme-input text-xs font-bold">
                    </div>

                    <div>
                        <label class="theme-label mb-1.5">Company Email</label>
                        <input type="email" name="company_email" value="{{ $settings->get('company')?->where('key', 'company_email')->first()->value ?? 'info@safetylinkmarine.com' }}" class="theme-input text-xs font-bold">
                    </div>

                    <div>
                        <label class="theme-label mb-1.5">Phone Number</label>
                        <input type="text" name="company_phone" value="{{ $settings->get('company')?->where('key', 'company_phone')->first()->value ?? '' }}" class="theme-input text-xs font-mono">
                    </div>

                    <div>
                        <label class="theme-label mb-1.5">Official Website URL</label>
                        <input type="url" name="company_website" value="{{ $settings->get('company')?->where('key', 'company_website')->first()->value ?? '' }}" class="theme-input text-xs font-mono" placeholder="https://">
                    </div>

                    <div class="md:col-span-2">
                        <label class="theme-label mb-1.5">Corporate Address</label>
                        <textarea name="company_address" rows="3" class="theme-input text-xs">{{ $settings->get('company')?->where('key', 'company_address')->first()->value ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Branding Tab -->
            <div x-show="activeTab === 'branding'" class="theme-card space-y-6" style="display: none;">
                <div class="pb-3 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Branding & Theme Customization</h3>
                    <span class="text-xs text-slate-500">Logos, favicons & A4 document layouts</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="theme-label mb-1.5">Company Main Logo</label>
                        <input type="file" name="company_logo" class="theme-input text-xs py-2">
                        @if($logo = $settings->get('branding')?->where('key', 'company_logo')->first())
                            <div class="mt-2 text-xs text-slate-500 flex items-center gap-2 p-2 bg-slate-50 dark:bg-slate-800/80 rounded-lg border border-slate-200 dark:border-slate-700">
                                <span>Current:</span>
                                <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($logo->value) }}" target="_blank" class="text-brand-600 dark:text-brand-400 font-bold hover:underline">Preview Main Logo</a>
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="theme-label mb-1.5">Browser Favicon</label>
                        <input type="file" name="site_favicon" class="theme-input text-xs py-2">
                    </div>

                    <div>
                        <label class="theme-label mb-1.5">Primary Theme Accent Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="branding_primary_color" class="h-9 w-14 rounded-lg cursor-pointer border border-slate-300 dark:border-slate-700" value="{{ $settings->get('branding')?->where('key', 'branding_primary_color')->first()->value ?? '#1e3a8a' }}">
                            <input type="text" class="theme-input text-xs font-mono w-full" value="{{ $settings->get('branding')?->where('key', 'branding_primary_color')->first()->value ?? '#1e3a8a' }}" readonly>
                        </div>
                    </div>

                    <div>
                        <label class="theme-label mb-1.5">Logo Height (px)</label>
                        <input type="number" name="branding_logo_height" value="{{ $settings->get('branding')?->where('key', 'branding_logo_height')->first()->value ?? '40' }}" min="20" max="100" class="theme-input text-xs font-mono">
                        <p class="text-[11px] text-slate-500 mt-1">Default 40px height for optimal header navigation proportion.</p>
                    </div>

                    @if(config('app.certificate_only')) @include('admin.settings.partials.cert-theme-picker') @endif

                    <!-- Certificate A4 Header & Footer Overlay Sub-Card -->
                    <div class="md:col-span-2 p-4 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 space-y-4 mt-2">
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200 flex items-center gap-2">
                                <svg class="w-4 h-4 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Certificate A4 Custom PNG Overlays
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Upload pre-designed A4 header & footer PNG images to overlay onto print certificates.</p>
                        </div>

                        @php
                            $certHeaderObj = \App\Models\Setting::where('key', 'certificate_header_png')->first();
                            $certFooterObj = \App\Models\Setting::where('key', 'certificate_footer_png')->first();
                        @endphp

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="theme-label mb-1.5">Certificate Header PNG</label>
                                <input type="file" name="certificate_header_png" accept="image/*,.png,.jpg,.jpeg,.webp" class="theme-input text-xs py-2">
                                @error('certificate_header_png')
                                    <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                                @enderror
                                @if($certHeaderObj && !empty($certHeaderObj->value))
                                    <div class="mt-2.5 text-xs text-slate-500 bg-white dark:bg-slate-800 p-3 rounded-lg border border-slate-200 dark:border-slate-700 space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Header Overlay Active
                                            </span>
                                            <label class="inline-flex items-center text-xs text-rose-500 font-bold cursor-pointer gap-1 hover:underline">
                                                <input type="checkbox" name="remove_certificate_header_png" value="1" class="rounded text-rose-600">
                                                <span>Remove</span>
                                            </label>
                                        </div>
                                        <div class="p-2 bg-slate-100 dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-700 text-center">
                                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($certHeaderObj->value) }}" alt="Header Overlay" class="max-h-24 w-full object-contain mx-auto">
                                            <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($certHeaderObj->value) }}" target="_blank" class="text-[11px] text-brand-600 dark:text-brand-400 font-bold hover:underline mt-1.5 inline-block">View Full Image ↗</a>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div>
                                <label class="theme-label mb-1.5">Certificate Footer PNG</label>
                                <input type="file" name="certificate_footer_png" accept="image/*,.png,.jpg,.jpeg,.webp" class="theme-input text-xs py-2">
                                @error('certificate_footer_png')
                                    <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                                @enderror
                                @if($certFooterObj && !empty($certFooterObj->value))
                                    <div class="mt-2.5 text-xs text-slate-500 bg-white dark:bg-slate-800 p-3 rounded-lg border border-slate-200 dark:border-slate-700 space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Footer Overlay Active
                                            </span>
                                            <label class="inline-flex items-center text-xs text-rose-500 font-bold cursor-pointer gap-1 hover:underline">
                                                <input type="checkbox" name="remove_certificate_footer_png" value="1" class="rounded text-rose-600">
                                                <span>Remove</span>
                                            </label>
                                        </div>
                                        <div class="p-2 bg-slate-100 dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-700 text-center">
                                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($certFooterObj->value) }}" alt="Footer Overlay" class="max-h-24 w-full object-contain mx-auto">
                                            <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($certFooterObj->value) }}" target="_blank" class="text-[11px] text-brand-600 dark:text-brand-400 font-bold hover:underline mt-1.5 inline-block">View Full Image ↗</a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Email Tab -->
            <div x-show="activeTab === 'email'" class="theme-card space-y-6" style="display: none;">
                <div class="pb-3 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Email & Notifications</h3>
                    <span class="text-xs text-slate-500">Sender details and automated email alerts</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="theme-label mb-1.5">Sender Name</label>
                        <input type="text" name="mail_from_name" value="{{ $settings->get('email')?->where('key', 'mail_from_name')->first()->value ?? config('app.name') }}" class="theme-input text-xs font-bold">
                    </div>

                    <div>
                        <label class="theme-label mb-1.5">Sender Email Address</label>
                        <input type="email" name="mail_from_address" value="{{ $settings->get('email')?->where('key', 'mail_from_address')->first()->value ?? '' }}" class="theme-input text-xs font-bold">
                    </div>

                    <div class="md:col-span-2 pt-2">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="hidden" name="mail_notify_expiry" value="0">
                            <input type="checkbox" name="mail_notify_expiry" value="1" class="rounded text-brand-600 focus:ring-brand-500" {{ ($settings->get('email')?->where('key', 'mail_notify_expiry')->first()->value ?? '0') == '1' ? 'checked' : '' }}>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Enable Automated Certificate Expiry Email Alerts</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Numbering Tab -->
            <div x-show="activeTab === 'numbering'" class="theme-card space-y-6" style="display: none;">
                <div class="pb-3 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Document Code & Numbering Formats</h3>
                    <span class="text-xs text-slate-500">Auto-increment document prefixes</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5">
                    <div>
                        <label class="theme-label mb-1.5">Invoice Prefix</label>
                        <input type="text" name="prefix_invoice" value="{{ $settings->get('numbering')?->where('key', 'prefix_invoice')->first()->value ?? 'INV' }}" class="theme-input text-xs font-mono uppercase font-bold">
                    </div>

                    <div>
                        <label class="theme-label mb-1.5">Quotation Prefix</label>
                        <input type="text" name="prefix_quotation" value="{{ $settings->get('numbering')?->where('key', 'prefix_quotation')->first()->value ?? 'QTE' }}" class="theme-input text-xs font-mono uppercase font-bold">
                    </div>

                    <div>
                        <label class="theme-label mb-1.5">Job Prefix</label>
                        <input type="text" name="prefix_job" value="{{ $settings->get('numbering')?->where('key', 'prefix_job')->first()->value ?? 'JOB' }}" class="theme-input text-xs font-mono uppercase font-bold">
                    </div>

                    <div>
                        <label class="theme-label mb-1.5">Certificate Prefix</label>
                        <input type="text" name="prefix_certificate" value="{{ $settings->get('numbering')?->where('key', 'prefix_certificate')->first()->value ?? 'CERT' }}" class="theme-input text-xs font-mono uppercase font-bold">
                    </div>

                    <div>
                        <label class="theme-label mb-1.5">Dispatch Note Prefix</label>
                        <input type="text" name="prefix_dispatch" value="{{ $settings->get('numbering')?->where('key', 'prefix_dispatch')->first()->value ?? 'DN' }}" class="theme-input text-xs font-mono uppercase font-bold">
                    </div>

                    <div>
                        <label class="theme-label mb-1.5">Purchase Order Prefix</label>
                        <input type="text" name="prefix_purchase_order" value="{{ $settings->get('numbering')?->where('key', 'prefix_purchase_order')->first()->value ?? 'PO' }}" class="theme-input text-xs font-mono uppercase font-bold">
                    </div>

                    <div>
                        <label class="theme-label mb-1.5">SKU / Item Prefix</label>
                        <input type="text" name="prefix_inventory" value="{{ $settings->get('numbering')?->where('key', 'prefix_inventory')->first()->value ?? 'INV' }}" class="theme-input text-xs font-mono uppercase font-bold">
                    </div>
                </div>
            </div>

            <!-- Security Tab -->
            <div x-show="activeTab === 'security'" class="theme-card space-y-6" style="display: none;">
                <div class="pb-3 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">System Security & Maintenance</h3>
                    <span class="text-xs text-slate-500">Session limits and system access modes</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="theme-label mb-1.5">Session Timeout (Minutes)</label>
                        <input type="number" name="security_session_lifetime" value="{{ $settings->get('security')?->where('key', 'security_session_lifetime')->first()->value ?? '120' }}" class="theme-input text-xs font-mono">
                    </div>

                    <div class="pt-2">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="hidden" name="security_maintenance_mode" value="0">
                            <input type="checkbox" name="security_maintenance_mode" value="1" class="rounded text-rose-600 focus:ring-rose-500" {{ ($settings->get('security')?->where('key', 'security_maintenance_mode')->first()->value ?? '0') == '1' ? 'checked' : '' }}>
                            <span class="text-xs font-bold text-rose-600 dark:text-rose-400">Enable Maintenance Mode</span>
                        </label>
                        <p class="text-[11px] text-slate-500 mt-1">When enabled, only administrators will be able to log into the ERP system.</p>
                    </div>
                </div>
            </div>

            <!-- Fixed Bottom Action Bar -->
            <div class="mt-6 flex items-center justify-end gap-3 p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-lg">
                <button type="submit" class="btn-primary text-xs font-bold gap-2 px-6 py-2.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Save All Changes</span>
                </button>
            </div>

        </form>
    </div>
</x-app-layout>
