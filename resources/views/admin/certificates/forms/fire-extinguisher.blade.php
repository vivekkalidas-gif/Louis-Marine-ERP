<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="col-span-2">
        <h4 class="text-lg font-semibold text-marine-900 dark:text-marine-100 mb-4 border-b pb-2">Fire Extinguisher Details</h4>
    </div>

    <div>
        <label class="theme-label">Extinguisher Type</label>
        <select name="data[extinguisher_type]" class="theme-input">
            <option value="CO2">CO2</option>
            <option value="DCP">DCP (Dry Powder)</option>
            <option value="Foam">Foam</option>
            <option value="Water">Water</option>
            <option value="Wet Chemical">Wet Chemical</option>
        </select>
    </div>

    <div>
        <label class="theme-label">Capacity (kg/L)</label>
        <input type="text" name="data[capacity]" class="theme-input">
    </div>

    <div>
        <label class="theme-label">Quantity</label>
        <input type="number" name="data[quantity]" class="theme-input" value="1">
    </div>

    <div>
        <label class="theme-label">Serial Number(s)</label>
        <input type="text" name="data[serial_number]" class="theme-input" placeholder="Comma separated if multiple">
    </div>

    <div>
        <label class="theme-label">Location Onboard</label>
        <input type="text" name="data[location]" class="theme-input">
    </div>
    
    <div>
        <label class="theme-label">Hydro Test Due</label>
        <input type="date" name="data[hydro_test_due]" class="theme-input">
    </div>

    <!-- Checks -->
    <div class="col-span-2 mt-4">
        <h4 class="text-lg font-semibold text-marine-900 dark:text-marine-100 mb-4 border-b pb-2">Service Checks</h4>
    </div>
    
    <div>
        <label class="theme-label">Pressure Check</label>
        <select name="data[pressure_check]" class="theme-input">
            <option value="OK">OK - Correct Pressure</option>
            <option value="Recharged">Low - Recharged</option>
        </select>
    </div>

    <div>
        <label class="theme-label">Weight Check</label>
        <select name="data[weight_check]" class="theme-input">
            <option value="OK">OK</option>
            <option value="Failed">Failed</option>
        </select>
    </div>

    <div>
        <label class="theme-label">Hose & Nozzle</label>
        <select name="data[hose_nozzle]" class="theme-input">
            <option value="Clear">Clear / Intact</option>
            <option value="Blocked">Blocked - Cleared</option>
            <option value="Damaged">Damaged - Replaced</option>
        </select>
    </div>

    <div class="col-span-2">
        <label class="theme-label">Remarks</label>
        <textarea name="data[remarks]" rows="3" class="theme-input"></textarea>
    </div>
</div>
