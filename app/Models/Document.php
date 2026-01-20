<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasUuids;

    protected $fillable = ['title', 'file_path', 'file_type', 'expiry_date', 'description'];

    protected $casts = ['expiry_date' => 'date'];

    public function documentable()
    {
        return $this->morphTo();
    }
}
