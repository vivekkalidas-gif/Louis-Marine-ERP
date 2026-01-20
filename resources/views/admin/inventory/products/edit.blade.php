<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-marine-900 dark:text-marine-100 leading-tight">
            {{ __('Edit Spare Part: ') . $product->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-marine-900 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('admin.inventory.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Basic Info -->
                            <div>
                                <label class="block text-sm font-bold mb-2">Item Name *</label>
                                <input type="text" name="name" value="{{ old('name', $product->name) }}" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold mb-2">SKU / Code *</label>
                                <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700" required>
                            </div>

                            <div>
                                <label class="block text-sm font-bold mb-2">Category *</label>
                                <select name="inventory_category_id" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700" required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $product->inventory_category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold mb-2">Cost Price ($)</label>
                                <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700">
                            </div>

                            <!-- Inventory Info -->
                            <div>
                                <label class="block text-sm font-bold mb-2">Rack Location</label>
                                <input type="text" name="location" value="{{ old('location', $product->location) }}" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700">
                            </div>
                            <div>
                                <label class="block text-sm font-bold mb-2">Min Stock Level</label>
                                <input type="number" name="min_stock_level" value="{{ old('min_stock_level', $product->min_stock_level) }}" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700" required>
                            </div>

                             <!-- CMS Info -->
                             <div class="col-span-1 md:col-span-2 border-t pt-6 mt-4">
                                <h3 class="font-bold text-lg mb-4 text-marine-600 border-b pb-2">Web Showcase Settings</h3>
                            </div>

                            <div>
                                <label class="block text-sm font-bold mb-2">Slug (URL) <span class="text-xs font-normal text-gray-400">(Auto-generated if empty)</span></label>
                                <input type="text" name="slug" value="{{ old('slug', $product->slug) }}" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700">
                            </div>

                            <div class="flex items-center mt-6">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_published" value="1" {{ $product->is_published ? 'checked' : '' }} class="sr-only peer">
                                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white dark:bg-gray-800 after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                    <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Publish to Website</span>
                                </label>
                            </div>

                            <!-- Image Manager -->
                            <div class="col-span-1 md:col-span-2 mt-4" x-data="{ removedImages: [] }">
                                <label class="block text-sm font-bold mb-2">Product Images</label>
                                
                                <!-- Existing Images -->
                                @if(!empty($product->image_paths))
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                    @foreach($product->image_paths as $path)
                                        <div class="relative group border rounded p-1" x-show="!removedImages.includes('{{ $path }}')">
                                            <img src="{{ Storage::url($path) }}" class="w-full h-32 object-cover rounded">
                                            <button type="button" @click="removedImages.push('{{ $path }}')" class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1 shadow hover:bg-red-700 opacity-0 group-hover:opacity-100 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                                @endif
                                
                                <!-- Hidden Inputs for Removed Images -->
                                <template x-for="img in removedImages" :key="img">
                                    <input type="hidden" name="remove_images[]" :value="img">
                                </template>

                                <!-- Upload New -->
                                <div class="border-2 border-dashed border-gray-300 p-4 rounded text-center hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 transition">
                                    <input type="file" name="new_images[]" multiple class="w-full" accept="image/*">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Click to select new images (Multiple allowed)</p>
                                </div>
                            </div>

                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-bold mb-2">Short Description <span class="text-xs font-normal text-gray-400">(Shown on list page)</span></label>
                                <textarea name="short_description" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700" rows="2">{{ old('short_description', $product->short_description) }}</textarea>
                            </div>
                            
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-bold mb-2">Full Description <span class="text-xs font-normal text-gray-400">(HTML Supported)</span></label>
                                <textarea name="description" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700 font-mono text-sm" rows="6">{{ old('description', $product->description) }}</textarea>
                            </div>

                            <!-- Specifications Editor (Alpine.js) -->
                             <div class="col-span-1 md:col-span-2 mt-4" 
                                x-data="{ 
                                    specs: {{ json_encode($product->specifications ? collect($product->specifications)->map(fn($v, $k) => ['key' => $k, 'value' => $v])->values() : [['key' => '', 'value' => '']]) }}
                                }">
                                <label class="block text-sm font-bold mb-2">Specifications</label>
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded border dark:bg-marine-800 dark:border-marine-700">
                                    <template x-for="(spec, index) in specs" :key="index">
                                        <div class="flex gap-2 mb-2 items-center">
                                            <input type="text" name="specs_keys[]" x-model="spec.key" placeholder="Name (e.g. Color)" class="flex-1 p-2 border rounded text-sm">
                                            <input type="text" name="specs_values[]" x-model="spec.value" placeholder="Value (e.g. Red)" class="flex-1 p-2 border rounded text-sm">
                                            <button type="button" @click="specs.splice(index, 1)" class="text-red-500 hover:text-red-700">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </template>
                                    <button type="button" @click="specs.push({key: '', value: ''})" class="mt-2 text-sm text-blue-600 hover:underline font-bold">+ Add Specification</button>
                                </div>
                            </div>

                            <!-- Ignore Supplier for CMS focus, but keep hidden/optional if needed or just leave as is -->
                            <input type="hidden" name="supplier_id" value="{{ old('supplier_id', $product->supplier_id) }}"> 
                        </div>

                        <div class="flex justify-end">
                            <a href="{{ route('admin.inventory.products.index') }}" class="mr-4 bg-gray-200 text-gray-700 dark:text-gray-300 font-bold py-2 px-4 rounded hover:bg-gray-300">Cancel</a>
                            <button type="submit" class="bg-marine-600 hover:bg-marine-700 text-white font-bold py-2 px-4 rounded">
                                Update Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
