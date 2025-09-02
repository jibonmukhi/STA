<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TestRoleCreation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test role creation and permission assignment';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Role and Permission creation...');

        try {
            // Create additional permissions for testing
            $permissions = [
                'view dashboard',
                'view reports',
                'manage settings',
                'export data',
                'import data',
                'view analytics',
                'manage backups',
                'system administration',
                'user management',
                'content management'
            ];

            foreach ($permissions as $permissionName) {
                if (!Permission::where('name', $permissionName)->exists()) {
                    Permission::create(['name' => $permissionName]);
                    $this->line("✓ Created permission: {$permissionName}");
                }
            }

            // Create test roles with different permission sets
            $roles = [
                [
                    'name' => 'Super Admin',
                    'permissions' => $permissions // All permissions
                ],
                [
                    'name' => 'Manager',
                    'permissions' => [
                        'view dashboard',
                        'view reports', 
                        'view analytics',
                        'user management',
                        'content management'
                    ]
                ],
                [
                    'name' => 'Editor',
                    'permissions' => [
                        'view dashboard',
                        'content management',
                        'export data'
                    ]
                ],
                [
                    'name' => 'Viewer',
                    'permissions' => [
                        'view dashboard',
                        'view reports'
                    ]
                ],
                [
                    'name' => 'Data Analyst',
                    'permissions' => [
                        'view dashboard',
                        'view reports',
                        'view analytics',
                        'export data'
                    ]
                ],
                [
                    'name' => 'System Operator',
                    'permissions' => [
                        'view dashboard',
                        'manage settings',
                        'manage backups',
                        'system administration'
                    ]
                ],
                [
                    'name' => 'Content Creator',
                    'permissions' => [
                        'view dashboard',
                        'content management',
                        'export data',
                        'import data'
                    ]
                ]
            ];

            foreach ($roles as $roleData) {
                $existing = Role::where('name', $roleData['name'])->first();
                if (!$existing) {
                    $role = Role::create(['name' => $roleData['name']]);
                    $role->givePermissionTo($roleData['permissions']);
                    
                    $this->line("✓ Created role: {$role->name} with " . count($roleData['permissions']) . " permissions");
                } else {
                    $this->line("- Exists: {$existing->name}");
                }
            }

            $this->info('✅ SUCCESS: Test roles and permissions created/verified!');
            
            // Test role statistics
            $this->info('');
            $this->info('Testing role statistics...');
            
            $roleCount = Role::count();
            $permissionCount = Permission::count();
            
            $this->info("Total roles: {$roleCount}");
            $this->info("Total permissions: {$permissionCount}");
            
            // Test search functionality
            $this->info('');
            $this->info('Testing search functionality...');
            
            $adminRoles = Role::where('name', 'like', '%Admin%')->get();
            $this->info("Roles containing 'Admin': {$adminRoles->count()}");
            
            $rolesWithReports = Role::whereHas('permissions', function($q) {
                $q->where('name', 'like', '%report%');
            })->get();
            $this->info("Roles with 'report' permissions: {$rolesWithReports->count()}");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ ERROR: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
