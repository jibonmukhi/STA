<?php

namespace App\Traits;

use App\Services\AuditLogService;
use Illuminate\Database\Eloquent\Model;

trait HasAuditLog
{
    /**
     * Boot the trait
     */
    protected static function bootHasAuditLog()
    {
        // Log when a model is created
        static::created(function (Model $model) {
            if (!$model->shouldSkipAuditLog()) {
                AuditLogService::logCreated($model);
            }
        });

        // Log when a model is updated
        static::updating(function (Model $model) {
            if (!$model->shouldSkipAuditLog()) {
                $model->oldValues = $model->getOriginal();
            }
        });

        static::updated(function (Model $model) {
            if (!$model->shouldSkipAuditLog() && isset($model->oldValues)) {
                AuditLogService::logUpdated($model, $model->oldValues);
                unset($model->oldValues);
            }
        });

        // Log when a model is deleted
        static::deleted(function (Model $model) {
            if (!$model->shouldSkipAuditLog()) {
                AuditLogService::logDeleted($model);
            }
        });

        // Log when a soft-deleted model is restored
        if (method_exists(static::class, 'restored')) {
            static::restored(function (Model $model) {
                if (!$model->shouldSkipAuditLog()) {
                    AuditLogService::logRestored($model);
                }
            });
        }
    }

    /**
     * Get the name to display in audit logs
     */
    public function getAuditLogName(): string
    {
        // Try various common name fields
        $nameFields = ['name', 'title', 'full_name', 'display_name', 'email'];

        foreach ($nameFields as $field) {
            if (isset($this->$field)) {
                return $this->$field;
            }
        }

        // If the model has a custom method for displaying
        if (method_exists($this, '__toString')) {
            return (string) $this;
        }

        // Default to model class name with ID
        return class_basename($this) . ' #' . $this->getKey();
    }

    /**
     * Check if audit logging should be skipped
     */
    public function shouldSkipAuditLog(): bool
    {
        // Override this in your model if you want to conditionally skip logging
        return property_exists($this, 'skipAuditLog') && $this->skipAuditLog === true;
    }

    /**
     * Temporarily skip audit logging for this instance
     */
    public function withoutAuditLog(): self
    {
        $this->skipAuditLog = true;
        return $this;
    }

    /**
     * Re-enable audit logging for this instance
     */
    public function withAuditLog(): self
    {
        $this->skipAuditLog = false;
        return $this;
    }

    /**
     * Get the fields that should be excluded from audit logs
     */
    public function getAuditExclude(): array
    {
        // Override this in your model to exclude specific fields
        return property_exists($this, 'auditExclude')
            ? $this->auditExclude
            : ['password', 'remember_token'];
    }

    /**
     * Get audit logs for this model
     */
    public function auditLogs()
    {
        return \App\Models\AuditLog::where('model_type', get_class($this))
            ->where('model_id', $this->getKey())
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get the latest audit log entry for this model
     */
    public function latestAuditLog()
    {
        return $this->auditLogs()->first();
    }

    /**
     * Get audit logs by action
     */
    public function auditLogsByAction(string $action)
    {
        return $this->auditLogs()->where('action', $action);
    }

    /**
     * Check if model has audit logs
     */
    public function hasAuditLogs(): bool
    {
        return $this->auditLogs()->exists();
    }

    /**
     * Get creation audit log
     */
    public function getCreationAuditLog()
    {
        return $this->auditLogsByAction('created')->first();
    }

    /**
     * Get last update audit log
     */
    public function getLastUpdateAuditLog()
    {
        return $this->auditLogsByAction('updated')->first();
    }

    /**
     * Get who created this model
     */
    public function getCreatedBy()
    {
        $log = $this->getCreationAuditLog();
        return $log ? $log->user : null;
    }

    /**
     * Get who last updated this model
     */
    public function getLastUpdatedBy()
    {
        $log = $this->getLastUpdateAuditLog();
        return $log ? $log->user : null;
    }
}