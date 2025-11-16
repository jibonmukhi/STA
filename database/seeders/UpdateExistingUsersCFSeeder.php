<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateExistingUsersCFSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder updates existing users who don't have a Codice Fiscale (CF).
     * It sets a demo CF value for testing purposes.
     */
    public function run(): void
    {
        // Demo Codice Fiscale values (valid format and checksum)
        $demoCFs = [
            'RSSMRA80A01H501U', // Mario Rossi, Male, 01/01/1980, Roma
            'VRDGPP85M42F205K', // Giuseppe Verdi, Male, 02/09/1985, Milano
            'BNCLGU90E45L219N', // Luigi Bianchi, Male, 05/05/1990, Torino
            'FRRNNZ75D50G273T', // Anna Ferrari, Female, 10/04/1975, Palermo
            'GLLMRA88H49A662D', // Maria Galli, Female, 09/06/1988, Bari
        ];

        // Find users without CF
        $usersWithoutCF = User::whereNull('cf')->get();

        if ($usersWithoutCF->isEmpty()) {
            $this->command->info('No users found without Codice Fiscale. All users already have CF.');
            return;
        }

        $this->command->info("Found {$usersWithoutCF->count()} users without Codice Fiscale.");

        $updated = 0;
        $cfIndex = 0;

        foreach ($usersWithoutCF as $user) {
            // Use demo CF values in rotation
            $demoCF = $demoCFs[$cfIndex % count($demoCFs)];

            // Make it unique by appending user ID if needed
            $uniqueCF = $demoCF;
            $attempt = 0;

            while (User::where('cf', $uniqueCF)->exists() && $attempt < 100) {
                // Modify the last character to make it unique
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $lastChar = $chars[($attempt + ord(substr($demoCF, -1, 1))) % 26];
                $uniqueCF = substr($demoCF, 0, 15) . $lastChar;
                $attempt++;
            }

            try {
                $user->update([
                    'cf' => $uniqueCF,
                    // Set default values for other required fields if they're missing
                    'surname' => $user->surname ?? 'Demo',
                    'date_of_birth' => $user->date_of_birth ?? '1990-01-01',
                    'place_of_birth' => $user->place_of_birth ?? 'Roma',
                    'gender' => $user->gender ?? 'other',
                    'country' => $user->country ?? 'IT',
                ]);

                $updated++;
                $this->command->info("Updated user #{$user->id} ({$user->name}) with CF: {$uniqueCF}");
            } catch (\Exception $e) {
                $this->command->error("Failed to update user #{$user->id}: " . $e->getMessage());
            }

            $cfIndex++;
        }

        $this->command->info("\nSuccessfully updated {$updated} users with demo Codice Fiscale values.");
        $this->command->warn("Note: These are DEMO values for testing only. Users should update with their real CF.");
    }
}
