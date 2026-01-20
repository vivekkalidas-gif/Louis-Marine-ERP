<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class JournalItem extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'journal_entry_id', 'account_id', 'partner_id', 'cost_center_id', 'project_id',
        'label', 'debit', 'credit', 'date'
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'date' => 'date'
    ];

    public function entry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public function partner()
    {
        return $this->belongsTo(\App\Models\User::class, 'partner_id');
    }

    public function costCenter()
    {
        return $this->belongsTo(\App\Models\Accounting\CostCenter::class, 'cost_center_id');
    }

    public function project()
    {
        return $this->belongsTo(\App\Models\Accounting\Project::class, 'project_id');
    }
}
