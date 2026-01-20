<?php

namespace App\Models\Storekeeper;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DeliveryNote extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'dr_number',
        'date',
        'customer_id',
        'reference',
        'status',
        'invoice_id',
        'quotation_id',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'date' => 'date'
    ];

    public function items()
    {
        return $this->hasMany(DeliveryNoteItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer::class);
    }

    public function invoice()
    {
        return $this->belongsTo(\App\Models\Invoice::class);
    }

    public function quotation()
    {
        return $this->belongsTo(\App\Models\Quotation::class);
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
