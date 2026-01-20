<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use App\Models\Accounting\JournalEntry;

class InventoryTransaction extends Model
{
    use HasUuids;

    public const TYPE_IN = 'IN';
    public const TYPE_OUT = 'OUT';
    public const TYPE_ADJUSTMENT = 'ADJUSTMENT';

    protected $fillable = [
        'inventory_item_id', 'user_id', 'type', 'quantity', 
        'reference', 'notes',
        'unit_cost', 'total_cost', 'stock_value_after', 
        'batch_no', 'journal_entry_id'
    ];
    
    protected $casts = [
        'unit_cost' => 'decimal:4',
        'total_cost' => 'decimal:2',
        'stock_value_after' => 'decimal:2',
    ];

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
}
