<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Customer;
use App\Models\Vessel;
use App\Models\ServiceJob;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\User;

class Invoice extends Model
{
    use HasFactory, HasUuids, SoftDeletes, \App\Traits\LogsActivity;

    protected $moduleName = 'Sales';

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'vessel_id',
        'service_job_id',
        'quotation_id',
        'date',
        'due_date',
        'status',
        'subtotal',
        'tax_amount',
        'discount',
        'total_amount',
        'paid_amount',
        'notes',
        'created_by',
        'journal_entry_id',
        'reference'
    ];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
    ];

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function job() {
        return $this->belongsTo(ServiceJob::class, 'service_job_id');
    }

    public function serviceJob() {
        return $this->belongsTo(ServiceJob::class, 'service_job_id');
    }

    public function vessel() {
        return $this->belongsTo(Vessel::class);
    }

    public function items() {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments() {
        return $this->hasMany(Payment::class);
    }

    public function contacts() {
        return $this->belongsToMany(CustomerContact::class, 'invoice_contacts')
                    ->withPivot('type')
                    ->withTimestamps();
    }

    public function journalEntry()
    {
        return $this->belongsTo(\App\Models\Accounting\JournalEntry::class);
    }

    public function getBalanceAttribute()
    {
        return $this->total_amount - $this->paid_amount;
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
