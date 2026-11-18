<x-app-layout>
<div class="container mx-auto px-4 py-4 max-w-7xl">

    <!-- Top Breadcrumb & Action Bar (Sticky / Glassmorphic) -->
    <div class="sticky top-0 z-20 backdrop-blur-md bg-slate-50/80 dark:bg-slate-900/80 border-b border-slate-200/80 dark:border-slate-800 -mx-4 px-4 py-3 mb-6 transition-all">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.certificate-templates.index') }}" class="p-2 rounded-lg text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-200/60 dark:hover:bg-slate-800 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        <span>Certificate Master</span>
                        <span>/</span>
                        <span>Templates</span>
                    </div>
                    <h1 class="text-xl font-bold text-slate-900 dark:text-white leading-tight flex items-center gap-2">
                        {{ $certificateTemplate->name }}
                        <span class="{{ $certificateTemplate->is_active ? 'badge-success' : 'badge-neutral' }}">
                            {{ $certificateTemplate->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @if($certificateTemplate->is_default)
                        <span class="badge-info">Default</span>
                        @endif
                    </h1>
                </div>
            </div>

            <!-- Sticky Action Buttons -->
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.certificate-templates.preview', $certificateTemplate) }}" target="_blank" class="btn-ghost text-sm gap-2">
                    <svg class="w-4 h-4 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <span>Live Preview</span>
                </a>
                <a href="{{ route('admin.certificate-templates.edit', $certificateTemplate) }}" class="btn-primary text-sm gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>Edit Template</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Grid Layout (Left: Content, Right: Info Panel) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Main Column: Structure & Fields -->
        <div class="lg:col-span-8 space-y-6">

            <!-- Compact KPI Summary Bar -->
            <div class="grid grid-cols-3 gap-4">
                <div class="stat-card flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Equipment Category</p>
                        <p class="text-base font-bold text-slate-900 dark:text-white mt-0.5 truncate">{{ $certificateTemplate->equipmentCategory->name ?? 'General Category' }}</p>
                    </div>
                    <div class="p-2.5 rounded-lg bg-brand-50 dark:bg-brand-950/60 text-brand-600 dark:text-brand-400 border border-brand-200 dark:border-brand-800/50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                </div>

                <div class="stat-card flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Sections</p>
                        <p class="text-2xl font-black text-brand-600 dark:text-brand-400 mt-0.5">{{ $certificateTemplate->sections->count() }}</p>
                    </div>
                    <div class="p-2.5 rounded-lg bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-800/50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                    </div>
                </div>

                <div class="stat-card flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Form Fields</p>
                        <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5">{{ $certificateTemplate->sections->sum(fn($s) => $s->fields->count()) }}</p>
                    </div>
                    <div class="p-2.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Template Structure Section Header -->
            <div class="theme-card">
                <div class="flex items-center justify-between mb-6 pb-3 border-b border-slate-200 dark:border-slate-700/80">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Template Sections & Form Architecture</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Configured layout schema and field parameters for dynamic certificate generation</p>
                    </div>
                    <span class="badge-neutral text-xs">{{ $certificateTemplate->sections->count() }} Configured Sections</span>
                </div>

                <!-- Collapsible Sections Accordion -->
                <div class="space-y-4" x-data="{ expandedSection: null }">
                    @forelse($certificateTemplate->sections as $index => $section)
                    <div class="border border-slate-200 dark:border-slate-700/90 rounded-xl overflow-hidden bg-slate-50/50 dark:bg-slate-900/40 transition-all" :class="{ 'ring-2 ring-brand-500/20 border-brand-500/50': expandedSection === {{ $index }} }">
                        
                        <!-- Section Header -->
                        <div @click="expandedSection = (expandedSection === {{ $index }} ? null : {{ $index }})" class="p-4 flex items-center justify-between cursor-pointer hover:bg-slate-100/70 dark:hover:bg-slate-800/60 transition select-none">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-lg bg-brand-600 text-white font-bold text-xs flex items-center justify-center shadow-sm">
                                    {{ $index + 1 }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-900 dark:text-white text-base flex items-center gap-2">
                                        {{ $section->name }}
                                        <span class="badge-info text-[10px] px-2 py-0.5">{{ ucfirst(str_replace('_', ' ', $section->layout_type)) }}</span>
                                        @if($section->is_repeatable)
                                        <span class="badge-warning text-[10px] px-2 py-0.5">Repeatable</span>
                                        @endif
                                    </h3>
                                    @if($section->description)
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $section->description }}</p>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 bg-slate-200/60 dark:bg-slate-800 px-2.5 py-1 rounded-md">
                                    {{ $section->fields->count() }} {{ Str::plural('field', $section->fields->count()) }}
                                </span>
                                <svg class="w-5 h-5 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': expandedSection === {{ $index }} || expandedSection === null }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Section Fields Body (Expanded by default or toggled) -->
                        <div x-show="expandedSection === {{ $index }} || expandedSection === null" x-collapse class="p-4 pt-0 border-t border-slate-200/60 dark:border-slate-700/60">
                            @if($section->fields->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                                @foreach($section->fields as $field)
                                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/80 rounded-lg p-3.5 shadow-sm hover:border-brand-300 dark:hover:border-slate-600 transition">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="font-bold text-slate-900 dark:text-slate-100 text-sm">{{ $field->field_label }}</span>
                                        @if($field->is_required)
                                        <span class="badge-danger text-[10px] px-2 py-0.5">Required</span>
                                        @else
                                        <span class="badge-neutral text-[10px] px-2 py-0.5">Optional</span>
                                        @endif
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 text-xs text-slate-600 dark:text-slate-400 mt-2 pt-2 border-t border-slate-100 dark:border-slate-700/50">
                                        <div>
                                            <span class="text-slate-400 dark:text-slate-500 font-medium">Type:</span> 
                                            <span class="font-semibold text-slate-700 dark:text-slate-200 ml-1">{{ ucfirst($field->field_type) }}</span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 dark:text-slate-500 font-medium">Width:</span> 
                                            <span class="font-semibold text-slate-700 dark:text-slate-200 ml-1">{{ ucfirst($field->width) }}</span>
                                        </div>
                                        @if($field->placeholder)
                                        <div class="col-span-2 truncate">
                                            <span class="text-slate-400 dark:text-slate-500 font-medium">Placeholder:</span> 
                                            <span class="text-slate-600 dark:text-slate-300 italic ml-1">"{{ $field->placeholder }}"</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="text-center py-6 text-slate-400 dark:text-slate-500 italic text-sm">
                                No fields configured in this section yet.
                            </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12 text-slate-400 dark:text-slate-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                        </svg>
                        <p class="font-semibold text-base text-slate-700 dark:text-slate-300">No sections defined yet</p>
                        <p class="text-xs mt-1">Click "Edit Template" above to add sections and input fields.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar Panel: Header/Footer Config & Audit Metadata -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Configuration Summary Card -->
            <div class="theme-card space-y-4">
                <h3 class="font-bold text-slate-900 dark:text-white text-base border-b border-slate-200 dark:border-slate-700/80 pb-2.5 flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    </svg>
                    Template Parameters
                </h3>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center py-1.5 border-b border-slate-100 dark:border-slate-700/50">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Category</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $certificateTemplate->equipmentCategory->name }}</span>
                    </div>

                    <div class="flex justify-between items-center py-1.5 border-b border-slate-100 dark:border-slate-700/50">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Default Template</span>
                        <span class="font-bold {{ $certificateTemplate->is_default ? 'text-brand-600 dark:text-brand-400' : 'text-slate-600 dark:text-slate-400' }}">
                            {{ $certificateTemplate->is_default ? 'Yes' : 'No' }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center py-1.5 border-b border-slate-100 dark:border-slate-700/50">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Show Logo Header</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">
                            {{ data_get($certificateTemplate->header_config, 'show_logo', true) ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center py-1.5 border-b border-slate-100 dark:border-slate-700/50">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Show QR Code</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">
                            {{ data_get($certificateTemplate->footer_config, 'show_qr', true) ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Audit Trail & Metadata Card -->
            <div class="theme-card space-y-3">
                <h3 class="font-bold text-slate-900 dark:text-white text-base border-b border-slate-200 dark:border-slate-700/80 pb-2.5 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Audit Metadata
                </h3>

                <div class="space-y-2.5 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 dark:text-slate-400">Created By</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $certificateTemplate->creator->name ?? 'System Admin' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 dark:text-slate-400">Created On</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $certificateTemplate->created_at->format('M d, Y • H:i') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 dark:text-slate-400">Last Modified</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $certificateTemplate->updated_at->format('M d, Y • H:i') }}</span>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
</x-app-layout>
