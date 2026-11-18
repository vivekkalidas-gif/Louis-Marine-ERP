<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateTemplate;
use App\Models\EquipmentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CertificateTemplateController extends Controller
{
    /**
     * Display a listing of certificate templates
     */
    public function index()
    {
        $templates = CertificateTemplate::with(['equipmentCategory', 'sections.fields', 'creator'])
            ->withCount(['sections', 'certificates'])
            ->latest()
            ->get();

        $categories = EquipmentCategory::where('is_active', true)->orderBy('name')->get();

        return view('admin.certificate-templates.index', compact('templates', 'categories'));
    }

    /**
     * Show the form for creating a new template
     */
    public function create()
    {
        $categories = EquipmentCategory::where('is_active', true)->orderBy('name')->get();
        return view('admin.certificate-templates.create', compact('categories'));
    }

    /**
     * Store a newly created template
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'equipment_category_id' => 'required|exists:equipment_categories,id',
            'title' => 'required|string|max:255',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['header_config'] = [
            'show_logo' => true,
            'show_company_name' => true,
        ];
        $validated['footer_config'] = [
            'show_qr_code' => true,
            'show_signature' => true,
        ];

        $template = CertificateTemplate::create($validated);

        // Log creation
        $template->logChange('created');

        return redirect()
            ->route('admin.certificate-templates.edit', $template)
            ->with('success', 'Template created successfully. Now add sections and fields.');
    }

    /**
     * Display the specified template
     */
    public function show(CertificateTemplate $certificateTemplate)
    {
        $certificateTemplate->load(['equipmentCategory', 'sections.fields', 'creator']);
        return view('admin.certificate-templates.show', compact('certificateTemplate'));
    }

    /**
     * Show the form for editing the template
     */
    public function edit(CertificateTemplate $certificateTemplate)
    {
        $certificateTemplate->load(['sections.fields', 'equipmentCategory']);
        $categories = EquipmentCategory::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.certificate-templates.edit', compact('certificateTemplate', 'categories'));
    }

    /**
     * Update the specified template
     */
    public function update(Request $request, CertificateTemplate $certificateTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'equipment_category_id' => 'required|exists:equipment_categories,id',
            'title' => 'required|string|max:255',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $original = $certificateTemplate->getOriginal();
        $certificateTemplate->update($validated);

        // Log update
        $certificateTemplate->logChange('updated', [
            'before' => $original,
            'after' => $certificateTemplate->getAttributes()
        ]);

        return redirect()
            ->route('admin.certificate-templates.edit', $certificateTemplate)
            ->with('success', 'Template updated successfully.');
    }

    /**
     * Remove the specified template
     */
    public function destroy(CertificateTemplate $certificateTemplate)
    {
        // Check if template is in use
        if ($certificateTemplate->certificates()->count() > 0) {
            return back()->with('error', 'Cannot delete template that is in use by certificates.');
        }

        $certificateTemplate->logChange('deleted');
        $certificateTemplate->delete();

        return redirect()
            ->route('admin.certificate-templates.index')
            ->with('success', 'Template deleted successfully.');
    }

    /**
     * Toggle template active status
     */
    public function toggle(CertificateTemplate $certificateTemplate)
    {
        $certificateTemplate->is_active = !$certificateTemplate->is_active;
        $certificateTemplate->save();

        $changeType = $certificateTemplate->is_active ? 'activated' : 'deactivated';
        $certificateTemplate->logChange($changeType);

        return back()->with('success', 'Template ' . $changeType . ' successfully.');
    }

    /**
     * Duplicate a certificate template with all sections and fields
     */
    public function duplicate(CertificateTemplate $certificateTemplate)
    {
        $certificateTemplate->load(['sections.fields']);

        $newTemplate = $certificateTemplate->replicate();
        $newTemplate->name = $certificateTemplate->name . ' (Copy)';
        $newTemplate->is_default = false;
        $newTemplate->created_by = auth()->id();
        $newTemplate->save();

        foreach ($certificateTemplate->sections as $section) {
            $newSection = $section->replicate();
            $newSection->certificate_template_id = $newTemplate->id;
            $newSection->save();

            foreach ($section->fields as $field) {
                $newField = $field->replicate();
                $newField->template_section_id = $newSection->id;
                $newField->save();
            }
        }

        return redirect()->route('admin.certificate-templates.index')
            ->with('success', 'Certificate template duplicated successfully.');
    }

    /**
     * Set template as default for its category
     */
    public function setDefault(CertificateTemplate $certificateTemplate)
    {
        // Remove default flag from other templates in same category
        CertificateTemplate::where('equipment_category_id', $certificateTemplate->equipment_category_id)
            ->where('id', '!=', $certificateTemplate->id)
            ->update(['is_default' => false]);

        $certificateTemplate->is_default = true;
        $certificateTemplate->save();

        return back()->with('success', 'Template set as default for this category.');
    }

    /**
     * Live preview template structure and document sheet with original data
     */
    public function preview(Request $request, CertificateTemplate $certificateTemplate)
    {
        $certificateTemplate->load(['sections.fields', 'equipmentCategory']);
        
        if ($request->has('ajax')) {
            return view('admin.certificate-templates.partials.fields-inputs', [
                'sections' => $certificateTemplate->sections,
                'values' => [] // Empty for fresh create
            ]);
        }
        
        // Fetch any real certificate in the system linked to this category or template
        $realCertificate = \App\Models\Certificate::with(['vessel', 'customer', 'serviceJob', 'fieldValues.definition', 'issuedBy', 'assignedVerifier', 'items'])
            ->where('certificate_template_id', $certificateTemplate->id)
            ->orWhere('equipment_category_id', $certificateTemplate->equipment_category_id)
            ->latest()
            ->first();

        // Standard original reference data matching Marine Safety standards
        $originalData = [
            'vessel_name' => $realCertificate?->vessel?->name ?? 'MV Gulf Pioneer',
            'customer_name' => $realCertificate?->customer?->company_name ?? 'Demo Customer',
            'imo_number' => $realCertificate?->vessel?->imo_number ?? 'IMO9321456',
            'port_registry' => $realCertificate?->vessel?->port_of_registry ?? '-',
            'call_sign' => $realCertificate?->vessel?->call_sign ?? 'A7EP',
            'job_number' => $realCertificate?->serviceJob?->job_number ?? '208-4582',
            'cert_number' => $realCertificate?->certificate_number ?? 'CERT-2026-088',
            'inspection_date' => $realCertificate?->issue_date ? $realCertificate->issue_date->format('d F Y') : '07 August 2026',
            'expiry_date' => $realCertificate?->expiry_date ? $realCertificate->expiry_date->format('d F Y') : '07 August 2027',
            'verified_by' => 'Engineer 1',
            'company_branch' => 'Louis Marine Qatar / Safety Link Marine Services',
        ];

        // Dynamically build category-relatable sample field values for every field in template
        $categorySlug = $certificateTemplate->equipmentCategory->slug ?? '';
        $sampleFieldValues = [];

        foreach ($certificateTemplate->sections as $section) {
            foreach ($section->fields as $field) {
                $sampleFieldValues[$field->id] = $this->resolveRelatableFieldValue($field, $categorySlug, $realCertificate);
            }
        }

        // Build 10 Sample Cylinder/Equipment Items for multi-item separate sheet annexure
        $sampleCylinders = [];
        $isMultiCylinderCategory = in_array($categorySlug, [
            'eebd', 
            'scba', 
            'breathing-apparatus-scba',
            'fire-extinguisher', 
            'co2-fixed-system', 
            'life-raft', 
            'lifeboat-rescue-boat', 
            'lifeboat',
            'medical-oxygen-resuscitator',
            'medical-oxygen',
            'hydrostatic-test',
            'life-jacket',
            'immersion-suit',
            'safety-harness-fall-arrest',
            'fire-hose-hydrant',
            'gas-detection-system',
            'fixed-foam-system',
            'breathing-air-compressor',
            'pyrotechnics-distress-signals'
        ]) || true; // Multi-item separate sheet available across all equipment categories
        $isLifeRaftCategory = ($categorySlug === 'life-raft');
        $isLifeboatCategory = in_array($categorySlug, ['lifeboat-rescue-boat', 'lifeboat']);

        $lifeRaftChecklistItems = [
            ['item_no' => 1, 'description' => 'Food Rations (SOLAS Emergency Rations)', 'required_qty' => '25 Packs', 'status' => 'Good / Sealed', 'expiry_date' => '2028-08', 'remarks' => 'SOLAS Approved'],
            ['item_no' => 2, 'description' => 'Drinking Water (Fresh Water Sachets)', 'required_qty' => '37.5 Liters', 'status' => 'Good / Sealed', 'expiry_date' => '2028-08', 'remarks' => 'SOLAS Approved'],
            ['item_no' => 3, 'description' => 'Parachute Rocket Flares (Red Signal)', 'required_qty' => '4 Pcs', 'status' => 'Passed / Good', 'expiry_date' => '2028-05', 'remarks' => 'Comet / Pains Wessex'],
            ['item_no' => 4, 'description' => 'Hand Flares (Red Hand Flares)', 'required_qty' => '6 Pcs', 'status' => 'Passed / Good', 'expiry_date' => '2028-05', 'remarks' => 'Comet / Pains Wessex'],
            ['item_no' => 5, 'description' => 'Buoyant Smoke Signals (Orange Smoke)', 'required_qty' => '2 Pcs', 'status' => 'Passed / Good', 'expiry_date' => '2028-05', 'remarks' => 'Comet / Pains Wessex'],
            ['item_no' => 6, 'description' => 'SOLAS First Aid Kit (Medical Chest)', 'required_qty' => '1 Kit', 'status' => 'Sealed / Complete', 'expiry_date' => '2028-04', 'remarks' => 'Category A Medical'],
            ['item_no' => 7, 'description' => 'Thermal Protective Aids (TPA Suits)', 'required_qty' => '3 Pcs', 'status' => 'Good Condition', 'expiry_date' => 'N/A', 'remarks' => 'IMO Compliant'],
            ['item_no' => 8, 'description' => 'Sea Anchor & Towing Swivel Line', 'required_qty' => '2 Pcs', 'status' => 'Intact & Rigged', 'expiry_date' => 'N/A', 'remarks' => 'Fitted with Swivel'],
            ['item_no' => 9, 'description' => 'Radar Reflector / SART Transponder', 'required_qty' => '1 Pc', 'status' => 'Tested / Operational', 'expiry_date' => '2027-11', 'remarks' => 'Operational Pass'],
            ['item_no' => 10, 'description' => 'Heliograph Signal Mirror & Whistle', 'required_qty' => '1 Set', 'status' => 'Good Condition', 'expiry_date' => 'N/A', 'remarks' => 'With Lanyard'],
            ['item_no' => 11, 'description' => 'Waterproof Torch & Spare Signal Light', 'required_qty' => '1 Set', 'status' => 'Tested / Working', 'expiry_date' => '2027-10', 'remarks' => 'Signal Light Pass'],
            ['item_no' => 12, 'description' => 'Hydrostatic Release Unit (HRU)', 'required_qty' => '1 Pc', 'status' => 'Replaced / Valid', 'expiry_date' => '2028-08', 'remarks' => 'Hammar H20 Model']
        ];

        $lifeboatChecklistItems = [
            ['item_no' => 1, 'description' => 'Lifeboat Structure & Canopy', 'inspection_parameter' => 'Visual examination for cracks, delamination & structural integrity', 'service_type' => 'Annual / 5-Yr', 'status' => 'Satisfactory / Good', 'remarks' => 'Structure Intact'],
            ['item_no' => 2, 'description' => 'Lifeboat Engine & Propulsion System', 'inspection_parameter' => 'Engine start test, cooling water flow, gear engagement & speed test', 'service_type' => 'Annual / 5-Yr', 'status' => 'Tested / Pass', 'remarks' => 'Dual Start Functional'],
            ['item_no' => 3, 'description' => 'Fuel System & Fuel Storage Tank', 'inspection_parameter' => 'Fuel level, tank integrity, fuel filter & piping leak examination', 'service_type' => 'Annual / 5-Yr', 'status' => 'Good / No Leaks', 'remarks' => 'Fully Fuelled'],
            ['item_no' => 4, 'description' => 'Steering Gear & Emergency Tiller', 'inspection_parameter' => 'Rudder stock, hydraulic/cable movement & emergency tiller connection', 'service_type' => 'Annual / 5-Yr', 'status' => 'Tested / Operational', 'remarks' => 'Smooth Operation'],
            ['item_no' => 5, 'description' => 'On-Load / Off-Load Release Hooks', 'inspection_parameter' => 'Hook release mechanism, hydrostatic interlock reset & cable tension', 'service_type' => '5-Yearly Overhaul', 'status' => 'Overhauled & Tested', 'remarks' => 'Reset Verified'],
            ['item_no' => 6, 'description' => 'Davit Structure & Hinge Pins', 'inspection_parameter' => 'Davit arms, sheaves, lubrication nipples & structural mounting bolts', 'service_type' => 'Annual / 5-Yr', 'status' => 'Examined / Good', 'remarks' => 'Greased & Serviced'],
            ['item_no' => 7, 'description' => 'Winch Main Brake System', 'inspection_parameter' => 'Dynamic 1.1x / 1.25x proof load test & brake holding verification', 'service_type' => '5-Yearly Proof Load Test', 'status' => '1.25x Load Test Pass', 'remarks' => 'Held 100% Load'],
            ['item_no' => 8, 'description' => 'Winch Centrifugal Speed Governor', 'inspection_parameter' => 'Lowering speed test, governor brake pads & internal gear inspection', 'service_type' => '5-Yearly Overhaul', 'status' => 'Dismantled & Passed', 'remarks' => 'Speed within Limits'],
            ['item_no' => 9, 'description' => 'Winch Wire Ropes / Falls', 'inspection_parameter' => 'Wire rope condition, corrosion check & end-for-end turn / renewal date', 'service_type' => 'Annual Inspection', 'status' => 'Good / Lubricated', 'remarks' => 'Fitted 2025-08'],
            ['item_no' => 10, 'description' => 'Electrical System & Batteries', 'inspection_parameter' => 'Battery charger, solar panel, searchlight & internal lighting test', 'service_type' => 'Annual / 5-Yr', 'status' => 'Tested / Operational', 'remarks' => 'Charge Normal'],
            ['item_no' => 11, 'description' => 'Water Spray System & Air Supply', 'inspection_parameter' => 'Air bottle pressure, spray pump operation & nozzle clearance (if fitted)', 'service_type' => 'Annual / 5-Yr', 'status' => 'Tested / Pass', 'remarks' => 'Air Bottles 200 Bar'],
            ['item_no' => 12, 'description' => 'Limit Switches & Hand Crank Interlock', 'inspection_parameter' => 'Upper limit switch cut-out & hand crank power disengagement interlock', 'service_type' => 'Annual / 5-Yr', 'status' => 'Tested / Operational', 'remarks' => 'Safety Cut-out Pass']
        ];

        if ($isMultiCylinderCategory) {
            for ($i = 1; $i <= 10; $i++) {
                if ($categorySlug === 'fire-extinguisher') {
                    $types = ['CO2 Fire Extinguisher', 'Dry Powder Extinguisher', 'Foam Fire Extinguisher', 'Water Fire Extinguisher'];
                    $type = $types[($i - 1) % 4];
                    $capacities = ['6 kg', '9 kg', '9 Liters', '5 kg'];
                    $cap = $capacities[($i - 1) % 4];
                    $mfrs = ['Kidde Protection', 'LALIZAS Marine', 'Bavaria Fire', 'Gloria'];
                    $mfr = $mfrs[($i - 1) % 4];

                    $sampleCylinders[] = [
                        'item_no' => $i,
                        'serial_number' => 'FE-99214-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                        'equipment_type' => $type,
                        'capacity' => $cap,
                        'manufacturer' => $mfr,
                        'test_pressure' => '15 Bar',
                        'manufacture_date' => '2024-0' . (($i % 9) + 1),
                        'visual_condition' => 'Good',
                        'test_result' => 'Pass',
                    ];
                } elseif ($categorySlug === 'eebd') {
                    $sampleCylinders[] = [
                        'item_no' => $i,
                        'serial_number' => 'EEBD-7894' . ($i - 1),
                        'equipment_type' => 'Emergency Escape Breathing Device (EEBD)',
                        'capacity' => '210 Bar / 15 Min',
                        'manufacturer' => ($i % 2 == 0) ? 'MSA Safety' : 'Dräger Safety',
                        'test_pressure' => '210 Bar',
                        'manufacture_date' => '2024-05',
                        'visual_condition' => 'Good / Intact',
                        'test_result' => 'Pass',
                    ];
                } elseif ($categorySlug === 'scba' || $categorySlug === 'breathing-apparatus-scba') {
                    $sampleCylinders[] = [
                        'item_no' => $i,
                        'serial_number' => 'SCBA-CYL-99' . str_pad($i, 2, '0', STR_PAD_LEFT),
                        'equipment_type' => 'SCBA Carbon Composite Cylinder',
                        'capacity' => '6.8 Liters (300 Bar)',
                        'manufacturer' => 'Dräger Safety',
                        'test_pressure' => '300 Bar',
                        'manufacture_date' => '2024-02',
                        'visual_condition' => 'Good',
                        'test_result' => 'Pass',
                    ];
                } elseif ($categorySlug === 'co2-fixed-system') {
                    $sampleCylinders[] = [
                        'item_no' => $i,
                        'serial_number' => 'CO2-CYL-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                        'equipment_type' => 'CO2 High Pressure Cylinder',
                        'capacity' => '45 kg (67 Liters)',
                        'manufacturer' => 'Kidde Fire Protection',
                        'test_pressure' => '250 Bar (Working 58 Bar)',
                        'manufacture_date' => '2023-11',
                        'visual_condition' => 'Good / Fully Charged',
                        'test_result' => 'Pass',
                    ];
                } elseif ($categorySlug === 'life-jacket') {
                    $types = ['Inflatable Life Jacket (275N)', 'Inflatable Life Jacket (150N)', 'SOLAS Foam Life Jacket'];
                    $type = $types[($i - 1) % 3];
                    $capacities = ['275 Newton (Auto-Inflation)', '150 Newton (Auto-Inflation)', '275 Newton (Manual/Auto)'];
                    $cap = $capacities[($i - 1) % 3];
                    $mfrs = ['LALIZAS Marine', 'Viking Life-Saving', 'Crewsaver', 'Hansen Protection'];
                    $mfr = $mfrs[($i - 1) % 4];

                    $sampleCylinders[] = [
                        'item_no' => $i,
                        'serial_number' => 'LJ-2026-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                        'equipment_type' => $type,
                        'capacity' => $cap,
                        'manufacturer' => $mfr,
                        'test_pressure' => 'CO2 Cylinder & Air Inflation Pass',
                        'manufacture_date' => '2024-0' . (($i % 8) + 1),
                        'visual_condition' => 'Fabric Good / Light & Whistle Working',
                        'test_result' => 'Pass',
                    ];
                } elseif ($categorySlug === 'immersion-suit') {
                    $types = ['SOLAS Insulated Immersion Suit', 'Immersion Suit with Buoyant Pillow', 'Thermal Protective Suit'];
                    $type = $types[($i - 1) % 3];
                    $capacities = ['Neoprene 5mm (Universal)', 'Neoprene 5mm (Extra Large)', 'Neoprene 5mm (Medium)'];
                    $cap = $capacities[($i - 1) % 3];
                    $mfrs = ['LALIZAS Marine', 'Viking Life-Saving', 'Mullion', 'Hansen Protection'];
                    $mfr = $mfrs[($i - 1) % 4];

                    $sampleCylinders[] = [
                        'item_no' => $i,
                        'serial_number' => 'IS-SOLAS-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                        'equipment_type' => $type,
                        'capacity' => $cap,
                        'manufacturer' => $mfr,
                        'test_pressure' => '0.2 Bar Air Leak Test Pass',
                        'manufacture_date' => '2024-0' . (($i % 8) + 1),
                        'visual_condition' => 'Seams & Zipper Lubricated / Intact',
                        'test_result' => 'Pass',
                    ];
                } elseif ($categorySlug === 'safety-harness-fall-arrest') {
                    $types = ['Full Body Safety Harness', 'Dual Fall Arrest Lanyard', 'Safety Harness with Rescue Loop'];
                    $type = $types[($i - 1) % 3];
                    $capacities = ['22 kN Proof Load (SOLAS)', '22 kN Proof Load (EN 361)', '25 kN Proof Load'];
                    $cap = $capacities[($i - 1) % 3];
                    $mfrs = ['Capital Safety', '3M Miller', 'Kratos Safety', 'LALIZAS Marine'];
                    $mfr = $mfrs[($i - 1) % 4];

                    $sampleCylinders[] = [
                        'item_no' => $i,
                        'serial_number' => 'SH-FA-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                        'equipment_type' => $type,
                        'capacity' => $cap,
                        'manufacturer' => $mfr,
                        'test_pressure' => '22 kN Load Test Pass',
                        'manufacture_date' => '2024-0' . (($i % 8) + 1),
                        'visual_condition' => 'Webbing, Buckles & D-Ring Intact',
                        'test_result' => 'Pass',
                    ];
                } elseif ($categorySlug === 'fire-hose-hydrant') {
                    $types = ['Marine Fire Hose 2" x 20m', 'Marine Fire Hose 1.5" x 20m', 'Fire Hose Dual Jet Nozzle'];
                    $type = $types[($i - 1) % 3];
                    $capacities = ['15 Bar Working (Hydraulic)', '15 Bar Working (Synthetic)', 'Dual Spray / Jet Nozzle'];
                    $cap = $capacities[($i - 1) % 3];
                    $mfrs = ['Angus Fire', 'Syntex Marine', 'LALIZAS Marine', 'Kidde Protection'];
                    $mfr = $mfrs[($i - 1) % 4];

                    $sampleCylinders[] = [
                        'item_no' => $i,
                        'serial_number' => 'FH-2026-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                        'equipment_type' => $type,
                        'capacity' => $cap,
                        'manufacturer' => $mfr,
                        'test_pressure' => '15 Bar Hydro Pressure Pass',
                        'manufacture_date' => '2024-0' . (($i % 8) + 1),
                        'visual_condition' => 'No Leaks / Coupling & Washer Intact',
                        'test_result' => 'Pass',
                    ];
                } else {
                    $types = ['Equipment Item Unit', 'Marine Safety Appliance', 'Inspection Unit'];
                    $type = $types[($i - 1) % 3];
                    $capacities = ['Standard SOLAS / IMO', 'Heavy Duty Marine', 'SOLAS Approved'];
                    $cap = $capacities[($i - 1) % 3];
                    $mfrs = ['LALIZAS Marine', 'Viking Life-Saving', 'Dräger Safety', 'MSA Safety'];
                    $mfr = $mfrs[($i - 1) % 4];

                    $sampleCylinders[] = [
                        'item_no' => $i,
                        'serial_number' => 'EQ-ITEM-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                        'equipment_type' => $type,
                        'capacity' => $cap,
                        'manufacturer' => $mfr,
                        'test_pressure' => 'Operational & Pressure Pass',
                        'manufacture_date' => '2024-0' . (($i % 8) + 1),
                        'visual_condition' => 'Good Condition / Passed',
                        'test_result' => 'Pass',
                    ];
                }
            }
        }

        // Build 10 Sample Rows for Repeatable Section Tables
        $sampleRowFieldValues = [];

        for ($i = 1; $i <= 10; $i++) {
            $cyl = $sampleCylinders[$i - 1] ?? null;

            foreach ($certificateTemplate->sections as $section) {
                foreach ($section->fields as $field) {
                    if ($cyl) {
                        $fName = strtolower($field->field_name);
                        $fLabel = strtolower($field->field_label);

                        if (Str::contains($fName, ['serial', 'sn', 'number', 'tag'])) {
                            $sampleRowFieldValues[$i][$field->id] = $cyl['serial_number'];
                        } elseif (Str::contains($fName, ['type', 'equipment', 'description', 'model'])) {
                            $sampleRowFieldValues[$i][$field->id] = $cyl['equipment_type'];
                        } elseif (Str::contains($fName, ['capacity', 'weight', 'size', 'rating'])) {
                            $sampleRowFieldValues[$i][$field->id] = $cyl['capacity'];
                        } elseif (Str::contains($fName, ['manufacturer', 'mfr', 'make', 'brand'])) {
                            $sampleRowFieldValues[$i][$field->id] = $cyl['manufacturer'];
                        } elseif (Str::contains($fName, ['pressure', 'test_bar', 'working_bar'])) {
                            $sampleRowFieldValues[$i][$field->id] = $cyl['test_pressure'];
                        } elseif (Str::contains($fName, ['mfg', 'manufacture', 'year', 'built'])) {
                            $sampleRowFieldValues[$i][$field->id] = $cyl['manufacture_date'];
                        } elseif (Str::contains($fName, ['condition', 'visual', 'status', 'state'])) {
                            $sampleRowFieldValues[$i][$field->id] = $cyl['visual_condition'];
                        } elseif (Str::contains($fName, ['result', 'outcome', 'pass', 'hydro'])) {
                            $sampleRowFieldValues[$i][$field->id] = $cyl['test_result'];
                        } else {
                            $sampleRowFieldValues[$i][$field->id] = $sampleFieldValues[$field->id] ?? 'Pass';
                        }
                    } else {
                        $sampleRowFieldValues[$i][$field->id] = $sampleFieldValues[$field->id] ?? 'Pass';
                    }
                }
            }
        }

        // Load branding & certificate settings
        $settings = \App\Models\Setting::getAllCached();

        $customHeaderUrl = !empty($settings['certificate_header_png']) 
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($settings['certificate_header_png']) 
            : null;

        $customFooterUrl = !empty($settings['certificate_footer_png']) 
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($settings['certificate_footer_png']) 
            : null;

        $companyLogoUrl = !empty($settings['company_logo']) 
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($settings['company_logo']) 
            : null;

        return view('admin.certificate-templates.preview', compact(
            'certificateTemplate', 
            'realCertificate', 
            'originalData', 
            'sampleFieldValues',
            'sampleRowFieldValues',
            'settings',
            'customHeaderUrl',
            'customFooterUrl',
            'companyLogoUrl',
            'sampleCylinders',
            'isMultiCylinderCategory',
            'isLifeRaftCategory',
            'lifeRaftChecklistItems',
            'isLifeboatCategory',
            'lifeboatChecklistItems'
        ));
    }

    private function resolveRelatableFieldValue($field, $categorySlug, $realCertificate = null)
    {
        if ($realCertificate) {
            $val = $realCertificate->fieldValues->where('template_field_id', $field->id)->first();
            if ($val && !empty($val->field_value)) {
                return $val->field_value;
            }
        }

        $name = strtolower($field->field_name);
        $label = strtolower($field->field_label);
        $type = $field->field_type;

        // 1. Dynamic Inspection Report / Findings / Remarks
        if (\Illuminate\Support\Str::contains($name, ['report', 'finding', 'remark'])) {
            $equipmentName = 'equipment';
            if (\Illuminate\Support\Str::contains($categorySlug, ['raft'])) $equipmentName = 'life rafts';
            elseif (\Illuminate\Support\Str::contains($categorySlug, ['extinguisher'])) $equipmentName = 'fire extinguishers';
            elseif (\Illuminate\Support\Str::contains($categorySlug, ['eebd'])) $equipmentName = 'emergency escape breathing devices (EEBD)';
            elseif (\Illuminate\Support\Str::contains($categorySlug, ['scba'])) $equipmentName = 'self-contained breathing apparatus (SCBA)';
            elseif (\Illuminate\Support\Str::contains($categorySlug, ['jacket'])) $equipmentName = 'life jackets';
            elseif (\Illuminate\Support\Str::contains($categorySlug, ['immersion', 'suit'])) $equipmentName = 'immersion suits';
            elseif (\Illuminate\Support\Str::contains($categorySlug, ['boat', 'lifeboat'])) $equipmentName = 'lifeboats & rescue boats';
            elseif (\Illuminate\Support\Str::contains($categorySlug, ['gas'])) $equipmentName = 'gas detection systems';
            elseif (\Illuminate\Support\Str::contains($categorySlug, ['compressor'])) $equipmentName = 'breathing air compressors';
            elseif (\Illuminate\Support\Str::contains($categorySlug, ['foam'])) $equipmentName = 'fixed foam systems';
            elseif (\Illuminate\Support\Str::contains($categorySlug, ['hose'])) $equipmentName = 'fire hoses & hydrants';
            elseif (\Illuminate\Support\Str::contains($categorySlug, ['harness'])) $equipmentName = 'safety harnesses & fall arrest systems';
            elseif (\Illuminate\Support\Str::contains($categorySlug, ['hydrostatic'])) $equipmentName = 'pressure vessels & gas cylinders';

            return "The above {$equipmentName} have been inspected, tested, and serviced in accordance with applicable SOLAS / IMO requirements, manufacturer instructions, and classification society standards. The equipment was found to be in satisfactory operating condition and fit for service, except as noted above.";
        }

        // 2. Service & Life Raft Specific Fields
        if (\Illuminate\Support\Str::contains($name, ['service_station'])) return 'Louis Marine Qatar Service Station';
        if (\Illuminate\Support\Str::contains($name, ['service_cert_number'])) return 'LR-CERT-8841';
        if (\Illuminate\Support\Str::contains($name, ['last_service'])) return '2025-08-10';
        if (\Illuminate\Support\Str::contains($name, ['inspector_name', 'inspector'])) return 'Engineer 1';
        if (\Illuminate\Support\Str::contains($name, ['visual_condition', 'container_condition', 'hydrostatic_release', 'painter_line'])) return 'Good';
        if (\Illuminate\Support\Str::contains($name, ['safety_pin'])) return 'Intact';
        if (\Illuminate\Support\Str::contains($name, ['pressure_gauge'])) return '15 Bar';

        // 3. Hydrostatic Test Specific Fields
        if (\Illuminate\Support\Str::contains($name, ['tare_weight'])) return '62.5 Kg';
        if (\Illuminate\Support\Str::contains($name, ['water_capacity'])) return '50 Liters';
        if (\Illuminate\Support\Str::contains($name, ['test_pressure'])) return '250 Bar';
        if (\Illuminate\Support\Str::contains($name, ['applied_pressure'])) return '250 Bar';
        if (\Illuminate\Support\Str::contains($name, ['test_medium'])) return 'Fresh Water';
        if (\Illuminate\Support\Str::contains($name, ['holding_time', 'hold_duration'])) return '60 Seconds';
        if (\Illuminate\Support\Str::contains($name, ['expansion_method'])) return 'Water Jacket Method';
        if (\Illuminate\Support\Str::contains($name, ['permanent_expansion'])) return '1.2% (Pass < 10%)';
        if (\Illuminate\Support\Str::contains($name, ['cylinder_contents'])) return 'CO2 Gas';
        if (\Illuminate\Support\Str::contains($name, ['stamp_date'])) return '2026-08-07';

        // 2. Model Number / Model
        if (\Illuminate\Support\Str::contains($name, ['model'])) {
            if (\Illuminate\Support\Str::contains($categorySlug, ['compressor'])) return 'Bauer Poseidon PE-300-VE';
            if (\Illuminate\Support\Str::contains($categorySlug, ['scba'])) return 'Dräger PSS 5000';
            if (\Illuminate\Support\Str::contains($categorySlug, ['eebd'])) return '210 bar 15 min eebd';
            if (\Illuminate\Support\Str::contains($categorySlug, ['harness'])) return 'Miller Revolution R5';
            if (\Illuminate\Support\Str::contains($categorySlug, ['gas'])) return 'Riken Keiki GX-3R Pro';
            if (\Illuminate\Support\Str::contains($categorySlug, ['oxygen', 'resuscitator'])) return 'Oxylator HD-2000';
            if (\Illuminate\Support\Str::contains($categorySlug, ['jacket'])) return 'LALIZAS ISO 275N';
            if (\Illuminate\Support\Str::contains($categorySlug, ['lifeboat', 'boat'])) return 'Totally Enclosed Lifeboat TELB-32';
            return 'MSA-210-B';
        }

        // 3. Serial Number / Tags
        if (\Illuminate\Support\Str::contains($name, ['serial'])) {
            if (\Illuminate\Support\Str::contains($categorySlug, ['compressor'])) return 'BAC-88942-2024';
            if (\Illuminate\Support\Str::contains($categorySlug, ['eebd'])) return '78945';
            if (\Illuminate\Support\Str::contains($categorySlug, ['scba'])) return 'SCBA-66321';
            if (\Illuminate\Support\Str::contains($categorySlug, ['lifeboat', 'boat'])) return 'LB-55201';
            if (\Illuminate\Support\Str::contains($categorySlug, ['raft'])) return 'LR-25P-7741';
            if (\Illuminate\Support\Str::contains($categorySlug, ['fire', 'extinguisher'])) return 'FE-99214';
            if (\Illuminate\Support\Str::contains($categorySlug, ['gas'])) return 'GD-77401';
            if (\Illuminate\Support\Str::contains($categorySlug, ['hydrostatic'])) return 'HT-993201';
            if (\Illuminate\Support\Str::contains($categorySlug, ['harness'])) return 'SH-88210';
            if (\Illuminate\Support\Str::contains($categorySlug, ['hose'])) return 'FH-30M-881';
            return '78945';
        }

        // 4. Manufacturer / Brand
        if (\Illuminate\Support\Str::contains($name, ['manufacturer', 'brand'])) {
            if (\Illuminate\Support\Str::contains($categorySlug, ['compressor'])) return 'BAUER Kompressoren';
            if (\Illuminate\Support\Str::contains($categorySlug, ['eebd'])) return 'msa';
            if (\Illuminate\Support\Str::contains($categorySlug, ['scba'])) return 'Dräger Safety';
            if (\Illuminate\Support\Str::contains($categorySlug, ['extinguisher'])) return 'Kidde Fire Protection';
            if (\Illuminate\Support\Str::contains($categorySlug, ['raft'])) return 'Viking Life-Saving Equipment';
            if (\Illuminate\Support\Str::contains($categorySlug, ['jacket'])) return 'LALIZAS Marine';
            if (\Illuminate\Support\Str::contains($categorySlug, ['boat', 'lifeboat'])) return 'Hatecke GmbH';
            if (\Illuminate\Support\Str::contains($categorySlug, ['gas'])) return 'Riken Keiki';
            if (\Illuminate\Support\Str::contains($categorySlug, ['harness'])) return 'Honeywell Miller';
            if (\Illuminate\Support\Str::contains($categorySlug, ['pyrotechnics'])) return 'Comet Marine Pyrotechnics';
            return 'msa';
        }

        // 5. Pressures & Capacities
        if (\Illuminate\Support\Str::contains($name, ['working_pressure', 'pressure_bar'])) {
            if (\Illuminate\Support\Str::contains($categorySlug, ['compressor'])) return '330 Bar';
            if (\Illuminate\Support\Str::contains($categorySlug, ['hose'])) return '15 Bar';
            if (\Illuminate\Support\Str::contains($categorySlug, ['co2'])) return '58 Bar';
            return '300 Bar';
        }
        if (\Illuminate\Support\Str::contains($name, ['motor', 'drive'])) return 'Electric 7.5 kW / 10 HP';
        if (\Illuminate\Support\Str::contains($name, ['engine_model_hp'])) return 'Bukh FK29 / 29 HP';
        if (\Illuminate\Support\Str::contains($name, ['release_mechanism'])) return 'Simson Hook On-Off Load';
        if (\Illuminate\Support\Str::contains($name, ['cylinder_capacity'])) {
            if (\Illuminate\Support\Str::contains($categorySlug, ['oxygen', 'resuscitator'])) return '2.5 Liters (200 Bar)';
            return '6.8 Liters (300 Bar)';
        }
        if (\Illuminate\Support\Str::contains($name, ['capacity'])) {
            if (\Illuminate\Support\Str::contains($categorySlug, ['raft', 'boat'])) return '25 Persons';
            if (\Illuminate\Support\Str::contains($categorySlug, ['extinguisher'])) return '6 kg';
            if (\Illuminate\Support\Str::contains($categorySlug, ['foam'])) return '1000 Liters';
            if (\Illuminate\Support\Str::contains($categorySlug, ['co2'])) return '2025 kg CO2';
            return '25 Persons';
        }

        // 6. Pyrotechnics Quantities
        if (\Illuminate\Support\Str::contains($name, ['parachute_flares_qty'])) return '12';
        if (\Illuminate\Support\Str::contains($name, ['hand_flares_qty'])) return '6';
        if (\Illuminate\Support\Str::contains($name, ['buoyant_smoke_qty'])) return '2';

        // 7. Fire Hose & Fixed System Specific
        if (\Illuminate\Support\Str::contains($name, ['hose_diameter'])) return '2.5" x 30m';
        if (\Illuminate\Support\Str::contains($name, ['coupling_type'])) return 'Storz 65';
        if (\Illuminate\Support\Str::contains($name, ['nozzle_type'])) return 'Jet/Spray Dual Purpose';
        if (\Illuminate\Support\Str::contains($name, ['protected_area'])) return 'Engine Room & Paint Locker';
        if (\Illuminate\Support\Str::contains($name, ['tank_capacity'])) return '1000 Liters';
        if (\Illuminate\Support\Str::contains($name, ['foam_type'])) return 'AFFF 3% Synthetic';
        if (\Illuminate\Support\Str::contains($name, ['installation_location'])) return 'Engine Room & Cargo Hold';
        if (\Illuminate\Support\Str::contains($name, ['number_of_cylinders'])) return '45 Cylinders';

        // 8. Air Compressor Gas Purity Metrics
        if (\Illuminate\Support\Str::contains($name, ['co_level'])) return '2.5 ppm (Limit < 5 ppm)';
        if (\Illuminate\Support\Str::contains($name, ['co2_level'])) return '380 ppm (Limit < 500 ppm)';
        if (\Illuminate\Support\Str::contains($name, ['oil_mist'])) return '0.05 mg/m³ (Limit < 0.5 mg/m³)';
        if (\Illuminate\Support\Str::contains($name, ['moisture_content'])) return '15 mg/m³ (Limit < 25 mg/m³)';
        if (\Illuminate\Support\Str::contains($name, ['air_quality_result'])) return 'Pass';

        // 9. Gas Detector Setpoints
        if (\Illuminate\Support\Str::contains($name, ['monitored_gases'])) return 'O2, H2S, CO, LEL';
        if (\Illuminate\Support\Str::contains($name, ['span_gas'])) return 'O2: 18.0%, H2S: 25ppm, CO: 50ppm, LEL: 50%';
        if (\Illuminate\Support\Str::contains($name, ['alarm1', 'alarm_1'])) return 'O2: 19.5%, H2S: 10ppm, CO: 25ppm, LEL: 10%';
        if (\Illuminate\Support\Str::contains($name, ['alarm2', 'alarm_2'])) return 'O2: 23.5%, H2S: 20ppm, CO: 100ppm, LEL: 20%';
        if (\Illuminate\Support\Str::contains($name, ['response_time'])) return 'T90 < 15 seconds';

        // 10. Dates
        if ($type === 'date' || \Illuminate\Support\Str::contains($name, ['date', 'due'])) {
            if (\Illuminate\Support\Str::contains($name, ['next', 'due', 'expiry'])) {
                if (\Illuminate\Support\Str::contains($categorySlug, ['hydrostatic'])) return '2031-08-07';
                return '2027-08-07';
            }
            if (\Illuminate\Support\Str::contains($name, ['manufacture', 'filter', 'recharge'])) return '2026-07-07';
            return '2026-08-07';
        }

        // 11. Numbers
        if ($type === 'number') return '1';

        // 12. Checkbox
        if ($type === 'checkbox') return 'Complied / Pass';

        // 13. Standards reference
        if (\Illuminate\Support\Str::contains($name, ['standard_reference'])) {
            if (\Illuminate\Support\Str::contains($categorySlug, ['hydrostatic'])) return 'DOT / CGA C-6 & ISO 18119';
            if (\Illuminate\Support\Str::contains($categorySlug, ['compressor'])) return 'EN 12021 & BS 4275 Breathing Air Standard';
            if (\Illuminate\Support\Str::contains($categorySlug, ['gas'])) return 'SOLAS Reg. VI/3 & MSC.1/Circ.1477';
            if (\Illuminate\Support\Str::contains($categorySlug, ['lifeboat', 'boat'])) return 'SOLAS / IMO MSC.1/Circ.1206';
            if (\Illuminate\Support\Str::contains($categorySlug, ['harness'])) return 'EN 361 / EN 355 / OSHA 1926';
            if (\Illuminate\Support\Str::contains($categorySlug, ['hose'])) return 'SOLAS Reg. II-2/10 & NFPA 1962';
            if (\Illuminate\Support\Str::contains($categorySlug, ['foam'])) return 'SOLAS Ch. II-2 & IMO MSC.1/Circ.1312';
            return 'SOLAS / IMO Standard';
        }

        // 14. Dropdown / Options
        if ($field->field_options && isset($field->field_options['options']) && count($field->field_options['options']) > 0) {
            return $field->field_options['options'][0];
        }

        return 'Pass';
    }

    public function export()
    {
        $filename = 'certificate-templates-export-' . date('Y-m-d') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        
        $columns = array('Name', 'Category', 'Title', 'Active', 'Default');
        
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            $templates = CertificateTemplate::with('equipmentCategory')->get();
            foreach ($templates as $template) {
                fputcsv($file, array(
                    $template->name, 
                    $template->equipmentCategory->name ?? 'N/A', 
                    $template->title,
                    $template->is_active ? 'Yes' : 'No',
                    $template->is_default ? 'Yes' : 'No'
                ));
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function downloadTemplate()
    {
        $filename = 'certificate-template-import-template.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        
        $columns = array('name', 'category_name', 'title');
        
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            // Example row
            fputcsv($file, array('Standard Fire Extinguisher', 'Fire Extinguisher', 'Certificate of Inspection'));
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();
            
            $handle = fopen($file->getPathname(), 'r');
            $header = fgetcsv($handle); // Skip header
            
            $count = 0;
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 3) continue; 
                
                $name = $row[0] ?? null;
                $categoryName = $row[1] ?? null;
                $title = $row[2] ?? null;
                
                if (!$name || !$categoryName || !$title) continue;
                
                // Find Category by name
                $category = EquipmentCategory::where('name', $categoryName)->first();
                if (!$category) continue; // Skip if category not found
                
                CertificateTemplate::updateOrCreate(
                    ['name' => $name, 'equipment_category_id' => $category->id],
                    [
                        'title' => $title,
                        'is_active' => true,
                        'created_by' => auth()->id(),
                        'header_config' => ['show_logo' => true, 'show_company_name' => true],
                        'footer_config' => ['show_qr_code' => true, 'show_signature' => true]
                    ]
                );
                $count++;
            }
            
            fclose($handle);
            \Illuminate\Support\Facades\DB::commit();
            
            return redirect()->route('admin.certificate-templates.index')->with('success', "Successfully imported $count templates.");
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }
}
