<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateTemplateHistory extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'template_id',
        'changed_by',
        'change_type',
        'changes',
        'created_at',
    ];

    protected $casts = [
        'changes' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Get the template this history belongs to
     */
    public function template()
    {
        return $this->belongsTo(CertificateTemplate::class, 'template_id');
    }

    /**
     * Get the user who made the change
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
