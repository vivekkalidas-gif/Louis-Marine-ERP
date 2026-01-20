<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-marine-900 dark:text-marine-100 leading-tight">
                {{ $product->name }} <span class="text-sm font-normal text-gray-500 dark:text-gray-400">({{ $product->sku }})</span>
            </h2>
            <div class="text-right">
                <div class="text-sm text-gray-500 dark:text-gray-400">Current Stock</div>
                <div class="text-3xl font-bold {{ $product->current_stock <= $product->min_stock_level ? 'text-red-600' : 'text-green-600' }}">
                    {{ $product->current_stock }}
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Use Stock Adjustment Form -->
                <div class="col-span-1 bg-white dark:bg-marine-900 shadow sm:rounded-lg p-6 h-fit">
                    <h3 class="font-bold text-lg mb-4 text-marine-800 dark:text-marine-200 border-b pb-2">Adjust Stock</h3>
                    <form action="{{ route('admin.inventory.products.adjust', $product->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-bold mb-2">Action Type</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="IN" class="peer sr-only">
                                    <div class="p-2 text-center rounded border peer-checked:bg-green-100 peer-checked:border-green-500 peer-checked:text-green-800 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700">
                                        Check IN (+)
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="OUT" class="peer sr-only">
                                    <div class="p-2 text-center rounded border peer-checked:bg-red-100 peer-checked:border-red-500 peer-checked:text-red-800 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700">
                                        Check OUT (-)
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-bold mb-2">Quantity</label>
                            <input type="number" name="quantity" min="1" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-bold mb-2">Reference (PO / Job #)</label>
                            <input type="text" name="reference" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-bold mb-2">Notes</label>
                            <textarea name="notes" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700" rows="2"></textarea>
                        </div>

                        <button type="submit" class="w-full bg-marine-600 hover:bg-marine-700 text-white font-bold py-2 rounded">
                            Update Stock
                        </button>
                    </form>
                </div>

                <!-- Ledger History -->
                <div class="col-span-2 bg-white dark:bg-marine-900 shadow sm:rounded-lg p-6">
                    <h3 class="font-bold text-lg mb-4 text-marine-800 dark:text-marine-200 border-b pb-2">Transaction Ledger</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-marine-800">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">User</th>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Type</th>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Qty</th>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Ref</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-marine-800">
                                @forelse($product->transactions()->latest()->get() as $tx)
                                <tr class="text-sm">
                                    <td class="px-4 py-2">{{ $tx->created_at->format('M d, H:i') }}</td>
                                    <td class="px-4 py-2">{{ $tx->user->name ?? 'System' }}</td>
                                    <td class="px-4 py-2 font-bold {{ $tx->type === 'IN' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $tx->type }}
                                    </td>
                                    <td class="px-4 py-2 font-mono">{{ $tx->quantity }}</td>
                                    <td class="px-4 py-2 text-gray-500 dark:text-gray-400">
                                        {{ $tx->reference }}
                                        @if($tx->notes)
                                            <div class="text-xs italic">{{ $tx->notes }}</div>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400 italic">No transactions found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Details Panel -->
                <div class="col-span-3 bg-gray-50 dark:bg-marine-800 p-6 rounded text-sm text-gray-700 dark:text-gray-300">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <span class="block text-xs font-bold uppercase text-gray-500 dark:text-gray-400">Category</span>
                            <span class="font-medium">{{ $product->category->name }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold uppercase text-gray-500 dark:text-gray-400">Supplier</span>
                            <span class="font-medium">{{ $product->supplier->name ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold uppercase text-gray-500 dark:text-gray-400">Location</span>
                            <span class="font-medium">{{ $product->location ?? 'Unassigned' }}</span>
                        </div>
                         <div>
                            <span class="block text-xs font-bold uppercase text-gray-500 dark:text-gray-400">Price</span>
                            <span class="font-medium">${{ $product->price }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
