<x-app-layout>
<div class="container-fluid px-4" x-data="dispatchForm()">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-slate-800">Edit Dispatch Note: {{ $dispatchNote->dispatch_number }}</h1>
        <a href="{{ route('admin.dispatch.index') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-100">
            &larr; Back to List
        </a>
    </div>

    {{-- Debug: ID is {{ $dispatchNote->id }} --}}
    <form action="{{ route('admin.dispatch.update', ['dispatch_note' => $dispatchNote->id]) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Header Info -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded shadow col-span-1">
                <h3 class="text-lg font-bold mb-4 border-b pb-2">Dispatch Details</h3>
                
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Dispatch Number</label>
                    <input type="text" value="{{ $dispatchNote->dispatch_number }}" readonly class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight bg-gray-100">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Date</label>
                    <input type="date" name="date" value="{{ optional($dispatchNote->date)->format('Y-m-d') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4" x-data="{ isCustom: {{ !in_array($dispatchNote->type, ['Calibration', 'Site Transfer', 'Return to Vendor', 'Delivery']) ? 'true' : 'false' }} }">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                        Type
                        <button type="button" @click="isCustom = !isCustom; if(!isCustom) $nextTick(() => $refs.select.focus()); else $nextTick(() => $refs.input.focus());" class="ml-2 text-xs text-blue-600 hover:text-blue-800 underline focus:outline-none">
                            <span x-text="isCustom ? 'Select from List' : '+ Add New Type'"></span>
                        </button>
                    </label>
                    
                    <div x-show="!isCustom">
                        <select name="type" x-ref="select" :disabled="isCustom" class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="Calibration" {{ $dispatchNote->type == 'Calibration' ? 'selected' : '' }}>Calibration</option>
                            <option value="Site Transfer" {{ $dispatchNote->type == 'Site Transfer' ? 'selected' : '' }}>Site Transfer</option>
                            <option value="Return to Vendor" {{ $dispatchNote->type == 'Return to Vendor' ? 'selected' : '' }}>Return to Vendor</option>
                            <option value="Delivery" {{ $dispatchNote->type == 'Delivery' ? 'selected' : '' }}>Delivery</option>
                        </select>
                    </div>

                    <div x-show="isCustom" style="display: none;">
                        <input type="text" name="type" value="{{ $dispatchNote->type }}" x-ref="input" :disabled="!isCustom" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter custom type e.g. Repair">
                    </div>
                </div>
            </div>

            <!-- Recipient Info -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded shadow col-span-2">
                <h3 class="text-lg font-bold mb-4 border-b pb-2">Recipient</h3>
                
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Receiver Name / Company</label>
                    <input type="text" name="receiver_name" value="{{ $dispatchNote->receiver_name }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline" placeholder="e.g. ABC Calibration Labs" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Address / Location</label>
                    <textarea name="receiver_address" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline" placeholder="Detailed address or site location">{{ $dispatchNote->receiver_address }}</textarea>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded shadow mb-6">
            <h3 class="text-lg font-bold mb-4 border-b pb-2">Items to Dispatch</h3>
            
            <table class="min-w-full">
                <thead>
                    <tr>
                        <th class="text-left py-2 px-2">Description / Item Name</th>
                        <th class="text-left py-2 px-2">Serial Number / Ref</th>
                        <th class="text-left py-2 px-2 w-24">Qty</th>
                        <th class="text-left py-2 px-2">Remarks</th>
                        <th class="px-2"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, index) in items" :key="index">
                        <tr>
                            <td class="p-2">
                                <input type="text" :name="'items['+index+'][description]'" x-model="item.description" class="w-full border rounded px-2 py-1" required placeholder="Item description">
                            </td>
                            <td class="p-2">
                                <input type="text" :name="'items['+index+'][serial_number]'" x-model="item.serial_number" class="w-full border rounded px-2 py-1" placeholder="Serial/Asset No">
                            </td>
                            <td class="p-2">
                                <input type="number" :name="'items['+index+'][quantity]'" x-model="item.quantity" class="w-full border rounded px-2 py-1" min="1" required>
                            </td>
                            <td class="p-2">
                                <input type="text" :name="'items['+index+'][remarks]'" x-model="item.remarks" class="w-full border rounded px-2 py-1" placeholder="e.g. Handle with care">
                            </td>
                            <td class="p-2 text-center">
                                <button type="button" @click="removeItem(index)" class="text-red-600 hover:text-red-900 font-bold">&times;</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <div class="mt-4">
                <button type="button" @click="addItem()" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">
                    + Add Another Item
                </button>
            </div>
        </div>

        <div class="flex justify-end space-x-4">
             <a href="{{ route('admin.dispatch.index') }}" class="bg-gray-50 dark:bg-gray-7000 text-white font-bold py-3 px-8 rounded shadow hover:bg-gray-600">
                Cancel
            </a>
            <button type="submit" class="bg-green-600 text-white font-bold py-3 px-8 rounded shadow hover:bg-green-700">
                Update Dispatch Note
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('dispatchForm', () => ({
        items: @json($dispatchNote->items),
        addItem() {
            this.items.push({ description: '', serial_number: '', quantity: 1, remarks: '' });
        },
        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            } else if (this.items.length === 1 && confirm('Clear this item row?')) {
                 this.items = [{ description: '', serial_number: '', quantity: 1, remarks: '' }];
            }
        },
        init() {
            if (!this.items || this.items.length === 0) {
                this.items = [{ description: '', serial_number: '', quantity: 1, remarks: '' }];
            }
        }
    }));
});
</script>
</x-app-layout>
