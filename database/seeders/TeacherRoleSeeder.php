<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TeacherRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create teacher role
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);

        // Create teacher-specific permissions
        $permissions = [
            // Course permissions
            'manage own courses',
            'view all courses',
            'create course schedules',
            'edit own course schedules',
            'view course students',

            // Certificate permissions
            'issue certificates',
            'view issued certificates',

            // Student management
            'view student progress',
            'update student grades',
            'manage course enrollments',

            // Personal permissions
            'view personal reports',
            'edit own profile',
        ];

        foreach ($permissions as $permissionName) {
            $permission = Permission::firstOrCreate(['name' => $permissionName]);

            // Assign permission to teacher role if not already assigned
            if (!$teacherRole->hasPermissionTo($permission)) {
                $teacherRole->givePermissionTo($permission);
            }
        }

        // Also ensure STA managers have all these permissions
        $staManagerRole = Role::where('name', 'sta_manager')->first();
        if ($staManagerRole) {
            foreach ($permissions as $permissionName) {
                $permission = Permission::where('name', $permissionName)->first();
                if ($permission && !$staManagerRole->hasPermissionTo($permission)) {
                    $staManagerRole->givePermissionTo($permission);
                }
            }
        }

        $this->command->info('Teacher role and permissions created successfully!');
    }
}
