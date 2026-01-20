<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceReportCharge extends Model
{
    protected $fillable = ['service_report_id', 'item_description', 'quantity', 'unit_price', 'total_price'];

    public function report()
    {
        return $this->belongsTo(ServiceReport::class, 'service_report_id');
    }
}
