<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class WorkshopTool extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'code',
        'name',
        'type', // Added type
        'brand',
        'model',
        // 'category_id', // Deprecated
        'unit',
        'description',
        'is_active',
        'purchase_date',
        'cost'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'purchase_date' => 'date',
        'cost' => 'decimal:2'
    ];

    // public function category()
    // {
    //     return $this->belongsTo(EquipmentCategory::class, 'category_id');
    // }

    public function requests()
    {
        return $this->hasMany(ToolRequest::class, 'workshop_tool_id');
    }

    public function currentRequest()
    {
        return $this->hasOne(ToolRequest::class, 'workshop_tool_id')
            ->whereNull('returned_at')
            ->whereNull('rejected_at')
            ->whereIn('status', ['issued', 'pending']);
    }

    // Assignee helper
    public function getAssigneeAttribute()
    {
        return $this->currentRequest?->requester;
    }
}
