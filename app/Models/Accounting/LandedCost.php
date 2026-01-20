<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\PurchaseOrder;
use App\Models\User;

class LandedCost extends Model
{
    use HasUuids;

    protected $fillable = [
        'purchase_order_id', 'reference', 'date', 'total_amount', 'status', 'created_by'
    ];

    protected $casts = [
        'date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function lines()
    {
        return $this->hasMany(LandedCostLine::class);
    }
    
    public function allocations()
    {
        return $this->hasMany(LandedCostAllocation::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
