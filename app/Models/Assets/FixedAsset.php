<?php

namespace App\Models\Assets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\User;

class FixedAsset extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'category_id', 'asset_code', 'name', 'description', 
        'serial_number', 'location', 'purchase_date', 'capitalization_date',
        'purchase_cost', 'salvage_value', 'depreciation_method', 'useful_life_years',
        'status', 'accumulated_depreciation', 'current_value', 'created_by'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'capitalization_date' => 'date',
        'purchase_cost' => 'decimal:2',
        'salvage_value' => 'decimal:2',
        'accumulated_depreciation' => 'decimal:2',
        'current_value' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    public function schedules()
    {
        return $this->hasMany(DepreciationSchedule::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
