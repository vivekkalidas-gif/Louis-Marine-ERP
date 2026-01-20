<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceReportItem extends Model
{
    protected $fillable = ['service_report_id', 'service_type_id', 'description', 'price', 'quantity'];

    public function report()
    {
        return $this->belongsTo(ServiceReport::class, 'service_report_id');
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }
}
