<x-app-layout>
    <x-slot name="header">
        <h2 class="theme-text-header">Service Categories</h2>
    </x-slot>

    <div class="py-12" x-data="{ activeTab: '{{ request('category_id') || request('page') ? 'items' : 'categories' }}', editCategory: {} }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Tab Navigation -->
            <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8">
                    <button @click="activeTab = 'categories'" 
                            :class="activeTab === 'categories' ? 'border-marine-500 text-marine-600 dark:text-marine-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Equipment Categories
                    </button>
                    <button @click="activeTab = 'items'" 
                            :class="activeTab === 'items' ? 'border-marine-500 text-marine-600 dark:text-marine-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Equipment Items
                    </button>
                </nav>
            </div>

            <!-- TAB 1: Categories -->
            <div x-show="activeTab === 'categories'" x-transition>
                <div class="theme-card">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-marine-900 dark:text-marine-100">Equipment Categories</h3>
                        <div class="flex space-x-2">
                             <a href="{{ route('admin.equipment.categories.export') }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded font-medium text-sm flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Export
                            </a>
                            <button @click="$dispatch('open-modal', 'import-categories')" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded font-medium text-sm flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Import
                            </button>
                            <button @click="$dispatch('open-modal', 'add-category')" class="bg-marine-700 hover:bg-marine-600 text-white px-4 py-2 rounded font-medium flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Add Category
                            </button>
                        </div>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">

                        <thead class="bg-gray-50 dark:bg-marine-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Items</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-marine-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($categories as $category)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $category->name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ Str::limit($category->description, 50) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $category->templates_count }} items
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($category->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                    @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:text-gray-200">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <button @click="editCategory = {{ $category->toJson() }}; $dispatch('open-modal', 'edit-category')" class="text-blue-600 hover:text-blue-900">Edit</button>
                                    <form method="POST" action="{{ route('admin.equipment.categories.toggle', $category->id) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-yellow-600 hover:text-yellow-900">
                                            {{ $category->is_active ? 'Disable' : 'Enable' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No categories found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 2: Equipment Items -->
            <div x-show="activeTab === 'items'" x-transition>
                <div class="theme-card">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-marine-900 dark:text-marine-100">Equipment Items</h3>
                        <div class="flex space-x-2">
                             <a href="{{ route('admin.equipment.items.export') }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded font-medium text-sm flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Export
                            </a>
                            <button @click="$dispatch('open-modal', 'import-items')" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded font-medium text-sm flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Import
                            </button>
                            <a href="{{ route('admin.equipment.items.create') }}" class="bg-marine-700 hover:bg-marine-600 text-white px-3 py-2 rounded font-medium text-sm flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Add Equipment
                            </a>
                        </div>
                    </div>

                    <!-- Filter -->
                    <div class="mb-4">
                        <form method="GET" action="{{ route('admin.equipment.categories.index') }}">
                            <select name="category_id" onchange="this.form.submit()" class="theme-input w-64">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-marine-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Brand</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-marine-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($items as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-gray-100">
                                    {{ $item->code }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $item->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $item->category->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $item->brand ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($item->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                    @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:text-gray-200">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="{{ route('admin.equipment.items.edit', $item->id) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                    <form method="POST" action="{{ route('admin.equipment.items.destroy', $item->id) }}" class="inline" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No equipment items found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $items->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div x-data="{ open: false }" @open-modal.window="if ($event.detail === 'add-category') open = true" @close-modal.window="open = false" x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-50 dark:bg-gray-7000 bg-opacity-75" @click="open = false"></div>
            <div class="relative bg-white dark:bg-marine-900 rounded-lg p-6 max-w-md w-full">
                <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">Add Equipment Category</h3>
                <form method="POST" action="{{ route('admin.equipment.categories.store') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="theme-label">Category Name *</label>
                            <input type="text" name="name" class="theme-input" required>
                        </div>
                        <div>
                            <label class="theme-label">Description</label>
                            <textarea name="description" class="theme-input" rows="3"></textarea>
                        </div>
                        <div>
                            <label class="theme-label">Display Order</label>
                            <input type="number" name="display_order" class="theme-input" value="0">
                        </div>
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" checked class="rounded">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                            </label>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="open = false" class="bg-gray-50 dark:bg-gray-7000 hover:bg-gray-600 text-white px-4 py-2 rounded">Cancel</button>
                        <button type="submit" class="bg-marine-700 hover:bg-marine-600 text-white px-4 py-2 rounded">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div @open-modal.window="if ($event.detail === 'edit-category') $el.style.display = 'block'" @close-modal.window="$el.style.display = 'none'" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-50 dark:bg-gray-7000 bg-opacity-75" @click="$el.parentElement.parentElement.style.display = 'none'"></div>
            <div class="relative bg-white dark:bg-marine-900 rounded-lg p-6 max-w-md w-full">
                <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">Edit Equipment Category</h3>
                <form method="POST" x-bind:action="(editCategory && editCategory.id) ? '/Louis/public/admin/equipment/categories/' + editCategory.id : '#'">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="theme-label">Category Name *</label>
                            <input type="text" name="name" class="theme-input" :value="editCategory.name || ''" required>
                        </div>
                        <div>
                            <label class="theme-label">Description</label>
                            <textarea name="description" class="theme-input" rows="3" x-text="editCategory.description || ''"></textarea>
                        </div>
                        <div>
                            <label class="theme-label">Display Order</label>
                            <input type="number" name="display_order" class="theme-input" :value="editCategory.display_order || 0">
                        </div>
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" :checked="editCategory.is_active || false" class="rounded">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                            </label>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="$el.closest('.fixed').style.display = 'none'" class="bg-gray-50 dark:bg-gray-7000 hover:bg-gray-600 text-white px-4 py-2 rounded">Cancel</button>
                        <button type="submit" class="bg-marine-700 hover:bg-marine-600 text-white px-4 py-2 rounded">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Import Items Modal -->
    <div x-data="{ open: false }" @open-modal.window="if ($event.detail === 'import-items') open = true" @close-modal.window="open = false" x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-50 dark:bg-gray-7000 bg-opacity-75" @click="open = false"></div>
            <div class="relative bg-white dark:bg-marine-900 rounded-lg p-6 max-w-md w-full">
                <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">Import Equipment Items</h3>
                <form method="POST" action="{{ route('admin.equipment.items.import') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                         <div class="bg-blue-50 dark:bg-blue-900 p-3 rounded text-sm text-blue-800 dark:text-blue-100">
                            Upload a CSV file with columns: <br>
                            Category, Name, Brand, Model, Unit, Description, Attributes
                        </div>
                        
                        <div>
                            <label class="theme-label">CSV File *</label>
                            <input type="file" name="file" accept=".csv" class="theme-input p-2" required>
                        </div>
                        
                        <div class="text-right">
                            <a href="{{ route('admin.equipment.items.template') }}" class="text-xs text-blue-600 hover:underline">Download Sample Template</a>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="open = false" class="bg-gray-50 dark:bg-gray-7000 hover:bg-gray-600 text-white px-4 py-2 rounded">Cancel</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Import Categories Modal -->
    <div x-data="{ open: false }" @open-modal.window="if ($event.detail === 'import-categories') open = true" @close-modal.window="open = false" x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-50 dark:bg-gray-7000 bg-opacity-75" @click="open = false"></div>
            <div class="relative bg-white dark:bg-marine-900 rounded-lg p-6 max-w-md w-full">
                <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">Import Equipment Categories</h3>
                <form method="POST" action="{{ route('admin.equipment.categories.import') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                         <div class="bg-blue-50 dark:bg-blue-900 p-3 rounded text-sm text-blue-800 dark:text-blue-100">
                            Upload a CSV file with columns: <br>
                            Name, Description, Status
                        </div>
                        
                        <div>
                            <label class="theme-label">CSV File *</label>
                            <input type="file" name="file" accept=".csv" class="theme-input p-2" required>
                        </div>
                        
                        <div class="text-right">
                            <a href="{{ route('admin.equipment.categories.template') }}" class="text-xs text-blue-600 hover:underline">Download Sample Template</a>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="open = false" class="bg-gray-50 dark:bg-gray-7000 hover:bg-gray-600 text-white px-4 py-2 rounded">Cancel</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
