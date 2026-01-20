<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $invoice->invoice_number }}
            </h2>
             <div class="flex gap-2">
                <a href="{{ route('admin.invoices.print', $invoice) }}" target="_blank" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Print PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Invoice Details -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6 flex justify-between">
                            <div>
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Invoice Information</h3>
                                <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">Issued to {{ $invoice->customer->company_name }}</p>
                            </div>
                            <div class="text-right">
                                <span class="px-3 py-1 rounded-full text-sm font-bold {{ $invoice->status == 'PAID' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $invoice->status }}
                                </span>
                            </div>
                        </div>
                        <div class="border-t border-gray-200">
                            <dl>
                                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Invoice Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">{{ $invoice->date->format('d M Y') }}</dd>
                                </div>
                                <div class="bg-white dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Due Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">{{ $invoice->due_date->format('d M Y') }}</dd>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Related Job</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                                        @if($invoice->job)
                                            <a href="{{ route('admin.jobs.show', $invoice->job) }}" class="text-blue-600 hover:underline">{{ $invoice->job->job_number }}</a>
                                        @else
                                            <span class="text-gray-400">None</span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Line Items</h3>
                        </div>
                        <div class="border-t border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Description</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Qty</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Unit Price</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                                    @foreach($invoice->items as $item)
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $item->description }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100 text-right">{{ $item->quantity }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100 text-right">{{ number_format($item->unit_price, 2) }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100 text-right">{{ number_format($item->total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50 dark:bg-gray-700">
                                     <tr>
                                        <td colspan="3" class="px-6 py-3 text-right font-bold text-gray-500 dark:text-gray-400">Subtotal</td>
                                        <td class="px-6 py-3 text-right font-bold text-gray-900 dark:text-gray-100">{{ number_format($invoice->subtotal, 2) }}</td>
                                    </tr>
                                     <tr>
                                        <td colspan="3" class="px-6 py-3 text-right font-bold text-gray-500 dark:text-gray-400">VAT (5%)</td>
                                        <td class="px-6 py-3 text-right font-bold text-gray-900 dark:text-gray-100">{{ number_format($invoice->tax, 2) }}</td>
                                    </tr>
                                     <tr>
                                        <td colspan="3" class="px-6 py-3 text-right font-bold text-gray-900 dark:text-gray-100 text-lg">Total</td>
                                        <td class="px-6 py-3 text-right font-bold text-blue-600 text-lg">{{ number_format($invoice->total, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Payments & Actions -->
                <div class="space-y-6">
                    <!-- Payment History -->
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Payment History</h3>
                        <ul class="space-y-4 mb-6">
                            @forelse($invoice->payments as $payment)
                                <li class="flex justify-between border-b pb-2">
                                    <div>
                                        <div class="text-sm font-bold">{{ $payment->payment_date->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $payment->method }} - {{ $payment->reference }}</div>
                                    </div>
                                    <div class="font-bold text-green-600">
                                        {{ number_format($payment->amount, 2) }}
                                    </div>
                                </li>
                            @empty
                                <li class="text-gray-500 dark:text-gray-400 text-sm">No payments recorded.</li>
                            @endforelse
                        </ul>
                        
                        <div class="border-t pt-4">
                            <div class="flex justify-between text-sm mb-2">
                                <span>Total Paid:</span>
                                <span class="font-bold">{{ number_format($invoice->payments->sum('amount'), 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm mb-4">
                                <span>Balance Due:</span>
                                <span class="font-bold text-red-600">{{ number_format($invoice->total - $invoice->payments->sum('amount'), 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Record Payment Form -->
                    @if($invoice->status != 'PAID')
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Record Payment</h3>
                        <form action="{{ route('admin.invoices.payment', $invoice) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount</label>
                                <input type="number" name="amount" step="0.01" max="{{ $invoice->total - $invoice->payments->sum('amount') }}" value="{{ $invoice->total - $invoice->payments->sum('amount') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                                <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Method</label>
                                <select name="method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Cheque">Cheque</option>
                                    <option value="Cash">Cash</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reference #</label>
                                <input type="text" name="reference" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <button type="submit" class="w-full bg-green-600 text-white font-bold py-2 rounded hover:bg-green-700">
                                Record Payment
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
