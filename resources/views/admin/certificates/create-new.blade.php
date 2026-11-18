<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create New Certificate') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
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
                <form method="POST" action="{{ route('admin.certificates.store') }}" 
                      id="certificateForm"
                      x-data="certificateFormData()" 
                      @submit.prevent="submitForm">
                    @csrf

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
                                        data-vessel="{{ $job->vessel->name ?? 'N/A' }}">
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
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
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
                                <input type="date" name="issue_date" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500" required value="{{ date('Y-m-d') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expiry Date *</label>
                                <input type="date" name="expiry_date" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">General Remarks / Notes</label>
                            <textarea name="remarks" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500" rows="3"></textarea>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3 pt-6 border-t">
                        <a href="{{ route('admin.certificates.index') }}" 
                           class="px-6 py-2 bg-gray-50 dark:bg-gray-700 hover:bg-gray-600 text-white rounded-md transition">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition"
                                x-show="templateLoaded">
                            Create Certificate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function certificateFormData() {
            return {
                formData: {
                    job_id: '',
                    category_id: ''
                },
                templateLoaded: false,
                templateName: '',
                currentTemplate: null,

                onJobSelect() {
                    console.log('Job selected:', this.formData.job_id);
                },

                async loadTemplate() {
                    if (!this.formData.category_id) {
                        this.templateLoaded = false;
                        return;
                    }

                    try {
                        const response = await fetch(`/admin/api/certificate-fields/${this.formData.category_id}`);
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
                    container.innerHTML = html;
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
                    let html = `<div x-data="{ instances: 1 }">`;
                    html += `<template x-for="instance in instances" :key="instance">`;
                    html += `<div class="mb-6 pb-6 border-b border-gray-200 last:border-0">`;
                    html += `<div class="flex justify-between items-center mb-4">`;
                    html += `<h4 class="font-semibold text-gray-700 dark:text-gray-300">Entry <span x-text="instance"></span></h4>`;
                    html += `<button type="button" x-show="instance > 1" @click="instances--" class="text-red-600 hover:text-red-800 text-sm">
                        <i class="fas fa-trash mr-1"></i>Remove
                    </button>`;
                    html += `</div>`;
                    
                    html += `<div class="grid grid-cols-1 ${section.layout_type === 'two_column' ? 'md:grid-cols-2' : ''} gap-4">`;
                    section.fields.forEach(field => {
                        html += this.renderField(field, true);
                    });
                    html += `</div></div></template>`;
                    
                    html += `<input type="hidden" :name="'section_${sectionId}_instances'" x-model="instances">`;
                    html += `<button type="button" @click="instances++" class="bg-green-100 hover:bg-green-200 text-green-800 px-4 py-2 rounded font-semibold text-sm transition">
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
                        ? `:name="'field_${field.id}_instance_' + instance"` 
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
                    
                    switch (field.field_type) {
                        case 'text':
                            return `<input type="text" ${fieldName} class="${baseClass}" ${required}>`;
                        case 'number':
                            return `<input type="number" ${fieldName} class="${baseClass}" ${required}>`;
                        case 'date':
                            return `<input type="date" ${fieldName} class="${baseClass}" ${required}>`;
                        case 'textarea':
                            return `<textarea ${fieldName} class="${baseClass}" rows="3" ${required}></textarea>`;
                        case 'select':
                        case 'dropdown':
                            let options = '<option value="">-- Select --</option>';
                            if (field.field_options) {
                                const opts = typeof field.field_options === 'string' 
                                    ? JSON.parse(field.field_options) 
                                    : field.field_options;
                                opts.forEach(opt => {
                                    options += `<option value="${opt}">${opt}</option>`;
                                });
                            }
                            return `<select ${fieldName} class="${baseClass}" ${required}>${options}</select>`;
                        default:
                            return `<input type="text" ${fieldName} class="${baseClass}" ${required}>`;
                    }
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
