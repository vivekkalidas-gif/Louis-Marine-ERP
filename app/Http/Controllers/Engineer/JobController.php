<?php

namespace App\Http\Controllers\Engineer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceJob;
use App\Models\Customer;
use App\Models\Vessel;

class JobController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(ServiceJob::class, 'job');
    }

    public function index(Request $request)
    {
        $query = ServiceJob::with([
                'customer:id,company_name,email', 
                'vessel:id,name,imo_number', 
                'engineer:id,name'
            ])
            ->orderBy('created_at', 'desc');

        if ($request->has('status') && !empty($request->status)) {
            if ($request->status === 'today') {
                $query->whereDate('scheduled_at', now()->startOfDay());
            } elseif ($request->status === 'overdue') {
                $query->where('scheduled_at', '<', now()->startOfDay())
                      ->whereNotIn('status', ['completed', 'cancelled']);
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('job_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('company_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('vessel', function($vq) use ($search) {
                      $vq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $jobs = $query->paginate(15);
                    
        return view('engineer.jobs.index', compact('jobs'));
    }

    public function show(ServiceJob $job)
    {
        $job->load([
            'customer:id,company_name,email,phone,city,country', 
            'vessel:id,name,imo_number,type,flag', 
            'assignments.user.roles',
            'materials', 
            'documents', 
            'requestedBy', 
            'activeReport',
            'items',
            'equipment.equipmentItem'
        ]);
        return view('engineer.jobs.show', compact('job'));
    }

    public function deliveryNote(ServiceJob $job)
    {
        if ($job->job_type !== 'Workshop Service') {
            abort(404, 'Delivery note only available for Workshop Service jobs');
        }

        $job->load(['customer', 'vessel', 'equipment.equipmentItem']);
        return view('engineer.jobs.delivery-note', compact('job'));
    }

    public function pdf(ServiceJob $job, Request $request)
    {
        $job->load([
            'customer', 
            'vessel', 
            'engineer', 
            'assignments.user', 
            'requestedBy', 
            'equipment.equipmentItem', 
            'materials'
        ]);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('engineer.jobs.pdf', compact('job'));
        $filename = 'Job-Order-' . str_replace(['/', '\\', ' '], '-', $job->job_number) . '.pdf';
        
        if ($request->has('download')) {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }

    public function create()
    {
        // Lightweight column selects for fast form rendering
        $customers = Customer::select(['id', 'company_name', 'email'])->orderBy('company_name')->get();
        $vessels = Vessel::select(['id', 'name', 'customer_id', 'imo_number'])->where('status', 'active')->orderBy('name')->get();
        $contacts = \App\Models\CustomerContact::select(['id', 'customer_id', 'name', 'email', 'phone'])->get();
        
        $engineers = \App\Models\User::select(['id', 'name', 'email'])
            ->whereHas('roles', function($q) {
                $q->where('slug', 'engineer');
            })->get();

        $technicians = \App\Models\User::select(['id', 'name', 'email'])
            ->whereHas('roles', function($q) {
                $q->whereIn('slug', ['technician', 'engineer']);
            })->get();

        $equipmentCategories = \App\Models\EquipmentCategory::with(['items' => function($q) {
            $q->select(['id', 'category_id', 'name', 'code', 'brand', 'model'])->where('is_active', true);
        }])->where('is_active', true)->ordered()->get();

        return view('engineer.jobs.create', compact('customers', 'vessels', 'engineers', 'technicians', 'contacts', 'equipmentCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_type' => 'required',
            'priority' => 'required',
            'scheduled_at' => 'nullable|date',
            'job_source' => 'nullable',
            'customer_id' => 'required|exists:customers,id',
            'vessel_id' => 'nullable|exists:vessels,id',
            'requested_by_contact_id' => 'nullable|exists:customer_contacts,id',
            'service_category' => 'nullable',
            'scope_of_work' => 'nullable',
            'service_types' => 'nullable|array',
            'assigned_engineer_id' => 'nullable|exists:users,id',
            'location_details' => 'nullable',
            'special_instructions' => 'nullable',
            'reference_no' => 'nullable',
            'title' => 'nullable',
            'technicians' => 'nullable|array',
            'materials' => 'nullable|array',
            'equipment' => 'nullable|array',
            'equipment.*.id' => 'sometimes|exists:equipment_items,id',
            'equipment.*.equipment_item_id' => 'sometimes|exists:equipment_items,id',
            'equipment.*.quantity' => 'sometimes|integer|min:1',
            'attachments.*' => 'nullable|file|max:10240',
            'delivered_by' => 'required_if:job_type,Workshop Service',
            'delivery_contact_person' => 'required_if:job_type,Workshop Service',
            'delivery_contact_number' => 'required_if:job_type,Workshop Service',
            'delivery_notes' => 'nullable',
        ]);

        $year = date('Y');
        $lastJob = ServiceJob::where('job_number', 'like', "JOB-$year-%")->orderBy('id', 'desc')->first();
        $sequence = $lastJob ? intval(substr($lastJob->job_number, -4)) + 1 : 1;
        $jobNumber = "JOB-$year-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);

        $deliveryNoteNumber = null;
        if ($validated['job_type'] === 'Workshop Service') {
            $lastDN = ServiceJob::where('delivery_note_number', 'like', "DN-$year-%")
                ->orderBy('id', 'desc')->first();
            $dnSequence = $lastDN ? intval(substr($lastDN->delivery_note_number, -4)) + 1 : 1;
            $deliveryNoteNumber = "DN-$year-" . str_pad($dnSequence, 4, '0', STR_PAD_LEFT);
        }

        $title = $validated['title'] ?? 
                 ($validated['service_category'] ?? 'Service') . ' - ' . 
                 ($request->service_types[0] ?? 'Job');

        $job = ServiceJob::create([
            'job_number' => $jobNumber,
            'title' => $title,
            'customer_id' => $validated['customer_id'],
            'vessel_id' => $validated['vessel_id'],
            'requested_by_contact_id' => $validated['requested_by_contact_id'] ?? null,
            'status' => ServiceJob::STATUS_REQUESTED,
            'priority' => $validated['priority'],
            'job_type' => $validated['job_type'],
            'job_source' => $validated['job_source'],
            'scheduled_at' => $validated['scheduled_at'],
            'scheduled_start' => $validated['scheduled_at'],
            'service_category' => $validated['service_category'] ?? null,
            'service_types' => $validated['service_types'] ?? [],
            'scope_of_work' => $validated['scope_of_work'] ?? null,
            'special_instructions' => $validated['special_instructions'],
            'location_details' => $validated['location_details'],
            'reference_no' => $validated['reference_no'],
            'assigned_engineer_id' => $validated['assigned_engineer_id'] ?? auth()->id(),
            'created_by' => auth()->id(),
            'delivery_note_number' => $deliveryNoteNumber,
            'delivered_by' => $validated['delivered_by'] ?? null,
            'delivery_contact_person' => $validated['delivery_contact_person'] ?? null,
            'delivery_contact_number' => $validated['delivery_contact_number'] ?? null,
            'delivery_notes' => $validated['delivery_notes'] ?? null,
            'delivery_received_at' => ($validated['job_type'] === 'Workshop Service') ? now() : null,
        ]);

        if (!empty($request->technicians)) {
            foreach ($request->technicians as $techId) {
                \App\Models\JobAssignment::create([
                    'service_job_id' => $job->id,
                    'user_id' => $techId,
                    'assigned_by' => auth()->id(),
                    'status' => 'assigned',
                    'assigned_at' => now()
                ]);
            }
        }

        if (!empty($request->materials)) {
            foreach ($request->materials as $mat) {
                if (!empty($mat['name'])) {
                    \App\Models\MaterialRequest::create([
                        'service_job_id' => $job->id,
                        'requested_by' => auth()->id(),
                        'item_name' => $mat['name'],
                        'quantity' => $mat['qty'],
                        'status' => 'pending'
                    ]);
                }
            }
        }

        if (!empty($request->equipment)) {
            foreach ($request->equipment as $equip) {
                \App\Models\JobEquipment::create([
                    'service_job_id' => $job->id,
                    'equipment_item_id' => $equip['equipment_item_id'],
                    'quantity' => $equip['quantity'],
                    'condition_notes' => $equip['remarks'] ?? null
                ]);
            }
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('job-documents', 'public');
                \App\Models\ServiceJobDocument::create([
                    'service_job_id' => $job->id,
                    'uploaded_by' => auth()->id(),
                    'title' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientOriginalExtension(),
                    'file_size' => $file->getSize()
                ]);
            }
        }

        return redirect()->route('engineer.jobs.show', $job->id)
            ->with('success', 'Job created successfully! Job Number: ' . $job->job_number);
    }
}
