<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Stock Adjustment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-4 bg-yellow-50 border-l-4 border-yellow-400 p-4 dark:bg-yellow-900/20">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700 dark:text-yellow-200">
                                    Use this form to correct stock discrepancies (e.g., damaged items, lost stock, or counting errors). This action will be audited.
                                </p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('storekeeper.stock.store-adjustment') }}" class="space-y-6">
                        @csrf

                        <!-- Item Selection -->
                        <div>
                            <x-input-label for="inventory_item_id" :value="__('Select Item')" />
                            <select id="inventory_item_id" name="inventory_item_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required autofocus>
                                <option value="">-- Choose Item --</option>
                                @foreach($items as $item)
                                <option value="{{ $item->id }}" {{ (old('inventory_item_id') == $item->id || request('item_id') == $item->id) ? 'selected' : '' }}>
                                    {{ $item->item_name }} (Current: {{ $item->current_stock }} {{ $item->unit }})
                                </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('inventory_item_id')" class="mt-2" />
                        </div>

                        <!-- Adjustment Type -->
                        <div>
                            <x-input-label for="adjustment_type" :value="__('Adjustment Type')" />
                            <select id="adjustment_type" name="adjustment_type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                <option value="increase" {{ old('adjustment_type') == 'increase' ? 'selected' : '' }}>Increase Stock (+)</option>
                                <option value="decrease" {{ old('adjustment_type') == 'decrease' ? 'selected' : '' }}>Decrease Stock (-)</option>
                            </select>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Select 'Decrease' for damaged or lost items.</p>
                            <x-input-error :messages="$errors->get('adjustment_type')" class="mt-2" />
                        </div>

                        <!-- Quantity -->
                        <div>
                            <x-input-label for="quantity" :value="__('Quantity to Adjust')" />
                            <x-text-input id="quantity" class="block mt-1 w-full" type="number" name="quantity" :value="old('quantity')" required min="1" />
                            <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                        </div>

                        <!-- Remarks -->
                        <div>
                            <x-input-label for="remarks" :value="__('Reason for Adjustment')" />
                            <x-text-input id="remarks" class="block mt-1 w-full" type="text" name="remarks" :value="old('remarks')" required placeholder="e.g., Found during annual audit, Damaged in transit" />
                            <x-input-error :messages="$errors->get('remarks')" class="mt-2" />
                        </div>

                        <div class="flex justify-end gap-4 border-t pt-4 mt-4">
                            <a href="{{ route('storekeeper.stock.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <x-primary-button class="bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500">
                                {{ __('Submit Adjustment') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
