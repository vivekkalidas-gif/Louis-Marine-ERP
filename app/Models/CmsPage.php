<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CmsPage extends Model
{
    use HasUuids;

    protected $fillable = ['title', 'slug', 'content', 'meta_title', 'meta_description', 'is_published'];

    public function sections()
    {
        return $this->hasMany(PageSection::class, 'cms_page_id')->orderBy('order');
    }
}
