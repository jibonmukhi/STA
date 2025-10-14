<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataVaultCategory;
use App\Models\DataVaultItem;

class DataVaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Data Vault...');

        // 1. Gender
        $this->createCategory('gender', 'Gender', 'Genere', [
            ['code' => 'male', 'label_en' => 'Male', 'label_it' => 'Maschio', 'icon' => 'fas fa-mars', 'color' => 'primary'],
            ['code' => 'female', 'label_en' => 'Female', 'label_it' => 'Femmina', 'icon' => 'fas fa-venus', 'color' => 'danger'],
            ['code' => 'other', 'label_en' => 'Other', 'label_it' => 'Altro', 'icon' => 'fas fa-genderless', 'color' => 'secondary'],
        ]);

        // 2. User Status
        $this->createCategory('user_status', 'User Status', 'Stato Utente', [
            ['code' => 'active', 'label_en' => 'Active', 'label_it' => 'Attivo', 'color' => 'success', 'is_default' => true],
            ['code' => 'inactive', 'label_en' => 'Inactive', 'label_it' => 'Inattivo', 'color' => 'secondary'],
            ['code' => 'parked', 'label_en' => 'Parked', 'label_it' => 'Parcheggiato', 'color' => 'warning'],
            ['code' => 'pending_approval', 'label_en' => 'Pending Approval', 'label_it' => 'In Attesa di Approvazione', 'color' => 'info'],
        ]);

        // 3. Course Category
        $this->createCategory('course_category', 'Course Category', 'Categoria Corso', [
            ['code' => 'programming', 'label_en' => 'Programming', 'label_it' => 'Programmazione'],
            ['code' => 'web_development', 'label_en' => 'Web Development', 'label_it' => 'Sviluppo Web'],
            ['code' => 'mobile_development', 'label_en' => 'Mobile Development', 'label_it' => 'Sviluppo Mobile'],
            ['code' => 'data_science', 'label_en' => 'Data Science', 'label_it' => 'Data Science'],
            ['code' => 'cybersecurity', 'label_en' => 'Cybersecurity', 'label_it' => 'Sicurezza Informatica'],
            ['code' => 'project_management', 'label_en' => 'Project Management', 'label_it' => 'Gestione Progetti'],
            ['code' => 'design', 'label_en' => 'Design', 'label_it' => 'Design'],
            ['code' => 'business', 'label_en' => 'Business', 'label_it' => 'Business'],
            ['code' => 'marketing', 'label_en' => 'Marketing', 'label_it' => 'Marketing'],
            ['code' => 'other', 'label_en' => 'Other', 'label_it' => 'Altro'],
        ]);

        // 4. Course Level
        $this->createCategory('course_level', 'Course Level', 'Livello Corso', [
            ['code' => 'beginner', 'label_en' => 'Beginner', 'label_it' => 'Principiante', 'color' => 'success', 'is_default' => true],
            ['code' => 'intermediate', 'label_en' => 'Intermediate', 'label_it' => 'Intermedio', 'color' => 'warning'],
            ['code' => 'advanced', 'label_en' => 'Advanced', 'label_it' => 'Avanzato', 'color' => 'danger'],
        ]);

        // 5. Delivery Method
        $this->createCategory('delivery_method', 'Delivery Method', 'Metodo di Erogazione', [
            ['code' => 'online', 'label_en' => 'Online', 'label_it' => 'Online', 'icon' => 'fas fa-laptop', 'color' => 'primary'],
            ['code' => 'offline', 'label_en' => 'Offline', 'label_it' => 'In Presenza', 'icon' => 'fas fa-users', 'color' => 'success'],
            ['code' => 'hybrid', 'label_en' => 'Hybrid', 'label_it' => 'Ibrido', 'icon' => 'fas fa-exchange-alt', 'color' => 'info'],
        ]);

        // 6. Certificate Type
        $this->createCategory('certificate_type', 'Certificate Type', 'Tipo Certificato', [
            ['code' => 'training', 'label_en' => 'Training', 'label_it' => 'Formazione', 'color' => 'primary'],
            ['code' => 'qualification', 'label_en' => 'Qualification', 'label_it' => 'Qualifica', 'color' => 'success'],
            ['code' => 'compliance', 'label_en' => 'Compliance', 'label_it' => 'Conformità', 'color' => 'warning'],
            ['code' => 'professional', 'label_en' => 'Professional', 'label_it' => 'Professionale', 'color' => 'info'],
            ['code' => 'academic', 'label_en' => 'Academic', 'label_it' => 'Accademico', 'color' => 'dark'],
        ]);

        // 7. Certificate Level
        $this->createCategory('certificate_level', 'Certificate Level', 'Livello Certificato', [
            ['code' => 'beginner', 'label_en' => 'Beginner', 'label_it' => 'Principiante', 'color' => 'success'],
            ['code' => 'intermediate', 'label_en' => 'Intermediate', 'label_it' => 'Intermedio', 'color' => 'primary'],
            ['code' => 'advanced', 'label_en' => 'Advanced', 'label_it' => 'Avanzato', 'color' => 'warning'],
            ['code' => 'professional', 'label_en' => 'Professional', 'label_it' => 'Professionale', 'color' => 'danger'],
            ['code' => 'expert', 'label_en' => 'Expert', 'label_it' => 'Esperto', 'color' => 'dark'],
        ]);

        // 8. Certificate Status
        $this->createCategory('certificate_status', 'Certificate Status', 'Stato Certificato', [
            ['code' => 'active', 'label_en' => 'Active', 'label_it' => 'Attivo', 'color' => 'success', 'is_default' => true],
            ['code' => 'expired', 'label_en' => 'Expired', 'label_it' => 'Scaduto', 'color' => 'danger'],
            ['code' => 'revoked', 'label_en' => 'Revoked', 'label_it' => 'Revocato', 'color' => 'dark'],
            ['code' => 'pending', 'label_en' => 'Pending', 'label_it' => 'In Attesa', 'color' => 'warning'],
            ['code' => 'suspended', 'label_en' => 'Suspended', 'label_it' => 'Sospeso', 'color' => 'secondary'],
        ]);

        // 9. Enrollment Status
        $this->createCategory('enrollment_status', 'Enrollment Status', 'Stato Iscrizione', [
            ['code' => 'enrolled', 'label_en' => 'Enrolled', 'label_it' => 'Iscritto', 'color' => 'info', 'is_default' => true],
            ['code' => 'in_progress', 'label_en' => 'In Progress', 'label_it' => 'In Corso', 'color' => 'primary'],
            ['code' => 'completed', 'label_en' => 'Completed', 'label_it' => 'Completato', 'color' => 'success'],
            ['code' => 'dropped', 'label_en' => 'Dropped', 'label_it' => 'Abbandonato', 'color' => 'warning'],
            ['code' => 'failed', 'label_en' => 'Failed', 'label_it' => 'Fallito', 'color' => 'danger'],
        ]);

        // 10. Language
        $this->createCategory('language', 'Language', 'Lingua', [
            ['code' => 'en', 'label_en' => 'English', 'label_it' => 'Inglese', 'icon' => 'flag-icon flag-icon-gb'],
            ['code' => 'it', 'label_en' => 'Italian', 'label_it' => 'Italiano', 'icon' => 'flag-icon flag-icon-it'],
        ]);

        // 11. Country
        $this->createCategory('country', 'Country', 'Paese', [
            ['code' => 'IT', 'label_en' => 'Italy', 'label_it' => 'Italia', 'icon' => 'flag-icon flag-icon-it'],
            ['code' => 'US', 'label_en' => 'United States', 'label_it' => 'Stati Uniti', 'icon' => 'flag-icon flag-icon-us'],
            ['code' => 'GB', 'label_en' => 'United Kingdom', 'label_it' => 'Regno Unito', 'icon' => 'flag-icon flag-icon-gb'],
            ['code' => 'FR', 'label_en' => 'France', 'label_it' => 'Francia', 'icon' => 'flag-icon flag-icon-fr'],
            ['code' => 'DE', 'label_en' => 'Germany', 'label_it' => 'Germania', 'icon' => 'flag-icon flag-icon-de'],
            ['code' => 'ES', 'label_en' => 'Spain', 'label_it' => 'Spagna', 'icon' => 'flag-icon flag-icon-es'],
        ]);

        $this->command->info('Data Vault seeded successfully!');
    }

    private function createCategory(string $code, string $nameEn, string $nameIt, array $items)
    {
        $category = DataVaultCategory::updateOrCreate(
            ['code' => $code],
            [
                'name_en' => $nameEn,
                'name_it' => $nameIt,
                'is_system' => true,
                'is_active' => true,
                'sort_order' => 0,
            ]
        );

        foreach ($items as $index => $item) {
            DataVaultItem::updateOrCreate(
                ['category_id' => $category->id, 'code' => $item['code']],
                [
                    'label_en' => $item['label_en'],
                    'label_it' => $item['label_it'],
                    'color' => $item['color'] ?? null,
                    'icon' => $item['icon'] ?? null,
                    'is_default' => $item['is_default'] ?? false,
                    'is_system' => true,
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }

        $this->command->info("  ✓ Created category: {$code} with " . count($items) . " items");
    }
}
