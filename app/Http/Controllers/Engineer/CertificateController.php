<?php

namespace App\Http\Controllers\Engineer;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\CertificateFieldValue;
use App\Models\Customer;
use App\Models\ServiceJob;
use App\Models\User;
use App\Models\EquipmentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class CertificateController extends Controller
{
    /**
     * Display a listing of certificates.
     */
    public function index(Request $request)
    {
        $query = Certificate::with(['customer', 'vessel', 'job', 'type', 'verifier', 'creator'])
            ->latest();

        // Filter: Pending Verification (Assigned to Me)
        if ($request->has('filter') && $request->filter === 'to_verify') {
            $query->where('verifier_id', auth()->id())
                  ->where('status', 'PENDING_VERIFICATION');
        } 
        // Filter: My Certificates
        else {
             // Show certificates I created OR I am verifying
            $query->where(function($q) {
                $q->where('created_by', auth()->id())
                  ->orWhere('verifier_id', auth()->id());
            });
        }

        $certificates = $query->paginate(10);
        
        // Count pending internal verifications
        $pendingCount = Certificate::where('verifier_id', auth()->id())
            ->where('status', 'PENDING_VERIFICATION')
            ->count();

        return view('engineer.certificates.index', compact('certificates', 'pendingCount'));
    }

    /**
     * Show the form for creating a new certificate.
     */
    public function create(Request $request)
    {
        $templates = CertificateTemplate::where('is_active', true)->get();
        $customers = Customer::all(); // Should optimize for large db
        $jobs = ServiceJob::where('status', '!=', 'COMPLETED')->latest()->get(); // Active jobs
        
        // Potential Verifiers (Engineers excluding self)
        $verifiers = User::whereHas('roles', function($q) {
            $q->where('slug', 'engineer');
        })->where('id', '!=', auth()->id())->get();

        return view('engineer.certificates.create', compact('templates', 'customers', 'jobs', 'verifiers'));
    }

    /**
     * Store a newly created certificate in storage.
     */
    public function store(Request $request)
    {
        $isDraft = $request->input('action') === 'draft';

        // Basic validation for both actions
        $rules = [
            'certificate_template_id' => 'required|exists:certificate_templates,id',
            'customer_id' => 'required|exists:customers,id',
            'vessel_id' => 'required|exists:vessels,id',
            // Verifier and Issue Date are optional for Drafts
            'verifier_id' => $isDraft ? 'nullable|exists:users,id' : 'required|exists:users,id',
            'issue_date' => $isDraft ? 'nullable|date' : 'required|date',
        ];

        $request->validate($rules);

        // Validation for date logic (only if not draft and dates are present)
        if (!$isDraft && $request->has('expiry_date') && $request->issue_date) {
            $request->validate([
                'expiry_date' => 'after:issue_date',
            ]);
        }

        DB::transaction(function () use ($request, $isDraft) {
            $template = CertificateTemplate::find($request->certificate_template_id);

            $status = $isDraft ? 'DRAFT' : 'PENDING_VERIFICATION';

            $certificate = Certificate::create([
                'certificate_template_id' => $template->id,
                'certificate_type_id' => null, 
                'equipment_category_id' => $template->equipment_category_id,
                'customer_id' => $request->customer_id,
                'vessel_id' => $request->vessel_id,
                'service_job_id' => $request->job_id,
                'verifier_id' => $request->verifier_id,
                
                // Allow Draft status
                'status' => $status,
                'certificate_number' => 'DRAFT-' . rand(1000, 9999),
                
                'issue_date' => $request->issue_date,
                // Auto-calculate expiry if issue date is present
                'expiry_date' => $request->issue_date ? Carbon::parse($request->issue_date)->addYear() : null,
                'created_by' => auth()->id(),
            ]);

            // Handle Dynamic Fields
            if ($request->has('fields')) {
                foreach ($request->fields as $fieldId => $value) {
                    CertificateFieldValue::create([
                        'certificate_id' => $certificate->id,
                        'certificate_template_field_id' => $fieldId,
                        'section_instance' => 1,
                        'value' => is_array($value) ? json_encode($value) : $value,
                    ]);
                }
            }
        });

        $message = $isDraft ? 'Certificate saved as Draft.' : 'Certificate created and sent for verification.';
        return redirect()->route('engineer.certificates.index')->with('success', $message);
    }

    /**
     * Display the specified certificate.
     */
    public function show(Certificate $certificate)
    {
        $certificate->load(['fieldValues.definition.section', 'customer', 'vessel', 'verifier', 'creator']);
        return view('engineer.certificates.show', compact('certificate'));
    }

    /**
     * Show the form for verifying a certificate.
     */
    public function verify(Certificate $certificate)
    {
        // Check authorization
        if ($certificate->verifier_id !== auth()->id()) {
            abort(403, 'You are not assigned to verify this certificate.');
        }

        $certificate->load(['fieldValues.definition.section', 'customer', 'vessel', 'creator']);
        
        return view('engineer.certificates.verify', compact('certificate'));
    }

    /**
     * Approve or Reject the certificate
     */
    public function approve(Request $request, Certificate $certificate)
    {
        if ($certificate->verifier_id !== auth()->id()) {
            abort(403);
        }

        $action = $request->input('action'); // 'approve' or 'reject'
        
        if ($action === 'approve') {
            // Check signature
            if (!auth()->user()->signature_path) {
                return back()->with('error', 'You must upload your digital signature in your profile before approving.');
            }

            $certificate->update([
                'status' => 'ISSUED',
                'verified_at' => now(),
                'issued_by' => auth()->id(), // Verifier issues it? or Creator? Usually the one signing off effectively issues it here.
                'certificate_number' => 'CERT-' . date('Y') . '-' . rand(10000, 99999), // Generate final number
            ]);
            
            return redirect()->route('engineer.certificates.index')->with('success', 'Certificate Approved and Issued.');
        } else {
            $request->validate(['notes' => 'required']);
            
            $certificate->update([
                'status' => 'REJECTED',
                'verification_notes' => $request->notes,
            ]);

            return redirect()->route('engineer.certificates.index')->with('success', 'Certificate Rejected.');
        }
    }
    
    public function edit(Certificate $certificate)
    {
        if ($certificate->created_by !== auth()->id()) {
            abort(403);
        }
        
        if ($certificate->status === 'ISSUED') {
            return redirect()->route('engineer.certificates.show', $certificate)->with('error', 'Cannot edit issued certificate.');
        }

        $certificate->load(['fieldValues', 'certificateTemplate.sections.fields']);
        
        $customers = Customer::all();
        $jobs = ServiceJob::where('status', '!=', 'COMPLETED')->latest()->get();
        // Potential Verifiers
        $verifiers = User::whereHas('roles', function($q) {
            $q->where('slug', 'engineer');
        })->where('id', '!=', auth()->id())->get();

        return view('engineer.certificates.edit', compact('certificate', 'customers', 'jobs', 'verifiers'));
    }

    public function update(Request $request, Certificate $certificate)
    {
        if ($certificate->created_by !== auth()->id()) {
            abort(403);
        }

        if ($certificate->status === 'ISSUED') {
            return back()->with('error', 'Cannot update issued certificate.');
        }

        $isDraft = $request->input('action') === 'draft';
        
        $rules = [
            'customer_id' => 'required|exists:customers,id',
            'vessel_id' => 'required|exists:vessels,id',
            'verifier_id' => $isDraft ? 'nullable|exists:users,id' : 'required|exists:users,id',
            'issue_date' => $isDraft ? 'nullable|date' : 'required|date',
        ];

        $request->validate($rules);

        if (!$isDraft && $request->has('expiry_date') && $request->issue_date) {
             $request->validate(['expiry_date' => 'after:issue_date']);
        }

        DB::transaction(function () use ($request, $isDraft, $certificate) {
             $status = $isDraft ? 'DRAFT' : 'PENDING_VERIFICATION';
             
             $certificate->update([
                'customer_id' => $request->customer_id,
                'vessel_id' => $request->vessel_id,
                'service_job_id' => $request->job_id,
                'verifier_id' => $request->verifier_id,
                'status' => $status,
                'issue_date' => $request->issue_date,
                'expiry_date' => $request->issue_date ? Carbon::parse($request->issue_date)->addYear() : null,
             ]);

             // Update Fields
             if ($request->has('fields')) {
                // Delete old values (simplest approach for now)
                $certificate->fieldValues()->delete();

                foreach ($request->fields as $fieldId => $value) {
                    CertificateFieldValue::create([
                        'certificate_id' => $certificate->id,
                        'certificate_template_field_id' => $fieldId,
                        'section_instance' => 1,
                        'value' => is_array($value) ? json_encode($value) : $value,
                    ]);
                }
            }
        });

        $message = $isDraft ? 'Certificate draft updated.' : 'Certificate updated and sent for verification.';
        return redirect()->route('engineer.certificates.index')->with('success', $message);
    }

    public function download(Certificate $certificate)
    {
         if ($certificate->status !== 'ISSUED') {
             return back()->with('error', 'Certificate is not issued yet.');
         }
         
         $pdf = Pdf::loadView('admin.certificates.pdf', compact('certificate'));
         return $pdf->download("Certificate-{$certificate->certificate_number}.pdf");
    }
}
