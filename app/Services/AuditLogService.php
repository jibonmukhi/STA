<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    /**
     * Log an activity
     */
    public static function log(
        string $action,
        Model $model = null,
        array $oldValues = null,
        array $newValues = null,
        string $description = null,
        string $module = null,
        string $severity = 'info',
        array $metadata = null
    ): AuditLog {
        $user = Auth::user();

        // Prepare changed fields
        $changedFields = [];
        if ($oldValues && $newValues) {
            $changedFields = array_keys(array_diff_assoc($newValues, $oldValues));
        }

        // Get model information
        $modelType = $model ? get_class($model) : null;
        $modelId = $model ? $model->getKey() : null;
        $modelName = null;

        if ($model) {
            // Try to get a display name for the model
            if (method_exists($model, 'getAuditLogName')) {
                $modelName = $model->getAuditLogName();
            } elseif (property_exists($model, 'name')) {
                $modelName = $model->name;
            } elseif (property_exists($model, 'title')) {
                $modelName = $model->title;
            } elseif (method_exists($model, '__toString')) {
                $modelName = (string) $model;
            }
        }

        // Determine module from model if not provided
        if (!$module && $modelType) {
            $module = self::getModuleFromModel($modelType);
        }

        return AuditLog::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'user_email' => $user?->email,
            'user_role' => $user?->roles->first()?->name,
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'model_name' => $modelName,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'changed_fields' => $changedFields,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'method' => Request::method(),
            'url' => Request::fullUrl(),
            'route_name' => Request::route()?->getName(),
            'description' => $description,
            'module' => $module,
            'severity' => $severity,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Log a model creation
     */
    public static function logCreated(Model $model, string $description = null): AuditLog
    {
        return self::log(
            action: 'created',
            model: $model,
            newValues: $model->getAttributes(),
            description: $description
        );
    }

    /**
     * Log a model update
     */
    public static function logUpdated(Model $model, array $oldValues, string $description = null): AuditLog
    {
        return self::log(
            action: 'updated',
            model: $model,
            oldValues: $oldValues,
            newValues: $model->getAttributes(),
            description: $description
        );
    }

    /**
     * Log a model deletion
     */
    public static function logDeleted(Model $model, string $description = null): AuditLog
    {
        return self::log(
            action: 'deleted',
            model: $model,
            oldValues: $model->getAttributes(),
            description: $description
        );
    }

    /**
     * Log a model restoration
     */
    public static function logRestored(Model $model, string $description = null): AuditLog
    {
        return self::log(
            action: 'restored',
            model: $model,
            newValues: $model->getAttributes(),
            description: $description
        );
    }

    /**
     * Log a login
     */
    public static function logLogin(User $user = null): AuditLog
    {
        $user = $user ?? Auth::user();

        return self::log(
            action: 'logged_in',
            description: "{$user->name} logged in",
            module: 'auth',
            metadata: [
                'login_at' => now()->toISOString(),
                'ip' => Request::ip(),
            ]
        );
    }

    /**
     * Log a logout
     */
    public static function logLogout(User $user = null): AuditLog
    {
        $user = $user ?? Auth::user();

        return self::log(
            action: 'logged_out',
            description: "{$user->name} logged out",
            module: 'auth',
            metadata: [
                'logout_at' => now()->toISOString(),
            ]
        );
    }

    /**
     * Log a password change
     */
    public static function logPasswordChange(User $user = null): AuditLog
    {
        $user = $user ?? Auth::user();

        return self::log(
            action: 'password_changed',
            model: $user,
            description: "Password changed for {$user->name}",
            module: 'auth',
            severity: 'warning'
        );
    }

    /**
     * Log a role assignment
     */
    public static function logRoleAssigned(User $user, string $role): AuditLog
    {
        return self::log(
            action: 'role_assigned',
            model: $user,
            description: "Role '{$role}' assigned to {$user->name}",
            module: 'users',
            metadata: ['role' => $role]
        );
    }

    /**
     * Log a role removal
     */
    public static function logRoleRemoved(User $user, string $role): AuditLog
    {
        return self::log(
            action: 'role_removed',
            model: $user,
            description: "Role '{$role}' removed from {$user->name}",
            module: 'users',
            severity: 'warning',
            metadata: ['role' => $role]
        );
    }

    /**
     * Log a file upload
     */
    public static function logFileUpload(string $filename, string $path, Model $model = null): AuditLog
    {
        return self::log(
            action: 'file_uploaded',
            model: $model,
            description: "File uploaded: {$filename}",
            module: 'files',
            metadata: [
                'filename' => $filename,
                'path' => $path,
                'size' => filesize($path) ?? null,
            ]
        );
    }

    /**
     * Log a file deletion
     */
    public static function logFileDelete(string $filename, Model $model = null): AuditLog
    {
        return self::log(
            action: 'file_deleted',
            model: $model,
            description: "File deleted: {$filename}",
            module: 'files',
            severity: 'warning',
            metadata: ['filename' => $filename]
        );
    }

    /**
     * Log data export
     */
    public static function logExport(string $type, int $recordCount = null): AuditLog
    {
        return self::log(
            action: 'export',
            description: "Data exported: {$type}",
            module: 'export',
            metadata: [
                'type' => $type,
                'record_count' => $recordCount,
                'exported_at' => now()->toISOString(),
            ]
        );
    }

    /**
     * Log data import
     */
    public static function logImport(string $type, int $recordCount = null): AuditLog
    {
        return self::log(
            action: 'import',
            description: "Data imported: {$type}",
            module: 'import',
            metadata: [
                'type' => $type,
                'record_count' => $recordCount,
                'imported_at' => now()->toISOString(),
            ]
        );
    }

    /**
     * Log a custom activity
     */
    public static function logCustom(
        string $action,
        string $description,
        string $module = null,
        string $severity = 'info',
        array $metadata = null
    ): AuditLog {
        return self::log(
            action: $action,
            description: $description,
            module: $module,
            severity: $severity,
            metadata: $metadata
        );
    }

    /**
     * Determine module from model class
     */
    private static function getModuleFromModel(string $modelClass): string
    {
        $className = class_basename($modelClass);

        return match($className) {
            'User' => 'users',
            'Company' => 'companies',
            'Course' => 'courses',
            'CourseEnrollment' => 'enrollments',
            'CourseEvent' => 'events',
            'Certificate' => 'certificates',
            'DataVaultCategory', 'DataVaultItem' => 'data_vault',
            'Role', 'Permission' => 'roles',
            default => strtolower($className)
        };
    }

    /**
     * Clean old audit logs
     */
    public static function cleanOldLogs(int $daysToKeep = 90): int
    {
        $cutoffDate = now()->subDays($daysToKeep);

        return AuditLog::where('created_at', '<', $cutoffDate)->delete();
    }
}