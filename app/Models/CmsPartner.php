<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CmsPartner extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = ['name', 'website_url', 'logo_path', 'is_active', 'order'];
}
