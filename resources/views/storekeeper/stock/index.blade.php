<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Stock Maintenance') }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('storekeeper.stock.inward') }}" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700">Inward Stock</a>
                <a href="{{ route('storekeeper.stock.adjustment') }}" class="bg-yellow-600 text-white px-4 py-2 rounded-md text-sm hover:bg-yellow-700">Adjust Stock</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4">
                    <form method="GET" action="{{ route('storekeeper.stock.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Filter by Item</label>
                            <select name="item_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300">
                                <option value="">All Items</option>
                                @foreach($items as $item)
                                <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->item_name }} ({{ $item->current_stock }} {{ $item->unit }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Movement Type</label>
                            <select name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300">
                                <option value="">All Types</option>
                                <option value="inward" {{ request('type') == 'inward' ? 'selected' : '' }}>Inward</option>
                                <option value="outward" {{ request('type') == 'outward' ? 'selected' : '' }}>Outward</option>
                                <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm hover:bg-gray-700 w-full">Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Stock Movement Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Item</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Reference</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Qty</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Balance</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($movements as $movement)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $movement->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $movement->inventoryItem->item_name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $movement->inventoryItem->category->name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $movement->movement_type === 'inward' ? 'bg-green-100 text-green-800' : ($movement->movement_type === 'outward' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                        {{ ucfirst($movement->movement_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    @if($movement->reference && $movement->reference instanceof \App\Models\Storekeeper\DeliveryNote)
                                        <div class="flex flex-col">
                                            <span class="text-xs text-gray-400">Delivery Note</span>
                                            <a href="{{ route('storekeeper.delivery-notes.show', $movement->reference->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 font-medium hover:underline">
                                                {{ $movement->reference->dr_number }}
                                            </a>
                                        </div>
                                    @elseif($movement->movement_type === 'inward' && !$movement->reference_type)
                                        <span class="text-green-600 font-medium">Manual Inward</span>
                                    @elseif($movement->movement_type === 'adjustment')
                                        <span class="text-yellow-600 font-medium">Stock Adjustment</span>
                                    @elseif($movement->reference && $movement->reference instanceof \App\Models\MaterialRequest)
                                         <div class="flex flex-col">
                                            <span class="text-xs text-gray-400">Material Request</span>
                                            <a href="{{ route('storekeeper.material-requests.show', $movement->reference->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 font-medium hover:underline">
                                                MR #{{ $movement->reference->id }}
                                            </a>
                                        </div>
                                    @elseif($movement->reference && $movement->reference instanceof \App\Models\PurchaseOrder)
                                         <div class="flex flex-col">
                                            <span class="text-xs text-gray-400">Purchase Order</span>
                                            <a href="{{ route('storekeeper.purchase-orders.show', $movement->reference->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 font-medium hover:underline">
                                                {{ $movement->reference->po_number }}
                                            </a>
                                        </div>
                                    @else
                                        <!-- Fallback: Clean Class Name -->
                                        <span class="block" title="{{ $movement->reference_type }}">
                                            {{ class_basename($movement->reference_type) }}
                                        </span>
                                        @if($movement->reference_id)
                                            <span class="text-xs text-gray-400 font-mono">
                                                {{ Str::limit($movement->reference_id, 8) }}
                                            </span>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium {{ $movement->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">
                                    {{ $movement->balance_after }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $movement->remarks }}
                                    <div class="text-xs text-gray-400" title="Location">{{ $movement->location }}</div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No stock movements found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $movements->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
