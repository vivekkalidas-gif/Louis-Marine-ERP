<?php

namespace App\Services\Accounting;

use App\Models\Accounting\BankStatement;
use App\Models\Accounting\BankStatementLine;
use App\Models\Accounting\JournalItem;
use Illuminate\Support\Facades\DB;

class BankReconciliationService
{
    /**
     * Auto-match statement lines to journal items based on rules.
     */
    public function autoMatch(BankStatement $statement)
    {
        $matchesFound = 0;
        
        // 1. Get Unreconciled Lines
        $lines = $statement->lines()->where('status', '!=', 'reconciled')->get();
        
        // 2. Get Candidate Journal Items
        // In same Journal, Posted, Not Reconciled
        // Note: Ideally JournalItem would have 'reconciled' flag. 
        // For now, we assume if it's not linked to a BankStatementLine, it's open.
        // We need to check if ANY BankStatementLine links to this item.
        
        foreach ($lines as $line) {
            
            // Loose Date Matching (+/- 3 days)
            $dateStart = $line->date->copy()->subDays(3);
            $dateEnd = $line->date->copy()->addDays(3);
            
            // Amount Match (Exact)
            // Statement Line Amount: +/- (Positive = Deposit, Negative = Withdrawal for Bank Account usually)
            // But usually Bank Statement files come with "Credit" and "Debit" columns.
            // Our model has 'amount'. Let's assume Signed Amount.
            // Journal Item: Debit is Positive for Asset (Deposit). Credit is Negative.
            // So Statement Amount should match (Debit - Credit) of the Item.
            
            $match = JournalItem::whereHas('entry', function($q) use ($statement) {
                    $q->where('journal_id', $statement->journal_id)
                      ->where('state', 'posted')
                      ->doesntHave('bankStatementLines'); // Check Entry for prior reconciliation
                })
                ->whereBetween('date', [$dateStart, $dateEnd])
                ->where(function($q) use ($line) {
                    // Check exact amount match
                    // DB amount = debit - credit
                    // We check if (debit - credit) == line->amount
                    $q->whereRaw('abs((debit - credit) - ?) < 0.01', [$line->amount]);
                })
                ->first();

            if ($match) {
                 // Double check uniqueness (optional but recommended)
                 // For now, proceed.
                 
                 $this->match($line, $match);
                 $matchesFound++;
            }
        }
        
        return $matchesFound;
    }

    /**
     * Link a Line to a Journal Item/Entry
     */
    public function match(BankStatementLine $line, JournalItem $item)
    {
        DB::transaction(function() use ($line, $item) {
            $line->update([
                'journal_entry_id' => $item->entry_id, // Keeping existing logic linking to Entry
                 // Ideally we should link to the specific ITEM ($item->id) if we want granularity.
                 // But the current schema links to Entry.
                 // Let's stick to Entry for now as per schema.
                'status' => 'reconciled'
            ]);
            
            // If we could link to Item, we would:
            // $line->journal_item_id = $item->id;
            // $line->save();
        });
    }
}
