<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToolRequest extends Model
{
    protected $fillable = [
        'user_id',
        'workshop_tool_id',
        'status',
        'requested_at',
        'issued_by',
        'issued_at',
        'received_by',
        'returned_at',
        'notes',
        'rejection_reason'
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'issued_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    // Status Constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_ISSUED = 'issued';
    const STATUS_RETURNED = 'returned';
    const STATUS_REJECTED = 'rejected';

    // Relationships
    public function requester()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tool() // Renamed from item()
    {
        return $this->belongsTo(WorkshopTool::class, 'workshop_tool_id');
    }

    // Alias for backward compatibility during refactor if needed (or just use tool)
    public function item() {
        return $this->tool();
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ISSUED);
    }
    
    public function scopeReturned($query)
    {
        return $query->where('status', self::STATUS_RETURNED);
    }

    public function scopeMyRequests($query)
    {
        return $query->where('user_id', auth()->id());
    }
}
