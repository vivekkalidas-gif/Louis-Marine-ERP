<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EquipmentCategory;
use App\Models\EquipmentItem;
use App\Models\EquipmentAttributeTemplate;

class EquipmentMasterSeeder extends Seeder
{
    public function run()
    {
        // 1. FIRE EXTINGUISHER
        $fireCategory = EquipmentCategory::firstOrCreate(
            ['slug' => 'fire-extinguisher'],
            [
                'name' => 'Fire Extinguisher',
                'description' => 'Portable fire fighting equipment',
                'icon' => 'fire',
                'display_order' => 1,
                'is_active' => true
            ]
        );

        // Fire Extinguisher Attribute Templates
        if ($fireCategory->attributeTemplates()->count() == 0) {
            EquipmentAttributeTemplate::insert([
                ['category_id' => $fireCategory->id, 'attribute_key' => 'capacity_kg', 'attribute_label' => 'Capacity (KG)', 'attribute_type' => 'number', 'options' => null, 'is_required' => true, 'display_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['category_id' => $fireCategory->id, 'attribute_key' => 'extinguisher_type', 'attribute_label' => 'Extinguisher Type', 'attribute_type' => 'select', 'options' => json_encode(['CO2', 'DCP', 'Foam', 'Water', 'Clean Agent', 'Wet Chemical']), 'is_required' => true, 'display_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['category_id' => $fireCategory->id, 'attribute_key' => 'operation_type', 'attribute_label' => 'Operation Type', 'attribute_type' => 'select', 'options' => json_encode(['Cartridge Operated', 'Stored Pressure']), 'is_required' => false, 'display_order' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['category_id' => $fireCategory->id, 'attribute_key' => 'working_pressure_bar', 'attribute_label' => 'Working Pressure (Bar)', 'attribute_type' => 'number', 'options' => null, 'is_required' => false, 'display_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // Fire Extinguisher Items
        if ($fireCategory->items()->count() == 0) {
            EquipmentItem::insert([
                ['category_id' => $fireCategory->id, 'name' => 'CO2 Fire Extinguisher 2kg', 'code' => 'FE-CO2-2KG', 'brand' => null, 'model' => null, 'description' => 'Carbon Dioxide Fire Extinguisher', 'attributes' => json_encode(['capacity_kg' => 2, 'extinguisher_type' => 'CO2', 'operation_type' => 'Stored Pressure']), 'unit' => 'Nos', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['category_id' => $fireCategory->id, 'name' => 'CO2 Fire Extinguisher 5kg', 'code' => 'FE-CO2-5KG', 'brand' => null, 'model' => null, 'description' => 'Carbon Dioxide Fire Extinguisher', 'attributes' => json_encode(['capacity_kg' => 5, 'extinguisher_type' => 'CO2', 'operation_type' => 'Stored Pressure']), 'unit' => 'Nos', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['category_id' => $fireCategory->id, 'name' => 'DCP Fire Extinguisher 9kg', 'code' => 'FE-DCP-9KG', 'brand' => null, 'model' => null, 'description' => 'Dry Chemical Powder Fire Extinguisher', 'attributes' => json_encode(['capacity_kg' => 9, 'extinguisher_type' => 'DCP', 'operation_type' => 'Stored Pressure']), 'unit' => 'Nos', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // 2. BREATHING APPARATUS (SCBA)
        $scbaCategory = EquipmentCategory::firstOrCreate(
            ['slug' => 'breathing-apparatus-scba'],
            [
                'name' => 'Breathing Apparatus (SCBA)',
                'description' => 'Self Contained Breathing Apparatus',
                'icon' => 'wind',
                'display_order' => 2,
                'is_active' => true
            ]
        );

        if ($scbaCategory->attributeTemplates()->count() == 0) {
            EquipmentAttributeTemplate::insert([
                ['category_id' => $scbaCategory->id, 'attribute_key' => 'duration_minutes', 'attribute_label' => 'Duration (Minutes)', 'attribute_type' => 'number', 'options' => null, 'is_required' => true, 'display_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['category_id' => $scbaCategory->id, 'attribute_key' => 'cylinder_capacity', 'attribute_label' => 'Cylinder Capacity (Ltrs)', 'attribute_type' => 'number', 'options' => null, 'is_required' => false, 'display_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['category_id' => $scbaCategory->id, 'attribute_key' => 'working_pressure_bar', 'attribute_label' => 'Working Pressure (Bar)', 'attribute_type' => 'number', 'options' => null, 'is_required' => false, 'display_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if ($scbaCategory->items()->count() == 0) {
            EquipmentItem::insert([
                ['category_id' => $scbaCategory->id, 'name' => 'SCBA Set 30 Minutes', 'code' => 'SCBA-30MIN', 'brand' => null, 'model' => null, 'description' => 'Self Contained Breathing Apparatus', 'attributes' => json_encode(['duration_minutes' => 30, 'cylinder_capacity' => 6, 'working_pressure_bar' => 300]), 'unit' => 'Sets', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['category_id' => $scbaCategory->id, 'name' => 'SCBA Set 45 Minutes', 'code' => 'SCBA-45MIN', 'brand' => null, 'model' => null, 'description' => 'Self Contained Breathing Apparatus', 'attributes' => json_encode(['duration_minutes' => 45, 'cylinder_capacity' => 9, 'working_pressure_bar' => 300]), 'unit' => 'Sets', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // 3. LIFE RAFT
        $lifeRaftCategory = EquipmentCategory::firstOrCreate(
            ['slug' => 'life-raft'],
            [
                'name' => 'Life Raft',
                'description' => 'Inflatable life saving equipment',
                'icon' => 'life-ring',
                'display_order' => 3,
                'is_active' => true
            ]
        );

        if ($lifeRaftCategory->attributeTemplates()->count() == 0) {
            EquipmentAttributeTemplate::insert([
                ['category_id' => $lifeRaftCategory->id, 'attribute_key' => 'capacity_persons', 'attribute_label' => 'Capacity (Persons)', 'attribute_type' => 'number', 'options' => null, 'is_required' => true, 'display_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['category_id' => $lifeRaftCategory->id, 'attribute_key' => 'type', 'attribute_label' => 'Type', 'attribute_type' => 'select', 'options' => json_encode(['Throw Over', 'Davit Launched']), 'is_required' => true, 'display_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['category_id' => $lifeRaftCategory->id, 'attribute_key' => 'service_interval_months', 'attribute_label' => 'Service Interval (Months)', 'attribute_type' => 'number', 'options' => null, 'is_required' => false, 'display_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if ($lifeRaftCategory->items()->count() == 0) {
            EquipmentItem::insert([
                ['category_id' => $lifeRaftCategory->id, 'name' => 'Life Raft 6 Person Throw Over', 'code' => 'LR-6P-TO', 'brand' => null, 'model' => null, 'description' => 'Inflatable Life Raft', 'attributes' => json_encode(['capacity_persons' => 6, 'type' => 'Throw Over', 'service_interval_months' => 12]), 'unit' => 'Nos', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['category_id' => $lifeRaftCategory->id, 'name' => 'Life Raft 10 Person Davit', 'code' => 'LR-10P-DL', 'brand' => null, 'model' => null, 'description' => 'Inflatable Life Raft', 'attributes' => json_encode(['capacity_persons' => 10, 'type' => 'Davit Launched', 'service_interval_months' => 12]), 'unit' => 'Nos', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // 4. LIFE JACKET
        $lifeJacketCategory = EquipmentCategory::firstOrCreate(
            ['slug' => 'life-jacket'],
            [
                'name' => 'Life Jacket',
                'description' => 'Personal flotation devices',
                'icon' => 'shield',
                'display_order' => 4,
                'is_active' => true
            ]
        );

        if ($lifeJacketCategory->attributeTemplates()->count() == 0) {
            EquipmentAttributeTemplate::insert([
                ['category_id' => $lifeJacketCategory->id, 'attribute_key' => 'type', 'attribute_label' => 'Type', 'attribute_type' => 'select', 'options' => json_encode(['Adult', 'Child', 'Infant']), 'is_required' => true, 'display_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['category_id' => $lifeJacketCategory->id, 'attribute_key' => 'approval_standard', 'attribute_label' => 'Approval Standard', 'attribute_type' => 'select', 'options' => json_encode(['SOLAS', 'ISO', 'CE']), 'is_required' => false, 'display_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['category_id' => $lifeJacketCategory->id, 'attribute_key' => 'buoyancy_rating', 'attribute_label' => 'Buoyancy Rating', 'attribute_type' => 'text', 'options' => null, 'is_required' => false, 'display_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if ($lifeJacketCategory->items()->count() == 0) {
            EquipmentItem::insert([
                ['category_id' => $lifeJacketCategory->id, 'name' => 'Life Jacket Adult SOLAS', 'code' => 'LJ-ADULT-SOLAS', 'brand' => null, 'model' => null, 'description' => 'Adult Life Jacket', 'attributes' => json_encode(['type' => 'Adult', 'approval_standard' => 'SOLAS', 'buoyancy_rating' => '150N']), 'unit' => 'Nos', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // 5. IMMERSION SUIT
        $immersionSuitCategory = EquipmentCategory::firstOrCreate(
            ['slug' => 'immersion-suit'],
            [
                'name' => 'Immersion Suit',
                'description' => 'Thermal protection suits',
                'icon' => 'user',
                'display_order' => 5,
                'is_active' => true
            ]
        );

        if ($immersionSuitCategory->attributeTemplates()->count() == 0) {
            EquipmentAttributeTemplate::insert([
                ['category_id' => $immersionSuitCategory->id, 'attribute_key' => 'size', 'attribute_label' => 'Size', 'attribute_type' => 'select', 'options' => json_encode(['S', 'M', 'L', 'XL', 'XXL']), 'is_required' => true, 'display_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['category_id' => $immersionSuitCategory->id, 'attribute_key' => 'thermal_protection_rating', 'attribute_label' => 'Thermal Protection Rating', 'attribute_type' => 'text', 'options' => null, 'is_required' => false, 'display_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if ($immersionSuitCategory->items()->count() == 0) {
            EquipmentItem::insert([
                ['category_id' => $immersionSuitCategory->id, 'name' => 'Immersion Suit Large', 'code' => 'IS-L', 'brand' => null, 'model' => null, 'description' => 'Thermal Protection Suit', 'attributes' => json_encode(['size' => 'L', 'thermal_protection_rating' => '6 Hours']), 'unit' => 'Nos', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // 6. EEBD
        $eebdCategory = EquipmentCategory::firstOrCreate(
            ['slug' => 'eebd'],
            [
                'name' => 'EEBD',
                'description' => 'Emergency Escape Breathing Device',
                'icon' => 'alert-triangle',
                'display_order' => 6,
                'is_active' => true
            ]
        );

        if ($eebdCategory->attributeTemplates()->count() == 0) {
            EquipmentAttributeTemplate::insert([
                ['category_id' => $eebdCategory->id, 'attribute_key' => 'duration_minutes', 'attribute_label' => 'Duration (Minutes)', 'attribute_type' => 'number', 'options' => null, 'is_required' => true, 'display_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if ($eebdCategory->items()->count() == 0) {
            EquipmentItem::insert([
                ['category_id' => $eebdCategory->id, 'name' => 'EEBD 10 Minutes', 'code' => 'EEBD-10MIN', 'brand' => null, 'model' => null, 'description' => 'Emergency Escape Breathing Device', 'attributes' => json_encode(['duration_minutes' => 10]), 'unit' => 'Nos', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['category_id' => $eebdCategory->id, 'name' => 'EEBD 15 Minutes', 'code' => 'EEBD-15MIN', 'brand' => null, 'model' => null, 'description' => 'Emergency Escape Breathing Device', 'attributes' => json_encode(['duration_minutes' => 15]), 'unit' => 'Nos', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // 7. CO2 FIXED SYSTEM
        $co2Category = EquipmentCategory::firstOrCreate(
            ['slug' => 'co2-fixed-system'],
            [
                'name' => 'CO₂ Fixed System',
                'description' => 'Fixed CO2 fire suppression system',
                'icon' => 'database',
                'display_order' => 7,
                'is_active' => true
            ]
        );

        if ($co2Category->items()->count() == 0) {
            EquipmentItem::insert([
                ['category_id' => $co2Category->id, 'name' => 'CO2 Main Cylinder Bank', 'code' => 'CO2-MAIN-BANK', 'brand' => null, 'model' => null, 'description' => 'Main CO2 Cylinder Bank', 'attributes' => json_encode([]), 'unit' => 'Sets', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['category_id' => $co2Category->id, 'name' => 'CO2 Pilot Cylinder', 'code' => 'CO2-PILOT', 'brand' => null, 'model' => null, 'description' => 'Pilot Cylinder for Activation', 'attributes' => json_encode([]), 'unit' => 'Nos', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // 8. GAS DETECTION SYSTEM
        $gasDetectionCategory = EquipmentCategory::firstOrCreate(
            ['slug' => 'gas-detection-system'],
            [
                'name' => 'Gas Detection System',
                'description' => 'Gas detection and monitoring equipment',
                'icon' => 'activity',
                'display_order' => 8,
                'is_active' => true
            ]
        );

        if ($gasDetectionCategory->items()->count() == 0) {
            EquipmentItem::insert([
                ['category_id' => $gasDetectionCategory->id, 'name' => 'Portable Gas Detector', 'code' => 'GD-PORTABLE', 'brand' => null, 'model' => null, 'description' => 'Portable Multi-Gas Detector', 'attributes' => json_encode([]), 'unit' => 'Nos', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['category_id' => $gasDetectionCategory->id, 'name' => 'Fixed Gas Detector', 'code' => 'GD-FIXED', 'brand' => null, 'model' => null, 'description' => 'Fixed Gas Detection Sensor', 'attributes' => json_encode([]), 'unit' => 'Nos', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }
}
