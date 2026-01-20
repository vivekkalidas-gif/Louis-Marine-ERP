<?php

namespace App\Observers;

use App\Models\Payment;
use App\Models\Income;
use App\Models\FinanceCategory;
use Illuminate\Support\Facades\Log;

class PaymentObserver
{
    /**
     * Handle the Payment "created" event.
     */
    public function created(Payment $payment): void
    {
        // 1. Find or Create default Income Category for "Invoice Payments"
        $category = FinanceCategory::firstOrCreate(
            ['name' => 'Invoice Payment', 'type' => 'income'],
            ['color' => 'blue', 'is_system' => true, 'description' => 'Automatic income from invoice payments']
        );

        // 2. Create Income Record
        try {
            Income::create([
                'date' => $payment->payment_date,
                'category_id' => $category->id,
                'customer_id' => $payment->invoice->customer_id ?? null, // payment belongs to invoice
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'reference_type' => Payment::class,
                'reference_id' => $payment->id,
                'reference_number' => $payment->payment_number,
                'description' => "Payment for Invoice " . ($payment->invoice->invoice_number ?? 'Unknown'),
                'notes' => $payment->notes,
                'status' => 'approved',
                'recorded_by' => $payment->created_by,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to sync Payment {$payment->id} to Income: " . $e->getMessage());
        }
    }

    /**
     * Handle the Payment "updated" event.
     */
    public function updated(Payment $payment): void
    {
        // Sync changes
        $income = Income::where('reference_type', Payment::class)
            ->where('reference_id', $payment->id)
            ->first();

        if ($income) {
            $income->update([
                'date' => $payment->payment_date,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'notes' => $payment->notes,
            ]);
        }
    }

    /**
     * Handle the Payment "deleted" event.
     */
    public function deleted(Payment $payment): void
    {
        // Delete linked income
        Income::where('reference_type', Payment::class)
            ->where('reference_id', $payment->id)
            ->delete();
    }
}
