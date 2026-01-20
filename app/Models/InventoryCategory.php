<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryCategory extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = ['name', 'slug', 'description', 'created_by'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class);
    }
}
