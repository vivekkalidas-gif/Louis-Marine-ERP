<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Str;

trait LogsActivity
{
    /**
     * Boot the trait
     */
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            $model->logActivity('created');
        });

        static::updated(function ($model) {
            $model->logActivity('updated');
        });

        static::deleted(function ($model) {
            $model->logActivity('deleted');
        });
    }

    /**
     * Log the activity
     */
    protected function logActivity(string $action)
    {
        // Skip logging if no authenticated user (e.g., seeding, console commands)
        if (!auth()->check()) {
            return;
        }

        $oldValues = null;
        $newValues = null;

        if ($action === 'created') {
            $newValues = $this->getLoggableAttributes();
        } elseif ($action === 'updated') {
            $oldValues = $this->getOriginalLoggableAttributes();
            $newValues = $this->getLoggableAttributes();
            
            // Only log if there are actual changes
            if ($oldValues === $newValues) {
                return;
            }
        } elseif ($action === 'deleted') {
            $oldValues = $this->getLoggableAttributes();
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => get_class($this),
            'model_id' => $this->id,
            'module' => $this->getModuleName(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get attributes that should be logged
     */
    protected function getLoggableAttributes(): array
    {
        $attributes = $this->getAttributes();
        
        // Remove sensitive or unnecessary fields
        $excludedFields = array_merge(
            ['password', 'remember_token', 'created_at', 'updated_at'],
            $this->getHiddenLoggableAttributes()
        );

        return array_diff_key($attributes, array_flip($excludedFields));
    }

    /**
     * Get original attributes that should be logged
     */
    protected function getOriginalLoggableAttributes(): array
    {
        $attributes = $this->getOriginal();
        
        $excludedFields = array_merge(
            ['password', 'remember_token', 'created_at', 'updated_at'],
            $this->getHiddenLoggableAttributes()
        );

        return array_diff_key($attributes, array_flip($excludedFields));
    }

    /**
     * Get additional fields to exclude from logging
     * Override this method in your model to add more fields
     */
    protected function getHiddenLoggableAttributes(): array
    {
        return property_exists($this, 'hiddenLoggableAttributes') 
            ? $this->hiddenLoggableAttributes 
            : [];
    }

    /**
     * Determine the module name from the model
     * Override this method in your model for custom module names
     */
    protected function getModuleName(): string
    {
        if (property_exists($this, 'moduleName')) {
            return $this->moduleName;
        }

        // Auto-detect module from namespace
        $namespace = get_class($this);
        
        // Extract module from namespace (e.g., App\Models\Accounts\Invoice => Accounts)
        if (preg_match('/Models\\\\(\w+)\\\\/', $namespace, $matches)) {
            return $matches[1];
        }

        // Fallback: use model name
        return class_basename($this);
    }

    /**
     * Get activity logs for this model
     */
    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'model', 'model_type', 'model_id');
    }
}
