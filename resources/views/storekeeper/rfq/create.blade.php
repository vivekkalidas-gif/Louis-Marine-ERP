<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Create New RFQ') }}
            </h2>
            <a href="{{ route('storekeeper.rfq.index') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-100 font-medium">Back</a>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ type: 'local', itemCount: 1 }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('storekeeper.rfq.store') }}" method="POST">
                @csrf
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">1. General Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Purchase Type</label>
                                <select name="type" x-model="type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="local">Local Purchase (QAR)</option>
                                    <option value="overseas">Overseas Purchase</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Deadline Date</label>
                                <input type="date" name="deadline_date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            </div>
                            
                            <!-- Overseas Fields -->
                            <div x-show="type === 'overseas'" style="display: none;">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Currency</label>
                                <select name="currency_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Select Currency</option>
                                    @foreach($currencies as $currency)
                                        @if(!$currency->is_base)
                                            <option value="{{ $currency->id }}">{{ $currency->code }} - {{ $currency->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div x-show="type === 'overseas'" style="display: none;">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Incoterms</label>
                                <div class="flex">
                                    <select name="incoterm" id="incoterm_select" class="w-full rounded-l-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="">Select Incoterm</option>
                                        @foreach($incoterms as $incoterm)
                                            <option value="{{ $incoterm->code }}">{{ $incoterm->code }} - {{ $incoterm->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" onclick="document.getElementById('addIncotermModal').classList.remove('hidden')" class="bg-indigo-600 text-white px-3 py-2 rounded-r-md hover:bg-indigo-700 font-bold">+</button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Delivery Location / Instructions</label>
                            <input type="text" name="delivery_location" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="e.g. Warehouse 1, Doha Industrial Area">
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">2. Requested Items</h3>
                        <div class="space-y-4" id="items-container">
                            <!-- SCRIPT TO INJECT PRODUCTS DATA -->
                            <!-- SCRIPT TO INJECT PRODUCTS DATA -->
                            @php
                                $inventoryProductsJs = $products->map(function($p) {
                                    return [
                                        'id' => $p->id,
                                        'name' => $p->item_name,
                                        'model' => $p->model ?? '',
                                        'unit' => $p->unit ?? 'Nos'
                                    ];
                                });
                            @endphp
                            <script>
                                const inventoryProducts = @json($inventoryProductsJs);
                            </script>

                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <div class="md:col-span-4">
                                     <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Product (Optional)</label>
                                     <select name="items[0][product_id]" onchange="updateRow(this, 0)" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                         <option value="">Custom / New Item</option>
                                     </select>
                                </div>
                                <div class="md:col-span-4">
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Description</label>
                                    <input type="text" name="items[0][description]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Item Name or Description" required>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Quantity</label>
                                    <input type="number" step="0.01" name="items[0][quantity]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                </div>
                                <div class="md:col-span-1">
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Unit</label>
                                    <select name="items[0][unit]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="Nos">Nos</option>
                                        <option value="Kg">Kg</option>
                                        <option value="Set">Set</option>
                                        <option value="Ltr">Ltr</option>
                                        <option value="Mtr">Mtr</option>
                                    </select>
                                </div>
                                <div class="md:col-span-1">
                                   <!-- Spacer or Action -->
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="button" onclick="addItem()" class="text-sm text-blue-600 font-medium hover:text-blue-800">+ Add Another Item</button>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">3. Select Suppliers</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($suppliers as $supplier)
                            <label class="inline-flex items-center space-x-2 p-3 border rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 cursor-pointer">
                                <input type="checkbox" name="supplier_ids[]" value="{{ $supplier->id }}" class="rounded text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="text-sm text-gray-700 dark:text-gray-300 font-medium">{{ $supplier->company_name ?? $supplier->name }}</span>
                            </label>
                            @endforeach
                        </div>
                        @error('supplier_ids')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded shadow-lg">
                        Create RFQ
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let itemIndex = 1;

        // Populate initial dropdown
        document.addEventListener('DOMContentLoaded', function() {
            const firstSelect = document.querySelector('select[name="items[0][product_id]"]');
            populateProductDropdown(firstSelect);
        });

        function populateProductDropdown(selectElement) {
            inventoryProducts.forEach(product => {
                const option = document.createElement('option');
                option.value = product.id;
                option.textContent = product.model ? `${product.name} (${product.model})` : product.name;
                selectElement.appendChild(option);
            });
        }

        function updateRow(selectElement, index) {
            const productId = selectElement.value;
            const descriptionInput = document.querySelector(`input[name="items[${index}][description]"]`);
            const unitSelect = document.querySelector(`select[name="items[${index}][unit]"]`);

            if (productId) {
                const product = inventoryProducts.find(p => p.id == productId);
                if (product) {
                    descriptionInput.value = product.name; // Auto-fill Description
                    if (product.unit) {
                        unitSelect.value = product.unit; // Auto-fill Unit
                    }
                }
            } else {
                descriptionInput.value = ''; // Clear if Custom selected
            }
        }

        function addItem() {
            const container = document.getElementById('items-container');
            const template = `
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end bg-gray-50 dark:bg-gray-700 p-4 rounded-md border mt-2">
                    <div class="md:col-span-4">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Product (Optional)</label>
                        <select name="items[${itemIndex}][product_id]" onchange="updateRow(this, ${itemIndex})" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Custom / New Item</option>
                        </select>
                    </div>
                    <div class="md:col-span-4">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Description</label>
                        <input type="text" name="items[${itemIndex}][description]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Quantity</label>
                        <input type="number" step="0.01" name="items[${itemIndex}][quantity]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    </div>
                    <div class="md:col-span-1">
                         <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Unit</label>
                         <select name="items[${itemIndex}][unit]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                             <option value="Nos">Nos</option>
                             <option value="Kg">Kg</option>
                             <option value="Set">Set</option>
                             <option value="Ltr">Ltr</option>
                             <option value="Mtr">Mtr</option>
                         </select>
                     </div>
                     <div class="md:col-span-1 text-right">
                         <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-red-500 hover:text-red-700 text-sm">Remove</button>
                     </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', template);
            
            // Populate the new dropdown
            const newSelect = container.lastElementChild.querySelector(`select[name="items[${itemIndex}][product_id]"]`);
            populateProductDropdown(newSelect);

            itemIndex++;
        }
    </script>
    <!-- Incoterm Modal -->
    <div id="addIncotermModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-50 dark:bg-gray-7000 bg-opacity-75 transition-opacity" onclick="document.getElementById('addIncotermModal').classList.add('hidden')"></div>
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
