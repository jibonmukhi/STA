<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Services\AuditLogService;
use App\Mail\CompanyInvitationMail;
use App\Http\Requests\CompanyInvitationRequest;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Company::query();
        
        // Handle individual field searches
        if ($request->filled('search_name')) {
            $query->where('name', 'like', '%' . $request->get('search_name') . '%');
        }
        
        if ($request->filled('search_email')) {
            $query->where('email', 'like', '%' . $request->get('search_email') . '%');
        }
        
        if ($request->filled('search_phone')) {
            $query->where('phone', 'like', '%' . $request->get('search_phone') . '%');
        }
        
        if ($request->filled('search_piva')) {
            $query->where('piva', 'like', '%' . $request->get('search_piva') . '%');
        }
        
        if ($request->filled('search_ateco_code')) {
            $query->where('ateco_code', 'like', '%' . $request->get('search_ateco_code') . '%');
        }
        
        // Handle status search
        if ($request->filled('search_status')) {
            $query->where('active', (bool) $request->get('search_status'));
        }
        
        // Handle date range search
        if ($request->filled('search_date_from')) {
            $query->whereDate('created_at', '>=', $request->get('search_date_from'));
        }
        
        if ($request->filled('search_date_to')) {
            $query->whereDate('created_at', '<=', $request->get('search_date_to'));
        }
        
        // Handle per page
        $perPage = $request->get('per_page', 10);
        if (!in_array($perPage, [5, 10, 25, 50, 100])) {
            $perPage = 10;
        }
        
        // Handle sorting
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        
        if (!in_array($sortField, ['name', 'email', 'phone', 'piva', 'ateco_code', 'active', 'created_at'])) {
            $sortField = 'name';
        }
        
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }
        
        $companies = $query->orderBy($sortField, $sortDirection)
                          ->paginate($perPage)
                          ->withQueryString();
        
        return view('companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('companies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255|unique:companies',
                'phone' => 'nullable|string|max:20',
                'piva' => 'nullable|string|max:50',
                'ateco_code' => 'nullable|string|max:10|regex:/^[0-9]+$/',
                'website' => 'nullable|url|max:255',
                'address' => 'nullable|string|max:500',
                'logo' => 'nullable|file|max:2048',
                'active' => 'sometimes|boolean'
            ]);

            if ($request->hasFile('logo')) {
                $logoFile = $request->file('logo');
                
                // Manual validation for image file extensions
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                $fileExtension = strtolower($logoFile->getClientOriginalExtension());
                
                if (!in_array($fileExtension, $allowedExtensions)) {
                    return redirect()->back()
                        ->withErrors(['logo' => 'Logo must be an image file (jpg, jpeg, png, gif).'])
                        ->withInput();
                }
                
                // Use native PHP file operations to avoid fileinfo dependency
                $fileName = time() . '_' . uniqid() . '.' . $fileExtension;
                $destinationPath = storage_path('app/public/company-logos');
                
                // Create directory if it doesn't exist
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                
                // Move uploaded file to destination
                if (move_uploaded_file($logoFile->getPathname(), $destinationPath . '/' . $fileName)) {
                    $validated['logo'] = 'company-logos/' . $fileName;
                } else {
                    return redirect()->back()
                        ->withErrors(['logo' => 'Failed to upload logo file.'])
                        ->withInput();
                }
            }

            $validated['active'] = (bool) $request->input('active', 0);

            $company = Company::create($validated);

            return redirect()->route('companies.index')
                ->with('success', "Company '{$company->name}' has been created successfully.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create company: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        return view('companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        try {
            $validated = $request->validate([
                'ateco_code' => 'nullable|string|max:10|regex:/^[0-9]+$/',
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255|unique:companies,email,' . $company->id,
                'phone' => 'nullable|string|max:20',
                'piva' => 'nullable|string|max:50',
                'website' => 'nullable|url|max:255',
                'address' => 'nullable|string|max:500',
                'logo' => 'nullable|file|max:2048',
                'active' => 'sometimes|boolean'
            ]);

            if ($request->hasFile('logo')) {
                $logoFile = $request->file('logo');
                
                // Manual validation for image file extensions
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                $fileExtension = strtolower($logoFile->getClientOriginalExtension());
                
                if (!in_array($fileExtension, $allowedExtensions)) {
                    return redirect()->back()
                        ->withErrors(['logo' => 'Logo must be an image file (jpg, jpeg, png, gif).'])
                        ->withInput();
                }
                
                // Delete old logo using native PHP
                if ($company->logo) {
                    $oldLogoPath = storage_path('app/public/' . $company->logo);
                    if (file_exists($oldLogoPath)) {
                        unlink($oldLogoPath);
                    }
                }
                
                // Use native PHP file operations to avoid fileinfo dependency
                $fileName = time() . '_' . uniqid() . '.' . $fileExtension;
                $destinationPath = storage_path('app/public/company-logos');
                
                // Create directory if it doesn't exist
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                
                // Move uploaded file to destination
                if (move_uploaded_file($logoFile->getPathname(), $destinationPath . '/' . $fileName)) {
                    $validated['logo'] = 'company-logos/' . $fileName;
                } else {
                    return redirect()->back()
                        ->withErrors(['logo' => 'Failed to upload logo file.'])
                        ->withInput();
                }
            }

            $oldStatus = $company->active;
            $validated['active'] = (bool) $request->input('active', 0);

            // Log status change if it changed
            if ($oldStatus !== $validated['active']) {
                $statusText = $validated['active'] ? 'activated' : 'deactivated';
                AuditLogService::logCustom(
                    'company_status_changed',
                    "Company {$company->name} was {$statusText}",
                    'companies',
                    'info',
                    [
                        'company_id' => $company->id,
                        'company_name' => $company->name,
                        'old_status' => $oldStatus,
                        'new_status' => $validated['active'],
                        'changed_by' => auth()->id()
                    ]
                );
            }

            $company->update($validated);

            return redirect()->route('companies.index')
                ->with('success', "Company '{$company->name}' has been updated successfully.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update company: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        try {
            $companyName = $company->name;

            // Log company deletion (before actual deletion)
            AuditLogService::logCustom(
                'company_deleted',
                "Company {$companyName} (ID: {$company->id}) was deleted",
                'companies',
                'warning',
                [
                    'company_id' => $company->id,
                    'company_name' => $companyName,
                    'had_users' => $company->users->count(),
                    'deleted_by' => auth()->id()
                ]
            );

            // Delete logo file
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }

            $company->delete();

            return redirect()->route('companies.index')
                ->with('success', "Company '{$companyName}' has been deleted successfully.");

        } catch (\Exception $e) {
            return redirect()->route('companies.index')
                ->with('error', 'Failed to delete company: ' . $e->getMessage());
        }
    }

    /**
     * Show companies for the authenticated company manager
     */
    public function myCompanies(Request $request)
    {
        $user = auth()->user();
        $companies = $user->companies()->with('users')->get();

        return view('my-companies.index', compact('companies'));
    }

    /**
     * Show the company invitation form
     */
    public function showInviteForm()
    {
        return view('companies.invite');
    }

    /**
     * Send company invitation email
     */
    public function sendInvite(CompanyInvitationRequest $request)
    {
        try {
            // Generate unique token and temporary password
            $token = CompanyInvitation::generateToken();
            $tempPassword = CompanyInvitation::generateTemporaryPassword();

            // Create the invitation record
            $invitation = CompanyInvitation::create([
                'token' => $token,
                'company_name' => $request->company_name,
                'company_email' => $request->company_email,
                'company_phone' => $request->company_phone,
                'company_piva' => $request->company_piva,
                'company_ateco_code' => $request->company_ateco_code,
                'manager_username' => $request->manager_email, // Using email as username
                'manager_name' => $request->manager_name,
                'manager_surname' => $request->manager_surname,
                'manager_email' => $request->manager_email,
                'temporary_password' => Hash::make($tempPassword),
                'status' => 'pending',
                'expires_at' => now()->addHours(48), // Expires in 48 hours
                'invited_by' => auth()->id(),
            ]);

            // Generate invitation URL
            $invitationUrl = route('invitation.accept', ['token' => $token]);

            // Send invitation email
            Mail::to($request->manager_email)->send(
                new CompanyInvitationMail($invitation, $tempPassword, $invitationUrl)
            );

            // Log the invitation
            AuditLogService::logCustom(
                'company_invitation_sent',
                "Invitation sent to {$request->manager_email} for company {$request->company_name}",
                'companies',
                'info',
                [
                    'invitation_id' => $invitation->id,
                    'company_name' => $request->company_name,
                    'company_email' => $request->company_email,
                    'manager_email' => $request->manager_email,
                    'invited_by' => auth()->id(),
                    'expires_at' => $invitation->expires_at->toDateTimeString(),
                ]
            );

            return redirect()->route('companies.invite.form')
                ->with('success', "Invitation sent successfully to {$request->manager_email}. The invitation will expire on {$invitation->expires_at->format('F j, Y \a\t g:i A')}.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to send invitation: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show the invitation acceptance page
     */
    public function showAcceptInvitation($token)
    {
        $invitation = CompanyInvitation::where('token', $token)->firstOrFail();

        // Check if invitation is valid
        if ($invitation->isExpired()) {
            return view('invitations.expired', compact('invitation'));
        }

        if ($invitation->isAccepted()) {
            return view('invitations.already-accepted', compact('invitation'));
        }

        return view('invitations.accept', compact('invitation'));
    }

    /**
     * Process the invitation acceptance
     */
    public function acceptInvitation(Request $request, $token)
    {
        try {
            $invitation = CompanyInvitation::where('token', $token)->firstOrFail();

            // Validate invitation status
            if ($invitation->isExpired()) {
                return redirect()->route('invitation.accept', ['token' => $token])
                    ->with('error', 'This invitation has expired.');
            }

            if ($invitation->isAccepted()) {
                return redirect()->route('invitation.accept', ['token' => $token])
                    ->with('error', 'This invitation has already been accepted.');
            }

            // Create the company
            $company = Company::create([
                'name' => $invitation->company_name,
                'email' => $invitation->company_email,
                'phone' => $invitation->company_phone,
                'piva' => $invitation->company_piva,
                'ateco_code' => $invitation->company_ateco_code,
                'active' => true, // Activate the company immediately
            ]);

            // Create the company manager user
            $user = User::create([
                'name' => $invitation->manager_name,
                'surname' => $invitation->manager_surname,
                'email' => $invitation->manager_email,
                'password' => $invitation->temporary_password,
                'status' => 'active', // Activate the user immediately
            ]);

            // Assign company_manager role
            $user->assignRole('company_manager');

            // Link user to company
            $user->companies()->attach($company->id, [
                'is_primary' => true,
                'role_in_company' => 'Manager',
                'joined_at' => now(),
                'percentage' => 100,
            ]);

            // Mark invitation as accepted
            $invitation->markAsAccepted($company->id, $user->id);

            // Log the acceptance
            AuditLogService::logCustom(
                'company_invitation_accepted',
                "Invitation accepted for company {$company->name} by {$user->email}",
                'companies',
                'info',
                [
                    'invitation_id' => $invitation->id,
                    'company_id' => $company->id,
                    'company_name' => $company->name,
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                ]
            );

            return redirect()->route('login')
                ->with('success', 'Invitation accepted successfully! Please login with your credentials. You will be prompted to change your password.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to accept invitation: ' . $e->getMessage());
        }
    }

    /**
     * Display all invited companies (from invitation records)
     */
    public function invitationsList(Request $request)
    {
        $query = CompanyInvitation::with(['inviter', 'company', 'user']);

        // Apply filters
        if ($request->filled('search_company')) {
            $query->where('company_name', 'like', '%' . $request->search_company . '%');
        }

        if ($request->filled('search_email')) {
            $query->where('manager_email', 'like', '%' . $request->search_email . '%');
        }

        if ($request->filled('filter_status')) {
            $query->where('status', $request->filter_status);
        }

        // Get invitations with pagination
        $invitations = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Calculate statistics
        $stats = [
            'total' => CompanyInvitation::count(),
            'pending' => CompanyInvitation::pending()->count(),
            'accepted' => CompanyInvitation::accepted()->count(),
            'expired' => CompanyInvitation::expired()->count(),
        ];

        return view('companies.invitations', compact('invitations', 'stats'));
    }

    /**
     * Show invitation details on a separate page
     */
    public function showInvitationDetails($id)
    {
        $invitation = CompanyInvitation::with(['inviter', 'company', 'user'])->findOrFail($id);

        return view('companies.invitation-details', compact('invitation'));
    }

    /**
     * Resend an invitation
     */
    public function resendInvitation($id)
    {
        try {
            $invitation = CompanyInvitation::findOrFail($id);

            // Check if invitation can be resent
            if ($invitation->isAccepted()) {
                return redirect()->back()
                    ->with('error', 'Cannot resend an accepted invitation.');
            }

            // Generate new token and extend expiration
            $newToken = CompanyInvitation::generateToken();
            $newPassword = CompanyInvitation::generateTemporaryPassword();

            $invitation->update([
                'token' => $newToken,
                'temporary_password' => Hash::make($newPassword),
                'expires_at' => now()->addHours(48),
                'status' => 'pending',
            ]);

            // Generate new invitation URL
            $invitationUrl = route('invitation.accept', ['token' => $newToken]);

            // Resend invitation email
            Mail::to($invitation->manager_email)->send(
                new CompanyInvitationMail($invitation, $newPassword, $invitationUrl)
            );

            // Log the resend
            AuditLogService::logCustom(
                'company_invitation_resent',
                "Invitation resent to {$invitation->manager_email} for company {$invitation->company_name}",
                'companies',
                'info',
                [
                    'invitation_id' => $invitation->id,
                    'company_name' => $invitation->company_name,
                    'manager_email' => $invitation->manager_email,
                    'resent_by' => auth()->id(),
                    'new_expires_at' => $invitation->expires_at->toDateTimeString(),
                ]
            );

            return redirect()->back()
                ->with('success', "Invitation resent successfully to {$invitation->manager_email}. New expiration: {$invitation->expires_at->format('F j, Y \a\t g:i A')}");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to resend invitation: ' . $e->getMessage());
        }
    }

    /**
     * Delete an invitation
     */
    public function destroyInvitation($id)
    {
        try {
            $invitation = CompanyInvitation::findOrFail($id);

            // Prevent deletion of accepted invitations
            if ($invitation->isAccepted()) {
                return redirect()->back()
                    ->with('error', 'Cannot delete an accepted invitation. The company and user have already been created.');
            }

            $companyName = $invitation->company_name;
            $managerEmail = $invitation->manager_email;

            // Log the deletion
            AuditLogService::logCustom(
                'company_invitation_deleted',
                "Invitation deleted for company {$companyName} (Manager: {$managerEmail})",
                'companies',
                'warning',
                [
                    'invitation_id' => $invitation->id,
                    'company_name' => $companyName,
                    'manager_email' => $managerEmail,
                    'status' => $invitation->status,
                    'deleted_by' => auth()->id(),
                ]
            );

            $invitation->delete();

            return redirect()->back()
                ->with('success', "Invitation for {$companyName} has been deleted successfully.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete invitation: ' . $e->getMessage());
        }
    }
}
