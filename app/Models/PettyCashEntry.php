<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PettyCashEntry extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'date',
        'type',
        'amount',
        'description',
        'received_by',
        'approved_by',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];
}