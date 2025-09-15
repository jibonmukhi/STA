<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

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

            $user->fill($validated);

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();

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

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
