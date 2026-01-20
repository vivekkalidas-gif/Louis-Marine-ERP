<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create New Invoice') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="invoiceForm()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('admin.invoices.store') }}" method="POST">
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
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Invoice Date</label>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" class="shadow border rounded w-full py-2 px-3" required>
                        </div>
                         <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Due Date</label>
                            <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}" class="shadow border rounded w-full py-2 px-3" required>
                        </div>
                    </div>
                </div>

                <!-- Items -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-4">Line Items</h3>
                        <table class="min-w-full divide-y divide-gray-200 mb-4">
                            <thead>
                                <tr>
                                    <th class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-64">Product (Optional)</th>
                                    <th class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Description</th>
                                    <th class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-24">Qty</th>
                                    <th class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-32">Unit Price</th>
                                    <th class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-32">Total</th>
                                    <th class="w-10"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, index) in items" :key="index">
                                    <tr x-init="$watch('item.product_id', value => updateLineFromProduct(index, value))">
                                        <td class="p-2">
                                            <x-searchable-select 
                                                :options="$inventoryItems->map(fn($i) => ['value' => $i->id, 'label' => $i->item_name])->values()->all()"
                                                name="ignore" 
                                                x-model="item.product_id"
                                                @input="selectProduct(index, $event.detail)"
                                                placeholder="Select Product"
                                            />
                                        </td>
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
                                Create Invoice
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <script>
        function invoiceForm() {
            return {
                items: [{ product_id: '', description: '', quantity: 1, unit_price: 0 }],
                inventoryItems: @js($inventoryItems),
                taxExempt: false,
                subtotal: 0,
                tax: 0,
                total: 0,
                
                addItem() {
                    this.items.push({ product_id: '', description: '', quantity: 1, unit_price: 0 });
                },
                
                removeItem(index) {
                    if(this.items.length > 1) {
                        this.items.splice(index, 1);
                        this.calculateTotal();
                    }
                },
                
                selectProduct(index, productId) {
                   // This is triggered by x-model watching, but since x-searchable-select updates the model...
                   // Actually x-model handles the update. We need to watch the change.
                   // Or simpler: The component doesn't emit 'input' by default since I removed it?
                   // Wait, I removed $dispatch('input') in favor of x-modelable.
                   // So I should use $watch in the init() or loop?
                   // Cannot easily use $watch on array items in Alpine v2/v3 without init loop.
                   // EASIER: Just use @change or x-effect?
                   // The component updates `item.product_id`.
                   // Let's use a watcher on the item itself? No.
                   // Let's re-add @input dispatch to the component? No, I want to use standard events.
                   // Let's rely on `x-effect` inside the data component or just call a method `updateLineItem(index)` on change.
                   // Problem: `x-modelable` syncs value. It doesn't fire an event on the *parent* automatically unless I bind `@input` or `@change` on the component root?
                   // My component has `x-modelable="selected"`.
                   
                   // Let's just find the item based on the ID.
                   // I will assume `item.product_id` is updated.
                   // I will use x-init="$watch('item.product_id', value => selectProduct(index, value))" on the TR.
                },

                updateLineFromProduct(index, productId) {
                    const product = this.inventoryItems.find(p => p.id == productId);
                    if (product) {
                        this.items[index].description = product.item_name;
                        this.items[index].unit_price = product.selling_price;
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
