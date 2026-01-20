<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vessel extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'name',
        'imo_number',
        'type',
        'flag',
        'owner_name', // Legacy, can use for history or secondary info
        'status',
        'year_built',
        'port_of_registry',
        'official_number',
        'call_sign',
        'company_contact_id'
    ];
    
    public function jobs()
    {
        return $this->hasMany(ServiceJob::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function companyContact()
    {
        return $this->belongsTo(CustomerContact::class, 'company_contact_id');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'notable');
    }
}
