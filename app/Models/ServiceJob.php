<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Customer; // Added
use App\Models\ServiceReport; // Added
use App\Models\ServiceJobDocument; // Added
use App\Models\Note;



class ServiceJob extends Model
{
    use HasUuids, SoftDeletes;
    
    protected $table = 'service_jobs';

    // Status Constants
    public const STATUS_REQUESTED = 'Requested'; // Was Open
    public const STATUS_ASSIGNED = 'Assigned';
    public const STATUS_IN_PROGRESS = 'In Progress';
    public const STATUS_COMPLETED = 'Completed';
    public const STATUS_CLOSED = 'Closed';
    public const STATUS_CANCELLED = 'Cancelled';

    protected $fillable = [
        'job_number', 'title', 'customer_id', 'vessel_id', 'status', 
        'priority', 'scope_of_work', 'reference_no', 
        'assigned_engineer_id', 'scheduled_start', 'scheduled_end', 'completed_at',
        'job_type', 'job_source', 'scheduled_at', 'service_category', 
        'service_types', 'special_instructions', 'location_details', 'created_by',
        'requested_by_contact_id',
        // Delivery Note fields for Workshop Service
        'delivery_note_number', 'delivered_by', 'delivery_contact_person',
        'delivery_contact_number', 'delivery_notes', 'delivery_received_at'
    ];

    protected $casts = [
        'scheduled_start' => 'datetime',
        'scheduled_end' => 'datetime',
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
        'delivery_received_at' => 'datetime',
        'service_types' => 'array',
    ];

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function vessel() {
        return $this->belongsTo(Vessel::class);
    }

    public function engineer() {
        return $this->belongsTo(User::class, 'assigned_engineer_id');
    }

    public function reports() {
        return $this->hasMany(ServiceReport::class);
    }

    public function activeReport() {
        return $this->hasOne(ServiceReport::class)->latest();
    }

    public function documents() {
        return $this->hasMany(ServiceJobDocument::class);
    }

    public function assignments() {
        return $this->hasMany(JobAssignment::class);
    }

    public function materials() {
        return $this->hasMany(MaterialRequest::class);
    }

    public function requestedBy() {
        return $this->belongsTo(CustomerContact::class, 'requested_by_contact_id');
    }

    public function scopes() {
        return $this->hasMany(JobScope::class);
    }

    public function certificates() {
        return $this->hasMany(Certificate::class);
    }

    public function equipment() {
        return $this->hasMany(JobEquipment::class);
    }
    public function items() {
        return $this->hasMany(JobItem::class);
    }

    public function invoices() {
        return $this->hasMany(Invoice::class);
    }

    public function notes() {
        return $this->morphMany(Note::class, 'notable');
    }
}
