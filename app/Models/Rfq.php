<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rfq extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference_number',
        'type',
        'status',
        'currency_id',
        'incoterm',
        'delivery_location',
        'deadline_date',
        'created_by'
    ];

    protected $casts = [
        'deadline_date' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function items()
    {
        return $this->hasMany(RfqItem::class);
    }

    public function suppliers()
    {
        // Many-to-Many via rfq_suppliers pivot
        return $this->belongsToMany(Supplier::class, 'rfq_suppliers')
                    ->withPivot('sent_at', 'responded_at', 'supplier_ref')
                    ->withTimestamps();
    }

    public function quotations()
    {
        return $this->hasMany(SupplierQuotation::class);
    }
}
