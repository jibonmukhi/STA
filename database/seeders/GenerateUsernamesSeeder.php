<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;

class GenerateUsernamesSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $this->command->info('Setting usernames from CF for users without username...');

        // Find all users without a username
        $usersWithoutUsername = User::whereNull('username')->orWhere('username', '')->get();

        if ($usersWithoutUsername->isEmpty()) {
            $this->command->info('No users found without username.');
            return;
        }

        $this->command->info("Found {$usersWithoutUsername->count()} users without username.");

        foreach ($usersWithoutUsername as $user) {
            // Use CF as username
            if (!empty($user->cf)) {
                $user->username = $user->cf;
                $user->save();
                $this->command->info("Set username to CF '{$user->cf}' for user: {$user->name} {$user->surname} (ID: {$user->id})");
            } else {
                $this->command->warn("User {$user->name} {$user->surname} (ID: {$user->id}) has no CF. Skipping.");
            }
        }

        $this->command->info('Username generation from CF completed!');
    }

    /**
     * Generate a base username from name and surname
     */
    private function generateUsername(string $name, ?string $surname): string
    {
        // Clean and prepare the name parts
        $name = Str::slug($name, '');
        $surname = $surname ? Str::slug($surname, '') : '';

        // Create base username: firstname.lastname format
        if ($surname) {
            $baseUsername = strtolower($name . '.' . $surname);
        } else {
            $baseUsername = strtolower($name);
        }

        // Remove any invalid characters (only allow letters, numbers, dots, hyphens, underscores)
        $baseUsername = preg_replace('/[^a-z0-9._-]/', '', $baseUsername);

        // Limit to 50 characters
        $baseUsername = substr($baseUsername, 0, 50);

        return $baseUsername;
    }

    /**
     * Ensure the username is unique by appending a number if necessary
     */
    private function ensureUniqueUsername(string $baseUsername): string
    {
        $username = $baseUsername;
        $counter = 1;

        // Check if username already exists
        while (User::where('username', $username)->exists()) {
            // Append a number to make it unique
            $username = $baseUsername . $counter;
            $counter++;

            // Make sure we don't exceed 50 characters
            if (strlen($username) > 50) {
                // Truncate base and add counter
                $maxBaseLength = 50 - strlen((string)$counter);
                $username = substr($baseUsername, 0, $maxBaseLength) . $counter;
            }
        }

        return $username;
    }
}
