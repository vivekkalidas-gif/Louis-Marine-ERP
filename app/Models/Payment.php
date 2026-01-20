<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, HasUuids, SoftDeletes, \App\Traits\LogsActivity;

    protected $moduleName = 'Sales';

    protected $fillable = [
        'payment_number',
        'customer_id', // Added
        'invoice_id',
        'payment_date',
        'amount',
        'status', // Added
        'payment_method',
        'reference_number',
        'notes',
        'created_by',
        'journal_entry_id'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function journalEntry()
    {
        return $this->belongsTo(\App\Models\Accounting\JournalEntry::class);
    }
}
