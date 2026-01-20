<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Quotation Details: {{ $quotation->supplier->company_name ?? $quotation->supplier->name }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('storekeeper.rfq.quotations.edit', [$rfq, $quotation]) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded shadow">
                    Edit
                </a>
                <a href="{{ route('storekeeper.rfq.show', $rfq) }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-100 font-medium">Back to RFQ</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">Financial Breakdown</h3>
                    
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-8 sm:grid-cols-3 big-dl">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Subtotal Amount</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ number_format($quotation->subtotal_amount, 2) }} {{ $rfq->currency->code ?? 'QAR' }}
                            </dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tax</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ number_format($quotation->tax_amount, 2) }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Exchange Rate</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $quotation->exchange_rate }}</dd>
                        </div>
                        
                        @if($rfq->type == 'overseas')
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Shipping Cost</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ number_format($quotation->shipping_cost, 2) }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Customs Duty</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ number_format($quotation->customs_duty, 2) }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Other Costs</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ number_format($quotation->other_costs, 2) }}</dd>
                        </div>
                        @endif

                        <div class="sm:col-span-1 bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                            <dt class="text-sm font-bold text-gray-700 dark:text-gray-300">Total Foreign</dt>
                            <dd class="mt-1 text-xl font-bold text-indigo-600">
                                {{ number_format($quotation->total_amount_foreign, 2) }} {{ $rfq->currency->code ?? 'QAR' }}
                            </dd>
                        </div>
                        <div class="sm:col-span-1 bg-green-50 p-4 rounded-md">
                            <dt class="text-sm font-bold text-gray-700 dark:text-gray-300">Total Landed (Local)</dt>
                            <dd class="mt-1 text-xl font-bold text-green-700">
                                {{ number_format($quotation->total_amount_local, 2) }} QAR
                            </dd>
                        </div>
                    </dl>

                    <div class="border-t border-gray-200 mt-8 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Terms & Conditions</h3>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-8">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Lead Time</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $quotation->lead_time ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Payment Terms</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $quotation->payment_terms ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Warranty</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $quotation->warranty ?? 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
