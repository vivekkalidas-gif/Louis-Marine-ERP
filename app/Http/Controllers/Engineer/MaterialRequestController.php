<?php

namespace App\Http\Controllers\Engineer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryItem; // Correct Model

class MaterialRequestController extends Controller
{
    public function index()
    {
        $requests = \Illuminate\Support\Facades\DB::table('material_requests')
            ->where('requested_by', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('engineer.materials.index', compact('requests'));
    }
    public function create()
    {
        $products = InventoryItem::where('status', 'active')->get(); // Use InventoryItem
        return view('engineer.materials.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:inventory_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'urgency' => 'required',
            'service_job_id' => 'nullable|exists:service_jobs,id'
        ]);

        foreach ($request->items as $itemData) {
            $inventoryItem = InventoryItem::findOrFail($itemData['product_id']);

            \App\Models\MaterialRequest::create([
                'service_job_id' => $request->service_job_id,
                'requested_by' => auth()->id(),
                'inventory_item_id' => $inventoryItem->id,
                'item_name' => $inventoryItem->item_name,
                'quantity' => $itemData['quantity'],
                'urgency' => $request->urgency,
                'status' => 'pending'
            ]);
        }

        if($request->service_job_id) {
             return redirect()->route('engineer.jobs.show', $request->service_job_id)->with('success', 'Material Requests submitted successfully.');
        }

        return redirect()->route('engineer.dashboard')->with('success', 'Material Requests submitted successfully.');
    }

    public function createForJob(Request $request, $jobId)
    {
        $job = \App\Models\ServiceJob::findOrFail($jobId);
        $products = InventoryItem::where('status', 'active')->get(); // Use InventoryItem
        return view('engineer.materials.create', compact('products', 'job'));
    }
}
