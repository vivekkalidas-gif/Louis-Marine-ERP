<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ForecastItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'forecast_id', 'chart_of_account_id', 'period_date', 'amount'
    ];

    protected $casts = [
        'period_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }
}
