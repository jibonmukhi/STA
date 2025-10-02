<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Services\AuditLogService;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Role::with('permissions');
        
        // Handle individual field searches
        if ($request->filled('search_name')) {
            $query->where('name', 'like', '%' . $request->get('search_name') . '%');
        }
        
        // Handle advanced search options
        if ($request->filled('search_permission')) {
            $query->whereHas('permissions', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->get('search_permission') . '%');
            });
        }
        
        // Handle users count filter
        if ($request->filled('search_users_count')) {
            $usersCount = (int) $request->get('search_users_count');
            $query->whereHas('users', function($q) {}, '=', $usersCount);
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
        
        if (!in_array($sortField, ['name', 'created_at'])) {
            $sortField = 'name';
        }
        
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }
        
        $roles = $query->orderBy($sortField, $sortDirection)
                      ->paginate($perPage)
                      ->withQueryString();
        
        // Get permissions for filter dropdown
        $permissions = Permission::orderBy('name')->get();
        
        return view('roles.index', compact('roles', 'permissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::all();
        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'permissions' => 'array'
        ]);

        $role = Role::create(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->givePermissionTo($request->permissions);
        }

        // Log role creation
        AuditLogService::logCustom(
            'role_created',
            "Created new role: {$role->name}",
            'roles',
            'info',
            [
                'role_id' => $role->id,
                'role_name' => $role->name,
                'permissions' => $request->permissions ?? [],
                'created_by' => auth()->id()
            ]
        );

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all();
        return view('roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array'
        ]);

        $oldName = $role->name;
        $oldPermissions = $role->permissions->pluck('name')->toArray();
        $newPermissions = $request->permissions ?? [];

        $role->update(['name' => $request->name]);
        $role->syncPermissions($newPermissions);

        // Log role update
        AuditLogService::logCustom(
            'role_updated',
            "Updated role: {$role->name}",
            'roles',
            'info',
            [
                'role_id' => $role->id,
                'old_name' => $oldName,
                'new_name' => $request->name,
                'old_permissions' => $oldPermissions,
                'new_permissions' => $newPermissions,
                'updated_by' => auth()->id()
            ]
        );

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')->with('error', 'Cannot delete role that is assigned to users.');
        }

        $roleName = $role->name;
        $permissions = $role->permissions->pluck('name')->toArray();

        // Log role deletion
        AuditLogService::logCustom(
            'role_deleted',
            "Deleted role: {$roleName}",
            'roles',
            'warning',
            [
                'role_id' => $role->id,
                'role_name' => $roleName,
                'had_permissions' => $permissions,
                'deleted_by' => auth()->id()
            ]
        );

        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }
}
