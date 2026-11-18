<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceJob;
use App\Models\ServiceJobDocument;
use App\Models\Customer;
use App\Models\Vessel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\EquipmentCategory;

class ServiceJobController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(ServiceJob::class, 'job');
    }

    public function index(Request $request)
    {
        // Simple filter logic
        $query = ServiceJob::with(['customer', 'vessel', 'engineer']);

        if($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $jobs = $query->latest()->paginate(15);
        return view('admin.jobs.index', compact('jobs'));
    }

    public function create()
    {
        $customers = Customer::where('status', 'Active')->orderBy('company_name')->get();
        // Eager load vessels with their customers to prevent lazy loading violation
        $vessels = Vessel::with('customer')->orderBy('name')->get();

        // Load Engineers (assuming role 'engineer' exists)
        $engineers = User::whereHas('roles', function($q){ $q->where('name', 'engineer'); })->get();
        return view('admin.jobs.create', compact('customers', 'engineers', 'vessels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_number' => 'nullable|string',
            'title' => 'required|string',
            'vessel_id' => 'required|exists:vessels,id',
            'priority' => 'required|string',
            'status' => 'sometimes|string',
            'scheduled_start' => 'nullable|date',
            'scheduled_end' => 'nullable|date',
            'sla_due_at' => 'nullable|date',
            'scope_of_work' => 'required|string',
            'assigned_engineer_id' => 'nullable|exists:users,id',
            // New fields
            'job_type' => 'sometimes|string',
            'job_source' => 'sometimes|string',
            'reference_no' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();

        if ($validated['status'] === ServiceJob::STATUS_CLOSED) {
            $validated['closed_at'] = now();
        }

        $job = ServiceJob::create($validated);
        return redirect()->route('admin.jobs.show', $job->id)->with('success', 'Job created successfully.');
    }

    public function show(ServiceJob $job)
    {
        $job->load(['vessel', 'engineer', 'documents', 'notes.author', 'items', 'equipment.equipmentItem']);
        $engineers = User::whereHas('roles', function($q){ $q->where('slug', 'engineer'); })->get();
        return view('admin.jobs.show', compact('job', 'engineers'));
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



    public function edit(ServiceJob $job)
    {
        $job->load(['items', 'equipment.equipmentItem']); // Load Job equipment too
        $vessels = Vessel::with('customer')->get();
        $engineers = User::whereHas('roles', function($q){ $q->where('slug', 'engineer'); })->get();
        $equipmentCategories = EquipmentCategory::with('items')->get();
        
        return view('admin.jobs.edit', compact('job', 'vessels', 'engineers', 'equipmentCategories'));
    }

    public function update(Request $request, ServiceJob $job)
    {
        $this->authorize('update', $job); // Policy handles role check

        $validated = $request->validate([
            'job_number' => 'sometimes|string',
            'title' => 'sometimes|string',
            'vessel_id' => 'sometimes|exists:vessels,id',
            'status' => 'sometimes|string',
            'assigned_engineer_id' => 'sometimes|exists:users,id',
            'priority' => 'sometimes|string',
            'scheduled_start' => 'sometimes|date',
            'scheduled_end' => 'sometimes|date',
            'scope_of_work' => 'sometimes|string',
            'description' => 'sometimes|string',
            // New fields for 5-step wizard
            'job_type' => 'sometimes|string',
            'job_source' => 'sometimes|string',
            'location_details' => 'sometimes|nullable|string',
            'reference_no' => 'sometimes|nullable|string',
            'delivered_by' => 'sometimes|nullable|string',
            'delivery_contact_person' => 'sometimes|nullable|string',
            'delivery_contact_number' => 'sometimes|nullable|string',
            'delivery_notes' => 'sometimes|nullable|string',
        ]);
        
        // Status Logic Protection
        if(isset($validated['status'])) {
             // If closing, ensure admin
             if($validated['status'] == ServiceJob::STATUS_CLOSED && !auth()->user()->hasRole('admin')) {
                  abort(403, 'Only Admins can close jobs.');
             }
             
             // If completing
             if($validated['status'] == ServiceJob::STATUS_COMPLETED && $job->status != ServiceJob::STATUS_COMPLETED) {
                  $validated['completed_at'] = now();
             }
        }
        
        // Auto-update status on assignment change
        if(isset($validated['assigned_engineer_id']) && $job->status == ServiceJob::STATUS_REQUESTED) {
             $validated['status'] = ServiceJob::STATUS_ASSIGNED;
        }

        // Equipment Handling
        if ($request->has('equipment_submitted')) { // Assuming 'equipment_submitted' is a flag from the form
            $submittedItems = collect($request->input('equipment', []));
            $submittedItemIds = $submittedItems->pluck('equipment_item_id')->filter()->toArray();

            // 1. Delete items NOT in the submission
            $job->equipment()->whereNotIn('equipment_item_id', $submittedItemIds)->delete();

            // 2. Update or Create
            foreach ($submittedItems as $itemData) {
                if(!isset($itemData['equipment_item_id'])) continue;
                
                $job->equipment()->updateOrCreate(
                    ['service_job_id' => $job->id, 'equipment_item_id' => $itemData['equipment_item_id']],
                    ['quantity' => $itemData['quantity']]
                );
            }
        }

        $job->update($validated);
        
        // Handle next step redirection
        if ($request->has('next_step')) {
            return redirect()->route('admin.jobs.edit', ['job' => $job->id, 'step' => $request->input('next_step')])->with('success', 'Job Details Updated');
        }

        return redirect()->back()->with('success', 'Job Updated');
    }

    public function destroy(ServiceJob $job)
    {
        $job->delete();
        return redirect()->route('admin.jobs.index')->with('success', 'Job deleted successfully.');
    }

    public function storeNote(Request $request, ServiceJob $job)
    {
        $this->authorize('update', $job);
        $request->validate(['content' => 'required']);
        $job->notes()->create([
            'content' => $request->content,
            'user_id' => auth()->id()
        ]);
        return back()->with('success', 'Note added.');
    }

    public function storeDocument(Request $request, ServiceJob $job)
    {
        $this->authorize('update', $job);
        $request->validate([
            'title' => 'required',
            'file' => 'required|file|max:10240',
        ]);

        $path = $request->file('file')->store('job_documents', 'public');

        $job->documents()->create([
            'title' => $request->title,
            'file_path' => $path,
            'file_type' => $request->file('file')->getClientOriginalExtension(),
        ]);

        return back()->with('success', 'Document attached.');
    }
}
