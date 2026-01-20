<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class LandedCostLine extends Model
{
    use HasUuids;

    protected $fillable = [
        'landed_cost_id', 'account_id', 'description', 'amount', 'allocation_method'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function landedCost()
    {
        return $this->belongsTo(LandedCost::class);
    }
    
    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }
}
