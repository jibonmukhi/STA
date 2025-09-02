<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class AssignCompanyPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'companies:assign-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign company permissions to all existing users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Assigning company permissions to all users...');

        $permissions = [
            'view companies',
            'create companies',
            'edit companies',
            'delete companies',
        ];

        // Get all users
        $users = User::all();
        
        foreach ($users as $user) {
            foreach ($permissions as $permission) {
                if (!$user->hasPermissionTo($permission)) {
                    $user->givePermissionTo($permission);
                }
            }
            $this->line("✓ Assigned company permissions to: {$user->name} ({$user->email})");
        }

        $this->info("Successfully assigned company permissions to {$users->count()} users!");
        $this->info('You should now see the Companies menu in the sidebar.');
        
        return Command::SUCCESS;
    }
}
