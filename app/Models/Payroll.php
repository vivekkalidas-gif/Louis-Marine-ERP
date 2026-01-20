<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payroll extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'month', // YYYY-MM
        'status', // draft, finalized, paid
        'processed_date',
        'total_amount',
        'processed_by',
    ];

    protected $casts = [
        'processed_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function entries()
    {
        return $this->hasMany(PayrollEntry::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
