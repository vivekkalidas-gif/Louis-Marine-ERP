<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\User;

class Forecast extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name', 'base_budget_id', 'version', 'start_date', 'end_date', 'created_by'
    ];
    
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function budget()
    {
        return $this->belongsTo(Budget::class, 'base_budget_id');
    }

    public function items()
    {
        return $this->hasMany(ForecastItem::class);
    }
}
