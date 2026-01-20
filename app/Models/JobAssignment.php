<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobAssignment extends Model
{
    protected $fillable = [
        'service_job_id',
        'user_id',
        'assigned_by',
        'assigned_at',
        'hours_worked',
        'overtime_hours',
        'status',
        'notes'
    ];

    public $timestamps = true;

    public function job()
    {
        return $this->belongsTo(ServiceJob::class, 'service_job_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
