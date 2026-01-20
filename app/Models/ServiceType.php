<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    protected $fillable = ['name', 'code', 'default_price', 'quantity', 'description', 'status'];
}
