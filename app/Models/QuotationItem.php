<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    protected $fillable = [
        'quotation_id', 'inventory_item_id', 'description', 'quantity', 'unit_price', 'total'
    ];

    public function quotation() {
        return $this->belongsTo(Quotation::class);
    }

    public function product() {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }
}
