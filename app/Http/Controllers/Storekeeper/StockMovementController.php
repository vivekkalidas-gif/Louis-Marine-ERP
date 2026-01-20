<?php

namespace App\Http\Controllers\Storekeeper;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMovement::with(['inventoryItem.category', 'creator', 'reference']);
        
        // Filter by item
        if ($request->filled('item_id')) {
            $query->where('inventory_item_id', $request->item_id);
        }
        
        // Filter by movement type
        if ($request->filled('type')) {
            $query->where('movement_type', $request->type);
        }
        
        $movements = $query->latest()->paginate(20);
        $items = InventoryItem::active()->orderBy('item_name')->get();
        
        return view('storekeeper.stock.index', compact('movements', 'items'));
    }

    public function inward()
    {
        $items = InventoryItem::active()->orderBy('item_name')->get();
        return view('storekeeper.stock.inward', compact('items'));
    }

    public function storeInward(Request $request)
    {
        $validated = $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'quantity' => 'required|integer|min:1',
            'location' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $item = InventoryItem::findOrFail($validated['inventory_item_id']);
        $newBalance = $item->current_stock + $validated['quantity'];

        StockMovement::create([
            'inventory_item_id' => $item->id,
            'movement_type' => 'inward',
            'reference_type' => null,
            'reference_id' => null,
            'quantity' => $validated['quantity'],
            'balance_after' => $newBalance,
            'location' => $validated['location'],
            'remarks' => $validated['remarks'],
            'created_by' => auth()->id(),
        ]);

        $item->update(['current_stock' => $newBalance]);

        return redirect()->route('storekeeper.stock.index')
            ->with('success', 'Stock inward recorded successfully.');
    }

    public function adjustment()
    {
        $items = InventoryItem::active()->orderBy('item_name')->get();
        return view('storekeeper.stock.adjustment', compact('items'));
    }

    public function storeAdjustment(Request $request)
    {
        $validated = $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'adjustment_type' => 'required|in:increase,decrease',
            'quantity' => 'required|integer|min:1',
            'remarks' => 'required|string',
        ]);

        $item = InventoryItem::findOrFail($validated['inventory_item_id']);
        
        $adjustmentQty = $validated['adjustment_type'] === 'increase' 
            ? $validated['quantity'] 
            : -$validated['quantity'];
            
        $newBalance = max(0, $item->current_stock + $adjustmentQty);

        StockMovement::create([
            'inventory_item_id' => $item->id,
            'movement_type' => 'adjustment',
            'reference_type' => null,
            'reference_id' => null,
            'quantity' => $adjustmentQty,
            'balance_after' => $newBalance,
            'remarks' => $validated['remarks'],
            'created_by' => auth()->id(),
        ]);

        $item->update(['current_stock' => $newBalance]);

        return redirect()->route('storekeeper.stock.index')
            ->with('success', 'Stock adjustment recorded successfully.');
    }
}
