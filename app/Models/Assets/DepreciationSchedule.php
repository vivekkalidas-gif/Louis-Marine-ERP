<?php

namespace App\Models\Assets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Accounting\JournalEntry;

class DepreciationSchedule extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'fixed_asset_id', 'schedule_date', 'amount', 
        'net_book_value_before', 'net_book_value_after', 
        'is_posted', 'journal_entry_id'
    ];

    protected $casts = [
        'schedule_date' => 'date',
        'amount' => 'decimal:2',
        'net_book_value_before' => 'decimal:2',
        'net_book_value_after' => 'decimal:2',
        'is_posted' => 'boolean',
    ];

    public function asset()
    {
        return $this->belongsTo(FixedAsset::class, 'fixed_asset_id');
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
}
