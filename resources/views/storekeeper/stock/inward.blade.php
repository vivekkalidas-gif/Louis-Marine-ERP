<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Inward Stock') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('storekeeper.stock.store-inward') }}" class="space-y-6">
                        @csrf

                        <!-- Item Selection -->
                        <div>
                            <x-input-label for="inventory_item_id" :value="__('Select Item')" />
                            <select id="inventory_item_id" name="inventory_item_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required autofocus>
                                <option value="">-- Choose Item --</option>
                                @foreach($items as $item)
                                <option value="{{ $item->id }}" {{ old('inventory_item_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->item_name }} (Current: {{ $item->current_stock }} {{ $item->unit }})
                                </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('inventory_item_id')" class="mt-2" />
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                <a href="{{ route('storekeeper.inventory.create') }}" class="text-blue-600 hover:underline">Create new item</a> if it doesn't exist.
                            </p>
                        </div>

                        <!-- Quantity -->
                        <div>
                            <x-input-label for="quantity" :value="__('Quantity Received')" />
                            <x-text-input id="quantity" class="block mt-1 w-full" type="number" name="quantity" :value="old('quantity')" required min="1" />
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Enter the quantity to add to stock.</p>
                            <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                        </div>

                        <!-- Location -->
                        <div>
                            <x-input-label for="location" :value="__('Storage Location')" />
                            <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" :value="old('location')" placeholder="e.g., Shelf A1, Rack 3" />
                            <x-input-error :messages="$errors->get('location')" class="mt-2" />
                        </div>

                        <!-- Remarks -->
                        <div>
                            <x-input-label for="remarks" :value="__('Remarks / Reference')" />
                            <x-text-input id="remarks" class="block mt-1 w-full" type="text" name="remarks" :value="old('remarks')" placeholder="e.g., PO #1234, Delivery Note #5678" />
                            <x-input-error :messages="$errors->get('remarks')" class="mt-2" />
                        </div>

                        <div class="flex justify-end gap-4 border-t pt-4 mt-4">
                            <a href="{{ route('storekeeper.stock.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Add Stock') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
