<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Company;

class TestCompanyCreation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'companies:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test company creation functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Company creation...');

        try {
            // Create multiple test companies for pagination testing
            $companies = [
                [
                    'name' => 'Acme Corporation',
                    'email' => 'contact@acmecorp.com',
                    'phone' => '+1-555-0101',
                    'piva' => 'IT12345678901',
                    'website' => 'https://acmecorp.com',
                    'address' => '123 Main St, New York, NY 10001',
                    'active' => true
                ],
                [
                    'name' => 'Beta Solutions Ltd',
                    'email' => 'info@betasolutions.com',
                    'phone' => '+1-555-0102',
                    'piva' => 'IT12345678902',
                    'website' => 'https://betasolutions.com',
                    'address' => '456 Oak Ave, Los Angeles, CA 90210',
                    'active' => true
                ],
                [
                    'name' => 'Gamma Industries',
                    'email' => 'hello@gammaindustries.com',
                    'phone' => '+1-555-0103',
                    'piva' => 'IT12345678903',
                    'website' => 'https://gammaindustries.com',
                    'address' => '789 Pine St, Chicago, IL 60601',
                    'active' => false
                ],
                [
                    'name' => 'Delta Technologies',
                    'email' => 'support@deltatech.com',
                    'phone' => '+1-555-0104',
                    'piva' => 'IT12345678904',
                    'website' => 'https://deltatech.com',
                    'address' => '321 Elm St, Houston, TX 77001',
                    'active' => true
                ],
                [
                    'name' => 'Epsilon Consulting',
                    'email' => 'contact@epsiloncons.com',
                    'phone' => '+1-555-0105',
                    'piva' => 'IT12345678905',
                    'website' => 'https://epsiloncons.com',
                    'address' => '654 Maple Ave, Phoenix, AZ 85001',
                    'active' => true
                ]
            ];

            foreach ($companies as $companyData) {
                $existing = Company::where('email', $companyData['email'])->first();
                if (!$existing) {
                    Company::create($companyData);
                    $this->line("✓ Created: {$companyData['name']}");
                }
            }

            $company = $companies[0]; // For backward compatibility

            $this->info('✅ SUCCESS: Test companies created/verified!');
            
            // Test validation
            $this->info('');
            $this->info('Testing validation...');
            
            try {
                Company::create(['name' => '']); // Should fail
                $this->error('❌ VALIDATION FAILED: Empty name was accepted');
            } catch (\Exception $e) {
                $this->info('✅ VALIDATION OK: Empty name rejected');
            }

            // Test count
            $count = Company::count();
            $this->info("Total companies in database: {$count}");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ ERROR: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
