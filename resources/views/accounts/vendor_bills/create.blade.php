@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="itemManager()">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">New Vendor Bill</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Record a new bill from a vendor.</p>
        </div>
        <a href="{{ route('accounts.vendor-bills.index') }}" class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-100">
            &larr; Back to Bills
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('accounts.vendor-bills.store') }}" method="POST">
        @csrf
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Header Info -->
            <div class="p-6 grid grid-cols-1 md:grid-cols-4 gap-6 bg-gray-50 dark:bg-gray-700 border-b border-gray-100">
                <div class="col-span-1 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vendor <span class="text-red-500">*</span></label>
                    <select name="vendor_id" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        <option value="">Select Vendor</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ (isset($prefilledData['vendor_id']) && $prefilledData['vendor_id'] == $vendor->id) ? 'selected' : '' }}>{{ $vendor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bill Reference <span class="text-red-500">*</span></label>
                    <input type="text" name="bill_number" required value="{{ $prefilledData['bill_number'] ?? '' }}" placeholder="e.g. V-202X-001" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bill Date <span class="text-red-500">*</span></label>
                    <input type="date" name="bill_date" required value="{{ date('Y-m-d') }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Due Date <span class="text-red-500">*</span></label>
                    <input type="date" name="due_date" required value="{{ date('Y-m-d') }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>
            </div>

            <!-- Items -->
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Bill Items</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Account</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Description</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-24">Qty</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-32">Price</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-24">Tax %</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-32">Total</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template x-for="(item, index) in items" :key="index">
                                <tr>
                                    <td class="px-4 py-2">
                                        <select :name="`items[${index}][account_id]`" x-model="item.account_id" required class="w-full border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                            <option value="">Select Account</option>
                                            @foreach($accounts as $account)
                                                <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="text" :name="`items[${index}][description]`" x-model="item.description" class="w-full border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" step="0.01" :name="`items[${index}][quantity]`" x-model="item.quantity" @input="calculateTotal(index)" class="w-full border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm text-right">
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" step="0.01" :name="`items[${index}][unit_price]`" x-model="item.unit_price" @input="calculateTotal(index)" class="w-full border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm text-right">
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" step="0.01" :name="`items[${index}][tax_rate]`" x-model="item.tax_rate" @input="calculateTotal(index)" class="w-full border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm text-right">
                                    </td>
                                    <td class="px-4 py-2 text-right font-medium text-gray-900 dark:text-gray-100">
                                        <span x-text="formatNumber(item.total)"></span>
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <button type="button" @click="removeItem(index)" class="text-red-600 hover:text-red-900">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <td colspan="7" class="px-4 py-3">
                                    <button type="button" @click="addItem()" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">+ Add Line Item</button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" class="px-4 py-2 text-right font-medium text-gray-600">Subtotal:</td>
                                <td class="px-4 py-2 text-right font-medium text-gray-900 dark:text-gray-100" x-text="formatNumber(subTotal)"></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="px-4 py-2 text-right font-medium text-gray-600">Tax Total:</td>
                                <td class="px-4 py-2 text-right font-medium text-gray-900 dark:text-gray-100" x-text="formatNumber(taxTotal)"></td>
                                <td></td>
                            </tr>
                            <tr class="bg-indigo-50">
                                <td colspan="5" class="px-4 py-3 text-right font-bold text-indigo-900">Grand Total:</td>
                                <td class="px-4 py-3 text-right font-bold text-indigo-900" x-text="formatNumber(grandTotal)"></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Notes -->
            <div class="px-6 pb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">{{ $prefilledData['notes'] ?? '' }}</textarea>
                @if(isset($prefilledData['purchase_order_id']))
                    <input type="hidden" name="purchase_order_id" value="{{ $prefilledData['purchase_order_id'] }}">
                @endif
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex items-center justify-end gap-3 border-t border-gray-100">
                <a href="{{ route('accounts.vendor-bills.index') }}" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 text-sm font-medium">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-sm text-sm font-medium transition-colors">Save as Draft</button>
            </div>
        </div>
    </form>
</div>

<script>
    function itemManager() {
        return {
            @php
                $defaultItems = [['account_id' => '', 'description' => '', 'quantity' => 1, 'unit_price' => 0, 'tax_rate' => 0, 'total' => 0]];
            @endphp
            items: @json($prefilledData['items'] ?? $defaultItems),
            
            get subTotal() {
                return this.items.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
            },
            
            get taxTotal() {
                return this.items.reduce((sum, item) => sum + ((item.quantity * item.unit_price) * (item.tax_rate / 100)), 0);
            },
            
            get grandTotal() {
                return this.subTotal + this.taxTotal;
            },
            
            addItem() {
                this.items.push({ account_id: '', description: '', quantity: 1, unit_price: 0, tax_rate: 0, total: 0 });
            },
            
            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            },
            
            calculateTotal(index) {
                let item = this.items[index];
                let sub = item.quantity * item.unit_price;
                item.total = sub + (sub * (item.tax_rate / 100));
            },
            
            formatNumber(num) {
                return new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(num);
            }
        }
    }
</script>
@endsection
