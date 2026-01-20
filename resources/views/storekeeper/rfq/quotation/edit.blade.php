<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit Quotation: {{ $quotation->supplier->company_name ?? $quotation->supplier->name }}
            </h2>
            <a href="{{ route('storekeeper.rfq.show', $rfq) }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-100 font-medium">Back to RFQ</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('storekeeper.rfq.quotations.update', [$rfq, $quotation]) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">Quotation Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <!-- If Overseas, show exchange rate -->
                            @if($rfq->type == 'overseas')
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Exchange Rate (to QAR)</label>
                                <input type="number" step="0.000001" name="exchange_rate" value="{{ old('exchange_rate', $quotation->exchange_rate) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            </div>
                            @endif

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subtotal ({{ $rfq->currency->code ?? 'QAR' }})</label>
                                <input type="number" step="0.01" name="subtotal_amount" value="{{ old('subtotal_amount', $quotation->subtotal_amount) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tax</label>
                                <input type="number" step="0.01" name="tax_amount" value="{{ old('tax_amount', $quotation->tax_amount) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            
                            @if($rfq->type == 'overseas')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Shipping Cost</label>
                                <input type="number" step="0.01" name="shipping_cost" value="{{ old('shipping_cost', $quotation->shipping_cost) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Customs Duty</label>
                                <input type="number" step="0.01" name="customs_duty" value="{{ old('customs_duty', $quotation->customs_duty) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            @endif

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lead Time</label>
                                <input type="text" name="lead_time" value="{{ old('lead_time', $quotation->lead_time) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Terms</label>
                                <input type="text" name="payment_terms" value="{{ old('payment_terms', $quotation->payment_terms) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                        </div>
                        
                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded shadow-lg">
                                Update Quotation
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
