<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificateType extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = ['name', 'slug', 'validity_months', 'description', 'template_structure'];

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }
}
