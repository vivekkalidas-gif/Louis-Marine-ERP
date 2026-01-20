<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Equipment Certificate Field Templates
    |--------------------------------------------------------------------------
    |
    | Define category-specific fields for certificate issuance.
    | Each equipment category has its own set of required technical fields.
    |
    */

    'fire_extinguisher' => [
        ['name' => 'capacity_kg', 'label' => 'Capacity (KG)', 'type' => 'number', 'required' => true, 'step' => '0.1'],
        ['name' => 'operation_type', 'label' => 'Operation Type', 'type' => 'select', 'required' => true,
         'options' => [
             'cartridge_operated' => 'Cartridge Operated',
             'stored_pressure' => 'Stored Pressure'
         ]],
        ['name' => 'hydro_test_date', 'label' => 'Hydro Test Date', 'type' => 'date', 'required' => true],
        ['name' => 'actual_weight', 'label' => 'Actual Weight (KG)', 'type' => 'number', 'required' => true, 'step' => '0.01'],
        ['name' => 'tare_weight', 'label' => 'Tare Weight (KG)', 'type' => 'number', 'required' => true, 'step' => '0.01'],
        ['name' => 'pressure_gauge_status', 'label' => 'Pressure Gauge Status', 'type' => 'select', 'required' => true,
         'options' => [
             'green_zone' => 'Green Zone (Good)',
             'yellow_zone' => 'Yellow Zone (Recharge)',
             'red_zone' => 'Red Zone (Empty)'
         ]],
    ],

    'life_raft' => [
        ['name' => 'capacity_persons', 'label' => 'Capacity (Persons)', 'type' => 'number', 'required' => true],
        ['name' => 'raft_type', 'label' => 'Type', 'type' => 'select', 'required' => true,
         'options' => [
             'throw_over' => 'Throw Over',
             'davit_launched' => 'Davit Launched'
         ]],
        ['name' => 'last_inflation_test', 'label' => 'Last Inflation Test Date', 'type' => 'date', 'required' => true],
        ['name' => 'hru_expiry_date', 'label' => 'HRU Expiry Date', 'type' => 'date', 'required' => true],
        ['name' => 'container_condition', 'label' => 'Container Condition', 'type' => 'select', 'required' => true,
         'options' => [
             'good' => 'Good',
             'fair' => 'Fair',
             'poor' => 'Poor - Needs Replacement'
         ]],
    ],

    'scba' => [
        ['name' => 'cylinder_capacity', 'label' => 'Cylinder Capacity', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g., 6L, 9L'],
        ['name' => 'working_pressure', 'label' => 'Working Pressure', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g., 300 bar'],
        ['name' => 'cylinder_hydro_test', 'label' => 'Cylinder Hydro Test Date', 'type' => 'date', 'required' => true],
        ['name' => 'mask_condition', 'label' => 'Mask Condition', 'type' => 'select', 'required' => true,
         'options' => [
             'good' => 'Good',
             'fair' => 'Fair',
             'poor' => 'Poor - Needs Replacement'
         ]],
        ['name' => 'alarm_function', 'label' => 'Alarm Function Test', 'type' => 'select', 'required' => true,
         'options' => [
             'passed' => 'Passed',
             'failed' => 'Failed'
         ]],
    ],

    'eebd' => [
        ['name' => 'duration_minutes', 'label' => 'Duration (Minutes)', 'type' => 'number', 'required' => true],
        ['name' => 'cylinder_pressure', 'label' => 'Cylinder Pressure', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g., 200 bar'],
        ['name' => 'seal_condition', 'label' => 'Seal Condition', 'type' => 'select', 'required' => true,
         'options' => [
             'intact' => 'Intact',
             'damaged' => 'Damaged - Replace'
         ]],
        ['name' => 'expiry_date', 'label' => 'Expiry Date', 'type' => 'date', 'required' => true],
    ],

    'life_jacket' => [
        ['name' => 'jacket_type', 'label' => 'Type', 'type' => 'select', 'required' => true,
         'options' => [
             'adult' => 'Adult',
             'child' => 'Child',
             'infant' => 'Infant'
         ]],
        ['name' => 'approval_standard', 'label' => 'Approval Standard', 'type' => 'select', 'required' => true,
         'options' => [
             'solas' => 'SOLAS',
             'iso' => 'ISO 12402',
             'uscg' => 'USCG'
         ]],
        ['name' => 'buoyancy_rating', 'label' => 'Buoyancy Rating', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g., 150N'],
        ['name' => 'fabric_condition', 'label' => 'Fabric Condition', 'type' => 'select', 'required' => true,
         'options' => [
             'good' => 'Good',
             'fair' => 'Fair - Minor Wear',
             'poor' => 'Poor - Replace'
         ]],
    ],

    'immersion_suit' => [
        ['name' => 'size', 'label' => 'Size', 'type' => 'select', 'required' => true,
         'options' => [
             'S' => 'Small',
             'M' => 'Medium',
             'L' => 'Large',
             'XL' => 'Extra Large',
             'XXL' => '2XL'
         ]],
        ['name' => 'thermal_protection_rating', 'label' => 'Thermal Protection Rating', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g., 6 hours'],
        ['name' => 'zipper_condition', 'label' => 'Zipper Condition', 'type' => 'select', 'required' => true,
         'options' => [
             'good' => 'Good - Watertight',
             'fair' => 'Fair - Minor Issues',
             'poor' => 'Poor - Replace'
         ]],
        ['name' => 'approval_standard', 'label' => 'Approval Standard', 'type' => 'select', 'required' => true,
         'options' => [
             'solas' => 'SOLAS',
             'iso' => 'ISO 15027'
         ]],
    ],

    'co2_fixed_system' => [
        ['name' => 'cylinder_serial_numbers', 'label' => 'Cylinder Serial Numbers', 'type' => 'textarea', 'required' => true, 'placeholder' => 'Enter one per line'],
        ['name' => 'number_of_cylinders', 'label' => 'Number of Cylinders', 'type' => 'number', 'required' => true],
        ['name' => 'hose_condition', 'label' => 'Hose Condition', 'type' => 'select', 'required' => true,
         'options' => [
             'good' => 'Good',
             'fair' => 'Fair',
             'poor' => 'Poor - Replace'
         ]],
        ['name' => 'control_panel_test', 'label' => 'Control Panel Test Result', 'type' => 'select', 'required' => true,
         'options' => [
             'passed' => 'Passed',
             'failed' => 'Failed'
         ]],
        ['name' => 'alarm_test', 'label' => 'Alarm Test Result', 'type' => 'select', 'required' => true,
         'options' => [
             'passed' => 'Passed',
             'failed' => 'Failed'
         ]],
    ],

    'gas_detection_system' => [
        ['name' => 'sensor_type', 'label' => 'Sensor Type', 'type' => 'text', 'required' => true],
        ['name' => 'calibration_date', 'label' => 'Last Calibration Date', 'type' => 'date', 'required' => true],
        ['name' => 'sensor_condition', 'label' => 'Sensor Condition', 'type' => 'select', 'required' => true,
         'options' => [
             'good' => 'Good - Within Range',
             'fair' => 'Fair - Needs Calibration',
             'poor' => 'Poor - Replace Sensor'
         ]],
        ['name' => 'alarm_test', 'label' => 'Alarm Test Result', 'type' => 'select', 'required' => true,
         'options' => [
             'passed' => 'Passed',
             'failed' => 'Failed'
         ]],
    ],
];
