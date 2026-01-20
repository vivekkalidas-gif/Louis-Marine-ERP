<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class BudgetItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'budget_id', 'chart_of_account_id', 'period_date', 'amount', 'notes'
    ];

    protected $casts = [
        'period_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }
}
