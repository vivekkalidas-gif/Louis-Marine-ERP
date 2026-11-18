<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight">
                    {{ __('Create Field Service Report') }} - {{ $job->job_number }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Record service activities, observations, customer sign-off, and materials used</p>
            </div>
            <a href="{{ route('engineer.jobs.show', $job->id) }}" class="btn-ghost text-xs">
                Back to Job Details
            </a>
        </div>
    </x-slot>

    <script>
        window.initialJobGroups = @json($prefilledGroups ?? []);
    </script>

    <div class="py-6 space-y-6" x-data="{
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
        <div class="max-w-7xl mx-auto space-y-6">
            <form method="POST" action="{{ route('engineer.jobs.report.store', $job->id) }}">
                @csrf
                
                <!-- Job Summary Header Card -->
                <div class="theme-card">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-4 pb-2 border-b border-slate-200 dark:border-slate-700/80">
                        <svg class="h-5 w-5 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span>Field Service Order Details</span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="theme-label">Customer Company</label>
                            <p class="font-bold text-slate-900 dark:text-white text-sm">{{ $job->customer->company_name }}</p>
                        </div>
                        <div>
                            <label class="theme-label">Vessel Name</label>
                            <p class="font-bold text-slate-900 dark:text-white text-sm">{{ $job->vessel->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="theme-label">Service Report Date</label>
                            <input type="date" name="report_date" value="{{ date('Y-m-d') }}" class="theme-input text-xs py-2" required>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Left Column: Services & Description -->
                    <div class="lg:col-span-2 space-y-6">
                        
                        <!-- 1. Services Performed -->
                        <div class="theme-card">
                            <div class="flex justify-between items-center mb-4 pb-2 border-b border-slate-200 dark:border-slate-700/80">
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Services Performed</h3>
                                <button type="button" @click="addServiceGroup()" class="btn-secondary text-xs">
                                    + Add Additional Service Group
                                </button>
                            </div>
                            
                            <div class="space-y-5">
                                <template x-for="(group, groupIndex) in service_groups" :key="groupIndex">
                                    <div class="p-4 border border-slate-200 dark:border-slate-700/80 rounded-xl bg-slate-50/50 dark:bg-slate-900/40 relative">
                                        <!-- Remove Group Button -->
                                        <button type="button" @click="removeServiceGroup(groupIndex)" class="absolute top-3 right-3 text-rose-600 dark:text-rose-400 hover:text-rose-700 text-xs font-bold uppercase">
                                            Remove Group
                                        </button>
                                        
                                        <!-- Service Type Select -->
                                        <div class="mb-4 pr-24">
                                            <label class="theme-label">Service Activity Type</label>
                                            <select :name="'service_groups['+groupIndex+'][service_type_id]'" x-model="group.service_type_id" class="theme-input text-xs py-2" required>
                                                <option value="">-- Select Service Type (e.g., Refilling, Hydrotest) --</option>
                                                @foreach($serviceTypes as $service)
                                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Inner Items List -->
                                        <div class="space-y-3 bg-white dark:bg-slate-800 p-3.5 rounded-lg border border-slate-200 dark:border-slate-700/80">
                                            <template x-for="(item, itemIndex) in group.items" :key="itemIndex">
                                                <div class="flex items-start gap-2">
                                                    <div class="flex-grow">
                                                        <label class="theme-label mb-1" x-show="itemIndex === 0">Equipment / Item Description</label>
                                                        <div class="flex gap-2">
                                                            @if($job->items->count() > 0 || $job->equipment->count() > 0)
                                                                <div class="flex-grow">
                                                                    <select :name="'service_groups['+groupIndex+'][items]['+itemIndex+'][description]'" x-model="item.description" class="theme-input text-xs py-2" required>
                                                                        <option value="">-- Select Equipment Item --</option>
                                                                        @foreach($job->equipment as $equip)
                                                                            <option value="{{ $equip->equipmentItem->name ?? 'Item' }}">
                                                                                {{ $equip->equipmentItem->name ?? 'Unknown' }} (Ref Qty: {{ $equip->quantity }})
                                                                            </option>
                                                                        @endforeach

                                                                        @foreach($job->items as $jItem)
                                                                            <option value="{{ $jItem->name }}">
                                                                                {{ $jItem->name }} (Ref Qty: {{ $jItem->quantity }})
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            @else
                                                                <input type="text" :name="'service_groups['+groupIndex+'][items]['+itemIndex+'][description]'" x-model="item.description" placeholder="Item Name..." class="theme-input text-xs py-2 flex-grow" required>
                                                            @endif
                                                            
                                                            <div class="w-24">
                                                                 <input type="number" :name="'service_groups['+groupIndex+'][items]['+itemIndex+'][quantity]'" x-model="item.quantity" min="1" placeholder="Qty" class="theme-input text-xs py-2" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <button type="button" @click="removeItemFromGroup(groupIndex, itemIndex)" class="mt-6 text-rose-500 hover:text-rose-700">
                                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                </div>
                                            </template>

                                            <button type="button" @click="addItemToGroup(groupIndex)" class="mt-2 text-xs font-bold text-brand-600 dark:text-brand-400 hover:underline flex items-center">
                                                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                <span>Add Item to this Service Activity</span>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                                
                                <div x-show="service_groups.length === 0" class="text-center py-8 bg-slate-50 dark:bg-slate-900/60 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl">
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">No service activities added yet.</p>
                                    <button type="button" @click="addServiceGroup()" class="btn-primary text-xs">
                                        Start Adding Services
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Work Description & Recommendations -->
                        <div class="theme-card">
                            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4 pb-2 border-b border-slate-200 dark:border-slate-700/80">Work Description & Observations</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="theme-label">Work Done & Field Observations</label>
                                    <textarea name="work_description" rows="4" class="theme-input text-xs" required placeholder="Describe the inspection and servicing carried out..."></textarea>
                                </div>
                                <div>
                                    <label class="theme-label">Engineer Recommendations</label>
                                    <textarea name="recommendations" rows="3" class="theme-input text-xs" placeholder="Any further maintenance or replacement required..."></textarea>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column: Materials & Hours -->
                    <div class="space-y-6">
                        
                        <!-- Materials Used -->
                        <div class="theme-card">
                            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4 pb-2 border-b border-slate-200 dark:border-slate-700/80">Spares & Stock Materials</h3>
                            
                            @if($job->materials && $job->materials->count() > 0)
                            <div class="mb-6">
                                <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Approved Stock Requisitions</h4>
                                <div class="space-y-2">
                                    @foreach($job->materials as $material)
                                    <label class="flex items-center p-3 rounded-lg border border-slate-200 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-900/40 cursor-pointer">
                                        <input type="checkbox" name="stock_materials[]" value="{{ $material->id }}" class="rounded border-slate-300 text-brand-600 mr-3" checked>
                                        <div class="flex-1 text-xs">
                                            <span class="font-bold text-slate-900 dark:text-white block">{{ $material->item_name }}</span>
                                            <span class="text-slate-500 dark:text-slate-400">Qty: {{ $material->quantity }}</span>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <div>
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Additional Manual Parts</h4>
                                    <button type="button" @click="addCharge()" class="btn-ghost text-xs">
                                        + Add Part
                                    </button>
                                </div>
                                
                                <div class="space-y-3">
                                    <template x-for="(charge, index) in charges" :key="index">
                                        <div class="p-3 border border-slate-200 dark:border-slate-700/80 rounded-lg bg-slate-50/50 dark:bg-slate-900/40 relative">
                                            <button type="button" @click="removeCharge(index)" class="absolute top-2 right-2 text-rose-500 hover:text-rose-700">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                            
                                            <div class="grid grid-cols-1 gap-2">
                                                <input type="text" :name="'manual_materials['+index+'][description]'" x-model="charge.description" placeholder="Item Name / Part #" class="theme-input text-xs py-1.5" required>
                                                <input type="number" :name="'manual_materials['+index+'][quantity]'" x-model="charge.quantity" min="1" placeholder="Qty" class="theme-input text-xs py-1.5 w-24" required>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Technician Hours & Submission -->
                        <div class="theme-card">
                            <div class="flex justify-between items-center mb-4 pb-2 border-b border-slate-200 dark:border-slate-700/80">
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Technician Hours</h3>
                                <button type="button" @click="addTechnician()" class="btn-ghost text-xs">
                                    + Add Tech
                                </button>
                            </div>

                            <div class="space-y-3">
                                <template x-for="(tech, index) in technicians" :key="index">
                                    <div class="flex gap-2 items-start">
                                        <div class="flex-grow">
                                            <label class="theme-label mb-1" x-show="index === 0">Technician Name</label>
                                            <select :name="'technicians['+index+'][user_id]'" x-model="tech.user_id" class="theme-input text-xs py-2" required>
                                                <option value="">Select Technician</option>
                                                @foreach($employees as $emp)
                                                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="w-24">
                                            <label class="theme-label mb-1" x-show="index === 0">Hours</label>
                                            <input type="number" :name="'technicians['+index+'][hours]'" x-model="tech.hours" min="0.5" step="0.5" class="theme-input text-xs py-2" required placeholder="Hrs">
                                        </div>
                                        <button type="button" @click="removeTechnician(index)" class="mt-6 text-rose-500 hover:text-rose-700">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700/80">
                                <button type="submit" onclick="return confirm('Submit Service Report? This will mark the job as Completed.')" class="btn-primary w-full text-center text-xs py-3 font-bold">
                                    Submit Report & Complete Job
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
