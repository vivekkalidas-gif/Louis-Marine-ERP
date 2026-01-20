<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ChartOfAccount extends Model
{
    use HasFactory, SoftDeletes, HasUuids, \App\Traits\LogsActivity;

    protected $moduleName = 'Finance';

    protected $fillable = [
        'code', 'name', 'type', 'is_reconcilable', 'description', 'parent_id', 'is_active'
    ];

    protected $casts = [
        'is_reconcilable' => 'boolean',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function parent()
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    public function journalItems()
    {
        return $this->hasMany(JournalItem::class, 'account_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeShouldCache($query)
    {
        // Placeholder for caching logic
        return $query;
    }
}
