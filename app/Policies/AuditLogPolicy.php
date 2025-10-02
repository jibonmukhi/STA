<?php

namespace App\Policies;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AuditLogPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Only STA Managers can view audit logs
        return $user->hasRole('sta_manager');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AuditLog $auditLog): bool
    {
        // STA Managers can view all audit logs
        if ($user->hasRole('sta_manager')) {
            return true;
        }

        // Users can view their own audit logs (if we want to allow this)
        // return $auditLog->user_id === $user->id;

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Audit logs are created automatically by the system
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AuditLog $auditLog): bool
    {
        // Audit logs should never be updated
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AuditLog $auditLog = null): bool
    {
        // Only STA Managers can delete audit logs (for cleanup)
        return $user->hasRole('sta_manager');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AuditLog $auditLog = null): bool
    {
        // Audit logs don't have soft delete
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AuditLog $auditLog = null): bool
    {
        // Only STA Managers can permanently delete audit logs
        return $user->hasRole('sta_manager');
    }

    /**
     * Determine whether the user can export audit logs.
     */
    public function export(User $user): bool
    {
        // Only STA Managers can export audit logs
        return $user->hasRole('sta_manager');
    }
}