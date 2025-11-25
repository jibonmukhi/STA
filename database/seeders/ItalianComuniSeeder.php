<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ItalianComune;
use Illuminate\Support\Facades\DB;

class ItalianComuniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = database_path('data/comuni_italiani.csv');

        if (!file_exists($csvFile)) {
            $this->command->error("CSV file not found: {$csvFile}");
            return;
        }

        $this->command->info('Importing Italian comuni...');

        // Truncate table
        DB::table('italian_comuni')->truncate();

        $file = fopen($csvFile, 'r');
        $header = fgetcsv($file); // Skip header row

        $count = 0;
        $batch = [];

        while (($row = fgetcsv($file)) !== false) {
            $batch[] = [
                'nome' => $row[0],
                'regione' => $row[1],
                'provincia' => $row[2],
                'sigla_provincia' => $row[3],
                'codice_catastale' => $row[4],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $count++;

            // Insert in batches of 500
            if (count($batch) >= 500) {
                DB::table('italian_comuni')->insert($batch);
                $batch = [];
            }
        }

        // Insert remaining records
        if (count($batch) > 0) {
            DB::table('italian_comuni')->insert($batch);
        }

        fclose($file);

        $this->command->info("Successfully imported {$count} Italian comuni");
    }
}
