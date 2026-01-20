<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'inventory_category_id', 'supplier_id', 'sku', 'name', 
        'slug', 'description', 'short_description', 'specifications',
        'price', 'current_stock', 'min_stock_level', 'location',
        'is_published', 'sort_order', 'image_paths'
    ];

    protected $casts = [
        'specifications' => 'array',
        'image_paths' => 'array',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
        'price' => 'decimal:2',
    ];

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function category()
    {
        return $this->belongsTo(InventoryCategory::class, 'inventory_category_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }
}
