<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Purchase Order Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Actions -->
            <div class="flex justify-between mb-6">
                <a href="{{ route('storekeeper.purchase-orders.index') }}" class="text-blue-600 hover:text-blue-900 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to List
                </a>
                <div class="flex space-x-3">
                    <!-- Actions like Print, Edit, Receive could go here -->
                     @if(!in_array($purchaseOrder->status, ['received', 'cancelled']))
                        <a href="{{ route('storekeeper.purchase-orders.edit', $purchaseOrder) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">Edit PO</a>
                    @endif
                    <a href="{{ route('storekeeper.purchase-orders.pdf', $purchaseOrder) }}" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm hover:bg-gray-700" target="_blank">Print / Download PDF</a>
                    @if($purchaseOrder->status !== 'received' && $purchaseOrder->status !== 'cancelled')
                        <form action="{{ route('storekeeper.purchase-orders.receive', $purchaseOrder) }}" method="POST" onsubmit="return confirm('Are you sure you want to receive this PO? This will update stock levels.');" class="inline">
                            @csrf
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700">Receive Stock</button>
                        </form>
                    @endif
                    @if($purchaseOrder->bills->isEmpty())
                        <a href="{{ route('accounts.vendor-bills.create-from-po', $purchaseOrder) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">Create Vendor Bill</a>
                    @else
                        <span class="inline-flex items-center px-3 py-2 rounded border border-purple-200 bg-purple-50 text-purple-700 text-sm">
                            Billed
                        </span>
                    @endif
                </div>
            </div>

            <!-- PO Info Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">PO Number</p>
                            <p class="font-bold text-lg">{{ $purchaseOrder->po_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Supplier</p>
                            <p class="font-bold text-lg">{{ $purchaseOrder->supplier->name }}</p>
                            <p class="text-sm">{{ $purchaseOrder->supplier->contact_person }}</p>
                        </div>
                         <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Date</p>
                            <p class="font-bold text-lg">{{ $purchaseOrder->po_date->format('d M Y') }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Expected: {{ $purchaseOrder->expected_delivery_date ? $purchaseOrder->expected_delivery_date->format('d M Y') : 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($purchaseOrder->status === 'received') bg-green-100 text-green-800 
                                @elseif($purchaseOrder->status === 'sent') bg-blue-100 text-blue-800
                                @elseif($purchaseOrder->status === 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 dark:text-gray-200 @endif">
                                {{ ucfirst($purchaseOrder->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium border-b pb-2 mb-4">Items Ordered</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Item</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Quantity</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Unit Price</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($purchaseOrder->items as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $item->inventoryItem->item_name }}
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $item->inventoryItem->brand }} {{ $item->inventoryItem->model }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-400">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-400">
                                        {{ number_format($item->unit_price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900 dark:text-gray-100">
                                        {{ number_format($item->total_price, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                @if($purchaseOrder->transport_charges > 0 || $purchaseOrder->customs_charges > 0 || $purchaseOrder->other_charges > 0)
                                    <tr class="bg-gray-50 dark:bg-gray-900 border-t">
                                        <td colspan="3" class="px-6 py-2 text-right text-gray-700 dark:text-gray-300">Subtotal:</td>
                                        <td class="px-6 py-2 text-right text-gray-900 dark:text-gray-100">
                                            QAR {{ number_format($purchaseOrder->items->sum('total_price'), 2) }}
                                        </td>
                                    </tr>
                                    @if($purchaseOrder->transport_charges > 0)
                                    <tr class="bg-gray-50 dark:bg-gray-900">
                                        <td colspan="3" class="px-6 py-2 text-right text-gray-500 dark:text-gray-400">Transport:</td>
                                        <td class="px-6 py-2 text-right text-gray-700 dark:text-gray-300">QAR {{ number_format($purchaseOrder->transport_charges, 2) }}</td>
                                    </tr>
                                    @endif
                                    @if($purchaseOrder->customs_charges > 0)
                                    <tr class="bg-gray-50 dark:bg-gray-900">
                                        <td colspan="3" class="px-6 py-2 text-right text-gray-500 dark:text-gray-400">Customs:</td>
                                        <td class="px-6 py-2 text-right text-gray-700 dark:text-gray-300">QAR {{ number_format($purchaseOrder->customs_charges, 2) }}</td>
                                    </tr>
                                    @endif
                                    @if($purchaseOrder->other_charges > 0)
                                    <tr class="bg-gray-50 dark:bg-gray-900">
                                        <td colspan="3" class="px-6 py-2 text-right text-gray-500 dark:text-gray-400">Other:</td>
                                        <td class="px-6 py-2 text-right text-gray-700 dark:text-gray-300">QAR {{ number_format($purchaseOrder->other_charges, 2) }}</td>
                                    </tr>
                                    @endif
                                @endif
                                <tr class="bg-gray-50 dark:bg-gray-900 border-t-2 border-gray-200">
                                    <td colspan="3" class="px-6 py-4 text-right font-bold text-gray-900 dark:text-gray-100">Grand Total:</td>
                                    <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-gray-100">
                                        QAR {{ number_format($purchaseOrder->total_amount, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    @if($purchaseOrder->notes)
                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Notes</h4>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700 p-3 rounded-md">
                            {{ $purchaseOrder->notes }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
