<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ContactSubmission extends Model
{
    use HasUuids;

    protected $fillable = [
        'name', 
        'email', 
        'company',
        'phone', 
        'subject', 
        'service_type',
        'message', 
        'status',
        'ip_address', 
        'read_at'
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];
}
