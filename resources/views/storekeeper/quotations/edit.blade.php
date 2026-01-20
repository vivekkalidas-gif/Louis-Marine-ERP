<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Quotation') }} #{{ $quotation->quotation_number }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="quotationManager({{ $quotation->items->toJson() }}, {{ $items->toJson() }})">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('storekeeper.quotations.update', $quotation->id) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Header Information -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="customer_id" :value="__('Customer')" />
                            <select id="customer_id" name="customer_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="">-- Select Customer --</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ $quotation->customer_id == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->company_name }}
                                </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="quotation_date" :value="__('Date')" />
                            <x-text-input id="quotation_date" class="block mt-1 w-full" type="date" name="quotation_date" :value="old('quotation_date', $quotation->date->format('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('quotation_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="reference_number" :value="__('Reference Number')" />
                            <x-text-input id="reference_number" class="block mt-1 w-full" type="text" name="reference_number" :value="old('reference_number', $quotation->reference_number)" />
                            <x-input-error :messages="$errors->get('reference_number')" class="mt-2" />
                        </div>
                        
                        <div>
                            <x-input-label for="valid_until" :value="__('Valid Until')" />
                            <x-text-input id="valid_until" class="block mt-1 w-full" type="date" name="valid_until" :value="old('valid_until', $quotation->valid_until ? $quotation->valid_until->format('Y-m-d') : '')" />
                            <x-input-error :messages="$errors->get('valid_until')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="draft" {{ $quotation->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="sent" {{ $quotation->status == 'sent' ? 'selected' : '' }}>Sent</option>
                                <option value="approved" {{ $quotation->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ $quotation->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Line Items -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Line Items</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/4">Item</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/3">Description</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-24">Qty</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-32">Unit Price</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-32">Total</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-12"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <template x-for="(line, index) in lines" :key="index">
                                        <tr>
                                            <td class="px-4 py-2">
                                                <select :name="'items['+index+'][inventory_item_id]'" x-model="line.inventory_item_id" @change="updateItemDetails(index)" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm sm:text-sm">
                                                    <option value="">-- Custom --</option>
                                                    <template x-for="inv in inventory" :key="inv.id">
                                                        <option :value="inv.id" x-text="inv.item_name"></option>
                                                    </template>
                                                </select>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="text" :name="'items['+index+'][description]'" x-model="line.description" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm sm:text-sm" required>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="number" :name="'items['+index+'][quantity]'" x-model="line.quantity" min="0.01" step="0.01" class="block w-24 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm sm:text-sm text-center" required>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="number" :name="'items['+index+'][unit_price]'" x-model="line.unit_price" min="0" step="0.01" class="block w-32 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm sm:text-sm text-right" required>
                                            </td>
                                            <td class="px-4 py-2 text-right font-bold text-gray-900 dark:text-gray-100">
                                                <span x-text="formatMoney(line.quantity * line.unit_price)"></span>
                                            </td>
                                            <td class="px-4 py-2 text-center">
                                                <button type="button" @click="removeLine(index)" class="text-red-600 hover:text-red-900">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6" class="px-4 py-4">
                                            <button type="button" @click="addLine()" class="flex items-center text-blue-600 hover:text-blue-900 font-bold">
                                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                Add Item
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="bg-gray-50 dark:bg-gray-700 font-bold text-gray-900 dark:text-gray-100">
                                        <td colspan="4" class="px-4 py-3 text-right">Total:</td>
                                        <td class="px-4 py-3 text-right" x-text="formatMoney(netTotal)"></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="flex justify-between items-center bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <div class="flex space-x-4">
                        <a href="{{ route('storekeeper.quotations.pdf', $quotation->id) }}" target="_blank" class="text-blue-600 hover:underline flex items-center">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            PDF
                        </a>
                        
                        @if($quotation->status === 'approved')
                        <a href="{{ route('storekeeper.quotations.delivery-note', $quotation->id) }}" target="_blank" class="text-green-600 hover:underline flex items-center">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Delivery Note
                        </a>
                        
                        <form action="{{ route('storekeeper.quotations.billing', $quotation->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" onclick="return confirm('Notify Finance to bill this quotation?')" class="text-indigo-600 hover:underline flex items-center">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Send for Billing
                            </button>
                        </form>
                        @endif
                    </div>
                    <div class="flex gap-4">
                        <a href="{{ route('storekeeper.quotations.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 transition ease-in-out duration-150">
                            Cancel
                        </a>
                        <x-primary-button>
                            {{ __('Save Changes') }}
                        </x-primary-button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function quotationManager(initialLines, inventoryItems) {
            return {
                lines: initialLines.length ? initialLines : [{ inventory_item_id: '', description: '', quantity: 1, unit_price: 0 }],
                inventory: inventoryItems,

                addLine() {
                    this.lines.push({ inventory_item_id: '', description: '', quantity: 1, unit_price: 0 });
                },

                removeLine(index) {
                    if (this.lines.length > 1) {
                        this.lines.splice(index, 1);
                    } else {
                        // Clear last line if it's the only one
                        this.lines[0] = { inventory_item_id: '', description: '', quantity: 1, unit_price: 0 };
                    }
                },

                updateItemDetails(index) {
                    const invId = this.lines[index].inventory_item_id;
                    const item = this.inventory.find(i => i.id === invId);
                    if (item) {
                        this.lines[index].description = item.item_name;
                        // this.lines[index].unit_price = item.price || 0; // If price exists
                    }
                },

                get netTotal() {
                    return this.lines.reduce((sum, line) => {
                        return sum + (parseFloat(line.quantity || 0) * parseFloat(line.unit_price || 0));
                    }, 0);
                },

                formatMoney(amount) {
                    return Number(amount).toLocaleString('en-US', { style: 'currency', currency: 'QAR' });
                }
            }
        }
    </script>
</x-app-layout>
