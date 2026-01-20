<?php

namespace App\Services;

use App\Models\Payroll;
use App\Models\PayrollEntry;
use App\Models\EmployeeDetail;
use App\Models\OvertimeRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PayrollService
{
    /**
     * Generate Draft Payroll for a specific month.
     */
    public function generatePayroll($month)
    {
        // Check if payroll already exists
        $existing = Payroll::where('month', $month)->first();
        if ($existing && $existing->status !== 'draft') {
            throw new \Exception("Payroll for $month is already finalized or paid.");
        }

        return DB::transaction(function () use ($month, $existing) {
            $payroll = $existing ?? Payroll::create([
                'month' => $month,
                'status' => 'draft',
                'processed_by' => Auth::id(),
            ]);

            // Clear existing draft entries if regenerating
            if ($existing) {
                $payroll->entries()->delete();
            }

            // Fetch eligible employees (Users with EmployeeDetails)
            $employees = User::whereHas('employeeDetail')->with('employeeDetail')->get();
            $totalAmount = 0;

            foreach ($employees as $user) {
                $entry = $this->calculateEntry($user, $month);
                $entry['payroll_id'] = $payroll->id;
                
                PayrollEntry::create($entry);
                $totalAmount += $entry['net_salary'];
            }

            $payroll->update(['total_amount' => $totalAmount]);

            return $payroll;
        });
    }

    /**
     * Calculate individual employee salary for a month.
     */
    private function calculateEntry(User $user, $month)
    {
        $detail = $user->employeeDetail;
        
        $basic = $detail->basic_salary;
        
        // Allowances
        $allowances = $detail->allowance_transport + $detail->allowance_food + $detail->allowance_other;
        
        // Overtime
        // Assuming month format YYYY-MM
        $startDate = Carbon::parse("$month-01");
        $endDate = $startDate->copy()->endOfMonth();
        
        $otHours = OvertimeRecord::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->sum('hours');
            
        $otAmount = $otHours * $detail->hourly_rate;
        
        // Deductions (Placeholder logic - can be expanded)
        $deductions = 0; 
        
        $gross = $basic + $allowances + $otAmount;
        $net = $gross - $deductions;
        
        return [
            'user_id' => $user->id,
            'basic_salary' => $basic,
            'allowances_total' => $allowances,
            'overtime_hours' => $otHours,
            'overtime_amount' => $otAmount,
            'deductions_total' => $deductions,
            'gross_salary' => $gross,
            'net_salary' => $net,
            'payment_status' => 'pending',
        ];
    }

    /**
     * Finalize payroll and lock it.
     */
    public function finalizePayroll(Payroll $payroll)
    {
        if ($payroll->status !== 'draft') {
            return;
        }

        $payroll->update([
            'status' => 'finalized',
            'processed_date' => now(),
        ]);
        
        // Trigger Expense Posting via Observer or Direct event
    }
}
