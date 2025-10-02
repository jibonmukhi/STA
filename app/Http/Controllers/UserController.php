<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\AuditLogService;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with(['roles', 'companies']);
        
        // Handle individual field searches
        if ($request->filled('search_name')) {
            $query->where('name', 'like', '%' . $request->get('search_name') . '%');
        }
        
        if ($request->filled('search_surname')) {
            $query->where('surname', 'like', '%' . $request->get('search_surname') . '%');
        }
        
        if ($request->filled('search_email')) {
            $query->where('email', 'like', '%' . $request->get('search_email') . '%');
        }
        
        if ($request->filled('search_phone')) {
            $query->where(function($q) use ($request) {
                $q->where('phone', 'like', '%' . $request->get('search_phone') . '%')
                  ->orWhere('mobile', 'like', '%' . $request->get('search_phone') . '%');
            });
        }
        
        if ($request->filled('search_cf')) {
            $query->where('cf', 'like', '%' . $request->get('search_cf') . '%');
        }

        if ($request->filled('search_place_of_birth')) {
            $query->where('place_of_birth', 'like', '%' . $request->get('search_place_of_birth') . '%');
        }
        
        // Handle advanced search options
        if ($request->filled('search_gender')) {
            $query->where('gender', $request->get('search_gender'));
        }
        
        if ($request->filled('search_status')) {
            $query->where('status', $request->get('search_status'));
        }

        // Handle status filter for dashboard links
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        
        if ($request->filled('search_company')) {
            $query->whereHas('companies', function($q) use ($request) {
                $q->where('companies.id', $request->get('search_company'));
            });
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
        
        if (!in_array($sortField, ['name', 'surname', 'email', 'phone', 'mobile', 'cf', 'date_of_birth', 'status', 'created_at'])) {
            $sortField = 'name';
        }
        
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }
        
        $users = $query->orderBy($sortField, $sortDirection)
                      ->paginate($perPage)
                      ->withQueryString();
        
        // Get companies for filter dropdown
        $companies = Company::active()->orderBy('name')->get();
        
        return view('users.index', compact('users', 'companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        $companies = Company::active()->orderBy('name')->get();
        return view('users.create', compact('roles', 'companies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        try {
            
            $validated = $request->validated();

            $userData = [
                'name' => $validated['name'],
                'surname' => $validated['surname'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'place_of_birth' => $validated['place_of_birth'] ?? null,
                'country' => $validated['country'] ?? 'IT',
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'mobile' => $validated['mobile'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'cf' => $validated['cf'] ?? null,
                'address' => $validated['address'] ?? null,
                'status' => $validated['status'] ?? 'parked',
                'password' => Hash::make($validated['password']),
            ];

            // Handle photo upload
            if (isset($validated['photo'])) {
                // Use native PHP file operations to avoid fileinfo dependency  
                $photoFile = $validated['photo'];
                $fileExtension = strtolower($photoFile->getClientOriginalExtension());
                $fileName = time() . '_' . uniqid() . '.' . $fileExtension;
                $destinationPath = storage_path('app/public/user-photos');
                
                // Create directory if it doesn't exist
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                
                // Move uploaded file to destination
                if (move_uploaded_file($photoFile->getPathname(), $destinationPath . '/' . $fileName)) {
                    $userData['photo'] = 'user-photos/' . $fileName;
                } else {
                    return redirect()->back()
                        ->withErrors(['photo' => 'Failed to upload photo file.'])
                        ->withInput();
                }
            }

            $user = User::create($userData);

            // Assign roles
            if (isset($validated['roles'])) {
                $user->assignRole($validated['roles']);

                // Log role assignment
                AuditLogService::logCustom(
                    'roles_assigned',
                    "Assigned roles to user {$user->full_name}: " . implode(', ', (array)$validated['roles']),
                    'users',
                    'info',
                    [
                        'user_id' => $user->id,
                        'roles' => $validated['roles'],
                        'assigned_by' => auth()->id()
                    ]
                );
            }

            // Assign companies with percentages
            if (isset($validated['companies'])) {
                $companyData = [];
                $percentages = $validated['company_percentages'] ?? [];
                
                foreach ($validated['companies'] as $companyId) {
                    $percentage = isset($percentages[$companyId]) ? (float) $percentages[$companyId] : 0;
                    $companyData[$companyId] = [
                        'is_primary' => isset($validated['primary_company']) && $validated['primary_company'] == $companyId,
                        'percentage' => $percentage,
                        'joined_at' => now(),
                    ];
                }
                $user->companies()->attach($companyData);
            }

            $statusMessage = $user->status === 'parked' 
                ? "User '{$user->full_name}' has been created and is pending approval." 
                : "User '{$user->full_name}' has been created successfully.";
                
            return redirect()->route('users.index')
                ->with('success', $statusMessage);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create user: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['roles', 'companies']);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $user->load(['roles', 'companies']);
        $roles = Role::all();
        $companies = Company::active()->orderBy('name')->get();
        
        // Prepare existing companies data for JavaScript
        $existingCompanies = $user->companies->map(function($company) {
            return [
                'id' => $company->id,
                'name' => $company->name,
                'email' => $company->email,
                'percentage' => $company->pivot->percentage,
                'is_primary' => $company->pivot->is_primary
            ];
        })->values(); // Use values() to ensure proper array indexing
        
        return view('users.edit', compact('user', 'roles', 'companies', 'existingCompanies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $validated = $request->validated();

            $userData = [
                'name' => $validated['name'],
                'surname' => $validated['surname'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'place_of_birth' => $validated['place_of_birth'] ?? null,
                'country' => $validated['country'] ?? 'IT',
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'mobile' => $validated['mobile'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'cf' => $validated['cf'] ?? null,
                'address' => $validated['address'] ?? null,
                'status' => $validated['status'] ?? 'parked',
            ];

            // Handle photo upload
            if (isset($validated['photo'])) {
                // Delete old photo using native PHP
                if ($user->photo) {
                    $oldPhotoPath = storage_path('app/public/' . $user->photo);
                    if (file_exists($oldPhotoPath)) {
                        unlink($oldPhotoPath);
                    }
                }
                
                // Use native PHP file operations to avoid fileinfo dependency  
                $photoFile = $validated['photo'];
                $fileExtension = strtolower($photoFile->getClientOriginalExtension());
                $fileName = time() . '_' . uniqid() . '.' . $fileExtension;
                $destinationPath = storage_path('app/public/user-photos');
                
                // Create directory if it doesn't exist
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                
                // Move uploaded file to destination
                if (move_uploaded_file($photoFile->getPathname(), $destinationPath . '/' . $fileName)) {
                    $userData['photo'] = 'user-photos/' . $fileName;
                } else {
                    return redirect()->back()
                        ->withErrors(['photo' => 'Failed to upload photo file.'])
                        ->withInput();
                }
            }

            $user->update($userData);

            if (isset($validated['password']) && !empty($validated['password'])) {
                $user->update(['password' => Hash::make($validated['password'])]);
            }

            // Update roles
            $oldRoles = $user->getRoleNames()->toArray();
            $newRoles = $validated['roles'] ?? [];
            $user->syncRoles($newRoles);

            // Log role changes if any
            if ($oldRoles != $newRoles) {
                AuditLogService::logCustom(
                    'roles_updated',
                    "Updated roles for user {$user->full_name}. Old: [" . implode(', ', $oldRoles) . "], New: [" . implode(', ', $newRoles) . "]",
                    'users',
                    'info',
                    [
                        'user_id' => $user->id,
                        'old_roles' => $oldRoles,
                        'new_roles' => $newRoles,
                        'updated_by' => auth()->id()
                    ]
                );
            }

            // Update companies with percentages
            if (isset($validated['companies'])) {
                $companyData = [];
                $percentages = $validated['company_percentages'] ?? [];
                
                foreach ($validated['companies'] as $companyId) {
                    $percentage = isset($percentages[$companyId]) ? (float) $percentages[$companyId] : 0;
                    $companyData[$companyId] = [
                        'is_primary' => isset($validated['primary_company']) && $validated['primary_company'] == $companyId,
                        'percentage' => $percentage,
                        'joined_at' => now(),
                    ];
                }
                $user->companies()->sync($companyData);
            } else {
                $user->companies()->detach();
            }

            return redirect()->route('users.index')
                ->with('success', "User '{$user->full_name}' has been updated successfully.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update user: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            if ($user->id === auth()->id()) {
                return redirect()->route('users.index')
                    ->with('error', 'You cannot delete your own account.');
            }

            $userName = $user->full_name;
            
            // Delete photo file using native PHP
            if ($user->photo) {
                $photoPath = storage_path('app/public/' . $user->photo);
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }
            }

            // Log user deletion (before actual deletion)
            AuditLogService::logCustom(
                'user_deleted',
                "User {$userName} (ID: {$user->id}, Email: {$user->email}) was deleted",
                'users',
                'warning',
                [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'user_name' => $userName,
                    'deleted_by' => auth()->id(),
                    'had_roles' => $user->getRoleNames()->toArray(),
                    'had_companies' => $user->companies->pluck('name')->toArray()
                ]
            );

            // Detach companies
            $user->companies()->detach();

            $user->delete();

            return redirect()->route('users.index')
                ->with('success', "User '{$userName}' has been deleted successfully.");

        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * Show company users for company managers
     */
    public function companyUsers(Request $request)
    {
        $user = auth()->user();
        $userCompanies = $user->companies;

        // Get all users from the companies this user manages
        $companyUserIds = collect();
        foreach ($userCompanies as $company) {
            $companyUserIds = $companyUserIds->merge($company->users->pluck('id'));
        }

        $query = User::with(['roles', 'companies'])
            ->whereIn('id', $companyUserIds->unique());

        // Apply filters
        if ($request->filled('search_name')) {
            $query->where('name', 'like', '%' . $request->get('search_name') . '%');
        }

        if ($request->filled('search_email')) {
            $query->where('email', 'like', '%' . $request->get('search_email') . '%');
        }

        $users = $query->paginate(15);

        return view('company-users.index', compact('users', 'userCompanies'));
    }

    /**
     * Show form for creating company user
     */
    public function createCompanyUser()
    {
        $user = auth()->user();
        $companies = $user->companies;
        $roles = Role::where('name', '!=', 'sta_manager')->get(); // Company managers can't assign STA manager role

        return view('company-users.create', compact('companies', 'roles'));
    }

    /**
     * Store company user
     */
    public function storeCompanyUser(StoreUserRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'surname' => $request->surname,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'place_of_birth' => $request->place_of_birth,
                'country' => $request->country,
                'gender' => $request->gender,
                'cf' => $request->cf,
                'address' => $request->address,
                'status' => 'parked', // Company-created users need approval
                'password' => Hash::make('password123'), // Default password
            ]);

            // Assign role
            $roleToAssign = $request->role ?? 'end_user';
            $user->assignRole($roleToAssign);

            // Log role assignment
            AuditLogService::logCustom(
                'roles_assigned',
                "Assigned role '{$roleToAssign}' to company user {$user->full_name}",
                'users',
                'info',
                [
                    'user_id' => $user->id,
                    'role' => $roleToAssign,
                    'assigned_by' => auth()->id(),
                    'created_via' => 'company_manager'
                ]
            );

            // Attach to selected companies
            if ($request->companies) {
                foreach ($request->companies as $companyId) {
                    $user->companies()->attach($companyId, [
                        'is_primary' => count($request->companies) === 1,
                        'role_in_company' => $request->role_in_company ?? 'Employee',
                        'joined_at' => now(),
                        'percentage' => $request->percentage ?? 0,
                    ]);
                }
            }

            return redirect()->route('company-users.index')
                ->with('success', 'User created successfully. Default password is: password123');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create user: ' . $e->getMessage())
                ->withInput();
        }
    }
}
