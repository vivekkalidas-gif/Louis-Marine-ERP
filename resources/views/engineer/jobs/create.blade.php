<x-app-layout>
    <x-slot name="header">
        <h2 class="theme-text-header">
            {{ __('Create New Service Job') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="jobWizard()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Stepper -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <template x-for="i in 5">
                        <div class="relative flex flex-col items-center flex-1">
                            <div :class="{'bg-marine-600 dark:bg-marine-500': step >= i, 'bg-gray-300 dark:bg-gray-700': step < i}" 
                                 class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold transition-colors duration-300 z-10">
                                <span x-text="i"></span>
                            </div>
                            <div class="text-xs mt-2 font-medium bg-white dark:bg-gray-900 px-1" 
                                 :class="{'text-marine-700 dark:text-marine-300': step >= i, 'text-gray-400': step < i}"
                                 x-text="stepLabels[i-1]"></div>
                            <div class="absolute top-4 w-full h-0.5 -z-0" 
                                 :class="{'bg-marine-600 dark:bg-marine-500': step > i, 'bg-gray-300 dark:bg-gray-700': step <= i}"
                                 x-show="i < 5"></div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="theme-card">
                <form method="POST" action="{{ route('engineer.jobs.store') }}" id="jobForm" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- STEP 1: Basic Info -->
                    <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                        <div class="border-b dark:border-marine-800 pb-4 mb-6">
                            <h3 class="text-lg font-medium text-marine-900 dark:text-marine-100">Step 1: Basic Information</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Classify the job and set priority.</p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="theme-label">Job Type *</label>
                                <select name="job_type" class="theme-input" x-model="formData.job_type" required>
                                    <option value="Field Service">Field Service</option>
                                    <option value="Workshop Service">Workshop Service</option>
                                </select>
                            </div>

                            <div>
                                <label class="theme-label">Priority *</label>
                                <select name="priority" class="theme-input" required>
                                    <option value="Normal">Normal</option>
                                    <option value="Low">Low</option>
                                    <option value="High">High</option>
                                    <option value="Emergency">Emergency</option>
                                </select>
                            </div>

                            <div>
                                <label class="theme-label">Scheduled Date & Time</label>
                                <input type="text" name="scheduled_at" class="theme-input" id="scheduled_datepicker" 
                                       placeholder="Select Date & Time">
                            </div>

                            <div>
                                <label class="theme-label">Source</label>
                                <select name="job_source" class="theme-input">
                                    <option value="Email">Email</option>
                                    <option value="Call">Call</option>
                                    <option value="Walk-in">Walk-in</option>
                                    <option value="Contract">Contract</option>
                                </select>
                            </div>

                             <div>
                                <label class="theme-label">PO Number / Reference</label>
                                <input type="text" name="reference_no" class="theme-input" placeholder="e.g. PO-12345">
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: Customer & Vessel -->
                    <div x-show="step === 2" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                         <div class="border-b dark:border-marine-800 pb-4 mb-6">
                            <h3 class="text-lg font-medium text-marine-900 dark:text-marine-100">Step 2: Customer & Vessel</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Link the job to a client and vessel.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="theme-label">Customer *</label>
                                <select name="customer_id" class="theme-input" x-model="customer_id" required>
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $c)
                                        <option value="{{ $c->id }}">{{ $c->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="theme-label">Vessel</label>
                                <select name="vessel_id" class="theme-input" x-model="vessel_id" :disabled="!customer_id">
                                    <option value="">Select Vessel</option>
                                    <template x-for="v in filteredVessels" :key="v.id">
                                        <option :value="v.id" x-text="v.name"></option>
                                    </template>
                                </select>
                                <p x-show="!customer_id" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Select a customer first.</p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="theme-label">Requested By</label>
                                <select name="requested_by_contact_id" class="theme-input" :disabled="!customer_id" x-model="requested_by_contact_id">
                                    <option value="">Select Contact Person</option>
                                    <template x-for="c in filteredContacts" :key="c.id">
                                        <option :value="c.id" x-text="c.name + ' (' + (c.designation || 'Staff') + ')'"></option>
                                    </template>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="theme-label">Location Details</label>
                                <input type="text" name="location_details" class="theme-input" placeholder="e.g. Ras Laffan Port, Berth 5 OR Louis Workshop">
                            </div>

                            <!-- Delivery Note Section (Workshop Service Only) -->
                            <div x-show="formData.job_type === 'Workshop Service'" class="md:col-span-2 border-t dark:border-marine-700 pt-6 mt-4">
                                <h4 class="text-md font-semibold text-marine-900 dark:text-marine-100 mb-4">📋 Delivery Note Details</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Material drop-off information for workshop service</p>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="theme-label">Delivered By *</label>
                                        <input type="text" name="delivered_by" class="theme-input" 
                                               placeholder="Name of person who brought material"
                                               :required="formData.job_type === 'Workshop Service'">
                                    </div>

                                    <div>
                                        <label class="theme-label">Contact Person *</label>
                                        <input type="text" name="delivery_contact_person" class="theme-input" 
                                               placeholder="Contact person name"
                                               :required="formData.job_type === 'Workshop Service'">
                                    </div>

                                    <div>
                                        <label class="theme-label">Contact Number *</label>
                                        <input type="tel" name="delivery_contact_number" class="theme-input" 
                                               placeholder="+974 XXXX XXXX"
                                               :required="formData.job_type === 'Workshop Service'">
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="theme-label">Delivery Notes</label>
                                        <textarea name="delivery_notes" class="theme-input" rows="3" 
                                                  placeholder="Special instructions or remarks about the delivery"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: Service Scope (NEW SIDEBAR UI) -->
                    <div x-show="step === 3" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                        <div class="border-b dark:border-marine-800 pb-4 mb-6">
                            <h3 class="text-lg font-medium text-marine-900 dark:text-marine-100">Step 3: Scope of Work</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Select equipment & services from categories.</p>
                        </div>

                        <div class="flex gap-6">
                            <!-- LEFT SIDEBAR: Categories -->
                            <div class="w-64 flex-shrink-0">
                                <h4 class="font-bold text-sm text-marine-900 dark:text-marine-100 mb-3 uppercase tracking-wide">Equipment Categories</h4>
                                <div class="space-y-1">
                                    @foreach($equipmentCategories as $category)
                                    <button type="button" @click="selectedCategory = {{ $category->id }}" 
                                            :class="selectedCategory === {{ $category->id }} ? 'bg-marine-700 text-white' : 'bg-gray-100 dark:bg-marine-800 text-gray-700 dark:text-gray-300 hover:bg-marine-600 hover:text-white'"
                                            class="w-full text-left px-4 py-2 rounded text-sm font-medium transition">
                                        {{ $category->name }}
                                    </button>
                                    @endforeach
                                </div>
                            </div>

                            <!-- MAIN PANEL: Items & Selected Scopes -->
                            <div class="flex-1">
                                <!-- Available Items for Selected Category -->
                                <div class="mb-6" x-show="selectedCategory">
                                    <h4 class="font-bold text-sm text-marine-900 dark:text-marine-100 mb-3">Available Items</h4>
                                    <div class="grid grid-cols-2 gap-3">
                                        @foreach($equipmentCategories as $category)
                                        <template x-if="selectedCategory === {{ $category->id }}">
                                            <div class="col-span-2 grid grid-cols-2 gap-3">
                                                @foreach($category->items as $item)
                                                <button type="button" @click="addEquipment({{ $item->id }}, '{{ $item->name }}', '{{ $item->unit }}')"
                                                        class="text-left p-3 bg-gray-50 dark:bg-marine-800 border dark:border-marine-700 rounded hover:bg-marine-100 dark:hover:bg-marine-700 transition">
                                                    <div class="font-medium text-sm text-gray-900 dark:text-gray-100">{{ $item->name }}</div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $item->description }}</div>
                                                </button>
                                                @endforeach
                                            </div>
                                        </template>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Selected Equipment Table -->
                                <div>
                                    <h4 class="font-bold text-sm text-marine-900 dark:text-marine-100 mb-3">Selected Equipment Items</h4>
                                    <template x-if="equipment.length === 0">
                                        <p class="text-sm text-gray-500 dark:text-gray-400 italic">No items selected yet. Click items from categories above.</p>
                                    </template>
                                    <template x-if="equipment.length > 0">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead class="bg-gray-50 dark:bg-marine-800">
                                                <tr>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Item</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Quantity</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Unit</th>
                                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white dark:bg-marine-900 divide-y divide-gray-200 dark:divide-gray-700">
                                                <template x-for="(equip, index) in equipment" :key="index">
                                                    <tr>
                                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="equip.name"></td>
                                                        <td class="px-4 py-3">
                                                            <input type="number" x-model="equip.quantity" :name="'equipment['+index+'][quantity]'" min="1" class="w-20 px-2 py-1 border dark:border-marine-700 rounded text-sm bg-white dark:bg-marine-950 text-gray-900 dark:text-gray-100">
                                                            <input type="hidden" :name="'equipment['+index+'][equipment_item_id]'" :value="equip.id">
                                                        </td>
                                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400" x-text="equip.unit"></td>
                                                        <td class="px-4 py-3 text-right">
                                                            <button type="button" @click="removeEquipment(index)" class="text-red-500 hover:text-red-700 text-sm font-medium">Remove</button>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </template>
                                </div>

                                <!-- Special Instructions -->
                                <div class="mt-6">
                                    <label class="theme-label">Special Instructions</label>
                                    <textarea name="special_instructions" rows="2" class="theme-input" placeholder="Safety requirements, gate pass info, etc."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 4: Assignment -->
                    <div x-show="step === 4" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                         <div class="border-b dark:border-marine-800 pb-4 mb-6">
                            <h3 class="text-lg font-medium text-marine-900 dark:text-marine-100">Step 4: Resource Assignment</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Assign team members.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="theme-label">Lead Engineer</label>
                                <select name="assigned_engineer_id" class="theme-input">
                                    @foreach($engineers as $eng)
                                        <option value="{{ $eng->id }}" {{ auth()->id() == $eng->id ? 'selected' : '' }}>{{ $eng->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                             <div class="md:col-span-2">
                                <label class="theme-label mb-2 block">Assign Technicians</label>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    @foreach($technicians as $tech)
                                    <label class="flex items-center space-x-3 bg-gray-50 dark:bg-marine-800 p-3 rounded border dark:border-marine-700 cursor-pointer">
                                        <input type="checkbox" name="technicians[]" value="{{ $tech->id }}" class="rounded text-marine-600 focus:ring-marine-500">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $tech->name }}</span>
                                            <span class="text-xs text-gray-400">{{ $tech->email }}</span>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                     <!-- STEP 5: Materials & Attachments -->
                    <div x-show="step === 5" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                        <div class="border-b dark:border-marine-800 pb-4 mb-6">
                            <h3 class="text-lg font-medium text-marine-900 dark:text-marine-100">Step 5: Materials & Attachments</h3>
                             <p class="text-sm text-gray-500 dark:text-gray-400">Request materials and upload files.</p>
                        </div>

                        <!-- Materials -->
                        <div class="mb-8">
                            <div class="flex justify-between items-center mb-2">
                                <label class="theme-label text-lg">Material Requirements</label>
                                <button type="button" @click="addMaterial()" class="text-sm text-blue-600 hover:text-blue-500 font-medium">+ Add Item</button>
                            </div>
                            
                            <template x-if="materials.length === 0">
                                <p class="text-sm text-gray-500 dark:text-gray-400 italic mb-4">No materials requested yet.</p>
                            </template>

                            <div class="space-y-3">
                                <template x-for="(mat, index) in materials" :key="index">
                                    <div class="flex space-x-2">
                                        <input type="text" :name="'materials['+index+'][name]'" x-model="mat.name" placeholder="Item Description" class="theme-input flex-grow" required>
                                        <input type="number" :name="'materials['+index+'][qty]'" x-model="mat.qty" placeholder="Qty" class="theme-input w-24" min="1" required>
                                        <button type="button" @click="removeMaterial(index)" class="text-red-500 hover:text-red-700 p-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Attachments -->
                        <div>
                             <label class="theme-label text-lg mb-2 block">Attachments / Photos</label>
                             <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
                                <input type="file" name="attachments[]" multiple class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Upload relevant documents, photos, or customer requests. (Max 10MB)</p>
                             </div>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="mt-8 pt-6 border-t dark:border-marine-800 flex justify-between">
                        <button type="button" x-show="step > 1" @click="step--" class="bg-gray-50 dark:bg-gray-7000 text-white px-6 py-2 rounded font-bold uppercase text-xs tracking-widest hover:bg-gray-600">
                            Back
                        </button>
                        <div x-show="step === 1"></div> <!-- Spacer -->

                        <button type="button" x-show="step < 5" @click="nextStep()" class="bg-marine-700 hover:bg-marine-600 text-white px-6 py-2 rounded font-bold uppercase text-xs tracking-widest">
                            Next
                        </button>

                        <button type="submit" x-show="step === 5" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded font-bold uppercase text-xs tracking-widest">
                            Create Job
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function jobWizard() {
            return {
                step: 1,
                stepLabels: ['Basic Info', 'Customer', 'Service', 'Resource', 'Finish'],
                customer_id: '',
                vessel_id: '',
                requested_by_contact_id: '',
                vessels: @json($vessels),
                contacts: @json($contacts),
                init() {
                    flatpickr("#scheduled_datepicker", {
                        enableTime: true,
                        dateFormat: "Y-m-d H:i",
                        minDate: "today",
                        time_24hr: true
                    });

                    this.$watch('vessel_id', (value) => {
                        if (!value) return;
                        const vessel = this.vessels.find(v => v.id == value);
                        if (vessel && vessel.company_contact_id) {
                            // Check if contact belongs to current customer in filtered list
                            const contactExists = this.filteredContacts.find(c => c.id == vessel.company_contact_id);
                            if (contactExists) {
                                this.requested_by_contact_id = vessel.company_contact_id;
                            }
                        }
                    });
                },
                materials: [],
                equipment: [],
                selectedCategory: null,
                formData: {
                    job_type: 'Field Service'
                },
                addEquipment(id, name, unit) {
                    // Check if already added
                    if (this.equipment.find(s => s.id === id)) {
                        alert('This equipment item is already added.');
                        return;
                    }
                    this.equipment.push({ id, name, unit, quantity: 1 });
                },
                removeEquipment(index) {
                    this.equipment.splice(index, 1);
                },
                get filteredVessels() {
                    console.log('Customer ID:', this.customer_id);
                    console.log('All Vessels:', this.vessels);
                    if (!this.customer_id) return [];
                    const filtered = this.vessels.filter(v => {
                        console.log('Vessel:', v.name, 'Customer ID:', v.customer_id, 'Match:', v.customer_id == this.customer_id);
                        return v.customer_id == this.customer_id;
                    });
                    console.log('Filtered Vessels:', filtered);
                    return filtered;
                },
                get filteredContacts() {
                     if (!this.customer_id) return [];
                     // Filter by customer and optionally by permission (if enforced)
                     return this.contacts.filter(c => c.customer_id == this.customer_id && c.can_request_job); 
                },
                nextStep() {
                    // Simple validation check: Get all inputs in current step
                    // This is a basic implementation. For production, a more robust validator is better.
                    const container = document.querySelector('[x-show="step === ' + this.step + '"]');
                    const inputs = container.querySelectorAll('input, select, textarea');
                    let valid = true;
                    
                    inputs.forEach(input => {
                        if (!input.checkValidity()) {
                            input.reportValidity();
                            valid = false;
                        }
                    });

                    if (valid) {
                        this.step++;
                    }
                },
                addMaterial() {
                    this.materials.push({ name: '', qty: 1 });
                },
                removeMaterial(index) {
                    this.materials.splice(index, 1);
                }
            }
        }
    </script>
</x-app-layout>
