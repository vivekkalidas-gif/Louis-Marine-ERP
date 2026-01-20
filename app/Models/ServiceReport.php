<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ServiceJob;
use App\Models\User;

class ServiceReport extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'service_job_id', 'engineer_id', 'report_date',
        'work_description', 'recommendations', 'hours_worked',
        'status', 'customer_signature_path', 'total_service_amount'
    ];

    protected $casts = [
        'report_date' => 'date',
    ];

    public function job() {
        return $this->belongsTo(ServiceJob::class, 'service_job_id');
    }

    public function engineer() {
        return $this->belongsTo(User::class, 'engineer_id');
    }

    public function items()
    {
        return $this->hasMany(ServiceReportItem::class);
    }

    public function charges()
    {
        return $this->hasMany(ServiceReportCharge::class);
    }
}
