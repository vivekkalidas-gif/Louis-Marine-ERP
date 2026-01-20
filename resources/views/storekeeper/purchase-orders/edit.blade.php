<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Purchase Order') }} - {{ $purchaseOrder->po_number }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="purchaseOrderForm()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('storekeeper.purchase-orders.update', $purchaseOrder) }}">
                @csrf
                @method('PUT')
                
                <!-- PO Header -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                             <div>
                                <x-input-label for="supplier_id" :value="__('Supplier')" />
                                <select id="supplier_id" name="supplier_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id', $purchaseOrder->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('supplier_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="po_number" :value="__('PO Number')" />
                                <x-text-input id="po_number" class="block mt-1 w-full bg-gray-100" type="text" name="po_number" :value="$purchaseOrder->po_number" required readonly />
                            </div>

                            <div>
                                <x-input-label for="po_date" :value="__('PO Date')" />
                                <x-text-input id="po_date" class="block mt-1 w-full" type="date" name="po_date" :value="old('po_date', $purchaseOrder->po_date->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('po_date')" class="mt-2" />
                            </div>

                             <div>
                                <x-input-label for="expected_delivery_date" :value="__('Expected Delivery')" />
                                <x-text-input id="expected_delivery_date" class="block mt-1 w-full" type="date" name="expected_delivery_date" :value="old('expected_delivery_date', $purchaseOrder->expected_delivery_date ? $purchaseOrder->expected_delivery_date->format('Y-m-d') : '')" />
                                <x-input-error :messages="$errors->get('expected_delivery_date')" class="mt-2" />
                            </div>
                            
                            <div class="md:col-span-2">
                                <x-input-label for="notes" :value="__('Notes')" />
                                <textarea id="notes" name="notes" rows="1" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('notes', $purchaseOrder->notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PO Items -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-medium border-b pb-2 mb-4">Order Items</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Item</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-32">Qty</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-32">Unit Price</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-32">Total</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-16"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr>
                                            <td class="px-4 py-2">
                                                <select :name="'items['+index+'][inventory_item_id]'" x-model="item.inventory_item_id" @change="updatePrice(index)" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm text-sm" required>
                                                    <option value="">Select Item</option>
                                                    @foreach($inventoryItems as $invItem)
                                                        <option value="{{ $invItem->id }}" data-price="{{ $invItem->purchase_price }}">{{ $invItem->item_name }} (Stock: {{ $invItem->current_stock }})</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="number" :name="'items['+index+'][quantity]'" x-model="item.quantity" min="1" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm text-sm text-right" required>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="number" :name="'items['+index+'][unit_price]'" x-model="item.unit_price" step="0.01" min="0" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm text-sm text-right" required>
                                            </td>
                                            <td class="px-4 py-2 text-right font-mono">
                                                <span x-text="(item.quantity * item.unit_price).toFixed(2)"></span>
                                            </td>
                                            <td class="px-4 py-2 text-center">
                                                <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700" :disabled="items.length === 1">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="px-4 py-2 text-right text-gray-700 dark:text-gray-300">Subtotal:</td>
                                        <td class="px-4 py-2 text-right text-gray-900 dark:text-gray-100">
                                            QAR <span x-text="calculateSubtotal()"></span>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="px-4 py-2 text-right text-sm font-medium text-gray-700 dark:text-gray-300">Transport Charges:</td>
                                        <td class="px-4 py-2">
                                            <input type="number" name="transport_charges" x-model="transport_charges" step="0.01" min="0" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm text-sm text-right">
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="px-4 py-2 text-right text-sm font-medium text-gray-700 dark:text-gray-300">Customs Charges:</td>
                                        <td class="px-4 py-2">
                                            <input type="number" name="customs_charges" x-model="customs_charges" step="0.01" min="0" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm text-sm text-right">
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="px-4 py-2 text-right text-sm font-medium text-gray-700 dark:text-gray-300">Other Charges:</td>
                                        <td class="px-4 py-2">
                                            <input type="number" name="other_charges" x-model="other_charges" step="0.01" min="0" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm text-sm text-right">
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr class="bg-gray-50 dark:bg-gray-900">
                                        <td colspan="3" class="px-4 py-3 text-right font-bold text-gray-900 dark:text-gray-100">Grand Total:</td>
                                        <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-gray-100">
                                            QAR <span x-text="calculateTotal()"></span>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            <button type="button" @click="addItem()" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300">
                                + Add Item
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-4">
                    <a href="{{ route('storekeeper.purchase-orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300">
                        Cancel
                    </a>
                    <x-primary-button>
                        {{ __('Update Purchase Order') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function purchaseOrderForm() {
            return {
                items: @json($purchaseOrder->items->map(function($item){
                    return [
                        'inventory_item_id' => $item->inventory_item_id,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price
                    ];
                })),
                transport_charges: {{ $purchaseOrder->transport_charges ?? 0 }},
                customs_charges: {{ $purchaseOrder->customs_charges ?? 0 }},
                other_charges: {{ $purchaseOrder->other_charges ?? 0 }},
                init() {
                     if (this.items.length === 0) {
                         this.addItem();
                     }
                },
                addItem() {
                    this.items.push({ inventory_item_id: '', quantity: 1, unit_price: 0 });
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items = this.items.filter((_, i) => i !== index);
                    }
                },
                updatePrice(index) {
                    const select = document.getElementsByName('items['+index+'][inventory_item_id]')[0];
                    if(select && select.selectedOptions[0]) {
                        const price = select.selectedOptions[0].getAttribute('data-price');
                        if(price) {
                           // Standard behavior: selecting item gets its price.
                           this.items[index].unit_price = parseFloat(price);
                        }
                    }
                },
                calculateSubtotal() {
                     let subtotal = this.items.reduce((sum, item) => {
                        return sum + (item.quantity * item.unit_price);
                    }, 0);
                    return subtotal.toFixed(2);
                },
                calculateTotal() {
                    let subtotal = parseFloat(this.calculateSubtotal());
                    let total = subtotal + parseFloat(this.transport_charges||0) + parseFloat(this.customs_charges||0) + parseFloat(this.other_charges||0);
                    return total.toFixed(2);
                }
            }
        }
    </script>
</x-app-layout>
