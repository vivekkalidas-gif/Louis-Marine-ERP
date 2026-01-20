<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create New Quotation') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="quotationForm()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('admin.quotations.store') }}" method="POST">
                @csrf
                
                <!-- Header Info -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Customer</label>
                            <select name="customer_id" class="shadow border rounded w-full py-2 px-3" required>
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                         <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Link to Job (Optional)</label>
                            <select name="service_job_id" class="shadow border rounded w-full py-2 px-3">
                                <option value="">None</option>
                                @foreach($jobs as $job)
                                    <option value="{{ $job->id }}">{{ $job->job_number }} ({{ $job->customer->company_name }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Quotation Date</label>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" class="shadow border rounded w-full py-2 px-3" required>
                        </div>
                         <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Valid Until</label>
                            <input type="date" name="valid_until" value="{{ date('Y-m-d', strtotime('+30 days')) }}" class="shadow border rounded w-full py-2 px-3" required>
                        </div>
                    </div>
                </div>

                <!-- Items -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-4">Quotation Items</h3>
                        <table class="min-w-full divide-y divide-gray-200 mb-4">
                            <thead>
                                <tr>
                                    <th class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Description</th>
                                    <th class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-24">Qty</th>
                                    <th class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-32">Unit Price</th>
                                    <th class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-32">Total</th>
                                    <th class="w-10"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, index) in items" :key="index">
                                    <tr>
                                        <td class="p-2">
                                            <input type="text" :name="'items['+index+'][description]'" x-model="item.description" class="w-full border rounded p-1" placeholder="Item description" required>
                                        </td>
                                        <td class="p-2">
                                            <input type="number" :name="'items['+index+'][quantity]'" x-model="item.quantity" @input="calculateTotal()" class="w-full border rounded p-1" step="0.01" required>
                                        </td>
                                        <td class="p-2">
                                            <input type="number" :name="'items['+index+'][unit_price]'" x-model="item.unit_price" @input="calculateTotal()" class="w-full border rounded p-1" step="0.01" required>
                                        </td>
                                        <td class="p-2 text-right font-mono" x-text="(item.quantity * item.unit_price).toFixed(2)"></td>
                                        <td class="p-2 text-center">
                                            <button type="button" @click="removeItem(index)" class="text-red-600 font-bold">&times;</button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <button type="button" @click="addItem()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 dark:text-gray-200 font-bold py-1 px-3 rounded text-sm">
                            + Add Item
                        </button>
                    </div>
                </div>

                <!-- Additional Info -->
                 <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Terms & Notes</label>
                        <textarea name="terms" rows="4" class="shadow border rounded w-full py-2 px-3" placeholder="Enter terms specifically for this quotation...">Validity: 30 Days.&#10;Payment Terms: 50% Advance, Balance on Completion.</textarea>
                    </div>
                </div>

                <!-- Summary -->
                <div class="flex justify-end">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 w-full md:w-1/3">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-bold" x-text="subtotal.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between mb-2 items-center">
                            <span class="text-gray-600">VAT (5%):</span>
                            <div>
                                <span class="font-bold" x-show="!taxExempt" x-text="tax.toFixed(2)"></span>
                                <span class="font-bold text-gray-400" x-show="taxExempt">0.00 (Exempt)</span>
                            </div>
                        </div>
                         <div class="mb-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="tax_exempt" x-model="taxExempt" @change="calculateTotal()" class="form-checkbox">
                                <span class="ml-2 text-gray-700 dark:text-gray-300 text-sm">Tax Exempt</span>
                            </label>
                        </div>
                        <div class="flex justify-between border-t pt-2 text-xl">
                            <span class="font-bold">Total:</span>
                            <span class="font-bold text-blue-600" x-text="total.toFixed(2)"></span>
                        </div>
                        <div class="mt-6">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded">
                                Create Quotation
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <script>
        function quotationForm() {
            return {
                items: [{ description: '', quantity: 1, unit_price: 0 }],
                taxExempt: false,
                subtotal: 0,
                tax: 0,
                total: 0,
                addItem() {
                    this.items.push({ description: '', quantity: 1, unit_price: 0 });
                },
                removeItem(index) {
                    if(this.items.length > 1) {
                        this.items.splice(index, 1);
                        this.calculateTotal();
                    }
                },
                calculateTotal() {
                    this.subtotal = this.items.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
                    this.tax = this.taxExempt ? 0 : (this.subtotal * 0.05);
                    this.total = this.subtotal + this.tax;
                }
            }
        }
    </script>
</x-app-layout>
