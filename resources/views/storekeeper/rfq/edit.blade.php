<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit RFQ: {{ $rfq->reference_number }}
            </h2>
            <a href="{{ route('storekeeper.rfq.index') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-100 font-medium">Back</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('storekeeper.rfq.update', $rfq) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- 1. General Details -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">1. General Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reference Number</label>
                            <input type="text" value="{{ $rfq->reference_number }}" class="mt-1 block w-full bg-gray-100 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deadline Date</label>
                            <input type="date" name="deadline_date" value="{{ old('deadline_date', $rfq->deadline_date ? $rfq->deadline_date->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                            <select name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="local" {{ old('type', $rfq->type) == 'local' ? 'selected' : '' }}>Local</option>
                                <option value="overseas" {{ old('type', $rfq->type) == 'overseas' ? 'selected' : '' }}>Overseas</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Preferred Currency</label>
                            <select name="currency_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Select Currency</option>
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency->id }}" {{ old('currency_id', $rfq->currency_id) == $currency->id ? 'selected' : '' }}>{{ $currency->code }} - {{ $currency->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Incoterm</label>
                            <div class="flex">
                                <select name="incoterm" id="incoterm_select" class="w-full rounded-l-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Select Incoterm</option>
                                    @foreach($incoterms as $incoterm)
                                        <option value="{{ $incoterm->code }}" {{ old('incoterm', $rfq->incoterm) == $incoterm->code ? 'selected' : '' }}>
                                            {{ $incoterm->code }} - {{ $incoterm->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" onclick="document.getElementById('addIncotermModal').classList.remove('hidden')" class="bg-indigo-600 text-white px-3 py-2 rounded-r-md hover:bg-indigo-700 font-bold">+</button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Delivery Location</label>
                            <input type="text" name="delivery_location" value="{{ old('delivery_location', $rfq->delivery_location) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="e.g. Doha Port">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Requested Items -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">2. Requested Items</h3>
                    <div class="space-y-4" id="items-container">
                        <!-- Items will be injected by JS -->
                    </div>
                    <button type="button" onclick="addItem()" class="mt-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        + Add Another Item
                    </button>
                </div>
            </div>

            <!-- 3. Select Suppliers -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">3. Select Suppliers to Invite</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($suppliers as $supplier)
                            <label class="flex items-center space-x-3 p-3 border rounded hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700">
                                <input type="checkbox" name="supplier_ids[]" value="{{ $supplier->id }}" class="form-checkbox h-5 w-5 text-indigo-600" 
                                {{ in_array($supplier->id, old('supplier_ids', $rfq->suppliers->pluck('id')->toArray())) ? 'checked' : '' }}>
                                <span class="text-gray-900 dark:text-gray-100 font-medium">{{ $supplier->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded shadow-lg">
                    Update RFQ
                </button>
            </div>
        </form>
    </div>
</div>

<!-- SCRIPT TO INJECT PRODUCTS DATA AND PRE-POPULATE ITEMS -->
@php
    $inventoryProductsJs = $products->map(function($p) {
        return [
            'id' => $p->id,
            'name' => $p->item_name,
            'model' => $p->model ?? '',
            'unit' => $p->unit ?? 'Nos'
        ];
    });

    $existingItemsJs = $rfq->items->map(function($item) {
        return [
            'product_id' => $item->product_id,
            'description' => $item->description,
            'quantity' => $item->quantity,
            'unit' => $item->unit
        ];
    });
@endphp

<script>
    const inventoryProducts = @json($inventoryProductsJs);
    const existingItems = @json($existingItemsJs);

    function populateProductDropdown(selectElement) {
        // Clear existing options except the first one
        while (selectElement.options.length > 1) {
            selectElement.remove(1);
        }

        inventoryProducts.forEach(product => {
            const option = document.createElement('option');
            option.value = product.id;
            option.textContent = product.model ? `${product.name} (${product.model})` : product.name;
            selectElement.appendChild(option);
        });
    }

    function createItemRow(index, data = null) {
        /*
           data object structure: { product_id, description, quantity, unit }
        */
        const container = document.getElementById('items-container');
        const newItem = document.createElement('div');
        newItem.classList.add('grid', 'grid-cols-1', 'md:grid-cols-12', 'gap-4', 'items-end', 'bg-gray-50 dark:bg-gray-700', 'p-4', 'rounded-md', 'item-row');
        
        newItem.innerHTML = `
            <div class="md:col-span-4">
                 <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Product (Optional)</label>
                 <select name="items[${index}][product_id]" onchange="updateRow(this, ${index})" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 product-select">
                     <option value="">Custom / New Item</option>
                 </select>
            </div>
            <div class="md:col-span-4">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Description</label>
                <input type="text" name="items[${index}][description]" value="${data ? data.description : ''}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 description-input" placeholder="Item Name or Description" required>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Quantity</label>
                <input type="number" step="0.01" name="items[${index}][quantity]" value="${data ? data.quantity : ''}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
            </div>
            <div class="md:col-span-1">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Unit</label>
                <select name="items[${index}][unit]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 unit-select">
                    <option value="Nos" ${data && data.unit == 'Nos' ? 'selected' : ''}>Nos</option>
                    <option value="Kg" ${data && data.unit == 'Kg' ? 'selected' : ''}>Kg</option>
                    <option value="Set" ${data && data.unit == 'Set' ? 'selected' : ''}>Set</option>
                    <option value="Ltr" ${data && data.unit == 'Ltr' ? 'selected' : ''}>Ltr</option>
                    <option value="Mtr" ${data && data.unit == 'Mtr' ? 'selected' : ''}>Mtr</option>
                    <option value="Coil" ${data && data.unit == 'Coil' ? 'selected' : ''}>Coil</option>
                </select>
            </div>
            <div class="md:col-span-1 flex justify-end pb-1">
                <button type="button" onclick="this.closest('.item-row').remove()" class="text-red-600 hover:text-red-900 text-sm font-medium">Remove</button>
            </div>
        `;
        
        container.appendChild(newItem);

        // Populate Dropdown
        const selectElement = newItem.querySelector('.product-select');
        populateProductDropdown(selectElement);

        // Set value if data exists
        if (data && data.product_id) {
            selectElement.value = data.product_id;
        }
    }

    let itemIndex = 0; // Will be incremented

    function addItem() {
        createItemRow(itemIndex);
        itemIndex++;
    }

    // Auto-fill logic (same as create)
    function updateRow(selectElement, index) {
        const row = selectElement.closest('.item-row');
        const descriptionInput = row.querySelector('.description-input');
        const unitSelect = row.querySelector('.unit-select');
        
        const productId = selectElement.value;
        const product = inventoryProducts.find(p => p.id == productId);

        if (product) {
            descriptionInput.value = product.name; // Fill name
            // Optional: You could lock this field if you want strict adherence, but usually editable is better.
            
            // Allow unit to be set if available, or keep default
            if(product.unit) {
                // Check if unit exists in options, else might need to add it or fallback
                // For now assuming inventory units match dropdown options
                unitSelect.value = product.unit;
            }
        } else {
            // Cleared selection -> Clear details? Or keep them?
            // Usually correct to clear so user knows they are entering custom data
            descriptionInput.value = '';
            unitSelect.value = 'Nos';
        }
    }

    // Initialize with existing items
    document.addEventListener('DOMContentLoaded', () => {
        if (existingItems.length > 0) {
            existingItems.forEach(item => {
                createItemRow(itemIndex, item);
                itemIndex++;
            });
        } else {
            // If strictly no items (rare for edit), maybe add one empty
             addItem();
        }
    });

</script>
    <!-- Incoterm Modal -->
    <div id="addIncotermModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-50 dark:bg-gray-700 bg-opacity-75 transition-opacity" onclick="document.getElementById('addIncotermModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">Add New Incoterm</h3>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Code (e.g. EXW)</label>
                            <input type="text" id="new_incoterm_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" maxlength="3" style="text-transform:uppercase">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name (e.g. Ex Works)</label>
                            <input type="text" id="new_incoterm_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="saveIncoterm()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Save
                    </button>
                    <button type="button" onclick="document.getElementById('addIncotermModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function saveIncoterm() {
            const code = document.getElementById('new_incoterm_code').value;
            const name = document.getElementById('new_incoterm_name').value;

            if (!code) {
                alert('Please enter a code');
                return;
            }

            fetch('{{ route('storekeeper.rfq.incoterms.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ code: code, name: name })
            })
            .then(response => response.json())
            .then(data => {
                if (data.id) {
                    // Add to dropdown
                    const select = document.getElementById('incoterm_select');
                    const option = document.createElement('option');
                    option.value = data.code;
                    option.text = `${data.code} - ${data.name}`;
                    option.selected = true;
                    select.appendChild(option);
                    
                    // Close Modal
                    document.getElementById('addIncotermModal').classList.add('hidden');
                    document.getElementById('new_incoterm_code').value = '';
                    document.getElementById('new_incoterm_name').value = '';
                } else {
                    alert('Error saving Incoterm');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving Incoterm. Code might already exist.');
            });
        }
    </script>
</x-app-layout>
