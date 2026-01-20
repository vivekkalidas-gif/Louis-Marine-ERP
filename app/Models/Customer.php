<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasUuids, SoftDeletes, \App\Traits\LogsActivity;

    protected $moduleName = 'Sales';

    protected $fillable = [
        'company_name',
        'type', // Owner, Manager, Agent
        'status', // Active, Inactive
        'cr_number',
        'vat_number',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'website',
        'notes',
    ];

    protected $appends = ['name'];

    /**
     * Get the customer's name (alias for company_name)
     */
    public function getNameAttribute()
    {
        return $this->company_name;
    }

    public function contacts()
    {
        return $this->hasMany(CustomerContact::class);
    }

    public function vessels()
    {
        return $this->hasMany(Vessel::class);
    }

    public function documents()
    {
        return $this->hasMany(CustomerDocument::class);
    }

    public function customerNotes()
    {
        return $this->hasMany(CustomerNote::class);
    }

    public function invoices()
    {
        return $this->hasMany(\App\Models\Invoice::class);
    }

    public function ledgerEntries()
    {
        return $this->hasMany(\App\Models\LedgerEntry::class);
    }

    public function getOutstandingBalanceAttribute()
    {
        // Get the latest ledger entry balance, or 0
        return $this->ledgerEntries()->latest('created_at')->value('running_balance') ?? 0;
    }
}
