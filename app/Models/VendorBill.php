<?php

namespace App\Models;

use App\Models\Accounting\JournalEntry;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorBill extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'bill_number',
        'bill_date',
        'due_date',
        'status', // draft, posted, paid, partial, void
        'sub_total',
        'tax_total',
        'total_amount',
        'notes',
        'journal_entry_id',
        'created_by',
        'posted_by',
        'purchase_order_id'
    ];

    protected $casts = [
        'bill_date' => 'date',
        'due_date' => 'date',
        'sub_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // Relationships
    public function vendor()
    {
        return $this->belongsTo(Supplier::class, 'vendor_id');
    }

    public function items()
    {
        return $this->hasMany(VendorBillItem::class);
    }

    public function payments()
    {
        return $this->belongsToMany(VendorPayment::class, 'vendor_bill_payment')
            ->withPivot('amount')
            ->withTimestamps();
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function poster()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }
    
    // Scopes
    public function scopePending($query)
    {
        return $query->whereIn('status', ['posted', 'partial']);
    }

    // Accessors
    public function getPaidAmountAttribute()
    {
        return $this->payments()->sum('vendor_bill_payment.amount');
    }

    public function getOutstandingAmountAttribute()
    {
        return $this->total_amount - $this->paid_amount;
    }
}
