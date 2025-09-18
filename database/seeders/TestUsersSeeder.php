<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test company first
        $company = Company::firstOrCreate([
            'name' => 'Test Company Ltd',
            'email' => 'info@testcompany.com',
            'phone' => '+1234567890',
            'piva' => '12345678901',
            'website' => 'https://testcompany.com',
            'address' => '123 Test Street, Test City, TC 12345',
            'active' => true,
            'ateco_code' => '62010'
        ]);

        // 1. STA Manager (Super Admin)
        $staManager = User::firstOrCreate([
            'email' => 'admin@sta.com'
        ], [
            'name' => 'John',
            'surname' => 'Administrator',
            'date_of_birth' => '1985-01-15',
            'place_of_birth' => 'New York',
            'country' => 'US',
            'phone' => '+1-555-0101',
            'gender' => 'male',
            'cf' => 'ADMJHN85A15F205X',
            'address' => '456 Admin Street, Admin City, AC 12345',
            'status' => 'active',
            'password' => Hash::make('admin123'),
        ]);

        // Assign STA Manager role
        $staManager->assignRole('sta_manager');

        // Attach to company as owner
        $staManager->companies()->syncWithoutDetaching([
            $company->id => [
                'is_primary' => true,
                'role_in_company' => 'Owner',
                'joined_at' => now(),
                'percentage' => 100
            ]
        ]);

        // 2. Company Manager
        $companyManager = User::firstOrCreate([
            'email' => 'manager@testcompany.com'
        ], [
            'name' => 'Sarah',
            'surname' => 'Manager',
            'date_of_birth' => '1990-05-20',
            'place_of_birth' => 'Los Angeles',
            'country' => 'US',
            'phone' => '+1-555-0202',
            'gender' => 'female',
            'cf' => 'MNGSRH90E20L663Y',
            'address' => '789 Manager Avenue, Manager City, MC 12345',
            'status' => 'active',
            'password' => Hash::make('manager123'),
        ]);

        // Assign Company Manager role
        $companyManager->assignRole('company_manager');

        // Attach to company as manager
        $companyManager->companies()->syncWithoutDetaching([
            $company->id => [
                'is_primary' => true,
                'role_in_company' => 'Manager',
                'joined_at' => now()->subMonths(6),
                'percentage' => 25
            ]
        ]);

        // 3. End User
        $endUser = User::firstOrCreate([
            'email' => 'user@testcompany.com'
        ], [
            'name' => 'Mike',
            'surname' => 'Employee',
            'date_of_birth' => '1992-08-10',
            'place_of_birth' => 'Chicago',
            'country' => 'US',
            'phone' => '+1-555-0303',
            'gender' => 'male',
            'cf' => 'EMPMKE92M10L219Z',
            'address' => '321 Employee Road, Employee City, EC 12345',
            'status' => 'active',
            'password' => Hash::make('user123'),
        ]);

        // Assign End User role
        $endUser->assignRole('end_user');

        // Attach to company as employee
        $endUser->companies()->syncWithoutDetaching([
            $company->id => [
                'is_primary' => true,
                'role_in_company' => 'Employee',
                'joined_at' => now()->subYear(),
                'percentage' => 5
            ]
        ]);

        // 4. Pending User (for testing approvals)
        $pendingUser = User::firstOrCreate([
            'email' => 'pending@testcompany.com'
        ], [
            'name' => 'Jane',
            'surname' => 'Pending',
            'date_of_birth' => '1995-12-25',
            'place_of_birth' => 'Miami',
            'country' => 'US',
            'phone' => '+1-555-0404',
            'gender' => 'female',
            'cf' => 'PENJAN95T25F158W',
            'address' => '654 Pending Lane, Pending City, PC 12345',
            'status' => 'parked', // This user needs approval
            'password' => Hash::make('pending123'),
        ]);

        // Assign End User role (but status is parked)
        $pendingUser->assignRole('end_user');

        // Display credentials
        $this->command->info('Test users created successfully!');
        $this->command->line('');
        $this->command->line('=== LOGIN CREDENTIALS ===');
        $this->command->line('');

        $this->command->line('🔴 STA MANAGER (Super Admin):');
        $this->command->line('   Email: admin@sta.com');
        $this->command->line('   Password: admin123');
        $this->command->line('   Access: Full system administration');
        $this->command->line('');

        $this->command->line('🟡 COMPANY MANAGER:');
        $this->command->line('   Email: manager@testcompany.com');
        $this->command->line('   Password: manager123');
        $this->command->line('   Access: Company-level management');
        $this->command->line('');

        $this->command->line('🟢 END USER:');
        $this->command->line('   Email: user@testcompany.com');
        $this->command->line('   Password: user123');
        $this->command->line('   Access: Personal dashboard only');
        $this->command->line('');

        $this->command->line('⚪ PENDING USER (for testing approvals):');
        $this->command->line('   Email: pending@testcompany.com');
        $this->command->line('   Password: pending123');
        $this->command->line('   Status: Parked (needs approval)');
        $this->command->line('');

        $this->command->line('After login, each user will be redirected to their role-specific dashboard.');
    }
}