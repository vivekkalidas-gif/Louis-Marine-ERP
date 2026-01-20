<?php

namespace App\Models\Assets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Accounting\ChartOfAccount;

class AssetCategory extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'name', 'code', 'depreciation_method', 'useful_life_years',
        'asset_account_id', 'depreciation_expense_account_id', 'accumulated_depreciation_account_id'
    ];

    public function assetAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'asset_account_id');
    }

    public function expenseAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'depreciation_expense_account_id');
    }

    public function accumulatedAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'accumulated_depreciation_account_id');
    }
}
