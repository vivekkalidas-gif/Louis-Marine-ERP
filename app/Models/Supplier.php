<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'type', // local, overseas
        'category',
        'status', // active, inactive, blacklisted
        'contact_person',
        'email',
        'phone',
        'website',
        'address',
        'tax_id',
        'currency_id',
        'incoterm',
        'payment_terms',
        'rating',
        'created_by'
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function documents()
    {
        return $this->hasMany(SupplierDocument::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function bills()
    {
        return $this->hasMany(VendorBill::class, 'vendor_id');
    }

    public function payments()
    {
        return $this->hasMany(VendorPayment::class, 'vendor_id');
    }

    public function getOutstandingBalanceAttribute()
    {
        // This calculates real-time based on posted bills and payments
        // Ideally should be cached or stored in opening_balance + journal sums
        // For now, strict calculation:
        $billed = $this->bills()->where('status', '!=', 'void')->sum('total_amount'); // Includes Paid + Unpaid
        $paid = $this->payments()->where('status', 'posted')->sum('amount');
        
        // Adjust for opening balance
        return $this->opening_balance + $billed - $paid;
    }
}
