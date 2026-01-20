<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EquipmentItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id', 'assigned_engineer_id', 'name', 'code', 'brand', 'model', 
        'description', 'attributes', 'unit', 'is_active',
        'calibration_required', 'calibration_expiry_date', 'certificate_path'
    ];

    protected $casts = [
        'attributes' => 'array',
        'is_active' => 'boolean',
        'calibration_required' => 'boolean',
        'calibration_expiry_date' => 'date',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(EquipmentCategory::class);
    }
    
    // Alias for category to match request "Type" terminology
    public function type() {
        return $this->belongsTo(EquipmentCategory::class, 'category_id');
    }

    public function assignedEngineer()
    {
        return $this->belongsTo(User::class, 'assigned_engineer_id');
    }

    public function jobEquipment()
    {
        return $this->hasMany(JobEquipment::class);
    }

    public function toolRequests()
    {
        return $this->hasMany(ToolRequest::class, 'equipment_item_id');
    }

    public function currentRequest()
    {
        return $this->hasOne(ToolRequest::class, 'equipment_item_id')->where('status', ToolRequest::STATUS_ISSUED);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
    
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('calibration_required', true)
                     ->whereDate('calibration_expiry_date', '>', now())
                     ->whereDate('calibration_expiry_date', '<=', now()->addDays($days));
    }

    // Accessors
    public function getStatusAttribute()
    {
        if ($this->currentRequest()->exists()) {
            return 'checked_out';
        }

        if (!$this->calibration_required) {
            return 'not_required';
        }
        
        if (!$this->calibration_expiry_date) {
            return 'pending';
        }

        if ($this->calibration_expiry_date->isPast()) {
            return 'expired';
        }

        if ($this->calibration_expiry_date->diffInDays(now()) <= 30) {
            return 'expiring_soon';
        }

        return 'valid';
    }

    public function getAttribute($key)
    {
        // Allow accessing dynamic attributes directly
        $value = parent::getAttribute($key);
        
        if ($value === null && $this->attributes && isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }
        
        return $value;
    }

    // Helper Methods
    public function getAttributeValue($key, $default = null)
    {
        return $this->attributes[$key] ?? $default;
    }

    public function setAttributeValue($key, $value)
    {
        $attributes = $this->attributes ?? [];
        $attributes[$key] = $value;
        $this->attributes = $attributes;
    }
}
