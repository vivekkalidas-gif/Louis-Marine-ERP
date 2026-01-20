<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AccountingJournal extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'name', 'code', 'type', 'default_account_id'
    ];

    public function defaultAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'default_account_id');
    }

    public function entries()
    {
        return $this->hasMany(JournalEntry::class, 'journal_id');
    }
}
