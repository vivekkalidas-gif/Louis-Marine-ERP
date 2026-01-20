<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceJobDocument extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'service_job_id', 'title', 'file_path', 'type', 'uploaded_by', 'file_size', 'file_type'
    ];

    public function job() {
        return $this->belongsTo(ServiceJob::class, 'service_job_id');
    }
}
