<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerContact extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'customer_id', 'name', 'role', 'email', 'phone', 'is_primary',
        'designation', 'contact_type', 'can_request_job', 'can_receive_invoice', 'status'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'can_request_job' => 'boolean',
        'can_receive_invoice' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
