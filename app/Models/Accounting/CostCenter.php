<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CostCenter extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'code', 'name', 'description', 
        'parent_id', 'type', 'manager_id', 'is_active'
    ];

    public function parent()
    {
        return $this->belongsTo(CostCenter::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(CostCenter::class, 'parent_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function journalItems()
    {
        return $this->hasMany(JournalItem::class);
    }
    
    public function getTotalExpenseAttribute()
    {
         return $this->journalItems()
            ->whereHas('account', function($q) {
                $q->whereIn('type', ['expense', 'depreciation', 'cost_of_revenue']);
            })
            ->whereHas('entry', function($q) { $q->posted(); })
            ->sum(DB::raw('debit - credit'));
    }
}
