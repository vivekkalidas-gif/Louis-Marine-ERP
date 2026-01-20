@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Edit Certificate</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Edit draft certificate</p>
        </div>
        <a href="{{ route('engineer.certificates.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300">Back to List</a>
    </div>

    <form action="{{ route('engineer.certificates.update', $certificate) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b border-gray-100 pb-3 mb-4">Core Details</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Template (Read Only) -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Certificate Template</label>
                    <input type="text" value="{{ $certificate->certificateTemplate->name }}" disabled
                           class="w-full rounded-md border-gray-300 bg-gray-100 shadow-sm">
                    <input type="hidden" name="certificate_template_id" value="{{ $certificate->certificate_template_id }}">
                </div>

                <!-- Customer -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Customer</label>
                    <select name="customer_id" id="customer_id" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500">
                        <option value="">Select Customer...</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id', $certificate->customer_id) == $customer->id ? 'selected' : '' }}>{{ $customer->company_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Vessel -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vessel</label>
                    <select name="vessel_id" id="vessel_id" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500">
                        <option value="">Select Vessel...</option>
                        <!-- Populated via JS based on Customer (or just show all for now) -->
                        @foreach(\App\Models\Vessel::all() as $vessel)
                             <option value="{{ $vessel->id }}" data-customer-id="{{ $vessel->customer_id }}" {{ old('vessel_id', $certificate->vessel_id) == $vessel->id ? 'selected' : '' }}>
                                 {{ $vessel->name }}
                             </option>
                        @endforeach
                    </select>
                </div>

                <!-- Job Reference (Optional) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Service Job (Optional)</label>
                    <select name="job_id" id="job_id" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500">
                        <option value="">Select Job...</option>
                        @foreach($jobs as $job)
                            <option value="{{ $job->id }}" {{ old('job_id', $certificate->service_job_id) == $job->id ? 'selected' : '' }}>{{ $job->job_number }} - {{ $job->vessel->name ?? '' }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Issue Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Issue Date</label>
                    <input type="date" name="issue_date" value="{{ old('issue_date', optional($certificate->issue_date)->format('Y-m-d')) }}" required
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500">
                </div>

                <!-- Verifier (CRITICAL) -->
                <div class="col-span-2 bg-yellow-50 p-4 rounded-md border border-yellow-200">
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-200 mb-1">Assign Verifier <span class="text-red-500">*</span></label>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Select another engineer to verify and sign this certificate.</p>
                    <select name="verifier_id" id="verifier_id" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500">
                        <option value="">Select Engineer to Verify...</option>
                        @foreach($verifiers as $verifier)
                            <option value="{{ $verifier->id }}" {{ old('verifier_id', $certificate->verifier_id) == $verifier->id ? 'selected' : '' }}>{{ $verifier->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Dynamic Fields Container -->
        <div id="dynamic-fields-container" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b border-gray-100 pb-3 mb-4">Certificate Details</h3>
            <div id="fields-content">
                <!-- Render Fields Server Side for Edit -->
                @include('admin.certificate-templates.partials.fields-inputs', [
                    'sections' => $certificate->certificateTemplate->sections, 
                    'values' => $certificate->fieldValues->pluck('value', 'certificate_template_field_id')->toArray()
                ])
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <button type="button" onclick="history.back()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700">Cancel</button>
            
            <button type="submit" name="action" value="draft" class="px-4 py-2 bg-gray-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-gray-700">
                Save as Draft
            </button>
            
            <button type="submit" name="action" value="submit" class="px-4 py-2 bg-marine-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-marine-700">
                Update & Send for Verification
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Basic Vessel linking
        const customerSelect = document.getElementById('customer_id');
        const vesselSelect = document.getElementById('vessel_id');
        const allVessels = Array.from(vesselSelect.options);

        // Initial toggle based on load
        if(customerSelect.value) {
            const customerId = customerSelect.value;
             // Keep selected
             const currentVessel = vesselSelect.value;
             
             vesselSelect.innerHTML = '<option value="">Select Vessel...</option>';
             allVessels.forEach(opt => {
                if (opt.value === "" || opt.dataset.customerId == customerId) {
                    const clone = opt.cloneNode(true);
                    if(clone.value == currentVessel) clone.selected = true;
                    vesselSelect.appendChild(clone);
                }
            });
        }

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
@endsection
