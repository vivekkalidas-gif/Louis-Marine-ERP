<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuoteRequest extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'reference_no', 'company_name', 'contact_person', 
        'email', 'phone', 'message', 'status'
    ];

    public function items()
    {
        return $this->hasMany(QuoteRequestItem::class);
    }
}
