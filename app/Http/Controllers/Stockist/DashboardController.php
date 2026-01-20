<?php

namespace App\Http\Controllers\Stockist;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Fetch products WITHOUT price column for security
        $query = Product::select('id', 'sku', 'name', 'inventory_category_id', 'current_stock', 'min_stock_level', 'location')
            ->with('category:id,name');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->paginate(20);
        
        return view('stockist.dashboard', compact('products'));
    }

    public function transaction(Request $request, Product $product)
    {
        $request->validate([
            'type' => 'required|in:IN,OUT,DAMAGE',
            'quantity' => 'required|integer|min:1',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request, $product) {
            $type = $request->type === 'DAMAGE' ? InventoryTransaction::TYPE_OUT : $request->type;
            $notes = $request->notes;
            
            if ($request->type === 'DAMAGE') {
                $notes = "[DAMAGED] " . $notes;
            }

            // Create Transaction
            InventoryTransaction::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'type' => $type,
                'quantity' => $request->quantity,
                'reference' => $request->reference,
                'notes' => $notes
            ]);

            // Update Stock
            if ($type === InventoryTransaction::TYPE_IN) {
                $product->increment('current_stock', $request->quantity);
            } else {
                if ($product->current_stock < $request->quantity) {
                    throw new \Exception("Insufficient stock to issue/remove items.");
                }
                $product->decrement('current_stock', $request->quantity);
            }
        });

        return back()->with('success', 'Stock updated successfully.');
    }
}
