<?php

namespace Database\Seeders;

use App\Models\CertificateTemplate;
use App\Models\CertificateTemplateSection;
use App\Models\CertificateTemplateField;
use App\Models\EquipmentCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CertificateTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, ensure equipment categories exist
        $this->ensureEquipmentCategories();

        // Create default certificate templates
        $this->createFireExtinguisherTemplate();
        $this->createEEBDTemplate();
        $this->createLifeRaftTemplate();
        $this->createSCBATemplate();
        $this->createLifeJacketTemplate();
        $this->createImmersionSuitTemplate();
        $this->createCO2FixedSystemTemplate();

        // Additional Marine Certificate Templates
        $this->createLifeboatTemplate();
        $this->createGasDetectionTemplate();
        $this->createFixedFoamTemplate();
        $this->createFireHoseHydrantTemplate();
        $this->createBreathingAirCompressorTemplate();
        $this->createHydrostaticTestTemplate();
        $this->createSafetyHarnessTemplate();
        $this->createMedicalOxygenTemplate();
        $this->createPyrotechnicsTemplate();
    }

    private function ensureEquipmentCategories()
    {
        $categories = [
            ['name' => 'Fire Extinguisher', 'slug' => 'fire-extinguisher'],
            ['name' => 'EEBD', 'slug' => 'eebd'],
            ['name' => 'Life Raft', 'slug' => 'life-raft'],
            ['name' => 'SCBA', 'slug' => 'scba'],
            ['name' => 'Life Jacket', 'slug' => 'life-jacket'],
            ['name' => 'Immersion Suit', 'slug' => 'immersion-suit'],
            ['name' => 'CO₂ Fixed System', 'slug' => 'co2-fixed-system'],
            ['name' => 'Lifeboat & Rescue Boat', 'slug' => 'lifeboat-rescue-boat'],
            ['name' => 'Gas Detection System', 'slug' => 'gas-detection-system'],
            ['name' => 'Fixed Foam System', 'slug' => 'fixed-foam-system'],
            ['name' => 'Fire Hose & Hydrant', 'slug' => 'fire-hose-hydrant'],
            ['name' => 'Breathing Air Compressor', 'slug' => 'breathing-air-compressor'],
            ['name' => 'Hydrostatic Test', 'slug' => 'hydrostatic-test'],
            ['name' => 'Safety Harness & Fall Arrest', 'slug' => 'safety-harness-fall-arrest'],
            ['name' => 'Medical Oxygen Resuscitator', 'slug' => 'medical-oxygen-resuscitator'],
            ['name' => 'Pyrotechnics & Distress Signals', 'slug' => 'pyrotechnics-distress-signals'],
        ];

        foreach ($categories as $category) {
            EquipmentCategory::firstOrCreate(
                ['slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'description' => $category['name'] . ' equipment category',
                    'is_active' => true,
                ]
            );
        }
    }

    private function getOrCreateTemplate($slug, $name, $title)
    {
        $category = EquipmentCategory::where('slug', $slug)->first();
        if (!$category) return null;

        $template = CertificateTemplate::where('equipment_category_id', $category->id)
            ->where('name', $name)
            ->first();

        if (!$template) {
            $template = CertificateTemplate::create([
                'name' => $name,
                'equipment_category_id' => $category->id,
                'title' => $title,
                'is_active' => true,
                'is_default' => true,
                'header_config' => ['show_logo' => true, 'show_company_name' => true],
                'footer_config' => ['show_qr_code' => true, 'show_signature' => true],
            ]);
        }

        return $template;
    }

    private function createFireExtinguisherTemplate()
    {
        $template = $this->getOrCreateTemplate('fire-extinguisher', 'Fire Extinguisher Certificate', 'Fire Extinguisher Service Certificate');
        if (!$template || $template->sections()->count() > 0) return;

        // Section 1: Equipment Identification
        $section1 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Equipment Identification',
            'description' => 'Basic equipment information',
            'display_order' => 1,
            'is_repeatable' => false,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section1->id, 'equipment_type', 'Equipment Type', 'dropdown', ['Dry Powder', 'CO2', 'Foam', 'Water'], true, 1, 'half');
        $this->createField($section1->id, 'serial_number', 'Serial Number', 'text', null, true, 2, 'half');
        $this->createField($section1->id, 'manufacturer', 'Manufacturer', 'text', null, true, 3, 'half');
        $this->createField($section1->id, 'manufacture_date', 'Manufacture Date', 'date', null, false, 4, 'half');
        $this->createField($section1->id, 'capacity', 'Capacity', 'text', null, true, 5, 'half', 'e.g., 6kg, 9kg');

        // Section 2: Inspection Details
        $section2 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Inspection Details',
            'description' => 'Inspection and condition assessment',
            'display_order' => 2,
            'is_repeatable' => false,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section2->id, 'inspection_date', 'Inspection Date', 'date', null, true, 1, 'half');
        $this->createField($section2->id, 'inspector_name', 'Inspector Name', 'text', null, true, 2, 'half');
        $this->createField($section2->id, 'visual_condition', 'Visual Condition', 'dropdown', ['Good', 'Fair', 'Needs Replacement'], true, 3, 'half');
        $this->createField($section2->id, 'pressure_gauge', 'Pressure Gauge Reading', 'text', null, true, 4, 'half');
        $this->createField($section2->id, 'safety_pin', 'Safety Pin Status', 'dropdown', ['Intact', 'Missing', 'Damaged'], true, 5, 'half');

        // Section 3: Test Results
        $section3 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Test Results',
            'description' => 'Testing and service information',
            'display_order' => 3,
            'is_repeatable' => false,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section3->id, 'hydrostatic_test_date', 'Hydrostatic Test Date', 'date', null, false, 1, 'half');
        $this->createField($section3->id, 'test_result', 'Test Result', 'dropdown', ['Pass', 'Fail'], true, 2, 'half');
        $this->createField($section3->id, 'recharge_date', 'Recharge Date', 'date', null, false, 3, 'half');
        $this->createField($section3->id, 'next_service_due', 'Next Service Due', 'date', null, true, 4, 'half');

        // Section 4: Compliance
        $section4 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Compliance Statement',
            'description' => 'Standards compliance',
            'display_order' => 4,
            'is_repeatable' => false,
            'layout_type' => 'single_column',
        ]);

        $this->createField($section4->id, 'complies_standards', 'Complies with Standards', 'checkbox', null, true, 1, 'full');
        $this->createField($section4->id, 'standard_reference', 'Standard Reference', 'text', null, false, 2, 'full', 'e.g., SOLAS/IMO', 'SOLAS/IMO');
    }

    private function createEEBDTemplate()
    {
        $template = $this->getOrCreateTemplate('eebd', 'EEBD Certificate', 'Emergency Escape Breathing Device (EEBD) Certificate');
        if (!$template || $template->sections()->count() > 0) return;

        // Section 1: Equipment Identification
        $section1 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Equipment Identification',
            'display_order' => 1,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section1->id, 'model_number', 'Model Number', 'text', null, true, 1, 'half');
        $this->createField($section1->id, 'serial_number', 'Serial Number', 'text', null, true, 2, 'half');
        $this->createField($section1->id, 'manufacturer', 'Manufacturer', 'text', null, true, 3, 'half');
        $this->createField($section1->id, 'manufacture_date', 'Manufacture Date', 'date', null, false, 4, 'half');

        // Section 2: Inspection Details
        $section2 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Inspection Details',
            'display_order' => 2,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section2->id, 'inspection_date', 'Inspection Date', 'date', null, true, 1, 'half');
        $this->createField($section2->id, 'hood_condition', 'Hood Condition', 'dropdown', ['Good', 'Damaged'], true, 2, 'half');
        $this->createField($section2->id, 'breathing_bag_condition', 'Breathing Bag Condition', 'dropdown', ['Good', 'Damaged'], true, 3, 'half');
        $this->createField($section2->id, 'pressure_gauge', 'Pressure Gauge Reading', 'text', null, true, 4, 'half');

        // Section 3: Test Results
        $section3 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Test Results',
            'display_order' => 3,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section3->id, 'functional_test', 'Functional Test', 'dropdown', ['Pass', 'Fail'], true, 1, 'half');
        $this->createField($section3->id, 'leak_test', 'Leak Test', 'dropdown', ['Pass', 'Fail'], true, 2, 'half');
        $this->createField($section3->id, 'next_service_due', 'Next Service Due', 'date', null, true, 3, 'half');
    }

    private function createLifeRaftTemplate()
    {
        $template = $this->getOrCreateTemplate('life-raft', 'Life Raft Certificate', 'Life Raft Service Certificate');
        if (!$template || $template->sections()->count() > 0) return;

        // Section 1: Equipment Identification
        $section1 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Equipment Identification',
            'display_order' => 1,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section1->id, 'type', 'Type', 'text', null, true, 1, 'half', 'e.g., Inflatable Life Raft');
        $this->createField($section1->id, 'capacity', 'Capacity (Persons)', 'number', null, true, 2, 'half');
        $this->createField($section1->id, 'serial_number', 'Serial Number', 'text', null, true, 3, 'half');
        $this->createField($section1->id, 'manufacturer', 'Manufacturer', 'text', null, true, 4, 'half');
        $this->createField($section1->id, 'manufacture_date', 'Manufacture Date', 'date', null, false, 5, 'half');

        // Section 2: Service Details
        $section2 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Service Details',
            'display_order' => 2,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section2->id, 'last_service_date', 'Last Service Date', 'date', null, true, 1, 'half');
        $this->createField($section2->id, 'service_station', 'Service Station', 'text', null, true, 2, 'half');
        $this->createField($section2->id, 'service_cert_number', 'Service Certificate Number', 'text', null, false, 3, 'half');
        $this->createField($section2->id, 'next_service_due', 'Next Service Due', 'date', null, true, 4, 'half');

        // Section 3: Inspection Results
        $section3 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Inspection Results',
            'display_order' => 3,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section3->id, 'container_condition', 'Container Condition', 'dropdown', ['Good', 'Damaged'], true, 1, 'half');
        $this->createField($section3->id, 'hydrostatic_release', 'Hydrostatic Release Condition', 'dropdown', ['Good', 'Needs Replacement'], true, 2, 'half');
        $this->createField($section3->id, 'painter_line', 'Painter Line Condition', 'dropdown', ['Good', 'Damaged'], true, 3, 'half');
    }

    private function createSCBATemplate()
    {
        $template = $this->getOrCreateTemplate('scba', 'SCBA Certificate', 'Self-Contained Breathing Apparatus (SCBA) Certificate');
        if (!$template || $template->sections()->count() > 0) return;

        // Section 1: Equipment Identification
        $section1 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Equipment Identification',
            'display_order' => 1,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section1->id, 'model', 'Model', 'text', null, true, 1, 'half');
        $this->createField($section1->id, 'serial_number', 'Serial Number', 'text', null, true, 2, 'half');
        $this->createField($section1->id, 'manufacturer', 'Manufacturer', 'text', null, true, 3, 'half');
        $this->createField($section1->id, 'cylinder_capacity', 'Cylinder Capacity', 'text', null, true, 4, 'half');

        // Section 2: Cylinder Details
        $section2 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Cylinder Details',
            'display_order' => 2,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section2->id, 'cylinder_serial', 'Cylinder Serial Number', 'text', null, true, 1, 'half');
        $this->createField($section2->id, 'working_pressure', 'Working Pressure', 'text', null, true, 2, 'half');
        $this->createField($section2->id, 'last_hydrostatic_test', 'Last Hydrostatic Test', 'date', null, false, 3, 'half');
        $this->createField($section2->id, 'next_test_due', 'Next Test Due', 'date', null, true, 4, 'half');

        // Section 3: Inspection Details
        $section3 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Inspection Details',
            'display_order' => 3,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section3->id, 'face_mask_condition', 'Face Mask Condition', 'dropdown', ['Good', 'Damaged'], true, 1, 'half');
        $this->createField($section3->id, 'regulator_condition', 'Regulator Condition', 'dropdown', ['Good', 'Needs Service'], true, 2, 'half');
        $this->createField($section3->id, 'harness_condition', 'Harness Condition', 'dropdown', ['Good', 'Damaged'], true, 3, 'half');
        $this->createField($section3->id, 'pressure_gauge_accuracy', 'Pressure Gauge Accuracy', 'dropdown', ['Accurate', 'Needs Calibration'], true, 4, 'half');
    }

    private function createLifeJacketTemplate()
    {
        $template = $this->getOrCreateTemplate('life-jacket', 'Life Jacket Certificate', 'Life Jacket Inspection Certificate');
        if (!$template || $template->sections()->count() > 0) return;

        // Section 1: Equipment Identification
        $section1 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Equipment Identification',
            'display_order' => 1,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section1->id, 'type', 'Type', 'dropdown', ['Inflatable', 'Foam'], true, 1, 'half');
        $this->createField($section1->id, 'model', 'Model', 'text', null, true, 2, 'half');
        $this->createField($section1->id, 'serial_number', 'Serial Number', 'text', null, true, 3, 'half');
        $this->createField($section1->id, 'manufacturer', 'Manufacturer', 'text', null, true, 4, 'half');

        // Section 2: Inspection Details
        $section2 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Inspection Details',
            'display_order' => 2,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section2->id, 'fabric_condition', 'Fabric Condition', 'dropdown', ['Good', 'Torn', 'Faded'], true, 1, 'half');
        $this->createField($section2->id, 'inflation_mechanism', 'Inflation Mechanism', 'dropdown', ['Good', 'Needs Replacement'], true, 2, 'half');
        $this->createField($section2->id, 'light_status', 'Light Status', 'dropdown', ['Working', 'Not Working', 'N/A'], true, 3, 'half');
        $this->createField($section2->id, 'whistle_status', 'Whistle Status', 'dropdown', ['Present', 'Missing'], true, 4, 'half');
    }

    private function createImmersionSuitTemplate()
    {
        $template = $this->getOrCreateTemplate('immersion-suit', 'Immersion Suit Certificate', 'Immersion Suit Inspection Certificate');
        if (!$template || $template->sections()->count() > 0) return;

        // Section 1: Equipment Identification
        $section1 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Equipment Identification',
            'display_order' => 1,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section1->id, 'size', 'Size', 'dropdown', ['S', 'M', 'L', 'XL'], true, 1, 'half');
        $this->createField($section1->id, 'serial_number', 'Serial Number', 'text', null, true, 2, 'half');
        $this->createField($section1->id, 'manufacturer', 'Manufacturer', 'text', null, true, 3, 'half');
        $this->createField($section1->id, 'manufacture_date', 'Manufacture Date', 'date', null, false, 4, 'half');

        // Section 2: Inspection Details
        $section2 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Inspection Details',
            'display_order' => 2,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section2->id, 'suit_material_condition', 'Suit Material Condition', 'dropdown', ['Good', 'Damaged'], true, 1, 'half');
        $this->createField($section2->id, 'zipper_condition', 'Zipper Condition', 'dropdown', ['Good', 'Needs Replacement'], true, 2, 'half');
        $this->createField($section2->id, 'seals_condition', 'Seals Condition', 'dropdown', ['Intact', 'Damaged'], true, 3, 'half');
        $this->createField($section2->id, 'gloves_boots_condition', 'Gloves/Boots Condition', 'dropdown', ['Good', 'Damaged'], true, 4, 'half');
    }

    private function createCO2FixedSystemTemplate()
    {
        $template = $this->getOrCreateTemplate('co2-fixed-system', 'CO₂ Fixed System Certificate', 'CO₂ Fixed Fire Extinguishing System Certificate');
        if (!$template || $template->sections()->count() > 0) return;

        // Section 1: System Identification
        $section1 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'System Identification',
            'display_order' => 1,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section1->id, 'system_type', 'System Type', 'text', null, true, 1, 'half');
        $this->createField($section1->id, 'number_of_cylinders', 'Number of Cylinders', 'number', null, true, 2, 'half');
        $this->createField($section1->id, 'total_capacity', 'Total Capacity', 'text', null, true, 3, 'half');
        $this->createField($section1->id, 'installation_location', 'Installation Location', 'text', null, true, 4, 'half');

        // Section 2: Cylinder Details (Repeatable)
        $section2 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Cylinder Details',
            'description' => 'Individual cylinder information',
            'display_order' => 2,
            'is_repeatable' => true,
            'layout_type' => 'table',
        ]);

        $this->createField($section2->id, 'cylinder_serial', 'Cylinder Serial Number', 'text', null, true, 1, 'full');
        $this->createField($section2->id, 'weight', 'Weight', 'text', null, true, 2, 'full');
        $this->createField($section2->id, 'last_recharge_date', 'Last Recharge Date', 'date', null, false, 3, 'full');
        $this->createField($section2->id, 'pressure', 'Pressure', 'text', null, true, 4, 'full');

        // Section 3: System Test Results
        $section3 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'System Test Results',
            'display_order' => 3,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section3->id, 'alarm_test', 'Alarm Test', 'dropdown', ['Pass', 'Fail'], true, 1, 'half');
        $this->createField($section3->id, 'release_mechanism_test', 'Release Mechanism Test', 'dropdown', ['Pass', 'Fail'], true, 2, 'half');
        $this->createField($section3->id, 'piping_inspection', 'Piping Inspection', 'dropdown', ['Good', 'Needs Repair'], true, 3, 'half');
    }

    private function createLifeboatTemplate()
    {
        $template = $this->getOrCreateTemplate('lifeboat-rescue-boat', 'Lifeboat & Rescue Boat Certificate', 'Lifeboat & Rescue Boat Annual / 5-Yearly Inspection Certificate');
        if (!$template || $template->sections()->count() > 0) return;

        // Section 1: Equipment Identification
        $section1 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Equipment Identification',
            'display_order' => 1,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section1->id, 'boat_type', 'Boat Type', 'dropdown', ['Totally Enclosed Lifeboat', 'Partially Enclosed Lifeboat', 'Freefall Lifeboat', 'Rescue Boat', 'Fast Rescue Boat'], true, 1, 'half');
        $this->createField($section1->id, 'serial_number', 'Serial Number', 'text', null, true, 2, 'half');
        $this->createField($section1->id, 'manufacturer', 'Manufacturer', 'text', null, true, 3, 'half');
        $this->createField($section1->id, 'capacity', 'Capacity (Persons)', 'number', null, true, 4, 'half');
        $this->createField($section1->id, 'engine_model_hp', 'Engine Model & HP', 'text', null, true, 5, 'half');
        $this->createField($section1->id, 'release_mechanism', 'Release Hook Type', 'text', null, true, 6, 'half');

        // Section 2: Inspection Details
        $section2 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Inspection Details',
            'display_order' => 2,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section2->id, 'hull_condition', 'Hull & Fender Condition', 'dropdown', ['Good', 'Fair', 'Damaged'], true, 1, 'half');
        $this->createField($section2->id, 'engine_test', 'Engine Starting & Operational Test', 'dropdown', ['Satisfactory', 'Unsatisfactory'], true, 2, 'half');
        $this->createField($section2->id, 'steering_propulsion', 'Steering & Propulsion System', 'dropdown', ['Good', 'Needs Repair'], true, 3, 'half');
        $this->createField($section2->id, 'release_hook_status', 'Release Hook Inspection', 'dropdown', ['Good', 'Overhauled'], true, 4, 'half');

        // Section 3: Test Results
        $section3 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Test Results',
            'display_order' => 3,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section3->id, 'overload_test', 'Dynamic Overload Test', 'dropdown', ['Pass', 'Fail', 'N/A'], true, 1, 'half');
        $this->createField($section3->id, 'release_test', 'Operational Release Test', 'dropdown', ['Pass', 'Fail'], true, 2, 'half');
        $this->createField($section3->id, 'next_service_due', 'Next Service Due', 'date', null, true, 3, 'half');

        // Section 4: Compliance Statement
        $section4 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Compliance Statement',
            'display_order' => 4,
            'layout_type' => 'single_column',
        ]);

        $this->createField($section4->id, 'complies_standards', 'Complies with Standards', 'checkbox', null, true, 1, 'full');
        $this->createField($section4->id, 'standard_reference', 'Standard Reference', 'text', null, false, 2, 'full', 'SOLAS / IMO MSC.1/Circ.1206', 'SOLAS / IMO MSC.1/Circ.1206');
    }

    private function createGasDetectionTemplate()
    {
        $template = $this->getOrCreateTemplate('gas-detection-system', 'Gas Detection System Certificate', 'Gas Detection & Calibration Certificate');
        if (!$template || $template->sections()->count() > 0) return;

        // Section 1: Equipment Identification
        $section1 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Equipment Identification',
            'display_order' => 1,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section1->id, 'detector_type', 'System / Detector Type', 'dropdown', ['Portable Multi-Gas', 'Fixed Detection Panel', 'Single Gas Detector'], true, 1, 'half');
        $this->createField($section1->id, 'model_number', 'Model Number', 'text', null, true, 2, 'half');
        $this->createField($section1->id, 'serial_number', 'Serial Number', 'text', null, true, 3, 'half');
        $this->createField($section1->id, 'manufacturer', 'Manufacturer', 'text', null, true, 4, 'half');
        $this->createField($section1->id, 'monitored_gases', 'Monitored Gases', 'text', null, true, 5, 'half', 'e.g., O2, H2S, CO, LEL');

        // Section 2: Calibration Details
        $section2 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Calibration Details',
            'display_order' => 2,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section2->id, 'zero_calibration', 'Zero Calibration', 'dropdown', ['Passed', 'Failed'], true, 1, 'half');
        $this->createField($section2->id, 'span_gas_concentration', 'Span Gas Concentration', 'text', null, true, 2, 'half');
        $this->createField($section2->id, 'alarm1_setpoint', 'Alarm 1 (A1) Setpoint', 'text', null, true, 3, 'half');
        $this->createField($section2->id, 'alarm2_setpoint', 'Alarm 2 (A2) Setpoint', 'text', null, true, 4, 'half');

        // Section 3: Test Results
        $section3 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Test Results',
            'display_order' => 3,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section3->id, 'bump_test', 'Bump Test Result', 'dropdown', ['Pass', 'Fail'], true, 1, 'half');
        $this->createField($section3->id, 'response_time', 'Response Time (T90) Test', 'text', null, true, 2, 'half');
        $this->createField($section3->id, 'calibration_status', 'Overall Calibration Status', 'dropdown', ['Pass', 'Fail'], true, 3, 'half');
        $this->createField($section3->id, 'next_service_due', 'Next Calibration Due', 'date', null, true, 4, 'half');

        // Section 4: Compliance Statement
        $section4 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Compliance Statement',
            'display_order' => 4,
            'layout_type' => 'single_column',
        ]);

        $this->createField($section4->id, 'complies_standards', 'Complies with Standards', 'checkbox', null, true, 1, 'full');
        $this->createField($section4->id, 'standard_reference', 'Standard Reference', 'text', null, false, 2, 'full', 'SOLAS Reg. VI/3 & MSC.1/Circ.1477', 'SOLAS Reg. VI/3 & MSC.1/Circ.1477');
    }

    private function createFixedFoamTemplate()
    {
        $template = $this->getOrCreateTemplate('fixed-foam-system', 'Fixed Foam System Certificate', 'Fixed Foam Fire Extinguishing System Certificate');
        if (!$template || $template->sections()->count() > 0) return;

        // Section 1: System Identification
        $section1 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'System Identification',
            'display_order' => 1,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section1->id, 'system_type', 'System Type', 'dropdown', ['High Expansion Foam', 'Medium Expansion Foam', 'Low Expansion Foam', 'Deluge Foam System'], true, 1, 'half');
        $this->createField($section1->id, 'protected_area', 'Protected Area / Space', 'text', null, true, 2, 'half');
        $this->createField($section1->id, 'tank_capacity', 'Foam Tank Capacity (Ltr)', 'text', null, true, 3, 'half');
        $this->createField($section1->id, 'foam_type', 'Foam Concentrate Type', 'dropdown', ['AFFF 3%', 'AFFF 6%', 'AR-AFFF 3x3', 'Fluoroprotein'], true, 4, 'half');

        // Section 2: Inspection Details
        $section2 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Inspection Details',
            'display_order' => 2,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section2->id, 'proportioner_condition', 'Proportioner / Inductor Condition', 'dropdown', ['Good', 'Needs Service'], true, 1, 'half');
        $this->createField($section2->id, 'piping_nozzles', 'Piping & Nozzles Integrity', 'dropdown', ['Clear / Intact', 'Obstructed / Damaged'], true, 2, 'half');
        $this->createField($section2->id, 'foam_sample_analysis', 'Foam Sample Lab Analysis Date', 'date', null, false, 3, 'half');
        $this->createField($section2->id, 'control_panel_status', 'Control Panel Status', 'dropdown', ['Operational', 'Faulty'], true, 4, 'half');

        // Section 3: Test Results
        $section3 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Test Results',
            'display_order' => 3,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section3->id, 'expansion_test', 'Foam Expansion & Drainage Test', 'dropdown', ['Pass', 'Fail'], true, 1, 'half');
        $this->createField($section3->id, 'actuation_test', 'Release & Actuation Test', 'dropdown', ['Pass', 'Fail'], true, 2, 'half');
        $this->createField($section3->id, 'next_service_due', 'Next Service Due', 'date', null, true, 3, 'half');

        // Section 4: Compliance Statement
        $section4 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Compliance Statement',
            'display_order' => 4,
            'layout_type' => 'single_column',
        ]);

        $this->createField($section4->id, 'complies_standards', 'Complies with Standards', 'checkbox', null, true, 1, 'full');
        $this->createField($section4->id, 'standard_reference', 'Standard Reference', 'text', null, false, 2, 'full', 'SOLAS Ch. II-2 & IMO MSC.1/Circ.1312', 'SOLAS Ch. II-2 & IMO MSC.1/Circ.1312');
    }

    private function createFireHoseHydrantTemplate()
    {
        $template = $this->getOrCreateTemplate('fire-hose-hydrant', 'Fire Hose & Hydrant Certificate', 'Fire Hose & Hydrant Inspection Certificate');
        if (!$template || $template->sections()->count() > 0) return;

        // Section 1: Equipment Identification
        $section1 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Equipment Identification',
            'display_order' => 1,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section1->id, 'hose_diameter', 'Hose Diameter & Length', 'dropdown', ['1.5" x 15m', '1.5" x 30m', '2.5" x 15m', '2.5" x 30m'], true, 1, 'half');
        $this->createField($section1->id, 'coupling_type', 'Coupling Type', 'dropdown', ['Storz', 'Nakajima', 'BS Instantaneous', 'UNI'], true, 2, 'half');
        $this->createField($section1->id, 'nozzle_type', 'Nozzle Type', 'dropdown', ['Jet/Spray Dual Purpose', 'Fog Nozzle', 'Standard Jet'], true, 3, 'half');
        $this->createField($section1->id, 'serial_number', 'Serial / Tag Number', 'text', null, true, 4, 'half');

        // Section 2: Inspection Details
        $section2 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Inspection Details',
            'display_order' => 2,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section2->id, 'hose_jacket_condition', 'Outer Hose Jacket Condition', 'dropdown', ['Good', 'Abraded', 'Damaged'], true, 1, 'half');
        $this->createField($section2->id, 'liner_condition', 'Inner Rubber Liner Condition', 'dropdown', ['Good', 'Deteriorated'], true, 2, 'half');
        $this->createField($section2->id, 'gasket_seal', 'Coupling Gasket / Seal', 'dropdown', ['Intact', 'Missing', 'Replaced'], true, 3, 'half');
        $this->createField($section2->id, 'hydrant_valve_test', 'Hydrant Valve Operation', 'dropdown', ['Smooth', 'Stiff', 'Leaking'], true, 4, 'half');

        // Section 3: Test Results
        $section3 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Test Results',
            'display_order' => 3,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section3->id, 'pressure_test_bar', 'Hydrostatic Test Pressure (Bar)', 'text', null, true, 1, 'half');
        $this->createField($section3->id, 'leak_test', 'Leak Test', 'dropdown', ['Pass', 'Fail'], true, 2, 'half');
        $this->createField($section3->id, 'next_service_due', 'Next Inspection Due', 'date', null, true, 3, 'half');

        // Section 4: Compliance Statement
        $section4 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Compliance Statement',
            'display_order' => 4,
            'layout_type' => 'single_column',
        ]);

        $this->createField($section4->id, 'complies_standards', 'Complies with Standards', 'checkbox', null, true, 1, 'full');
        $this->createField($section4->id, 'standard_reference', 'Standard Reference', 'text', null, false, 2, 'full', 'SOLAS Reg. II-2/10 & NFPA 1962', 'SOLAS Reg. II-2/10 & NFPA 1962');
    }

    private function createBreathingAirCompressorTemplate()
    {
        $template = $this->getOrCreateTemplate('breathing-air-compressor', 'Breathing Air Compressor Certificate', 'High Pressure Breathing Air Compressor & Air Quality Certificate');
        if (!$template || $template->sections()->count() > 0) return;

        // Section 1: Equipment Identification
        $section1 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Equipment Identification',
            'display_order' => 1,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section1->id, 'compressor_model', 'Compressor Model', 'text', null, true, 1, 'half');
        $this->createField($section1->id, 'serial_number', 'Serial Number', 'text', null, true, 2, 'half');
        $this->createField($section1->id, 'manufacturer', 'Manufacturer', 'text', null, true, 3, 'half');
        $this->createField($section1->id, 'working_pressure', 'Max Working Pressure (Bar)', 'text', null, true, 4, 'half');
        $this->createField($section1->id, 'drive_motor', 'Motor Type & HP', 'text', null, true, 5, 'half');

        // Section 2: Maintenance Details
        $section2 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Maintenance Details',
            'display_order' => 2,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section2->id, 'filter_change_date', 'Filter Cartridge Replacement Date', 'date', null, false, 1, 'half');
        $this->createField($section2->id, 'oil_condition', 'Oil Level & Condition', 'dropdown', ['Good', 'Changed'], true, 2, 'half');
        $this->createField($section2->id, 'safety_valve_test', 'Pressure Relief Valve Test', 'dropdown', ['Pass', 'Fail'], true, 3, 'half');
        $this->createField($section2->id, 'drain_system', 'Auto-Drain System Operation', 'dropdown', ['Operational', 'Needs Maintenance'], true, 4, 'half');

        // Section 3: Air Purity Test Results
        $section3 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Air Purity Test Results',
            'display_order' => 3,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section3->id, 'co_level', 'Carbon Monoxide (CO) Level (ppm)', 'text', null, true, 1, 'half');
        $this->createField($section3->id, 'co2_level', 'Carbon Dioxide (CO2) Level (ppm)', 'text', null, true, 2, 'half');
        $this->createField($section3->id, 'oil_mist', 'Oil Mist Content (mg/m³)', 'text', null, true, 3, 'half');
        $this->createField($section3->id, 'moisture_content', 'Water Vapor Content (mg/m³)', 'text', null, true, 4, 'half');
        $this->createField($section3->id, 'air_quality_result', 'Overall Air Purity Result', 'dropdown', ['Pass', 'Fail'], true, 5, 'half');
        $this->createField($section3->id, 'next_service_due', 'Next Service / Air Test Due', 'date', null, true, 6, 'half');

        // Section 4: Compliance Statement
        $section4 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Compliance Statement',
            'display_order' => 4,
            'layout_type' => 'single_column',
        ]);

        $this->createField($section4->id, 'complies_standards', 'Complies with Standards', 'checkbox', null, true, 1, 'full');
        $this->createField($section4->id, 'standard_reference', 'Standard Reference', 'text', null, false, 2, 'full', 'EN 12021 & BS 4275 Breathing Air Standard', 'EN 12021 & BS 4275 Breathing Air Standard');
    }

    private function createHydrostaticTestTemplate()
    {
        $template = $this->getOrCreateTemplate('hydrostatic-test', 'Hydrostatic Test Certificate', 'Pressure Vessel & Gas Cylinder Hydrostatic Test Certificate');
        if (!$template || $template->sections()->count() > 0) return;

        // Section 1: Cylinder Identification
        $section1 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Cylinder Identification',
            'display_order' => 1,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section1->id, 'cylinder_serial', 'Cylinder Serial Number', 'text', null, true, 1, 'half');
        $this->createField($section1->id, 'cylinder_contents', 'Gas Type / Contents', 'dropdown', ['CO2', 'Compressed Air', 'Nitrogen', 'Halon/Clean Agent', 'Oxygen'], true, 2, 'half');
        $this->createField($section1->id, 'water_capacity', 'Water Capacity (Liters)', 'text', null, true, 3, 'half');
        $this->createField($section1->id, 'tare_weight', 'Tare Weight (Kg)', 'text', null, true, 4, 'half');
        $this->createField($section1->id, 'test_pressure', 'Test Pressure (Bar)', 'text', null, true, 5, 'half');

        // Section 2: Test Parameters
        $section2 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Test Parameters',
            'display_order' => 2,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section2->id, 'test_medium', 'Hydrostatic Test Medium', 'text', null, true, 1, 'half', null, 'Water');
        $this->createField($section2->id, 'applied_pressure', 'Applied Pressure (Bar)', 'text', null, true, 2, 'half');
        $this->createField($section2->id, 'holding_time', 'Hold Duration (Minutes)', 'text', null, true, 3, 'half', null, '1 min');
        $this->createField($section2->id, 'expansion_method', 'Volumetric Expansion Method', 'dropdown', ['Water Jacket Method', 'Direct Expansion Method'], true, 4, 'half');

        // Section 3: Test Results
        $section3 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Test Results',
            'display_order' => 3,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section3->id, 'permanent_expansion', 'Permanent Volumetric Expansion (%)', 'text', null, true, 1, 'half');
        $this->createField($section3->id, 'test_result', 'Hydrostatic Test Result', 'dropdown', ['Pass', 'Fail'], true, 2, 'half');
        $this->createField($section3->id, 'stamp_date', 'Hydrostatic Stamp Date', 'date', null, true, 3, 'half');
        $this->createField($section3->id, 'next_test_due', 'Next Hydrostatic Test Due', 'date', null, true, 4, 'half');

        // Section 4: Compliance Statement
        $section4 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Compliance Statement',
            'display_order' => 4,
            'layout_type' => 'single_column',
        ]);

        $this->createField($section4->id, 'complies_standards', 'Complies with Standards', 'checkbox', null, true, 1, 'full');
        $this->createField($section4->id, 'standard_reference', 'Standard Reference', 'text', null, false, 2, 'full', 'DOT / CGA C-6 & ISO 18119', 'DOT / CGA C-6 & ISO 18119');
    }

    private function createSafetyHarnessTemplate()
    {
        $template = $this->getOrCreateTemplate('safety-harness-fall-arrest', 'Safety Harness Certificate', 'Safety Harness & Fall Arrest System Inspection Certificate');
        if (!$template || $template->sections()->count() > 0) return;

        // Section 1: Equipment Identification
        $section1 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Equipment Identification',
            'display_order' => 1,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section1->id, 'harness_model', 'Model', 'text', null, true, 1, 'half');
        $this->createField($section1->id, 'serial_number', 'Serial Number', 'text', null, true, 2, 'half');
        $this->createField($section1->id, 'manufacturer', 'Manufacturer', 'text', null, true, 3, 'half');
        $this->createField($section1->id, 'lanyard_type', 'Lanyard / Absorber Type', 'dropdown', ['Single Lanyard w/ Absorber', 'Twin Lanyard w/ Absorber', 'Self-Retracting Lifeline'], true, 4, 'half');
        $this->createField($section1->id, 'manufacture_date', 'Manufacture Date', 'date', null, false, 5, 'half');

        // Section 2: Inspection Details
        $section2 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Inspection Details',
            'display_order' => 2,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section2->id, 'webbing_condition', 'Webbing & Stitching Condition', 'dropdown', ['Good', 'Frayed / Damaged'], true, 1, 'half');
        $this->createField($section2->id, 'dring_buckles', 'D-Rings & Metal Buckles', 'dropdown', ['Good', 'Corroded / Distortion'], true, 2, 'half');
        $this->createField($section2->id, 'energy_absorber', 'Shock / Energy Absorber Status', 'dropdown', ['Intact / Unused', 'Deployed / Damaged'], true, 3, 'half');
        $this->createField($section2->id, 'snap_hooks', 'Snap Hooks & Karabiners', 'dropdown', ['Good', 'Faulty Gate'], true, 4, 'half');

        // Section 3: Test Results
        $section3 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Test Results',
            'display_order' => 3,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section3->id, 'functional_inspection', 'Functional & Tactile Inspection', 'dropdown', ['Pass', 'Fail'], true, 1, 'half');
        $this->createField($section3->id, 'next_service_due', 'Next Inspection Due', 'date', null, true, 2, 'half');

        // Section 4: Compliance Statement
        $section4 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Compliance Statement',
            'display_order' => 4,
            'layout_type' => 'single_column',
        ]);

        $this->createField($section4->id, 'complies_standards', 'Complies with Standards', 'checkbox', null, true, 1, 'full');
        $this->createField($section4->id, 'standard_reference', 'Standard Reference', 'text', null, false, 2, 'full', 'EN 361 / EN 355 / OSHA 1926', 'EN 361 / EN 355 / OSHA 1926');
    }

    private function createMedicalOxygenTemplate()
    {
        $template = $this->getOrCreateTemplate('medical-oxygen-resuscitator', 'Medical Oxygen Resuscitator Certificate', 'Medical Oxygen Resuscitator & Cylinder Certificate');
        if (!$template || $template->sections()->count() > 0) return;

        // Section 1: Equipment Identification
        $section1 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Equipment Identification',
            'display_order' => 1,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section1->id, 'resuscitator_model', 'Resuscitator Model', 'text', null, true, 1, 'half');
        $this->createField($section1->id, 'serial_number', 'Serial Number', 'text', null, true, 2, 'half');
        $this->createField($section1->id, 'manufacturer', 'Manufacturer', 'text', null, true, 3, 'half');
        $this->createField($section1->id, 'cylinder_serial', 'Cylinder Serial Number', 'text', null, true, 4, 'half');
        $this->createField($section1->id, 'cylinder_capacity', 'Cylinder Capacity (Ltr)', 'text', null, true, 5, 'half');

        // Section 2: Component Inspection
        $section2 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Component Inspection',
            'display_order' => 2,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section2->id, 'regulator_condition', 'Pressure Regulator & Flowmeter', 'dropdown', ['Good', 'Faulty'], true, 1, 'half');
        $this->createField($section2->id, 'resuscitation_bag_mask', 'Resuscitation Bag & Masks', 'dropdown', ['Clean & Intact', 'Damaged'], true, 2, 'half');
        $this->createField($section2->id, 'suction_unit', 'Suction Unit / Aspirator', 'dropdown', ['Operational', 'N/A', 'Faulty'], true, 3, 'half');
        $this->createField($section2->id, 'oxygen_pressure', 'Current Oxygen Pressure (Bar)', 'text', null, true, 4, 'half');

        // Section 3: Test Results
        $section3 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Test Results',
            'display_order' => 3,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section3->id, 'flow_test', 'Flow Rate & Demand Valve Test', 'dropdown', ['Pass', 'Fail'], true, 1, 'half');
        $this->createField($section3->id, 'relief_valve_test', 'Safety Relief Valve Test', 'dropdown', ['Pass', 'Fail'], true, 2, 'half');
        $this->createField($section3->id, 'next_service_due', 'Next Inspection Due', 'date', null, true, 3, 'half');

        // Section 4: Compliance Statement
        $section4 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Compliance Statement',
            'display_order' => 4,
            'layout_type' => 'single_column',
        ]);

        $this->createField($section4->id, 'complies_standards', 'Complies with Standards', 'checkbox', null, true, 1, 'full');
        $this->createField($section4->id, 'standard_reference', 'Standard Reference', 'text', null, false, 2, 'full', 'SOLAS Reg. III/MFA & ISO 10651', 'SOLAS Reg. III/MFA & ISO 10651');
    }

    private function createPyrotechnicsTemplate()
    {
        $template = $this->getOrCreateTemplate('pyrotechnics-distress-signals', 'Pyrotechnics & Distress Signals Certificate', 'Pyrotechnics & Marine Distress Signals Certificate');
        if (!$template || $template->sections()->count() > 0) return;

        // Section 1: Inventory Particulars
        $section1 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Inventory Particulars',
            'display_order' => 1,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section1->id, 'parachute_flares_qty', 'Rocket Parachute Flares (Qty)', 'number', null, true, 1, 'half');
        $this->createField($section1->id, 'hand_flares_qty', 'Red Hand Flares (Qty)', 'number', null, true, 2, 'half');
        $this->createField($section1->id, 'buoyant_smoke_qty', 'Buoyant Smoke Signals (Qty)', 'number', null, true, 3, 'half');
        $this->createField($section1->id, 'manufacturer_brand', 'Manufacturer / Brand', 'text', null, true, 4, 'half');

        // Section 2: Inspection Details
        $section2 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Inspection Details',
            'display_order' => 2,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section2->id, 'storage_container', 'Waterproof Container Condition', 'dropdown', ['Good', 'Damaged'], true, 1, 'half');
        $this->createField($section2->id, 'physical_condition', 'Pyrotechnic Casing Condition', 'dropdown', ['Good', 'Moisture Exposed'], true, 2, 'half');
        $this->createField($section2->id, 'earliest_expiry_date', 'Earliest Expiry Date', 'date', null, true, 3, 'half');

        // Section 3: Test Results
        $section3 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Test Results',
            'display_order' => 3,
            'layout_type' => 'two_column',
        ]);

        $this->createField($section3->id, 'overall_status', 'Overall Readiness Status', 'dropdown', ['Pass', 'Expired / Replace'], true, 1, 'half');
        $this->createField($section3->id, 'next_service_due', 'Next Annual Inspection Due', 'date', null, true, 2, 'half');

        // Section 4: Compliance Statement
        $section4 = CertificateTemplateSection::create([
            'certificate_template_id' => $template->id,
            'name' => 'Compliance Statement',
            'display_order' => 4,
            'layout_type' => 'single_column',
        ]);

        $this->createField($section4->id, 'complies_standards', 'Complies with Standards', 'checkbox', null, true, 1, 'full');
        $this->createField($section4->id, 'standard_reference', 'Standard Reference', 'text', null, false, 2, 'full', 'SOLAS Reg. III/6 & LSA Code', 'SOLAS Reg. III/6 & LSA Code');
    }

    private function createField($sectionId, $fieldName, $fieldLabel, $fieldType, $options = null, $isRequired = false, $order = 0, $width = 'full', $placeholder = null, $defaultValue = null)
    {
        CertificateTemplateField::firstOrCreate(
            ['section_id' => $sectionId, 'field_name' => $fieldName],
            [
                'field_label' => $fieldLabel,
                'field_type' => $fieldType,
                'field_options' => $options ? ['options' => $options] : null,
                'is_required' => $isRequired,
                'validation_rules' => null,
                'default_value' => $defaultValue,
                'placeholder' => $placeholder,
                'help_text' => null,
                'display_order' => $order,
                'width' => $width,
            ]
        );
    }
}
