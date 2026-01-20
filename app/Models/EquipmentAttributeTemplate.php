<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentAttributeTemplate extends Model
{
    protected $fillable = [
        'category_id', 'attribute_key', 'attribute_label', 
        'attribute_type', 'options', 'is_required', 'display_order'
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'display_order' => 'integer'
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(EquipmentCategory::class);
    }

    // Scopes
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('attribute_label');
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }
}
