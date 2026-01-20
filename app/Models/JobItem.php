<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class JobItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['service_job_id', 'name', 'quantity', 'description'];

    public function job()
    {
        return $this->belongsTo(ServiceJob::class, 'service_job_id');
    }
}
