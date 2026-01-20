<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="col-span-2">
        <h4 class="text-lg font-semibold text-marine-900 dark:text-marine-100 mb-4 border-b pb-2">Breathing Apparatus (SCBA)</h4>
    </div>

    <div>
        <label class="theme-label">Manufacturer</label>
        <input type="text" name="data[manufacturer]" class="theme-input">
    </div>

    <div>
        <label class="theme-label">Model</label>
        <input type="text" name="data[model]" class="theme-input">
    </div>

    <div>
        <label class="theme-label">Serial Number</label>
        <input type="text" name="data[serial_number]" class="theme-input">
    </div>

    <div>
        <label class="theme-label">Cylinder Pressure (Bar)</label>
        <input type="text" name="data[pressure]" class="theme-input">
    </div>
    
    <div>
        <label class="theme-label">Cylinder Hydro Test Date</label>
        <input type="date" name="data[hydro_test_date]" class="theme-input">
    </div>

    <!-- Checks -->
    <div class="col-span-2 mt-4">
        <h4 class="text-lg font-semibold text-marine-900 dark:text-marine-100 mb-4 border-b pb-2">Component Status</h4>
    </div>

    <div>
        <label class="theme-label">Face Mask Condition</label>
        <select name="data[mask_condition]" class="theme-input">
            <option value="Good">Good</option>
            <option value="Scratched">Scratched</option>
            <option value="Damaged">Damaged</option>
        </select>
    </div>

    <div>
        <label class="theme-label">Harness / Straps</label>
        <select name="data[harness_condition]" class="theme-input">
            <option value="Good">Good</option>
            <option value="Worn">Worn</option>
            <option value="Damaged">Damaged</option>
        </select>
    </div>

    <div>
        <label class="theme-label">Whistle / Alarm Test</label>
        <select name="data[alarm_test]" class="theme-input">
            <option value="Functional">Functional</option>
            <option value="Failed">Failed</option>
        </select>
    </div>

    <div class="col-span-2">
        <label class="theme-label">Remarks</label>
        <textarea name="data[remarks]" rows="3" class="theme-input"></textarea>
    </div>
</div>
