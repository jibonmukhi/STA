<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class UpdateCoursesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Update Courses...');

        // Alimentaristi courses
        $alimentaristiCourses = [
            'Alimentaristi Celiachia',
            'Alimentaristi Base Liguria',
            'Alimentaristi HACCP Responsabile Liguria',
        ];

        foreach ($alimentaristiCourses as $index => $courseTitle) {
            $courseCode = 'ALI-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

            Course::updateOrCreate(
                ['course_code' => $courseCode],
                [
                    'title' => $courseTitle,
                    'description' => 'Corso di formazione per ' . strtolower($courseTitle),
                    'objectives' => 'Fornire le competenze necessarie per operare nel settore alimentare secondo la normativa vigente',
                    'category' => 'alimentaristi',
                    'level' => 'beginner',
                    'duration_hours' => 4,
                    'credits' => 0.00,
                    'price' => 100.00,
                    'instructor' => 'Da definire',
                    'teacher_id' => null,
                    'prerequisites' => 'Nessuno',
                    'delivery_method' => 'hybrid',
                    'max_participants' => 30,
                    'is_active' => true,
                    'is_mandatory' => false,
                    'status' => 'active',
                    'available_from' => now(),
                    'available_until' => now()->addYear(),
                    'start_date' => null,
                    'start_time' => null,
                    'end_date' => null,
                    'end_time' => null,
                ]
            );

            $this->command->info("  ✓ Created course: {$courseTitle}");
        }

        // Antincendio courses
        $antincendioCourses = [
            'Antincendio Rischio Alto',
            'Antincendio Rischio Basso',
            'Antincendio Rischio Medio',
        ];

        foreach ($antincendioCourses as $index => $courseTitle) {
            $courseCode = 'ANT-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

            Course::updateOrCreate(
                ['course_code' => $courseCode],
                [
                    'title' => $courseTitle,
                    'description' => 'Corso di formazione antincendio per ' . strtolower($courseTitle),
                    'objectives' => 'Fornire le competenze necessarie per la prevenzione incendi e gestione delle emergenze secondo il livello di rischio',
                    'category' => 'antincendio',
                    'level' => 'beginner',
                    'duration_hours' => strpos($courseTitle, 'Alto') !== false ? 16 : (strpos($courseTitle, 'Medio') !== false ? 8 : 4),
                    'credits' => 0.00,
                    'price' => strpos($courseTitle, 'Alto') !== false ? 250.00 : (strpos($courseTitle, 'Medio') !== false ? 180.00 : 120.00),
                    'instructor' => 'Da definire',
                    'teacher_id' => null,
                    'prerequisites' => 'Nessuno',
                    'delivery_method' => 'hybrid',
                    'max_participants' => 30,
                    'is_active' => true,
                    'is_mandatory' => false,
                    'status' => 'active',
                    'available_from' => now(),
                    'available_until' => now()->addYear(),
                    'start_date' => null,
                    'start_time' => null,
                    'end_date' => null,
                    'end_time' => null,
                ]
            );

            $this->command->info("  ✓ Created course: {$courseTitle}");
        }

        // Altri Corsi (All training courses)
        $courses = [
            // Base courses
            'Asili Nidi e Infanzia',
            'Base Uffici',
            'Base Uffici E-Learning',
            'BLSD',
            'Caduta dall\'Alto',
            'Carrelli a Braccio Telescopico',
            'Carrelli Elevatori',
            'Carrelli Telescopici Rotativi e Telescopici',
            'Coordinatori per la Sicurezza',
            'DPI',
            'Dirigenti',
            'Dirigenti E-Learning',
            'DPI III Categoria',
            'Escavatore',
            'Generale',
            'Generale E-Learning',
            'Magazziniere',
            'Magazziniere Rischio Alto',
            'Magazziniere Rischio Medio',
            'Preposti',
            'Primo Soccorso Rischio Alto',
            'Primo Soccorso Rischio Basso',
            'Responsabile Gestione Problema Amianto',
            'RLS Base',
            'RSPP Datore di Lavoro Rischio Basso',
            'RSPP Modulo C',
            'RSPP-ASPP Modulo A',
            'RSPP-ASPP Modulo B',
            'Segnaletica di Cantiere Stradale',
            'Spazi Confinati',
            'Specifica Autista Rischio Alto',
            'Specifica Autista Rischio Medio',
            'Specifica Collaboratori Scolastici',
            'Specifica Docenti',
            'Specifica Grotte',
            'Specifica Magazzinieri Autisti',
            'Specifica Mobile Worker',
            'Specifica Mobile Worker E-Learning',
            'Specifica Operai Rischio Alto',
            'Specifica Operai Rischio Medio',
            'Specifica Operai',
            'Specifica Operatori Sociali Non Residenziali',
            'Specifica Operatori Sociali Residenziali',
            'Specifica Polizia Locale',
            'Specifica Rischio Alto',
            'Specifica Rischio Basso',
            'Specifica Rischio Basso E-Learning',
            'Specifica Rischio Medio',
            'Specifica Varie e Cucina',
            'Specifica Vendita',
            'Piattaforme di lavoro elevabili (PLE) con stabilizzatori',
            'Piattaforme di lavoro elevabili (PLE) senza stabilizzatori',
            'PLE con e senza stabilizzatori (combinato)',
            'Gru su autocarro',
            'Gru a torre (rotazione in basso)',
            'Gru a torre (rotazione in alto)',
            'Gru a torre (rotazione in basso e in alto)',
            'Carrelli elevatori industriali semoventi',
            'Carrelli a braccio telescopico',
            'Carrelli telescopici rotativi',
            'Trattori agricoli a ruote o a cingoli',
            'Escavatori idraulici / movimento terra',
            'Movimento terra combinato (terne, autoribaltabili, etc)',
            'Pompe per calcestruzzo',
            'Macchina agricola raccoglifrutta (CRF)',
            'Carroponte (comando cabina o pensile)',
            // Update courses
            'Aggiornamento Antincendio LV 3',
            'Aggiornamento Antincendio LV2',
            'Aggiornamento Antincendio Lv1',
            'Aggiornamento ASPP',
            'Aggiornamento DAE',
            'Aggiornamento Carrelli elevatori',
            'Aggiornamento Collaboratori Scolastici',
            'Aggiornamento Coordinatori per la Sicurezza',
            'Aggiornamento Operatori alimentari',
            'Aggiornamento Dirigenti',
            'Aggiornamento Dirigenti E-Learning',
            'Aggiornamento Docenti',
            'Aggiornamento Figure Tecniche',
            'Aggiornamento Lavoro su funi',
            'Aggiornamento Gru su Autocarro',
            'Aggiornamento HACCP Liguria',
            'Aggiornamento Magazzinieri Autisti',
            'Aggiornamento Mobile Worker',
            'Aggiornamento Mobile Worker E-Learning',
            'Aggiornamento Movimento Terra',
            'Aggiornamento Nidi ed Infanzia',
            'Aggiornamento Operai',
            'Aggiornamento Operatori Bonifiche da Amianto',
            'Aggiornamento Operatori Sociali',
            'Aggiornamento PES PAV',
            'Aggiornamento PLE',
            'Aggiornamento PLE con Stabilizzatori',
            'Aggiornamento Polizia Locale',
            'Aggiornamento Ponteggi',
            'Aggiornamento Preposti',
            'Aggiornamento Primo Soccorso Rischio Alto',
            'Aggiornamento Primo Soccorso Rischio Bass',
            'Aggiornamento RLS 4 ore',
            'Aggiornamento RLS 8 ore',
            'Aggiornamento RSPP',
            'Aggiornamento RSPP Datore di Lavoro',
            'Aggiornamento Segnaletica Stradale',
            'Aggiornamento Spazi Confinati',
            'Aggiornamento Specifica',
            'Aggiornamento Specifica E-Learning',
            'Aggiornamento Macchine Movimento Terra',
            'Aggiornamento Trattori Agricoli',
            'Aggiornamento Uffici',
            'Aggiornamento Uffici E-Learning',
        ];

        foreach ($courses as $index => $courseTitle) {
            // Generate a unique course code based on course type
            $isUpdateCourse = strpos($courseTitle, 'Aggiornamento') === 0;
            $courseCode = ($isUpdateCourse ? 'AGG-' : 'CRS-') . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

            // Set course properties based on type
            if ($isUpdateCourse) {
                $description = 'Corso di aggiornamento per ' . strtolower($courseTitle);
                $objectives = 'Aggiornare le competenze e conoscenze richieste dalla normativa vigente';
                $level = 'intermediate';
                $prerequisites = 'Aver frequentato il corso base';
            } else {
                $description = 'Corso di formazione per ' . strtolower($courseTitle);
                $objectives = 'Fornire le competenze necessarie secondo la normativa vigente';
                $level = 'beginner';
                $prerequisites = 'Nessuno';
            }

            Course::updateOrCreate(
                ['course_code' => $courseCode],
                [
                    'title' => $courseTitle,
                    'description' => $description,
                    'objectives' => $objectives,
                    'category' => 'altri_corsi',
                    'level' => $level,
                    'duration_hours' => 8,
                    'credits' => 0.00,
                    'price' => 150.00,
                    'instructor' => 'Da definire',
                    'teacher_id' => null,
                    'prerequisites' => $prerequisites,
                    'delivery_method' => strpos($courseTitle, 'E-Learning') !== false ? 'online' : 'hybrid',
                    'max_participants' => 30,
                    'is_active' => true,
                    'is_mandatory' => false,
                    'status' => 'active',
                    'available_from' => now(),
                    'available_until' => now()->addYear(),
                    'start_date' => null,
                    'start_time' => null,
                    'end_date' => null,
                    'end_time' => null,
                ]
            );

            $this->command->info("  ✓ Created course: {$courseTitle}");
        }

        $this->command->info('All courses seeded successfully!');
    }
}
