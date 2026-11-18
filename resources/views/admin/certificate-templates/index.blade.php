<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight">
                    {{ __('Certificate Templates') }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manage certificate templates for all equipment categories</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.certificate-templates.export') }}" class="btn-ghost text-xs gap-1.5" title="Export CSV">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>Export</span>
                </a>
                <button @click="$dispatch('open-modal', 'import-templates')" class="btn-ghost text-xs gap-1.5" title="Import CSV">
                    <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    <span>Import</span>
                </button>
                <a href="{{ route('admin.certificate-templates.create') }}" class="btn-primary text-xs gap-1.5 shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>+ Create Template</span>
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $totalTemplates = count($templates);
        $defaultTemplates = $templates->where('is_default', true)->count();
        $activeTemplates = $templates->where('is_active', true)->count();
        $totalSections = $templates->sum('sections_count');
        $totalFields = $templates->sum(fn($t) => $t->sections->sum(fn($s) => count($s->fields)));
        $recentUpdate = $templates->first()?->updated_at ? \Carbon\Carbon::parse($templates->first()->updated_at)->diffForHumans() : 'N/A';
    @endphp

    <div class="py-6 space-y-6" 
         x-data="{ 
            viewMode: 'grid', 
            search: '', 
            categoryFilter: '', 
            statusFilter: '',
            dropdownOpen: null,
            matchesFilter(name, category, isActive, isDefault) {
                const s = this.search.toLowerCase().trim();
                const matchSearch = !s || name.toLowerCase().includes(s) || category.toLowerCase().includes(s);
                const matchCategory = !this.categoryFilter || category === this.categoryFilter;
                let matchStatus = true;
                if (this.statusFilter === 'active') matchStatus = isActive;
                if (this.statusFilter === 'inactive') matchStatus = !isActive;
                if (this.statusFilter === 'default') matchStatus = isDefault;
                return matchSearch && matchCategory && matchStatus;
            }
         }">
        
        <div class="max-w-7xl mx-auto space-y-6">

            <!-- KPI SUMMARY DASHBOARD BAR -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <div class="theme-card p-3 flex items-center justify-between">
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Total Templates</span>
                        <span class="text-xl font-black text-slate-900 dark:text-white mt-0.5 block">{{ $totalTemplates }}</span>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-brand-50 dark:bg-brand-950/60 text-brand-600 dark:text-brand-400 border border-brand-200 dark:border-brand-800/50 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                </div>

                <div class="theme-card p-3 flex items-center justify-between">
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Default Master</span>
                        <span class="text-xl font-black text-sky-600 dark:text-sky-400 mt-0.5 block">{{ $defaultTemplates }}</span>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-800/50 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    </div>
                </div>

                <div class="theme-card p-3 flex items-center justify-between">
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Active Status</span>
                        <span class="text-xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5 block">{{ $activeTemplates }}</span>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>

                <div class="theme-card p-3 flex items-center justify-between">
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Total Sections</span>
                        <span class="text-xl font-black text-indigo-600 dark:text-indigo-400 mt-0.5 block">{{ $totalSections }}</span>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800/50 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    </div>
                </div>

                <div class="theme-card p-3 flex items-center justify-between">
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Total Fields</span>
                        <span class="text-xl font-black text-purple-600 dark:text-purple-400 mt-0.5 block">{{ $totalFields }}</span>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-800/50 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 012.828 0L20 4.828a2 2 0 010 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                </div>

                <div class="theme-card p-3 flex items-center justify-between">
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Last Modified</span>
                        <span class="text-xs font-bold text-slate-900 dark:text-white mt-1 block truncate" title="{{ $recentUpdate }}">{{ $recentUpdate }}</span>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800/50 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>

            <!-- SEARCH, FILTER & VIEW MODE TOOLBAR -->
            <div class="theme-card p-3 flex flex-wrap items-center justify-between gap-3 bg-slate-50/50 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-700/80">
                <div class="flex flex-wrap items-center gap-2.5 flex-1 min-w-[300px]">
                    <!-- Search Input -->
                    <div class="relative flex-1 min-w-[200px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" x-model="search" placeholder="Search templates or categories..." class="theme-input text-xs py-2 pl-9 pr-8 w-full">
                        <button x-show="search" @click="search = ''" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Category Filter Dropdown -->
                    <select x-model="categoryFilter" class="theme-input text-xs py-2 w-44">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>

                    <!-- Status Filter -->
                    <select x-model="statusFilter" class="theme-input text-xs py-2 w-36">
                        <option value="">All Statuses</option>
                        <option value="active">Active Only</option>
                        <option value="inactive">Inactive Only</option>
                        <option value="default">Default Master</option>
                    </select>
                </div>

                <!-- View Switcher Tabs -->
                <div class="flex items-center gap-1 bg-slate-200/60 dark:bg-slate-800 p-1 rounded-lg border border-slate-300/60 dark:border-slate-700">
                    <button @click="viewMode = 'grid'" 
                            :class="viewMode === 'grid' ? 'bg-white dark:bg-slate-700 text-brand-600 dark:text-brand-400 shadow-xs' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white'"
                            class="p-1.5 rounded-md text-xs font-bold transition flex items-center gap-1" title="Grid View">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        <span class="hidden sm:inline">Grid</span>
                    </button>

                    <button @click="viewMode = 'table'" 
                            :class="viewMode === 'table' ? 'bg-white dark:bg-slate-700 text-brand-600 dark:text-brand-400 shadow-xs' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white'"
                            class="p-1.5 rounded-md text-xs font-bold transition flex items-center gap-1" title="Table View">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        <span class="hidden sm:inline">Compact Table</span>
                    </button>
                </div>
            </div>

            <!-- VIEW MODE 1: COMPACT GRID LAYOUT (4 COLS DESKTOP) -->
            <div x-show="viewMode === 'grid'" x-transition class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @forelse($templates as $template)
                    @php
                        $catName = $template->equipmentCategory->name ?? 'General Category';
                        $sectionsCnt = $template->sections_count ?? $template->sections->count();
                        $fieldsCnt = $template->sections->sum(fn($s) => count($s->fields));
                        $updatedDiff = $template->updated_at ? \Carbon\Carbon::parse($template->updated_at)->diffForHumans() : 'N/A';
                    @endphp

                    <div x-show="matchesFilter('{{ addslashes($template->name) }}', '{{ addslashes($catName) }}', {{ $template->is_active ? 'true' : 'false' }}, {{ $template->is_default ? 'true' : 'false' }})"
                         class="theme-card p-4 flex flex-col justify-between group hover:border-brand-500/50 dark:hover:border-brand-500/50 hover:shadow-lg transition-all duration-200 rounded-2xl relative">
                        
                        <div>
                            <!-- Card Header: Category Icon, Name, Badges & 3-Dot Menu -->
                            <div class="flex items-start justify-between gap-2 mb-3">
                                <div class="flex items-start gap-3 min-w-0">
                                    <!-- Equipment Icon Recognition -->
                                    <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 text-brand-600 dark:text-brand-400 border border-slate-200 dark:border-slate-700/80 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                        @if(Str::contains($catName, ['Extinguisher', 'Fire'], true))
                                            <svg class="w-5 h-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/></svg>
                                        @elseif(Str::contains($catName, ['Life Jacket', 'Jacket'], true))
                                            <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        @elseif(Str::contains($catName, ['Life Raft', 'Raft'], true))
                                            <svg class="w-5 h-5 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        @elseif(Str::contains($catName, ['SCBA', 'Breathing'], true))
                                            <svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                                        @elseif(Str::contains($catName, ['EEBD'], true))
                                            <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        @else
                                            <svg class="w-5 h-5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        @endif
                                    </div>

                                    <div class="min-w-0">
                                        <h3 class="text-sm font-bold text-slate-900 dark:text-white truncate group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors" title="{{ $template->name }}">
                                            {{ $template->name }}
                                        </h3>
                                        <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 truncate">
                                            {{ $catName }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Dropdown Quick Action Menu -->
                                <div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false">
                                    <button @click="open = !open" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                                    </button>

                                    <div x-show="open" 
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute right-0 mt-1 w-44 rounded-xl bg-white dark:bg-slate-800 shadow-xl border border-slate-200 dark:border-slate-700/80 z-30 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-200">
                                        
                                        <a href="{{ route('admin.certificate-templates.show', $template) }}" class="flex items-center gap-2 px-3 py-1.5 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>View Details</span>
                                        </a>

                                        <a href="{{ route('admin.certificate-templates.edit', $template) }}" class="flex items-center gap-2 px-3 py-1.5 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 012.828 0L20 4.828a2 2 0 010 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Edit Template</span>
                                        </a>

                                        <a href="{{ route('admin.certificate-templates.preview', $template) }}" target="_blank" class="flex items-center gap-2 px-3 py-1.5 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            <span>Live Preview</span>
                                        </a>

                                        <form method="POST" action="{{ route('admin.certificate-templates.duplicate', $template) }}">
                                            @csrf
                                            <button type="submit" class="w-full text-left flex items-center gap-2 px-3 py-1.5 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                <span>Duplicate</span>
                                            </button>
                                        </form>

                                        @if(!$template->is_default)
                                        <form method="POST" action="{{ route('admin.certificate-templates.set-default', $template) }}">
                                            @csrf
                                            <button type="submit" class="w-full text-left flex items-center gap-2 px-3 py-1.5 hover:bg-slate-50 dark:hover:bg-slate-700/50 text-sky-600 dark:text-sky-400">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                                <span>Set Default</span>
                                            </button>
                                        </form>
                                        @endif

                                        <form method="POST" action="{{ route('admin.certificate-templates.toggle', $template) }}">
                                            @csrf
                                            <button type="submit" class="w-full text-left flex items-center gap-2 px-3 py-1.5 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                                <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                                <span>{{ $template->is_active ? 'Deactivate' : 'Activate' }}</span>
                                            </button>
                                        </form>

                                        <div class="border-t border-slate-200 dark:border-slate-700/80 my-1"></div>

                                        <form method="POST" action="{{ route('admin.certificate-templates.destroy', $template) }}" onsubmit="return confirm('Delete this template permanently?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full text-left flex items-center gap-2 px-3 py-1.5 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span>Delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Badges Row -->
                            <div class="flex items-center gap-1.5 mb-3">
                                @if($template->is_default)
                                    <span class="badge-info text-[10px] px-2 py-0.5 font-bold">Default Master</span>
                                @endif
                                @if($template->is_active)
                                    <span class="badge-success text-[10px] px-2 py-0.5 font-bold">Active</span>
                                @else
                                    <span class="badge-neutral text-[10px] px-2 py-0.5 font-bold">Inactive</span>
                                @endif
                            </div>

                            <!-- Compact Metric Chips -->
                            <div class="grid grid-cols-2 gap-2 my-3 py-2 px-2.5 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200/80 dark:border-slate-700/60">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-md bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                    </div>
                                    <div>
                                        <span class="block text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase">Sections</span>
                                        <span class="text-xs font-black text-slate-900 dark:text-white">{{ $sectionsCnt }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-md bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 012.828 0L20 4.828a2 2 0 010 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </div>
                                    <div>
                                        <span class="block text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase">Fields</span>
                                        <span class="text-xs font-black text-slate-900 dark:text-white">{{ $fieldsCnt }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer: Audit Info & Quick Actions -->
                        <div class="pt-3 border-t border-slate-200 dark:border-slate-700/80 space-y-2.5">
                            <div class="flex items-center justify-between text-[11px] text-slate-400 dark:text-slate-500 font-medium">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>{{ $updatedDiff }}</span>
                                </span>
                                <span>{{ $template->creator->name ?? 'Admin' }}</span>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <a href="{{ route('admin.certificate-templates.show', $template) }}" class="btn-secondary text-[11px] py-1.5 font-bold justify-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>View</span>
                                </a>
                                <a href="{{ route('admin.certificate-templates.edit', $template) }}" class="btn-primary text-[11px] py-1.5 font-bold justify-center gap-1 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 012.828 0L20 4.828a2 2 0 010 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>Edit</span>
                                </a>
                            </div>
                        </div>

                    </div>
                @empty
                    <div class="col-span-full py-16 text-center theme-card">
                        <svg class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <h3 class="text-base font-bold text-slate-700 dark:text-slate-300">No Certificate Templates Found</h3>
                        <p class="text-xs text-slate-400 mt-1 mb-4">Create your first certificate template or adjust filter options.</p>
                        <a href="{{ route('admin.certificate-templates.create') }}" class="btn-primary text-xs inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>+ Create Template</span>
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- VIEW MODE 2: COMPACT TABLE LIST VIEW -->
            <div x-show="viewMode === 'table'" x-transition class="table-container">
                <table class="theme-table">
                    <thead>
                        <tr class="theme-table-header">
                            <th class="px-6 py-3.5">Template & Category</th>
                            <th class="px-6 py-3.5">Status & Default</th>
                            <th class="px-6 py-3.5">Sections & Fields</th>
                            <th class="px-6 py-3.5">Certificates Count</th>
                            <th class="px-6 py-3.5">Last Updated</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700/60">
                        @forelse($templates as $template)
                            @php
                                $catName = $template->equipmentCategory->name ?? 'General Category';
                                $sectionsCnt = $template->sections_count ?? $template->sections->count();
                                $fieldsCnt = $template->sections->sum(fn($s) => count($s->fields));
                                $updatedDiff = $template->updated_at ? \Carbon\Carbon::parse($template->updated_at)->diffForHumans() : 'N/A';
                            @endphp
                            <tr x-show="matchesFilter('{{ addslashes($template->name) }}', '{{ addslashes($catName) }}', {{ $template->is_active ? 'true' : 'false' }}, {{ $template->is_default ? 'true' : 'false' }})" class="theme-table-row">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 text-brand-600 dark:text-brand-400 border border-slate-200 dark:border-slate-700 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.certificate-templates.show', $template) }}" class="font-bold text-slate-900 dark:text-white text-sm hover:text-brand-600 dark:hover:text-brand-400">
                                                {{ $template->name }}
                                            </a>
                                            <span class="block text-xs text-slate-500 dark:text-slate-400">{{ $catName }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1.5">
                                        @if($template->is_default)
                                            <span class="badge-info text-[10px] px-2 py-0.5 font-bold">Default</span>
                                        @endif
                                        @if($template->is_active)
                                            <span class="badge-success text-[10px] px-2 py-0.5 font-bold">Active</span>
                                        @else
                                            <span class="badge-neutral text-[10px] px-2 py-0.5 font-bold">Inactive</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3 text-xs">
                                        <span class="font-semibold text-indigo-600 dark:text-indigo-400">📑 {{ $sectionsCnt }} Sections</span>
                                        <span class="text-slate-300 dark:text-slate-600">•</span>
                                        <span class="font-semibold text-purple-600 dark:text-purple-400">📝 {{ $fieldsCnt }} Fields</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold text-slate-700 dark:text-slate-300">
                                    {{ $template->certificates_count ?? 0 }} Certificates Issued
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500 dark:text-slate-400">
                                    {{ $updatedDiff }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.certificate-templates.show', $template) }}" class="btn-ghost btn-sm text-xs">View</a>
                                        <a href="{{ route('admin.certificate-templates.edit', $template) }}" class="btn-secondary btn-sm text-xs">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">No certificate templates configured.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- Import Templates Modal -->
    <x-modal name="import-templates" focusable>
        <form method="POST" action="{{ route('admin.certificate-templates.import') }}" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <h3 class="text-base font-bold text-slate-900 dark:text-white pb-2 border-b border-slate-200 dark:border-slate-700">Import Certificate Templates CSV</h3>
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">
                    Upload a CSV file with columns: name, category_name, title. 
                    <a href="{{ route('admin.certificate-templates.template') }}" class="text-brand-600 dark:text-brand-400 font-bold hover:underline">Download Template</a>
                </p>
                <label class="theme-label">CSV File *</label>
                <input type="file" name="file" accept=".csv" class="theme-input text-xs" required>
            </div>
            <div class="pt-4 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="btn-secondary text-xs">Cancel</button>
                <button type="submit" class="btn-primary text-xs">Upload & Import</button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
