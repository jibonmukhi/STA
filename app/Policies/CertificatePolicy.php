<?php

namespace App\Policies;

use App\Models\Certificate;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CertificatePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Allow all authenticated users to view certificates (filtered in controller)
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Certificate $certificate): bool
    {
        // STA managers can view all certificates
        if ($user->hasRole('sta_manager')) {
            return true;
        }

        // Company managers can view certificates from their companies or their own
        if ($user->hasRole('company_manager')) {
            $userCompanyIds = $user->companies()->pluck('companies.id')->toArray();
            return $certificate->user_id === $user->id || in_array($certificate->company_id, $userCompanyIds);
        }

        // End users can only view their own certificates
        if ($user->hasRole('end_user')) {
            return $certificate->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only STA managers and company managers can create certificates
        return $user->hasRole(['sta_manager', 'company_manager']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Certificate $certificate): bool
    {
        // STA managers can update all certificates
        if ($user->hasRole('sta_manager')) {
            return true;
        }

        // Company managers can update certificates from their companies or their own
        if ($user->hasRole('company_manager')) {
            $userCompanyIds = $user->companies()->pluck('companies.id')->toArray();
            return $certificate->user_id === $user->id || in_array($certificate->company_id, $userCompanyIds);
        }

        // End users cannot update certificates
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Certificate $certificate): bool
    {
        // STA managers can delete all certificates
        if ($user->hasRole('sta_manager')) {
            return true;
        }

        // Company managers can delete certificates from their companies or their own
        if ($user->hasRole('company_manager')) {
            $userCompanyIds = $user->companies()->pluck('companies.id')->toArray();
            return $certificate->user_id === $user->id || in_array($certificate->company_id, $userCompanyIds);
        }

        // End users cannot delete certificates
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Certificate $certificate): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Certificate $certificate): bool
    {
        return false;
    }
}
