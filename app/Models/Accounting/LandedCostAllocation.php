<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\InventoryItem;

class LandedCostAllocation extends Model
{
    use HasUuids;

    protected $fillable = [
        'landed_cost_id', 'inventory_item_id', 'amount'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }
}
