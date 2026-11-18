<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight">
                    {{ __('Create Request for Quotation (RFQ)') }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Issue new supplier RFQ for local or overseas materials requisition</p>
            </div>
            <a href="{{ route('storekeeper.rfq.index') }}" class="btn-ghost text-xs">Back to RFQ List</a>
        </div>
    </x-slot>

    <div class="py-6 space-y-6" x-data="{ type: 'local', itemCount: 1 }">
        <div class="max-w-7xl mx-auto space-y-6">
            <form action="{{ route('storekeeper.rfq.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- General Info Card -->
                <div class="theme-card">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4 pb-2 border-b border-slate-200 dark:border-slate-700/80">1. General RFQ Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="theme-label">Purchase Type <span class="text-rose-500">*</span></label>
                            <select name="type" x-model="type" class="theme-input text-xs py-2">
                                <option value="local">Local Purchase (QAR)</option>
                                <option value="overseas">Overseas Import</option>
                            </select>
                        </div>
                        <div>
                            <label class="theme-label">Quotation Deadline <span class="text-rose-500">*</span></label>
                            <input type="date" name="deadline_date" class="theme-input text-xs py-2" required>
                        </div>
                        
                        <!-- Overseas Fields -->
                        <div x-show="type === 'overseas'" style="display: none;">
                            <label class="theme-label">Billing Currency</label>
                            <select name="currency_id" class="theme-input text-xs py-2">
                                <option value="">Select Currency...</option>
                                @foreach($currencies as $currency)
                                    @if(!$currency->is_base)
                                        <option value="{{ $currency->id }}">{{ $currency->code }} - {{ $currency->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div x-show="type === 'overseas'" style="display: none;">
                            <label class="theme-label">Incoterms</label>
                            <div class="flex gap-2">
                                <select name="incoterm" id="incoterm_select" class="theme-input text-xs py-2 flex-1">
                                    <option value="">Select Incoterm...</option>
                                    @foreach($incoterms as $incoterm)
                                        <option value="{{ $incoterm->code }}">{{ $incoterm->code }} - {{ $incoterm->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" onclick="document.getElementById('addIncotermModal').classList.remove('hidden')" class="btn-secondary text-xs px-3 font-bold">+</button>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="theme-label">Delivery Location / Instructions</label>
                            <input type="text" name="delivery_location" class="theme-input text-xs py-2" placeholder="e.g. Warehouse 1, Doha Industrial Area">
                        </div>
                    </div>
                </div>

                <!-- Requested Items Card -->
                <div class="theme-card">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4 pb-2 border-b border-slate-200 dark:border-slate-700/80">2. Requested Items & Materials</h3>
                    <div class="space-y-4" id="items-container">
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

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end bg-slate-50 dark:bg-slate-900/40 p-4 rounded-xl border border-slate-200 dark:border-slate-700/80">
                            <div class="md:col-span-4">
                                 <label class="theme-label mb-1">Product Master (Optional)</label>
                                 <select name="items[0][product_id]" onchange="updateRow(this, 0)" class="theme-input text-xs py-2">
                                     <option value="">Custom / Non-Stock Item</option>
                                 </select>
                            </div>
                            <div class="md:col-span-4">
                                <label class="theme-label mb-1">Description <span class="text-rose-500">*</span></label>
                                <input type="text" name="items[0][description]" class="theme-input text-xs py-2" placeholder="Item Name or Specifications" required>
                            </div>
                            <div class="md:col-span-2">
                                <label class="theme-label mb-1">Quantity <span class="text-rose-500">*</span></label>
                                <input type="number" step="0.01" name="items[0][quantity]" class="theme-input text-xs py-2" required>
                            </div>
                            <div class="md:col-span-2">
                                <label class="theme-label mb-1">Unit</label>
                                <select name="items[0][unit]" class="theme-input text-xs py-2">
                                    <option value="Nos">Nos</option>
                                    <option value="Kg">Kg</option>
                                    <option value="Set">Set</option>
                                    <option value="Ltr">Ltr</option>
                                    <option value="Mtr">Mtr</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="button" onclick="addItem()" class="btn-ghost text-xs font-bold gap-1 text-brand-600 dark:text-brand-400">
                            + Add Additional Line Item
                        </button>
                    </div>
                </div>

                <!-- Supplier Selection Card -->
                <div class="theme-card">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4 pb-2 border-b border-slate-200 dark:border-slate-700/80">3. Select Target Suppliers</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        @foreach($suppliers as $supplier)
                        <label class="flex items-center p-3 rounded-lg border border-slate-200 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-900/40 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer">
                            <input type="checkbox" name="supplier_ids[]" value="{{ $supplier->id }}" class="rounded border-slate-300 text-brand-600 mr-3">
                            <span class="text-xs font-bold text-slate-900 dark:text-white">{{ $supplier->company_name ?? $supplier->name }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('supplier_ids')
                        <p class="text-rose-500 text-xs mt-2 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('storekeeper.rfq.index') }}" class="btn-secondary text-xs">Cancel</a>
                    <button type="submit" class="btn-primary text-xs py-3 px-8 font-bold">
                        Create & Dispatch RFQ
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let itemIndex = 1;

        document.addEventListener('DOMContentLoaded', function() {
            const firstSelect = document.querySelector('select[name="items[0][product_id]"]');
            if (firstSelect) populateProductDropdown(firstSelect);
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
                    descriptionInput.value = product.name;
                    if (product.unit) unitSelect.value = product.unit;
                }
            } else {
                descriptionInput.value = '';
            }
        }

        function addItem() {
            const container = document.getElementById('items-container');
            const template = `
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end bg-slate-50 dark:bg-slate-900/40 p-4 rounded-xl border border-slate-200 dark:border-slate-700/80 mt-3">
                    <div class="md:col-span-4">
                        <label class="theme-label mb-1">Product Master (Optional)</label>
                        <select name="items[${itemIndex}][product_id]" onchange="updateRow(this, ${itemIndex})" class="theme-input text-xs py-2">
                            <option value="">Custom / Non-Stock Item</option>
                        </select>
                    </div>
                    <div class="md:col-span-4">
                        <label class="theme-label mb-1">Description <span class="text-rose-500">*</span></label>
                        <input type="text" name="items[${itemIndex}][description]" class="theme-input text-xs py-2" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="theme-label mb-1">Quantity <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.01" name="items[${itemIndex}][quantity]" class="theme-input text-xs py-2" required>
                    </div>
                    <div class="md:col-span-1">
                         <label class="theme-label mb-1">Unit</label>
                         <select name="items[${itemIndex}][unit]" class="theme-input text-xs py-2">
                             <option value="Nos">Nos</option>
                             <option value="Kg">Kg</option>
                             <option value="Set">Set</option>
                             <option value="Ltr">Ltr</option>
                             <option value="Mtr">Mtr</option>
                         </select>
                     </div>
                     <div class="md:col-span-1 text-right">
                         <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-rose-500 hover:text-rose-700 text-xs font-bold">Remove</button>
                     </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', template);
            
            const newSelect = container.lastElementChild.querySelector(`select[name="items[${itemIndex}][product_id]"]`);
            populateProductDropdown(newSelect);

            itemIndex++;
        }
    </script>
</x-app-layout>
