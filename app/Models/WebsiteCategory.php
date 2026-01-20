<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebsiteCategory extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = ['name', 'slug', 'image_path', 'is_active', 'sort_order'];

    public function products()
    {
        return $this->hasMany(WebsiteProduct::class);
    }
}
