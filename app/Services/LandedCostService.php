<?php

namespace App\Services;

use App\Models\Accounting\LandedCost;
use App\Models\Accounting\LandedCostAllocation;
use App\Models\PurchaseOrder;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;

class LandedCostService
{
    protected $stockService;

    public function __construct(StockValuationService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Calculate and save allocations based on lines
     */
    public function allocateCosts(LandedCost $landedCost)
    {
        // 1. Get PO Items
        // Assuming PO has items via 'items' relationship
        $poItems = $landedCost->purchaseOrder->items;
        if ($poItems->isEmpty()) return;

        $allocations = []; // item_id => total_allocated_amount

        // Initialize
        foreach ($poItems as $item) {
            $allocations[$item->inventory_item_id] = 0;
        }

        // 2. Iterate Lines and Allocate
        foreach ($landedCost->lines as $line) {
            $amount = $line->amount;
            
            if ($line->allocation_method == 'value') {
                $totalValue = $poItems->sum('total_price');
                if ($totalValue > 0) {
                    foreach ($poItems as $item) {
                        $ratio = $item->total_price / $totalValue;
                        $allocations[$item->inventory_item_id] += $amount * $ratio;
                    }
                }
            } elseif ($line->allocation_method == 'quantity') {
                $totalQty = $poItems->sum('quantity');
                if ($totalQty > 0) {
                    foreach ($poItems as $item) {
                        $ratio = $item->quantity / $totalQty;
                        $allocations[$item->inventory_item_id] += $amount * $ratio;
                    }
                }
            }
            // Manual method would require input, skipping for auto-allocate MVP
        }

        // 3. Save Allocations
        // Clear old allocations if re-running
        $landedCost->allocations()->delete();
        
        foreach ($allocations as $itemId => $allocatedAmount) {
            if ($allocatedAmount > 0) {
                LandedCostAllocation::create([
                    'landed_cost_id' => $landedCost->id,
                    'inventory_item_id' => $itemId,
                    'amount' => $allocatedAmount
                ]);
            }
        }
    }

    /**
     * Post the Landed Cost - Update Stock Value
     */
    public function post(LandedCost $landedCost)
    {
        return DB::transaction(function () use ($landedCost) {
            $this->allocateCosts($landedCost); // Ensure fresh allocation
            
            $landedCost->load('allocations.item'); // Eager load to prevent lazy loading exception
            
            foreach ($landedCost->allocations as $allocation) {
                // Call Stock Service to add value WITHOUT adding quantity
                // We use a special type 'LANDED_COST' or just 'IN' with qty 0?
                // StockValuationService needs to support this.
                
                $this->stockService->processTransaction(
                    $allocation->item,
                    'LANDED_COST', // New Type
                    0, // Qty
                    0, // Unit Cost (Not relevant for Landed Cost, it's total value add)
                    $landedCost->reference ?? 'LC-' . $landedCost->id,
                    'Landed Cost Allocation',
                    $allocation->amount // Pass total value increase explicitly? Method sig needs update.
                );
            }
            
            $landedCost->status = 'posted';
            $landedCost->save();
        });
    }
}
