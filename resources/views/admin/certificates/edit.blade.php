<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Certificate') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Feedback Messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded text-green-700">
                    <p class="font-bold">Success</p>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded text-red-700">
                    <p class="font-bold">Error</p>
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded text-red-700">
                    <p class="font-bold">Please correct the following errors:</p>
                    <ul class="list-disc pl-5 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Info Alert -->
            <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h4 class="font-bold text-blue-800">Marine Equipment Certificate</h4>
                        <p class="text-sm text-blue-700">Select job, equipment category, and fill in the certificate details using the template.</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.certificates.update', $certificate) }}" 
                      id="certificateForm"
                      x-data="certificateFormData()" 
                      @submit.prevent="submitForm"
                      @download-template.window="downloadSectionTemplate($event.detail.sectionId)"
                      @import-csv.window="importSectionData($event.detail.sectionId, $event.detail.file, $event.detail.alpineData)">
                    @csrf
                    @method('PUT')

                    <!-- Step 1: Job Selection -->
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 pb-2 border-b flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">1</span>
                            Select Service Job
                        </h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Job Number *</label>
                            <select name="service_job_id" 
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500" 
                                    required 
                                    x-model="formData.job_id"
                                    @change="onJobSelect()">
                                <option value="">-- Select Job --</option>
                                @foreach($jobs as $job)
                                <option value="{{ $job->id }}" 
                                        data-customer="{{ $job->customer->company_name }}"
                                        data-vessel="{{ $job->vessel->name ?? 'N/A' }}"
                                        {{ $certificate->service_job_id == $job->id ? 'selected' : '' }}>
                                    {{ $job->job_number }} - {{ $job->customer->company_name }}
                                </option>
                                @endforeach
                            </select>
                            @if($jobs->count() === 0)
                            <p class="text-sm text-yellow-600 mt-2">⚠️ No jobs available. Create a job first.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Step 2: Equipment Category Selection -->
                    <div x-show="formData.job_id" x-transition class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 pb-2 border-b flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">2</span>
                            Select Equipment Category
                        </h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Equipment Category *</label>
                            <select name="equipment_category_id" 
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500" 
                                    required 
                                    x-model="formData.category_id" 
                                    @change="loadTemplate()">
                                <option value="">-- Select Equipment Category --</option>
                                @foreach($equipmentCategories as $category)
                                <option value="{{ $category->id }}" {{ $certificate->equipment_category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Examples: Fire Extinguisher, Life Raft, SCBA, EEBD, Life Jacket, Immersion Suit, CO₂ Fixed System
                            </p>
                        </div>
                    </div>

                    <!-- Step 3: Template-Based Form -->
                    <div x-show="templateLoaded" x-transition class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 pb-2 border-b flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">3</span>
                            <span x-text="templateName || 'Equipment Details'"></span>
                        </h3>
                        
                        <!-- Template Form Container -->
                        <div id="templateFormContainer">
                            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p>Select an equipment category to load the certificate template</p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Certificate Information -->
                    <div x-show="templateLoaded" x-transition class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 pb-2 border-b flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">4</span>
                            Certificate Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Certificate Type *</label>
                                <select name="certificate_type_id" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                    <option value="">-- Select Type --</option>
                                    @foreach($types as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Issue Date *</label>
                                <input type="date" name="issue_date" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500" required value="{{ $certificate->issue_date ? $certificate->issue_date->format('Y-m-d') : date('Y-m-d') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expiry Date *</label>
                                <input type="date" name="expiry_date" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500" required value="{{ $certificate->expiry_date ? $certificate->expiry_date->format('Y-m-d') : '' }}">
                            </div>
                            
                            <!-- Assign Verifier -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Assign Engineer for Verification</label>
                                <select name="assigned_verifier_id" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">-- No Verifier Assigned --</option>
                                    @foreach($engineers as $engineer)
                                        <option value="{{ $engineer->id }}" {{ $certificate->assigned_verifier_id == $engineer->id ? 'selected' : '' }}>{{ $engineer->name }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">If assigned, verification is required before submission.</p>
                            </div>
                        </div>



                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3 pt-6 border-t">
                        <a href="{{ route('admin.certificates.index') }}" 
                           class="px-6 py-2 bg-gray-50 dark:bg-gray-7000 hover:bg-gray-600 text-white rounded-md transition">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition"
                                x-show="templateLoaded">
                            Update Certificate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>
    <script>
        function certificateFormData() {
            return {
                formData: {
                    job_id: '{{ $certificate->service_job_id }}',
                    category_id: '{{ $certificate->equipment_category_id }}'
                },
                templateLoaded: true,
                templateName: '{{ $certificate->certificateTemplate->name ?? "" }}',
                currentTemplate: @json($certificate->certificateTemplate),
                existingFieldValues: @json($certificate->fieldValues->keyBy(function($item) {
                    return 'field_' . $item->certificate_template_field_id . '_instance_' . $item->section_instance;
                })),

                init() {
                    // For edit mode, render the template immediately
                    if (this.currentTemplate) {
                        this.renderTemplate(this.currentTemplate);
                        // Pre-populate field values after a short delay
                        setTimeout(() => this.populateExistingValues(), 200);
                    }
                },

                populateExistingValues() {
                    // Populate all existing field values
                    Object.keys(this.existingFieldValues).forEach(key => {
                        const fieldValue = this.existingFieldValues[key];
                        let input = document.querySelector(`[name="${key}"]`);
                        
                        // Fallback for non-repeatable fields (which don't have _instance_1 suffix in form)
                        if (!input && key.endsWith('_instance_1')) {
                            const simpleKey = key.replace('_instance_1', '');
                            input = document.querySelector(`[name="${simpleKey}"]`);
                        }

                        if (input) {
                            if (input.type === 'radio') {
                                const radio = document.querySelector(`[name="${input.name}"][value="${fieldValue.value}"]`);
                                if (radio) radio.checked = true;
                            } else {
                                input.value = fieldValue.value;
                                // Trigger Alpine binding updates if any
                                input.dispatchEvent(new Event('input'));
                                input.dispatchEvent(new Event('change'));
                            }
                        }
                    });
                },

                onJobSelect() {
                    console.log('Job selected:', this.formData.job_id);
                },

                async loadTemplate() {
                    if (!this.formData.category_id) {
                        this.templateLoaded = false;
                        return;
                    }

                    try {
                        const response = await fetch(`{{ url('admin/certificates/api/certificate-fields') }}/${this.formData.category_id}`);
                        const data = await response.json();
                        
                        if (data.template) {
                            this.currentTemplate = data.template;
                            this.templateName = data.template.name;
                            this.renderTemplate(data.template);
                            this.templateLoaded = true;
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
                    let html = '<div class="space-y-8">';
                    html += `<input type="hidden" name="template_id" value="${template.id}">`;
                    
                    template.sections.forEach(section => {
                        html += this.renderSection(section);
                    });
                    
                    html += '</div>';
                    html += '</div>';
                    container.innerHTML = html;
                    
                    // Initialize Alpine.js on the new content
                    if (window.Alpine) {
                        try {
                            window.Alpine.initTree(container);
                        } catch (e) {
                            console.error('Alpine init error:', e);
                        }
                    } else {
                        // Fallback check or log
                        console.warn('Alpine.js not found globaly');
                    }
                    
                    // Initialize listeners for auto-fill logic
                    this.initAutoFillListeners();
                },

                renderSection(section) {
                    let html = `<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-blue-500">`;
                    html += `<div class="mb-6">`;
                    html += `<h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">${section.name}</h3>`;
                    if (section.description) {
                        html += `<p class="text-gray-600 text-sm mt-1">${section.description}</p>`;
                    }
                    if (section.is_repeatable) {
                        html += `<span class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded mt-2">
                            <i class="fas fa-repeat mr-1"></i>Repeatable Section
                        </span>`;
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
                    let html = `<div x-data="{ instanceIds: [1], nextId: 2 }">`;
                    
                    // Import/Export Controls
                    html += `<div class="flex flex-wrap gap-2 mb-4 bg-gray-50 dark:bg-gray-700 p-2 rounded border border-gray-200">`;
                    html += `<button type="button" @click="$dispatch('download-template', { sectionId: '${sectionId}' })" class="text-xs bg-gray-600 hover:bg-gray-700 text-white px-3 py-1.5 rounded transition">
                        <i class="fas fa-download mr-1"></i>Download Template
                    </button>`;
                    html += `<label class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded transition cursor-pointer">
                        <i class="fas fa-file-import mr-1"></i>Import CSV
                        <input type="file" accept=".csv" class="hidden" @change="$dispatch('import-csv', { sectionId: '${sectionId}', file: $event.target.files[0], alpineData: $data })">
                    </label>`;
                    html += `</div>`;
                    
                    html += `<template x-for="instanceId in instanceIds" :key="instanceId">`;
                    html += `<div class="mb-6 pb-6 border-b border-gray-200 last:border-0 relative">`;
                    html += `<div class="flex justify-between items-center mb-4 bg-gray-50 dark:bg-gray-700 p-2 rounded">`;
                    html += `<h4 class="font-semibold text-gray-700 dark:text-gray-300">Entry <span x-text="instanceIds.indexOf(instanceId) + 1"></span></h4>`;
                    html += `<button type="button" x-show="instanceIds.length > 1" @click="instanceIds = instanceIds.filter(id => id !== instanceId)" class="text-red-600 hover:text-red-800 text-sm">
                        <i class="fas fa-trash mr-1"></i>Remove
                    </button>`;
                    html += `</div>`;
                    
                    html += `<div class="grid grid-cols-1 ${section.layout_type === 'two_column' ? 'md:grid-cols-2' : ''} gap-4">`;
                    section.fields.forEach(field => {
                        html += this.renderField(field, true);
                    });
                    html += `</div></div></template>`;
                    
                    html += `<input type="hidden" :name="'section_${sectionId}_instances'" :value="instanceIds.length">`;
                    html += `<button type="button" @click="instanceIds.push(nextId++)" class="bg-green-100 hover:bg-green-200 text-green-800 px-4 py-2 rounded font-semibold text-sm transition">
                        <i class="fas fa-plus mr-2"></i>Add Another Entry
                    </button>`;
                    html += `</div>`;
                    return html;
                },

                renderRegularSection(section) {
                    let html = `<div class="grid grid-cols-1 ${section.layout_type === 'two_column' ? 'md:grid-cols-2' : ''} gap-6">`;
                    section.fields.forEach(field => {
                        html += this.renderField(field, false);
                    });
                    html += `</div>`;
                    return html;
                },

                renderField(field, isRepeatable) {
                    const colSpan = field.width === 'full' ? 'md:col-span-full' : '';
                    const fieldName = isRepeatable 
                        ? `:name="'field_${field.id}_instance_' + (instanceIds.indexOf(instanceId) + 1)"` 
                        : `name="field_${field.id}"`;
                    
                    let html = `<div class="${colSpan}">`;
                    html += `<label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">`;
                    html += field.field_label;
                    if (field.is_required) html += `<span class="text-red-600">*</span>`;
                    html += `</label>`;
                    
                    html += this.renderFieldInput(field, fieldName, isRepeatable);
                    
                    if (field.help_text) {
                        html += `<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">${field.help_text}</p>`;
                    }
                    html += `</div>`;
                    return html;
                },

                renderFieldInput(field, fieldName, isRepeatable) {
                    const required = field.is_required ? 'required' : '';
                    const baseClass = 'w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500';
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
                            let radioHtml = '<div class="flex flex-wrap gap-4 mt-2">';
                            if (field.field_options) {
                                try {
                                    let opts = field.field_options;
                                    if (typeof opts === 'string') opts = JSON.parse(opts);
                                    if (opts && typeof opts === 'object' && !Array.isArray(opts) && opts.options) opts = opts.options;
                                    
                                    if (Array.isArray(opts)) {
                                        opts.forEach(opt => {
                                            // Handle name binding slightly differently to ensure group works (Blade handles arrays/names usually fine)
                                            // For repeatable sections, name is unique per row thanks to existing logic.
                                            radioHtml += `
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input type="radio" value="${opt}" ${fieldName.replace(':', '')} 
                                                           ${fieldName.includes(':name') ? `:name="${fieldName.match(/:name="'(.*)'"/)[1]}"` : ''}
                                                           class="form-radio text-blue-600 border-gray-300 focus:ring-blue-500" 
                                                           ${required} ${labelAttr}>
                                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">${opt}</span>
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
                                    // If it's a string, try to parse it as JSON
                                    if (typeof opts === 'string') {
                                        opts = JSON.parse(opts);
                                    }

                                    // Handle wrapped options object (e.g. { "options": [...] }) as stored by the seeder
                                    if (opts && typeof opts === 'object' && !Array.isArray(opts) && opts.options) {
                                        opts = opts.options;
                                    }
                                    // Ensure it's an array
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

                initAutoFillListeners() {
                    // Use setTimeout to ensure DOM is updated
                    setTimeout(() => {
                        // Find "Service Type" field
                        const serviceTypeInputs = document.querySelectorAll('[data-field-label="Service Type"]');
                        
                        serviceTypeInputs.forEach(input => {
                            input.addEventListener('change', (e) => {
                                this.updateInspectionReport(e.target.value);
                            });
                        });
                    }, 100);
                },

                updateInspectionReport(serviceType) {
                    // Find "Inspection Report" field
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

                    // Match partial key if needed, or exact
                    // User list: Annual, 2-yr, 3-yr...
                    
                    // Simple Exact Match first
                    if (texts[serviceType]) {
                        reportField.value = texts[serviceType];
                    }
                    // Or check if selected value *contains* key
                    else {
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
                    const csv = Papa.unparse([fields]); // Header row only
                    
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

                            // Update instances count
                            const currentInstances = alpineData.instances;
                            const newInstances = data.length;
                            
                            // Adjust instances to match CSV rows (or append? Replacement is safer/easier for now, or just append)
                            // Let's Append:
                            alpineData.instances += newInstances;

                            // Wait for DOM to update
                            setTimeout(() => {
                                const template = this.currentTemplate;
                                const section = template.sections.find(s => s.id === sectionId);
                                
                                data.forEach((row, index) => {
                                    // Calculate target instance number (1-based)
                                    // If we appended, it starts from currentInstances + 1 + index
                                    // Use 'currentInstances + 1 + index' if appending
                                    // BUT: 'alpineData.instances' handles display. logic starts at 1.
                                    // existing instances: 1 (default). 
                                    // If row 1 is imported, do we overwrite instance 1? NO, append.
                                    
                                    const instanceNum = currentInstances + 1 + index;
                                    
                                    section.fields.forEach(field => {
                                        const label = field.field_label;
                                        // Find generic match in CSV header
                                        const csvKey = Object.keys(row).find(k => k.trim().toLowerCase() === label.trim().toLowerCase());
                                        
                                        if (csvKey && row[csvKey]) {
                                            const inputName = `field_${field.id}_instance_${instanceNum}`;
                                            // Handle arrays (radio/checkbox)?
                                            // For now assume text/number/select
                                            
                                            // Find inputs by NAME
                                            // Since render field logic uses specific names, we can target them
                                            const inputs = document.querySelectorAll(`[name="${inputName}"]`);
                                            if (inputs.length > 0) {
                                                const input = inputs[0];
                                                input.value = row[csvKey];
                                                // Trigger change event just in case
                                                input.dispatchEvent(new Event('change'));
                                                input.dispatchEvent(new Event('input'));
                                            } else {
                                                // Try finding radio buttons with value
                                                const radios = document.querySelectorAll(`[name="${inputName}"][value="${row[csvKey]}"]`);
                                                if (radios.length > 0) {
                                                    radios[0].checked = true;
                                                }
                                            }
                                        }
                                    });
                                });
                                alert(`Successfully imported ${data.length} items.`);
                            }, 500); // Allow Alpine to render
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
