<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs text-brand-600 dark:text-brand-400 font-semibold mb-1">
                    <a href="{{ route('engineer.jobs.index') }}" class="hover:underline">Field Service Jobs</a>
                    <span>/</span>
                    <span class="text-slate-500 dark:text-slate-400">New Service Order</span>
                </div>
                <h2 class="font-black text-2xl text-slate-900 dark:text-white leading-tight tracking-tight">
                    {{ __('Create New Service Job') }}
                </h2>
            </div>
            <a href="{{ route('engineer.jobs.index') }}" class="btn-ghost text-xs gap-1.5 font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Back to Jobs</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6 space-y-6" x-data="jobWizard()">
        <div class="max-w-7xl mx-auto space-y-6">
            
            <!-- Modern High-Tech Progress Stepper -->
            <div class="bg-slate-100 dark:bg-slate-950 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                <div class="flex items-center justify-between relative max-w-4xl mx-auto">
                    <!-- Connecting Progress Line -->
                    <div class="absolute top-5 left-10 right-10 h-1 bg-slate-200 dark:bg-slate-800 -z-0 rounded-full">
                        <div class="h-full bg-gradient-to-r from-brand-600 via-brand-500 to-emerald-500 transition-all duration-500 rounded-full"
                             :style="'width: ' + ((step - 1) / 4 * 100) + '%'"></div>
                    </div>

                    <template x-for="i in 5">
                        <div class="relative flex flex-col items-center z-10 cursor-pointer" @click="if(i < step) step = i">
                            <div :class="{
                                'bg-emerald-500 text-white shadow-lg shadow-emerald-500/30 scale-105': step > i,
                                'bg-brand-600 text-white ring-4 ring-brand-500/20 shadow-lg shadow-brand-500/30 scale-110': step === i,
                                'bg-white dark:bg-slate-900 text-slate-400 dark:text-slate-500 border border-slate-300 dark:border-slate-700': step < i
                            }" class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm transition-all duration-300">
                                <template x-if="step > i">
                                    <svg class="w-5 h-5 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </template>
                                <template x-if="step <= i">
                                    <span x-text="i"></span>
                                </template>
                            </div>
                            <span class="text-xs mt-2.5 font-bold transition-colors duration-300"
                                  :class="{
                                      'text-emerald-600 dark:text-emerald-400': step > i,
                                      'text-brand-600 dark:text-brand-400 font-extrabold': step === i,
                                      'text-slate-400 dark:text-slate-500': step < i
                                  }"
                                  x-text="stepLabels[i-1]"></span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Form Wizard Card -->
            <div class="theme-card p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-xl bg-white dark:bg-slate-900 rounded-2xl">
                <form method="POST" action="{{ route('engineer.jobs.store') }}" id="jobForm" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- STEP 1: Basic Info -->
                    <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                        <div class="flex items-center gap-3 border-b border-slate-200 dark:border-slate-800 pb-4 mb-6">
                            <div class="w-9 h-9 rounded-lg bg-brand-500/10 border border-brand-500/20 text-brand-600 dark:text-brand-400 flex items-center justify-center font-bold text-sm">
                                01
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-slate-900 dark:text-white">Basic Information</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Classify the field service dispatch and set priority level.</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="theme-label">Job Type *</label>
                                <select name="job_type" class="w-full text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 px-3.5 py-2.5 focus:ring-2 focus:ring-brand-500 font-semibold" x-model="formData.job_type" required>
                                    <option value="Field Service">Field Service</option>
                                    <option value="Workshop Service">Workshop Service</option>
                                </select>
                            </div>

                            <div>
                                <label class="theme-label">Priority *</label>
                                <select name="priority" class="w-full text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 px-3.5 py-2.5 focus:ring-2 focus:ring-brand-500 font-semibold" required>
                                    <option value="Normal">Normal</option>
                                    <option value="Low">Low</option>
                                    <option value="High">High</option>
                                    <option value="Emergency">Emergency</option>
                                </select>
                            </div>

                            <div>
                                <label class="theme-label">Scheduled Date & Time</label>
                                <input type="text" name="scheduled_at" class="w-full text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 px-3.5 py-2.5 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-brand-500 font-medium" id="scheduled_datepicker" 
                                       placeholder="Select Scheduled Date & Time">
                            </div>

                            <div>
                                <label class="theme-label">Service Source</label>
                                <select name="job_source" class="w-full text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 px-3.5 py-2.5 focus:ring-2 focus:ring-brand-500 font-medium">
                                    <option value="Email">Email Request</option>
                                    <option value="Call">Phone Call</option>
                                    <option value="Walk-in">Walk-in Customer</option>
                                    <option value="Contract">Annual Maintenance Contract</option>
                                </select>
                            </div>

                             <div class="md:col-span-2">
                                <label class="theme-label">PO Number / Reference</label>
                                <input type="text" name="reference_no" class="w-full text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 px-3.5 py-2.5 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-brand-500 font-medium" placeholder="e.g. PO-12345 / REF-8890">
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: Customer & Vessel -->
                    <div x-show="step === 2" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                        <div class="flex items-center gap-3 border-b border-slate-200 dark:border-slate-800 pb-4 mb-6">
                            <div class="w-9 h-9 rounded-lg bg-brand-500/10 border border-brand-500/20 text-brand-600 dark:text-brand-400 flex items-center justify-center font-bold text-sm">
                                02
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-slate-900 dark:text-white">Customer & Vessel Linkage</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Link the job dispatch to a customer account, vessel, and contact person.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="theme-label">Customer *</label>
                                <select name="customer_id" class="w-full text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 px-3.5 py-2.5 focus:ring-2 focus:ring-brand-500 font-bold" x-model="customer_id" required>
                                    <option value="">Select Customer Company</option>
                                    @foreach($customers as $c)
                                        <option value="{{ $c->id }}">{{ $c->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="theme-label">Vessel</label>
                                <select name="vessel_id" class="w-full text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 px-3.5 py-2.5 focus:ring-2 focus:ring-brand-500 font-semibold" x-model="vessel_id" :disabled="!customer_id">
                                    <option value="">Select Target Vessel</option>
                                    <template x-for="v in filteredVessels" :key="v.id">
                                        <option :value="v.id" x-text="v.name"></option>
                                    </template>
                                </select>
                                <p x-show="!customer_id" class="text-xs text-slate-400 dark:text-slate-500 mt-1">Please select a customer first to load associated vessels.</p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="theme-label">Requested By Contact Person</label>
                                <select name="requested_by_contact_id" class="w-full text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 px-3.5 py-2.5 focus:ring-2 focus:ring-brand-500 font-medium" :disabled="!customer_id" x-model="requested_by_contact_id">
                                    <option value="">Select Contact Person</option>
                                    <template x-for="c in filteredContacts" :key="c.id">
                                        <option :value="c.id" x-text="c.name + ' (' + (c.designation || 'Staff') + ')'"></option>
                                    </template>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="theme-label">Location Details</label>
                                <input type="text" name="location_details" class="w-full text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 px-3.5 py-2.5 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-brand-500 font-medium" placeholder="e.g. Ras Laffan Port, Berth 5 OR Louis Marine Service Center">
                            </div>

                            <!-- Delivery Note Section (Workshop Service Only) -->
                            <div x-show="formData.job_type === 'Workshop Service'" class="md:col-span-2 border-t border-slate-200 dark:border-slate-800 pt-6 mt-2 bg-slate-50 dark:bg-slate-950/60 p-4 rounded-xl border">
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-1 flex items-center gap-2">
                                    <span>📋 Workshop Material Drop-Off Details</span>
                                </h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Provide details of person who delivered equipment to the workshop</p>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="theme-label">Delivered By *</label>
                                        <input type="text" name="delivered_by" class="w-full text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 px-3.5 py-2.5 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-brand-500 font-medium" 
                                               placeholder="Name of courier or client representative"
                                               :required="formData.job_type === 'Workshop Service'">
                                    </div>

                                    <div>
                                        <label class="theme-label">Contact Person *</label>
                                        <input type="text" name="delivery_contact_person" class="w-full text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 px-3.5 py-2.5 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-brand-500 font-medium" 
                                               placeholder="Contact person name"
                                               :required="formData.job_type === 'Workshop Service'">
                                    </div>

                                    <div>
                                        <label class="theme-label">Contact Number *</label>
                                        <input type="tel" name="delivery_contact_number" class="w-full text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 px-3.5 py-2.5 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-brand-500 font-medium" 
                                               placeholder="+974 XXXX XXXX"
                                               :required="formData.job_type === 'Workshop Service'">
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="theme-label">Delivery Notes</label>
                                        <textarea name="delivery_notes" class="w-full text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 px-3.5 py-2.5 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-brand-500 font-medium" rows="3" 
                                                  placeholder="Special instructions or remarks about the material drop-off"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: Service Scope & Equipment Selection -->
                    <div x-show="step === 3" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                        <div class="flex items-center gap-3 border-b border-slate-200 dark:border-slate-800 pb-4 mb-6">
                            <div class="w-9 h-9 rounded-lg bg-brand-500/10 border border-brand-500/20 text-brand-600 dark:text-brand-400 flex items-center justify-center font-bold text-sm">
                                03
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-slate-900 dark:text-white">Scope of Work & Equipment</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Pick equipment items to inspect or calibrate from master categories.</p>
                            </div>
                        </div>

                        <div class="flex flex-col lg:flex-row gap-6">
                            <!-- LEFT SIDEBAR: Categories -->
                            <div class="w-full lg:w-64 flex-shrink-0">
                                <h4 class="font-bold text-xs text-slate-500 dark:text-slate-400 mb-3 uppercase tracking-wider">Equipment Categories</h4>
                                <div class="space-y-1.5 bg-slate-50 dark:bg-slate-950 p-2 rounded-xl border border-slate-200 dark:border-slate-800">
                                    @foreach($equipmentCategories as $category)
                                    <button type="button" @click="selectedCategory = {{ $category->id }}" 
                                            :class="selectedCategory === {{ $category->id }} ? 'bg-brand-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800/80'"
                                            class="w-full text-left px-3.5 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center justify-between cursor-pointer">
                                        <span>{{ $category->name }}</span>
                                        <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-black/20 text-white font-mono">{{ count($category->items) }}</span>
                                    </button>
                                    @endforeach
                                </div>
                            </div>

                            <!-- MAIN PANEL: Items & Selected Scopes -->
                            <div class="flex-1 space-y-6">
                                <!-- Available Items for Selected Category -->
                                <div x-show="selectedCategory" class="bg-slate-50 dark:bg-slate-950 p-4 rounded-xl border border-slate-200 dark:border-slate-800">
                                    <h4 class="font-bold text-xs text-slate-500 dark:text-slate-400 mb-3 uppercase tracking-wider">Available Items to Pick</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        @foreach($equipmentCategories as $category)
                                        <template x-if="selectedCategory === {{ $category->id }}">
                                            <div class="col-span-1 sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                @foreach($category->items as $item)
                                                <button type="button" @click="addEquipment({{ $item->id }}, '{{ addslashes($item->name) }}', '{{ $item->unit }}')"
                                                        class="text-left p-3.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl hover:border-brand-500 hover:shadow-md transition-all group cursor-pointer">
                                                    <div class="flex items-center justify-between">
                                                        <div class="font-bold text-xs text-slate-900 dark:text-white group-hover:text-brand-600 dark:group-hover:text-brand-400">{{ $item->name }}</div>
                                                        <span class="text-[10px] font-bold text-brand-600 dark:text-brand-400 bg-brand-500/10 px-2 py-0.5 rounded-md">+ Add</span>
                                                    </div>
                                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 font-mono">Code: {{ $item->code ?? 'N/A' }} | Model: {{ $item->model ?? 'N/A' }}</div>
                                                </button>
                                                @endforeach
                                            </div>
                                        </template>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Selected Equipment Table -->
                                <div>
                                    <h4 class="font-bold text-xs text-slate-500 dark:text-slate-400 mb-3 uppercase tracking-wider">Selected Equipment Schedule</h4>
                                    <template x-if="equipment.length === 0">
                                        <div class="p-8 text-center bg-slate-50 dark:bg-slate-950/60 rounded-xl border border-slate-200 dark:border-slate-800 text-xs text-slate-400">
                                            No equipment items selected yet. Click an item from the category list on the left to include it.
                                        </div>
                                    </template>
                                    <template x-if="equipment.length > 0">
                                        <div class="table-container border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-xs">
                                            <table class="theme-table">
                                                <thead>
                                                    <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                                        <th class="px-4 py-3">Item Description</th>
                                                        <th class="px-4 py-3">Quantity</th>
                                                        <th class="px-4 py-3">Unit</th>
                                                        <th class="px-4 py-3 text-right">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
                                                    <template x-for="(equip, index) in equipment" :key="index">
                                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                                            <td class="px-4 py-3 text-xs font-bold text-slate-900 dark:text-white" x-text="equip.name"></td>
                                                            <td class="px-4 py-3">
                                                                <input type="number" x-model="equip.quantity" :name="'equipment['+index+'][quantity]'" min="1" class="w-20 px-2.5 py-1 text-xs border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-bold">
                                                                <input type="hidden" :name="'equipment['+index+'][equipment_item_id]'" :value="equip.id">
                                                            </td>
                                                            <td class="px-4 py-3 text-xs font-medium text-slate-500 dark:text-slate-400" x-text="equip.unit"></td>
                                                            <td class="px-4 py-3 text-right">
                                                                <button type="button" @click="removeEquipment(index)" class="text-rose-600 dark:text-rose-400 hover:underline text-xs font-bold">Remove</button>
                                                            </td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                            </table>
                                        </div>
                                    </template>
                                </div>

                                <!-- Special Instructions -->
                                <div class="mt-4">
                                    <label class="theme-label">Special Scope Instructions</label>
                                    <textarea name="special_instructions" rows="3" class="w-full text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 px-3.5 py-2.5 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-brand-500 font-medium" placeholder="Specify any safety requirements, vessel gate passes, or calibration standards..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 4: Assignment -->
                    <div x-show="step === 4" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                        <div class="flex items-center gap-3 border-b border-slate-200 dark:border-slate-800 pb-4 mb-6">
                            <div class="w-9 h-9 rounded-lg bg-brand-500/10 border border-brand-500/20 text-brand-600 dark:text-brand-400 flex items-center justify-center font-bold text-sm">
                                04
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-slate-900 dark:text-white">Resource & Team Assignment</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Assign lead engineer and technician staff for field service execution.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="theme-label">Lead Engineer *</label>
                                <select name="assigned_engineer_id" class="w-full text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 px-3.5 py-2.5 focus:ring-2 focus:ring-brand-500 font-bold">
                                    @foreach($engineers as $eng)
                                        <option value="{{ $eng->id }}" {{ auth()->id() == $eng->id ? 'selected' : '' }}>{{ $eng->name }} ({{ $eng->email }})</option>
                                    @endforeach
                                </select>
                            </div>

                             <div class="md:col-span-2">
                                <label class="theme-label mb-2 block">Assign Assistant Technicians</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($technicians as $tech)
                                    <label class="flex items-center space-x-3 bg-slate-50 dark:bg-slate-950 p-3 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-brand-500 cursor-pointer transition-all">
                                        <input type="checkbox" name="technicians[]" value="{{ $tech->id }}" class="rounded text-brand-600 focus:ring-brand-500 w-4 h-4">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-bold text-slate-900 dark:text-slate-100">{{ $tech->name }}</span>
                                            <span class="text-[11px] text-slate-400 font-mono">{{ $tech->email }}</span>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                     <!-- STEP 5: Materials & Attachments -->
                    <div x-show="step === 5" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                        <div class="flex items-center gap-3 border-b border-slate-200 dark:border-slate-800 pb-4 mb-6">
                            <div class="w-9 h-9 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-sm">
                                05
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-slate-900 dark:text-white">Materials & Document Attachments</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Specify material requirements and upload customer PO or inspection files.</p>
                            </div>
                        </div>

                        <!-- Materials -->
                        <div class="mb-8">
                            <div class="flex justify-between items-center mb-3">
                                <label class="theme-label text-sm">Required Materials & Spare Parts</label>
                                <button type="button" @click="addMaterial()" class="text-xs text-brand-600 dark:text-brand-400 hover:underline font-bold">+ Add Material Item</button>
                            </div>
                            
                            <template x-if="materials.length === 0">
                                <div class="p-4 text-center bg-slate-50 dark:bg-slate-950/60 rounded-xl border border-slate-200 dark:border-slate-800 text-xs text-slate-400 italic">
                                    No material items requested yet. Click "+ Add Material Item" to add requirements.
                                </div>
                            </template>

                            <div class="space-y-3">
                                <template x-for="(mat, index) in materials" :key="index">
                                    <div class="flex space-x-2 items-center">
                                        <input type="text" :name="'materials['+index+'][name]'" x-model="mat.name" placeholder="Item Description (e.g. O-Rings, CO2 Cylinder Caps)" class="w-full text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 px-3.5 py-2 placeholder-slate-400 font-medium" required>
                                        <input type="number" :name="'materials['+index+'][qty]'" x-model="mat.qty" placeholder="Qty" class="w-24 text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 px-3.5 py-2 font-bold" min="1" required>
                                        <button type="button" @click="removeMaterial(index)" class="text-rose-500 hover:text-rose-700 p-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Attachments -->
                        <div>
                             <label class="theme-label text-sm mb-2 block">Attachments & Documents</label>
                             <div class="border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-brand-500 rounded-xl p-8 text-center bg-slate-50 dark:bg-slate-950/60 transition-all">
                                <svg class="w-10 h-10 mx-auto text-slate-400 dark:text-slate-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                <input type="file" name="attachments[]" multiple class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-brand-500/10 file:text-brand-600 dark:file:text-brand-400 hover:file:bg-brand-500/20 cursor-pointer">
                                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-2">Upload relevant PO, drawings, or customer photos. (Max 10MB per file)</p>
                             </div>
                        </div>
                    </div>

                    <!-- Navigation Footer -->
                    <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-800 flex justify-between items-center">
                        <button type="button" x-show="step > 1" @click="step--" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 transition-all shadow-xs cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            <span>Back</span>
                        </button>
                        <div x-show="step === 1"></div> <!-- Spacer -->

                        <button type="button" x-show="step < 5" @click="nextStep()" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-xs font-bold bg-brand-600 hover:bg-brand-500 text-white shadow-lg shadow-brand-500/20 transition-all cursor-pointer">
                            <span>Next Step</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>

                        <button type="submit" x-show="step === 5" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-500/20 transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Create Service Job</span>
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
                    if (!this.customer_id) return [];
                    return this.vessels.filter(v => v.customer_id == this.customer_id);
                },
                get filteredContacts() {
                     if (!this.customer_id) return [];
                     return this.contacts.filter(c => c.customer_id == this.customer_id && c.can_request_job); 
                },
                nextStep() {
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
