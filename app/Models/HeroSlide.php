<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HeroSlide extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cms_hero_slides';

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'background_image',
        'background_video_url',
        'cta_text_1',
        'cta_link_1',
        'cta_text_2',
        'cta_link_2',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
