<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-marine-900 dark:text-marine-100 leading-tight">
                {{ __('Inventory: Spare Parts') }}
            </h2>
            <a href="{{ route('admin.inventory.products.create') }}" class="bg-marine-600 hover:bg-marine-700 text-white font-bold py-2 px-4 rounded">
                Add New Part
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-marine-900 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-marine-800">
                            <thead class="bg-gray-50 dark:bg-marine-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">SKU / Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Location</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Stock Level</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Price</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-marine-900 divide-y divide-gray-200 dark:divide-marine-800">
                                @foreach($products as $product)
                                    <tr class="{{ $product->current_stock <= $product->min_stock_level ? 'bg-red-50 dark:bg-red-900/20' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $product->name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $product->sku }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $product->category->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $product->location ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($product->current_stock <= $product->min_stock_level)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                    Low: {{ $product->current_stock }}
                                                </span>
                                            @else
                                                <span class="text-sm font-bold text-green-600 dark:text-green-400">{{ $product->current_stock }}</span>
                                            @endif
                                            <span class="text-xs text-gray-500 dark:text-gray-400 block">Min: {{ $product->min_stock_level }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            ${{ number_format($product->price, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.inventory.products.show', $product->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 mr-3">Manage Stock</a>
                                            <a href="{{ route('admin.inventory.products.edit', $product->id) }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-500 dark:text-gray-400 dark:hover:text-gray-300">Edit</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
