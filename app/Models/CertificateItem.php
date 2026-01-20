<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CertificateItem extends Model
{
    use HasUuids;

    protected $fillable = [
        'certificate_id',
        'equipment_type',
        'serial_number',
        'manufacturer',
        'manufacture_date',
        'last_service_date',
        'next_due_date',
        'condition',
        'remarks',
        'details'
    ];

    protected $casts = [
        'manufacture_date' => 'date',
        'last_service_date' => 'date',
        'next_due_date' => 'date',
        'details' => 'array'
    ];

    public function certificate()
    {
        return $this->belongsTo(Certificate::class);
    }
}
