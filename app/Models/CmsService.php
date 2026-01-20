<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CmsService extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = ['title', 'slug', 'description', 'content', 'icon_class', 'image_path', 'is_active', 'order'];

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
