<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EquipmentCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'icon', 'display_order', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer'
    ];

    // Relationships
    public function items()
    {
        return $this->hasMany(EquipmentItem::class, 'category_id')
            ->where('is_active', true)
            ->orderBy('name');
    }

    public function workshopTools()
    {
        return $this->hasMany(WorkshopTool::class, 'category_id', 'id')
             ->where('is_active', true);
    }

    public function allItems()
    {
        return $this->hasMany(EquipmentItem::class, 'category_id');
    }

    public function certificateTemplates()
    {
        return $this->hasMany(CertificateTemplate::class, 'equipment_category_id');
    }

    public function templates()
    {
        return $this->hasMany(CertificateTemplate::class, 'equipment_category_id');
    }

    public function itemTemplates()
    {
        return $this->hasMany(EquipmentItem::class, 'category_id')
            ->where('record_type', 'template');
    }

    public function attributeTemplates()
    {
        return $this->hasMany(EquipmentAttributeTemplate::class, 'category_id')
            ->orderBy('display_order');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }
}
