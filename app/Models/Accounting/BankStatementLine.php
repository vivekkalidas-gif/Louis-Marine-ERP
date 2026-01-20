<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class BankStatementLine extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'statement_id',
        'date',
        'label',
        'partner_name',
        'ref',
        'amount',
        'journal_entry_id',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function statement()
    {
        return $this->belongsTo(BankStatement::class, 'statement_id');
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
}
