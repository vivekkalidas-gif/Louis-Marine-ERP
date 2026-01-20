<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create New Invoice') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="invoiceForm()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <form action="{{ route('accounts.invoices.store') }}" method="POST">
                        @csrf
                        
                        <!-- Error Display -->
                        <x-alert-error class="mb-4" />

                        @if($quotation)
                            <input type="hidden" name="quotation_id" value="{{ $quotation->id }}">
                            <div class="bg-blue-50 dark:bg-blue-900/50 border-l-4 border-blue-400 p-4 mb-4">
                                <div class="flex">
                                    <div class="ml-3">
                                        <p class="text-sm text-blue-700 dark:text-blue-300">
                                            Creating Invoice from Quotation: <span class="font-bold">{{ $quotation->quotation_number }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Header Details -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <!-- Invoice # -->
                            <div>
                                <x-input-label for="invoice_number" :value="__('Invoice Number')" />
                                <x-text-input id="invoice_number" class="block mt-1 w-full dark:bg-gray-900" type="text" name="invoice_number" value="{{ $invoiceNumber }}" required readonly />
                            </div>
                            
                            <!-- Date -->
                            <div>
                                <x-input-label for="date" :value="__('Date')" />
                                <x-text-input id="date" class="block mt-1 w-full dark:bg-gray-900" type="date" name="date" value="{{ date('Y-m-d') }}" required />
                            </div>
                            
                            <!-- Due Date -->
                            <div>
                                <x-input-label for="due_date" :value="__('Due Date')" />
                                <x-text-input id="due_date" class="block mt-1 w-full dark:bg-gray-900" type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Customer -->
                            <div>
                                <x-input-label for="customer_id" :value="__('Customer')" />
                                <select id="customer_id" name="customer_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required x-model="customerId" @change="loadJobs()">
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Job Link (Optional) -->
                            <div>
                                <x-input-label for="service_job_id" :value="__('Link to Job (Optional)')" />
                                <select id="service_job_id" name="service_job_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" x-model="jobId">
                                    <option value="">None</option>
                                    @foreach($jobs as $job)
                                        <option value="{{ $job->id }}" data-customer="{{ $job->customer_id }}">
                                            JOB-{{ $job->job_number }} ({{ $job->vessel->name ?? 'No Vessel' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Hidden Vessel Input -->
                            <input type="hidden" name="vessel_id" x-model="vesselId">
                        </div>

                        <!-- Items Table -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Invoice Items</h3>
                            <div class="overflow-x-auto border dark:border-gray-700 rounded-md">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Description</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-24">Qty</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-32">Unit Price</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-32">Total</th>
                                            <th class="px-4 py-2 w-10"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <template x-for="(item, index) in items" :key="index">
                                            <tr>
                                                <td class="px-4 py-2">
                                                    <input type="text" :name="'items['+index+'][description]'" x-model="item.description" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm text-sm" required placeholder="Item description">
                                                </td>
                                                <td class="px-4 py-2">
                                                    <input type="number" step="0.01" :name="'items['+index+'][quantity]'" x-model="item.quantity" @input="calculateTotal()" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm text-right text-sm" required>
                                                </td>
                                                <td class="px-4 py-2">
                                                    <input type="number" step="0.01" :name="'items['+index+'][unit_price]'" x-model="item.unit_price" @input="calculateTotal()" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm text-right text-sm" required>
                                                </td>
                                                <td class="px-4 py-2 text-right text-sm font-medium text-gray-900 dark:text-gray-300">
                                                    <span x-text="(item.quantity * item.unit_price).toFixed(2)"></span>
                                                </td>
                                                <td class="px-4 py-2 text-center">
                                                    <button type="button" @click="removeItem(index)" class="text-red-600 hover:text-red-900 dark:hover:text-red-400" x-show="items.length > 1">&times;</button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" @click="addItem()" class="mt-2 text-sm text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 font-medium">+ Add Item</button>
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
                            <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm"></textarea>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button class="ml-4 bg-blue-600 hover:bg-blue-700 text-white border-transparent">
                                {{ __('Create Invoice') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        function invoiceForm() {
            return {
                customerId: '{{ $quotation ? $quotation->customer_id : ($selectedJob ? $selectedJob->customer_id : "") }}',
                jobId: '{{ $selectedJob ? $selectedJob->id : "" }}',
                vesselId: '{{ $selectedJob ? $selectedJob->vessel_id : "" }}',
                items: @json($initialItems),
                subtotal: {{ $quotation ? $quotation->subtotal : 0 }},
                discount: 0,
                taxAmount: {{ $quotation ? $quotation->tax : 0 }},
                grandTotal: {{ $quotation ? $quotation->total : 0 }},
                
                init() {
                    this.calculateTotal();
                },
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
                    // Total = Subtotal - Discount + Tax
                    // Assuming Tax is manual input amount, or should it be calculated? Currently manual.
                    // Assuming Discount is manual amount.
                    let taxable = parseFloat(this.subtotal) - parseFloat(this.discount || 0);
                    if(taxable < 0) taxable = 0;
                    
                    this.grandTotal = taxable + parseFloat(this.taxAmount || 0);
                },
                loadJobs() {
                    // Optional: Filter jobs dropdown
                }
            }
        }
    </script>
</x-app-layout>
