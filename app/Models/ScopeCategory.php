<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScopeCategory extends Model
{
    protected $fillable = ['name', 'slug', 'order'];

    public function items()
    {
        return $this->hasMany(ScopeItem::class, 'category_id')->where('is_active', true)->orderBy('name');
    }
}
