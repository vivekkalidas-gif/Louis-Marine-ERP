<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                RFQ: {{ $rfq->reference_number }} 
                <span class="ml-2 text-sm px-2 py-1 bg-gray-200 rounded-full text-gray-700 dark:text-gray-300">{{ strtoupper($rfq->status) }}</span>
            </h2>
            <div class="space-x-2">
                @if($rfq->status == 'draft')
                <form action="{{ route('storekeeper.rfq.status', $rfq) }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="status" value="sent">
                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded shadow">
                        Mark as Sent
                    </button>
                </form>
                @endif
                <a href="{{ route('storekeeper.rfq.preview', $rfq) }}" target="_blank" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded shadow">
                    Preview PDF
                </a>
                <a href="{{ route('storekeeper.rfq.pdf', $rfq) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow">
                    Download PDF
                </a>
                <a href="{{ route('storekeeper.rfq.edit', $rfq) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow">
                    Edit RFQ
                </a>
                <a href="{{ route('storekeeper.rfq.index') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-100 font-medium">Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Details Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">RFQ Details</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span class="block text-gray-500 dark:text-gray-400">Type</span>
                            <span class="font-semibold">{{ ucfirst($rfq->type) }}</span>
                        </div>
                        <div>
                            <span class="block text-gray-500 dark:text-gray-400">Deadline</span>
                            <span class="font-semibold">{{ $rfq->deadline_date ? $rfq->deadline_date->format('d M Y') : '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-gray-500 dark:text-gray-400">Currency</span>
                            <span class="font-semibold">{{ $rfq->currency->code ?? 'QAR (Local)' }}</span>
                        </div>
                        <div>
                            <span class="block text-gray-500 dark:text-gray-400">Incoterm</span>
                            <span class="font-semibold">{{ $rfq->incoterm ?? '-' }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="block text-gray-500 dark:text-gray-400">Delivery Location</span>
                            <span class="font-semibold">{{ $rfq->delivery_location ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Requested Items -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Items Required</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Item Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Unit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Linked Request</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                            @foreach($rfq->items as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    @if($item->product)
                                        <div class="font-medium">{{ $item->product->item_name }}</div>
                                        @if($item->product->model)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Model: {{ $item->product->model }}</div>
                                        @endif
                                    @else
                                        <span class="text-gray-400 text-xs italic">Custom Item</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $item->description }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->unit }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->material_request_id ? 'MR #'.$item->material_request_id : '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Comparison Matrix / Quotations -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Supplier Quotations</h3>
                        <button onclick="document.getElementById('addQuoteModal').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-medium">
                            + Add Quote
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Supplier</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase">Subtotal (Foreign)</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase">Landed Cost ({{ $rfq->type == 'local' ? 'QAR' : 'Base' }})</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Lead Time</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Payment Terms</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase">Selection</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                                @forelse($rfq->quotations as $quote)
                                <tr class="{{ $quote->is_selected ? 'bg-green-50' : '' }}">
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $quote->supplier->company_name ?? $quote->supplier->name }}
                                        @if($quote->is_selected) <span class="text-green-600 text-xs ml-2">(Selected)</span> @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">
                                        {{ number_format($quote->total_amount_foreign, 2) }} 
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $rfq->currency->code ?? 'QAR' }}</span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-right font-bold text-gray-900 dark:text-gray-100">
                                        {{ number_format($quote->total_amount_local, 2) }} QAR
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $quote->lead_time }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $quote->payment_terms }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        @if(!$quote->is_selected)
                                        <form action="{{ route('storekeeper.rfq.quotations.select', [$rfq, $quote]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-indigo-600 hover:text-indigo-900">Select</button>
                                        </form>
                                        @else
                                        <span class="text-green-600 font-bold block mb-1">✓ Winner</span>
                                        <a href="{{ route('storekeeper.purchase-orders.create', ['rfq_id' => $rfq->id, 'quotation_id' => $quote->id]) }}" class="text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded hover:bg-indigo-200">Convert to PO</a>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex justify-center items-center space-x-3">
                                            <a href="{{ route('storekeeper.rfq.quotations.show', [$rfq, $quote]) }}" class="text-blue-600 hover:text-blue-900" title="View">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </a>
                                            <a href="{{ route('storekeeper.rfq.quotations.edit', [$rfq, $quote]) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 00-2 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>
                                            <form action="{{ route('storekeeper.rfq.quotations.destroy', [$rfq, $quote]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this quote?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No quotations received yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Add Quote Modal -->
    <div id="addQuoteModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-50 dark:bg-gray-700 bg-opacity-75 transition-opacity" onclick="document.getElementById('addQuoteModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('storekeeper.rfq.quotations.store', $rfq) }}" method="POST">
                    @csrf
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">Enter Supplier Quotation</h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Supplier</label>
                                <select name="supplier_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    @foreach($rfq->suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->company_name ?? $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- If Overseas, show exchange rate -->
                            @if($rfq->type == 'overseas')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Exchange Rate (to QAR)</label>
                                <input type="number" step="0.000001" name="exchange_rate" value="{{ $rfq->currency->exchange_rate ?? 1.0 }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            </div>
                            @endif

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subtotal ({{ $rfq->currency->code ?? 'QAR' }})</label>
                                    <input type="number" step="0.01" name="subtotal_amount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tax</label>
                                    <input type="number" step="0.01" name="tax_amount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="0">
                                </div>
                            </div>
                            
                            @if($rfq->type == 'overseas')
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Shipping Cost</label>
                                    <input type="number" step="0.01" name="shipping_cost" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="0">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Customs Duty</label>
                                    <input type="number" step="0.01" name="customs_duty" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="0">
                                </div>
                            </div>
                            @endif

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lead Time</label>
                                <input type="text" name="lead_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="e.g. 14 Days">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Terms</label>
                                <input type="text" name="payment_terms" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="e.g. 50% Advance">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Save Quote
                        </button>
                        <button type="button" onclick="document.getElementById('addQuoteModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>
