<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\User;

class Budget extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'name', 'fiscal_year', 'period_type', 'status', 'created_by'
    ];

    public function items()
    {
        return $this->hasMany(BudgetItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
