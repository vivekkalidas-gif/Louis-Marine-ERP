<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-marine-900 dark:text-marine-100 leading-tight">
            {{ __('Add New Spare Part') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-marine-900 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('admin.inventory.products.store') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Basic Info -->
                            <div>
                                <label class="block text-sm font-bold mb-2">Item Name *</label>
                                <input type="text" name="name" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold mb-2">SKU / Code *</label>
                                <input type="text" name="sku" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700" required>
                            </div>

                            <div>
                                <label class="block text-sm font-bold mb-2">Category *</label>
                                <select name="inventory_category_id" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700" required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold mb-2">Cost Price ($)</label>
                                <input type="number" step="0.01" name="price" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700">
                            </div>

                            <!-- Inventory Info -->
                            <div>
                                <label class="block text-sm font-bold mb-2">Rack Location</label>
                                <input type="text" name="location" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700">
                            </div>
                            <div>
                                <label class="block text-sm font-bold mb-2">Min Stock Level</label>
                                <input type="number" name="min_stock_level" value="5" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700" required>
                            </div>

                             <!-- CMS Info -->
                             <div class="col-span-1 md:col-span-2 border-t pt-4 mt-2">
                                <h3 class="font-bold text-lg mb-4 text-marine-600">Web Showcase Settings</h3>
                            </div>

                            <div>
                                <label class="block text-sm font-bold mb-2">Slug (URL) <span class="text-xs font-normal text-gray-400">(Auto-generated if empty)</span></label>
                                <input type="text" name="slug" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700">
                            </div>

                            <div class="flex items-center mt-6">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_published" value="1" class="sr-only peer">
                                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white dark:bg-gray-800 after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                    <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Publish to Website</span>
                                </label>
                            </div>

                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-bold mb-2">Short Description <span class="text-xs font-normal text-gray-400">(Shown on list page)</span></label>
                                <textarea name="short_description" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700" rows="2"></textarea>
                            </div>
                            
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-bold mb-2">Full Description</label>
                                <textarea name="description" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700" rows="4"></textarea>
                            </div>

                            <!-- Ignore Supplier for CMS focus, but keep hidden/optional if needed or just leave as is -->
                            <input type="hidden" name="supplier_id" value=""> 
                        </div>

                        <div class="flex justify-end">
                            <a href="{{ route('admin.inventory.products.index') }}" class="mr-4 bg-gray-200 text-gray-700 dark:text-gray-300 font-bold py-2 px-4 rounded hover:bg-gray-300">Cancel</a>
                            <button type="submit" class="bg-marine-600 hover:bg-marine-700 text-white font-bold py-2 px-4 rounded">
                                Create Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
