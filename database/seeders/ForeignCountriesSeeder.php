<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ForeignCountry;
use Illuminate\Support\Facades\DB;

class ForeignCountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = database_path('data/paesi_esteri.csv');

        if (!file_exists($csvFile)) {
            $this->command->error("CSV file not found: {$csvFile}");
            return;
        }

        $this->command->info('Importing foreign countries...');

        // Truncate table
        DB::table('foreign_countries')->truncate();

        $file = fopen($csvFile, 'r');
        $header = fgetcsv($file); // Skip header row

        $count = 0;
        $batch = [];

        while (($row = fgetcsv($file)) !== false) {
            $batch[] = [
                'nome_italiano' => $row[0],
                'nome_inglese' => $row[1],
                'codice_catastale' => $row[2],
                'codice_iso_alpha2' => $row[3] ?? null,
                'codice_iso_alpha3' => $row[4] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $count++;

            // Insert in batches of 100
            if (count($batch) >= 100) {
                DB::table('foreign_countries')->insert($batch);
                $batch = [];
            }
        }

        // Insert remaining records
        if (count($batch) > 0) {
            DB::table('foreign_countries')->insert($batch);
        }

        fclose($file);

        $this->command->info("Successfully imported {$count} foreign countries");
    }
}
