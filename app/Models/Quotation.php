<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'quotation_number',
        'date',
        'reference_number',
        'valid_until',
        'subtotal',
        'tax',
        'total',
        'status',
        'notes',
        'terms',
        'created_by',
    ];

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    protected $casts = [
        'date' => 'date',
        'valid_until' => 'date',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function job()
    {
        return $this->belongsTo(ServiceJob::class, 'service_job_id');
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }
}
