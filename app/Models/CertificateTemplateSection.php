<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateTemplateSection extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'certificate_template_id',
        'name',
        'description',
        'display_order',
        'is_repeatable',
        'layout_type',
    ];

    protected $casts = [
        'is_repeatable' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Get the template that owns this section
     */
    public function template()
    {
        return $this->belongsTo(CertificateTemplate::class, 'certificate_template_id');
    }

    /**
     * Get all fields in this section
     */
    public function fields()
    {
        return $this->hasMany(CertificateTemplateField::class, 'section_id')->orderBy('display_order');
    }
}
