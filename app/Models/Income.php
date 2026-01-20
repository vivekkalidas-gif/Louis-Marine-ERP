<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Income extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'date',
        'category_id',
        'customer_id',
        'amount',
        'payment_method',
        'reference_id',
        'reference_type',
        'reference_number',
        'description',
        'notes',
        'status', // approved, pending
        'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(FinanceCategory::class, 'category_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
    
    // Polymorphic relation to Invoice/Payment if needed
    public function reference()
    {
        return $this->morphTo();
    }
}
