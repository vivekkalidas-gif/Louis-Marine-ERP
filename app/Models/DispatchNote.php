<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DispatchNote extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'dispatch_number',
        'date',
        'type',
        'service_job_id',
        'receiver_name',
        'receiver_address',
        'status',
        'created_by'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(DispatchItem::class);
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function serviceJob()
    {
        return $this->belongsTo(ServiceJob::class, 'service_job_id');
    }
}
