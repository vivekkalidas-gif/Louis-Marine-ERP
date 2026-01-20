<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobScope extends Model
{
    protected $fillable = ['service_job_id', 'scope_item_id', 'quantity', 'remarks', 'technician_status'];

    protected $casts = [
        'technician_status' => 'array'
    ];

    public function job()
    {
        return $this->belongsTo(ServiceJob::class, 'service_job_id');
    }

    public function item()
    {
        return $this->belongsTo(ScopeItem::class, 'scope_item_id');
    }
}
