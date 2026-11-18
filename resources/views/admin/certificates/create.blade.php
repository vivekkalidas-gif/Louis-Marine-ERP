<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight">
                    {{ __('Issue New Marine Certificate') }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Select a service job and equipment category to populate template-driven inspection fields</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.certificates.index') }}" class="btn-ghost text-xs gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Cancel & Return</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6" 
         x-data="certificateFormData()" 
         @download-template.window="downloadSectionTemplate($event.detail.sectionId)"
         @import-csv.window="importSectionData($event.detail.sectionId, $event.detail.file, $event.detail.alpineData)"
         @open-excel.window="openExcelGrid($event.detail.sectionId, $event.detail.alpineData)"
         @add-entry.window="addInstance($event.detail.sectionId, $event.detail.alpineData)">
        
        <div class="max-w-7xl mx-auto space-y-6">

            <!-- Flash Success / Error Banner -->
            @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/80 text-emerald-800 dark:text-emerald-200 text-xs font-semibold flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800/80 text-rose-800 dark:text-rose-200 text-xs font-semibold flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
            @endif

            @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800/80 text-rose-800 dark:text-rose-200 text-xs space-y-1">
                <p class="font-bold flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Please correct the following errors:</span>
                </p>
                <ul class="list-disc pl-6 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- 70/30 LEFT-RIGHT SAAS SPLIT ARCHITECTURE -->
            <form method="POST" action="{{ route('admin.certificates.store') }}" id="certificateForm" @submit.prevent="submitForm">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    
                    <!-- LEFT COLUMN (70%): Certificate Creation Steps -->
                    <div class="lg:col-span-8 space-y-6">
                        
                        <!-- STEP 1: Service Job Selection -->
                        <div class="theme-card">
                            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-200 dark:border-slate-700/80">
                                <div class="w-8 h-8 rounded-xl bg-brand-600 text-white font-black text-sm flex items-center justify-center shrink-0 shadow-xs">
                                    1
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Job Details</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Enter the job number and choose the customer and vessel</p>
                                </div>
                            </div>

                            @include('admin.certificates.partials.job-details-fields')
                        </div>

                        <!-- STEP 2: Equipment Category Selection -->
                        <div x-show="formData.job_number.trim() && formData.customer_id" x-transition class="theme-card">
                            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-200 dark:border-slate-700/80">
                                <div class="w-8 h-8 rounded-xl bg-brand-600 text-white font-black text-sm flex items-center justify-center shrink-0 shadow-xs">
                                    2
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Select Equipment Category</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Choose category to dynamically load inspection template fields</p>
                                </div>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="theme-label mb-0">Equipment Category *</label>
                                    <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/50 px-1.5 py-0.5 rounded">Required</span>
                                </div>
                                <select name="equipment_category_id" 
                                        class="theme-input text-xs py-2.5 font-bold" 
                                        required 
                                        x-model="formData.category_id" 
                                        @change="loadTemplate()">
                                    <option value="">-- Select Equipment Category --</option>
                                    @include('admin.certificates.partials.category-options', ['categories' => $equipmentCategories, 'selected' => null])
                                </select>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1.5">
                                    Categories: Fire Extinguisher, Life Raft, SCBA, EEBD, Life Jacket, Immersion Suit, CO₂ Fixed System
                                </p>
                            </div>
                        </div>

                        <!-- STEP 3: Dynamic Template-Based Form Fields -->
                        <div x-show="templateLoaded" x-transition class="theme-card">
                            <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-200 dark:border-slate-700/80">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white font-black text-sm flex items-center justify-center shrink-0 shadow-xs">
                                        3
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold text-slate-900 dark:text-white" x-text="templateName || 'Equipment Details'"></h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Dynamic template inspection fields</p>
                                    </div>
                                </div>
                                <span class="badge-info text-xs">Dynamic Schema</span>
                            </div>

                            <!-- Dynamic Form Container -->
                            <div id="templateFormContainer">
                                <div class="text-center py-12 text-slate-400 dark:text-slate-500">
                                    <svg class="w-10 h-10 mx-auto text-slate-300 dark:text-slate-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Select an equipment category above to load form fields.</p>
                                </div>
                            </div>
                        </div>

                        <!-- STEP 4: Certificate Information & Expiry Settings -->
                        <div x-show="templateLoaded" x-transition class="theme-card">
                            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-200 dark:border-slate-700/80">
                                <div class="w-8 h-8 rounded-xl bg-purple-600 text-white font-black text-sm flex items-center justify-center shrink-0 shadow-xs">
                                    4
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Certificate Metadata & Validity</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Certificate issue date, validity period, and assigned verifying engineer</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="theme-label mb-0">Certificate Type *</label>
                                        <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/50 px-1.5 py-0.5 rounded">Required</span>
                                    </div>
                                    <select name="certificate_type_id" class="theme-input text-xs py-2.5 font-bold" required>
                                        <option value="">-- Select Certificate Type --</option>
                                        @foreach($types as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="theme-label mb-0">Assigned Verifier</label>
                                        <span class="badge-neutral text-[10px]">Optional</span>
                                    </div>
                                    <select name="assigned_verifier_id" class="theme-input text-xs py-2.5">
                                        <option value="">-- No Verifier Assigned --</option>
                                        @foreach($engineers as $engineer)
                                            <option value="{{ $engineer->id }}" {{ old('assigned_verifier_id') == $engineer->id ? 'selected' : '' }}>{{ $engineer->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">If assigned, verification approval is required before issue.</p>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="theme-label mb-0">Issue Date *</label>
                                        <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/50 px-1.5 py-0.5 rounded">Required</span>
                                    </div>
                                    <input type="date" name="issue_date" class="theme-input text-xs py-2.5 font-bold" required value="{{ date('Y-m-d') }}">
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="theme-label mb-0">Expiry Date *</label>
                                        <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/50 px-1.5 py-0.5 rounded">Required</span>
                                    </div>
                                    <input type="date" name="expiry_date" class="theme-input text-xs py-2.5 font-bold" required>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- RIGHT COLUMN (30% Sticky Panel): Certificate Summary & Actions -->
                    <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">
                        
                        <!-- Primary Action Panel -->
                        <div class="theme-card space-y-4">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white pb-3 border-b border-slate-200 dark:border-slate-700/80 flex items-center gap-2">
                                <svg class="w-4 h-4 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Save Certificate</span>
                            </h3>

                            <button type="submit" 
                                    class="btn-primary w-full text-center text-xs py-3 font-bold gap-2 shadow-md flex items-center justify-center"
                                    x-show="templateLoaded">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Create & Issue Certificate</span>
                            </button>

                            <a href="{{ route('admin.certificates.index') }}" class="btn-secondary w-full text-center text-xs py-2.5 font-bold gap-1.5 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                <span>Cancel & Return</span>
                            </a>
                        </div>

                        <!-- Live Certificate Metadata Summary Card -->
                        <div class="theme-card space-y-3 bg-slate-50/50 dark:bg-slate-900/40">
                            <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider pb-2 border-b border-slate-200 dark:border-slate-700/80">
                                Certificate Summary
                            </h4>

                            <div class="flex items-center justify-between text-xs py-1">
                                <span class="text-slate-500 dark:text-slate-400">Customer</span>
                                <span class="font-bold text-slate-900 dark:text-white truncate max-w-[140px]" x-text="selectedCustomer || 'Not Selected'"></span>
                            </div>

                            <div class="flex items-center justify-between text-xs py-1">
                                <span class="text-slate-500 dark:text-slate-400">Vessel Name</span>
                                <span class="font-bold text-slate-900 dark:text-white truncate max-w-[140px]" x-text="selectedVessel || 'Not Selected'"></span>
                            </div>

                            <div class="flex items-center justify-between text-xs py-1">
                                <span class="text-slate-500 dark:text-slate-400">Equipment Category</span>
                                <span class="font-bold text-brand-600 dark:text-brand-400 truncate max-w-[140px]" x-text="selectedCategoryName || 'Not Selected'"></span>
                            </div>

                            <div class="flex items-center justify-between text-xs py-1">
                                <span class="text-slate-500 dark:text-slate-400">Initial Status</span>
                                <span class="badge-neutral text-[10px] font-bold">DRAFT</span>
                            </div>
                        </div>

                        <!-- Helpful Guidance Banner -->
                        <div class="p-4 rounded-xl bg-sky-50/70 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800/60 text-xs space-y-2 text-sky-800 dark:text-sky-300">
                            <h4 class="font-bold flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Excel Grid Feature</span>
                            </h4>
                            <ul class="space-y-1 text-[11px] text-slate-600 dark:text-slate-400 list-disc list-inside">
                                <li>Use <strong>📊 Excel Grid Mode</strong> for fast tab-key spreadsheet data entry.</li>
                                <li>Pressing <strong>Tab</strong> on the last cell automatically appends a row & pre-fills previous inputs!</li>
                            </ul>
                        </div>

                    </div>

                </div>
            </form>
        </div>

        <!-- FULL-SCREEN EXCEL SPREADSHEET GRID MODAL -->
        <div x-show="excelModalOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="fixed inset-0 bg-slate-900/80 backdrop-blur-md z-50 flex flex-col p-4 md:p-6 overflow-hidden">
            
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl flex-1 flex flex-col overflow-hidden">
                <!-- Modal Top Navigation Header -->
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-950/60 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-500 text-white font-bold flex items-center justify-center shadow-md">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M9 4v16M15 4v16"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <span x-text="activeExcelSection ? activeExcelSection.name : ''"></span>
                                <span class="badge-emerald text-[10px]">Excel Grid Spreadsheet Editor</span>
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Use <kbd class="px-1.5 py-0.5 bg-slate-200 dark:bg-slate-800 rounded text-[10px] font-mono font-bold">TAB</kbd> key to navigate between cells. Pressing Tab on the last cell auto-inserts a new pre-filled row.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button" @click="addInstance(activeExcelSection.id, excelSectionAlpineData)" class="btn-secondary text-xs font-bold gap-1.5">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>+ Add Row</span>
                        </button>
                        <button type="button" @click="excelModalOpen = false" class="btn-primary text-xs font-bold gap-1 px-5 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Done & Save Grid</span>
                        </button>
                    </div>
                </div>

                <!-- Grid Table View Container -->
                <div class="flex-1 overflow-auto p-6" id="excel-grid-table-container">
                    <table class="w-full border-collapse border border-slate-200 dark:border-slate-800 text-left text-xs" id="excel-grid-table">
                        <thead class="bg-slate-100 dark:bg-slate-800 sticky top-0 z-10">
                            <tr>
                                <th class="p-3 border border-slate-200 dark:border-slate-700/80 font-bold text-slate-700 dark:text-slate-300 w-12 text-center bg-slate-200/70 dark:bg-slate-800">#</th>
                                <template x-for="field in (activeExcelSection ? activeExcelSection.fields : [])" :key="field.id">
                                    <th class="p-3 border border-slate-200 dark:border-slate-700/80 font-bold text-slate-800 dark:text-slate-200 min-w-[160px] bg-slate-100 dark:bg-slate-800">
                                        <div class="flex items-center justify-between gap-1">
                                            <span x-text="field.field_label"></span>
                                            <template x-if="field.is_required">
                                                <span class="text-amber-500 font-bold">*</span>
                                            </template>
                                        </div>
                                    </th>
                                </template>
                                <th class="p-3 border border-slate-200 dark:border-slate-700/80 font-bold text-slate-700 dark:text-slate-300 w-16 text-center bg-slate-100 dark:bg-slate-800">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
                            <template x-for="(instanceId, idx) in (excelSectionAlpineData ? excelSectionAlpineData.instanceIds : [])" :key="instanceId">
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                    <td class="p-2 border border-slate-200 dark:border-slate-800 text-center font-bold text-slate-500 dark:text-slate-400 bg-slate-50/50 dark:bg-slate-950/40" x-text="idx + 1"></td>
                                    
                                    <template x-for="(field, fIdx) in (activeExcelSection ? activeExcelSection.fields : [])" :key="field.id">
                                        <td class="p-1.5 border border-slate-200 dark:border-slate-800">
                                            <input type="text" 
                                                   :name="'field_' + field.id + '_instance_' + (idx + 1)"
                                                   class="theme-input text-xs py-1.5 px-2.5 w-full bg-transparent focus:bg-white dark:focus:bg-slate-800 border-transparent focus:border-brand-500 focus:ring-1 focus:ring-brand-500 font-medium"
                                                   :placeholder="field.placeholder || field.field_label"
                                                   @input="syncExcelInputToMainForm($event)"
                                                   @change="syncExcelInputToMainForm($event)"
                                                   @keydown.tab="handleExcelTab($event, idx, fIdx, fIdx === activeExcelSection.fields.length - 1)"
                                                   :data-field-label="field.field_label">
                                        </td>
                                    </template>

                                    <td class="p-1.5 border border-slate-200 dark:border-slate-800 text-center">
                                        <button type="button" 
                                                x-show="excelSectionAlpineData && excelSectionAlpineData.instanceIds.length > 1" 
                                                @click="excelSectionAlpineData.instanceIds = excelSectionAlpineData.instanceIds.filter(id => id !== instanceId)" 
                                                class="btn-ghost btn-sm p-1 text-rose-500 hover:text-rose-700" 
                                                title="Delete Row">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Footer Summary -->
                <div class="px-6 py-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-950/60 text-xs font-semibold text-slate-500 shrink-0">
                    <div>
                        <span>Total Rows: </span>
                        <strong class="text-slate-900 dark:text-white" x-text="excelSectionAlpineData ? excelSectionAlpineData.instanceIds.length : 0"></strong>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] text-slate-400">Pro-tip: All columns including Serial Number (auto-incremented) are auto-filled from the previous row!</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="{{ asset('js/cert-excel.js') }}?v={{ @filemtime(public_path('js/cert-excel.js')) }}"></script>
    <script>
        function certificateFormData() {
            return {
                formData: {
                    job_number: @json(old('job_number', '')),
                    customer_id: @json(old('customer_id', '')),
                    vessel_id: @json(old('vessel_id', '')),
                    category_id: ''
                },
                customers: @json($customers),
                vessels: @json($vessels),
                templateLoaded: false,
                templateName: '',
                currentTemplate: null,
                selectedCategoryName: '',
                excelModalOpen: false,
                activeExcelSection: null,
                excelSectionAlpineData: null,

                get selectedCustomer() {
                    return this.customers.find(c => c.id === this.formData.customer_id)?.company_name || '';
                },
                get selectedVessel() {
                    return this.vessels.find(v => v.id === this.formData.vessel_id)?.name || '';
                },
                // Vessels of the chosen customer; all vessels if none are linked to it
                customerVessels() {
                    const own = this.vessels.filter(v => v.customer_id === this.formData.customer_id);
                    return own.length ? own : this.vessels;
                },
                onCustomerChange() {
                    if (!this.customerVessels().some(v => v.id === this.formData.vessel_id)) {
                        this.formData.vessel_id = '';
                    }
                },

                async loadTemplate() {
                    if (!this.formData.category_id) {
                        this.templateLoaded = false;
                        this.selectedCategoryName = '';
                        return;
                    }

                    const catSelect = document.querySelector('[name="equipment_category_id"]');
                    const selectedCatOpt = catSelect.options[catSelect.selectedIndex];
                    if (selectedCatOpt) {
                        this.selectedCategoryName = selectedCatOpt.text;
                    }

                    try {
                        const url = `{{ route('admin.certificates.get-fields', 0) }}`.replace('/0', '/' + this.formData.category_id);
                        const response = await fetch(url);
                        const data = await response.json();
                        
                        if (data.template) {
                            this.currentTemplate = data.template;
                            this.templateName = data.template.name;
                            this.renderTemplate(data.template);
                            this.templateLoaded = true;

                            // Auto-select matching Certificate Type in Step 4
                            const typeSelect = document.querySelector('[name="certificate_type_id"]');
                            if (typeSelect) {
                                const templateNameLower = data.template.name.toLowerCase().trim();
                                const catNameLower = (this.selectedCategoryName || '').toLowerCase().trim();
                                
                                for (let i = 0; i < typeSelect.options.length; i++) {
                                    const optText = typeSelect.options[i].text.toLowerCase().trim();
                                    if (optText && (optText.includes(catNameLower) || templateNameLower.includes(optText) || optText.includes(templateNameLower))) {
                                        typeSelect.selectedIndex = i;
                                        break;
                                    }
                                }
                            }
                        } else {
                            alert('No template found for this equipment category. Please create a template first.');
                            this.templateLoaded = false;
                        }
                    } catch (error) {
                        console.error('Error loading template:', error);
                        alert('Failed to load template. Please try again.');
                    }
                },

                renderTemplate(template) {
                    const container = document.getElementById('templateFormContainer');
                    let html = '<div class="space-y-6">';
                    html += `<input type="hidden" name="template_id" value="${template.id}">`;
                    if (window.CertExcel) html += CertExcel.toolbarHtml();
                    
                    template.sections.forEach(section => {
                        html += this.renderSection(section);
                    });
                    
                    html += '</div>';
                    container.innerHTML = html;
                    
                    if (window.Alpine) {
                        try {
                            window.Alpine.initTree(container);
                        } catch (e) {
                            console.error('Alpine init error:', e);
                        }
                    }
                    
                    this.initAutoFillListeners();
                },

                renderSection(section) {
                    let html = `<div class="p-5 border border-slate-200 dark:border-slate-700/80 rounded-2xl bg-slate-50/40 dark:bg-slate-900/30 space-y-4">`;
                    html += `<div class="pb-3 border-b border-slate-200 dark:border-slate-700/80 flex items-center justify-between">`;
                    html += `<div>`;
                    html += `<h4 class="font-bold text-slate-900 dark:text-white text-sm flex items-center gap-2">${section.name}</h4>`;
                    if (section.description) {
                        html += `<p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">${section.description}</p>`;
                    }
                    html += `</div>`;
                    if (section.is_repeatable) {
                        html += `<span class="badge-info text-[10px]">Repeatable Grid</span>`;
                    }
                    html += `</div>`;

                    if (section.is_repeatable) {
                        html += this.renderRepeatableSection(section);
                    } else {
                        html += this.renderRegularSection(section);
                    }

                    html += `</div>`;
                    return html;
                },

                renderRepeatableSection(section) {
                    const sectionId = section.id;
                    let html = `<div data-cs-section="${sectionId}" x-data="{ instanceIds: [1], nextId: 2 }" class="space-y-4">`;
                    
                    // Header Toolbar (Excel Grid Mode button, CSV Template, Import CSV)
                    html += `<div class="flex items-center justify-between flex-wrap gap-2 p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700/80">`;
                    html += `<span class="text-xs font-bold text-slate-700 dark:text-slate-300">Repeatable Data Entries</span>`;
                    html += `<div class="flex items-center gap-2">`;
                    html += `<button type="button" @click="$dispatch('open-excel', { sectionId: '${sectionId}', alpineData: $data })" class="btn-primary btn-sm text-xs gap-1.5 font-bold shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M9 4v16M15 4v16"/></svg>
                        <span>📊 Excel Grid Mode</span>
                    </button>`;
                    html += `<button type="button" @click="$dispatch('download-template', { sectionId: '${sectionId}' })" class="btn-ghost btn-sm text-xs gap-1">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>CSV Template</span>
                    </button>`;
                    html += `<label class="btn-ghost btn-sm text-xs gap-1 cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        <span>Import CSV</span>
                        <input type="file" accept=".csv" class="hidden" @change="$dispatch('import-csv', { sectionId: '${sectionId}', file: $event.target.files[0], alpineData: $data })">
                    </label>`;
                    html += `</div></div>`;
                    
                    html += `<template x-for="instanceId in instanceIds" :key="instanceId">`;
                    html += `<div class="p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700/80 space-y-3 relative">`;
                    html += `<div class="flex justify-between items-center pb-2 border-b border-slate-100 dark:border-slate-700/60">`;
                    html += `<h5 class="font-bold text-xs text-slate-800 dark:text-slate-200">Entry #<span x-text="instanceIds.indexOf(instanceId) + 1"></span></h5>`;
                    html += `<button type="button" x-show="instanceIds.length > 1" @click="instanceIds = instanceIds.filter(id => id !== instanceId)" class="btn-ghost btn-sm text-xs text-rose-500 hover:text-rose-700">
                        <span>Remove Entry</span>
                    </button>`;
                    html += `</div>`;
                    
                    html += `<div class="grid grid-cols-1 ${section.layout_type === 'two_column' ? 'md:grid-cols-2' : ''} gap-4">`;
                    section.fields.forEach(field => {
                        html += this.renderField(field, true);
                    });
                    html += `</div></div></template>`;
                    
                    html += `<input type="hidden" :name="'section_${sectionId}_instances'" :value="instanceIds.length">`;
                    html += `<button type="button" @click="$dispatch('add-entry', { sectionId: '${sectionId}', alpineData: $data })" class="btn-secondary text-xs font-bold gap-1.5">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>+ Add Another Entry</span>
                    </button>`;
                    html += `</div>`;
                    return html;
                },

                renderRegularSection(section) {
                    let html = `<div class="grid grid-cols-1 ${section.layout_type === 'two_column' ? 'md:grid-cols-2' : ''} gap-5">`;
                    section.fields.forEach(field => {
                        html += this.renderField(field, false);
                    });
                    html += `</div>`;
                    return html;
                },

                renderField(field, isRepeatable) {
                    const colSpan = field.width === 'full' ? 'md:col-span-full' : (field.width === 'third' ? 'md:col-span-1' : '');
                    const fieldName = isRepeatable 
                        ? `:name="'field_${field.id}_instance_' + (instanceIds.indexOf(instanceId) + 1)"` 
                        : `name="field_${field.id}"`;
                    
                    let html = `<div class="${colSpan}">`;
                    html += `<div class="flex items-center justify-between mb-1.5">`;
                    html += `<label class="theme-label mb-0">${field.field_label}</label>`;
                    if (field.is_required) html += `<span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/50 px-1.5 py-0.5 rounded">Required</span>`;
                    html += `</div>`;
                    
                    html += this.renderFieldInput(field, fieldName, isRepeatable);
                    
                    if (field.help_text) {
                        html += `<p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">${field.help_text}</p>`;
                    }
                    html += `</div>`;
                    return html;
                },

                renderFieldInput(field, fieldName, isRepeatable) {
                    const required = field.is_required ? 'required' : '';
                    const baseClass = 'theme-input text-xs py-2.5';
                    const placeholder = field.placeholder ? `placeholder="${field.placeholder}"` : '';
                    const labelAttr = `data-field-label="${field.field_label.replace(/"/g, '&quot;')}"`;
                    
                    switch (field.field_type) {
                        case 'text':
                            return `<input type="text" ${fieldName} class="${baseClass}" ${required} ${placeholder} ${labelAttr}>`;
                        case 'number':
                            return `<input type="number" ${fieldName} class="${baseClass}" ${required} ${placeholder} ${labelAttr}>`;
                        case 'date':
                            return `<input type="date" ${fieldName} class="${baseClass}" ${required} ${placeholder} ${labelAttr}>`;
                        case 'textarea':
                            return `<textarea ${fieldName} class="${baseClass}" rows="3" ${required} ${placeholder} ${labelAttr}></textarea>`;
                        case 'radio':
                            let radioHtml = '<div class="flex flex-wrap gap-4 py-1">';
                            if (field.field_options) {
                                try {
                                    let opts = field.field_options;
                                    if (typeof opts === 'string') opts = JSON.parse(opts);
                                    if (opts && typeof opts === 'object' && !Array.isArray(opts) && opts.options) opts = opts.options;
                                    
                                    if (Array.isArray(opts)) {
                                        opts.forEach(opt => {
                                            radioHtml += `
                                                <label class="flex items-center gap-2 cursor-pointer">
                                                    <input type="radio" value="${opt}" ${fieldName.replace(':', '')} 
                                                           ${fieldName.includes(':name') ? `:name="${fieldName.match(/:name="'(.*)'"/)[1]}"` : ''}
                                                           class="text-brand-600 border-slate-300 focus:ring-brand-500" 
                                                           ${required} ${labelAttr}>
                                                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">${opt}</span>
                                                </label>`;
                                        });
                                    }
                                } catch (e) {
                                    console.error('Error parsing radio options:', e);
                                }
                            }
                            radioHtml += '</div>';
                            return radioHtml;
                        case 'select':
                        case 'dropdown':
                            let options = '<option value="">-- Select --</option>';
                            if (field.field_options) {
                                try {
                                    let opts = field.field_options;
                                    if (typeof opts === 'string') {
                                        opts = JSON.parse(opts);
                                    }
                                    if (opts && typeof opts === 'object' && !Array.isArray(opts) && opts.options) {
                                        opts = opts.options;
                                    }
                                    if (Array.isArray(opts)) {
                                        opts.forEach(opt => {
                                            options += `<option value="${opt}">${opt}</option>`;
                                        });
                                    }
                                } catch (e) {
                                    console.error('Error parsing field options:', e, field);
                                }
                            }
                            return `<select ${fieldName} class="${baseClass}" ${required} ${labelAttr}>${options}</select>`;
                        default:
                            return `<input type="text" ${fieldName} class="${baseClass}" ${required} ${placeholder} ${labelAttr}>`;
                    }
                },

                // EXCEL GRID OPEN & KEYBOARD TAB HANDLER
                openExcelGrid(sectionId, alpineData) {
                    const section = this.currentTemplate.sections.find(s => s.id === sectionId);
                    if (!section) return;
                    this.activeExcelSection = section;
                    this.excelSectionAlpineData = alpineData;
                    this.excelModalOpen = true;

                    // Sync values from DOM into modal inputs upon open
                    setTimeout(() => {
                        this.syncMainFormToExcelModal(section);
                    }, 100);
                },

                syncMainFormToExcelModal(section) {
                    if (!section || !this.excelSectionAlpineData) return;
                    const totalRows = this.excelSectionAlpineData.instanceIds.length;
                    
                    for (let r = 1; r <= totalRows; r++) {
                        section.fields.forEach(field => {
                            const name = `field_${field.id}_instance_${r}`;
                            const matchingInputs = document.querySelectorAll(`[name="${name}"]`);
                            let val = '';
                            matchingInputs.forEach(input => {
                                if (input.value) val = input.value;
                            });
                            if (val) {
                                matchingInputs.forEach(input => {
                                    input.value = val;
                                });
                            }
                        });
                    }
                },

                syncExcelInputToMainForm(e) {
                    const name = e.target.getAttribute('name');
                    const val = e.target.value;
                    if (name) {
                        const matchingInputs = document.querySelectorAll(`[name="${name}"]`);
                        matchingInputs.forEach(input => {
                            if (input !== e.target) {
                                input.value = val;
                            }
                        });
                    }
                },

                handleExcelTab(e, rowIdx, fieldIdx, isLastField) {
                    if (isLastField && !e.shiftKey) {
                        const totalRows = this.excelSectionAlpineData.instanceIds.length;
                        if (rowIdx === totalRows - 1) {
                            e.preventDefault();
                            this.addInstance(this.activeExcelSection.id, this.excelSectionAlpineData);
                            setTimeout(() => {
                                const inputs = document.querySelectorAll('#excel-grid-table tbody tr:last-child input, #excel-grid-table tbody tr:last-child select');
                                if (inputs.length > 0) {
                                    inputs[0].focus();
                                    if (inputs[0].select) inputs[0].select();
                                }
                            }, 180);
                        }
                    }
                },

                // AUTO INCREMENT TRAILING NUMBERS FOR SERIALS (875421 -> 875422, SN-101 -> SN-102)
                autoIncrementSerial(val) {
                    if (!val) return '';
                    const match = val.toString().match(/^(.*?)(\d+)$/);
                    if (match) {
                        const prefix = match[1];
                        const numStr = match[2];
                        const nextNum = (parseInt(numStr, 10) + 1).toString().padStart(numStr.length, '0');
                        return prefix + nextNum;
                    }
                    return val;
                },

                // ADD INSTANCE WITH 100% PRE-FILL FOR ALL COLUMNS IN BOTH MAIN FORM & EXCEL MODAL
                addInstance(sectionId, alpineData) {
                    const prevInstanceCount = alpineData.instanceIds.length;
                    const newId = alpineData.nextId++;
                    alpineData.instanceIds.push(newId);

                    if (prevInstanceCount > 0) {
                        const performCopy = () => {
                            const newInstanceNum = alpineData.instanceIds.length;
                            const prevInstanceNum = prevInstanceCount;
                            const section = this.currentTemplate.sections.find(s => s.id === sectionId);

                            if (section) {
                                section.fields.forEach(field => {
                                    const labelLower = field.field_label.toLowerCase();
                                    const isSerial = labelLower.includes('serial') || labelLower.includes('chassis') || labelLower.includes('qr');

                                    const prevName = `field_${field.id}_instance_${prevInstanceNum}`;
                                    const newName = `field_${field.id}_instance_${newInstanceNum}`;

                                    const prevElems = document.querySelectorAll(`[name="${prevName}"]`);
                                    const newElems = document.querySelectorAll(`[name="${newName}"]`);

                                    let prevVal = '';
                                    for (let p = 0; p < prevElems.length; p++) {
                                        if (prevElems[p].value) {
                                            prevVal = prevElems[p].value;
                                            break;
                                        }
                                    }

                                    if (prevVal && newElems.length > 0) {
                                        let newVal = prevVal;
                                        if (isSerial) {
                                            newVal = this.autoIncrementSerial(prevVal);
                                        }
                                        newElems.forEach(el => {
                                            el.value = newVal;
                                            el.dispatchEvent(new Event('change', { bubbles: true }));
                                            el.dispatchEvent(new Event('input', { bubbles: true }));
                                        });
                                    }
                                });
                            }
                        };

                        setTimeout(performCopy, 40);
                        setTimeout(performCopy, 140);
                        setTimeout(performCopy, 280);
                        setTimeout(performCopy, 500);
                    }
                },

                initAutoFillListeners() {
                    setTimeout(() => {
                        const serviceTypeInputs = document.querySelectorAll('[data-field-label="Service Type"]');
                        serviceTypeInputs.forEach(input => {
                            input.addEventListener('change', (e) => {
                                this.updateInspectionReport(e.target.value);
                            });
                        });
                    }, 100);
                },

                updateInspectionReport(serviceType) {
                    const reportField = document.querySelector('[data-field-label="Inspection Report"]');
                    if (!reportField) return;

                    const texts = {
                        'Annual': 'Annual inspection carried out in accordance with manufacturer instructions.',
                        '2-yr': 'Two-yearly service carried out in accordance with manufacturer instructions.',
                        '3-yr': 'Three-yearly service carried out in accordance with manufacturer instructions.', 
                        '5-yr': 'Five-yearly major service carried out including internal inspection and pressure testing.',
                        '10-yr': 'Ten-yearly major service carried out including internal inspection and pressure testing.',
                        '20-yr': 'Twenty-yearly major service carried out.'
                    };

                    if (texts[serviceType]) {
                        reportField.value = texts[serviceType];
                    } else {
                        for (const [key, text] of Object.entries(texts)) {
                            if (serviceType.includes(key)) {
                                reportField.value = text;
                                break;
                            }
                        }
                    }
                },

                downloadSectionTemplate(sectionId) {
                    const template = this.currentTemplate;
                    const section = template.sections.find(s => s.id === sectionId);
                    if (!section) return;

                    const fields = section.fields.map(f => f.field_label);
                    const csv = Papa.unparse([fields]);
                    
                    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = `${section.name.replace(/[^a-z0-9]/gi, '_').toLowerCase()}_template.csv`;
                    link.click();
                },

                importSectionData(sectionId, file, alpineData) {
                    if (!file) return;

                    Papa.parse(file, {
                        header: true,
                        skipEmptyLines: true,
                        complete: (results) => {
                            const data = results.data;
                            if (data.length === 0) return;

                            const currentInstances = alpineData.instanceIds.length;
                            const newInstances = data.length;
                            
                            for (let i = 0; i < newInstances; i++) {
                                alpineData.instanceIds.push(alpineData.nextId++);
                            }

                            setTimeout(() => {
                                const template = this.currentTemplate;
                                const section = template.sections.find(s => s.id === sectionId);
                                
                                data.forEach((row, index) => {
                                    const instanceNum = currentInstances + 1 + index;
                                    
                                    section.fields.forEach(field => {
                                        const label = field.field_label;
                                        const csvKey = Object.keys(row).find(k => k.trim().toLowerCase() === label.trim().toLowerCase());
                                        
                                        if (csvKey && row[csvKey]) {
                                            const inputName = `field_${field.id}_instance_${instanceNum}`;
                                            const inputs = document.querySelectorAll(`[name="${inputName}"]`);
                                            if (inputs.length > 0) {
                                                inputs.forEach(input => {
                                                    input.value = row[csvKey];
                                                    input.dispatchEvent(new Event('change', { bubbles: true }));
                                                    input.dispatchEvent(new Event('input', { bubbles: true }));
                                                });
                                            } else {
                                                const radios = document.querySelectorAll(`[name="${inputName}"][value="${row[csvKey]}"]`);
                                                if (radios.length > 0) {
                                                    radios[0].checked = true;
                                                }
                                            }
                                        }
                                    });
                                });
                                alert(`Successfully imported ${data.length} items.`);
                            }, 500);
                        },
                        error: (err) => {
                            console.error('CSV Parse Error:', err);
                            alert('Failed to parse CSV file.');
                        }
                    });
                },

                submitForm() {
                    const form = document.getElementById('certificateForm');
                    if (form.checkValidity()) {
                        form.submit();
                    } else {
                        form.reportValidity();
                    }
                }
            }
        }
    </script>
</x-app-layout>
