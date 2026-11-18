<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certificate extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'certificate_type_id',
        'certificate_template_id',
        'customer_id',
        'vessel_id',
        'service_job_id',
        'equipment_category_id', // Defines the scope (e.g. Fire Extinguisher)
        'certificate_number',
        'issue_date',
        'expiry_date',
        'status',
        'created_by',
        'issued_by',
        'verified_by',
        'remarks',
        'job_number',
        'assigned_verifier_id',
        'verified_at',
        'approved_by',
        'verification_notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function vessel()
    {
        return $this->belongsTo(Vessel::class);
    }

    public function job()
    {
        return $this->belongsTo(ServiceJob::class, 'service_job_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function equipmentCategory()
    {
        return $this->belongsTo(EquipmentCategory::class, 'equipment_category_id');
    }

    public function equipmentType()
    {
        return $this->belongsTo(EquipmentItem::class, 'equipment_type_id');
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'assigned_verifier_id');
    }

    // Alias for service job relationship
    public function serviceJob()
    {
        return $this->belongsTo(ServiceJob::class, 'service_job_id');
    }

    public function type()
    {
        return $this->belongsTo(CertificateType::class, 'certificate_type_id');
    }

    public function items()
    {
        return $this->hasMany(CertificateItem::class);
    }

    /**
     * Get the certificate template for this certificate
     */
    public function certificateTemplate()
    {
        return $this->belongsTo(CertificateTemplate::class, 'certificate_template_id');
    }

    /**
     * Get all field values for this certificate
     */
    /**
     * Get all field values for this certificate
     */
    public function fieldValues()
    {
        return $this->hasMany(CertificateFieldValue::class);
    }

    /**
     * Get the verifier for this certificate
     */
    /**
     * Get the assigned verifier for this certificate
     */
    public function assignedVerifier()
    {
        return $this->belongsTo(User::class, 'assigned_verifier_id');
    }

    /**
     * Check if the certificate is verified
     */
    public function isVerified()
    {
        return !is_null($this->verified_at);
    }
}
