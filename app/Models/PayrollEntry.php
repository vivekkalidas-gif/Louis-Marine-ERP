<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PayrollEntry extends Model
{
    use HasUuids;

    protected $fillable = [
        'payroll_id',
        'user_id',
        'basic_salary',
        'allowances_total',
        'overtime_hours',
        'overtime_amount',
        'deductions_total',
        'gross_salary',
        'net_salary',
        'payment_status',
        'payment_date',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'allowances_total' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'overtime_amount' => 'decimal:2',
        'deductions_total' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Helper to get employee detail snapshot if needed, or link to current
    public function employeeDetail()
    {
        return $this->user->employeeDetail();
    }
}
