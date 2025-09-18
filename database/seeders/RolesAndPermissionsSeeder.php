<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            'approve users',

            // Company Management
            'view companies',
            'create companies',
            'edit companies',
            'delete companies',
            'manage company users',

            // Role Management (STA Manager only)
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',

            // Dashboard & Reports
            'view dashboard',
            'view system reports',
            'view company reports',
            'view personal reports',

            // System Administration
            'system administration',
            'user approval',
            'manage permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create STA Manager Role (Super Admin - Full Access)
        $staManager = Role::firstOrCreate(['name' => 'sta_manager']);
        $staManager->givePermissionTo(Permission::all());

        // Create Company Manager Role (Company-level admin)
        $companyManager = Role::firstOrCreate(['name' => 'company_manager']);
        $companyManager->givePermissionTo([
            'view users',
            'create users',
            'edit users',
            'manage company users',
            'view companies',
            'edit companies',
            'view dashboard',
            'view company reports',
            'view personal reports',
        ]);

        // Create End User Role (Limited access)
        $endUser = Role::firstOrCreate(['name' => 'end_user']);
        $endUser->givePermissionTo([
            'view dashboard',
            'view personal reports',
        ]);

        // Legacy roles cleanup (if they exist)
        Role::where('name', 'Super Admin')->delete();
        Role::where('name', 'Admin')->delete();
        Role::where('name', 'User')->delete();
    }
}
