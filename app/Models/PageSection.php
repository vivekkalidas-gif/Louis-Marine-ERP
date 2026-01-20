<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PageSection extends Model
{
    use HasUuids;

    protected $fillable = [
        'cms_page_id',
        'section_key', // e.g., 'hero', 'about'
        'title',
        'subtitle',
        'content',
        'image_path',
        'order',
        'is_visible'
    ];

    public function page()
    {
        return $this->belongsTo(CmsPage::class, 'cms_page_id');
    }
}
