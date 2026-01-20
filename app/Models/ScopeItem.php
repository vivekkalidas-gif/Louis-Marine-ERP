<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScopeItem extends Model
{
    protected $fillable = ['category_id', 'name', 'description', 'unit', 'default_checklist', 'is_active'];

    protected $casts = [
        'default_checklist' => 'array',
        'is_active' => 'boolean'
    ];

    public function category()
    {
        return $this->belongsTo(ScopeCategory::class);
    }
}
