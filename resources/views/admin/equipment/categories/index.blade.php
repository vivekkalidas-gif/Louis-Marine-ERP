<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight">
                    {{ __('Equipment Categories & Master') }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manage master equipment categories, equipment items, and specifications</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6" x-data="{ activeTab: '{{ request('category_id') || request('page') ? 'items' : 'categories' }}' }">
        <div class="max-w-7xl mx-auto space-y-6">
            
            <!-- Segmented Control Tabs Navigation -->
            <div class="theme-card p-1.5 flex bg-slate-50/50 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-700/80 gap-2">
                <button @click="activeTab = 'categories'" 
                        :class="activeTab === 'categories' ? 'bg-white dark:bg-slate-800 text-brand-600 dark:text-brand-400 shadow-xs border border-slate-200/80 dark:border-slate-700/80' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                        class="flex-1 py-2.5 px-4 rounded-lg font-bold text-xs transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    <span>Equipment Categories</span>
                    <span class="badge-neutral text-[10px] px-2 py-0.5">{{ count($categories) }}</span>
                </button>
                <button @click="activeTab = 'items'" 
                        :class="activeTab === 'items' ? 'bg-white dark:bg-slate-800 text-brand-600 dark:text-brand-400 shadow-xs border border-slate-200/80 dark:border-slate-700/80' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                        class="flex-1 py-2.5 px-4 rounded-lg font-bold text-xs transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    <span>Equipment Items</span>
                    <span class="badge-neutral text-[10px] px-2 py-0.5">{{ $items->total() }}</span>
                </button>
            </div>

            <!-- TAB 1: Categories -->
            <div x-show="activeTab === 'categories'" x-transition>
                <div class="table-container">
                    <div class="p-5 border-b border-slate-200 dark:border-slate-700/80 flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Equipment Categories Directory</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">High-level grouping for equipment types and certificate templates</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.equipment.categories.export') }}" class="btn-ghost text-xs gap-1.5" title="Export to CSV">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span>Export CSV</span>
                            </a>
                            <button @click="$dispatch('open-modal', 'import-categories')" class="btn-ghost text-xs gap-1.5" title="Import from CSV">
                                <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                <span>Import CSV</span>
                            </button>
                            <button @click="$dispatch('open-modal', 'add-category')" class="btn-primary text-xs gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                <span>Add Category</span>
                            </button>
                        </div>
                    </div>

                    <table class="theme-table">
                        <thead>
                            <tr class="theme-table-header">
                                <th class="px-6 py-3.5">Category Name</th>
                                <th class="px-6 py-3.5">Description</th>
                                <th class="px-6 py-3.5">Templates Count</th>
                                <th class="px-6 py-3.5">Status</th>
                                <th class="px-6 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700/60">
                            @forelse($categories as $category)
                            <tr class="theme-table-row">
                                <td class="px-6 py-4 font-bold text-slate-900 dark:text-white text-sm">
                                    {{ $category->name }}
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-600 dark:text-slate-400">
                                    {{ Str::limit($category->description, 60) ?: '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.certificate-templates.index', ['category_id' => $category->id]) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-brand-50 text-brand-700 dark:bg-brand-950/60 dark:text-brand-300 border border-brand-200 dark:border-brand-800 hover:bg-brand-100 dark:hover:bg-brand-900 transition">
                                        <svg class="w-3.5 h-3.5 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>{{ $category->certificate_templates_count ?? $category->templates_count }} Templates</span>
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    @if($category->is_active)
                                        <span class="badge-success text-xs">Active</span>
                                    @else
                                        <span class="badge-neutral text-xs">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="$dispatch('open-modal', 'edit-category-{{ $category->id }}')" class="btn-secondary btn-sm">Edit</button>
                                        <form method="POST" action="{{ route('admin.equipment.categories.destroy', $category->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this category? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-danger btn-sm">
                                                Delete
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Edit Category Modal per Row -->
                                    <x-modal name="edit-category-{{ $category->id }}" focusable>
                                        <form method="POST" action="{{ route('admin.equipment.categories.update', $category->id) }}" class="p-6 space-y-4 text-left">
                                            @csrf
                                            @method('PUT')
                                            <h3 class="text-base font-bold text-slate-900 dark:text-white pb-2 border-b border-slate-200 dark:border-slate-700">Edit Service Category</h3>
                                            <div class="space-y-4">
                                                <div>
                                                    <label class="theme-label">Category Name *</label>
                                                    <input type="text" name="name" class="theme-input text-xs" value="{{ old('name', $category->name) }}" required>
                                                </div>
                                                <div>
                                                    <label class="theme-label">Description</label>
                                                    <textarea name="description" class="theme-input text-xs" rows="3">{{ old('description', $category->description) }}</textarea>
                                                </div>
                                                <div>
                                                    <label class="theme-label">Display Order</label>
                                                    <input type="number" name="display_order" class="theme-input text-xs" value="{{ old('display_order', $category->display_order ?? 0) }}">
                                                </div>
                                                <div>
                                                    <label class="flex items-center gap-2 cursor-pointer">
                                                        <input type="checkbox" name="is_active" value="1" {{ $category->is_active ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600">
                                                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Active</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="pt-4 flex justify-end gap-3">
                                                <button type="button" x-on:click="$dispatch('close')" class="btn-secondary text-xs">Cancel</button>
                                                <button type="submit" class="btn-primary text-xs">Update Category</button>
                                            </div>
                                        </form>
                                    </x-modal>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500 text-sm">No service categories configured yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 2: Equipment Items -->
            <div x-show="activeTab === 'items'" x-transition>
                <div class="table-container">
                    <div class="p-5 border-b border-slate-200 dark:border-slate-700/80 flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Equipment Items Master</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Specific tool models, serial codes, and specifications</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.equipment.items.export') }}" class="btn-ghost text-xs gap-1.5" title="Export to CSV">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span>Export CSV</span>
                            </a>
                            <button @click="$dispatch('open-modal', 'import-items')" class="btn-ghost text-xs gap-1.5" title="Import from CSV">
                                <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                <span>Import CSV</span>
                            </button>
                            <a href="{{ route('admin.equipment.items.create') }}" class="btn-primary text-xs gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                <span>Add Equipment Item</span>
                            </a>
                        </div>
                    </div>

                    <!-- Category Filter Dropdown -->
                    <div class="p-4 border-b border-slate-200 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-900/40">
                        <form method="GET" action="{{ route('admin.equipment.categories.index') }}" class="flex items-center gap-3">
                            <label class="theme-label mb-0 whitespace-nowrap">Filter Category:</label>
                            <select name="category_id" onchange="this.form.submit()" class="theme-input text-xs py-2 w-64">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    <table class="theme-table">
                        <thead>
                            <tr class="theme-table-header">
                                <th class="px-6 py-3.5">Code</th>
                                <th class="px-6 py-3.5">Equipment Name</th>
                                <th class="px-6 py-3.5">Category</th>
                                <th class="px-6 py-3.5">Brand / Model</th>
                                <th class="px-6 py-3.5">Status</th>
                                <th class="px-6 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700/60">
                            @forelse($items as $item)
                            <tr class="theme-table-row">
                                <td class="px-6 py-4 font-mono font-bold text-xs text-slate-700 dark:text-slate-300">
                                    {{ $item->code }}
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-900 dark:text-white text-sm">
                                    {{ $item->name }}
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold text-slate-700 dark:text-slate-300">
                                    {{ $item->category->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-600 dark:text-slate-400">
                                    {{ $item->brand ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($item->is_active)
                                        <span class="badge-success text-xs">Active</span>
                                    @else
                                        <span class="badge-neutral text-xs">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.equipment.items.edit', $item->id) }}" class="btn-secondary btn-sm">Edit</a>
                                        <form method="POST" action="{{ route('admin.equipment.items.destroy', $item->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this item?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500 text-sm">No equipment items found matching criteria.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if($items->hasPages())
                    <div class="p-4 border-t border-slate-200 dark:border-slate-700/80 bg-slate-50 dark:bg-slate-800">
                        {{ $items->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Add Category Modal -->
        <x-modal name="add-category" focusable>
            <form method="POST" action="{{ route('admin.equipment.categories.store') }}" class="p-6 space-y-4">
                @csrf
                <h3 class="text-base font-bold text-slate-900 dark:text-white pb-2 border-b border-slate-200 dark:border-slate-700">Add Service Category</h3>
                <div class="space-y-4">
                    <div>
                        <label class="theme-label">Category Name *</label>
                        <input type="text" name="name" class="theme-input text-xs" required>
                    </div>
                    <div>
                        <label class="theme-label">Description</label>
                        <textarea name="description" class="theme-input text-xs" rows="3"></textarea>
                    </div>
                    <div>
                        <label class="theme-label">Display Order</label>
                        <input type="number" name="display_order" class="theme-input text-xs" value="0">
                    </div>
                    <div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-brand-600">
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Active</span>
                        </label>
                    </div>
                </div>
                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close')" class="btn-secondary text-xs">Cancel</button>
                    <button type="submit" class="btn-primary text-xs">Create Category</button>
                </div>
            </form>
        </x-modal>

        <!-- Import Categories Modal -->
        <x-modal name="import-categories" focusable>
            <form method="POST" action="{{ route('admin.equipment.categories.import') }}" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <h3 class="text-base font-bold text-slate-900 dark:text-white pb-2 border-b border-slate-200 dark:border-slate-700">Import Categories CSV</h3>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">
                        Upload a CSV file with columns: Name, Description, Status. 
                        <a href="{{ route('admin.equipment.categories.template') }}" class="text-brand-600 dark:text-brand-400 font-bold hover:underline">Download Template</a>
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

        <!-- Import Items Modal -->
        <x-modal name="import-items" focusable>
            <form method="POST" action="{{ route('admin.equipment.items.import') }}" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <h3 class="text-base font-bold text-slate-900 dark:text-white pb-2 border-b border-slate-200 dark:border-slate-700">Import Equipment Items CSV</h3>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">
                        Upload a CSV file with columns: Category, Name, Brand, Model, Unit, Description. 
                        <a href="{{ route('admin.equipment.items.template') }}" class="text-brand-600 dark:text-brand-400 font-bold hover:underline">Download Template</a>
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
    </div>
</x-app-layout>
