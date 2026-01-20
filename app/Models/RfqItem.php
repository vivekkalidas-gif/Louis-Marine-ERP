<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RfqItem extends Model
{
    protected $fillable = [
        'rfq_id',
        'material_request_id',
        'product_id',
        'description',
        'quantity',
        'unit'
    ];

    public function rfq()
    {
        return $this->belongsTo(Rfq::class);
    }

    public function materialRequest()
    {
        return $this->belongsTo(MaterialRequest::class);
    }

    public function product()
    {
        return $this->belongsTo(InventoryItem::class, 'product_id');
    }
}
