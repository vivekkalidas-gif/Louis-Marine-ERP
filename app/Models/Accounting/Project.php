<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class Project extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'code', 'name', 'customer_id', 'manager_id', 
        'status', 'start_date', 'end_date', 
        'budget_revenue', 'budget_cost'
    ];
    
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget_revenue' => 'decimal:2',
        'budget_cost' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function journalItems()
    {
        return $this->hasMany(JournalItem::class);
    }
    
    // Calculated Attribute: Net Profit
    public function getNetProfitAttribute()
    {
        // Revenue (Credit) - Expense (Debit)
        // Usually, Income accounts are Credit positive. Expense Debit positive.
        // P&L = (Sum Credits where Type=Income) - (Sum Debits where Type=Expense)
        // Adjust for Journal Item sign convention:
        // Net Profit = Sum(Credit - Debit) for Income - Sum(Debit - Credit) for Expernse
        
        // Simpler: Just sum(credit - debit) for all P&L accounts linked to this project.
        // If Result > 0 => Profit.
        
        return $this->journalItems()
            ->whereHas('account', function($q) {
                $q->whereIn('type', ['income', 'other_income', 'expense', 'cost_of_revenue', 'depreciation']);
            })
            ->whereHas('entry', function($q) { $q->posted(); })
            ->sum(DB::raw('credit - debit'));
    }
}
