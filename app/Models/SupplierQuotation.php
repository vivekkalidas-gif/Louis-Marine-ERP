<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierQuotation extends Model
{
    protected $fillable = [
        'rfq_id',
        'supplier_id',
        'subtotal_amount',
        'tax_amount',
        'shipping_cost',
        'insurance_cost',
        'customs_duty',
        'other_costs',
        'total_amount_foreign',
        'exchange_rate',
        'total_amount_local',
        'lead_time',
        'warranty',
        'payment_terms',
        'quotation_file',
        'is_selected',
        'selection_note'
    ];

    protected $casts = [
        'is_selected' => 'boolean',
        'subtotal_amount' => 'decimal:2',
        'total_amount_foreign' => 'decimal:2',
        'total_amount_local' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
    ];

    public function rfq()
    {
        return $this->belongsTo(Rfq::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
