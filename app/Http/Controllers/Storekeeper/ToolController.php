<?php

namespace App\Http\Controllers\Storekeeper;

use App\Http\Controllers\Controller;
use App\Models\WorkshopTool;
use App\Models\ToolRequest;
use App\Models\User;
use Illuminate\Http\Request;

class ToolController extends Controller
{
    /**
     * Display a listing of tools.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        
        $query = WorkshopTool::with(['currentRequest.requester'])
            ->where('is_active', true)
            ->orderBy('name'); // Group naturally by name

        if ($status === 'issued') {
            $query->whereHas('currentRequest');
        } elseif ($status === 'available') {
            $query->whereDoesntHave('currentRequest');
        }
        
        // Filter by text search if needed
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        $tools = $query->paginate(15);
        $engineers = User::role('engineer')->get();

        return view('storekeeper.tools.index', compact('tools', 'engineers', 'status'));
    }

    /**
     * Show the form for creating a new tool.
     */
    public function create()
    {
        // No categories needed
        return view('storekeeper.tools.create');
    }

    /**
     * Store a newly created tool.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255', // E.g., Spanner, Drill
            'code' => 'nullable|string|max:50|unique:workshop_tools,code',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:20',
        ]);
        
        // Auto-generate code if missing
        if (empty($validated['code'])) {
            $prefix = strtoupper(substr($validated['name'], 0, 3));
            $count = WorkshopTool::where('name', $validated['name'])->count() + 1;
            $validated['code'] = $prefix . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        }

        WorkshopTool::create($validated);

        return redirect()->route('storekeeper.tools.index')->with('success', 'Tool added successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(WorkshopTool $tool)
    {
        return view('storekeeper.tools.edit', compact('tool'));
    }

    /**
     * Update tool.
     */
    public function update(Request $request, WorkshopTool $tool)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:workshop_tools,code,' . $tool->id,
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:20',
        ]);

        $tool->update($validated);

        return redirect()->route('storekeeper.tools.index')->with('success', 'Tool updated successfully.');
    }

    /**
     * Remove tool.
     */
    public function destroy(WorkshopTool $tool)
    {
        if ($tool->currentRequest()->exists()) {
            return back()->with('error', 'Cannot delete a tool that is currently checked out.');
        }
        
        $tool->delete();
        return redirect()->route('storekeeper.tools.index')->with('success', 'Tool deleted successfully.');
    }

    // --- REQUEST MANAGEMENT ---

    public function requests()
    {
        $requests = ToolRequest::with(['requester', 'tool'])
            ->whereIn('status', [ToolRequest::STATUS_PENDING, ToolRequest::STATUS_APPROVED])
            ->orderBy('requested_at', 'asc')
            ->get();
            
        return view('storekeeper.tools.requests', compact('requests'));
    }

    public function approve(Request $request, ToolRequest $toolRequest)
    {
        if ($toolRequest->status !== ToolRequest::STATUS_PENDING) {
            return back()->with('error', 'Request is not pending.');
        }
        
        $toolRequest->update([
            'status' => ToolRequest::STATUS_APPROVED,
        ]);

        return back()->with('success', 'Request approved. Ready to issue.');
    }

    public function issue(Request $request, ToolRequest $toolRequest)
    {
        if ($toolRequest->status !== ToolRequest::STATUS_APPROVED) {
            return back()->with('error', 'Request must be approved before issuing.');
        }
        
        // Check if item is already issued to someone else (concurrency check)
        if ($toolRequest->tool->currentRequest()->exists()) {
            return back()->with('error', 'This tool is already checked out to someone else.');
        }

        $toolRequest->update([
            'status' => ToolRequest::STATUS_ISSUED,
            'issued_by' => auth()->id(),
            'issued_at' => now(),
            'notes' => $request->notes
        ]);

        return back()->with('success', 'Tool issued successfully.');
    }
    
    public function reject(Request $request, ToolRequest $toolRequest)
    {
        $toolRequest->update([
            'status' => ToolRequest::STATUS_REJECTED,
            'rejection_reason' => $request->rejection_reason ?? 'Rejected by storekeeper',
            'returned_at' => now() 
        ]);
        
        return back()->with('success', 'Request rejected.');
    }

    public function returnItem(Request $request, ToolRequest $toolRequest)
    {
        $toolRequest->update([
            'status' => ToolRequest::STATUS_RETURNED,
            'received_by' => auth()->id(),
            'returned_at' => now(),
            'notes' => $toolRequest->notes . "\nReturn Notes: " . $request->notes
        ]);

        return back()->with('success', 'Tool marked as returned.');
    }
}
