<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $inventoryItem->item_name }}
            </h2>
                <span class="text-xs text-gray-400 mr-2">ID: {{ $inventoryItem->id }}</span>
                <a href="{{ route('storekeeper.stock.adjustment', ['item_id' => $inventoryItem->id]) }}" class="bg-yellow-600 text-white px-4 py-2 rounded-md text-sm hover:bg-yellow-700 mr-2">Adjust Stock</a>
                <a href="{{ route('storekeeper.inventory.edit', $inventoryItem) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">Edit Item</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Main Info Card -->
                <div class="md:col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Item Details</h3>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Category</p>
                                <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $inventoryItem->category->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Brand / Model</p>
                                <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ $inventoryItem->brand ?? '-' }} / {{ $inventoryItem->model ?? '-' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Unit of Measure</p>
                                <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $inventoryItem->unit }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $inventoryItem->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800 dark:text-gray-200' }}">
                                    {{ ucfirst($inventoryItem->status) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Cost Price</p>
                                <p class="text-lg font-medium text-gray-900 dark:text-gray-100">QAR {{ number_format($inventoryItem->purchase_price, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Selling Price</p>
                                <p class="text-lg font-medium text-gray-900 dark:text-gray-100">QAR {{ number_format($inventoryItem->selling_price, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stock Info Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Stock Status</h3>
                        
                        <div class="text-center mb-6">
                            <p class="text-4xl font-bold {{ $inventoryItem->current_stock == 0 ? 'text-red-600' : ($inventoryItem->is_low_stock ? 'text-yellow-600' : 'text-green-600') }}">
                                {{ $inventoryItem->current_stock }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Current Stock</p>
                        </div>

                        <div class="space-y-3">
                            <div class="flex justify-between items-center border-b pb-2">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Minimum Level</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $inventoryItem->minimum_stock_level }}</span>
                            </div>
                            <div class="flex justify-between items-center border-b pb-2">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Reorder Quantity</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $inventoryItem->reorder_quantity }}</span>
                            </div>
                            <div class="mt-4">
                                @if($inventoryItem->current_stock == 0)
                                    <div class="bg-red-100 text-red-800 p-2 rounded text-center text-sm font-bold">OUT OF STOCK</div>
                                @elseif($inventoryItem->is_low_stock)
                                    <div class="bg-yellow-100 text-yellow-800 p-2 rounded text-center text-sm font-bold">LOW STOCK ALERT</div>
                                @else
                                    <div class="bg-green-100 text-green-800 p-2 rounded text-center text-sm font-bold">Stock Healthy</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock History -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Recent Stock Movements</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reference</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Qty</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Balance</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Remarks</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($inventoryItem->stockMovements as $movement)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $movement->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $movement->movement_type === 'inward' ? 'bg-green-100 text-green-800' : ($movement->movement_type === 'outward' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                            {{ ucfirst($movement->movement_type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $movement->reference_type }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium {{ $movement->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">
                                        {{ $movement->balance_after }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $movement->remarks }}
                                        @if($movement->creator)
                                            <div class="text-xs text-gray-400">By: {{ $movement->creator->name }}</div>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No movements recorded.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
