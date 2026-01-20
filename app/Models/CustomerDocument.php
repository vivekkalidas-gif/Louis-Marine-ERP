<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Customer;
use App\Models\Vessel;
use App\Models\User;

class CustomerDocument extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = ['customer_id', 'vessel_id', 'title', 'file_path', 'expiry_date', 'description', 'uploaded_by'];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function vessel()
    {
        return $this->belongsTo(Vessel::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
