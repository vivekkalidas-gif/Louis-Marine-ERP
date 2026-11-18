<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-marine-900 dark:text-marine-100 leading-tight">
            {{ __('Stockist Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ showModal: false, selectedProduct: null, actionType: 'OUT' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Search -->
            <div class="mb-6">
                <form action="{{ route('stockist.dashboard') }}" method="GET">
                    <div class="flex">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by SKU or Name..." class="w-full p-3 rounded-l border-y border-l dark:bg-marine-900 dark:border-marine-700">
                        <button type="submit" class="bg-marine-600 text-white px-6 rounded-r font-bold hover:bg-marine-700">Search</button>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-marine-900 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-marine-800">
                            <thead class="bg-gray-50 dark:bg-marine-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">SKU</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Item Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Location</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Stock</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-marine-900 divide-y divide-gray-200 dark:divide-marine-800">
                                @forelse($products as $product)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-marine-800 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-xs font-mono text-gray-500 dark:text-gray-400">{{ $product->sku }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap font-bold text-marine-900 dark:text-marine-100">{{ $product->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $product->category->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $product->location ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($product->current_stock <= $product->min_stock_level)
                                                <span class="px-2 py-1 text-xs font-bold rounded bg-red-100 text-red-800">
                                                    {{ $product->current_stock }}
                                                </span>
                                            @else
                                                <span class="text-lg font-bold text-green-600">{{ $product->current_stock }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button @click="showModal = true; selectedProduct = {{ $product }}; actionType='OUT'" 
                                                class="text-white bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-xs mr-2">
                                                Issue / Out
                                            </button>
                                            <button @click="showModal = true; selectedProduct = {{ $product }}; actionType='IN'" 
                                                class="text-white bg-green-500 hover:bg-green-600 px-3 py-1 rounded text-xs">
                                                Receive / In
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 italic">No products found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
            
            <!-- Transaction Modal -->
            <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showModal = false">
                        <div class="absolute inset-0 bg-gray-50 dark:bg-gray-700 opacity-75"></div>
                    </div>

                    <div class="inline-block align-bottom bg-white dark:bg-marine-900 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <form x-bind:action="'/stockist/products/' + (selectedProduct ? selectedProduct.id : '') + '/transaction'" method="POST">
                            @csrf
                            <div class="bg-white dark:bg-marine-900 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                    <span x-text="actionType === 'IN' ? 'Receive Stock (Check In)' : 'Issue Stock (Check Out)'"></span>
                                    : <span x-text="selectedProduct?.name" class="font-bold"></span>
                                </h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label class="block text-sm font-bold mb-1">Action Type</label>
                                        <select name="type" x-model="actionType" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700">
                                            <option value="OUT">Issue Out (-)</option>
                                            <option value="IN">Receive In (+)</option>
                                            <option value="DAMAGE">Report Damage (-)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold mb-1">Quantity</label>
                                        <input type="number" name="quantity" min="1" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold mb-1">Reference (Job # / PO #)</label>
                                        <input type="text" name="reference" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold mb-1">Notes</label>
                                        <textarea name="notes" placeholder="Optional notes..." class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-marine-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-marine-600 text-base font-medium text-white hover:bg-marine-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                    Confirm Update
                                </button>
                                <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
