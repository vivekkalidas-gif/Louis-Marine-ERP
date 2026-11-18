<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeLedgerEntry extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'date',
        'type',
        'amount',
        'debit',
        'credit',
        'description',
        'reference',
        'created_by'
    ];

    protected $casts = [
        'date' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}