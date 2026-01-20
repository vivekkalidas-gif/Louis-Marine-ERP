<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DispatchItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'dispatch_note_id',
        'description',
        'serial_number',
        'quantity',
        'remarks'
    ];

    public function dispatchNote()
    {
        return $this->belongsTo(DispatchNote::class);
    }
}
