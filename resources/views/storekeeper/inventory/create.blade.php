<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add New Inventory Item') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('storekeeper.inventory.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Basic Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium border-b pb-2">Basic Information</h3>
                                
                                <div>
                                    <x-input-label for="item_name" :value="__('Item Name')" />
                                    <x-text-input id="item_name" class="block mt-1 w-full" type="text" name="item_name" :value="old('item_name')" required autofocus />
                                    <x-input-error :messages="$errors->get('item_name')" class="mt-2" />
                                </div>

                                <div x-data="{ 
                                    generateCode() { 
                                        this.code = 'INV-' + Math.floor(100000 + Math.random() * 900000); 
                                    },
                                    code: '{{ old('item_code') }}'
                                }">
                                    <x-input-label for="item_code" :value="__('Stock Number / Item Code')" />
                                    <div class="flex gap-2">
                                        <x-text-input id="item_code" class="block mt-1 w-full" type="text" name="item_code" x-model="code" placeholder="Enter or Generate Code" />
                                        <button type="button" @click="generateCode()" class="mt-1 px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white dark:bg-gray-800 focus:bg-gray-700 dark:focus:bg-white dark:bg-gray-800 active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                            {{ __('Generate') }}
                                        </button>
                                    </div>
                                    <x-input-error :messages="$errors->get('item_code')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="inventory_category_id" :value="__('Category')" />
                                    <select id="inventory_category_id" name="inventory_category_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('inventory_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('inventory_category_id')" class="mt-2" />
                                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        <a href="{{ route('storekeeper.categories.create') }}" class="text-indigo-600 hover:text-indigo-900" target="_blank">+ Add New Category</a>
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="brand" :value="__('Brand')" />
                                    <x-text-input id="brand" class="block mt-1 w-full" type="text" name="brand" :value="old('brand')" />
                                    <x-input-error :messages="$errors->get('brand')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="model" :value="__('Model')" />
                                    <x-text-input id="model" class="block mt-1 w-full" type="text" name="model" :value="old('model')" />
                                    <x-input-error :messages="$errors->get('model')" class="mt-2" />
                                </div>
                            </div>
                            <!-- Basic Information End -->
                            
                            <!-- Pricing Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium border-b pb-2">Pricing Information</h3>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="purchase_price" :value="__('Cost Price')" />
                                        <div class="relative mt-1 rounded-md shadow-sm">
                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                              <span class="text-gray-500 dark:text-gray-400 sm:text-sm">QAR</span>
                                            </div>
                                            <x-text-input id="purchase_price" class="block w-full pl-12" type="number" step="0.01" name="purchase_price" :value="old('purchase_price', 0)" />
                                        </div>
                                        <x-input-error :messages="$errors->get('purchase_price')" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-input-label for="selling_price" :value="__('Selling Price')" />
                                        <div class="relative mt-1 rounded-md shadow-sm">
                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                              <span class="text-gray-500 dark:text-gray-400 sm:text-sm">QAR</span>
                                            </div>
                                            <x-text-input id="selling_price" class="block w-full pl-12" type="number" step="0.01" name="selling_price" :value="old('selling_price', 0)" />
                                        </div>
                                        <x-input-error :messages="$errors->get('selling_price')" class="mt-2" />
                                    </div>
                                </div>
                            </div>
                            <!-- Pricing Information End -->

                            <!-- Stock Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium border-b pb-2">Stock Settings</h3>

                                <div>
                                    <x-input-label for="unit" :value="__('Unit of Measure')" />
                                    <select id="unit" name="unit" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">
                                        <option value="Nos" {{ old('unit') == 'Nos' ? 'selected' : '' }}>Numbers (Nos)</option>
                                        <option value="Kg" {{ old('unit') == 'Kg' ? 'selected' : '' }}>Kilograms (Kg)</option>
                                        <option value="Set" {{ old('unit') == 'Set' ? 'selected' : '' }}>Sets (Set)</option>
                                        <option value="Ltr" {{ old('unit') == 'Ltr' ? 'selected' : '' }}>Liters (Ltr)</option>
                                        <option value="Mtr" {{ old('unit') == 'Mtr' ? 'selected' : '' }}>Meters (Mtr)</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('unit')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="current_stock" :value="__('Opening Stock')" />
                                    <x-text-input id="current_stock" class="block mt-1 w-full" type="number" name="current_stock" :value="old('current_stock', 0)" required min="0" />
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Initial quantity available.</p>
                                    <x-input-error :messages="$errors->get('current_stock')" class="mt-2" />
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="minimum_stock_level" :value="__('Min Stock Level')" />
                                        <x-text-input id="minimum_stock_level" class="block mt-1 w-full" type="number" name="minimum_stock_level" :value="old('minimum_stock_level', 5)" required min="0" />
                                        <x-input-error :messages="$errors->get('minimum_stock_level')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="reorder_quantity" :value="__('Reorder Qty')" />
                                        <x-text-input id="reorder_quantity" class="block mt-1 w-full" type="number" name="reorder_quantity" :value="old('reorder_quantity', 10)" required min="0" />
                                        <x-input-error :messages="$errors->get('reorder_quantity')" class="mt-2" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-4 border-t pt-4 mt-4">
                            <a href="{{ route('storekeeper.inventory.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Create Item') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
