<?php

namespace App\Services;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Accounting\JournalEntry;
use App\Models\Accounting\AccountingJournal;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockValuationService
{
    /**
     * Process an inventory transaction (IN/OUT) and update valuation.
     */
    public function processTransaction(
        InventoryItem $item, 
        string $type, 
        int $quantity, 
        float $unitCost = 0, 
        $reference = null,
        $notes = null,
        float $explicitTotalCost = 0 // New Argument
    ) {
        return DB::transaction(function () use ($item, $type, $quantity, $unitCost, $reference, $notes, $explicitTotalCost) {
            
            // 1. Calculate Costs
            $totalCost = 0;
            
            if ($type === 'LANDED_COST') {
                $totalCost = $explicitTotalCost;
                $unitCost = 0; 
            } elseif ($type === InventoryTransaction::TYPE_OUT) {
                // For OUT, unit cost depends on costing method (Weighted Avg for now)
                $unitCost = $this->calculateOutCost($item);
                $totalCost = $quantity * $unitCost;
            } else {
                // IN
                $totalCost = $quantity * $unitCost;
            }
            
            // 2. Journal Entry (If Accounts Configured)
            $journalEntryId = null;
            if ($item->inventory_account_id) {
                 // For Landed Cost, Debit Inventory, Credit Clearing (handled by LandedCostService? No, here is better for consistency)
                 // But LandedCost involves multiple items.
                 // Ideally LandedCostService creates ONE Journal Entry for the whole document, not per item.
                 // So we skip JE creation here for Landed Cost OR we link it to the main JE.
                 // Let's assume LandedCostService handles the JE for simplicity/aggregation.
                if ($type !== 'LANDED_COST') {
                    $journalEntryId = $this->createJournalEntry($item, $type, $totalCost, $reference);
                }
            }
            
            // 3. Create Transaction Record
            $currentValue = $this->getCurrentStockValue($item);
            
            if ($type === 'IN' || $type === 'LANDED_COST') {
                 $newValue = $currentValue + $totalCost;
            } else {
                 $newValue = $currentValue - $totalCost;
            }
            
            $transaction = InventoryTransaction::create([
                'inventory_item_id' => $item->id,
                'user_id' => auth()->id() ?? \App\Models\User::first()->id, 
                'type' => $type,
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
                'stock_value_after' => $newValue,
                'reference' => $reference,
                'notes' => $notes,
                'journal_entry_id' => $journalEntryId
            ]);
            
            // 4. Update Item Stock
            if ($type === InventoryTransaction::TYPE_IN) {
                 $oldQty = $item->current_stock;
                 $newQty = $oldQty + $quantity;
                 
                 if ($newQty > 0) {
                     // Standard WAC Formula: (OldValue + NewCost) / NewQty
                     $newAvgCost = ($currentValue + $totalCost) / $newQty;
                     $item->purchase_price = $newAvgCost; 
                 }
                 $item->current_stock += $quantity;
                 
            } elseif ($type === InventoryTransaction::TYPE_OUT) {
                $item->current_stock -= $quantity;
            } elseif ($type === 'LANDED_COST') {
                // Only Value Increases, Qty stays same.
                // New Avg = (OldValue + LandedCost) / OldQty
                $qty = $item->current_stock;
                if ($qty > 0) {
                     $newAvgCost = ($currentValue + $totalCost) / $qty;
                     $item->purchase_price = $newAvgCost;
                }
            }
            
            $item->save();
            
            return $transaction;
        });
    }
    
    public function calculateOutCost(InventoryItem $item)
    {
        // Simple Weighted Average using the 'purchase_price' field as the stored Avg Cost
        // For FIFO, we would query unconsumed IN transactions.
        
        if ($item->costing_method == 'standard') {
            return $item->std_cost;
        }
        
        // Default: Weighted Average
        // We assume $item->purchase_price is maintained as WAC.
        return $item->purchase_price > 0 ? $item->purchase_price : 0;
    }
    
    private function getCurrentStockValue(InventoryItem $item)
    {
        // Value = Qty * Avg Cost
        return $item->current_stock * $item->purchase_price;
    }
    
    private function createJournalEntry(InventoryItem $item, $type, $amount, $reference)
    {
        $journal = AccountingJournal::where('type', 'general')->first(); // Or dedicated Stock Journal
        if (!$journal) return null;
        
        $entry = JournalEntry::create([
            'journal_id' => $journal->id,
            'date' => Carbon::now(),
            'name' => 'STK-' . strtoupper($type) . '-' . $reference,
            'reference' => $reference,
            'state' => 'posted',
            'created_by' => auth()->id() ?? \App\Models\User::first()->id,
        ]);
        
        if ($type === InventoryTransaction::TYPE_IN) {
            // Purchase/Receipt: Dr Inventory, Cr Accounts Payable (or Suspense/Clearing)
            // Note: Usually AP is credited via Bill. This entry is just for Stock Value?
            // If Bill creates AP, it Dr Expense/Asset. 
            // Correct flow: 
            // 1. Bill: Dr Inventory Clearing, Cr AP.
            // 2. Receipt: Dr Inventory Asset, Cr Inventory Clearing.
            
            // MVP Simplification: Assume this is an Adjustment or direct Stock addition not linked to Bill yet.
            // Or if linked to Bill, the Bill shouldn't double count.
            
            // Let's implement COGS logic mainly (OUT).
            // For IN, we debit Inventory. Credit? Maybe 'Stock Adjustment' or 'Cost of Goods Sold' (if return).
            
            // For now: Dr Inventory, Cr Cost of Goods Sold (Simplistic for Returns/Adjustments)
            // Realistically, PO Receipt: Dr Inventory, Cr GRNI (Goods Received Not Invoiced)
            
             $entry->items()->create([
                'account_id' => $item->inventory_account_id,
                'debit' => $amount,
                'credit' => 0,
                'date' => Carbon::now(),
            ]);
            
            // Balancing entry - placeholder 'Opening Balance Equity' or similar for now if strictly testing
             $entry->items()->create([
                'account_id' => $item->cogs_account_id, // Using COGS temporarily as offset for simplicity in test
                'debit' => 0,
                'credit' => $amount,
                'date' => Carbon::now(),
            ]);
            
        } elseif ($type === InventoryTransaction::TYPE_OUT) {
            // Sale/Delivery: Dr COGS, Cr Inventory
            
            // Debit COGS
             $entry->items()->create([
                'account_id' => $item->cogs_account_id,
                'debit' => $amount,
                'credit' => 0,
                'date' => Carbon::now(),
            ]);
            
            // Credit Inventory
             $entry->items()->create([
                'account_id' => $item->inventory_account_id,
                'debit' => 0,
                'credit' => $amount,
                'date' => Carbon::now(),
            ]);
        }
        
        return $entry->id;
    }
}
