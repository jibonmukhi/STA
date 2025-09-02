<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CompanyPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create company permissions
        $permissions = [
            'view companies',
            'create companies',
            'edit companies',
            'delete companies',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign all company permissions to admin role (if exists)
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        // Assign view companies permission to user role (if exists)
        $userRole = Role::where('name', 'user')->first();
        if ($userRole) {
            $userRole->givePermissionTo('view companies');
        }

        $this->command->info('Company permissions created and assigned successfully.');
    }
}
