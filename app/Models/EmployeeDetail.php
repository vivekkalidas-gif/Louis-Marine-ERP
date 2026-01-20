<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeDetail extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'employee_code',
        'nationality',
        'dob', // New
        'gender', // New
        'designation',
        'department', // New
        'employment_type', // New
        'work_location', // New
        'reporting_manager_id', // New
        'join_date',
        'status', // New
        'basic_salary',
        'hourly_rate',
        'allowance_transport',
        'allowance_food',
        'allowance_other',
        'bank_details',
        'payment_mode', // New
    ];

    protected $casts = [
        'join_date' => 'date',
        'dob' => 'date',
        'basic_salary' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'allowance_transport' => 'decimal:2',
        'allowance_food' => 'decimal:2',
        'allowance_other' => 'decimal:2',
        'bank_details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'reporting_manager_id');
    }
}
