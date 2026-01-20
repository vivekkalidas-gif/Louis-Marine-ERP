<?php

namespace App\Http\Controllers\Engineer;

use App\Http\Controllers\Controller;
use App\Models\WorkshopTool;
use App\Models\ToolRequest;
use Illuminate\Http\Request;

class ToolRequestController extends Controller
{
    /**
     * Display My Tools (Active and History).
     */
    public function index()
    {
        $myRequests = ToolRequest::with('tool')
            ->myRequests()
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('engineer.tools.index', compact('myRequests'));
    }

    /**
     * Show form to request a new tool.
     */
    public function create(Request $request)
    {
        // List available tools grouped by Type
        $search = $request->get('search');
        
        $query = WorkshopTool::where('is_active', true)
            ->whereDoesntHave('currentRequest'); // Available tools only

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }
        
        $tools = $query->orderBy('name')->get();

        // Group by Name for the view -> 'Spanner' => [Tool1, Tool2]
        $groupedTools = $tools->groupBy('name');
            
        return view('engineer.tools.create', compact('groupedTools', 'search'));
    }

    /**
     * Submit a request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_item_id' => 'required|exists:workshop_tools,id',
            'notes' => 'nullable|string'
        ]);
        
        $toolId = $validated['equipment_item_id'];

        // Double check availability
        $tool = WorkshopTool::find($toolId);
        if ($tool->currentRequest()->exists()) {
            return back()->with('error', 'This tool is currently checked out.');
        }
        
        // Check if I already requested this item and it's pending
        $existing = ToolRequest::where('user_id', auth()->id())
            ->where('workshop_tool_id', $tool->id)
            ->pending()
            ->exists();
            
        if ($existing) {
             return back()->with('error', 'You already have a pending request for this tool.');
        }

        ToolRequest::create([
            'user_id' => auth()->id(),
            'workshop_tool_id' => $tool->id,
            'status' => ToolRequest::STATUS_PENDING,
            'requested_at' => now(),
            'notes' => $request->notes
        ]);

        return redirect()->route('engineer.tools.index')->with('success', 'Tool requested successfully. Status is Pending.');
    }
}
