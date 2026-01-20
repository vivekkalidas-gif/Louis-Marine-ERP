<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CtaSection extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cms_cta_sections';

    protected $fillable = [
        'section_key',
        'title',
        'description',
        'button_text',
        'button_link',
        'background_color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
