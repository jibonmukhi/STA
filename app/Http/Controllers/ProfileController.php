<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\ProfileChangeRequest;
use App\Models\User;
use App\Notifications\ProfileChangeRequestNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Services\AuditLogService;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // Check if user has privileged role
        $privilegedRoles = ['sta_manager', 'company_manager', 'teacher'];
        $hasPrivilegedRole = $user->hasAnyRole($privilegedRoles);

        // Get pending profile change request only for end users
        $pendingRequest = null;
        if (!$hasPrivilegedRole) {
            $pendingRequest = ProfileChangeRequest::where('user_id', $user->id)
                ->where('status', 'pending')
                ->first();
        }

        return view('profile.edit', [
            'user' => $user,
            'pendingRequest' => $pendingRequest,
            'requiresApproval' => !$hasPrivilegedRole,
        ]);
    }

    /**
     * Update the user's profile information (creates change request for approval for end users).
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        try {
            $user = $request->user();

            // Check if user has privileged role (can update directly without approval)
            $privilegedRoles = ['sta_manager', 'company_manager', 'teacher'];
            $hasPrivilegedRole = $user->hasAnyRole($privilegedRoles);

            // If user has privileged role, update directly without approval
            if ($hasPrivilegedRole) {
                return $this->updateDirectly($request);
            }

            // For end users: Create approval request
            // Check if user already has a pending request
            $existingPendingRequest = ProfileChangeRequest::where('user_id', $user->id)
                ->where('status', 'pending')
                ->exists();

            if ($existingPendingRequest) {
                return Redirect::route('profile.edit')
                    ->withErrors(['error' => trans('profile.pending_request_exists_error')])
                    ->withInput();
            }

            $validated = $request->validated();

            // Store current user data
            $currentData = $user->only([
                'name', 'surname', 'email', 'phone', 'mobile',
                'gender', 'date_of_birth', 'place_of_birth', 'country',
                'cf', 'address', 'photo'
            ]);

            // Handle photo upload (store temporarily for approval)
            if ($request->hasFile('photo')) {
                $photoFile = $request->file('photo');
                $fileExtension = strtolower($photoFile->getClientOriginalExtension());
                $fileName = 'pending_' . time() . '_' . uniqid() . '.' . $fileExtension;
                $destinationPath = storage_path('app/public/user-photos');

                // Create directory if it doesn't exist
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                // Move uploaded file to destination
                if (move_uploaded_file($photoFile->getPathname(), $destinationPath . '/' . $fileName)) {
                    $validated['photo'] = 'user-photos/' . $fileName;
                } else {
                    return Redirect::route('profile.edit')
                        ->withErrors(['photo' => 'Failed to upload photo file.'])
                        ->withInput();
                }
            }

            // Determine what changed
            $requestedChanges = [];
            foreach ($validated as $field => $value) {
                if ($field === 'photo') {
                    if (isset($validated['photo'])) {
                        $requestedChanges['photo'] = $validated['photo'];
                    }
                } else {
                    $currentValue = $currentData[$field] ?? null;
                    // Handle date comparison
                    if ($field === 'date_of_birth' && $currentValue instanceof \DateTime) {
                        $currentValue = $currentValue->format('Y-m-d');
                    }
                    if ($value != $currentValue) {
                        $requestedChanges[$field] = $value;
                    }
                }
            }

            // If no changes, redirect back
            if (empty($requestedChanges)) {
                return Redirect::route('profile.edit')
                    ->with('info', trans('profile.no_changes_detected'));
            }

            // Create profile change request
            $changeRequest = ProfileChangeRequest::create([
                'user_id' => $user->id,
                'requested_changes' => $requestedChanges,
                'current_data' => $currentData,
                'status' => 'pending',
                'request_message' => $request->input('request_message'),
            ]);

            // Get company managers from user's companies
            $companyManagers = User::whereHas('companies', function ($query) use ($user) {
                    $query->whereIn('companies.id', $user->companies->pluck('id'));
                })
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'company_manager');
                })
                ->get();

            // Notify company managers
            foreach ($companyManagers as $manager) {
                $manager->notify(new ProfileChangeRequestNotification($changeRequest));
            }

            // Log the request
            AuditLogService::logCustom(
                'profile_change_requested',
                'User requested profile changes',
                'users',
                'info',
                [
                    'user_id' => $user->id,
                    'request_id' => $changeRequest->id,
                    'changed_fields' => array_keys($requestedChanges),
                ]
            );

            return Redirect::route('profile.edit')->with('status', 'profile-change-requested');

        } catch (\Exception $e) {
            return Redirect::route('profile.edit')
                ->withErrors(['error' => 'Failed to submit change request: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Update profile directly without approval (for privileged roles).
     */
    private function updateDirectly(ProfileUpdateRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $user = $request->user();

            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo using native PHP
                if ($user->photo) {
                    $oldPhotoPath = storage_path('app/public/' . $user->photo);
                    if (file_exists($oldPhotoPath)) {
                        unlink($oldPhotoPath);
                    }
                }

                // Use native PHP file operations to avoid fileinfo dependency
                $photoFile = $request->file('photo');
                $fileExtension = strtolower($photoFile->getClientOriginalExtension());
                $fileName = time() . '_' . uniqid() . '.' . $fileExtension;
                $destinationPath = storage_path('app/public/user-photos');

                // Create directory if it doesn't exist
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                // Move uploaded file to destination
                if (move_uploaded_file($photoFile->getPathname(), $destinationPath . '/' . $fileName)) {
                    $validated['photo'] = 'user-photos/' . $fileName;
                } else {
                    return Redirect::route('profile.edit')
                        ->withErrors(['photo' => 'Failed to upload photo file.'])
                        ->withInput();
                }
            }

            // Store old values for audit log
            $oldValues = $user->only(['name', 'surname', 'email', 'phone', 'mobile', 'date_of_birth', 'place_of_birth', 'cf', 'address', 'gender']);
            $changedFields = [];

            $user->fill($validated);

            // Track which fields changed
            foreach ($oldValues as $field => $oldValue) {
                if ($user->isDirty($field)) {
                    $changedFields[$field] = [
                        'old' => $oldValue,
                        'new' => $user->$field
                    ];
                }
            }

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();

            // Log profile update if there were changes
            if (!empty($changedFields)) {
                AuditLogService::logCustom(
                    'profile_updated',
                    'User updated their profile',
                    'users',
                    'info',
                    [
                        'user_id' => $user->id,
                        'changed_fields' => array_keys($changedFields),
                        'changes' => $changedFields,
                        'updated_by' => $user->id
                    ]
                );
            }

            return Redirect::route('profile.edit')->with('status', 'profile-updated');

        } catch (\Exception $e) {
            return Redirect::route('profile.edit')
                ->withErrors(['error' => 'Failed to update profile: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        $userId = $user->id;
        $userEmail = $user->email;
        $userName = $user->full_name;

        // Log account deletion
        AuditLogService::logCustom(
            'account_deleted',
            "User {$userName} deleted their own account",
            'users',
            'critical',
            [
                'user_id' => $userId,
                'user_email' => $userEmail,
                'user_name' => $userName,
                'deleted_by' => $userId,
                'self_deletion' => true
            ]
        );

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
