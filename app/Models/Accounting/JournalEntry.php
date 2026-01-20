<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class JournalEntry extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'date', 'journal_id', 'number', 'name', 'reference', 'state', 
        'entity_id', 'entity_type', 'partner_id', 'created_by', 'posted_by', 
        'reversed_date', 'reversed_entry_id', 'reversal_reason'
    ];

    protected $casts = [
        'date' => 'date',
        'reversed_date' => 'date',
    ];

    // Scopes
    public function scopePosted($query)
    {
        return $query->where('state', 'posted');
    }

    public function scopeDraft($query)
    {
        return $query->where('state', 'draft');
    }

    // Relationships
    public function journal()
    {
        return $this->belongsTo(AccountingJournal::class, 'journal_id');
    }

    public function items()
    {
        return $this->hasMany(JournalItem::class, 'journal_entry_id');
    }

    public function entity()
    {
        return $this->morphTo();
    }
    
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function poster()
    {
        return $this->belongsTo(\App\Models\User::class, 'posted_by');
    }

    public function reversalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'reversed_entry_id');
    }

    public function bankStatementLines()
    {
        return $this->hasMany(BankStatementLine::class, 'journal_entry_id');
    }

    // Logic
    public function isBalanced()
    {
        $debit = $this->items->sum('debit');
        $credit = $this->items->sum('credit');
        // Use epsilon for float comparison
        return abs($debit - $credit) < 0.001;
    }
}
