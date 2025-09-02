<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

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
        
        if ($request->filled('search_mobile')) {
            $query->where('mobile', 'like', '%' . $request->get('search_mobile') . '%');
        }
        
        if ($request->filled('search_tax_id')) {
            $query->where('tax_id_code', 'like', '%' . $request->get('search_tax_id') . '%');
        }
        
        // Handle advanced search options
        if ($request->filled('search_gender')) {
            $query->where('gender', $request->get('search_gender'));
        }
        
        if ($request->filled('search_status')) {
            $query->where('status', (bool) $request->get('search_status'));
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
        
        if (!in_array($sortField, ['name', 'surname', 'email', 'mobile', 'status', 'created_at'])) {
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

            $user = User::create([
                'name' => $validated['name'],
                'surname' => $validated['surname'] ?? null,
                'email' => $validated['email'],
                'mobile' => $validated['mobile'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'tax_id_code' => $validated['tax_id_code'] ?? null,
                'status' => (bool) ($validated['status'] ?? true),
                'address' => $validated['address'] ?? null,
                'password' => Hash::make($validated['password']),
            ]);

            // Assign roles
            if (isset($validated['roles'])) {
                $user->assignRole($validated['roles']);
            }

            // Assign companies
            if (isset($validated['companies'])) {
                $companyData = [];
                foreach ($validated['companies'] as $companyId) {
                    $companyData[$companyId] = [
                        'is_primary' => isset($validated['primary_company']) && $validated['primary_company'] == $companyId,
                        'joined_at' => now(),
                    ];
                }
                $user->companies()->attach($companyData);
            }

            return redirect()->route('users.index')
                ->with('success', "User '{$user->full_name}' has been created successfully.");

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
        return view('users.edit', compact('user', 'roles', 'companies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $validated = $request->validated();

            $user->update([
                'name' => $validated['name'],
                'surname' => $validated['surname'] ?? null,
                'email' => $validated['email'],
                'mobile' => $validated['mobile'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'tax_id_code' => $validated['tax_id_code'] ?? null,
                'status' => (bool) ($validated['status'] ?? true),
                'address' => $validated['address'] ?? null,
            ]);

            if (isset($validated['password']) && !empty($validated['password'])) {
                $user->update(['password' => Hash::make($validated['password'])]);
            }

            // Update roles
            $user->syncRoles($validated['roles'] ?? []);

            // Update companies
            if (isset($validated['companies'])) {
                $companyData = [];
                foreach ($validated['companies'] as $companyId) {
                    $companyData[$companyId] = [
                        'is_primary' => isset($validated['primary_company']) && $validated['primary_company'] == $companyId,
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
}
