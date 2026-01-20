<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_locked',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_locked' => 'boolean',
    ];

    /**
     * Check if a date falls within this period
     */
    public function covers($date)
    {
        return $date >= $this->start_date && $date <= $this->end_date;
    }
}
