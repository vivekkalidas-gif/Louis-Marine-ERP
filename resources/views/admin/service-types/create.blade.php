<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight">
                    {{ __('Create New Service Type') }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Define a new service activity type for marine inspection job orders and certificates</p>
            </div>
            
            @php
                $routePrefix = request()->routeIs('engineer.*') ? 'engineer' : 'admin';
            @endphp
            <a href="{{ route($routePrefix . '.service-types.index') }}" class="btn-ghost text-xs gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Back to Service Types</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            @php
                $routePrefix = request()->routeIs('engineer.*') ? 'engineer' : 'admin';
            @endphp
            <form method="POST" action="{{ route($routePrefix . '.service-types.store') }}">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    
                    <!-- LEFT COLUMN (70%): Form Sections -->
                    <div class="lg:col-span-8 space-y-6">
                        
                        <!-- SECTION 1: General Information -->
                        <div class="theme-card">
                            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-200 dark:border-slate-700/80">
                                <div class="w-9 h-9 rounded-xl bg-brand-50 dark:bg-brand-950/60 text-brand-600 dark:text-brand-400 border border-brand-200 dark:border-brand-800/50 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white">1. General Service Identification</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Set primary service title and unique identification code</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="theme-label mb-0">Service Title</label>
                                        <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/50 px-1.5 py-0.5 rounded">Required</span>
                                    </div>
                                    <input type="text" name="name" value="{{ old('name') }}" class="theme-input text-xs py-2.5 font-bold" required placeholder="e.g. Visual Inspection, Hydrotest">
                                    @error('name')
                                        <p class="text-[11px] text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="theme-label mb-0">Unique Service Code</label>
                                        <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/50 px-1.5 py-0.5 rounded">Required</span>
                                    </div>
                                    <input type="text" name="code" value="{{ old('code') }}" class="theme-input text-xs py-2.5 font-mono uppercase font-bold tracking-wider" required placeholder="e.g. VISUAL, REFILL">
                                    @error('code')
                                        <p class="text-[11px] text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 2: Pricing & Quantities -->
                        <div class="theme-card">
                            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-200 dark:border-slate-700/80">
                                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white">2. Billing & Commercial Defaults</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Configure default quantity units and base billing price in QAR</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="theme-label">Default Quantity</label>
                                    <input type="number" name="quantity" value="{{ old('quantity', 1) }}" class="theme-input text-xs py-2.5" min="1" placeholder="1">
                                </div>

                                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager') || auth()->user()->hasRole('accountant'))
                                    <div>
                                        <div class="flex items-center justify-between mb-1.5">
                                            <label class="theme-label mb-0">Default Price (QAR)</label>
                                            <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/50 px-1.5 py-0.5 rounded">Required</span>
                                        </div>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-xs font-bold text-slate-400">QAR</div>
                                            <input type="number" name="default_price" value="{{ old('default_price') }}" step="0.01" class="theme-input text-xs py-2.5 pl-14 font-mono font-bold" required placeholder="0.00">
                                        </div>
                                    </div>
                                @else
                                    <input type="hidden" name="default_price" value="0">
                                @endif
                            </div>
                        </div>

                        <!-- SECTION 3: Description & Scope -->
                        <div class="theme-card">
                            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-200 dark:border-slate-700/80">
                                <div class="w-9 h-9 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-800/50 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white">3. Service Scope & Field Guidelines</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Provide detailed instructions for field engineers executing this service</p>
                                </div>
                            </div>

                            <div>
                                <label class="theme-label">Detailed Scope & Description</label>
                                <textarea name="description" rows="4" class="theme-input text-xs" placeholder="Specify inspection protocols, testing standards, equipment requirements...">{{ old('description') }}</textarea>
                            </div>
                        </div>

                    </div>

                    <!-- RIGHT COLUMN (30% Sticky Panel): Actions & Information -->
                    <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">
                        
                        <!-- Primary Action Panel Card -->
                        <div class="theme-card space-y-4">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white pb-3 border-b border-slate-200 dark:border-slate-700/80 flex items-center gap-2">
                                <svg class="w-4 h-4 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                <span>Publish New Service</span>
                            </h3>

                            <button type="submit" class="btn-primary w-full text-center text-xs py-3 font-bold gap-2 shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Create & Save Service Type</span>
                            </button>

                            <a href="{{ route($routePrefix . '.service-types.index') }}" class="btn-secondary w-full text-center text-xs py-2.5 font-bold gap-1.5 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Cancel</span>
                            </a>
                        </div>

                        <!-- Info Guidance Card -->
                        <div class="theme-card space-y-3 bg-slate-50/50 dark:bg-slate-900/40">
                            <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider pb-2 border-b border-slate-200 dark:border-slate-700/80 flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Service Creation Guide</span>
                            </h4>

                            <ul class="text-xs text-slate-500 dark:text-slate-400 space-y-2">
                                <li class="flex items-start gap-2">
                                    <span class="text-brand-500 font-bold">•</span>
                                    <span><strong>Service Code:</strong> Must be unique (e.g. VISUAL, REFILL). Used on Certificates & Invoices.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-brand-500 font-bold">•</span>
                                    <span><strong>Pricing:</strong> Default price will populate work orders & field service reports.</span>
                                </li>
                            </ul>
                        </div>

                    </div>

                </div>
            </form>
        </div>
    </div>
</x-app-layout>
