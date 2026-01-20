@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="noteManager()">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">New Delivery Note</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Create a delivery note for outgoing stock.</p>
        </div>
        <a href="{{ route('storekeeper.delivery-notes.index') }}" class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-100">
            &larr; Back to List
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('storekeeper.delivery-notes.store') }}" method="POST">
        @csrf
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Header Info -->
            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6 bg-gray-50 dark:bg-gray-700 border-b border-gray-100">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Customer <span class="text-red-500">*</span></label>
                    <select name="customer_id" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->company_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">DR Number <span class="text-red-500">*</span></label>
                    <input type="text" name="dr_number" required value="{{ $drNumber }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date <span class="text-red-500">*</span></label>
                    <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>
            </div>

            <!-- Items -->
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Items to Deliver</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Item</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Description</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-32">Qty</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template x-for="(item, index) in items" :key="index">
                                <tr>
                                    <td class="px-4 py-2">
                                        <select :name="`items[${index}][inventory_item_id]`" x-model="item.inventory_item_id" @change="updateDescription(index)" required class="w-full border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                            <option value="">Select Item</option>
                                            @foreach($items as $invItem)
                                                <option value="{{ $invItem->id }}" data-name="{{ $invItem->item_name }}">{{ $invItem->item_name }} (Stock: {{ $invItem->current_stock }})</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="text" :name="`items[${index}][description]`" x-model="item.description" class="w-full border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" step="0.01" :name="`items[${index}][quantity]`" x-model="item.quantity" class="w-full border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm text-right">
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
                                <td colspan="4" class="px-4 py-3">
                                    <button type="button" @click="addItem()" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">+ Add Item</button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Notes -->
            <div class="px-6 pb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"></textarea>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex items-center justify-end gap-3 border-t border-gray-100">
                <a href="{{ route('storekeeper.delivery-notes.index') }}" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 text-sm font-medium">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-sm text-sm font-medium transition-colors">Create Note</button>
            </div>
        </div>
    </form>
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
                // Simple logic: we rely on x-model binding, but if we want to auto-fill description:
                // We'd need to find the selected option text.
                // For now, let user type or backend handle default.
                // Advanced: Use event.target to find selected option data-name.
                
                // Let's try to grab it from the DOM element after typical microtask delay or use $nextTick if in Alpine
                // but standard change event works.
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
@endsection
