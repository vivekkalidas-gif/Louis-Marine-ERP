<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialRequest extends Model
{
    protected $fillable = [
        'service_job_id',
        'requested_by',
        'inventory_item_id', // Changed from product_id
        'item_name',
        'quantity',
        'urgency',
        'status',
        'admin_remarks'
    ];

    public function job()
    {
        return $this->belongsTo(ServiceJob::class, 'service_job_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function getNameAttribute()
    {
        return $this->attributes['item_name'] ?? null;
    }

    public function getQtyAttribute()
    {
        return $this->attributes['quantity'] ?? null;
    }
}
