<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebsiteProduct extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'website_category_id', 'name', 'slug', 
        'short_description', 'description', 
        'image_path', 'gallery_images', 'specifications',
        'seo_title', 'seo_description', 'seo_keywords',
        'is_active', 'sort_order'
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'specifications' => 'array',
        'is_active' => 'boolean'
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function category()
    {
        return $this->belongsTo(WebsiteCategory::class, 'website_category_id');
    }
}
