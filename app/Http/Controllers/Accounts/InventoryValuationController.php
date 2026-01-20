<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;

class InventoryValuationController extends Controller
{
    public function valuation()
    {
        // Snapshot of current stock value
        $items = InventoryItem::with(['inventoryAccount'])
            ->where('current_stock', '!=', 0) // Or > 0 only? Stick to non-zero.
            ->paginate(50);
            
        // Calculate Total Value of Inventory (Page or Global? Global is better for KPI)
        $totalValue = InventoryItem::sum(\DB::raw('current_stock * purchase_price')); // Assuming purchase_price is WAC
        
        return view('accounts.inventory.valuation', compact('items', 'totalValue'));
    }

    public function ledger(Request $request)
    {
        $query = InventoryTransaction::with(['item', 'user', 'journalEntry'])
            ->orderBy('created_at', 'desc');
            
        if ($request->has('item_id')) {
            $query->where('inventory_item_id', $request->item_id);
        }
        
        $transactions = $query->paginate(50);
        $items = InventoryItem::orderBy('item_name')->get();
        
        return view('accounts.inventory.ledger', compact('transactions', 'items'));
    }
}
