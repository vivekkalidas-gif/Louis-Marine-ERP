<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class JobTimeLog extends Model
{
    use HasUuids;

    protected $fillable = ['service_job_id', 'user_id', 'start_time', 'end_time', 'description'];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function serviceJob()
    {
        return $this->belongsTo(ServiceJob::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
