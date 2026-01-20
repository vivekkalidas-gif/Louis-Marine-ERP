<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class BankStatement extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'name',
        'date',
        'journal_id',
        'balance_start',
        'balance_end_real',
        'state',
    ];

    protected $casts = [
        'date' => 'date',
        'balance_start' => 'decimal:2',
        'balance_end_real' => 'decimal:2',
    ];

    public function journal()
    {
        return $this->belongsTo(AccountingJournal::class, 'journal_id');
    }

    public function lines()
    {
        return $this->hasMany(BankStatementLine::class, 'statement_id');
    }
}
