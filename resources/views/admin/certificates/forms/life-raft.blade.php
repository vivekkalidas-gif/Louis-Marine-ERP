<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Equipment Details -->
    <div class="col-span-2">
        <h4 class="text-lg font-semibold text-marine-900 dark:text-marine-100 mb-4 border-b pb-2">Equipment Details</h4>
    </div>

    <div>
        <label class="theme-label">Manfacturer</label>
        <input type="text" name="data[manufacturer]" class="theme-input" placeholder="e.g. Viking, RFD">
    </div>

    <div>
        <label class="theme-label">Model / Type</label>
        <input type="text" name="data[model]" class="theme-input">
    </div>

    <div>
        <label class="theme-label">Serial Number</label>
        <input type="text" name="data[serial_number]" class="theme-input">
    </div>

    <div>
        <label class="theme-label">Capacity (Persons)</label>
        <input type="number" name="data[capacity]" class="theme-input">
    </div>

    <div>
        <label class="theme-label">Last Service Date</label>
        <input type="date" name="data[last_service_date]" class="theme-input">
    </div>

    <div>
        <label class="theme-label">Next Due Date</label>
        <input type="date" name="data[next_due_date]" class="theme-input">
    </div>

    <!-- Service Station Info -->
    <div class="col-span-2">
        <label class="theme-label">Service Station Name</label>
        <input type="text" name="data[service_station]" class="theme-input" value="Louis Marine Qatar" readonly> <!-- Auto-filled but editable if strictly needed -->
    </div>

    <!-- Inspection Results -->
    <div class="col-span-2 mt-4">
        <h4 class="text-lg font-semibold text-marine-900 dark:text-marine-100 mb-4 border-b pb-2">Inspection Checklist</h4>
    </div>

    <div class="col-span-2 md:col-span-1">
        <label class="theme-label">Inflation Test Result</label>
        <select name="data[inflation_test]" class="theme-input">
            <option value="Passed">Passed</option>
            <option value="Failed">Failed</option>
            <option value="N/A">N/A</option>
        </select>
    </div>

    <div class="col-span-2 md:col-span-1">
        <label class="theme-label">Relief Valve Test</label>
        <select name="data[relief_valve_test]" class="theme-input">
            <option value="Passed">Passed</option>
            <option value="Failed">Failed</option>
            <option value="N/A">N/A</option>
        </select>
    </div>

    <div class="col-span-2 md:col-span-1">
        <label class="theme-label">Survival Pack Check</label>
        <select name="data[survival_pack_check]" class="theme-input">
            <option value="Complete">Complete</option>
            <option value="Incomplete">Incomplete</option>
            <option value="Expired">Expired</option>
        </select>
    </div>

    <div class="col-span-2 md:col-span-1">
        <label class="theme-label">HRU Status</label>
        <select name="data[hru_status]" class="theme-input">
            <option value="Operational">Operational</option>
            <option value="Replaced">Replaced</option>
            <option value="Expired">Expired</option>
        </select>
    </div>

    <div class="col-span-2">
        <label class="theme-label">Remarks</label>
        <textarea name="data[remarks]" rows="3" class="theme-input"></textarea>
    </div>
</div>
