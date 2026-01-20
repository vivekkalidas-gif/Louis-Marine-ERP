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
        // Authorize resource actions using ServiceJobPolicy
        $this->authorizeResource(ServiceJob::class, 'job');
    }

    public function index(Request $request)
    {
        // Show all jobs for engineers (they can view all)
        $query = ServiceJob::with(['customer', 'vessel', 'engineer'])
                    ->orderBy('created_at', 'desc');

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        $jobs = $query->paginate(10);
                    
        return view('engineer.jobs.index', compact('jobs'));
    }

    public function show(ServiceJob $job)
    {
        $job->load([
            'customer', 
            'vessel', 
            'assignments.user.roles',  // Eager load roles to prevent lazy loading
            'materials', 
            'documents', 
            'requestedBy', 
            'activeReport',
            'items', // Job Items (Delivery Note)
            'equipment.equipmentItem' // Job Equipment (Master Data)
        ]);
        return view('engineer.jobs.show', compact('job'));
    }

    public function deliveryNote(ServiceJob $job)
    {
        // Only allow delivery note for Workshop Service jobs
        if ($job->job_type !== 'Workshop Service') {
            abort(404, 'Delivery note only available for Workshop Service jobs');
        }

        $job->load(['customer', 'vessel', 'equipment.equipmentItem']);
        return view('engineer.jobs.delivery-note', compact('job'));
    }

    public function create()
    {
        $customers = Customer::all();
        $vessels = Vessel::where('status', 'active')->get();
        $contacts = \App\Models\CustomerContact::all();
        
        $engineers = \App\Models\User::whereHas('roles', function($q) {
            $q->where('slug', 'engineer');
        })->get();

        $technicians = \App\Models\User::whereHas('roles', function($q) {
            $q->whereIn('slug', ['technician', 'engineer']);
        })->get();

        // Use Equipment Master instead of Scope Categories
        $equipmentCategories = \App\Models\EquipmentCategory::with(['items' => function($q) {
            $q->where('is_active', true);
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
            // Delivery Note fields (required for Workshop Service)
            'delivered_by' => 'required_if:job_type,Workshop Service',
            'delivery_contact_person' => 'required_if:job_type,Workshop Service',
            'delivery_contact_number' => 'required_if:job_type,Workshop Service',
            'delivery_notes' => 'nullable',
        ]);

        // Generate Job Number
        $year = date('Y');
        $lastJob = ServiceJob::where('job_number', 'like', "JOB-$year-%")->orderBy('id', 'desc')->first();
        $sequence = $lastJob ? intval(substr($lastJob->job_number, -4)) + 1 : 1;
        $jobNumber = "JOB-$year-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);

        // Generate Delivery Note Number for Workshop Service
        $deliveryNoteNumber = null;
        if ($validated['job_type'] === 'Workshop Service') {
            $lastDN = ServiceJob::where('delivery_note_number', 'like', "DN-$year-%")
                ->orderBy('id', 'desc')->first();
            $dnSequence = $lastDN ? intval(substr($lastDN->delivery_note_number, -4)) + 1 : 1;
            $deliveryNoteNumber = "DN-$year-" . str_pad($dnSequence, 4, '0', STR_PAD_LEFT);
        }

        // Generate title if not provided
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
            'scheduled_start' => $validated['scheduled_at'], // Sync for now
            'service_category' => $validated['service_category'] ?? null,
            'service_types' => $validated['service_types'] ?? [],
            'scope_of_work' => $validated['scope_of_work'] ?? null,
            'special_instructions' => $validated['special_instructions'],
            'location_details' => $validated['location_details'],
            'reference_no' => $validated['reference_no'],
            'assigned_engineer_id' => $validated['assigned_engineer_id'] ?? auth()->id(),
            'created_by' => auth()->id(),
            // Delivery Note fields (for Workshop Service)
            'delivery_note_number' => $deliveryNoteNumber,
            'delivered_by' => $validated['delivered_by'] ?? null,
            'delivery_contact_person' => $validated['delivery_contact_person'] ?? null,
            'delivery_contact_number' => $validated['delivery_contact_number'] ?? null,
            'delivery_notes' => $validated['delivery_notes'] ?? null,
            'delivery_received_at' => ($validated['job_type'] === 'Workshop Service') ? now() : null,
        ]);

        // Assign Technicians
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

        // Material Requests
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

        // Equipment Items (replaces Scope Items)
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

        // Attachments
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
