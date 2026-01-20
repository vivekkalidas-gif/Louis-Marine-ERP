<?php

namespace App\Models;

use App\Models\Accounting\ChartOfAccount;
use App\Models\Accounting\JournalEntry;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorPayment extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'payment_number',
        'vendor_id',
        'payment_date',
        'amount',
        'payment_method',
        'reference',
        'status', // draft, posted, void
        'notes',
        'journal_entry_id',
        'bank_account_id',
        'created_by'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function vendor()
    {
        return $this->belongsTo(Supplier::class, 'vendor_id');
    }

    public function bills()
    {
        return $this->belongsToMany(VendorBill::class, 'vendor_bill_payment')
            ->withPivot('amount')
            ->withTimestamps();
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }
    
    public function bankAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'bank_account_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
