<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignSuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assign Super Admin role to the first user
        $user = User::first();
        if ($user) {
            $superAdminRole = Role::where('name', 'Super Admin')->first();
            if ($superAdminRole) {
                $user->assignRole($superAdminRole);
                echo "Super Admin role assigned to user: {$user->email}\n";
            }
        }
    }
}
