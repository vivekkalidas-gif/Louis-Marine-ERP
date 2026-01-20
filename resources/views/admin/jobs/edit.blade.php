<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-marine-900 dark:text-marine-100 leading-tight">
            {{ __('Edit Service Job: ') . $job->job_number }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="jobWizard()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Stepper -->
            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Whoops! Something went wrong.</strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

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

            <div class="bg-white dark:bg-marine-900 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <!-- Main Job Form (Steps 1-4 wrappers) -->
                    <form action="{{ route('admin.jobs.update', ['job' => $job->id]) }}" method="POST" id="jobForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="next_step" :disabled="step !== 4" value="5">
                        <input type="hidden" name="equipment_submitted" value="1">

                        <!-- STEP 1: Basic Info -->
                        <div x-show="step === 1" x-transition:enter="transition ease-out duration-300">
                            <div class="border-b dark:border-marine-800 pb-4 mb-6">
                                <h3 class="text-lg font-medium text-marine-900 dark:text-marine-100">Step 1: Basic Information</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Classify the job and set priority.</p>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold mb-2">Job Number</label>
                                    <input type="text" name="job_number" value="{{ $job->job_number }}" class="w-full p-2 border rounded bg-gray-100 dark:bg-marine-800 dark:border-marine-700 cursor-not-allowed" readonly>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold mb-2">Title *</label>
                                    <input type="text" name="title" value="{{ $job->title }}" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold mb-2">Job Type *</label>
                                    <select name="job_type" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700" x-model="job_type" required>
                                        <option value="Field Service" {{ $job->job_type == 'Field Service' ? 'selected' : '' }}>Field Service</option>
                                        <option value="Workshop Service" {{ $job->job_type == 'Workshop Service' ? 'selected' : '' }}>Workshop Service</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold mb-2">Priority *</label>
                                    <select name="priority" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700">
                                        <option value="Low" {{ $job->priority == 'Low' ? 'selected' : '' }}>Low</option>
                                        <option value="Normal" {{ $job->priority == 'Normal' || $job->priority == 'Medium' ? 'selected' : '' }}>Normal</option>
                                        <option value="High" {{ $job->priority == 'High' ? 'selected' : '' }}>High</option>
                                        <option value="Urgent" {{ $job->priority == 'Urgent' ? 'selected' : '' }}>URGENT</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold mb-2">Scheduled (End/SLA)</label>
                                    <input type="date" name="scheduled_end" value="{{ $job->scheduled_end ? $job->scheduled_end->format('Y-m-d') : '' }}" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700">
                                </div>

                                <div>
                                    <label class="block text-sm font-bold mb-2">Source</label>
                                    <select name="job_source" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700">
                                        <option value="Email" {{ $job->job_source == 'Email' ? 'selected' : '' }}>Email</option>
                                        <option value="Call" {{ $job->job_source == 'Call' ? 'selected' : '' }}>Call</option>
                                        <option value="Walk-in" {{ $job->job_source == 'Walk-in' ? 'selected' : '' }}>Walk-in</option>
                                        <option value="Contract" {{ $job->job_source == 'Contract' ? 'selected' : '' }}>Contract</option>
                                        <option value="Other" {{ $job->job_source == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold mb-2">Reference / PO #</label>
                                    <input type="text" name="reference_no" value="{{ $job->reference_no }}" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700">
                                </div>
                            </div>

                            <div class="flex justify-end mt-6">
                                <a href="{{ route('admin.jobs.show', ['job' => $job->id]) }}" class="mr-4 bg-gray-200 text-gray-700 dark:text-gray-300 font-bold py-2 px-4 rounded hover:bg-gray-300">Cancel</a>
                                <button type="button" @click="nextStep()" class="bg-marine-600 hover:bg-marine-700 text-white font-bold py-2 px-4 rounded">
                                    Next
                                </button>
                            </div>
                        </div>

                        <!-- STEP 2: Customer & Vessel -->
                        <div x-show="step === 2" x-cloak x-transition:enter="transition ease-out duration-300">
                             <div class="border-b dark:border-marine-800 pb-4 mb-6">
                                <h3 class="text-lg font-medium text-marine-900 dark:text-marine-100">Step 2: Customer & Vessel</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Review client and vessel details.</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold mb-2">Vessel *</label>
                                    <select name="vessel_id" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700" required>
                                        @foreach($vessels as $vessel)
                                            <option value="{{ $vessel->id }}" {{ $job->vessel_id == $vessel->id ? 'selected' : '' }}>{{ $vessel->name }} ({{ $vessel->customer->company_name ?? 'Unknown Customer' }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold mb-2">Location Details</label>
                                    <input type="text" name="location_details" value="{{ $job->location_details }}" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700" placeholder="e.g. Ras Laffan Port">
                                </div>

                                <!-- Delivery Note Section (Workshop Service Only) -->
                                <div x-show="job_type === 'Workshop Service'" class="md:col-span-2 border-t dark:border-marine-700 pt-6 mt-4">
                                    <h4 class="text-md font-semibold text-marine-900 dark:text-marine-100 mb-4">📋 Delivery Note Details</h4>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-bold mb-2">Delivered By</label>
                                            <input type="text" name="delivered_by" value="{{ $job->delivered_by }}" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-bold mb-2">Contact Person</label>
                                            <input type="text" name="delivery_contact_person" value="{{ $job->delivery_contact_person }}" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-bold mb-2">Contact Number</label>
                                            <input type="text" name="delivery_contact_number" value="{{ $job->delivery_contact_number }}" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700">
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-bold mb-2">Delivery Notes</label>
                                            <textarea name="delivery_notes" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700" rows="3">{{ $job->delivery_notes }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex justify-end mt-6">
                                <button type="button" @click="step--" class="mr-4 bg-gray-200 text-gray-700 dark:text-gray-300 font-bold py-2 px-4 rounded hover:bg-gray-300">Back</button>
                                <button type="button" @click="nextStep()" class="bg-marine-600 hover:bg-marine-700 text-white font-bold py-2 px-4 rounded">Next</button>
                            </div>
                        </div>

                        <!-- STEP 3: Scope of Work (Updated with Equipment Selector) -->
                        <div x-show="step === 3" x-cloak x-transition:enter="transition ease-out duration-300">
                             <div class="border-b dark:border-marine-800 pb-4 mb-6">
                                <h3 class="text-lg font-medium text-marine-900 dark:text-marine-100">Step 3: Scope of Work</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Select equipment & services from categories.</p>
                            </div>

                            <div class="flex gap-6 mb-6">
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
                                </div>
                            </div>

                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-bold mb-2">Scope of Work / Special Instructions</label>
                                <textarea name="scope_of_work" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700" rows="6">{{ $job->scope_of_work }}</textarea>
                            </div>

                            <div class="flex justify-end mt-6">
                                <button type="button" @click="step--" class="mr-4 bg-gray-200 text-gray-700 dark:text-gray-300 font-bold py-2 px-4 rounded hover:bg-gray-300">Back</button>
                                <button type="button" @click="nextStep()" class="bg-marine-600 hover:bg-marine-700 text-white font-bold py-2 px-4 rounded">Next</button>
                            </div>
                        </div>

                        <!-- STEP 4: Assignment -->
                        <div x-show="step === 4" x-cloak x-transition:enter="transition ease-out duration-300">
                             <div class="border-b dark:border-marine-800 pb-4 mb-6">
                                <h3 class="text-lg font-medium text-marine-900 dark:text-marine-100">Step 4: Resource Assignment</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Assign team members.</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold mb-2">Assign Engineer</label>
                                    <select name="assigned_engineer_id" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700">
                                        <option value="">-- Unassigned --</option>
                                        @foreach($engineers as $engineer)
                                            <option value="{{ $engineer->id }}" {{ $job->assigned_engineer_id == $engineer->id ? 'selected' : '' }}>{{ $engineer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-span-1 md:col-span-2">
                                    <label class="block text-sm font-bold mb-2">Status</label>
                                    <select name="status" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700">
                                        <option value="Open" {{ $job->status == 'Open' ? 'selected' : '' }}>Open</option>
                                        <option value="Assigned" {{ $job->status == 'Assigned' ? 'selected' : '' }}>Assigned</option>
                                        <option value="In Progress" {{ $job->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="Completed" {{ $job->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="Closed" {{ $job->status == 'Closed' ? 'selected' : '' }}>Closed</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex justify-end mt-6">
                                <button type="button" @click="step--" class="mr-4 bg-gray-200 text-gray-700 dark:text-gray-300 font-bold py-2 px-4 rounded hover:bg-gray-300">Back</button>
                                <button type="submit" class="bg-marine-600 hover:bg-marine-700 text-white font-bold py-2 px-4 rounded">
                                    Update & Manage Items
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- STEP 5: Items & Finish -->
                    <div x-show="step === 5" x-cloak x-transition:enter="transition ease-out duration-300">
                        <div class="border-b dark:border-marine-800 pb-4 mb-6">
                             <h3 class="text-lg font-medium text-marine-900 dark:text-marine-100">Step 5: Materials & Attachments</h3>
                             <p class="text-sm text-gray-500 dark:text-gray-400">Manage items and finalize.</p>
                        </div>
                        
                        <!-- Existing Items List (Using separate logic as before) -->
                        <div class="bg-gray-50 dark:bg-marine-800 rounded p-4 mb-6">
                            <h4 class="font-bold text-md mb-3">Received Items (Delivery Note)</h4>
                            @if($job->items->count() > 0)
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="text-left text-gray-500 dark:text-gray-400 border-b">
                                            <th class="pb-2">Item Name</th>
                                            <th class="pb-2">Qty</th>
                                            <th class="pb-2">Description / Serial</th>
                                            <th class="pb-2 text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($job->items as $item)
                                            <tr class="border-b border-gray-100 last:border-0">
                                                <td class="py-2">{{ $item->name }}</td>
                                                <td class="py-2">{{ $item->quantity }}</td>
                                                <td class="py-2">{{ $item->description }}</td>
                                                <td class="py-2 text-right">
                                                    <button type="button" onclick="if(confirm('Remove this item?')) document.getElementById('delete-item-{{ $item->id }}').submit()" class="text-red-500 hover:text-red-700">Remove</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-gray-500 dark:text-gray-400 italic">No items added yet.</p>
                            @endif
                        </div>

                        <!-- Add Item Form -->
                        <div class="bg-gray-100 dark:bg-marine-700 p-4 rounded mb-6">
                            <h4 class="font-bold text-md mb-2">Add New Item</h4>
                            <form action="{{ route('admin.jobs.items.store', ['job' => $job->id]) }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                                @csrf
                                <div class="col-span-1 md:col-span-1">
                                    <label class="block text-sm font-bold mb-1">Item Name *</label>
                                    <input type="text" name="name" placeholder="e.g 5kg CO2" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700" required>
                                </div>
                                <div class="col-span-1 md:col-span-1">
                                    <label class="block text-sm font-bold mb-1">Quantity *</label>
                                    <input type="number" name="quantity" min="1" value="1" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700" required>
                                </div>
                                <div class="col-span-1 md:col-span-1">
                                    <label class="block text-sm font-bold mb-1">Description / Serial</label>
                                    <input type="text" name="description" placeholder="Optional" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700">
                                </div>
                                <div class="col-span-1 md:col-span-1">
                                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                        Add Item
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="flex justify-between mt-6">
                            <a href="?step=4" class="bg-gray-200 text-gray-700 dark:text-gray-300 font-bold py-2 px-4 rounded hover:bg-gray-300">Back to Assignment</a>
                            <div class="space-x-4">
                                <!-- Final Submit calling the MAIN FORM update from Step 5 -->
                                <button type="button" onclick="document.getElementById('jobForm').submit()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded">
                                    Update Job & Finish
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Delete Forms -->
    @foreach($job->items as $item)
        <form id="delete-item-{{ $item->id }}" action="{{ route('admin.jobs.items.destroy', ['job' => $job->id, 'item' => $item->id]) }}" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

    @php
        $equipmentData = $job->equipment->map(function($item){ 
            return [ 
                'id' => $item->equipment_item_id, 
                'name' => $item->equipmentItem->name ?? 'Item', 
                'unit' => $item->equipmentItem->unit ?? '', 
                'quantity' => $item->quantity 
            ]; 
        });
    @endphp

    <script>
        function jobWizard() {
            return {
                step: {{ request('step', 1) }},
                stepLabels: ['Basic Info', 'Customer', 'Service', 'Resource', 'Finish'],
                job_type: '{{ $job->job_type }}' || 'Field Service',
                selectedCategory: null,
                equipment: @json($equipmentData),
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
                nextStep() {
                     // Check validity of current step inputs
                     const container = document.querySelector('[x-show="step === ' + this.step + '"]');
                     if(container) {
                         const inputs = container.querySelectorAll('input[required], select[required], textarea[required]');
                         let valid = true;
                         inputs.forEach(input => {
                             if (!input.checkValidity()) {
                                 input.reportValidity();
                                 valid = false;
                             }
                         });
                         if (!valid) return;
                     }
                     this.step++;
                }
            }
        }
    </script>
</x-app-layout>
