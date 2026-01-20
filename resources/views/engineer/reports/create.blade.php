<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Service Report') }} - {{ $job->job_number }}
        </h2>
    </x-slot>

    <script>
        window.initialJobGroups = @json($prefilledGroups ?? []);
    </script>

    <div class="py-12" x-data="{
        service_groups: window.initialJobGroups,
        charges: [],
        
        addServiceGroup() {
            this.service_groups.push({
                service_type_id: '',
                items: [
                    { description: '', quantity: 1 }
                ]
            });
        },

        removeServiceGroup(index) {
            this.service_groups.splice(index, 1);
        },

        addItemToGroup(groupIndex) {
            this.service_groups[groupIndex].items.push({
                description: '',
                quantity: 1
            });
        },

        removeItemFromGroup(groupIndex, itemIndex) {
            this.service_groups[groupIndex].items.splice(itemIndex, 1);
        },

        addCharge() {
            this.charges.push({
                description: '',
                quantity: 1,
                unit_price: 0
            });
        },

        removeCharge(index) {
            this.charges.splice(index, 1);
        },

        technicians: [
            { user_id: '', hours: '' }
        ],

        addTechnician() {
            this.technicians.push({ user_id: '', hours: '' });
        },

        removeTechnician(index) {
            this.technicians.splice(index, 1);
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('engineer.jobs.report.store', $job->id) }}">
                @csrf
                
                <!-- Job Summary -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 flex items-center mb-4">
                            <svg class="h-6 w-6 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Job Details
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="text-xs text-gray-500 dark:text-gray-400 uppercase">Customer</label>
                                <p class="font-semibold text-gray-900 dark:text-gray-200">{{ $job->customer->company_name }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 dark:text-gray-400 uppercase">Vessel</label>
                                <p class="font-semibold text-gray-900 dark:text-gray-200">{{ $job->vessel->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 dark:text-gray-400 uppercase">Date</label>
                                <input type="date" name="report_date" value="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Left Column: Services & Notes -->
                    <div class="lg:col-span-2 space-y-6">
                        
                        <!-- 1. Services Performed (Grouped) -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Services Performed</h3>
                                    <button type="button" @click="addServiceGroup()" class="text-sm bg-indigo-100 text-indigo-700 py-1 px-3 rounded hover:bg-indigo-200">
                                        + Add Additional Service Group
                                    </button>
                                </div>
                                
                                <div class="space-y-6">
                                    <template x-for="(group, groupIndex) in service_groups" :key="groupIndex">
                                        <div class="p-4 border dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-700 relative">
                                            <!-- Remove Group Button -->
                                            <button type="button" @click="removeServiceGroup(groupIndex)" class="absolute top-2 right-2 text-red-500 hover:text-red-700 text-xs font-bold uppercase">
                                                Remove Group
                                            </button>
                                            
                                            <!-- Service Type Select -->
                                            <div class="mb-4 pr-16 dark:text-gray-200">
                                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Service Activity</label>
                                                <select :name="'service_groups['+groupIndex+'][service_type_id]'" x-model="group.service_type_id" class="text-sm rounded border-gray-300 w-full dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:border-indigo-500 focus:ring-indigo-500" required>
                                                    <option value="">-- Select Service Type (e.g., Refilling, Hydrotest) --</option>
                                                    @foreach($serviceTypes as $service)
                                                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Inner Items List -->
                                            <div class="space-y-3 bg-white dark:bg-gray-800 p-3 rounded border dark:border-gray-600">
                                                <template x-for="(item, itemIndex) in group.items" :key="itemIndex">
                                                    <div class="flex items-start gap-2">
                                                        <div class="flex-grow">
                                                            <label class="block text-xs text-gray-400 mb-1" x-show="itemIndex === 0">Item / Quantity</label>
                                                            <div class="flex gap-2">
                                                                <!-- Job Item Select (Populates Description) -->
                                                                @if($job->items->count() > 0 || $job->equipment->count() > 0)
                                                                    <div class="flex-grow">
                                                                        <select :name="'service_groups['+groupIndex+'][items]['+itemIndex+'][description]'" x-model="item.description" class="text-sm rounded border-gray-300 w-full dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" required>
                                                                            <option value="">-- Select Item --</option>
                                                                             <!-- Equipment (Master) -->
                                                                            @foreach($job->equipment as $equip)
                                                                                <option value="{{ $equip->equipmentItem->name ?? 'Item' }}">
                                                                                    {{ $equip->equipmentItem->name ?? 'Unknown' }} (Ref Qty: {{ $equip->quantity }})
                                                                                </option>
                                                                            @endforeach

                                                                            <!-- Custom Items -->
                                                                            @foreach($job->items as $jItem)
                                                                                <option value="{{ $jItem->name }}">
                                                                                    {{ $jItem->name }} (Ref Qty: {{ $jItem->quantity }})
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                @else
                                                                    <input type="text" :name="'service_groups['+groupIndex+'][items]['+itemIndex+'][description]'" x-model="item.description" placeholder="Item Name..." class="flex-grow text-sm rounded border-gray-300 dark:bg-gray-700 dark:text-white" required>
                                                                @endif
                                                                
                                                                <!-- Quantity Input -->
                                                                <div class="w-20">
                                                                     <input type="number" :name="'service_groups['+groupIndex+'][items]['+itemIndex+'][quantity]'" x-model="item.quantity" min="1" placeholder="Qty" class="text-sm rounded border-gray-300 w-full dark:bg-gray-700 dark:text-white" required>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Remove Item Button -->
                                                        <button type="button" @click="removeItemFromGroup(groupIndex, itemIndex)" class="mt-6 text-red-400 hover:text-red-600">
                                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    </div>
                                                </template>

                                                <!-- Add Item Button -->
                                                <button type="button" @click="addItemToGroup(groupIndex)" class="mt-2 text-xs font-semibold text-blue-600 hover:text-blue-800 flex items-center">
                                                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                    Add Item to this Service
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                    
                                    <!-- Empty State / Initial Prompt -->
                                    <div x-show="service_groups.length === 0" class="text-center py-8 bg-gray-50 dark:bg-gray-900 border-2 border-dashed border-gray-300 rounded-lg">
                                        <p class="text-gray-500 dark:text-gray-400 mb-2">No service activities added yet.</p>
                                        <button type="button" @click="addServiceGroup()" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 shadow font-semibold">
                                            Start Adding Services
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Work Description -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Work Description & Notes</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Work Done / Observations</label>
                                        <textarea name="work_description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300" required placeholder="Describe the work carried out..."></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Recommendations</label>
                                        <textarea name="recommendations" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300" placeholder="Any further actions required..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column: Charges & Summary -->
                    <div class="space-y-6">
                        
                        <!-- 3. Additional Charges (Parts) -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Materials / Spares Used</h3>
                                
                                <!-- From Stock -->
                                @if($job->materials && $job->materials->count() > 0)
                                <div class="mb-6">
                                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">From Stock (Approved/Issued)</h4>
                                    <div class="space-y-2">
                                        @foreach($job->materials as $material)
                                        <label class="flex items-center p-3 border rounded-md bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer">
                                            <input type="checkbox" 
                                                   name="stock_materials[]" 
                                                   value="{{ $material->id }}" 
                                                   class="rounded text-blue-600 mr-3"
                                                   checked>
                                            <div class="flex-1 grid grid-cols-2 gap-4">
                                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $material->item_name }}</span>
                                                <span class="text-sm text-gray-600 dark:text-gray-400">Qty: {{ $material->quantity }} | Status: {{ $material->status }}</span>
                                            </div>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                <!-- Manual Entry -->
                                <div>
                                    <div class="flex justify-between items-center mb-3">
                                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Additional Materials (Manual Entry)</h4>
                                        <button type="button" @click="addCharge()" class="text-sm bg-indigo-100 text-indigo-700 py-1 px-3 rounded hover:bg-indigo-200">
                                            + Add Item
                                        </button>
                                    </div>
                                    
                                    <div class="space-y-4">
                                        <template x-for="(charge, index) in charges" :key="index">
                                            <div class="p-3 border rounded-md bg-gray-50 dark:bg-gray-700 relative">
                                                <button type="button" @click="removeCharge(index)" class="absolute top-2 right-2 text-red-500 hover:text-red-700">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                                
                                                <div class="grid grid-cols-1 gap-2">
                                                    <input type="text" :name="'manual_materials['+index+'][description]'" x-model="charge.description" placeholder="Item Name / Part" class="block w-full text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                                                    <div class="flex gap-2">
                                                        <input type="number" :name="'manual_materials['+index+'][quantity]'" x-model="charge.quantity" min="1" placeholder="Qty" class="block w-20 text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                                                        <!-- Price is optional/hidden for Engineer -->
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                        <div x-show="charges.length === 0" class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">
                                            No additional materials added.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Technician Hours (Overtime) -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Technician Hours</h3>
                                    <button type="button" @click="addTechnician()" class="text-sm bg-indigo-100 text-indigo-700 py-1 px-3 rounded hover:bg-indigo-200">
                                        + Add Technician
                                    </button>
                                </div>

                                <div class="space-y-3">
                                    <template x-for="(tech, index) in technicians" :key="index">
                                        <div class="flex gap-2 items-start">
                                            <div class="flex-grow">
                                                <label class="block text-xs text-gray-400 mb-1" x-show="index === 0">Technician Name</label>
                                                <select :name="'technicians['+index+'][user_id]'" x-model="tech.user_id" class="block w-full text-sm rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                                    <option value="">Select Technician</option>
                                                    @foreach($employees as $emp)
                                                        <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="w-24">
                                                <label class="block text-xs text-gray-400 mb-1" x-show="index === 0">Hours</label>
                                                <input type="number" :name="'technicians['+index+'][hours]'" x-model="tech.hours" min="0.5" step="0.5" class="block w-full text-sm rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required placeholder="Hrs">
                                            </div>
                                            <button type="button" @click="removeTechnician(index)" class="mt-6 text-red-400 hover:text-red-600">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                    <div class="bg-blue-50 text-blue-800 p-4 rounded-md text-sm mb-4">
                                        <strong>Note:</strong> Logged hours will be automatically sent to Payroll for overtime calculation.
                                    </div>

                                    <button type="submit" onclick="return confirm('Submit Service Report? This will mark the job as Completed.')" class="w-full bg-indigo-600 text-white px-4 py-3 rounded-lg font-bold hover:bg-indigo-700 shadow-lg transition transform hover:scale-105">
                                        Submit Report & Complete Job
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
