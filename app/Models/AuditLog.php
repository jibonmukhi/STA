<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'user_role',
        'action',
        'model_type',
        'model_id',
        'model_name',
        'old_values',
        'new_values',
        'changed_fields',
        'ip_address',
        'user_agent',
        'method',
        'url',
        'route_name',
        'description',
        'module',
        'severity',
        'metadata',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'changed_fields' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that performed the action
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
        if ($this->model_type && $this->model_id) {
            return $this->model_type::find($this->model_id);
        }
        return null;
    }

    /**
     * Scope for filtering by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for filtering by action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for filtering by model
     */
    public function scopeByModel($query, $modelType, $modelId = null)
    {
        $query->where('model_type', $modelType);
        if ($modelId) {
            $query->where('model_id', $modelId);
        }
        return $query;
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope for filtering by module
     */
    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope for filtering by severity
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Get formatted action name
     */
    public function getFormattedActionAttribute(): string
    {
        return match($this->action) {
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'restored' => 'Restored',
            'logged_in' => 'Logged In',
            'logged_out' => 'Logged Out',
            'password_changed' => 'Changed Password',
            'email_verified' => 'Verified Email',
            'status_changed' => 'Changed Status',
            'role_assigned' => 'Role Assigned',
            'role_removed' => 'Role Removed',
            'permission_granted' => 'Permission Granted',
            'permission_revoked' => 'Permission Revoked',
            'file_uploaded' => 'File Uploaded',
            'file_deleted' => 'File Deleted',
            'export' => 'Data Exported',
            'import' => 'Data Imported',
            default => ucwords(str_replace('_', ' ', $this->action))
        };
    }

    /**
     * Get action color for badges
     */
    public function getActionColorAttribute(): string
    {
        return match($this->action) {
            'created' => 'success',
            'updated' => 'info',
            'deleted' => 'danger',
            'restored' => 'warning',
            'logged_in' => 'primary',
            'logged_out' => 'secondary',
            'password_changed' => 'warning',
            'email_verified' => 'success',
            'status_changed' => 'info',
            'role_assigned' => 'success',
            'role_removed' => 'warning',
            'permission_granted' => 'success',
            'permission_revoked' => 'danger',
            'file_uploaded' => 'primary',
            'file_deleted' => 'danger',
            'export' => 'info',
            'import' => 'primary',
            default => 'secondary'
        };
    }

    /**
     * Get severity color for badges
     */
    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            'critical' => 'danger',
            'error' => 'danger',
            'warning' => 'warning',
            'info' => 'info',
            default => 'secondary'
        };
    }

    /**
     * Get the display name for the model type
     */
    public function getModelTypeNameAttribute(): string
    {
        if (!$this->model_type) {
            return '-';
        }

        $className = class_basename($this->model_type);
        return ucwords(str_replace('_', ' ', $className));
    }

    /**
     * Get a formatted description of the activity
     */
    public function getActivityDescriptionAttribute(): string
    {
        if ($this->description) {
            return $this->description;
        }

        $user = $this->user_name ?? 'System';
        $action = $this->formatted_action;
        $model = $this->model_type_name;
        $modelName = $this->model_name;

        if ($modelName) {
            return "{$user} {$action} {$model}: {$modelName}";
        } elseif ($model && $model !== '-') {
            return "{$user} {$action} {$model} #{$this->model_id}";
        } else {
            return "{$user} {$action}";
        }
    }

    /**
     * Check if this log entry has recorded changes
     */
    public function hasRecordedChanges(): bool
    {
        return !empty($this->changed_fields) || !empty($this->old_values) || !empty($this->new_values);
    }

    /**
     * Get the changes in a readable format
     */
    public function getFormattedChanges(): array
    {
        $changes = [];

        if ($this->changed_fields) {
            foreach ($this->changed_fields as $field) {
                $oldValue = $this->old_values[$field] ?? null;
                $newValue = $this->new_values[$field] ?? null;

                // Skip if both values are null or the same
                if ($oldValue === $newValue) {
                    continue;
                }

                // Format the field name
                $fieldName = ucwords(str_replace('_', ' ', $field));

                // Format values for display
                $oldDisplay = $this->formatValue($oldValue);
                $newDisplay = $this->formatValue($newValue);

                $changes[$fieldName] = [
                    'old' => $oldDisplay,
                    'new' => $newDisplay,
                ];
            }
        }

        return $changes;
    }

    /**
     * Format a value for display
     */
    private function formatValue($value)
    {
        if (is_null($value)) {
            return '<em>empty</em>';
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value)) {
            return json_encode($value);
        }

        // Truncate long strings
        if (strlen($value) > 100) {
            return substr($value, 0, 100) . '...';
        }

        return $value;
    }
}