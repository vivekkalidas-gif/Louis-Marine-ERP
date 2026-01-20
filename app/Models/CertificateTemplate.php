<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificateTemplate extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'equipment_category_id',
        'title',
        'is_active',
        'is_default',
        'header_config',
        'footer_config',
        'created_by',
    ];

    protected $casts = [
        'header_config' => 'array',
        'footer_config' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    /**
     * Get the equipment category for this template
     */
    public function equipmentCategory()
    {
        return $this->belongsTo(EquipmentCategory::class);
    }

    /**
     * Get all sections for this template
     */
    public function sections()
    {
        return $this->hasMany(CertificateTemplateSection::class)->orderBy('display_order');
    }

    /**
     * Get all certificates using this template
     */
    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'certificate_template_id');
    }

    /**
     * Get the user who created this template
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the change history for this template
     */
    public function history()
    {
        return $this->hasMany(CertificateTemplateHistory::class, 'template_id');
    }

    /**
     * Scope to get only active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get default template for a category
     */
    public function scopeDefaultForCategory($query, $categoryId)
    {
        return $query->where('equipment_category_id', $categoryId)
                    ->where('is_active', true)
                    ->where('is_default', true);
    }

    /**
     * Log template changes
     */
    public function logChange($changeType, $changes = null)
    {
        CertificateTemplateHistory::create([
            'template_id' => $this->id,
            'changed_by' => auth()->id(),
            'change_type' => $changeType,
            'changes' => $changes,
        ]);
    }
}
