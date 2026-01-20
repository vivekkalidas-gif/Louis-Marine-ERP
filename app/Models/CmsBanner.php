<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CmsBanner extends Model
{
    use HasUuids;

    protected $fillable = ['title', 'image_path', 'link', 'order', 'is_active'];
}
