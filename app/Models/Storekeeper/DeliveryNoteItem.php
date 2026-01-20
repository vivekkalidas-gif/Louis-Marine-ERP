<?php

namespace App\Models\Storekeeper;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryNoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_note_id',
        'inventory_item_id',
        'description',
        'quantity'
    ];

    public function inventoryItem()
    {
        return $this->belongsTo(\App\Models\InventoryItem::class);
    }

    public function deliveryNote()
    {
        return $this->belongsTo(DeliveryNote::class);
    }
}
