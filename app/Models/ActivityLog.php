<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ActivityLog extends Model
{
    use HasUuids;
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'module',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Get the user who performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the model that was affected
     */
    public function model()
    {
        return $this->morphTo('model', 'model_type', 'model_id');
    }

    /**
     * Get a formatted summary of changes
     */
    public function getChangesSummaryAttribute(): string
    {
        if ($this->action === 'created') {
            return 'Record created';
        }

        if ($this->action === 'deleted') {
            return 'Record deleted';
        }

        if ($this->action === 'updated' && $this->old_values && $this->new_values) {
            $changes = [];
            foreach ($this->new_values as $key => $newValue) {
                $oldValue = $this->old_values[$key] ?? null;
                if ($oldValue != $newValue) {
                    $changes[] = ucfirst(str_replace('_', ' ', $key));
                }
            }
            return count($changes) > 0 ? 'Changed: ' . implode(', ', $changes) : 'No changes detected';
        }

        return 'Unknown action';
    }

    /**
     * Scope to filter by module
     */
    public function scopeModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope to filter by action
     */
    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter by user
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by model type
     */
    public function scopeModelType($query, string $modelType)
    {
        return $query->where('model_type', $modelType);
    }
}
