<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="col-span-2">
        <h4 class="text-lg font-semibold text-marine-900 dark:text-marine-100 mb-4 border-b pb-2">Lifeboat / Rescue Boat</h4>
    </div>

    <div>
        <label class="theme-label">Boat Type</label>
        <select name="data[boat_type]" class="theme-input">
            <option value="Totally Enclosed Lifeboat">Totally Enclosed Lifeboat</option>
            <option value="Rescue Boat">Rescue Boat</option>
            <option value="Freefall Lifeboat">Freefall Lifeboat</option>
        </select>
    </div>

    <div>
        <label class="theme-label">Serial Number / IDs</label>
        <input type="text" name="data[serial_number]" class="theme-input">
    </div>

    <!-- Annual / 5-Yearly Switch could be here but Type usually handles validity -->
    
    <!-- Test Results -->
    <div class="col-span-2 mt-4">
        <h4 class="text-lg font-semibold text-marine-900 dark:text-marine-100 mb-4 border-b pb-2">Test Results</h4>
    </div>

    <div>
        <label class="theme-label">Load Test Result</label>
        <select name="data[load_test]" class="theme-input">
            <option value="Passed">Passed (1.1 x SWL)</option>
            <option value="Not Applicable">Not Applicable (Annual)</option>
            <option value="Failed">Failed</option>
        </select>
    </div>

    <div>
        <label class="theme-label">Engine Start Test</label>
        <select name="data[engine_test]" class="theme-input">
            <option value="Operational">Operational</option>
            <option value="Non-Operational">Non-Operational</option>
        </select>
    </div>

    <div>
        <label class="theme-label">Hook Release System</label>
        <select name="data[hook_test]" class="theme-input">
            <option value="Operational">Operational</option>
            <option value="Faulty">Faulty</option>
        </select>
    </div>

    <div>
        <label class="theme-label">Launching Appliance (Davit)</label>
        <select name="data[davit_condition]" class="theme-input">
            <option value="Good">Good / Operational</option>
            <option value="Defective">Defective</option>
        </select>
    </div>

    <div class="col-span-2">
        <label class="theme-label">Remarks</label>
        <textarea name="data[remarks]" rows="3" class="theme-input"></textarea>
    </div>
</div>
