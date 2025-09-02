<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class TestUserCreation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test user creation functionality with company associations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing User creation with company associations...');

        try {
            // Get companies for association
            $companies = Company::take(3)->get();
            if ($companies->count() < 2) {
                $this->error('Need at least 2 companies in database. Please run companies:test first.');
                return Command::FAILURE;
            }

            // Create test users with various profiles
            $users = [
                [
                    'name' => 'Mario',
                    'surname' => 'Rossi',
                    'email' => 'mario.rossi@testmail.com',
                    'mobile' => '+39 333 123 4567',
                    'gender' => 'male',
                    'date_of_birth' => '1985-06-15',
                    'tax_id_code' => 'RSSMRA85H15F205X',
                    'status' => true,
                    'address' => 'Via Roma 123, Milano, Italy',
                    'password' => Hash::make('password123'),
                    'companies' => [$companies[0]->id, $companies[1]->id],
                    'primary_company' => $companies[0]->id
                ],
                [
                    'name' => 'Giulia',
                    'surname' => 'Bianchi',
                    'email' => 'giulia.bianchi@testmail.com',
                    'mobile' => '+39 333 987 6543',
                    'gender' => 'female',
                    'date_of_birth' => '1990-03-22',
                    'tax_id_code' => 'BNCGLI90C62F205Y',
                    'status' => true,
                    'address' => 'Corso Italia 456, Roma, Italy',
                    'password' => Hash::make('password123'),
                    'companies' => [$companies[1]->id, $companies[2]->id ?? $companies[0]->id],
                    'primary_company' => $companies[1]->id
                ],
                [
                    'name' => 'Alessandro',
                    'surname' => 'Verdi',
                    'email' => 'alessandro.verdi@testmail.com',
                    'mobile' => '+39 333 555 7890',
                    'gender' => 'male',
                    'date_of_birth' => '1982-11-08',
                    'tax_id_code' => 'VRDLSN82S08F205Z',
                    'status' => false,
                    'address' => 'Piazza Garibaldi 789, Napoli, Italy',
                    'password' => Hash::make('password123'),
                    'companies' => [$companies[0]->id],
                    'primary_company' => $companies[0]->id
                ]
            ];

            foreach ($users as $userData) {
                $existing = User::where('email', $userData['email'])->first();
                if (!$existing) {
                    // Extract company data before creating user
                    $userCompanies = $userData['companies'];
                    $primaryCompany = $userData['primary_company'];
                    unset($userData['companies'], $userData['primary_company']);

                    // Create user
                    $user = User::create($userData);

                    // Associate with companies
                    $companyData = [];
                    foreach ($userCompanies as $companyId) {
                        $companyData[$companyId] = [
                            'is_primary' => $companyId == $primaryCompany,
                            'joined_at' => now(),
                        ];
                    }
                    $user->companies()->attach($companyData);

                    $this->line("✓ Created: {$user->full_name} ({$user->email})");
                    $this->line("  - Associated with " . count($userCompanies) . " companies");
                    $this->line("  - Primary company: " . Company::find($primaryCompany)->name);
                } else {
                    $this->line("- Exists: {$existing->full_name} ({$existing->email})");
                }
            }

            $this->info('✅ SUCCESS: Test users created/verified!');
            
            // Test user relationships
            $this->info('');
            $this->info('Testing relationships...');
            
            $testUser = User::with('companies')->where('email', 'mario.rossi@testmail.com')->first();
            if ($testUser) {
                $this->info("User: {$testUser->full_name}");
                $this->info("Age: " . ($testUser->age ?: 'N/A') . " years old");
                $this->info("Companies: " . $testUser->companies->count());
                $primaryCompany = $testUser->companies->where('pivot.is_primary', true)->first();
                if ($primaryCompany) {
                    $this->info("Primary Company: " . $primaryCompany->name);
                }
            }

            // Test count
            $count = User::count();
            $this->info("Total users in database: {$count}");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ ERROR: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
