<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateTemplateField extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'section_id',
        'field_name',
        'field_label',
        'field_type',
        'field_options',
        'is_required',
        'validation_rules',
        'default_value',
        'placeholder',
        'help_text',
        'display_order',
        'width',
    ];

    protected $casts = [
        'field_options' => 'array',
        'validation_rules' => 'array',
        'is_required' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Get the section that owns this field
     */
    public function section()
    {
        return $this->belongsTo(CertificateTemplateSection::class, 'section_id');
    }

    /**
     * Get all values for this field
     */
    public function values()
    {
        return $this->hasMany(CertificateFieldValue::class, 'certificate_template_field_id');
    }

    /**
     * Get validation rules for this field
     */
    public function getValidationRule()
    {
        $rules = [];
        
        if ($this->is_required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }
        
        // Add type-specific validation
        switch ($this->field_type) {
            case 'number':
                $rules[] = 'numeric';
                break;
            case 'date':
                $rules[] = 'date';
                break;
            case 'email':
                $rules[] = 'email';
                break;
        }
        
        // Add custom validation rules from JSON
        if ($this->validation_rules) {
            $rules = array_merge($rules, $this->validation_rules);
        }
        
        return implode('|', $rules);
    }
}
