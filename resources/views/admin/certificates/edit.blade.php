<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight flex items-center gap-2">
                    <span>Edit Marine Certificate</span>
                    <span class="font-mono text-xs px-2 py-0.5 bg-brand-100 dark:bg-brand-950/60 text-brand-600 dark:text-brand-400 border border-brand-200 dark:border-brand-800/60 rounded-md">{{ $certificate->certificate_number }}</span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Modify inspection values, equipment specs, and certificate parameters</p>
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
            <form method="POST" action="{{ route('admin.certificates.update', $certificate) }}" id="certificateForm" @submit.prevent="submitForm">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    
                    <!-- LEFT COLUMN (70%): Certificate Form Steps -->
                    <div class="lg:col-span-8 space-y-6">
                        
                        <!-- Step 1: Select Service Job Card -->
                        <div class="theme-card">
                            <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-200 dark:border-slate-700/80">
                                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-brand-600 text-white text-xs font-black flex items-center justify-center">1</span>
                                    <span>Job Details</span>
                                </h3>
                                <span class="badge-neutral text-[10px]">Job Binding</span>
                            </div>

                            @include('admin.certificates.partials.job-details-fields')
                        </div>

                        <!-- Step 2: Select Equipment Category Card -->
                        <div class="theme-card">
                            <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-200 dark:border-slate-700/80">
                                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-indigo-600 text-white text-xs font-black flex items-center justify-center">2</span>
                                    <span>Select Equipment Category</span>
                                </h3>
                                <span class="badge-neutral text-[10px]">Template Engine</span>
                            </div>

                            <div>
                                <label class="theme-label mb-1.5">Equipment Category *</label>
                                <select name="equipment_category_id" 
                                        class="theme-input text-xs py-2.5 font-bold" 
                                        required 
                                        x-model="formData.category_id"
                                        @change="onCategorySelect()">
                                    <option value="">-- Select Category --</option>
                                    @include('admin.certificates.partials.category-options', ['categories' => $categories, 'selected' => $certificate->equipment_category_id])
                                </select>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Examples: Fire Extinguisher, Life Raft, SCBA, EEBD, Life Jacket, Immersion Suit, CO₂ Fixed System</p>
                            </div>
                        </div>

                        <!-- Step 3: Dynamic Certificate Fields Container -->
                        <div x-show="formData.category_id" x-transition class="space-y-6">
                            <div class="theme-card">
                                <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-200 dark:border-slate-700/80">
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-full bg-emerald-600 text-white text-xs font-black flex items-center justify-center">3</span>
                                        <span x-text="categoryName ? categoryName + ' Certificate Details' : 'Certificate Field Inspection'"></span>
                                    </h3>
                                    <span class="badge-emerald text-[10px]">Inspection Template</span>
                                </div>

                                <!-- Dynamic Field Sections Container -->
                                <div id="templateFormContainer" class="space-y-6">
                                    <!-- Rendered dynamically by Alpine JavaScript -->
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Certificate Type & Additional Parameters Card -->
                        <div class="theme-card space-y-5">
                            <div class="flex items-center justify-between pb-4 mb-3 border-b border-slate-200 dark:border-slate-700/80">
                                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-amber-600 text-white text-xs font-black flex items-center justify-center">4</span>
                                    <span>Certificate Parameters & Classification</span>
                                </h3>
                                <span class="badge-neutral text-[10px]">Classification</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <!-- Certificate Type Select -->
                                <div>
                                    <label class="theme-label mb-1.5">Certificate Type *</label>
                                    <select name="certificate_type_id" 
                                            class="theme-input text-xs py-2.5 font-bold" 
                                            required 
                                            x-model="formData.certificate_type_id"
                                            @change="onTypeSelect()">
                                        <option value="">-- Select Certificate Type --</option>
                                        @foreach($certificateTypes as $type)
                                        <option value="{{ $type->id }}" 
                                                data-name="{{ $type->name }}"
                                                {{ $certificate->certificate_type_id == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Certificate Status -->
                                <div>
                                    <label class="theme-label mb-1.5">Certificate Status *</label>
                                    <select name="status" class="theme-input text-xs py-2.5 font-bold" required x-model="formData.status">
                                        <option value="ISSUED" {{ $certificate->status == 'ISSUED' ? 'selected' : '' }}>ISSUED</option>
                                        <option value="DRAFT" {{ $certificate->status == 'DRAFT' ? 'selected' : '' }}>DRAFT</option>
                                        <option value="PENDING" {{ $certificate->status == 'PENDING' ? 'selected' : '' }}>PENDING</option>
                                        <option value="CANCELLED" {{ $certificate->status == 'CANCELLED' ? 'selected' : '' }}>CANCELLED</option>
                                    </select>
                                </div>

                                <!-- Issue Date -->
                                <div>
                                    <label class="theme-label mb-1.5">Issue Date *</label>
                                    <input type="date" name="issue_date" value="{{ optional($certificate->issue_date)->format('Y-m-d') ?? date('Y-m-d') }}" required
                                        class="theme-input text-xs py-2.5 font-bold">
                                </div>

                                <!-- Expiry Date -->
                                <div>
                                    <label class="theme-label mb-1.5">Expiry Date</label>
                                    <input type="date" name="expiry_date" value="{{ optional($certificate->expiry_date)->format('Y-m-d') }}"
                                        class="theme-input text-xs py-2.5">
                                </div>

                                <!-- Work Order / Ref No -->
                                <div class="md:col-span-2">
                                    <label class="theme-label mb-1.5">Job / Reference Number</label>
                                    <input type="text" name="job_number" value="{{ $certificate->job_number }}"
                                        class="theme-input text-xs py-2.5" 
                                        placeholder="Optional job number or customer PO ref">
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- RIGHT COLUMN (30% Sticky Panel): Actions & Live Summary -->
                    <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">
                        
                        <!-- Primary Action Panel Card -->
                        <div class="theme-card space-y-4">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white pb-3 border-b border-slate-200 dark:border-slate-700/80 flex items-center gap-2">
                                <svg class="w-4 h-4 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Update Certificate</span>
                            </h3>

                            <button type="submit" 
                                    class="btn-primary w-full text-center text-xs py-3 font-bold gap-2 shadow-md flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Update & Save Certificate</span>
                            </button>

                            <a href="{{ route('admin.certificates.index') }}" class="btn-secondary w-full text-center text-xs py-2.5 font-bold gap-1.5 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                <span>Cancel & Return</span>
                            </a>
                        </div>

                        <!-- Live Certificate Summary Card -->
                        <div class="theme-card space-y-3 bg-slate-50/50 dark:bg-slate-900/40">
                            <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider pb-2 border-b border-slate-200 dark:border-slate-700/80">
                                Certificate Summary
                            </h4>

                            <div class="flex items-center justify-between text-xs py-1">
                                <span class="text-slate-500 dark:text-slate-400">Certificate No</span>
                                <span class="font-mono font-bold text-brand-600 dark:text-brand-400">{{ $certificate->certificate_number }}</span>
                            </div>

                            <div class="flex items-center justify-between text-xs py-1">
                                <span class="text-slate-500 dark:text-slate-400">Customer</span>
                                <span class="font-bold text-slate-900 dark:text-white truncate max-w-[140px]" x-text="formData.customer_name || 'Not Selected'"></span>
                            </div>

                            <div class="flex items-center justify-between text-xs py-1">
                                <span class="text-slate-500 dark:text-slate-400">Vessel</span>
                                <span class="font-bold text-slate-900 dark:text-white truncate max-w-[140px]" x-text="formData.vessel_name || 'Not Selected'"></span>
                            </div>

                            <div class="flex items-center justify-between text-xs py-1">
                                <span class="text-slate-500 dark:text-slate-400">Category</span>
                                <span class="font-bold text-slate-900 dark:text-white truncate max-w-[140px]" x-text="categoryName || 'Not Selected'"></span>
                            </div>

                            <div class="flex items-center justify-between text-xs py-1">
                                <span class="text-slate-500 dark:text-slate-400">Type</span>
                                <span class="font-bold text-slate-900 dark:text-white truncate max-w-[140px]" x-text="selectedTypeName || 'Not Selected'"></span>
                            </div>

                            <div class="flex items-center justify-between text-xs py-1">
                                <span class="text-slate-500 dark:text-slate-400">Status</span>
                                <span class="badge-info text-[10px] font-bold" x-text="formData.status"></span>
                            </div>
                        </div>

                        <!-- Verification & Guidance Banner -->
                        <div class="p-4 rounded-xl bg-sky-50/70 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800/60 text-xs space-y-2 text-sky-800 dark:text-sky-300">
                            <h4 class="font-bold flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Certificate Guidance</span>
                            </h4>
                            <ul class="space-y-1 text-[11px] text-slate-600 dark:text-slate-400 list-disc list-inside">
                                <li>Use <strong>📊 Excel Grid Mode</strong> for batch equipment entries.</li>
                                <li>Pressing <strong>Tab</strong> on the last cell appends a row & auto-increments serial numbers.</li>
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
             class="fixed inset-0 bg-slate-900/80 backdrop-blur-md z-50 flex flex-col p-4 md:p-6 overflow-hidden"
             style="display: none;">
            
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl flex-1 flex flex-col overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-950/60 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-500 text-white font-bold flex items-center justify-center shadow-md">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M9 4v16M15 4v16"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <span>Excel Grid Editor</span>
                                <span class="badge-emerald text-[10px]" x-text="activeExcelSectionName"></span>
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Use <kbd class="px-1.5 py-0.5 bg-slate-200 dark:bg-slate-800 rounded text-[10px] font-mono font-bold">TAB</kbd> key to navigate between cells. Tab on last cell inserts a new row with auto-fill & auto-incrementing serial numbers.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button" @click="addExcelRow()" class="btn-secondary text-xs font-bold gap-1.5">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>+ Add Row</span>
                        </button>
                        <button type="button" @click="saveAndCloseExcelGrid()" class="btn-primary text-xs font-bold gap-1 px-5 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Done & Save Grid</span>
                        </button>
                    </div>
                </div>

                <div class="flex-1 overflow-auto p-6" id="excel-grid-table-container">
                    <table class="w-full border-collapse border border-slate-200 dark:border-slate-800 text-left text-xs" id="excel-grid-table">
                        <!-- Dynamic Excel Table Header & Body -->
                    </table>
                </div>

                <div class="px-6 py-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-950/60 text-xs font-semibold text-slate-500 shrink-0">
                    <div>
                        <span>Total Rows: </span>
                        <strong class="text-slate-900 dark:text-white" id="excel-grid-row-count">1</strong>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- PAPAPARSE LIBRARY FOR CSV IMPORT/EXPORT -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>

    <script>
        function certificateFormData() {
            return {
                formData: {
                    job_number: @json(old('job_number', $certificate->job_number ?? $certificate->job?->job_number ?? '')),
                    customer_id: @json(old('customer_id', $certificate->customer_id ?? '')),
                    vessel_id: @json(old('vessel_id', $certificate->vessel_id ?? '')),
                    category_id: '{{ $certificate->equipment_category_id }}',
                    certificate_type_id: '{{ $certificate->certificate_type_id }}',
                    customer_name: '{{ addslashes($certificate->customer->company_name ?? "") }}',
                    vessel_name: '{{ addslashes($certificate->vessel->name ?? "") }}',
                    status: '{{ $certificate->status }}'
                },
                customers: @json($customers),
                vessels: @json($vessels),
                templates: @json($templates),
                types: @json($certificateTypes),
                existingFieldValues: @json($certificate->fieldValues->groupBy(function($fv) { return $fv->field_id ?: $fv->certificate_template_field_id; })),
                categoryName: '',
                selectedTypeName: '',
                currentTemplate: null,
                excelModalOpen: false,
                activeExcelSectionId: null,
                activeExcelSectionName: '',
                activeExcelAlpineData: null,

                init() {
                    // Keep the summary panel's customer / vessel names in step with the selects
                    this.$watch('formData.customer_id', id => {
                        this.formData.customer_name = this.customers.find(c => c.id === id)?.company_name || '';
                    });
                    this.$watch('formData.vessel_id', id => {
                        this.formData.vessel_name = this.vessels.find(v => v.id === id)?.name || '';
                    });
                    this.$nextTick(() => {
                        if (this.formData.category_id) {
                            this.onCategorySelect();
                        }
                        if (this.formData.certificate_type_id) {
                            this.onTypeSelect();
                        }
                    });
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

                onCategorySelect() {
                    const categoryId = this.formData.category_id;
                    if (!categoryId) {
                        this.currentTemplate = null;
                        this.categoryName = '';
                        document.getElementById('templateFormContainer').innerHTML = '';
                        return;
                    }

                    const certTemplateId = '{{ $certificate->certificate_template_id }}';
                    this.currentTemplate = this.templates.find(t => t.id == certTemplateId) 
                        || this.templates.find(t => t.equipment_category_id == categoryId);

                    const select = document.querySelector('[name="equipment_category_id"]');
                    if (select && select.selectedOptions.length > 0) {
                        this.categoryName = select.selectedOptions[0].text.trim();
                    }

                    if (this.currentTemplate) {
                        this.renderTemplateForm();
                    } else {
                        document.getElementById('templateFormContainer').innerHTML = `
                            <div class="p-8 text-center bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-dashed border-slate-300 dark:border-slate-700">
                                <svg class="w-12 h-12 mx-auto text-slate-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-300">No template found for this equipment category</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Create a template first under Certificate Templates master.</p>
                            </div>
                        `;
                    }
                },

                onTypeSelect() {
                    const typeId = this.formData.certificate_type_id;
                    if (!typeId) {
                        this.selectedTypeName = '';
                        return;
                    }
                    const typeObj = this.types.find(t => t.id == typeId);
                    if (typeObj) {
                        this.selectedTypeName = typeObj.name;
                    }
                },

                renderTemplateForm() {
                    const container = document.getElementById('templateFormContainer');
                    container.innerHTML = '';

                    this.currentTemplate.sections.forEach((section, sIndex) => {
                        const sectionEl = document.createElement('div');
                        sectionEl.className = 'p-5 rounded-xl bg-slate-50/50 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-700/80 space-y-4';

                        const repeatableMax = section.repeatable ? section.max_rows || 50 : 1;

                        sectionEl.setAttribute('x-data', JSON.stringify({
                            instances: repeatableMax > 1 ? this.getExistingMaxInstance(section) : 1,
                            sectionId: section.id,
                            maxRows: repeatableMax
                        }));

                        let sectionHTML = `
                            <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-700/80 flex-wrap gap-2">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">${section.name}</h4>
                                    ${section.repeatable ? `<span class="badge-neutral text-[10px]">Repeatable Grid</span>` : ''}
                                </div>

                                <div class="flex items-center gap-2">
                                    ${section.repeatable ? `
                                    <button type="button" @click="$dispatch('open-excel', { sectionId: '${section.id}', alpineData: $data })" class="btn-primary btn-sm text-xs gap-1 font-bold">
                                        📊 Excel Grid Mode
                                    </button>
                                    ` : ''}
                                    <button type="button" @click="$dispatch('download-template', { sectionId: '${section.id}' })" class="btn-ghost btn-sm text-xs gap-1">
                                        Download CSV
                                    </button>
                                    <label class="btn-ghost btn-sm text-xs gap-1 cursor-pointer">
                                        Import CSV
                                        <input type="file" accept=".csv" class="hidden" @change="$dispatch('import-csv', { sectionId: '${section.id}', file: $event.target.files[0], alpineData: $data })">
                                    </label>
                                </div>
                            </div>
                        `;

                        if (section.repeatable) {
                            sectionHTML += `<div id="section_${section.id}_instances" class="space-y-4">`;
                            sectionHTML += `</div>`;

                            sectionHTML += `
                                <div class="flex items-center justify-between pt-2">
                                    <button type="button" @click="$dispatch('add-entry', { sectionId: '${section.id}', alpineData: $data })" 
                                            x-show="instances < maxRows"
                                            class="btn-secondary btn-sm text-xs gap-1.5 font-bold">
                                        + Add Item
                                    </button>
                                    <span class="text-[11px] text-slate-500 font-semibold" x-text="'Entries: ' + instances + ' / ' + maxRows"></span>
                                </div>
                            `;
                        } else {
                            sectionHTML += `<div class="grid grid-cols-1 md:grid-cols-2 gap-4">`;
                            section.fields.forEach(field => {
                                sectionHTML += this.renderFieldHTML(field, 1);
                            });
                            sectionHTML += `</div>`;
                        }

                        sectionEl.innerHTML = sectionHTML;
                        container.appendChild(sectionEl);

                        if (section.repeatable) {
                            const instContainer = sectionEl.querySelector(`#section_${section.id}_instances`);
                            const currentInstances = this.getExistingMaxInstance(section);
                            for (let i = 1; i <= currentInstances; i++) {
                                instContainer.appendChild(this.renderInstanceBlock(section, i));
                            }
                        }
                    });

                    // Trigger service type inspection report listener
                    const serviceTypeSelect = document.querySelector('[name*="Service Type"]');
                    if (serviceTypeSelect) {
                        serviceTypeSelect.addEventListener('change', (e) => {
                            this.updateInspectionReport(e.target.value);
                        });
                    }
                },

                getExistingMaxInstance(section) {
                    let maxInst = 1;
                    if (!section || !section.fields) return maxInst;
                    section.fields.forEach(field => {
                        const list = this.existingFieldValues ? this.existingFieldValues[field.id] : null;
                        if (list && Array.isArray(list)) {
                            list.forEach(fv => {
                                const inst = parseInt(fv.section_instance || fv.instance_index || 1, 10);
                                if (inst > maxInst) {
                                    maxInst = inst;
                                }
                            });
                        }
                    });
                    return maxInst;
                },

                getExistingValue(fieldId, instanceIndex) {
                    if (this.existingFieldValues && this.existingFieldValues[fieldId]) {
                        const list = this.existingFieldValues[fieldId];
                        const match = list.find(fv => 
                            (fv.section_instance == instanceIndex) || 
                            (fv.instance_index == instanceIndex) || 
                            (!fv.section_instance && !fv.instance_index && instanceIndex == 1)
                        );
                        if (match) {
                            return (match.field_value !== null && match.field_value !== undefined && match.field_value !== '') 
                                ? match.field_value 
                                : (match.value !== null && match.value !== undefined ? match.value : '');
                        }
                    }
                    return '';
                },

                renderInstanceBlock(section, instanceNum) {
                    const block = document.createElement('div');
                    block.className = 'p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 space-y-3 relative group';
                    
                    let html = `
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Item #${instanceNum}</span>
                            ${instanceNum > 1 ? `
                            <button type="button" onclick="this.closest('.group').remove()" class="text-rose-500 hover:text-rose-700 text-xs font-bold">Remove</button>
                            ` : ''}
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    `;

                    section.fields.forEach(field => {
                        html += this.renderFieldHTML(field, instanceNum);
                    });

                    html += `</div>`;
                    block.innerHTML = html;
                    return block;
                },

                renderFieldHTML(field, instanceNum) {
                    const inputName = `field_${field.id}_instance_${instanceNum}`;
                    const requiredAttr = field.required ? 'required' : '';
                    const existingVal = this.getExistingValue(field.id, instanceNum);
                    const placeholder = field.placeholder ? `placeholder="${field.placeholder}"` : '';

                    let html = `<div class="space-y-1.5" data-field-label="${field.field_label}">`;
                    html += `<label class="theme-label mb-1">${field.field_label} ${field.required ? '<span class="text-rose-500">*</span>' : ''}</label>`;

                    if (field.field_type === 'select') {
                        html += `<select name="${inputName}" class="theme-input text-xs py-2 font-medium" ${requiredAttr}>`;
                        html += `<option value="">-- Select --</option>`;
                        if (field.options && Array.isArray(field.options)) {
                            field.options.forEach(opt => {
                                const sel = existingVal == opt ? 'selected' : '';
                                html += `<option value="${opt}" ${sel}>${opt}</option>`;
                            });
                        }
                        html += `</select>`;
                    } else if (field.field_type === 'textarea') {
                        html += `<textarea name="${inputName}" rows="2" class="theme-input text-xs py-2" ${placeholder} ${requiredAttr}>${existingVal}</textarea>`;
                    } else if (field.field_type === 'radio') {
                        html += `<div class="flex items-center gap-4 py-1.5">`;
                        if (field.options && Array.isArray(field.options)) {
                            field.options.forEach((opt, idx) => {
                                const chk = existingVal == opt ? 'checked' : '';
                                html += `
                                    <label class="inline-flex items-center text-xs font-medium text-slate-700 dark:text-slate-300">
                                        <input type="radio" name="${inputName}" value="${opt}" ${chk} class="text-brand-600 focus:ring-brand-500 mr-1.5" ${requiredAttr}>
                                        <span>${opt}</span>
                                    </label>
                                `;
                            });
                        }
                        html += `</div>`;
                    } else if (field.field_type === 'checkbox') {
                        html += `<div class="flex items-center gap-4 py-1.5">`;
                        if (field.options && Array.isArray(field.options)) {
                            field.options.forEach((opt, idx) => {
                                const chk = existingVal == opt ? 'checked' : '';
                                html += `
                                    <label class="inline-flex items-center text-xs font-medium text-slate-700 dark:text-slate-300">
                                        <input type="checkbox" name="${inputName}[]" value="${opt}" ${chk} class="rounded text-brand-600 focus:ring-brand-500 mr-1.5">
                                        <span>${opt}</span>
                                    </label>
                                `;
                            });
                        }
                        html += `</div>`;
                    } else if (field.field_type === 'date') {
                        html += `<input type="date" name="${inputName}" value="${existingVal}" class="theme-input text-xs py-2" ${requiredAttr}>`;
                    } else if (field.field_type === 'number') {
                        html += `<input type="number" step="any" name="${inputName}" value="${existingVal}" class="theme-input text-xs py-2 font-mono" ${placeholder} ${requiredAttr}>`;
                    } else {
                        html += `<input type="text" name="${inputName}" value="${existingVal}" class="theme-input text-xs py-2 font-medium" ${placeholder} ${requiredAttr}>`;
                    }

                    html += `</div>`;
                    return html;
                },

                addInstance(sectionId, alpineData) {
                    if (alpineData.instances >= alpineData.maxRows) return;
                    
                    const section = this.currentTemplate.sections.find(s => s.id === sectionId);
                    if (!section) return;

                    const newInstanceNum = alpineData.instances + 1;
                    const instContainer = document.getElementById(`section_${sectionId}_instances`);
                    
                    if (instContainer) {
                        const newBlock = this.renderInstanceBlock(section, newInstanceNum);
                        instContainer.appendChild(newBlock);
                        alpineData.instances++;

                        // PRE-FILL & AUTO-INCREMENT FROM PREVIOUS INSTANCE!
                        if (newInstanceNum > 1) {
                            const prevInstanceNum = newInstanceNum - 1;
                            section.fields.forEach(field => {
                                const prevInputName = `field_${field.id}_instance_${prevInstanceNum}`;
                                const newInputName = `field_${field.id}_instance_${newInstanceNum}`;

                                const prevElems = document.querySelectorAll(`[name="${prevInputName}"]`);
                                const newElems = document.querySelectorAll(`[name="${newInputName}"]`);

                                if (prevElems.length > 0 && newElems.length > 0) {
                                    let val = prevElems[0].value;
                                    const label = field.field_label.toLowerCase();
                                    
                                    if ((label.includes('serial') || label.includes('code') || label.includes('ref')) && val) {
                                        val = this.autoIncrementSerial(val);
                                    }

                                    newElems.forEach(el => {
                                        el.value = val;
                                        el.dispatchEvent(new Event('input'));
                                        el.dispatchEvent(new Event('change'));
                                    });
                                }
                            });
                        }
                    }
                },

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

                // EXCEL GRID SPREADSHEET ENGINE
                openExcelGrid(sectionId, alpineData) {
                    const section = this.currentTemplate.sections.find(s => s.id === sectionId);
                    if (!section) return;

                    this.activeExcelSectionId = sectionId;
                    this.activeExcelSectionName = section.name;
                    this.activeExcelAlpineData = alpineData;

                    this.buildExcelTable(section, alpineData.instances);
                    this.excelModalOpen = true;
                },

                buildExcelTable(section, instanceCount) {
                    const table = document.getElementById('excel-grid-table');
                    table.innerHTML = '';

                    let theadHTML = `
                        <thead class="bg-slate-100 dark:bg-slate-800 sticky top-0 z-10">
                            <tr>
                                <th class="p-3 border border-slate-200 dark:border-slate-700/80 font-bold text-slate-700 dark:text-slate-300 w-12 text-center bg-slate-200/70 dark:bg-slate-800">#</th>
                    `;

                    section.fields.forEach(field => {
                        theadHTML += `<th class="p-3 border border-slate-200 dark:border-slate-700/80 font-bold text-slate-800 dark:text-slate-200 min-w-[160px]">${field.field_label} ${field.required ? '<span class="text-rose-500">*</span>' : ''}</th>`;
                    });

                    theadHTML += `<th class="p-3 border border-slate-200 dark:border-slate-700/80 font-bold text-slate-700 dark:text-slate-300 w-16 text-center">Action</th></tr></thead>`;
                    table.innerHTML = theadHTML;

                    const tbody = document.createElement('tbody');
                    tbody.className = 'divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900';

                    for (let i = 1; i <= instanceCount; i++) {
                        tbody.appendChild(this.buildExcelRow(section, i));
                    }

                    table.appendChild(tbody);
                    document.getElementById('excel-grid-row-count').innerText = instanceCount;
                },

                buildExcelRow(section, instanceNum) {
                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-slate-50 dark:hover:bg-slate-800/50 transition';

                    let html = `<td class="p-2 border border-slate-200 dark:border-slate-800 text-center font-bold text-slate-500 dark:text-slate-400 bg-slate-50/50 dark:bg-slate-950/40">${instanceNum}</td>`;

                    section.fields.forEach((field, fIdx) => {
                        const inputName = `field_${field.id}_instance_${instanceNum}`;
                        const isLastField = fIdx === section.fields.length - 1;

                        const inputs = document.querySelectorAll(`[name="${inputName}"]`);
                        let currentVal = inputs.length > 0 ? inputs[0].value : this.getExistingValue(field.id, instanceNum);

                        html += `<td class="p-1.5 border border-slate-200 dark:border-slate-800">`;

                        if (field.field_type === 'select') {
                            html += `<select data-excel-name="${inputName}" class="theme-input text-xs py-1.5 px-2 w-full bg-transparent focus:bg-white dark:focus:bg-slate-800 border-transparent focus:border-brand-500 font-medium" ${isLastField ? `onkeydown="if(event.key==='Tab' && !event.shiftKey){ event.preventDefault(); window.dispatchEvent(new CustomEvent('excel-tab-last', {detail:{instanceNum:${instanceNum}}})); }"` : ''}>`;
                            html += `<option value="">-- Select --</option>`;
                            if (field.options && Array.isArray(field.options)) {
                                field.options.forEach(opt => {
                                    const sel = currentVal == opt ? 'selected' : '';
                                    html += `<option value="${opt}" ${sel}>${opt}</option>`;
                                });
                            }
                            html += `</select>`;
                        } else {
                            html += `<input type="text" data-excel-name="${inputName}" value="${currentVal}" class="theme-input text-xs py-1.5 px-2.5 w-full bg-transparent focus:bg-white dark:focus:bg-slate-800 border-transparent focus:border-brand-500 font-medium" ${isLastField ? `onkeydown="if(event.key==='Tab' && !event.shiftKey){ event.preventDefault(); window.dispatchEvent(new CustomEvent('excel-tab-last', {detail:{instanceNum:${instanceNum}}})); }"` : ''} placeholder="${field.field_label}">`;
                        }

                        html += `</td>`;
                    });

                    html += `
                        <td class="p-1.5 border border-slate-200 dark:border-slate-800 text-center">
                            ${instanceNum > 1 ? `
                            <button type="button" onclick="this.closest('tr').remove()" class="btn-ghost btn-sm p-1 text-rose-500 hover:text-rose-700" title="Delete Row">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                            ` : ''}
                        </td>
                    `;

                    tr.innerHTML = html;
                    return tr;
                },

                addExcelRow() {
                    if (!this.activeExcelSectionId || !this.activeExcelAlpineData) return;
                    
                    this.addInstance(this.activeExcelSectionId, this.activeExcelAlpineData);

                    const section = this.currentTemplate.sections.find(s => s.id === this.activeExcelSectionId);
                    const newInstNum = this.activeExcelAlpineData.instances;
                    const tbody = document.querySelector('#excel-grid-table tbody');

                    if (tbody && section) {
                        const newRow = this.buildExcelRow(section, newInstNum);
                        tbody.appendChild(newRow);
                        document.getElementById('excel-grid-row-count').innerText = newInstNum;

                        // Focus cell #1 of new row
                        setTimeout(() => {
                            const firstInput = newRow.querySelector('input, select');
                            if (firstInput) {
                                firstInput.focus();
                                if (firstInput.select) firstInput.select();
                            }
                        }, 100);
                    }
                },

                saveAndCloseExcelGrid() {
                    const excelInputs = document.querySelectorAll('#excel-grid-table [data-excel-name]');
                    excelInputs.forEach(el => {
                        const name = el.getAttribute('data-excel-name');
                        const targetInputs = document.querySelectorAll(`#templateFormContainer [name="${name}"]`);
                        targetInputs.forEach(tInput => {
                            tInput.value = el.value;
                            tInput.dispatchEvent(new Event('input'));
                            tInput.dispatchEvent(new Event('change'));
                        });
                    });

                    this.excelModalOpen = false;
                },

                updateInspectionReport(serviceType) {
                    const reportField = document.querySelector('[data-field-label="Inspection Report"] input, [data-field-label="Inspection Report"] textarea');
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

                            const currentInstances = alpineData.instances;
                            const newInstances = data.length;
                            alpineData.instances += newInstances;

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
                                                inputs[0].value = row[csvKey];
                                                inputs[0].dispatchEvent(new Event('change'));
                                                inputs[0].dispatchEvent(new Event('input'));
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

        window.addEventListener('excel-tab-last', (e) => {
            const formDataComp = Alpine.$data(document.querySelector('[x-data="certificateFormData()"]'));
            if (formDataComp) {
                formDataComp.addExcelRow();
            }
        });
    </script>
</x-app-layout>
