<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="col-span-2">
        <h4 class="text-lg font-semibold text-marine-900 dark:text-marine-100 mb-4 border-b pb-2">Gas Detection System</h4>
    </div>

    <div>
        <label class="theme-label">Detector Type</label>
        <select name="data[detector_type]" class="theme-input">
            <option value="Portable">Portable Multi-Gas Detector</option>
            <option value="Fixed">Fixed Sensor System</option>
        </select>
    </div>

    <div>
        <label class="theme-label">Location / Serial</label>
        <input type="text" name="data[location]" class="theme-input" placeholder="Serial Number or Dedicated Location">
    </div>

    <div>
        <label class="theme-label">Sensor Types</label>
        <input type="text" name="data[sensor_types]" class="theme-input" placeholder="e.g. H2S, O2, LEL, CO">
    </div>

    <div>
        <label class="theme-label">Sensor Expiry Date</label>
        <input type="date" name="data[sensor_expiry]" class="theme-input">
    </div>

    <div>
        <label class="theme-label">Calibration Date</label>
        <input type="date" name="data[calibration_date]" class="theme-input" value="{{ date('Y-m-d') }}">
    </div>

    <!-- Tests -->
    <div class="col-span-2 mt-4">
        <h4 class="text-lg font-semibold text-marine-900 dark:text-marine-100 mb-4 border-b pb-2">Functional Tests</h4>
    </div>

    <div>
        <label class="theme-label">Alarm Sound/Light</label>
        <select name="data[alarm_test]" class="theme-input">
            <option value="Pass">Pass</option>
            <option value="Fail">Fail</option>
        </select>
    </div>

    <div>
        <label class="theme-label">Display Unit</label>
        <select name="data[display_test]" class="theme-input">
            <option value="Pass">Clear / Readable</option>
            <option value="Fail">Faulty / Unreadable</option>
        </select>
    </div>

    <div class="col-span-2">
        <label class="theme-label">Remarks</label>
        <textarea name="data[remarks]" rows="3" class="theme-input"></textarea>
    </div>
</div>
