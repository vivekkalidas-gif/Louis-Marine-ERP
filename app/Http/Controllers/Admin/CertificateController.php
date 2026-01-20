<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CertificateType;
use App\Models\CertificateTemplate;
use App\Models\CertificateFieldValue;
use App\Models\Customer;
use App\Models\Vessel;
use App\Models\ServiceJob;
use App\Models\EquipmentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CertificateController extends Controller
{
    use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(Certificate::class, 'certificate');
    }
    
    public function index()
    {
        $drafts = Certificate::with(['type', 'customer', 'vessel', 'certificateTemplate'])
            ->whereIn('status', ['DRAFT', 'PENDING', 'REJECTED'])
            ->latest()
            ->get();

        $issued = Certificate::with(['type', 'customer', 'vessel', 'certificateTemplate'])
            ->where('status', 'ISSUED')
            ->latest()
            ->get();
            
        return view('admin.certificates.index', compact('drafts', 'issued'));
    }

    public function create(Request $request)
    {
        // Fetch all jobs for certificate issuance
        $jobs = ServiceJob::with(['customer', 'vessel', 'equipment.equipmentItem'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $types = CertificateType::all();
        
        // Load equipment categories for selection
        $equipmentCategories = \App\Models\EquipmentCategory::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        $selectedJob = null;
        $autoFillData = null;
        $template = null;
        
        // If job_id is provided, pre-select and auto-fill
        if ($request->has('job_id')) {
            $selectedJob = ServiceJob::with(['customer', 'vessel', 'equipment.equipmentItem'])
                ->find($request->job_id);
            
            if ($selectedJob) {
                $autoFillData = [
                    'customer_id' => $selectedJob->customer_id,
                    'customer_name' => $selectedJob->customer->company_name,
                    'vessel_id' => $selectedJob->vessel_id,
                    'vessel_name' => $selectedJob->vessel->name ?? 'N/A',
                    'equipment_items' => $selectedJob->equipment
                ];
            }
        }
        
        if ($request->has('category_id')) {
            $template = CertificateTemplate::where('equipment_category_id', $request->category_id)
                ->where('is_active', true)
                ->where('is_default', true)
                ->with(['sections.fields'])
                ->first();
        }

        $engineers = \App\Models\User::whereHas('roles', function($q) {
            $q->where('slug', 'engineer');
        })->get();
        
        return view('admin.certificates.create', compact('jobs', 'types', 'equipmentCategories', 'selectedJob', 'autoFillData', 'template', 'engineers'));
    }

    /**
     * API endpoint to get certificate template for a specific equipment category
     */
    public function getCertificateFields($categoryId)
    {
        // Public/Authenticated API, generally safe to view if you can view certificates
        $this->authorize('viewAny', Certificate::class);
        $template = CertificateTemplate::where('equipment_category_id', $categoryId)
            ->where('is_active', true)
            ->where('is_default', true)
            ->with(['sections.fields'])
            ->first();
        
        if (!$template) {
            return response()->json([
                'error' => 'No active template found for this category'
            ], 404);
        }
        
        return response()->json([
            'template' => $template,
            'sections' => $template->sections,
        ]);
    }

    public function store(Request $request)
    {
        // 1. Basic Validation
        $request->validate([
            'service_job_id' => 'required|exists:service_jobs,id',
            'equipment_category_id' => 'required|exists:equipment_categories,id',
            'template_id' => 'required|exists:certificate_templates,id',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'remarks' => 'nullable|string',
        ]);

        // 2. Load Template
        $template = \App\Models\CertificateTemplate::with('sections.fields')->findOrFail($request->template_id);

        // 3. Dynamic Validation Rules
        $rules = [];
        $fieldValues = [];

        foreach ($template->sections as $section) {
            if ($section->is_repeatable) {
                // Get instance count for this section
                $instanceCount = $request->input("section_{$section->id}_instances", 1);
                
                for ($i = 1; $i <= $instanceCount; $i++) {
                    foreach ($section->fields as $field) {
                        $inputName = "field_{$field->id}_instance_{$i}";
                        // Apply validation rules if field is required or has rules
                        if ($field->is_required) {
                            $rules[$inputName] = $field->getValidationRule();
                        }
                        
                        // Collect value logic
                        if ($request->has($inputName)) {
                            $fieldValues[] = [
                                'certificate_template_field_id' => $field->id,
                                'section_instance' => $i,
                                'value' => $request->input($inputName),
                            ];
                        }
                    }
                }
            } else {
                foreach ($section->fields as $field) {
                    $inputName = "field_{$field->id}";
                    if ($field->is_required) {
                        $rules[$inputName] = $field->getValidationRule();
                    }

                    if ($request->has($inputName)) {
                        $fieldValues[] = [
                            'certificate_template_field_id' => $field->id,
                            'section_instance' => 1,
                            'value' => $request->input($inputName),
                        ];
                    }
                }
            }
        }

        // Run Dynamic Validation
        $request->validate($rules);

        // 4. Create Certificate Record
        $job = ServiceJob::findOrFail($request->service_job_id);
        
        // Generate number
        $category = \App\Models\EquipmentCategory::find($request->equipment_category_id);
        // Use Str::slug to converting 'CO₂' -> 'co2' and strip special chars
        $startStr = str_replace('-', '', \Illuminate\Support\Str::slug($category->name));
        $catPrefix = strtoupper(substr($startStr, 0, 3));
        $year = date('Y');
        $seq = str_pad(Certificate::whereYear('issue_date', $year)->count() + 1, 4, '0', STR_PAD_LEFT);
        $certNumber = "LMQ-{$year}-{$catPrefix}-{$seq}";

        DB::beginTransaction();
        try {
            $certificate = Certificate::create([
                'certificate_number' => $certNumber,
                'service_job_id' => $request->service_job_id,
                'equipment_category_id' => $request->equipment_category_id,
                'certificate_template_id' => $template->id,
                'customer_id' => $job->customer_id,
                'vessel_id' => $job->vessel_id,
                'job_number' => $job->job_number,
                'issue_date' => $request->issue_date,
                'expiry_date' => $request->expiry_date,
                'remarks' => $request->remarks,
                'created_by' => auth()->id(),
                'issued_by' => auth()->id(),
                'issue_date' => $request->issue_date,
                'expiry_date' => $request->expiry_date,
                'remarks' => $request->remarks,
                'created_by' => auth()->id(),
                'issued_by' => auth()->id(),
                'status' => 'DRAFT', // Always DRAFT on create
                'assigned_verifier_id' => $request->assigned_verifier_id,
            ]);

            // Notify Verifier
            if ($request->assigned_verifier_id) {
                $verifier = \App\Models\User::find($request->assigned_verifier_id);
                if ($verifier) {
                    $verifier->notify(new \App\Notifications\CertificateVerificationAssigned($certificate));
                }
            }

            // 5. Save Field Values
            foreach ($fieldValues as $fv) {
                // Ensure value is string or json encoded if array
                $val = $fv['value'];
                if (is_array($val)) $val = json_encode($val);

                \App\Models\CertificateFieldValue::create([
                    'certificate_id' => $certificate->id,
                    'certificate_template_field_id' => $fv['certificate_template_field_id'],
                    'section_instance' => $fv['section_instance'],
                    'value' => $val,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.certificates.show', $certificate)
                ->with('success', 'Certificate created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error creating certificate: ' . $e->getMessage());
        }
    }

    public function show(Certificate $certificate)
    {
        $certificate->load([
            'type', 
            'customer', 
            'vessel', 
            'job', 
            'creator', 
            'approver', 
            'certificateTemplate.sections.fields', 
            'fieldValues.definition',
            'assignedVerifier'
        ]);
        return view('admin.certificates.show', compact('certificate'));
    }

    public function edit(Certificate $certificate)
    {
        // Load all necessary relationships
        $certificate->load([
            'certificateTemplate.sections.fields',
            'fieldValues',
            'customer',
            'vessel',
            'job'
        ]);
        
        $jobs = ServiceJob::with(['customer', 'vessel'])->latest()->get();
        $equipmentCategories = EquipmentCategory::all();
        $types = CertificateType::all();
        $engineers = \App\Models\User::whereHas('roles', function($q) {
            $q->where('slug', 'engineer');
        })->get();
        
        return view('admin.certificates.edit', compact('certificate', 'jobs', 'equipmentCategories', 'types', 'engineers'));
    }

    public function update(Request $request, Certificate $certificate)
    {
        // Get the template with necessary relationships eagerly loaded
        $certificate->load('certificateTemplate.sections.fields');
        $template = $certificate->certificateTemplate;
        if (!$template) {
            return back()->with('error', 'Certificate template not found.');
        }

        // Build validation rules dynamically
        $rules = [
            'service_job_id' => 'required|exists:service_jobs,id',
            'equipment_category_id' => 'required|exists:equipment_categories,id',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
        ];

        $fieldValues = [];
        
        // Validate dynamic fields
        foreach ($template->sections as $section) {
            if ($section->is_repeatable) {
                // Handle repeatable sections
                foreach ($section->fields as $field) {
                    $inputName = "field_{$field->id}_instance_*";
                    // We'll handle this in the loop below
                }
            } else {
                // Non-repeatable fields
                foreach ($section->fields as $field) {
                    $inputName = "field_{$field->id}";
                    if ($field->is_required) {
                        $rules[$inputName] = $field->getValidationRule();
                    }
                }
            }
        }

        // Run validation
        $request->validate($rules);

        // Update certificate basic data
        $job = ServiceJob::findOrFail($request->service_job_id);
        
        // Check if verifier changed
        $oldVerifierId = $certificate->assigned_verifier_id;
        
        $certificate->update([
            'service_job_id' => $request->service_job_id,
            'equipment_category_id' => $request->equipment_category_id,
            'customer_id' => $job->customer_id,
            'vessel_id' => $job->vessel_id,
            'job_number' => $job->job_number,
            'issue_date' => $request->issue_date,
            'expiry_date' => $request->expiry_date,
            'assigned_verifier_id' => $request->assigned_verifier_id,
        ]);
        
        // Notify if new verifier assigned
        if ($request->assigned_verifier_id && $request->assigned_verifier_id != $oldVerifierId) {
            $verifier = \App\Models\User::find($request->assigned_verifier_id);
            if ($verifier) {
                $verifier->notify(new \App\Notifications\CertificateVerificationAssigned($certificate));
            }
            // Reset verification status if re-assigned
            // User requirement: "Only after the assigned engineer verifies and approves, the certificate status should be allowed to move forward"
            // We must revert status to DRAFT to allow the verification workflow to proceed
            $certificate->update([
                'verified_at' => null,
                'status' => 'DRAFT'
            ]);
        }

        // Delete existing field values
        $certificate->fieldValues()->delete();

        // Save new field values
        foreach ($template->sections as $section) {
            if ($section->is_repeatable) {
                // Find max instance
                $maxInstance = 0;
                foreach ($section->fields as $field) {
                    foreach ($request->all() as $key => $value) {
                        if (preg_match("/^field_{$field->id}_instance_(\d+)$/", $key, $matches)) {
                            $maxInstance = max($maxInstance, (int)$matches[1]);
                        }
                    }
                }

                // Save each instance
                for ($i = 1; $i <= $maxInstance; $i++) {
                    foreach ($section->fields as $field) {
                        $inputName = "field_{$field->id}_instance_{$i}";
                        if ($request->has($inputName)) {
                            $certificate->fieldValues()->create([
                                'certificate_template_field_id' => $field->id,
                                'section_instance' => $i,
                                'value' => $request->input($inputName),
                            ]);
                        }
                    }
                }
            } else {
                // Non-repeatable
                foreach ($section->fields as $field) {
                    $inputName = "field_{$field->id}";
                    if ($request->has($inputName)) {
                        $certificate->fieldValues()->create([
                            'certificate_template_field_id' => $field->id,
                            'section_instance' => 1,
                            'value' => $request->input($inputName),
                        ]);
                    }
                }
            }
        }

        return redirect()->route('admin.certificates.show', $certificate)->with('success', 'Certificate updated successfully.');
    }

    public function submit(Certificate $certificate)
    {
        $this->authorize('update', $certificate);
        if($certificate->status !== 'DRAFT') {
            return back()->with('error', 'Only draft certificates can be submitted.');
        }

        // Verification Check - DISABLED per user request (Eng 1 submits directly to Pending for Eng 2 to Approve)
        // if ($certificate->assigned_verifier_id && !$certificate->isVerified()) {
        //     return back()->with('error', 'Certificate must be verified by the assigned engineer before submission.');
        // }

        $certificate->update(['status' => 'PENDING']);

        return redirect()->route('admin.certificates.index', ['tab' => 'drafts'])
            ->with('success', 'Certificate submitted successfully.');
    }

    public function verify(Certificate $certificate)
    {
        $this->authorize('update', $certificate);
        if (auth()->id() !== $certificate->assigned_verifier_id) {
            return back()->with('error', 'You are not the assigned verifier for this certificate.');
        }

        $certificate->update([
            'verified_at' => now(), 
            'status' => 'PENDING' // Auto-submit to pending so it can be approved immediately
        ]);

        return back()->with('success', 'Certificate Verified & Submitted. You can now approve it.');
    }

    public function approve(Certificate $certificate)
    {
        $this->authorize('update', $certificate);
        // Allow Admin, Manager, PRO, or Engineer (Direct Issue)
        if(!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('manager') && !auth()->user()->hasRole('pro') && !auth()->user()->hasRole('engineer')) {
            return back()->with('error', 'Unauthorized to issue certificates.');
        }

        // Prevent self-approval for Engineers (Creator != Approver)
        if (auth()->user()->hasRole('engineer') && $certificate->created_by === auth()->id()) {
             // Exception: Admins/Managers might be able to override, but strictly for Engineers:
             return back()->with('error', 'You cannot approve a certificate you created. Please wait for another engineer.');
        }

        // Allow approving Pending OR Draft (Direct Issue)
        if($certificate->status !== 'PENDING' && $certificate->status !== 'DRAFT') {
            return back()->with('error', 'Certificate must be Draft or Pending to issue.');
        }

        $type = $certificate->type;
        $year = date('Y');
        $seq = str_pad(Certificate::whereYear('issue_date', $year)->where('status', 'ISSUED')->count() + 1, 4, '0', STR_PAD_LEFT);
        
        $certificate->update([
            'status' => 'ISSUED',
            'certificate_number' => "LMQ-{$year}-" . strtoupper(Str::limit($type->name ?? 'GEN', 3, '')) . "-{$seq}",
            'issue_date' => now(),
            'expiry_date' => now()->addMonths($type->validity_months ?? 12),
            'approved_by' => auth()->id()
        ]);

        return redirect()->route('admin.certificates.index', ['tab' => 'issued'])
            ->with('success', 'Certificate Issued Successfully.');
    }

    public function reject(Request $request, Certificate $certificate)
    {
        $this->authorize('update', $certificate);
        if(!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('manager') && !auth()->user()->hasRole('pro')) {
            return back()->with('error', 'Unauthorized to reject certificates.');
        }

        if($certificate->status !== 'PENDING') {
            return back()->with('error', 'Certificate is not pending approval.');
        }

        $request->validate(['rejection_reason' => 'required|string|max:255']);

        // Merge rejection reason into data array
        $data = $certificate->data ?? [];
        $data['rejection_reason'] = $request->rejection_reason;
        $data['rejected_at'] = now()->toDateTimeString();
        $data['rejected_by_name'] = auth()->user()->name;

        $certificate->update([
            'status' => 'REJECTED',
            'data' => $data
        ]);

        return back()->with('success', 'Certificate Rejected.');
    }

    public function renew(Certificate $certificate)
    {
        $this->authorize('create', Certificate::class);
        // Replicate the certificate with basic fields
        $newCertificate = $certificate->replicate([
            'certificate_number', 
            'issue_date', 
            'expiry_date', 
            'assigned_verifier_id', 
            'verified_at', 
            'approved_by', 
            'issued_by',
            'status',
            'created_by'
        ]);

        $newCertificate->status = 'DRAFT';
        $newCertificate->created_by = auth()->id();
        $newCertificate->save();

        // Replicate Field Values
        foreach ($certificate->fieldValues as $fieldValue) {
            $newFieldValue = $fieldValue->replicate(['certificate_id']);
            $newFieldValue->certificate_id = $newCertificate->id;
            $newFieldValue->save();
        }

        // Replicate Certificate Items if any (though usually fields handle this, check relationships)
        foreach ($certificate->items as $item) {
             $newItem = $item->replicate(['certificate_id']);
             $newItem->certificate_id = $newCertificate->id;
             $newItem->save();
        }

        return redirect()->route('admin.certificates.edit', $newCertificate)
            ->with('success', 'Certificate renewed successfully. Please review and update details.');
    }

    public function verifyPublic($number)
    {
        $certificate = Certificate::where('certificate_number', $number)->firstOrFail();
        
        // Check expiry
        $isExpired = $certificate->expiry_date < now();

        return view('public.verify', compact('certificate', 'isExpired'));
    }

    public function pdf(Certificate $certificate)
    {
        $this->authorize('view', $certificate);
        if($certificate->status !== 'ISSUED') {
            return back()->with('error', 'Certificate must be ISSUED to generate PDF.');
        }

        $certificate->load(['type', 'customer', 'vessel', 'job', 'creator', 'approver', 'equipmentCategory', 'items', 'certificateTemplate.sections.fields', 'fieldValues.definition', 'issuedBy', 'assignedVerifier']);

        $qrUrl = route('certificates.verify-public', $certificate->certificate_number);
        // Use a reliable QR API for now
        $qrImage = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($qrUrl);
        $qrCodeBase64 = null;
        try {
            $qrCodeBase64 = base64_encode(file_get_contents($qrImage));
        } catch (\Exception $e) {
            // Log error or ignore
        }

        // ------------------------------------------------------------------------
    // STRICT MULTI-PDF MERGE ARCHITECTURE (FAIL-SAFE)
    // ------------------------------------------------------------------------
    
    // 1. Generate PDF A: Certificate Summary (Page 1 Sealed)
    $pdfSummary = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.certificates.print_summary', compact('certificate', 'qrCodeBase64'));
    $summaryPath = storage_path('app/temp_summary_' . $certificate->id . '.pdf');
    $pdfSummary->save($summaryPath);

    // 2. Generate PDF B: Cylinder Details (Page 2+ Flowing)
    $hasRepeatableSections = $certificate->certificateTemplate 
        && $certificate->certificateTemplate->sections->where('is_repeatable', true)->count() > 0;
    
    $detailsPath = null;
    
    if ($hasRepeatableSections) {
        $pdfDetails = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.certificates.print_details', compact('certificate'));
        $detailsPath = storage_path('app/temp_details_' . $certificate->id . '.pdf');
        $pdfDetails->save($detailsPath);
    }

    // 3. Merge PDFs using FPDI
    $pdf = new \setasign\Fpdi\Fpdi();
    
    // Import Summary (Page A)
    // Summary is strictly 1 page
    $pageCount = $pdf->setSourceFile($summaryPath);
    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
        $templateId = $pdf->importPage($pageNo);
        $pdf->AddPage('P', 'A4'); // P = Portrait
        $pdf->useTemplate($templateId);
    }
    
    // Import Details (Page B+)
    // Details can be multiple pages
    if ($detailsPath && file_exists($detailsPath)) {
        $pageCount = $pdf->setSourceFile($detailsPath);
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $pdf->AddPage('P', 'A4');
            $pdf->useTemplate($templateId);
        }
    }

    // 4. Output and Clean Up
    // Ensure temp files are deleted
    if (file_exists($summaryPath)) unlink($summaryPath);
    if ($detailsPath && file_exists($detailsPath)) unlink($detailsPath);

    return response($pdf->Output('S'), 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="' . $certificate->certificate_number . '.pdf"');
    }
}
