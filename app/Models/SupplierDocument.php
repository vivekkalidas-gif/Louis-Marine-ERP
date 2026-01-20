<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'title',
        'file_path',
        'expiry_date',
        'is_compliant',
        'remarks'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'is_compliant' => 'boolean',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function getStatusColorAttribute()
    {
        if ($this->expiry_date && $this->expiry_date->isPast()) {
            return 'red'; // Expired
        }
        if ($this->expiry_date && $this->expiry_date->diffInDays(now()) < 30) {
            return 'yellow'; // Expiring soon
        }
        return 'green';
    }
}
