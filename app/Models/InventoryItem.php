<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = [
        'item_name',
        'item_code',
        'inventory_category_id',
        'brand',
        'model',
        'unit',
        'purchase_price',
        'selling_price',
        'minimum_stock_level',
        'reorder_quantity',
        'current_stock',
        'status',
        'created_by',
        'costing_method',
        'std_cost',
        'inventory_account_id',
        'cogs_account_id',
        'sales_account_id'
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'std_cost' => 'decimal:2',
        'minimum_stock_level' => 'integer',
        'reorder_quantity' => 'integer',
        'current_stock' => 'integer',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(InventoryCategory::class, 'inventory_category_id');
    }

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class, 'inventory_item_id');
    }
    
    public function inventoryAccount()
    {
        return $this->belongsTo(\App\Models\Accounting\ChartOfAccount::class, 'inventory_account_id');
    }
    
    public function cogsAccount()
    {
        return $this->belongsTo(\App\Models\Accounting\ChartOfAccount::class, 'cogs_account_id');
    }
    
    public function salesAccount()
    {
        return $this->belongsTo(\App\Models\Accounting\ChartOfAccount::class, 'sales_account_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function materialRequests()
    {
        return $this->hasMany(MaterialRequest::class, 'product_id');
    }

    // Accessors
    public function getIsLowStockAttribute()
    {
        return $this->current_stock <= $this->minimum_stock_level;
    }

    public function getStockStatusAttribute()
    {
        if ($this->current_stock == 0) {
            return 'out_of_stock';
        } elseif ($this->is_low_stock) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('current_stock <= minimum_stock_level');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('current_stock', 0);
    }
}
