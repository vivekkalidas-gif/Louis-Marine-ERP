<?php

namespace App\Observers;

use App\Models\Payroll;
use App\Models\Expense;
use App\Models\FinanceCategory;
use Illuminate\Support\Facades\Log;

class PayrollObserver
{
    /**
     * Handle the Payroll "updated" event.
     */
    public function updated(Payroll $payroll): void
    {
        if ($payroll->wasChanged('status') && $payroll->status === 'finalized') {
            $this->createPayrollExpense($payroll);
        }
    }

    protected function createPayrollExpense(Payroll $payroll)
    {
        // 1. Get or Create "Salary & Wages" Category
        $category = FinanceCategory::firstOrCreate(
            ['name' => 'Salaries & Wages', 'type' => 'expense'],
            ['color' => 'indigo', 'is_system' => true, 'description' => 'Monthly Staff Payroll']
        );

        // 2. Create Expense
        try {
            Expense::create([
                'date' => now(), // Or use payroll Month end date
                'category_id' => $category->id,
                'amount' => $payroll->total_amount,
                'payment_method' => 'bank_transfer', // Default mode
                'vendor_name' => 'Internal Payroll',
                'reference_number' => "PAYROLL-{$payroll->month}",
                'notes' => "Consolidated Payroll for Month: {$payroll->month}",
                'recorded_by' => $payroll->processed_by,
            ]);
            
            Log::info("Payroll Expense created for Month: {$payroll->month}");
            
        } catch (\Exception $e) {
            Log::error("Failed to create Payroll Expense: " . $e->getMessage());
        }
    }
}
