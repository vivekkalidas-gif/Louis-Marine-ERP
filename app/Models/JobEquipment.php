<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobEquipment extends Model
{
    protected $table = 'job_equipment';

    protected $fillable = [
        'service_job_id', 'equipment_item_id', 'quantity', 
        'serial_numbers', 'condition_notes', 'technician_status'
    ];

    protected $casts = [
        'serial_numbers' => 'array',
        'technician_status' => 'array',
        'quantity' => 'integer'
    ];

    // Relationships
    public function job()
    {
        return $this->belongsTo(ServiceJob::class, 'service_job_id');
    }

    public function item()
    {
        return $this->belongsTo(EquipmentItem::class, 'equipment_item_id');
    }

    // Alias for item relationship (for consistency)
    public function equipmentItem()
    {
        return $this->belongsTo(EquipmentItem::class, 'equipment_item_id');
    }
}
