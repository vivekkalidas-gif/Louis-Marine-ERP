<?php

namespace App\Observers;

use App\Models\Income;
use App\Models\LedgerEntry;
use Illuminate\Support\Facades\Log;

class IncomeObserver
{
    /**
     * Handle the Income "created" event.
     */
    public function created(Income $income): void
    {
        if ($income->customer_id) {
            $this->createLedgerEntry($income);
        }
    }

    /**
     * Handle the Income "updated" event.
     */
    public function updated(Income $income): void
    {
        // Check if customer changed or amount changed
        if ($income->wasChanged('customer_id') || $income->wasChanged('amount') || $income->wasChanged('date')) {
            // Delete old entry if exists and recreate
            // Or update existing.
            // Simplest: Find by reference and update.
            $entry = LedgerEntry::where('reference_type', Income::class)
                ->where('reference_id', $income->id)
                ->first();

            if ($entry) {
                if ($income->customer_id) {
                    $entry->update([
                        'customer_id' => $income->customer_id,
                        'date' => $income->date,
                        'credit' => $income->amount,
                        'debit' => 0,
                        'description' => $income->description ?? 'Income Record',
                    ]);
                } else {
                    $entry->delete(); // No longer linked to customer
                }
            } else {
                if ($income->customer_id) {
                    $this->createLedgerEntry($income);
                }
            }
        }
    }

    /**
     * Handle the Income "deleted" event.
     */
    public function deleted(Income $income): void
    {
        LedgerEntry::where('reference_type', Income::class)
            ->where('reference_id', $income->id)
            ->delete();
    }

    protected function createLedgerEntry(Income $income)
    {
        LedgerEntry::create([
            'customer_id' => $income->customer_id,
            'date' => $income->date,
            'type' => 'income',
            'reference_type' => Income::class,
            'reference_id' => $income->id,
            'description' => $income->description ?? 'Income Record',
            'debit' => 0,
            'credit' => $income->amount,
        ]);
    }
}
