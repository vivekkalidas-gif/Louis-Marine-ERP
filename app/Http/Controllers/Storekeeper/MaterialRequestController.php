<?php

namespace App\Http\Controllers\Storekeeper;

use App\Http\Controllers\Controller;
use App\Models\MaterialRequest;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class MaterialRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = MaterialRequest::with(['job', 'requester', 'inventoryItem']);
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'pending'); // Default to pending
        }
        
        $requests = $query->latest()->paginate(15);
        
        return view('storekeeper.material-requests.index', compact('requests'));
    }

    public function show(MaterialRequest $materialRequest)
    {
        $materialRequest->load(['job', 'requester', 'inventoryItem']);
        return view('storekeeper.material-requests.show', compact('materialRequest'));
    }

    public function approve(Request $request, MaterialRequest $materialRequest)
    {
        $request->validate([
            'admin_remarks' => 'nullable|string',
        ]);

        if (!$materialRequest->inventory_item_id) {
            // For non-inventory items, just approve/issue (no stock deduction)
            $materialRequest->update([
                'status' => 'issued',
                'admin_remarks' => $request->admin_remarks,
            ]);
            return redirect()->route('storekeeper.material-requests.index')
                ->with('success', 'Material request issued successfully.');
        }

        $item = InventoryItem::find($materialRequest->inventory_item_id);
        
        // Detailed Stock Check
        if (!$item) {
             return back()->with('error', 'Inventory item not found.');
        }

        if ($item->current_stock < $materialRequest->quantity) {
            return back()->with('error', 'Insufficient stock. Available: ' . $item->current_stock);
        }

        // Deduct Stock
        $newBalance = $item->current_stock - $materialRequest->quantity;
        
        $item->update(['current_stock' => $newBalance]);

        // Create Stock Movement
        StockMovement::create([
            'inventory_item_id' => $item->id,
            'movement_type' => 'outward',
            'reference_type' => MaterialRequest::class,
            'reference_id' => $materialRequest->id,
            'quantity' => -$materialRequest->quantity,
            'balance_after' => $newBalance,
            'remarks' => 'Issued for Job: ' . ($materialRequest->job->job_number ?? 'N/A'),
            'created_by' => auth()->id(),
        ]);

        $materialRequest->update([
            'status' => 'issued',
            'admin_remarks' => $request->admin_remarks,
        ]);

        return redirect()->route('storekeeper.material-requests.index')
            ->with('success', 'Material request approved and issued successfully.');
    }

    public function reject(Request $request, MaterialRequest $materialRequest)
    {
        $request->validate([
            'admin_remarks' => 'required|string',
        ]);

        $materialRequest->update([
            'status' => 'rejected',
            'admin_remarks' => $request->admin_remarks,
        ]);

        return redirect()->route('storekeeper.material-requests.index')
            ->with('success', 'Material request rejected.');
    }

    public function issue(Request $request, MaterialRequest $materialRequest)
    {
        if ($materialRequest->status !== 'approved') {
            return back()->with('error', 'Only approved requests can be issued.');
        }

        if (!$materialRequest->inventory_item_id) {
            return back()->with('error', 'Cannot issue non-inventory items automatically.');
        }

        $item = InventoryItem::findOrFail($materialRequest->inventory_item_id);
        
        if ($item->current_stock < $materialRequest->quantity) {
            return back()->with('error', 'Insufficient stock.');
        }

        $newBalance = $item->current_stock - $materialRequest->quantity;

        // Create stock movement
        StockMovement::create([
            'inventory_item_id' => $item->id,
            'movement_type' => 'outward',
            'reference_type' => MaterialRequest::class,
            'reference_id' => $materialRequest->id,
            'quantity' => -$materialRequest->quantity,
            'balance_after' => $newBalance,
            'remarks' => 'Issued for Job: ' . ($materialRequest->job->job_number ?? 'N/A'),
            'created_by' => auth()->id(),
        ]);

        $item->update(['current_stock' => $newBalance]);
        
        $materialRequest->update(['status' => 'issued']);

        return redirect()->route('storekeeper.material-requests.index')
            ->with('success', 'Material issued successfully.');
    }
}
