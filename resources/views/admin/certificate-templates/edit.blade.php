<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight">
                        {{ $certificateTemplate->name }}
                    </h2>
                    @if($certificateTemplate->is_default)
                        <span class="badge-info text-xs">Default Master</span>
                    @endif
                    <span class="{{ $certificateTemplate->is_active ? 'badge-success' : 'badge-neutral' }} text-xs">
                        {{ $certificateTemplate->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Category: <strong>{{ $certificateTemplate->equipmentCategory->name ?? 'General Category' }}</strong> • Configure form layout sections and field parameters
                </p>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.certificate-templates.preview', $certificateTemplate) }}" target="_blank" class="btn-ghost text-xs gap-1.5" title="Live Preview">
                    <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <span>Preview</span>
                </a>
                <a href="{{ route('admin.certificate-templates.index') }}" class="btn-ghost text-xs gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Back to Templates</span>
                </a>
            </div>
        </div>
    </x-slot>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <style>
        .drag-handle, .section-drag-handle { cursor: grab; }
        .drag-handle:active, .section-drag-handle:active { cursor: grabbing; }
        .sortable-ghost { opacity: 0.4; background-color: rgba(59, 130, 246, 0.1); border-style: dashed; }
    </style>

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-6">

            <!-- Flash Success / Error Banner -->
            @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/80 text-emerald-800 dark:text-emerald-200 text-xs font-semibold flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800/80 text-rose-800 dark:text-rose-200 text-xs font-semibold flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
            @endif

            <!-- 70/30 LEFT-RIGHT SAAS SPLIT ARCHITECTURE -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <!-- LEFT COLUMN (70%): Form Sections & Field Builder -->
                <div class="lg:col-span-8 space-y-6">
                    
                    <!-- SECTION 1: Template General Identification Settings -->
                    <div class="theme-card">
                        <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-200 dark:border-slate-700/80">
                            <div class="w-9 h-9 rounded-xl bg-brand-50 dark:bg-brand-950/60 text-brand-600 dark:text-brand-400 border border-brand-200 dark:border-brand-800/50 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">1. Master Template Settings</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Template title, associated equipment category, and global active status</p>
                            </div>
                        </div>

                        <form action="{{ route('admin.certificate-templates.update', $certificateTemplate) }}" method="POST" class="space-y-5">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="theme-label mb-0">Template Name</label>
                                        <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/50 px-1.5 py-0.5 rounded">Required</span>
                                    </div>
                                    <input type="text" name="name" value="{{ old('name', $certificateTemplate->name) }}" required class="theme-input text-xs py-2.5 font-bold" placeholder="e.g. Fire Extinguisher Inspection Certificate">
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="theme-label mb-0">Equipment Category</label>
                                        <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/50 px-1.5 py-0.5 rounded">Required</span>
                                    </div>
                                    <select name="equipment_category_id" required class="theme-input text-xs py-2.5">
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $certificateTemplate->equipment_category_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="md:col-span-2">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="theme-label mb-0">Official Certificate Header Title</label>
                                        <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/50 px-1.5 py-0.5 rounded">Required</span>
                                    </div>
                                    <input type="text" name="title" value="{{ old('title', $certificateTemplate->title) }}" required class="theme-input text-xs py-2.5 font-semibold" placeholder="e.g. CERTIFICATE OF INSPECTION & TESTING">
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center justify-between gap-4 pt-4 border-t border-slate-200 dark:border-slate-700/80">
                                <div class="flex items-center gap-6">
                                    <label class="flex items-center gap-2.5 cursor-pointer">
                                        <input type="checkbox" name="is_active" value="1" {{ $certificateTemplate->is_active ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 w-4 h-4">
                                        <span class="text-xs font-bold text-slate-900 dark:text-white">Active Status</span>
                                    </label>

                                    <label class="flex items-center gap-2.5 cursor-pointer">
                                        <input type="checkbox" name="is_default" value="1" {{ $certificateTemplate->is_default ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 w-4 h-4">
                                        <span class="text-xs font-bold text-slate-900 dark:text-white">Default Category Template</span>
                                    </label>
                                </div>

                                <button type="submit" class="btn-primary text-xs font-bold px-4 py-2 gap-1.5 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Update Settings</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- SECTION 2: Interactive Form Layout Builder (Sections & Fields) -->
                    <div class="theme-card">
                        <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-200 dark:border-slate-700/80">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800/50 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white">2. Form Architecture & Field Builder</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Drag to reorder sections and fields. Click `+ Add Field` to insert form controls</p>
                                </div>
                            </div>

                            <button onclick="showAddSectionModal()" class="btn-primary text-xs font-bold gap-1.5 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>+ Add Section</span>
                            </button>
                        </div>

                        <!-- Sections Container -->
                        <div id="sections-container" class="space-y-4">
                            @forelse($certificateTemplate->sections as $section)
                            <div class="theme-card p-4 space-y-3 section-item border border-slate-200/90 dark:border-slate-700/90 bg-slate-50/40 dark:bg-slate-900/30 rounded-2xl" data-section-id="{{ $section->id }}">
                                
                                <!-- Section Header Row -->
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <div class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 section-drag-handle p-1 shrink-0" title="Drag Section to Reorder">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h4 class="font-bold text-slate-900 dark:text-white text-sm truncate">{{ $section->name }}</h4>
                                                <span class="badge-neutral text-[10px] uppercase font-bold">{{ str_replace('_', ' ', $section->layout_type) }}</span>
                                                @if($section->is_repeatable)
                                                    <span class="badge-info text-[10px] font-bold">Repeatable Table</span>
                                                @endif
                                                <span class="badge-neutral text-[10px] font-mono">{{ $section->fields->count() }} Fields</span>
                                            </div>
                                            @if($section->description)
                                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">{{ $section->description }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Section Actions -->
                                    <div class="flex items-center gap-2 shrink-0">
                                        <button onclick='editSection(@json($section, JSON_HEX_APOS | JSON_HEX_QUOT))' class="btn-ghost btn-sm text-xs text-brand-600 dark:text-brand-400 gap-1 font-bold">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 012.828 0L20 4.828a2 2 0 010 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Edit</span>
                                        </button>
                                        <button onclick="showAddFieldModal('{{ $section->id }}')" class="btn-secondary btn-sm text-xs text-emerald-600 dark:text-emerald-400 gap-1 font-bold">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            <span>+ Add Field</span>
                                        </button>
                                        <form action="{{ route('admin.template-sections.destroy', $section) }}" method="POST" onsubmit="return confirm('Delete this section and all associated fields?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-ghost btn-sm text-xs text-rose-500 hover:text-rose-700 p-1.5" title="Delete Section">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Fields Container inside Section -->
                                @if($section->fields->count() > 0)
                                <div class="space-y-2 pt-2 border-t border-slate-200/80 dark:border-slate-700/60 sortable-fields" data-section-id="{{ $section->id }}">
                                    @foreach($section->fields as $field)
                                    <div class="flex items-center justify-between p-2.5 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700/80 hover:border-brand-500/40 transition-all shadow-xs" data-field-id="{{ $field->id }}">
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <div class="text-slate-400 hover:text-slate-600 drag-handle p-1 shrink-0" title="Drag Field to Reorder">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
                                            </div>
                                            <div class="min-w-0 flex-1 flex items-center gap-2 flex-wrap">
                                                <span class="font-bold text-slate-900 dark:text-white text-xs truncate">{{ $field->field_label }}</span>
                                                <span class="badge-neutral text-[10px] font-mono lowercase px-2 py-0.5">{{ $field->field_type }}</span>
                                                <span class="badge-neutral text-[10px] uppercase font-mono px-1.5 py-0.5">{{ $field->width }}</span>
                                                @if($field->is_required)
                                                    <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/50 px-1.5 py-0.5 rounded">Required</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2 shrink-0">
                                            <button onclick='editField(@json($field, JSON_HEX_APOS | JSON_HEX_QUOT))' type="button" class="btn-ghost btn-sm text-xs text-brand-600 dark:text-brand-400 gap-1 font-bold">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 012.828 0L20 4.828a2 2 0 010 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                <span>Edit</span>
                                            </button>
                                            <form action="{{ route('admin.template-fields.destroy', $field) }}" method="POST" onsubmit="return confirm('Delete this field?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-ghost btn-sm text-xs text-rose-500 hover:text-rose-700 p-1" title="Delete Field">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <div class="p-3 text-center text-xs text-slate-400 border border-dashed border-slate-200 dark:border-slate-700/60 rounded-xl">
                                    No fields added to this section yet. Click <strong class="text-emerald-500 cursor-pointer" onclick="showAddFieldModal('{{ $section->id }}')">+ Add Field</strong> to create controls.
                                </div>
                                @endif
                            </div>
                            @empty
                            <div class="py-12 text-center text-slate-400 border border-dashed border-slate-200 dark:border-slate-700/80 rounded-2xl">
                                <svg class="w-10 h-10 mx-auto text-slate-300 dark:text-slate-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                <p class="text-xs font-bold text-slate-700 dark:text-slate-300">No sections added to this template yet.</p>
                                <button onclick="showAddSectionModal()" class="btn-primary text-xs mt-3 inline-flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    <span>+ Add First Section</span>
                                </button>
                            </div>
                            @endforelse
                        </div>
                    </div>

                </div>

                <!-- RIGHT COLUMN (30% Sticky Panel): Actions & Quick Stats -->
                <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">
                    
                    <!-- Primary Action Panel -->
                    <div class="theme-card space-y-4">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white pb-3 border-b border-slate-200 dark:border-slate-700/80 flex items-center gap-2">
                            <svg class="w-4 h-4 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            <span>Template Actions</span>
                        </h3>

                        <a href="{{ route('admin.certificate-templates.preview', $certificateTemplate) }}" target="_blank" class="btn-primary w-full text-center text-xs py-3 font-bold gap-2 shadow-md flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>Live Certificate Preview</span>
                        </a>

                        <a href="{{ route('admin.certificate-templates.index') }}" class="btn-secondary w-full text-center text-xs py-2.5 font-bold gap-1.5 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            <span>Return to Templates List</span>
                        </a>
                    </div>

                    <!-- Audit & Stats Metadata Card -->
                    <div class="theme-card space-y-3 bg-slate-50/50 dark:bg-slate-900/40">
                        <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider pb-2 border-b border-slate-200 dark:border-slate-700/80">
                            Architecture Summary
                        </h4>

                        <div class="flex items-center justify-between text-xs py-1">
                            <span class="text-slate-500 dark:text-slate-400">Total Sections</span>
                            <span class="font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $certificateTemplate->sections->count() }}</span>
                        </div>

                        <div class="flex items-center justify-between text-xs py-1">
                            <span class="text-slate-500 dark:text-slate-400">Total Form Fields</span>
                            <span class="font-mono font-bold text-purple-600 dark:text-purple-400">{{ $certificateTemplate->sections->sum(fn($s) => $s->fields->count()) }}</span>
                        </div>

                        <div class="flex items-center justify-between text-xs py-1">
                            <span class="text-slate-500 dark:text-slate-400">Template ID</span>
                            <span class="font-mono font-bold text-slate-900 dark:text-white">#{{ $certificateTemplate->id }}</span>
                        </div>

                        <div class="flex items-center justify-between text-xs py-1">
                            <span class="text-slate-500 dark:text-slate-400">Created Date</span>
                            <span class="font-semibold text-slate-900 dark:text-white">{{ $certificateTemplate->created_at ? \Carbon\Carbon::parse($certificateTemplate->created_at)->format('d M, Y') : 'N/A' }}</span>
                        </div>
                    </div>

                    <!-- Danger Zone -->
                    <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800/80 space-y-3">
                        <h4 class="text-xs font-bold text-rose-900 dark:text-rose-200 uppercase tracking-wider">Danger Zone</h4>
                        <p class="text-[11px] text-slate-600 dark:text-slate-300">Permanently delete this certificate template and all associated sections.</p>
                        <form action="{{ route('admin.certificate-templates.destroy', $certificateTemplate) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this template permanently?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger w-full text-xs font-bold py-2 gap-1.5 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Delete Template</span>
                            </button>
                        </form>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- Add/Edit Section Modal -->
    <div id="addSectionModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
        <div class="theme-card max-w-xl w-full p-6 space-y-4 shadow-2xl">
            <h3 id="sectionModalTitle" class="text-base font-bold text-slate-900 dark:text-white pb-2 border-b border-slate-200 dark:border-slate-700">Add New Section</h3>
            <form id="addSectionForm" action="{{ route('admin.template-sections.store', $certificateTemplate) }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" id="sectionFormMethod" value="POST">
                
                <div>
                    <label class="theme-label">Section Name *</label>
                    <input type="text" name="name" required class="theme-input text-xs" placeholder="e.g. Equipment Identification">
                </div>

                <div>
                    <label class="theme-label">Description</label>
                    <textarea name="description" rows="2" class="theme-input text-xs" placeholder="Brief section scope..."></textarea>
                </div>

                <div>
                    <label class="theme-label">Layout Type *</label>
                    <select name="layout_type" required class="theme-input text-xs">
                        <option value="single_column">Single Column</option>
                        <option value="two_column">Two Column</option>
                        <option value="table">Table (Repeatable Data Grid)</option>
                    </select>
                </div>

                <div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_repeatable" value="1" class="rounded border-slate-300 text-brand-600">
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Repeatable Section (Allow multiple entry rows)</span>
                    </label>
                </div>

                <div class="pt-3 flex justify-end gap-3 border-t border-slate-200 dark:border-slate-700">
                    <button type="button" onclick="hideAddSectionModal()" class="btn-secondary text-xs">Cancel</button>
                    <button type="submit" id="sectionSubmitBtn" class="btn-primary text-xs font-bold">Add Section</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add/Edit Field Modal -->
    <div id="addFieldModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
        <div class="theme-card max-w-xl w-full p-6 max-h-[90vh] overflow-y-auto space-y-4 shadow-2xl">
            <h3 id="fieldModalTitle" class="text-base font-bold text-slate-900 dark:text-white pb-2 border-b border-slate-200 dark:border-slate-700">Add New Field</h3>
            <form id="addFieldForm" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" id="fieldFormMethod" value="POST">
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="theme-label">Field Identifier (Key) *</label>
                        <input type="text" name="field_name" required class="theme-input text-xs font-mono" placeholder="e.g. serial_number">
                    </div>
                    <div>
                        <label class="theme-label">Field Label *</label>
                        <input type="text" name="field_label" required class="theme-input text-xs" placeholder="e.g. Serial Number">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="theme-label">Field Input Type *</label>
                        <select name="field_type" id="field_type" required class="theme-input text-xs" onchange="toggleFieldOptions()">
                            <option value="text">Text Input</option>
                            <option value="textarea">Textarea</option>
                            <option value="number">Number</option>
                            <option value="date">Date Picker</option>
                            <option value="dropdown">Dropdown Select</option>
                            <option value="checkbox">Checkbox</option>
                            <option value="radio">Radio Buttons</option>
                        </select>
                    </div>
                    <div>
                        <label class="theme-label">Field Width *</label>
                        <select name="width" required class="theme-input text-xs">
                            <option value="full">Full Width (100%)</option>
                            <option value="half">Half Width (50%)</option>
                            <option value="third">Third Width (33%)</option>
                        </select>
                    </div>
                </div>

                <div id="field_options_container" class="hidden">
                    <label class="theme-label">Dropdown / Radio Options (One per line)</label>
                    <textarea name="field_options_text" rows="4" class="theme-input text-xs font-mono" placeholder="Option 1&#10;Option 2&#10;Option 3"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="theme-label">Placeholder Text</label>
                        <input type="text" name="placeholder" class="theme-input text-xs" placeholder="e.g. Enter serial code...">
                    </div>
                    <div>
                        <label class="theme-label">Help Subtitle</label>
                        <input type="text" name="help_text" class="theme-input text-xs" placeholder="e.g. Located on metal tag">
                    </div>
                </div>

                <div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_required" value="1" class="rounded border-slate-300 text-brand-600">
                        <span class="text-xs font-bold text-slate-900 dark:text-white">Required Field</span>
                    </label>
                </div>

                <div class="pt-3 flex justify-end gap-3 border-t border-slate-200 dark:border-slate-700">
                    <button type="button" onclick="hideAddFieldModal()" class="btn-secondary text-xs">Cancel</button>
                    <button type="submit" id="fieldSubmitBtn" class="btn-primary text-xs font-bold">Add Field</button>
                </div>
            </form>
        </div>
    </div>

    <!-- SortableJS & Form JavaScript Logic -->
    <script>
        function showAddSectionModal() {
            resetSectionModal();
            document.getElementById('addSectionModal').classList.remove('hidden');
        }

        function hideAddSectionModal() {
            document.getElementById('addSectionModal').classList.add('hidden');
        }

        function editSection(section) {
            resetSectionModal();
            const modal = document.getElementById('addSectionModal');
            const form = document.getElementById('addSectionForm');
            
            document.getElementById('sectionModalTitle').innerText = 'Edit Section';
            document.getElementById('sectionSubmitBtn').innerText = 'Update Section';
            document.getElementById('sectionFormMethod').value = 'PUT';
            
            form.action = `{{ url('admin/template-sections') }}/${section.id}`;
            
            form.querySelector('[name="name"]').value = section.name;
            form.querySelector('[name="description"]').value = section.description || '';
            form.querySelector('[name="layout_type"]').value = section.layout_type;
            form.querySelector('[name="is_repeatable"]').checked = !!section.is_repeatable;
            
            modal.classList.remove('hidden');
        }

        function resetSectionModal() {
            const form = document.getElementById('addSectionForm');
            form.reset();
            form.action = "{{ route('admin.template-sections.store', $certificateTemplate) }}";
            document.getElementById('sectionModalTitle').innerText = 'Add New Section';
            document.getElementById('sectionSubmitBtn').innerText = 'Add Section';
            document.getElementById('sectionFormMethod').value = 'POST';
        }

        function showAddFieldModal(sectionId) {
            resetFieldModal();
            const form = document.getElementById('addFieldForm');
            form.action = `{{ url('admin/template-sections') }}/${sectionId}/fields`;
            document.getElementById('addFieldModal').classList.remove('hidden');
        }

        function editField(field) {
            resetFieldModal();
            const modal = document.getElementById('addFieldModal');
            const form = document.getElementById('addFieldForm');
            
            document.getElementById('fieldModalTitle').innerText = 'Edit Field';
            document.getElementById('fieldSubmitBtn').innerText = 'Update Field';
            document.getElementById('fieldFormMethod').value = 'PUT';
            
            form.action = `{{ url('admin/template-fields') }}/${field.id}`;
            
            form.querySelector('[name="field_name"]').value = field.field_name;
            form.querySelector('[name="field_label"]').value = field.field_label;
            form.querySelector('[name="field_type"]').value = field.field_type;
            form.querySelector('[name="width"]').value = field.width;
            form.querySelector('[name="placeholder"]').value = field.placeholder || '';
            form.querySelector('[name="help_text"]').value = field.help_text || '';
            form.querySelector('[name="is_required"]').checked = !!field.is_required;
            
            if (field.field_options) {
                let options = [];
                if (field.field_options.options) {
                    options = field.field_options.options;
                } else if (Array.isArray(field.field_options)) {
                    options = field.field_options;
                }
                
                if (options.length > 0) {
                    document.querySelector('[name="field_options_text"]').value = options.join('\n');
                    toggleFieldOptions(); 
                }
            } else {
                 toggleFieldOptions();
            }
            
            modal.classList.remove('hidden');
        }

        function resetFieldModal() {
            const form = document.getElementById('addFieldForm');
            form.reset();
            document.getElementById('fieldModalTitle').innerText = 'Add New Field';
            document.getElementById('fieldSubmitBtn').innerText = 'Add Field';
            document.getElementById('fieldFormMethod').value = 'POST';
            document.getElementById('field_options_container').classList.add('hidden');
            const dynamicInputs = form.querySelectorAll('input[type="hidden"][name^="field_options["]');
            dynamicInputs.forEach(input => input.remove());
        }

        function hideAddFieldModal() {
            document.getElementById('addFieldModal').classList.add('hidden');
        }

        function toggleFieldOptions() {
            const fieldType = document.getElementById('field_type').value;
            const container = document.getElementById('field_options_container');
            if (fieldType === 'dropdown' || fieldType === 'radio') {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }

        document.getElementById('addFieldForm').addEventListener('submit', function(e) {
            const fieldType = document.getElementById('field_type').value;
            if (fieldType === 'dropdown' || fieldType === 'radio') {
                const optionsText = document.querySelector('[name="field_options_text"]').value;
                const options = optionsText.split('\n').filter(o => o.trim());
                
                options.forEach((option, index) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `field_options[${index}]`;
                    input.value = option.trim();
                    this.appendChild(input);
                });
            }
        });

        // Initialize SortableJS for Sections & Fields
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Sortable === 'undefined') return;
            
            const containers = document.querySelectorAll('.sortable-fields');
            containers.forEach(container => {
                new Sortable(container, {
                    filter: 'button, a',
                    preventOnFilter: false,
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    onEnd: function (evt) {
                        const items = evt.to.querySelectorAll('[data-field-id]');
                        const fields = [];
                        
                        items.forEach((item, index) => {
                            fields.push({
                                id: item.getAttribute('data-field-id'),
                                order: index + 1
                            });
                        });

                        fetch('{{ route("admin.template-fields.reorder") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ fields: fields })
                        });
                    }
                });
            });

            const sectionsContainer = document.getElementById('sections-container');
            if (sectionsContainer) {
                new Sortable(sectionsContainer, {
                    filter: 'button, a, input, select, textarea',
                    preventOnFilter: false,
                    handle: '.section-drag-handle',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    onEnd: function (evt) {
                        const items = sectionsContainer.querySelectorAll('[data-section-id]');
                        const sections = [];
                        
                        items.forEach((item, index) => {
                            sections.push({
                                id: item.getAttribute('data-section-id'),
                                order: index + 1
                            });
                        });

                        fetch('{{ route("admin.template-sections.reorder") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ sections: sections })
                        });
                    }
                });
            }
        });
    </script>
</x-app-layout>
