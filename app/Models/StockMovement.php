<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'inventory_item_id',
        'movement_type',
        'reference_type',
        'reference_id',
        'quantity',
        'balance_after',
        'location',
        'remarks',
        'created_by'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'balance_after' => 'integer',
    ];

    // Relationships
    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Polymorphic relationship for reference (Job, Purchase, etc.)
    public function reference()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeInward($query)
    {
        return $query->where('movement_type', 'inward');
    }

    public function scopeOutward($query)
    {
        return $query->where('movement_type', 'outward');
    }

    public function scopeForItem($query, $itemId)
    {
        return $query->where('inventory_item_id', $itemId);
    }
}
