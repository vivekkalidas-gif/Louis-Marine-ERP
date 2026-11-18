<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight">
                    {{ __('New Delivery Note') }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Generate outgoing stock delivery voucher and dispatch details</p>
            </div>
            <a href="{{ route('storekeeper.delivery-notes.index') }}" class="btn-ghost text-xs">Back to List</a>
        </div>
    </x-slot>

    <div class="py-6 space-y-6" x-data="noteManager()">
        <div class="max-w-5xl mx-auto space-y-6">
            <form action="{{ route('storekeeper.delivery-notes.store') }}" method="POST">
                @csrf
                
                <div class="table-container">
                    <!-- Header Info -->
                    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-5 border-b border-slate-200 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-900/40">
                        <div>
                            <label class="theme-label">Customer Company <span class="text-rose-500">*</span></label>
                            <select name="customer_id" required class="theme-input text-xs py-2">
                                <option value="">Select Customer Company...</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="theme-label">DR Reference Number <span class="text-rose-500">*</span></label>
                            <input type="text" name="dr_number" required value="{{ $drNumber }}" class="theme-input text-xs py-2 font-mono">
                        </div>
                        <div>
                            <label class="theme-label">Dispatch Date <span class="text-rose-500">*</span></label>
                            <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="theme-input text-xs py-2">
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="p-6">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Stock Items to Deliver</h3>
                        <table class="theme-table">
                            <thead>
                                <tr class="theme-table-header">
                                    <th class="px-4 py-3.5">Inventory Item</th>
                                    <th class="px-4 py-3.5">Description</th>
                                    <th class="px-4 py-3.5 text-right w-32">Qty</th>
                                    <th class="px-4 py-3.5"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-700/60">
                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="theme-table-row">
                                        <td class="px-4 py-2.5">
                                            <select :name="`items[${index}][inventory_item_id]`" x-model="item.inventory_item_id" @change="updateDescription(index)" required class="theme-input text-xs py-1.5">
                                                <option value="">Select Stock Item...</option>
                                                @foreach($items as $invItem)
                                                    <option value="{{ $invItem->id }}" data-name="{{ $invItem->item_name }}">{{ $invItem->item_name }} (Stock: {{ $invItem->current_stock }})</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-4 py-2.5">
                                            <input type="text" :name="`items[${index}][description]`" x-model="item.description" class="theme-input text-xs py-1.5">
                                        </td>
                                        <td class="px-4 py-2.5">
                                            <input type="number" step="0.01" :name="`items[${index}][quantity]`" x-model="item.quantity" class="theme-input text-xs py-1.5 text-right font-mono">
                                        </td>
                                        <td class="px-4 py-2.5 text-center">
                                            <button type="button" @click="removeItem(index)" class="text-rose-500 hover:text-rose-700 text-xs font-bold">
                                                Remove
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot>
                                <tr class="bg-slate-50/50 dark:bg-slate-900/40">
                                    <td colspan="4" class="px-4 py-3">
                                        <button type="button" @click="addItem()" class="btn-ghost text-xs font-bold text-brand-600 dark:text-brand-400">+ Add Item Line</button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Notes -->
                    <div class="px-6 pb-6">
                        <label class="theme-label">Additional Dispatch Notes</label>
                        <textarea name="notes" rows="3" class="theme-input text-xs" placeholder="Special handling or delivery instructions..."></textarea>
                    </div>

                    <!-- Footer Actions -->
                    <div class="bg-slate-50/50 dark:bg-slate-900/40 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-700/80">
                        <a href="{{ route('storekeeper.delivery-notes.index') }}" class="btn-secondary text-xs">Cancel</a>
                        <button type="submit" class="btn-primary text-xs font-bold">Create Delivery Note</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function noteManager() {
            return {
                items: [
                    { inventory_item_id: '', description: '', quantity: 1 }
                ],
                
                addItem() {
                    this.items.push({ inventory_item_id: '', description: '', quantity: 1 });
                },
                
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },

                updateDescription(index) {
                    let select = document.getElementsByName(`items[${index}][inventory_item_id]`)[0];
                    if(select) {
                        let text = select.options[select.selectedIndex].getAttribute('data-name');
                        if(text && !this.items[index].description) {
                             this.items[index].description = text;
                        }
                    }
                }
            }
        }
    </script>
</x-app-layout>
