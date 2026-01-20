<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="col-span-2">
        <h4 class="text-lg font-semibold text-marine-900 dark:text-marine-100 mb-4 border-b pb-2">CO2 System Details</h4>
    </div>

    <div>
        <label class="theme-label">System Type</label>
        <input type="text" name="data[system_type]" class="theme-input" value="CO2 Total Flooding System">
    </div>

    <div>
        <label class="theme-label">Location Protected</label>
        <input type="text" name="data[location]" class="theme-input" placeholder="e.g. Engine Room">
    </div>

    <div>
        <label class="theme-label">Number of Cylinders</label>
        <input type="number" name="data[cylinder_count]" class="theme-input">
    </div>

    <div>
        <label class="theme-label">Cylinder Capacity (kg)</label>
        <input type="text" name="data[cylinder_capacity]" class="theme-input">
    </div>

    <div>
        <label class="theme-label">Last Pressure Test Date</label>
        <input type="date" name="data[pressure_test_date]" class="theme-input">
    </div>

    <!-- Checklist -->
    <div class="col-span-2 mt-4">
        <h4 class="text-lg font-semibold text-marine-900 dark:text-marine-100 mb-4 border-b pb-2">Inspection Points</h4>
    </div>

    <div>
        <label class="theme-label">Flexible Hose Condition</label>
        <select name="data[hose_condition]" class="theme-input">
            <option value="Good">Good</option>
            <option value="Damaged">Damaged - Replaced</option>
            <option value="N/A">N/A</option>
        </select>
    </div>

    <div>
        <label class="theme-label">Distribution Piping</label>
        <select name="data[piping_condition]" class="theme-input">
            <option value="Good">Good</option>
            <option value="Corroded">Corroded</option>
            <option value="Leakage">Leakage Found</option>
        </select>
    </div>

    <div>
        <label class="theme-label">Control Panel Test</label>
        <select name="data[control_panel_test]" class="theme-input">
            <option value="Functional">Functional</option>
            <option value="Faulty">Faulty</option>
        </select>
    </div>

    <div>
        <label class="theme-label">Audio/Visual Alarms</label>
        <select name="data[alarm_test]" class="theme-input">
            <option value="Functional">Functional</option>
            <option value="Faulty">Faulty</option>
        </select>
    </div>

    <div>
        <label class="theme-label">Safety Lock Status</label>
        <select name="data[safety_lock]" class="theme-input">
            <option value="Intact">Intact</option>
            <option value="Broken">Broken</option>
        </select>
    </div>

    <div class="col-span-2">
        <label class="theme-label">Remarks</label>
        <textarea name="data[remarks]" rows="3" class="theme-input"></textarea>
    </div>
</div>
