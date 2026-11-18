<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight">
                    {{ __('Issue Equipment Certificate') }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Select certificate template, populate field values, and assign internal verifier</p>
            </div>
            <a href="{{ route('engineer.certificates.index') }}" class="btn-ghost text-xs">Back to Certificates</a>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        <div class="max-w-4xl mx-auto space-y-6">

            <form action="{{ route('engineer.certificates.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Core Details Card -->
                <div class="theme-card">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white pb-3 mb-4 border-b border-slate-200 dark:border-slate-700/80">Certificate Master Details</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Template -->
                        <div class="md:col-span-2">
                            <label class="theme-label">Certificate Template</label>
                            <select name="certificate_template_id" id="certificate_template_id" required class="theme-input text-xs py-2">
                                <option value="">Select Certificate Template...</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}" data-category-id="{{ $template->equipment_category_id }}" {{ old('certificate_template_id') == $template->id ? 'selected' : '' }}>
                                        {{ $template->name }} (Category: {{ $template->equipmentCategory->name ?? 'General' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Customer -->
                        <div>
                            <label class="theme-label">Customer Company</label>
                            <select name="customer_id" id="customer_id" required class="theme-input text-xs py-2">
                                <option value="">Select Customer...</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->company_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Vessel -->
                        <div>
                            <label class="theme-label">Vessel Name</label>
                            <select name="vessel_id" id="vessel_id" required class="theme-input text-xs py-2">
                                <option value="">Select Vessel...</option>
                                @foreach(\App\Models\Vessel::all() as $vessel)
                                     <option value="{{ $vessel->id }}" data-customer-id="{{ $vessel->customer_id }}">
                                         {{ $vessel->name }}
                                     </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Service Job Reference -->
                        <div>
                            <label class="theme-label">Associated Service Job (Optional)</label>
                            <select name="job_id" id="job_id" class="theme-input text-xs py-2">
                                <option value="">Select Job Reference...</option>
                                @foreach($jobs as $job)
                                    <option value="{{ $job->id }}" {{ (request('job_id') == $job->id) ? 'selected' : '' }}>{{ $job->job_number }} - {{ $job->vessel->name ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Issue Date -->
                        <div>
                            <label class="theme-label">Inspection / Issue Date</label>
                            <input type="date" name="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" required class="theme-input text-xs py-2">
                        </div>

                        <!-- Verifier Assignment -->
                        <div class="md:col-span-2 p-4 rounded-xl bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/60">
                            <label class="theme-label text-amber-800 dark:text-amber-300 font-bold">Assign Verifying Engineer <span class="text-rose-500">*</span></label>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">Select a peer engineer to review, verify field values, and apply digital sign-off.</p>
                            <select name="verifier_id" id="verifier_id" required class="theme-input text-xs py-2">
                                <option value="">Select Engineer to Verify...</option>
                                @foreach($verifiers as $verifier)
                                    <option value="{{ $verifier->id }}" {{ old('verifier_id') == $verifier->id ? 'selected' : '' }}>{{ $verifier->name }} ({{ $verifier->email }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Certificate Fields Container -->
                <div id="dynamic-fields-container" class="theme-card hidden space-y-4">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white pb-3 border-b border-slate-200 dark:border-slate-700/80">Certificate Form Section Values</h3>
                    <div id="fields-loading" class="hidden text-center py-6 text-slate-400 text-xs font-semibold">
                        Loading template form fields...
                    </div>
                    <div id="fields-content" class="space-y-4"></div>
                </div>

                <!-- Action Footer Buttons -->
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="history.back()" class="btn-secondary text-xs">Cancel</button>
                    
                    <button type="submit" name="action" value="draft" class="btn-ghost text-xs">
                        Save Draft
                    </button>
                    
                    <button type="submit" name="action" value="submit" class="btn-primary text-xs font-bold">
                        Create & Send for Verification
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const templateSelect = document.getElementById('certificate_template_id');
            const fieldsContainer = document.getElementById('dynamic-fields-container');
            const fieldsContent = document.getElementById('fields-content');
            const fieldsLoading = document.getElementById('fields-loading');

            templateSelect.addEventListener('change', function() {
                const templateId = this.value;
                if (!templateId) {
                    fieldsContainer.classList.add('hidden');
                    return;
                }

                fieldsContainer.classList.remove('hidden');
                fieldsLoading.classList.remove('hidden');
                fieldsContent.innerHTML = '';

                fetch(`/admin/certificate-templates/${templateId}/preview?ajax=1`)
                    .then(response => response.text())
                    .then(html => {
                        fieldsContent.innerHTML = html;
                        fieldsLoading.classList.add('hidden');
                    })
                    .catch(error => {
                        console.error('Error loading template fields:', error);
                        fieldsContent.innerHTML = '<p class="text-xs text-rose-500 font-bold py-2">Failed to load dynamic template fields.</p>';
                        fieldsLoading.classList.add('hidden');
                    });
            });

            // Customer to Vessel Dropdown Sync
            const customerSelect = document.getElementById('customer_id');
            const vesselSelect = document.getElementById('vessel_id');
            const allVessels = Array.from(vesselSelect.options);

            customerSelect.addEventListener('change', function() {
                const customerId = this.value;
                vesselSelect.innerHTML = '<option value="">Select Vessel...</option>';
                
                allVessels.forEach(opt => {
                    if (opt.value === "" || opt.dataset.customerId == customerId) {
                        vesselSelect.appendChild(opt.cloneNode(true));
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
