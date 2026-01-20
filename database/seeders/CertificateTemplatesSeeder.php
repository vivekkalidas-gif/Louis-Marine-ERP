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

        // Create all 7 default certificate templates
        $this->createFireExtinguisherTemplate();
        $this->createEEBDTemplate();
        $this->createLifeRaftTemplate();
        $this->createSCBATemplate();
        $this->createLifeJacketTemplate();
        $this->createImmersionSuitTemplate();
        $this->createCO2FixedSystemTemplate();
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

    private function createFireExtinguisherTemplate()
    {
        $category = EquipmentCategory::where('slug', 'fire-extinguisher')->first();
        
        $template = CertificateTemplate::create([
            'name' => 'Fire Extinguisher Certificate',
            'equipment_category_id' => $category->id,
            'title' => 'Fire Extinguisher Service Certificate',
            'is_active' => true,
            'is_default' => true,
            'header_config' => [
                'show_logo' => true,
                'show_company_name' => true,
            ],
            'footer_config' => [
                'show_qr_code' => true,
                'show_signature' => true,
            ],
        ]);

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
        $category = EquipmentCategory::where('slug', 'eebd')->first();
        
        $template = CertificateTemplate::create([
            'name' => 'EEBD Certificate',
            'equipment_category_id' => $category->id,
            'title' => 'Emergency Escape Breathing Device (EEBD) Certificate',
            'is_active' => true,
            'is_default' => true,
            'header_config' => ['show_logo' => true, 'show_company_name' => true],
            'footer_config' => ['show_qr_code' => true, 'show_signature' => true],
        ]);

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
        $category = EquipmentCategory::where('slug', 'life-raft')->first();
        
        $template = CertificateTemplate::create([
            'name' => 'Life Raft Certificate',
            'equipment_category_id' => $category->id,
            'title' => 'Life Raft Service Certificate',
            'is_active' => true,
            'is_default' => true,
            'header_config' => ['show_logo' => true, 'show_company_name' => true],
            'footer_config' => ['show_qr_code' => true, 'show_signature' => true],
        ]);

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
        $category = EquipmentCategory::where('slug', 'scba')->first();
        
        $template = CertificateTemplate::create([
            'name' => 'SCBA Certificate',
            'equipment_category_id' => $category->id,
            'title' => 'Self-Contained Breathing Apparatus (SCBA) Certificate',
            'is_active' => true,
            'is_default' => true,
            'header_config' => ['show_logo' => true, 'show_company_name' => true],
            'footer_config' => ['show_qr_code' => true, 'show_signature' => true],
        ]);

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
        $category = EquipmentCategory::where('slug', 'life-jacket')->first();
        
        $template = CertificateTemplate::create([
            'name' => 'Life Jacket Certificate',
            'equipment_category_id' => $category->id,
            'title' => 'Life Jacket Inspection Certificate',
            'is_active' => true,
            'is_default' => true,
            'header_config' => ['show_logo' => true, 'show_company_name' => true],
            'footer_config' => ['show_qr_code' => true, 'show_signature' => true],
        ]);

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
        $category = EquipmentCategory::where('slug', 'immersion-suit')->first();
        
        $template = CertificateTemplate::create([
            'name' => 'Immersion Suit Certificate',
            'equipment_category_id' => $category->id,
            'title' => 'Immersion Suit Inspection Certificate',
            'is_active' => true,
            'is_default' => true,
            'header_config' => ['show_logo' => true, 'show_company_name' => true],
            'footer_config' => ['show_qr_code' => true, 'show_signature' => true],
        ]);

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
        $category = EquipmentCategory::where('slug', 'co2-fixed-system')->first();
        
        $template = CertificateTemplate::create([
            'name' => 'CO₂ Fixed System Certificate',
            'equipment_category_id' => $category->id,
            'title' => 'CO₂ Fixed Fire Extinguishing System Certificate',
            'is_active' => true,
            'is_default' => true,
            'header_config' => ['show_logo' => true, 'show_company_name' => true],
            'footer_config' => ['show_qr_code' => true, 'show_signature' => true],
        ]);

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

    private function createField($sectionId, $fieldName, $fieldLabel, $fieldType, $options = null, $isRequired = false, $order = 0, $width = 'full', $placeholder = null, $defaultValue = null)
    {
        CertificateTemplateField::create([
            'section_id' => $sectionId,
            'field_name' => $fieldName,
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
        ]);
    }
}
