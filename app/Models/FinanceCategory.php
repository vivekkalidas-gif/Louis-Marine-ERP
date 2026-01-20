<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceCategory extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'type', // income, expense
        'color',
        'is_system',
        'description',
    ];

    public function incomes()
    {
        return $this->hasMany(Income::class, 'category_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'category_id');
    }
}
