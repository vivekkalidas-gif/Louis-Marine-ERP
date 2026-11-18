<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Invoice: {{ $invoice->invoice_number }}
            </h2>
            <div class="space-x-2">
                @if($invoice->serviceJob)
                     <span class="text-xs text-gray-500 dark:text-gray-400 mr-2">Job: {{ $invoice->serviceJob->job_number }}</span>
                @endif
                <a href="{{ route('accounts.invoices.pdf', $invoice) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700">
                    Download PDF
                </a>
                @if($invoice->balance > 0 && $invoice->status !== 'draft')
                    <a href="{{ route('accounts.payments.create', ['invoice_id' => $invoice->id]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">
                        Record Payment
                    </a>
                @endif
                @if($invoice->status === 'draft')
                    <a href="{{ route('accounts.invoices.edit', $invoice) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                        Edit
                    </a>
                    
                    <form action="{{ route('accounts.invoices.mark-sent', $invoice) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure? This will finalize the invoice and add it to the ledger.');">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 ml-2">
                            Finalize & Send
                        </button>
                    </form>

                    <form action="{{ route('accounts.invoices.destroy', $invoice) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this draft invoice?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 ml-2">
                            Delete
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Details Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-bold mb-2">Customer Details</h3>
                            <p class="font-semibold">{{ $invoice->customer->company_name }}</p>
                            <p>{{ $invoice->customer->address }}</p>
                            <p>Phone: {{ $invoice->customer->phone }}</p>
                        </div>
                        <div class="text-right">
                            <h3 class="text-lg font-bold mb-2">Invoice Info</h3>
                            <p>Date: {{ $invoice->date->format('d/m/Y') }}</p>
                            <p>Due Date: {{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : 'N/A' }}</p>
                            <p>Status: <span class="uppercase font-bold">{{ $invoice->status }}</span></p>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <h3 class="text-lg font-bold mb-4">Line Items</h3>
                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-2 text-left">Description</th>
                                    <th class="px-4 py-2 text-right">Qty</th>
                                    <th class="px-4 py-2 text-right">Price</th>
                                    <th class="px-4 py-2 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->items as $item)
                                <tr>
                                    <td class="px-4 py-2">{{ $item->description }}</td>
                                    <td class="px-4 py-2 text-right">{{ $item->quantity + 0 }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($item->amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 dark:bg-gray-700 font-bold">
                                <tr>
                                    <td colspan="3" class="px-4 py-2 text-right">Subtotal</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($invoice->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="px-4 py-2 text-right">Discount</td>
                                    <td class="px-4 py-2 text-right text-red-600">-{{ number_format($invoice->discount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="px-4 py-2 text-right">Tax</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($invoice->tax_amount, 2) }}</td>
                                </tr>
                                <tr class="bg-gray-100 text-lg">
                                    <td colspan="3" class="px-4 py-2 text-right">Total</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($invoice->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Payments Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4">Payment History</h3>
                    
                    @if($invoice->payments->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-2 text-left">Date</th>
                                    <th class="px-4 py-2 text-left">Reference</th>
                                    <th class="px-4 py-2 text-right">Amount</th>
                                    <th class="px-4 py-2 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->payments as $payment)
                                <tr>
                                    <td class="px-4 py-2">{{ $payment->payment_date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2">
                                        {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                        <span class="text-gray-500 dark:text-gray-400 text-xs">({{ $payment->reference_number ?? '-' }})</span>
                                    </td>
                                    <td class="px-4 py-2 text-right">{{ number_format($payment->amount, 2) }}</td>
                                    <td class="px-4 py-2 text-right">
                                        <a href="{{ route('accounts.payments.receipt', $payment) }}" target="_blank" class="text-blue-600 hover:text-blue-900">Receipt</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 italic">No payments recorded yet.</p>
                    @endif

                    <div class="mt-4 border-t pt-4 flex justify-between items-center">
                        <div>
                            <span class="text-gray-600">Total Paid:</span>
                            <span class="font-bold text-green-600 ml-2">{{ number_format($invoice->paid_amount, 2) }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Balance Due:</span>
                            <span class="font-bold text-red-600 ml-2 text-xl">{{ number_format($invoice->balance, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
