<x-app-layout>
    <x-slot name="header">
        <h2 class="theme-text-header">
            {{ __('Issue Equipment Certificate') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Compliance Alert -->
            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900 border-l-4 border-blue-500 rounded">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-300 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h4 class="font-bold text-blue-800 dark:text-blue-100">Marine Compliance Certificate</h4>
                        <p class="text-sm text-blue-700 dark:text-blue-200">All certificates MUST be linked to Job + Equipment Category + Equipment Type with detailed technical data.</p>
                    </div>
                </div>
            </div>

            <div class="theme-card">
                <form method="POST" action="{{ route('admin.certificates.store') }}" 
                      id="certificateForm"
                      x-data="certificateForm()" 
                      @submit.prevent="submitForm"
                      novalidate>
                    <!-- Debug Step Indicator (Temporary) -->
                    <div class="text-xs text-red-500 font-mono absolute top-0 right-0 p-2">
                        Debug Step: <span x-text="step"></span>
                    </div>
                    @csrf

                    <!-- STEP 1: Job Selection -->
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-marine-900 dark:text-marine-100 mb-4 pb-2 border-b dark:border-marine-800 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">1</span>
                            Select Job Number
                        </h3>
                        
                        <div>
                            <label class="theme-label">Job Number *</label>
                            <select name="service_job_id" class="theme-input" required 
                                    x-model="formData.job_id"
                                    @change="step = 2">
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

                    <!-- STEP 2: Equipment Category Selection -->
                    <div x-show="formData.job_id" x-transition class="mb-8">
                        <h3 class="text-lg font-bold text-marine-900 dark:text-marine-100 mb-4 pb-2 border-b dark:border-marine-800 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">2</span>
                            Select Equipment Category
                        </h3>
                        
                        <div>
                            <label class="theme-label">Equipment Category *</label>
                            <select name="equipment_category_id" class="theme-input" required 
                                    x-model="formData.category_id" 
                                    @change="onCategorySelect()">
                                <option value="">-- Select Equipment Category --</option>
                                @foreach($equipmentCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Examples: Fire Extinguisher, Life Raft, SCBA, EEBD, Life Jacket, etc.
                            </p>
                            <div class="mt-4 flex justify-between" x-show="step === 2">
                                <x-secondary-button type="button" @click="step = 1">Back</x-secondary-button>
                                <x-primary-button type="button" @click="validateStep2()">Next: Equipment Details</x-primary-button>
                            </div>
                        </div>
                    </div>


                <div x-show="step === 3" x-transition>
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-marine-900 dark:text-marine-100 mb-4 pb-2 border-b dark:border-marine-800 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">3</span>
                            Equipment Details
                        </h3>
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Add equipment items manually or upload an Excel file.</p>
                        <div class="flex gap-2">
                             <a href="#" @click.prevent="downloadTemplate()" class="px-3 py-2 text-sm text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-md">Download Template</a>
                            <label class="cursor-pointer px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Upload Excel
                                <input type="file" class="hidden" accept=".xlsx, .xls, .csv" @change="handleExcelUpload">
                            </label>
                            <button type="button" @click="addItem()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Add Row
                            </button>
                        </div>
                    </div>

                    <!-- Grid Table -->
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider sticky left-0 bg-gray-50 dark:bg-gray-800 z-10 w-10">#</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[200px]">Equipment Type <span class="text-red-500">*</span></th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[150px]">Serial No <span class="text-red-500">*</span></th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[150px]">Manufacturer</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[130px]">Mfg Date</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[130px]">Last Service</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[130px]">Next Due</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[120px]">Condition</th>
                                    
                                    <!-- Dynamic Headers -->
                                    <template x-for="field in categoryFields" :key="field.name">
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[150px]" x-text="field.label"></th>
                                    </template>
                                    
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[200px]">Remarks</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-10 sticky right-0 bg-gray-50 dark:bg-gray-800 z-10">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-for="(item, index) in formData.items" :key="index">
                                    <tr>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 sticky left-0 bg-white dark:bg-gray-900 z-10" x-text="index + 1"></td>
                                        
                                        <!-- Equipment Type -->
                                        <td class="px-3 py-2">
                                            <select :name="'items[' + index + '][equipment_type]'" x-model="item.equipment_type" class="theme-input text-sm py-1">
                                                <option value="">Select Type</option>
                                                <template x-for="type in equipmentTypes" :key="type.id">
                                                    <option :value="type.name" x-text="type.name"></option>
                                                </template>
                                                <!-- Optional: Allow 'Other' manual entry? User asked for Dropdown from Master. -->
                                                <!-- If user wants manual override, we might need a combo box, but for now stick to Master Dropdown -->
                                            </select>
                                        </td>
                                        
                                        <!-- Serial Number -->
                                        <td class="px-3 py-2">
                                            <input type="text" :name="'items[' + index + '][serial_number]'" x-model="item.serial_number" class="theme-input text-sm py-1" placeholder="Serial No">
                                        </td>
                                        
                                        <!-- Manufacturer -->
                                        <td class="px-3 py-2">
                                            <input type="text" :name="'items[' + index + '][manufacturer]'" x-model="item.manufacturer" class="theme-input text-sm py-1" placeholder="Brand">
                                        </td>
                                        
                                        <!-- Dates -->
                                        <td class="px-3 py-2">
                                            <input type="date" :name="'items[' + index + '][manufacture_date]'" x-model="item.manufacture_date" class="theme-input text-sm py-1">
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="date" :name="'items[' + index + '][last_service_date]'" x-model="item.last_service_date" class="theme-input text-sm py-1">
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="date" :name="'items[' + index + '][next_due_date]'" x-model="item.next_due_date" class="theme-input text-sm py-1">
                                        </td>
                                        
                                        <!-- Condition -->
                                        <td class="px-3 py-2">
                                            <select :name="'items[' + index + '][condition]'" x-model="item.condition" class="theme-input text-sm py-1">
                                                <option value="good">Good</option>
                                                <option value="repair">Repair</option>
                                                <option value="replace">Replace</option>
                                            </select>
                                        </td>
                                        
                                        <!-- Dynamic Fields Inputs -->
                                        <template x-for="field in categoryFields" :key="field.name">
                                            <td class="px-3 py-2">
                                                <!-- Select -->
                                                <template x-if="field.type === 'select'">
                                                    <select :name="'items[' + index + '][details][' + field.name + ']'" x-model="item.details[field.name]" class="theme-input text-sm py-1">
                                                        <option value="">Select</option>
                                                        <template x-for="opt in field.options" :key="opt">
                                                            <option :value="opt" x-text="opt"></option>
                                                        </template>
                                                    </select>
                                                </template>
                                                
                                                <!-- Text/Number -->
                                                <template x-if="['text', 'number', 'date'].includes(field.type)">
                                                    <input :type="field.type" :name="'items[' + index + '][details][' + field.name + ']'" x-model="item.details[field.name]" class="theme-input text-sm py-1">
                                                </template>
                                            </td>
                                        </template>
                                        
                                        <!-- Remarks -->
                                        <td class="px-3 py-2">
                                            <input type="text" :name="'items[' + index + '][remarks]'" x-model="item.remarks" class="theme-input text-sm py-1">
                                        </td>
                                        
                                        <!-- Action -->
                                        <td class="px-3 py-2 text-center sticky right-0 bg-white dark:bg-gray-900 z-10">
                                            <button type="button" @click="removeItem(index)" class="text-red-600 hover:text-red-900" title="Remove Row">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewbox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="formData.items.length === 0">
                                    <tr>
                                        <td colspan="100%" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                            Given that no items have been added yet, please add a row or upload an Excel file.
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4 flex justify-between">
                        <x-secondary-button type="button" @click="step = 2">Back</x-secondary-button>
                        <!-- Using standard button to force type="button" behavior and prevent accidental submit -->
                        <button type="button" 
                                @click.prevent="validateStep3()"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white dark:bg-gray-800 focus:bg-gray-700 dark:focus:bg-white dark:bg-gray-800 active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Next: Certificate
                        </button>
                    </div>
                </div>

                    <!-- STEP 4: Certificate Information -->
                    <div x-show="step === 4" x-transition class="mb-8">
                        <h3 class="text-lg font-bold text-marine-900 dark:text-marine-100 mb-4 pb-2 border-b dark:border-marine-800 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">4</span>
                            Certificate Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="theme-label">Certificate Type *</label>
                                <select name="certificate_type_id" class="theme-input" required>
                                    <option value="">-- Select Type --</option>
                                    @foreach($types as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="theme-label">Issue Date *</label>
                                <input type="date" name="issue_date" class="theme-input" required 
                                       value="{{ date('Y-m-d') }}">
                            </div>
                            <div>
                                <label class="theme-label">Expiry Date *</label>
                                <input type="date" name="expiry_date" class="theme-input" required>
                            </div>
                        </div>

                        <!-- Remarks (Global) -->
                        <div class="mt-4">
                            <label class="theme-label">General Remarks / Notes</label>
                            <textarea name="remarks" class="theme-input" rows="3"></textarea>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-3 pt-6 border-t dark:border-marine-800">
                        <a href="{{ route('admin.certificates.index') }}" 
                           class="px-6 py-2 bg-gray-50 dark:bg-gray-700 hover:bg-gray-600 text-white rounded-md transition">
                            Cancel
                        </a>
                        <button type="button" 
                                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition"
                                @click="submitForm()"
                                x-show="step === 4">
                            Issue Certificate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js"></script>
    <script>
        // Global Error Handler for Debugging
        window.onerror = function(msg, url, line, col, error) {
            alert("JS Error: " + msg + "\nLine: " + line);
            console.error(error);
            return false;
        };

        document.addEventListener('alpine:init', () => {
            try {
                Alpine.data('certificateForm', () => ({
                    step: 1,
                    jobs: @json($jobs),
                    categories: @json($equipmentCategories),
                    equipmentTypes: [],
                    categoryFields: [],
                    categoryName: '',
                    
                    formData: {
                        service_job_id: "{{ request('job_id') }}",
                        category_id: '',
                        items: [],
                        // Common fields stored for step 4
                        remarks: '' 
                    },

                    init() {
                         console.log('Certificate Form Initialized');
                         if (this.formData.service_job_id) {
                            // Valid job pre-selected logic could go here
                         }
                    },
                    
                    // ... methods ...

                    nextStep() {
                        if (this.step === 1 && !this.formData.service_job_id) { alert('Please select a job'); return; }
                        if (this.step === 2 && !this.formData.category_id) { alert('Please select a category'); return; }
                        this.step++;
                    },
                    
                    validateStep1() {
                        if (!this.formData.service_job_id) { alert('Please select a Job Number'); return; }
                        this.step = 2;
                    },
                    
                    validateStep2() {
                        if (!this.formData.category_id) { alert('Please select an Equipment Category'); return; }
                        this.step = 3;
                        if (this.formData.items.length === 0) { this.addItem(); }
                    },
                    
                    validateStep3() {
                        try {
                            console.log('Validating Step 3...');
                            if (this.formData.items.length === 0) {
                                alert('Please add at least one equipment item.');
                                return;
                            }
                            let isValid = true;
                            this.formData.items.forEach((item, index) => {
                                if (!item.equipment_type || !item.serial_number || !item.condition) { isValid = false; }
                            });
                            if (!isValid) {
                                alert('Please fill in all mandatory fields (Type, Serial No, Condition) for all rows.');
                                return;
                            }
                            this.step = 4;
                        } catch (error) {
                            alert('System Error in Step 3: ' + error.message);
                        }
                    },

                    async onCategorySelect() {
                        if (!this.formData.category_id) return;
                        this.categoryFields = [];
                        this.equipmentTypes = [];
                        try {
                            const r1 = await fetch(`api/equipment/categories/${this.formData.category_id}/items`);
                            if (!r1.ok) throw new Error('Failed to load types');
                            const d1 = await r1.json();
                            this.equipmentTypes = Array.isArray(d1) ? d1 : (d1.items || []);
                        } catch (e) { console.error(e); }

                        try {
                            const r2 = await fetch(`api/equipment-fields/${this.formData.category_id}`);
                            const d2 = await r2.json();
                            this.categoryFields = d2.fields || [];
                            this.categoryName = d2.category || '';
                        } catch (e) { console.error(e); }
                    },

                    addItem() {
                        let details = {};
                        this.categoryFields.forEach(f => details[f.name] = '');
                        this.formData.items.push({
                            equipment_type: '', serial_number: '', manufacturer: '', 
                            manufacture_date: '', last_service_date: '', next_due_date: '', 
                            condition: 'good', remarks: '', details: details
                        });
                    },
                    
                    removeItem(index) {
                        if (confirm('Remove row?')) this.formData.items.splice(index, 1);
                    },

                    downloadTemplate() {
                       // Build Headers
                       let headers = [
                           "Equipment Type", "Serial Number", "Manufacturer", "Mfg Date (YYYY-MM-DD)", 
                           "Last Service (YYYY-MM-DD)", "Next Due (YYYY-MM-DD)", "Condition (good/repair/replace)", "Remarks"
                       ];
                       
                       // Add dynamic headers
                       this.categoryFields.forEach(f => headers.push(f.label));
                       
                       // Create dummy row
                       let row = ["Portable CO2", "SN-123", "Kidde", "2023-01-01", "2024-01-01", "2025-01-01", "good", "Check OK"];
                       this.categoryFields.forEach(f => row.push("")); // Empty or sample
                       
                       if (typeof XLSX === 'undefined') { alert('Excel library not loaded'); return; }

                       const ws = XLSX.utils.aoa_to_sheet([headers, row]);
                       const wb = XLSX.utils.book_new();
                       XLSX.utils.book_append_sheet(wb, ws, "Template");
                       XLSX.writeFile(wb, `${this.categoryName}_Template.xlsx`);
                    },
                    
                    handleExcelUpload(event) {
                        const file = event.target.files[0];
                        if (!file) return;
                        
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            const data = new Uint8Array(e.target.result);
                            const workbook = XLSX.read(data, { type: 'array' });
                            const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                            const jsonData = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });
                            
                            // Parse Data (Skip header row 0)
                            if (jsonData.length > 1) {
                                const commonColCount = 8;
                                for (let i = 1; i < jsonData.length; i++) {
                                    const row = jsonData[i];
                                    if (!row[0]) continue;
                                    
                                    let details = {};
                                    this.categoryFields.forEach((field, fIndex) => {
                                        details[field.name] = row[commonColCount + fIndex] || '';
                                    });
                                    
                                    this.formData.items.push({
                                        equipment_type: row[0] || '',
                                        serial_number: row[1] || '',
                                        manufacturer: row[2] || '',
                                        manufacture_date: this.parseExcelDate(row[3]),
                                        last_service_date: this.parseExcelDate(row[4]),
                                        next_due_date: this.parseExcelDate(row[5]),
                                        condition: (row[6] || 'good').toLowerCase(),
                                        remarks: row[7] || '',
                                        details: details
                                    });
                                }
                                alert(`Imported ${jsonData.length - 1} rows successfully.`);
                            }
                        };
                        reader.readAsArrayBuffer(file);
                        event.target.value = '';
                    },
                    
                    parseExcelDate(excelDate) {
                        if (!excelDate) return '';
                        if (typeof excelDate === 'number') {
                             const date = new Date(Math.round((excelDate - 25569)*86400*1000));
                             if (isNaN(date.getTime())) return '';
                             return date.toISOString().split('T')[0];
                        }
                        if (typeof excelDate === 'string') {
                            const date = new Date(excelDate);
                            if (!isNaN(date.getTime())) {
                                 return date.toISOString().split('T')[0];
                            }
                        }
                        return '';
                    },
    
                    submitForm() {
                         try {
                             console.log('Submitting Form...');
                             const form = document.getElementById('certificateForm');
                             if (!form) throw new Error('Form element not found');
                             
                             const typeId = form.querySelector('[name="certificate_type_id"]').value;
                             const issueDate = form.querySelector('[name="issue_date"]').value;
                             const expiryDate = form.querySelector('[name="expiry_date"]').value;
        
                             if (!typeId) { alert('Please select a Certificate Type'); return; }
                             if (!issueDate) { alert('Please select an Issue Date'); return; }
                             if (!expiryDate) { alert('Please select an Expiry Date'); return; }
        
                             HTMLFormElement.prototype.submit.call(form);
                         } catch (error) {
                             alert('System Error in Submit: ' + error.message);
                             console.error(error);
                         }
                    }
                }));
            } catch (e) {
                alert("Alpine Init Error: " + e.message);
            }
        });
    </script>
                
                // Grid Methods
                addItem() {
                    // Create object with empty dynamic details
                    let details = {};
                    this.categoryFields.forEach(field => {
                        details[field.name] = '';
                    });
                
                    this.formData.items.push({
                        equipment_type: '',
                        serial_number: '',
                        manufacturer: '',
                        manufacture_date: '',
                        last_service_date: '',
                        next_due_date: '',
                        condition: 'good',
                        remarks: '',
                        details: details
                    });
                },
                
                removeItem(index) {
                    if (confirm('Are you sure you want to remove this row?')) {
                        this.formData.items.splice(index, 1);
                    }
                },
                
                // Excel Methods
                downloadTemplate() {
                    // Build Headers
                    let headers = [
                        "Equipment Type", "Serial Number", "Manufacturer", "Mfg Date (YYYY-MM-DD)", 
                        "Last Service (YYYY-MM-DD)", "Next Due (YYYY-MM-DD)", "Condition (good/repair/replace)", "Remarks"
                    ];
                    
                    // Add dynamic headers
                    this.categoryFields.forEach(f => headers.push(f.label));
                    
                    // Create dummy row
                    let row = ["Portable CO2", "SN-123", "Kidde", "2023-01-01", "2024-01-01", "2025-01-01", "good", "Check OK"];
                    this.categoryFields.forEach(f => row.push("")); // Empty or sample
                    
                    const ws = XLSX.utils.aoa_to_sheet([headers, row]);
                    const wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, "Template");
                    XLSX.writeFile(wb, `${this.categoryName}_Template.xlsx`);
                },
                
                handleExcelUpload(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const data = new Uint8Array(e.target.result);
                        const workbook = XLSX.read(data, { type: 'array' });
                        const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                        const jsonData = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });
                        
                        // Parse Data (Skip header row 0)
                        if (jsonData.length > 1) {
                            // Map columns based on fixed indices + dynamic
                            // Assuming Template Order is fixed as per downloadTemplate
                            
                            // Headers are at jsonData[0]
                            // We should map by header name ideally, but fixed index is easier if template is strict.
                            // Let's rely on fixed index for "Common" and then dynamic for the rest.
                            
                            const commonColCount = 8; // Type, Serial, Maker, Mfg, Last, Next, Cond, Rem
                            
                            for (let i = 1; i < jsonData.length; i++) {
                                const row = jsonData[i];
                                if (!row[0]) continue; // Skip empty type
                                
                                let details = {};
                                // Dynamic columns start at index 8
                                this.categoryFields.forEach((field, fIndex) => {
                                    // Excel row index = commonColCount + fIndex
                                    details[field.name] = row[commonColCount + fIndex] || '';
                                });
                                
                                this.formData.items.push({
                                    equipment_type: row[0] || '',
                                    serial_number: row[1] || '',
                                    manufacturer: row[2] || '',
                                    manufacture_date: this.parseExcelDate(row[3]),
                                    last_service_date: this.parseExcelDate(row[4]),
                                    next_due_date: this.parseExcelDate(row[5]),
                                    condition: (row[6] || 'good').toLowerCase(),
                                    remarks: row[7] || '',
                                    details: details
                                });
                            }
                            alert(`Imported ${jsonData.length - 1} rows successfully.`);
                        }
                    };
                    reader.readAsArrayBuffer(file);
                    // Reset input
                    event.target.value = '';
                },
                
                parseExcelDate(excelDate) {
                    // Handle JS date, Text date, or Excel serial
                    if (!excelDate) return '';
                    
                    if (typeof excelDate === 'number') {
                         // Excel serial date to JS Date
                         const date = new Date(Math.round((excelDate - 25569)*86400*1000));
                         // Verify validity
                         if (isNaN(date.getTime())) return '';
                         return date.toISOString().split('T')[0];
                    }
                    
                    // If string, check format YYYY-MM-DD
                    // Basic check: 10 chars, dashes at 4 and 7
                    if (typeof excelDate === 'string') {
                        // Attempt to parse info standard format
                        const date = new Date(excelDate);
                        if (!isNaN(date.getTime())) {
                             return date.toISOString().split('T')[0];
                        }
                    }
                    
                    // Invalid/Unrecognized format -> Return empty to avoid browser validation error
                    return '';
                },

                submitForm() {
                     try {
                         console.log('Submitting Form...');
                         // Manual Validation for Step 4
                         const form = document.getElementById('certificateForm');
                         if (!form) throw new Error('Form element not found');
                         
                         const typeId = form.querySelector('[name="certificate_type_id"]').value;
                         const issueDate = form.querySelector('[name="issue_date"]').value;
                         const expiryDate = form.querySelector('[name="expiry_date"]').value;
    
                         if (!typeId) { alert('Please select a Certificate Type'); return; }
                         if (!issueDate) { alert('Please select an Issue Date'); return; }
                         if (!expiryDate) { alert('Please select an Expiry Date'); return; }
    
                         // If valid, submit
                         // Use prototype call to avoid conflict if any input is named 'submit'
                         HTMLFormElement.prototype.submit.call(form);
                     } catch (error) {
                         alert('System Error in Submit: ' + error.message);
                         console.error(error);
                     }
                }
            }));
        });
    </script>
</x-app-layout>
