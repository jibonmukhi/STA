<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
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
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
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
