<?php

namespace App\Models;

use App\Models\Accounting\ChartOfAccount;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorBillItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'vendor_bill_id',
        'account_id', // Expense Account
        'inventory_item_id',
        'description',
        'quantity',
        'unit_price',
        'tax_rate',
        'tax_amount',
        'total'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function bill()
    {
        return $this->belongsTo(VendorBill::class, 'vendor_bill_id');
    }

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }
}
