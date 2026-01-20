<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'supplier_id',
        'po_number',
        'po_date',
        'expected_delivery_date',
        'status',
        'total_amount',
        'notes',
        'transport_charges',
        'customs_charges',
        'other_charges',
        'created_by'
    ];

    protected $casts = [
        'po_date' => 'date',
        'expected_delivery_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function bills()
    {
        return $this->hasMany(VendorBill::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
