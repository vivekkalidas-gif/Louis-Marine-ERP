<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateFieldValue extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'certificate_id',
        'certificate_template_field_id',
        'section_instance',
        'value',
    ];

    /**
     * Get the certificate that owns this value
     */
    public function certificate()
    {
        return $this->belongsTo(Certificate::class);
    }

    /**
     * Get the field definition for this value
     */
    public function definition()
    {
        return $this->belongsTo(CertificateTemplateField::class, 'certificate_template_field_id');
    }
}
