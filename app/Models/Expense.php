<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'date',
        'category_id',
        'customer_id',
        'job_id',
        'vendor_name',
        'reference_number',
        'amount',
        'tax_amount',
        'payment_method',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(FinanceCategory::class, 'category_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function job()
    {
        return $this->belongsTo(ServiceJob::class, 'job_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
