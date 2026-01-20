<?php

namespace App\Observers;

use App\Models\Expense;
use App\Models\LedgerEntry;

class ExpenseObserver
{
    /**
     * Handle the Expense "created" event.
     */
    public function created(Expense $expense): void
    {
        if ($expense->customer_id) {
            $this->createLedgerEntry($expense);
        }
    }

    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense): void
    {
        if ($expense->wasChanged('customer_id') || $expense->wasChanged('amount') || $expense->wasChanged('date')) {
            $entry = LedgerEntry::where('reference_type', Expense::class)
                ->where('reference_id', $expense->id)
                ->first();

            if ($entry) {
                if ($expense->customer_id) {
                    $entry->update([
                        'customer_id' => $expense->customer_id,
                        'date' => $expense->date,
                        'debit' => $expense->amount,
                        'credit' => 0,
                        'description' => "Expense: " . ($expense->category->name ?? 'General'),
                    ]);
                } else {
                    $entry->delete();
                }
            } else {
                if ($expense->customer_id) {
                    $this->createLedgerEntry($expense);
                }
            }
        }
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense): void
    {
        LedgerEntry::where('reference_type', Expense::class)
            ->where('reference_id', $expense->id)
            ->delete();
    }

    protected function createLedgerEntry(Expense $expense)
    {
        LedgerEntry::create([
            'customer_id' => $expense->customer_id,
            'date' => $expense->date,
            'type' => 'expense',
            'reference_type' => Expense::class,
            'reference_id' => $expense->id,
            'description' => "Expense: " . ($expense->category->name ?? 'General'),
            'debit' => $expense->amount,
            'credit' => 0,
        ]);
    }
}
