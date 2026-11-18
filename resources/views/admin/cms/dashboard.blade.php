<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-900 dark:text-white leading-tight tracking-tight">
            {{ __('Website Content Management Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-6">
            
            <!-- KPI Stats Summary Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="stat-card flex items-center justify-between border-l-4 border-l-brand-500">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Website Products</p>
                        <p class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $stats['total_products'] }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-brand-50 dark:bg-brand-950/60 text-brand-600 dark:text-brand-400 border border-brand-200 dark:border-brand-800/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                </div>

                <div class="stat-card flex items-center justify-between border-l-4 border-l-emerald-500">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Active Listings</p>
                        <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $stats['active_products'] }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>

                <div class="stat-card flex items-center justify-between border-l-4 border-l-purple-500">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Services Offered</p>
                        <p class="text-2xl font-black text-purple-600 dark:text-purple-400 mt-1">{{ $stats['services'] }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-800/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                </div>

                <div class="stat-card flex items-center justify-between border-l-4 border-l-rose-500">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">New Enquiries</p>
                        <p class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ $stats['enquiries'] }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Content Actions (2 Columns) -->
                <div class="lg:col-span-2 theme-card space-y-4">
                    <div class="pb-3 border-b border-slate-200 dark:border-slate-700/80">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Content Management Modules</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Quick access to public web pages, service catalogs, and product listings</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('admin.cms.services.index') }}" class="p-4 rounded-xl border border-slate-200 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-900/40 hover:bg-white dark:hover:bg-slate-800 hover:border-brand-500/50 transition duration-150 flex flex-col justify-between group shadow-xs">
                            <div>
                                <span class="font-bold text-slate-900 dark:text-white block group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors">Manage Services</span>
                                <span class="text-xs text-slate-500 dark:text-slate-400 mt-1 block">Edit service listings, icons, and public descriptions.</span>
                            </div>
                            <div class="flex items-center text-xs font-semibold text-brand-600 dark:text-brand-400 mt-4 group-hover:translate-x-1 transition-transform">
                                <span>Open Services</span>
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </div>
                        </a>
                        
                        <a href="{{ route('admin.cms.products.index') }}" class="p-4 rounded-xl border border-slate-200 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-900/40 hover:bg-white dark:hover:bg-slate-800 hover:border-brand-500/50 transition duration-150 flex flex-col justify-between group shadow-xs">
                            <div>
                                <span class="font-bold text-slate-900 dark:text-white block group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors">Website Products</span>
                                <span class="text-xs text-slate-500 dark:text-slate-400 mt-1 block">Manage public product catalog and showcase items.</span>
                            </div>
                            <div class="flex items-center text-xs font-semibold text-brand-600 dark:text-brand-400 mt-4 group-hover:translate-x-1 transition-transform">
                                <span>Open Catalog</span>
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </div>
                        </a>

                        <a href="{{ route('admin.cms.pages.index') }}" class="p-4 rounded-xl border border-slate-200 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-900/40 hover:bg-white dark:hover:bg-slate-800 hover:border-brand-500/50 transition duration-150 flex flex-col justify-between group shadow-xs">
                            <div>
                                <span class="font-bold text-slate-900 dark:text-white block group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors">Edit Pages</span>
                                <span class="text-xs text-slate-500 dark:text-slate-400 mt-1 block">Home, About Us, and Contact info content.</span>
                            </div>
                            <div class="flex items-center text-xs font-semibold text-brand-600 dark:text-brand-400 mt-4 group-hover:translate-x-1 transition-transform">
                                <span>Open Pages</span>
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Inbox Quick Widget -->
                <div class="theme-card flex flex-col justify-between">
                    <div>
                        <div class="pb-3 border-b border-slate-200 dark:border-slate-700/80 mb-4">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Contact Submissions</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Enquiries submitted via public website</p>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700/60 text-center">
                            <span class="text-3xl font-black text-brand-600 dark:text-brand-400">{{ $stats['enquiries'] }}</span>
                            <span class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-1">Pending Messages</span>
                        </div>
                    </div>
                    
                    <div class="pt-6">
                        <a href="{{ route('admin.cms.contact-submissions.index') }}" class="btn-primary w-full text-center text-xs py-2.5">
                            Open Inbox
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
