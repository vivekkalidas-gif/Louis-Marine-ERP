<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\LedgerEntry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LedgerService
{
    /**
     * Record a new transaction in the customer ledger.
     * 
     * @param Customer|int $customer
     * @param string $type  'invoice', 'payment', 'adjustment', 'credit_note'
     * @param float $amount Positive value. Logic determines debit/credit based on type.
     * @param Model|null $reference The related model (Invoice or Payment)
     * @param string|null $description
     * @param string|null $date Y-m-d
     * @return LedgerEntry
     */
    public function recordEntry($customer, string $type, float $amount, ?Model $reference = null, ?string $description = null, ?string $date = null)
    {
        $customerId = $customer instanceof Customer ? $customer->id : $customer;
        $date = $date ?? now()->toDateString();
        $amount = abs($amount); // Ensure positive

        // Determine Debit/Credit
        $debit = 0;
        $credit = 0;

        switch ($type) {
            case 'invoice':
            case 'debit_adjustment': // Admin increases debt
            case 'refund': // We pay back customer (increases their debt/reduces our liability ... wait. Refund means we give money back. )
                // Invoice increases what they owe us.
                $debit = $amount;
                break;

            case 'payment':
            case 'receipt':
            case 'credit_note': // Reduces what they owe
            case 'credit_adjustment': // Admin reduces debt
                $credit = $amount;
                break;
                
            default:
                throw new \Exception("Invalid ledger entry type: $type");
        }

        return DB::transaction(function () use ($customerId, $date, $type, $reference, $description, $debit, $credit) {
            // Get last balance
            // We need the balance referencing the immediate previous entry.
            // CAUTION: If backdating, this 'latest' logic is flawed. 
            // For MVP, we assume entries are mostly chronological. 
            // If backdating, we must trigger a recalculation.
            
            $lastEntry = LedgerEntry::where('customer_id', $customerId)
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->first();

            $previousBalance = $lastEntry ? $lastEntry->running_balance : 0;
            $newBalance = $previousBalance + $debit - $credit;

            $entry = LedgerEntry::create([
                'customer_id' => $customerId,
                'date' => $date,
                'type' => $type,
                'reference_id' => $reference ? $reference->id : null,
                'reference_type' => $reference ? get_class($reference) : null,
                'description' => $description,
                'debit' => $debit,
                'credit' => $credit,
                'running_balance' => $newBalance,
            ]);

            // If we inserted an entry in the past (not latest date), we MUST recalculate future entries.
            if ($lastEntry && $date < $lastEntry->date) {
                $this->recalculateLedger($customerId);
                // Refresh the returned entry to get correct balance
                $entry->refresh();
            }

            return $entry;
        });
    }

    /**
     * Completely recalculate the running balance for a customer.
     * Essential for audit/integrity and backdated entries.
     */
    public function recalculateLedger($customerId)
    {
        $entries = LedgerEntry::where('customer_id', $customerId)
            ->orderBy('date', 'asc')
            ->orderBy('created_at', 'asc') // Secondary sort for same-day
            ->get();

        $runningBalance = 0;

        DB::transaction(function () use ($entries, &$runningBalance) {
            foreach ($entries as $entry) {
                $runningBalance += ($entry->debit - $entry->credit);
                
                // Only update if changed to save DB calls
                if (abs($entry->running_balance - $runningBalance) > 0.001) {
                    $entry->running_balance = $runningBalance;
                    $entry->saveQuietly(); // modifying timestamps might mess up order if we rely on updated_at? No, we rely on created_at.
                }
            }
        });
        
        return $runningBalance;
    }
}
