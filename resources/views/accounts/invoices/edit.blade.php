<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Invoice') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="invoiceForm()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <form action="{{ route('accounts.invoices.update', $invoice) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Header Details -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <x-input-label for="invoice_number" :value="__('Invoice Number')" />
                                <div class="flex">
                                    <x-text-input id="invoice_number" class="block mt-1 w-full bg-gray-100 rounded-r-none" type="text" name="invoice_number" value="{{ $invoice->invoice_number }}" readonly />
                                    @if($invoice->service_job_id && \App\Models\ServiceReport::where('service_job_id', $invoice->service_job_id)->exists())
                                        @php $reportId = \App\Models\ServiceReport::where('service_job_id', $invoice->service_job_id)->latest()->first()->id; @endphp
                                        <a href="{{ route('engineer.reports.show', $reportId) }}" target="_blank" class="mt-1 inline-flex items-center px-3 border border-l-0 border-gray-300 bg-indigo-50 text-gray-500 dark:text-gray-400 text-sm rounded-r-md hover:text-indigo-600 hover:bg-indigo-100 transition" title="View Service Report">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            
                            <div>
                                <x-input-label for="date" :value="__('Date')" />
                                <x-text-input id="date" class="block mt-1 w-full" type="date" name="date" value="{{ $invoice->date->format('Y-m-d') }}" required />
                            </div>
                            
                            <div>
                                <x-input-label for="due_date" :value="__('Due Date')" />
                                <x-text-input id="due_date" class="block mt-1 w-full" type="date" name="due_date" value="{{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '' }}" />
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Invoice Items</h3>
                            <table class="min-w-full divide-y divide-gray-200 border">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Description</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-24">Qty</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-32">Unit Price</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-32">Total</th>
                                        <th class="px-4 py-2 w-10"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr>
                                            <td class="px-4 py-2">
                                                <input type="text" :name="'items['+index+'][description]'" x-model="item.description" class="w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="number" step="0.01" :name="'items['+index+'][quantity]'" x-model="item.quantity" @input="calculateTotal()" class="w-full border-gray-300 rounded-md shadow-sm text-right text-sm" required>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="number" step="0.01" :name="'items['+index+'][unit_price]'" x-model="item.unit_price" @input="calculateTotal()" class="w-full border-gray-300 rounded-md shadow-sm text-right text-sm" required>
                                            </td>
                                            <td class="px-4 py-2 text-right text-sm font-medium">
                                                <span x-text="(item.quantity * item.unit_price).toFixed(2)"></span>
                                            </td>
                                            <td class="px-4 py-2 text-center">
                                                <button type="button" @click="removeItem(index)" class="text-red-600 hover:text-red-900" x-show="items.length > 1">&times;</button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                            <button type="button" @click="addItem()" class="mt-2 text-sm text-blue-600 hover:text-blue-900 font-medium">+ Add Item</button>
                        </div>

                        <!-- Computations -->
                        <div class="flex justify-end">
                            <div class="w-full md:w-1/3 bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <div class="flex justify-between mb-2">
                                    <span class="text-gray-600 dark:text-gray-300">Subtotal:</span>
                                    <span class="font-semibold text-gray-900 dark:text-gray-100" x-text="subtotal.toFixed(2)"></span>
                                    <input type="hidden" name="subtotal" :value="subtotal">
                                </div>
                                <div class="flex justify-between mb-2 items-center">
                                    <span class="text-gray-600 dark:text-gray-300">Discount:</span>
                                    <input type="number" step="0.01" name="discount" x-model="discount" @input="calculateTotal()" class="w-24 border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm text-right text-sm p-1">
                                </div>
                                <div class="flex justify-between mb-2 items-center">
                                    <span class="text-gray-600 dark:text-gray-300">VAT Amount:</span>
                                    <input type="number" step="0.01" name="tax_amount" x-model="taxAmount" @input="calculateTotal()" class="w-24 border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm text-right text-sm p-1">
                                </div>
                                <div class="flex justify-between border-t dark:border-gray-600 pt-2 mt-2">
                                    <span class="text-lg font-bold text-gray-800 dark:text-white">Total:</span>
                                    <span class="text-lg font-bold text-blue-600 dark:text-blue-400" x-text="grandTotal.toFixed(2)"></span>
                                    <input type="hidden" name="total_amount" :value="grandTotal">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Notes -->
                        <div class="mt-6">
                            <x-input-label for="notes" :value="__('Notes / Terms')" />
                            <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">{{ $invoice->notes }}</textarea>
                        </div>

                        <div class="flex items-center justify-between mt-6">
                            <button type="button" 
                                onclick="if(confirm('Are you sure you want to delete this draft invoice?')) { document.getElementById('delete-invoice-form').submit(); }"
                                class="text-red-600 hover:text-red-900 font-semibold text-sm">
                                Delete Invoice
                            </button>

                            <x-primary-button>
                                {{ __('Update Invoice') }}
                            </x-primary-button>
                        </div>
                    </form>
                    
                    <form id="delete-invoice-form" action="{{ route('accounts.invoices.destroy', $invoice) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        function invoiceForm() {
            return {
                items: @json($invoice->items),
                subtotal: {{ $invoice->subtotal }},
                discount: {{ $invoice->discount ?? 0 }},
                taxAmount: {{ $invoice->tax_amount }},
                grandTotal: {{ $invoice->total_amount }},
                
                addItem() {
                    this.items.push({ description: '', quantity: 1, unit_price: 0 });
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                    this.calculateTotal();
                },
                calculateTotal() {
                    this.subtotal = this.items.reduce((sum, item) => {
                        return sum + (parseFloat(item.quantity || 0) * parseFloat(item.unit_price || 0));
                    }, 0);
                    let taxable = parseFloat(this.subtotal) - parseFloat(this.discount || 0);
                    if(taxable < 0) taxable = 0;
                    this.grandTotal = taxable + parseFloat(this.taxAmount || 0);
                }
            }
        }
    </script>
</x-app-layout>
