{{-- Step 1 of the certificate create/edit forms: job number typed in by hand,
     customer and vessel picked directly. Relies on the parent Alpine component's
     formData.{job_number,customer_id,vessel_id}, customerVessels() and onCustomerChange(). --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div>
        <label for="job_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Job Number *</label>
        <input type="text" id="job_number" name="job_number" required maxlength="100" autocomplete="off"
               x-model="formData.job_number"
               placeholder="e.g. JOB-2026-0142"
               class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500">
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Type the job number as it appears on the job card.</p>
    </div>

    <div>
        <label for="customer_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Customer *</label>
        <select id="customer_id" name="customer_id" required
                x-model="formData.customer_id" @change="onCustomerChange()"
                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500">
            <option value="">-- Select Customer --</option>
            @foreach($customers as $customer)
                <option value="{{ $customer->id }}">{{ $customer->company_name }}</option>
            @endforeach
        </select>
        @if($customers->isEmpty())
            <p class="text-sm text-yellow-600 mt-1">No customers yet — add one under Customers first.</p>
        @endif
    </div>

    <div>
        <label for="vessel_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Vessel</label>
        <select id="vessel_id" name="vessel_id"
                x-model="formData.vessel_id"
                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500">
            <option value="">-- No vessel --</option>
            <template x-for="vessel in customerVessels()" :key="vessel.id">
                <option :value="vessel.id" x-text="vessel.name" :selected="vessel.id === formData.vessel_id"></option>
            </template>
        </select>
    </div>
</div>
