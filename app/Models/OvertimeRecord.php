<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OvertimeRecord extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'date',
        'hours',
        'job_id',
        'reason',
        'status', // pending, approved, rejected
        'approved_by',
    ];

    protected $casts = [
        'date' => 'date',
        'hours' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function job()
    {
        return $this->belongsTo(ServiceJob::class, 'job_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
