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
            ['code' => 'alimentaristi', 'label_en' => 'Food Handlers', 'label_it' => 'Alimentaristi'],
            ['code' => 'antincendio', 'label_en' => 'Fire Safety', 'label_it' => 'Antincendio'],
            ['code' => 'altri_corsi', 'label_en' => 'Other Courses', 'label_it' => 'Altri Corsi'],
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

        // 9b. Course Status
        $this->createCategory('course_status', 'Course Status', 'Stato Corso', [
            ['code' => 'active', 'label_en' => 'Active', 'label_it' => 'Attivo', 'color' => 'success', 'icon' => 'fas fa-check-circle', 'is_default' => true],
            ['code' => 'inactive', 'label_en' => 'Inactive', 'label_it' => 'Inattivo', 'color' => 'secondary', 'icon' => 'fas fa-times-circle'],
            ['code' => 'ongoing', 'label_en' => 'Ongoing', 'label_it' => 'In Corso', 'color' => 'primary', 'icon' => 'fas fa-play-circle'],
            ['code' => 'done', 'label_en' => 'Done', 'label_it' => 'Completato', 'color' => 'info', 'icon' => 'fas fa-flag-checkered'],
        ]);

        // 10. Language
        $this->createCategory('language', 'Language', 'Lingua', [
            ['code' => 'en', 'label_en' => 'English', 'label_it' => 'Inglese', 'icon' => 'flag-icon flag-icon-gb'],
            ['code' => 'it', 'label_en' => 'Italian', 'label_it' => 'Italiano', 'icon' => 'flag-icon flag-icon-it'],
        ]);

        // 11. Country - All countries
        $this->createCategory('country', 'Country', 'Paese', $this->getAllCountries());

        // 12. Belfiore Codes - For CF calculation
        $this->createCategory('belfiore_code', 'Belfiore Code', 'Codice Belfiore', $this->getAllBelfioreCodes());

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
                    'is_system' => $item['is_system'] ?? false,
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }

        $this->command->info("  ✓ Created category: {$code} with " . count($items) . " items");
    }

    /**
     * Get all countries with ISO codes, sorted alphabetically
     *
     * @return array
     */
    private function getAllCountries(): array
    {
        $countries = [
            // Europe
            ['code' => 'AL', 'label_en' => 'Albania', 'label_it' => 'Albania', 'icon' => 'flag-icon flag-icon-al'],
            ['code' => 'AD', 'label_en' => 'Andorra', 'label_it' => 'Andorra', 'icon' => 'flag-icon flag-icon-ad'],
            ['code' => 'AT', 'label_en' => 'Austria', 'label_it' => 'Austria', 'icon' => 'flag-icon flag-icon-at'],
            ['code' => 'BY', 'label_en' => 'Belarus', 'label_it' => 'Bielorussia', 'icon' => 'flag-icon flag-icon-by'],
            ['code' => 'BE', 'label_en' => 'Belgium', 'label_it' => 'Belgio', 'icon' => 'flag-icon flag-icon-be'],
            ['code' => 'BA', 'label_en' => 'Bosnia and Herzegovina', 'label_it' => 'Bosnia ed Erzegovina', 'icon' => 'flag-icon flag-icon-ba'],
            ['code' => 'BG', 'label_en' => 'Bulgaria', 'label_it' => 'Bulgaria', 'icon' => 'flag-icon flag-icon-bg'],
            ['code' => 'HR', 'label_en' => 'Croatia', 'label_it' => 'Croazia', 'icon' => 'flag-icon flag-icon-hr'],
            ['code' => 'CY', 'label_en' => 'Cyprus', 'label_it' => 'Cipro', 'icon' => 'flag-icon flag-icon-cy'],
            ['code' => 'CZ', 'label_en' => 'Czech Republic', 'label_it' => 'Repubblica Ceca', 'icon' => 'flag-icon flag-icon-cz'],
            ['code' => 'DK', 'label_en' => 'Denmark', 'label_it' => 'Danimarca', 'icon' => 'flag-icon flag-icon-dk'],
            ['code' => 'EE', 'label_en' => 'Estonia', 'label_it' => 'Estonia', 'icon' => 'flag-icon flag-icon-ee'],
            ['code' => 'FI', 'label_en' => 'Finland', 'label_it' => 'Finlandia', 'icon' => 'flag-icon flag-icon-fi'],
            ['code' => 'FR', 'label_en' => 'France', 'label_it' => 'Francia', 'icon' => 'flag-icon flag-icon-fr'],
            ['code' => 'DE', 'label_en' => 'Germany', 'label_it' => 'Germania', 'icon' => 'flag-icon flag-icon-de'],
            ['code' => 'GR', 'label_en' => 'Greece', 'label_it' => 'Grecia', 'icon' => 'flag-icon flag-icon-gr'],
            ['code' => 'HU', 'label_en' => 'Hungary', 'label_it' => 'Ungheria', 'icon' => 'flag-icon flag-icon-hu'],
            ['code' => 'IS', 'label_en' => 'Iceland', 'label_it' => 'Islanda', 'icon' => 'flag-icon flag-icon-is'],
            ['code' => 'IE', 'label_en' => 'Ireland', 'label_it' => 'Irlanda', 'icon' => 'flag-icon flag-icon-ie'],
            ['code' => 'XK', 'label_en' => 'Kosovo', 'label_it' => 'Kosovo', 'icon' => 'flag-icon flag-icon-xk'],
            ['code' => 'LV', 'label_en' => 'Latvia', 'label_it' => 'Lettonia', 'icon' => 'flag-icon flag-icon-lv'],
            ['code' => 'LI', 'label_en' => 'Liechtenstein', 'label_it' => 'Liechtenstein', 'icon' => 'flag-icon flag-icon-li'],
            ['code' => 'LT', 'label_en' => 'Lithuania', 'label_it' => 'Lituania', 'icon' => 'flag-icon flag-icon-lt'],
            ['code' => 'LU', 'label_en' => 'Luxembourg', 'label_it' => 'Lussemburgo', 'icon' => 'flag-icon flag-icon-lu'],
            ['code' => 'MT', 'label_en' => 'Malta', 'label_it' => 'Malta', 'icon' => 'flag-icon flag-icon-mt'],
            ['code' => 'MD', 'label_en' => 'Moldova', 'label_it' => 'Moldova', 'icon' => 'flag-icon flag-icon-md'],
            ['code' => 'MC', 'label_en' => 'Monaco', 'label_it' => 'Monaco', 'icon' => 'flag-icon flag-icon-mc'],
            ['code' => 'ME', 'label_en' => 'Montenegro', 'label_it' => 'Montenegro', 'icon' => 'flag-icon flag-icon-me'],
            ['code' => 'NL', 'label_en' => 'Netherlands', 'label_it' => 'Paesi Bassi', 'icon' => 'flag-icon flag-icon-nl'],
            ['code' => 'MK', 'label_en' => 'North Macedonia', 'label_it' => 'Macedonia del Nord', 'icon' => 'flag-icon flag-icon-mk'],
            ['code' => 'NO', 'label_en' => 'Norway', 'label_it' => 'Norvegia', 'icon' => 'flag-icon flag-icon-no'],
            ['code' => 'PL', 'label_en' => 'Poland', 'label_it' => 'Polonia', 'icon' => 'flag-icon flag-icon-pl'],
            ['code' => 'PT', 'label_en' => 'Portugal', 'label_it' => 'Portogallo', 'icon' => 'flag-icon flag-icon-pt'],
            ['code' => 'RO', 'label_en' => 'Romania', 'label_it' => 'Romania', 'icon' => 'flag-icon flag-icon-ro'],
            ['code' => 'RU', 'label_en' => 'Russia', 'label_it' => 'Russia', 'icon' => 'flag-icon flag-icon-ru'],
            ['code' => 'SM', 'label_en' => 'San Marino', 'label_it' => 'San Marino', 'icon' => 'flag-icon flag-icon-sm'],
            ['code' => 'RS', 'label_en' => 'Serbia', 'label_it' => 'Serbia', 'icon' => 'flag-icon flag-icon-rs'],
            ['code' => 'SK', 'label_en' => 'Slovakia', 'label_it' => 'Slovacchia', 'icon' => 'flag-icon flag-icon-sk'],
            ['code' => 'SI', 'label_en' => 'Slovenia', 'label_it' => 'Slovenia', 'icon' => 'flag-icon flag-icon-si'],
            ['code' => 'ES', 'label_en' => 'Spain', 'label_it' => 'Spagna', 'icon' => 'flag-icon flag-icon-es'],
            ['code' => 'SE', 'label_en' => 'Sweden', 'label_it' => 'Svezia', 'icon' => 'flag-icon flag-icon-se'],
            ['code' => 'CH', 'label_en' => 'Switzerland', 'label_it' => 'Svizzera', 'icon' => 'flag-icon flag-icon-ch'],
            ['code' => 'UA', 'label_en' => 'Ukraine', 'label_it' => 'Ucraina', 'icon' => 'flag-icon flag-icon-ua'],
            ['code' => 'GB', 'label_en' => 'United Kingdom', 'label_it' => 'Regno Unito', 'icon' => 'flag-icon flag-icon-gb'],
            ['code' => 'VA', 'label_en' => 'Vatican City', 'label_it' => 'Città del Vaticano', 'icon' => 'flag-icon flag-icon-va'],

            // Americas - North America
            ['code' => 'CA', 'label_en' => 'Canada', 'label_it' => 'Canada', 'icon' => 'flag-icon flag-icon-ca'],
            ['code' => 'MX', 'label_en' => 'Mexico', 'label_it' => 'Messico', 'icon' => 'flag-icon flag-icon-mx'],
            ['code' => 'US', 'label_en' => 'United States', 'label_it' => 'Stati Uniti', 'icon' => 'flag-icon flag-icon-us'],

            // Americas - Central America & Caribbean
            ['code' => 'BZ', 'label_en' => 'Belize', 'label_it' => 'Belize', 'icon' => 'flag-icon flag-icon-bz'],
            ['code' => 'CR', 'label_en' => 'Costa Rica', 'label_it' => 'Costa Rica', 'icon' => 'flag-icon flag-icon-cr'],
            ['code' => 'CU', 'label_en' => 'Cuba', 'label_it' => 'Cuba', 'icon' => 'flag-icon flag-icon-cu'],
            ['code' => 'DO', 'label_en' => 'Dominican Republic', 'label_it' => 'Repubblica Dominicana', 'icon' => 'flag-icon flag-icon-do'],
            ['code' => 'SV', 'label_en' => 'El Salvador', 'label_it' => 'El Salvador', 'icon' => 'flag-icon flag-icon-sv'],
            ['code' => 'GT', 'label_en' => 'Guatemala', 'label_it' => 'Guatemala', 'icon' => 'flag-icon flag-icon-gt'],
            ['code' => 'HT', 'label_en' => 'Haiti', 'label_it' => 'Haiti', 'icon' => 'flag-icon flag-icon-ht'],
            ['code' => 'HN', 'label_en' => 'Honduras', 'label_it' => 'Honduras', 'icon' => 'flag-icon flag-icon-hn'],
            ['code' => 'JM', 'label_en' => 'Jamaica', 'label_it' => 'Giamaica', 'icon' => 'flag-icon flag-icon-jm'],
            ['code' => 'NI', 'label_en' => 'Nicaragua', 'label_it' => 'Nicaragua', 'icon' => 'flag-icon flag-icon-ni'],
            ['code' => 'PA', 'label_en' => 'Panama', 'label_it' => 'Panama', 'icon' => 'flag-icon flag-icon-pa'],
            ['code' => 'TT', 'label_en' => 'Trinidad and Tobago', 'label_it' => 'Trinidad e Tobago', 'icon' => 'flag-icon flag-icon-tt'],

            // Americas - South America
            ['code' => 'AR', 'label_en' => 'Argentina', 'label_it' => 'Argentina', 'icon' => 'flag-icon flag-icon-ar'],
            ['code' => 'BO', 'label_en' => 'Bolivia', 'label_it' => 'Bolivia', 'icon' => 'flag-icon flag-icon-bo'],
            ['code' => 'BR', 'label_en' => 'Brazil', 'label_it' => 'Brasile', 'icon' => 'flag-icon flag-icon-br'],
            ['code' => 'CL', 'label_en' => 'Chile', 'label_it' => 'Cile', 'icon' => 'flag-icon flag-icon-cl'],
            ['code' => 'CO', 'label_en' => 'Colombia', 'label_it' => 'Colombia', 'icon' => 'flag-icon flag-icon-co'],
            ['code' => 'EC', 'label_en' => 'Ecuador', 'label_it' => 'Ecuador', 'icon' => 'flag-icon flag-icon-ec'],
            ['code' => 'GY', 'label_en' => 'Guyana', 'label_it' => 'Guyana', 'icon' => 'flag-icon flag-icon-gy'],
            ['code' => 'PY', 'label_en' => 'Paraguay', 'label_it' => 'Paraguay', 'icon' => 'flag-icon flag-icon-py'],
            ['code' => 'PE', 'label_en' => 'Peru', 'label_it' => 'Perù', 'icon' => 'flag-icon flag-icon-pe'],
            ['code' => 'SR', 'label_en' => 'Suriname', 'label_it' => 'Suriname', 'icon' => 'flag-icon flag-icon-sr'],
            ['code' => 'UY', 'label_en' => 'Uruguay', 'label_it' => 'Uruguay', 'icon' => 'flag-icon flag-icon-uy'],
            ['code' => 'VE', 'label_en' => 'Venezuela', 'label_it' => 'Venezuela', 'icon' => 'flag-icon flag-icon-ve'],

            // Asia - East Asia
            ['code' => 'CN', 'label_en' => 'China', 'label_it' => 'Cina', 'icon' => 'flag-icon flag-icon-cn'],
            ['code' => 'HK', 'label_en' => 'Hong Kong', 'label_it' => 'Hong Kong', 'icon' => 'flag-icon flag-icon-hk'],
            ['code' => 'JP', 'label_en' => 'Japan', 'label_it' => 'Giappone', 'icon' => 'flag-icon flag-icon-jp'],
            ['code' => 'KP', 'label_en' => 'North Korea', 'label_it' => 'Corea del Nord', 'icon' => 'flag-icon flag-icon-kp'],
            ['code' => 'KR', 'label_en' => 'South Korea', 'label_it' => 'Corea del Sud', 'icon' => 'flag-icon flag-icon-kr'],
            ['code' => 'MN', 'label_en' => 'Mongolia', 'label_it' => 'Mongolia', 'icon' => 'flag-icon flag-icon-mn'],
            ['code' => 'TW', 'label_en' => 'Taiwan', 'label_it' => 'Taiwan', 'icon' => 'flag-icon flag-icon-tw'],

            // Asia - Southeast Asia
            ['code' => 'BN', 'label_en' => 'Brunei', 'label_it' => 'Brunei', 'icon' => 'flag-icon flag-icon-bn'],
            ['code' => 'KH', 'label_en' => 'Cambodia', 'label_it' => 'Cambogia', 'icon' => 'flag-icon flag-icon-kh'],
            ['code' => 'ID', 'label_en' => 'Indonesia', 'label_it' => 'Indonesia', 'icon' => 'flag-icon flag-icon-id'],
            ['code' => 'LA', 'label_en' => 'Laos', 'label_it' => 'Laos', 'icon' => 'flag-icon flag-icon-la'],
            ['code' => 'MY', 'label_en' => 'Malaysia', 'label_it' => 'Malesia', 'icon' => 'flag-icon flag-icon-my'],
            ['code' => 'MM', 'label_en' => 'Myanmar', 'label_it' => 'Myanmar', 'icon' => 'flag-icon flag-icon-mm'],
            ['code' => 'PH', 'label_en' => 'Philippines', 'label_it' => 'Filippine', 'icon' => 'flag-icon flag-icon-ph'],
            ['code' => 'SG', 'label_en' => 'Singapore', 'label_it' => 'Singapore', 'icon' => 'flag-icon flag-icon-sg'],
            ['code' => 'TH', 'label_en' => 'Thailand', 'label_it' => 'Thailandia', 'icon' => 'flag-icon flag-icon-th'],
            ['code' => 'TL', 'label_en' => 'Timor-Leste', 'label_it' => 'Timor Est', 'icon' => 'flag-icon flag-icon-tl'],
            ['code' => 'VN', 'label_en' => 'Vietnam', 'label_it' => 'Vietnam', 'icon' => 'flag-icon flag-icon-vn'],

            // Asia - South Asia
            ['code' => 'AF', 'label_en' => 'Afghanistan', 'label_it' => 'Afghanistan', 'icon' => 'flag-icon flag-icon-af'],
            ['code' => 'BD', 'label_en' => 'Bangladesh', 'label_it' => 'Bangladesh', 'icon' => 'flag-icon flag-icon-bd'],
            ['code' => 'BT', 'label_en' => 'Bhutan', 'label_it' => 'Bhutan', 'icon' => 'flag-icon flag-icon-bt'],
            ['code' => 'IN', 'label_en' => 'India', 'label_it' => 'India', 'icon' => 'flag-icon flag-icon-in'],
            ['code' => 'MV', 'label_en' => 'Maldives', 'label_it' => 'Maldive', 'icon' => 'flag-icon flag-icon-mv'],
            ['code' => 'NP', 'label_en' => 'Nepal', 'label_it' => 'Nepal', 'icon' => 'flag-icon flag-icon-np'],
            ['code' => 'PK', 'label_en' => 'Pakistan', 'label_it' => 'Pakistan', 'icon' => 'flag-icon flag-icon-pk'],
            ['code' => 'LK', 'label_en' => 'Sri Lanka', 'label_it' => 'Sri Lanka', 'icon' => 'flag-icon flag-icon-lk'],

            // Asia - Central Asia
            ['code' => 'KZ', 'label_en' => 'Kazakhstan', 'label_it' => 'Kazakistan', 'icon' => 'flag-icon flag-icon-kz'],
            ['code' => 'KG', 'label_en' => 'Kyrgyzstan', 'label_it' => 'Kirghizistan', 'icon' => 'flag-icon flag-icon-kg'],
            ['code' => 'TJ', 'label_en' => 'Tajikistan', 'label_it' => 'Tagikistan', 'icon' => 'flag-icon flag-icon-tj'],
            ['code' => 'TM', 'label_en' => 'Turkmenistan', 'label_it' => 'Turkmenistan', 'icon' => 'flag-icon flag-icon-tm'],
            ['code' => 'UZ', 'label_en' => 'Uzbekistan', 'label_it' => 'Uzbekistan', 'icon' => 'flag-icon flag-icon-uz'],

            // Africa - North Africa
            ['code' => 'DZ', 'label_en' => 'Algeria', 'label_it' => 'Algeria', 'icon' => 'flag-icon flag-icon-dz'],
            ['code' => 'EG', 'label_en' => 'Egypt', 'label_it' => 'Egitto', 'icon' => 'flag-icon flag-icon-eg'],
            ['code' => 'LY', 'label_en' => 'Libya', 'label_it' => 'Libia', 'icon' => 'flag-icon flag-icon-ly'],
            ['code' => 'MA', 'label_en' => 'Morocco', 'label_it' => 'Marocco', 'icon' => 'flag-icon flag-icon-ma'],
            ['code' => 'SD', 'label_en' => 'Sudan', 'label_it' => 'Sudan', 'icon' => 'flag-icon flag-icon-sd'],
            ['code' => 'TN', 'label_en' => 'Tunisia', 'label_it' => 'Tunisia', 'icon' => 'flag-icon flag-icon-tn'],

            // Africa - West Africa
            ['code' => 'BJ', 'label_en' => 'Benin', 'label_it' => 'Benin', 'icon' => 'flag-icon flag-icon-bj'],
            ['code' => 'BF', 'label_en' => 'Burkina Faso', 'label_it' => 'Burkina Faso', 'icon' => 'flag-icon flag-icon-bf'],
            ['code' => 'CI', 'label_en' => 'Ivory Coast', 'label_it' => 'Costa d\'Avorio', 'icon' => 'flag-icon flag-icon-ci'],
            ['code' => 'GM', 'label_en' => 'Gambia', 'label_it' => 'Gambia', 'icon' => 'flag-icon flag-icon-gm'],
            ['code' => 'GH', 'label_en' => 'Ghana', 'label_it' => 'Ghana', 'icon' => 'flag-icon flag-icon-gh'],
            ['code' => 'GN', 'label_en' => 'Guinea', 'label_it' => 'Guinea', 'icon' => 'flag-icon flag-icon-gn'],
            ['code' => 'GW', 'label_en' => 'Guinea-Bissau', 'label_it' => 'Guinea-Bissau', 'icon' => 'flag-icon flag-icon-gw'],
            ['code' => 'LR', 'label_en' => 'Liberia', 'label_it' => 'Liberia', 'icon' => 'flag-icon flag-icon-lr'],
            ['code' => 'ML', 'label_en' => 'Mali', 'label_it' => 'Mali', 'icon' => 'flag-icon flag-icon-ml'],
            ['code' => 'MR', 'label_en' => 'Mauritania', 'label_it' => 'Mauritania', 'icon' => 'flag-icon flag-icon-mr'],
            ['code' => 'NE', 'label_en' => 'Niger', 'label_it' => 'Niger', 'icon' => 'flag-icon flag-icon-ne'],
            ['code' => 'NG', 'label_en' => 'Nigeria', 'label_it' => 'Nigeria', 'icon' => 'flag-icon flag-icon-ng'],
            ['code' => 'SN', 'label_en' => 'Senegal', 'label_it' => 'Senegal', 'icon' => 'flag-icon flag-icon-sn'],
            ['code' => 'SL', 'label_en' => 'Sierra Leone', 'label_it' => 'Sierra Leone', 'icon' => 'flag-icon flag-icon-sl'],
            ['code' => 'TG', 'label_en' => 'Togo', 'label_it' => 'Togo', 'icon' => 'flag-icon flag-icon-tg'],

            // Africa - East Africa
            ['code' => 'ET', 'label_en' => 'Ethiopia', 'label_it' => 'Etiopia', 'icon' => 'flag-icon flag-icon-et'],
            ['code' => 'KE', 'label_en' => 'Kenya', 'label_it' => 'Kenya', 'icon' => 'flag-icon flag-icon-ke'],
            ['code' => 'MG', 'label_en' => 'Madagascar', 'label_it' => 'Madagascar', 'icon' => 'flag-icon flag-icon-mg'],
            ['code' => 'MW', 'label_en' => 'Malawi', 'label_it' => 'Malawi', 'icon' => 'flag-icon flag-icon-mw'],
            ['code' => 'MU', 'label_en' => 'Mauritius', 'label_it' => 'Mauritius', 'icon' => 'flag-icon flag-icon-mu'],
            ['code' => 'MZ', 'label_en' => 'Mozambique', 'label_it' => 'Mozambico', 'icon' => 'flag-icon flag-icon-mz'],
            ['code' => 'RW', 'label_en' => 'Rwanda', 'label_it' => 'Ruanda', 'icon' => 'flag-icon flag-icon-rw'],
            ['code' => 'SO', 'label_en' => 'Somalia', 'label_it' => 'Somalia', 'icon' => 'flag-icon flag-icon-so'],
            ['code' => 'TZ', 'label_en' => 'Tanzania', 'label_it' => 'Tanzania', 'icon' => 'flag-icon flag-icon-tz'],
            ['code' => 'UG', 'label_en' => 'Uganda', 'label_it' => 'Uganda', 'icon' => 'flag-icon flag-icon-ug'],
            ['code' => 'ZM', 'label_en' => 'Zambia', 'label_it' => 'Zambia', 'icon' => 'flag-icon flag-icon-zm'],
            ['code' => 'ZW', 'label_en' => 'Zimbabwe', 'label_it' => 'Zimbabwe', 'icon' => 'flag-icon flag-icon-zw'],

            // Africa - Central & Southern Africa
            ['code' => 'AO', 'label_en' => 'Angola', 'label_it' => 'Angola', 'icon' => 'flag-icon flag-icon-ao'],
            ['code' => 'BW', 'label_en' => 'Botswana', 'label_it' => 'Botswana', 'icon' => 'flag-icon flag-icon-bw'],
            ['code' => 'CM', 'label_en' => 'Cameroon', 'label_it' => 'Camerun', 'icon' => 'flag-icon flag-icon-cm'],
            ['code' => 'CF', 'label_en' => 'Central African Republic', 'label_it' => 'Repubblica Centrafricana', 'icon' => 'flag-icon flag-icon-cf'],
            ['code' => 'TD', 'label_en' => 'Chad', 'label_it' => 'Ciad', 'icon' => 'flag-icon flag-icon-td'],
            ['code' => 'CG', 'label_en' => 'Congo', 'label_it' => 'Congo', 'icon' => 'flag-icon flag-icon-cg'],
            ['code' => 'CD', 'label_en' => 'Democratic Republic of the Congo', 'label_it' => 'Rep. Democratica del Congo', 'icon' => 'flag-icon flag-icon-cd'],
            ['code' => 'GA', 'label_en' => 'Gabon', 'label_it' => 'Gabon', 'icon' => 'flag-icon flag-icon-ga'],
            ['code' => 'NA', 'label_en' => 'Namibia', 'label_it' => 'Namibia', 'icon' => 'flag-icon flag-icon-na'],
            ['code' => 'ZA', 'label_en' => 'South Africa', 'label_it' => 'Sudafrica', 'icon' => 'flag-icon flag-icon-za'],

            // Oceania
            ['code' => 'AU', 'label_en' => 'Australia', 'label_it' => 'Australia', 'icon' => 'flag-icon flag-icon-au'],
            ['code' => 'FJ', 'label_en' => 'Fiji', 'label_it' => 'Figi', 'icon' => 'flag-icon flag-icon-fj'],
            ['code' => 'NZ', 'label_en' => 'New Zealand', 'label_it' => 'Nuova Zelanda', 'icon' => 'flag-icon flag-icon-nz'],
            ['code' => 'PG', 'label_en' => 'Papua New Guinea', 'label_it' => 'Papua Nuova Guinea', 'icon' => 'flag-icon flag-icon-pg'],

            // Middle East
            ['code' => 'BH', 'label_en' => 'Bahrain', 'label_it' => 'Bahrein', 'icon' => 'flag-icon flag-icon-bh'],
            ['code' => 'IR', 'label_en' => 'Iran', 'label_it' => 'Iran', 'icon' => 'flag-icon flag-icon-ir'],
            ['code' => 'IQ', 'label_en' => 'Iraq', 'label_it' => 'Iraq', 'icon' => 'flag-icon flag-icon-iq'],
            ['code' => 'IL', 'label_en' => 'Israel', 'label_it' => 'Israele', 'icon' => 'flag-icon flag-icon-il'],
            ['code' => 'JO', 'label_en' => 'Jordan', 'label_it' => 'Giordania', 'icon' => 'flag-icon flag-icon-jo'],
            ['code' => 'KW', 'label_en' => 'Kuwait', 'label_it' => 'Kuwait', 'icon' => 'flag-icon flag-icon-kw'],
            ['code' => 'LB', 'label_en' => 'Lebanon', 'label_it' => 'Libano', 'icon' => 'flag-icon flag-icon-lb'],
            ['code' => 'OM', 'label_en' => 'Oman', 'label_it' => 'Oman', 'icon' => 'flag-icon flag-icon-om'],
            ['code' => 'PS', 'label_en' => 'Palestine', 'label_it' => 'Palestina', 'icon' => 'flag-icon flag-icon-ps'],
            ['code' => 'QA', 'label_en' => 'Qatar', 'label_it' => 'Qatar', 'icon' => 'flag-icon flag-icon-qa'],
            ['code' => 'SA', 'label_en' => 'Saudi Arabia', 'label_it' => 'Arabia Saudita', 'icon' => 'flag-icon flag-icon-sa'],
            ['code' => 'SY', 'label_en' => 'Syria', 'label_it' => 'Siria', 'icon' => 'flag-icon flag-icon-sy'],
            ['code' => 'TR', 'label_en' => 'Turkey', 'label_it' => 'Turchia', 'icon' => 'flag-icon flag-icon-tr'],
            ['code' => 'AE', 'label_en' => 'United Arab Emirates', 'label_it' => 'Emirati Arabi Uniti', 'icon' => 'flag-icon flag-icon-ae'],
            ['code' => 'YE', 'label_en' => 'Yemen', 'label_it' => 'Yemen', 'icon' => 'flag-icon flag-icon-ye'],

            // Add Italy
            ['code' => 'IT', 'label_en' => 'Italy', 'label_it' => 'Italia', 'icon' => 'flag-icon flag-icon-it'],
        ];

        // Sort countries alphabetically by English label
        usort($countries, function($a, $b) {
            return strcmp($a['label_en'], $b['label_en']);
        });

        // Move Italy to the top and mark as default
        $italyIndex = array_search('IT', array_column($countries, 'code'));
        if ($italyIndex !== false) {
            $italy = $countries[$italyIndex];
            $italy['is_default'] = true;
            array_splice($countries, $italyIndex, 1);
            array_unshift($countries, $italy);
        }

        return $countries;
    }

    /**
     * Get all Belfiore codes for Italian cities and foreign countries
     */
    private function getAllBelfioreCodes(): array
    {
        $codes = [];

        // MAJOR ITALIAN CITIES (most common)
        // Using code as unique identifier: BELFIORE_CODE|COUNTRY_CODE
        $italianCities = [
            // Major cities
            ['code' => 'H501|IT', 'label_en' => 'Rome', 'label_it' => 'Roma', 'metadata' => json_encode(['belfiore' => 'H501', 'country' => 'IT'])],
            ['code' => 'F205|IT', 'label_en' => 'Milan', 'label_it' => 'Milano', 'metadata' => json_encode(['belfiore' => 'F205', 'country' => 'IT'])],
            ['code' => 'F839|IT', 'label_en' => 'Naples', 'label_it' => 'Napoli', 'metadata' => json_encode(['belfiore' => 'F839', 'country' => 'IT'])],
            ['code' => 'L219|IT', 'label_en' => 'Turin', 'label_it' => 'Torino', 'metadata' => json_encode(['belfiore' => 'L219', 'country' => 'IT'])],
            ['code' => 'G273|IT', 'label_en' => 'Palermo', 'label_it' => 'Palermo', 'metadata' => json_encode(['belfiore' => 'G273', 'country' => 'IT'])],
            ['code' => 'D969|IT', 'label_en' => 'Genoa', 'label_it' => 'Genova', 'metadata' => json_encode(['belfiore' => 'D969', 'country' => 'IT'])],
            ['code' => 'A944|IT', 'label_en' => 'Bologna', 'label_it' => 'Bologna', 'metadata' => json_encode(['belfiore' => 'A944', 'country' => 'IT'])],
            ['code' => 'D612|IT', 'label_en' => 'Florence', 'label_it' => 'Firenze', 'metadata' => json_encode(['belfiore' => 'D612', 'country' => 'IT'])],
            ['code' => 'A662|IT', 'label_en' => 'Bari', 'label_it' => 'Bari', 'metadata' => json_encode(['belfiore' => 'A662', 'country' => 'IT'])],
            ['code' => 'C351|IT', 'label_en' => 'Catania', 'label_it' => 'Catania', 'metadata' => json_encode(['belfiore' => 'C351', 'country' => 'IT'])],
            ['code' => 'L736|IT', 'label_en' => 'Venice', 'label_it' => 'Venezia', 'metadata' => json_encode(['belfiore' => 'L736', 'country' => 'IT'])],
            ['code' => 'L781|IT', 'label_en' => 'Verona', 'label_it' => 'Verona', 'metadata' => json_encode(['belfiore' => 'L781', 'country' => 'IT'])],
            ['code' => 'F158|IT', 'label_en' => 'Messina', 'label_it' => 'Messina', 'metadata' => json_encode(['belfiore' => 'F158', 'country' => 'IT'])],
            ['code' => 'G224|IT', 'label_en' => 'Padua', 'label_it' => 'Padova', 'metadata' => json_encode(['belfiore' => 'G224', 'country' => 'IT'])],
            ['code' => 'L424|IT', 'label_en' => 'Trieste', 'label_it' => 'Trieste', 'metadata' => json_encode(['belfiore' => 'L424', 'country' => 'IT'])],
            ['code' => 'B157|IT', 'label_en' => 'Brescia', 'label_it' => 'Brescia', 'metadata' => json_encode(['belfiore' => 'B157', 'country' => 'IT'])],
            ['code' => 'G337|IT', 'label_en' => 'Parma', 'label_it' => 'Parma', 'metadata' => json_encode(['belfiore' => 'G337', 'country' => 'IT'])],
            ['code' => 'G999|IT', 'label_en' => 'Prato', 'label_it' => 'Prato', 'metadata' => json_encode(['belfiore' => 'G999', 'country' => 'IT'])],
            ['code' => 'L049|IT', 'label_en' => 'Taranto', 'label_it' => 'Taranto', 'metadata' => json_encode(['belfiore' => 'L049', 'country' => 'IT'])],
            ['code' => 'F257|IT', 'label_en' => 'Modena', 'label_it' => 'Modena', 'metadata' => json_encode(['belfiore' => 'F257', 'country' => 'IT'])],
            ['code' => 'H224|IT', 'label_en' => 'Reggio Calabria', 'label_it' => 'Reggio Calabria', 'metadata' => json_encode(['belfiore' => 'H224', 'country' => 'IT'])],
            ['code' => 'H223|IT', 'label_en' => 'Reggio Emilia', 'label_it' => 'Reggio Emilia', 'metadata' => json_encode(['belfiore' => 'H223', 'country' => 'IT'])],
            ['code' => 'G478|IT', 'label_en' => 'Perugia', 'label_it' => 'Perugia', 'metadata' => json_encode(['belfiore' => 'G478', 'country' => 'IT'])],
            ['code' => 'E625|IT', 'label_en' => 'Livorno', 'label_it' => 'Livorno', 'metadata' => json_encode(['belfiore' => 'E625', 'country' => 'IT'])],
            ['code' => 'B354|IT', 'label_en' => 'Cagliari', 'label_it' => 'Cagliari', 'metadata' => json_encode(['belfiore' => 'B354', 'country' => 'IT'])],
            ['code' => 'D643|IT', 'label_en' => 'Foggia', 'label_it' => 'Foggia', 'metadata' => json_encode(['belfiore' => 'D643', 'country' => 'IT'])],
            ['code' => 'H294|IT', 'label_en' => 'Rimini', 'label_it' => 'Rimini', 'metadata' => json_encode(['belfiore' => 'H294', 'country' => 'IT'])],
            ['code' => 'I452|IT', 'label_en' => 'Salerno', 'label_it' => 'Salerno', 'metadata' => json_encode(['belfiore' => 'I452', 'country' => 'IT'])],
            ['code' => 'D390|IT', 'label_en' => 'Ferrara', 'label_it' => 'Ferrara', 'metadata' => json_encode(['belfiore' => 'D390', 'country' => 'IT'])],
            ['code' => 'I754|IT', 'label_en' => 'Sassari', 'label_it' => 'Sassari', 'metadata' => json_encode(['belfiore' => 'I754', 'country' => 'IT'])],

            // Additional municipalities (Province capitals and common cities)
            ['code' => 'H517|IT', 'label_en' => 'Romeno', 'label_it' => 'Romeno', 'metadata' => json_encode(['belfiore' => 'H517', 'country' => 'IT'])],
            ['code' => 'B352|IT', 'label_en' => 'Trento', 'label_it' => 'Trento', 'metadata' => json_encode(['belfiore' => 'L378', 'country' => 'IT'])],
            ['code' => 'A794|IT', 'label_en' => 'Ancona', 'label_it' => 'Ancona', 'metadata' => json_encode(['belfiore' => 'A271', 'country' => 'IT'])],
            ['code' => 'C933|IT', 'label_en' => 'Pescara', 'label_it' => 'Pescara', 'metadata' => json_encode(['belfiore' => 'G482', 'country' => 'IT'])],
            ['code' => 'E690|IT', 'label_en' => "L'Aquila", 'label_it' => "L'Aquila", 'metadata' => json_encode(['belfiore' => 'A345', 'country' => 'IT'])],
            ['code' => 'A783|IT', 'label_en' => 'Aosta', 'label_it' => 'Aosta', 'metadata' => json_encode(['belfiore' => 'A326', 'country' => 'IT'])],
            ['code' => 'H211|IT', 'label_en' => 'Potenza', 'label_it' => 'Potenza', 'metadata' => json_encode(['belfiore' => 'G942', 'country' => 'IT'])],
            ['code' => 'C752|IT', 'label_en' => 'Catanzaro', 'label_it' => 'Catanzaro', 'metadata' => json_encode(['belfiore' => 'C352', 'country' => 'IT'])],
            ['code' => 'F152|IT', 'label_en' => 'Campobasso', 'label_it' => 'Campobasso', 'metadata' => json_encode(['belfiore' => 'B402', 'country' => 'IT'])],
        ];

        // FOREIGN COUNTRIES - Official Italian Belfiore Z-codes
        $foreignCountries = [
            // Europe
            ['code' => 'Z100|AL', 'label_en' => 'Albania', 'label_it' => 'Albania', 'metadata' => json_encode(['belfiore' => 'Z100', 'country' => 'AL'])],
            ['code' => 'Z101|AD', 'label_en' => 'Andorra', 'label_it' => 'Andorra', 'metadata' => json_encode(['belfiore' => 'Z101', 'country' => 'AD'])],
            ['code' => 'Z102|AT', 'label_en' => 'Austria', 'label_it' => 'Austria', 'metadata' => json_encode(['belfiore' => 'Z102', 'country' => 'AT'])],
            ['code' => 'Z103|BE', 'label_en' => 'Belgium', 'label_it' => 'Belgio', 'metadata' => json_encode(['belfiore' => 'Z103', 'country' => 'BE'])],
            ['code' => 'Z104|BG', 'label_en' => 'Bulgaria', 'label_it' => 'Bulgaria', 'metadata' => json_encode(['belfiore' => 'Z104', 'country' => 'BG'])],
            ['code' => 'Z105|BY', 'label_en' => 'Belarus', 'label_it' => 'Bielorussia', 'metadata' => json_encode(['belfiore' => 'Z105', 'country' => 'BY'])],
            ['code' => 'Z106|HR', 'label_en' => 'Croatia', 'label_it' => 'Croazia', 'metadata' => json_encode(['belfiore' => 'Z106', 'country' => 'HR'])],
            ['code' => 'Z107|DK', 'label_en' => 'Denmark', 'label_it' => 'Danimarca', 'metadata' => json_encode(['belfiore' => 'Z107', 'country' => 'DK'])],
            ['code' => 'Z108|EE', 'label_en' => 'Estonia', 'label_it' => 'Estonia', 'metadata' => json_encode(['belfiore' => 'Z108', 'country' => 'EE'])],
            ['code' => 'Z109|FI', 'label_en' => 'Finland', 'label_it' => 'Finlandia', 'metadata' => json_encode(['belfiore' => 'Z109', 'country' => 'FI'])],
            ['code' => 'Z110|FR', 'label_en' => 'France', 'label_it' => 'Francia', 'metadata' => json_encode(['belfiore' => 'Z110', 'country' => 'FR'])],
            ['code' => 'Z112|DE', 'label_en' => 'Germany', 'label_it' => 'Germania', 'metadata' => json_encode(['belfiore' => 'Z112', 'country' => 'DE'])],
            ['code' => 'Z115|GR', 'label_en' => 'Greece', 'label_it' => 'Grecia', 'metadata' => json_encode(['belfiore' => 'Z115', 'country' => 'GR'])],
            ['code' => 'Z114|GB', 'label_en' => 'United Kingdom', 'label_it' => 'Regno Unito', 'metadata' => json_encode(['belfiore' => 'Z114', 'country' => 'GB'])],
            ['code' => 'Z116|IE', 'label_en' => 'Ireland', 'label_it' => 'Irlanda', 'metadata' => json_encode(['belfiore' => 'Z116', 'country' => 'IE'])],
            ['code' => 'Z117|IS', 'label_en' => 'Iceland', 'label_it' => 'Islanda', 'metadata' => json_encode(['belfiore' => 'Z117', 'country' => 'IS'])],
            ['code' => 'Z118|LV', 'label_en' => 'Latvia', 'label_it' => 'Lettonia', 'metadata' => json_encode(['belfiore' => 'Z118', 'country' => 'LV'])],
            ['code' => 'Z119|LI', 'label_en' => 'Liechtenstein', 'label_it' => 'Liechtenstein', 'metadata' => json_encode(['belfiore' => 'Z119', 'country' => 'LI'])],
            ['code' => 'Z120|LT', 'label_en' => 'Lithuania', 'label_it' => 'Lituania', 'metadata' => json_encode(['belfiore' => 'Z120', 'country' => 'LT'])],
            ['code' => 'Z121|LU', 'label_en' => 'Luxembourg', 'label_it' => 'Lussemburgo', 'metadata' => json_encode(['belfiore' => 'Z121', 'country' => 'LU'])],
            ['code' => 'Z122|MT', 'label_en' => 'Malta', 'label_it' => 'Malta', 'metadata' => json_encode(['belfiore' => 'Z122', 'country' => 'MT'])],
            ['code' => 'Z123|MD', 'label_en' => 'Moldova', 'label_it' => 'Moldavia', 'metadata' => json_encode(['belfiore' => 'Z123', 'country' => 'MD'])],
            ['code' => 'Z124|MC', 'label_en' => 'Monaco', 'label_it' => 'Monaco', 'metadata' => json_encode(['belfiore' => 'Z124', 'country' => 'MC'])],
            ['code' => 'Z125|NO', 'label_en' => 'Norway', 'label_it' => 'Norvegia', 'metadata' => json_encode(['belfiore' => 'Z125', 'country' => 'NO'])],
            ['code' => 'Z126|NL', 'label_en' => 'Netherlands', 'label_it' => 'Paesi Bassi', 'metadata' => json_encode(['belfiore' => 'Z126', 'country' => 'NL'])],
            ['code' => 'Z127|PL', 'label_en' => 'Poland', 'label_it' => 'Polonia', 'metadata' => json_encode(['belfiore' => 'Z127', 'country' => 'PL'])],
            ['code' => 'Z128|PT', 'label_en' => 'Portugal', 'label_it' => 'Portogallo', 'metadata' => json_encode(['belfiore' => 'Z128', 'country' => 'PT'])],
            ['code' => 'Z129|RO', 'label_en' => 'Romania', 'label_it' => 'Romania', 'metadata' => json_encode(['belfiore' => 'Z129', 'country' => 'RO'])],
            ['code' => 'Z154|RU', 'label_en' => 'Russia', 'label_it' => 'Russia', 'metadata' => json_encode(['belfiore' => 'Z154', 'country' => 'RU'])],
            ['code' => 'Z130|SM', 'label_en' => 'San Marino', 'label_it' => 'San Marino', 'metadata' => json_encode(['belfiore' => 'Z130', 'country' => 'SM'])],
            ['code' => 'Z131|ES', 'label_en' => 'Spain', 'label_it' => 'Spagna', 'metadata' => json_encode(['belfiore' => 'Z131', 'country' => 'ES'])],
            ['code' => 'Z132|SE', 'label_en' => 'Sweden', 'label_it' => 'Svezia', 'metadata' => json_encode(['belfiore' => 'Z132', 'country' => 'SE'])],
            ['code' => 'Z133|CH', 'label_en' => 'Switzerland', 'label_it' => 'Svizzera', 'metadata' => json_encode(['belfiore' => 'Z133', 'country' => 'CH'])],
            ['code' => 'Z134|TR', 'label_en' => 'Turkey', 'label_it' => 'Turchia', 'metadata' => json_encode(['belfiore' => 'Z134', 'country' => 'TR'])],
            ['code' => 'Z135|UA', 'label_en' => 'Ukraine', 'label_it' => 'Ucraina', 'metadata' => json_encode(['belfiore' => 'Z135', 'country' => 'UA'])],
            ['code' => 'Z153|HU', 'label_en' => 'Hungary', 'label_it' => 'Ungheria', 'metadata' => json_encode(['belfiore' => 'Z153', 'country' => 'HU'])],
            ['code' => 'Z138|VA', 'label_en' => 'Vatican City', 'label_it' => 'Città del Vaticano', 'metadata' => json_encode(['belfiore' => 'Z138', 'country' => 'VA'])],
            ['code' => 'Z160|RS', 'label_en' => 'Serbia', 'label_it' => 'Serbia', 'metadata' => json_encode(['belfiore' => 'Z160', 'country' => 'RS'])],
            ['code' => 'Z161|ME', 'label_en' => 'Montenegro', 'label_it' => 'Montenegro', 'metadata' => json_encode(['belfiore' => 'Z161', 'country' => 'ME'])],
            ['code' => 'Z140|CZ', 'label_en' => 'Czech Republic', 'label_it' => 'Repubblica Ceca', 'metadata' => json_encode(['belfiore' => 'Z140', 'country' => 'CZ'])],
            ['code' => 'Z141|SK', 'label_en' => 'Slovakia', 'label_it' => 'Slovacchia', 'metadata' => json_encode(['belfiore' => 'Z141', 'country' => 'SK'])],
            ['code' => 'Z142|SI', 'label_en' => 'Slovenia', 'label_it' => 'Slovenia', 'metadata' => json_encode(['belfiore' => 'Z142', 'country' => 'SI'])],
            ['code' => 'Z144|BA', 'label_en' => 'Bosnia and Herzegovina', 'label_it' => 'Bosnia ed Erzegovina', 'metadata' => json_encode(['belfiore' => 'Z144', 'country' => 'BA'])],
            ['code' => 'Z145|MK', 'label_en' => 'North Macedonia', 'label_it' => 'Macedonia del Nord', 'metadata' => json_encode(['belfiore' => 'Z145', 'country' => 'MK'])],
            ['code' => 'Z139|CY', 'label_en' => 'Cyprus', 'label_it' => 'Cipro', 'metadata' => json_encode(['belfiore' => 'Z139', 'country' => 'CY'])],

            // Americas
            ['code' => 'Z401|CA', 'label_en' => 'Canada', 'label_it' => 'Canada', 'metadata' => json_encode(['belfiore' => 'Z401', 'country' => 'CA'])],
            ['code' => 'Z404|US', 'label_en' => 'United States', 'label_it' => 'Stati Uniti', 'metadata' => json_encode(['belfiore' => 'Z404', 'country' => 'US'])],
            ['code' => 'Z514|MX', 'label_en' => 'Mexico', 'label_it' => 'Messico', 'metadata' => json_encode(['belfiore' => 'Z514', 'country' => 'MX'])],
            ['code' => 'Z600|AR', 'label_en' => 'Argentina', 'label_it' => 'Argentina', 'metadata' => json_encode(['belfiore' => 'Z600', 'country' => 'AR'])],
            ['code' => 'Z602|BR', 'label_en' => 'Brazil', 'label_it' => 'Brasile', 'metadata' => json_encode(['belfiore' => 'Z602', 'country' => 'BR'])],
            ['code' => 'Z506|CL', 'label_en' => 'Chile', 'label_it' => 'Cile', 'metadata' => json_encode(['belfiore' => 'Z506', 'country' => 'CL'])],
            ['code' => 'Z508|CO', 'label_en' => 'Colombia', 'label_it' => 'Colombia', 'metadata' => json_encode(['belfiore' => 'Z508', 'country' => 'CO'])],
            ['code' => 'Z511|PE', 'label_en' => 'Peru', 'label_it' => 'Perù', 'metadata' => json_encode(['belfiore' => 'Z511', 'country' => 'PE'])],
            ['code' => 'Z523|VE', 'label_en' => 'Venezuela', 'label_it' => 'Venezuela', 'metadata' => json_encode(['belfiore' => 'Z523', 'country' => 'VE'])],
            ['code' => 'Z609|UY', 'label_en' => 'Uruguay', 'label_it' => 'Uruguay', 'metadata' => json_encode(['belfiore' => 'Z609', 'country' => 'UY'])],
            ['code' => 'Z529|EC', 'label_en' => 'Ecuador', 'label_it' => 'Ecuador', 'metadata' => json_encode(['belfiore' => 'Z529', 'country' => 'EC'])],
            ['code' => 'Z603|BO', 'label_en' => 'Bolivia', 'label_it' => 'Bolivia', 'metadata' => json_encode(['belfiore' => 'Z603', 'country' => 'BO'])],
            ['code' => 'Z527|PY', 'label_en' => 'Paraguay', 'label_it' => 'Paraguay', 'metadata' => json_encode(['belfiore' => 'Z527', 'country' => 'PY'])],

            // Asia
            ['code' => 'Z210|CN', 'label_en' => 'China', 'label_it' => 'Cina', 'metadata' => json_encode(['belfiore' => 'Z210', 'country' => 'CN'])],
            ['code' => 'Z222|IN', 'label_en' => 'India', 'label_it' => 'India', 'metadata' => json_encode(['belfiore' => 'Z222', 'country' => 'IN'])],
            ['code' => 'Z219|JP', 'label_en' => 'Japan', 'label_it' => 'Giappone', 'metadata' => json_encode(['belfiore' => 'Z219', 'country' => 'JP'])],
            ['code' => 'Z230|KR', 'label_en' => 'South Korea', 'label_it' => 'Corea del Sud', 'metadata' => json_encode(['belfiore' => 'Z230', 'country' => 'KR'])],
            ['code' => 'Z236|TH', 'label_en' => 'Thailand', 'label_it' => 'Thailandia', 'metadata' => json_encode(['belfiore' => 'Z236', 'country' => 'TH'])],
            ['code' => 'Z223|ID', 'label_en' => 'Indonesia', 'label_it' => 'Indonesia', 'metadata' => json_encode(['belfiore' => 'Z223', 'country' => 'ID'])],
            ['code' => 'Z215|PH', 'label_en' => 'Philippines', 'label_it' => 'Filippine', 'metadata' => json_encode(['belfiore' => 'Z215', 'country' => 'PH'])],
            ['code' => 'Z239|VN', 'label_en' => 'Vietnam', 'label_it' => 'Vietnam', 'metadata' => json_encode(['belfiore' => 'Z239', 'country' => 'VN'])],
            ['code' => 'Z232|MY', 'label_en' => 'Malaysia', 'label_it' => 'Malesia', 'metadata' => json_encode(['belfiore' => 'Z232', 'country' => 'MY'])],
            ['code' => 'Z237|SG', 'label_en' => 'Singapore', 'label_it' => 'Singapore', 'metadata' => json_encode(['belfiore' => 'Z237', 'country' => 'SG'])],
            ['code' => 'Z224|IL', 'label_en' => 'Israel', 'label_it' => 'Israele', 'metadata' => json_encode(['belfiore' => 'Z224', 'country' => 'IL'])],
            ['code' => 'Z255|AE', 'label_en' => 'United Arab Emirates', 'label_it' => 'Emirati Arabi Uniti', 'metadata' => json_encode(['belfiore' => 'Z255', 'country' => 'AE'])],
            ['code' => 'Z253|SA', 'label_en' => 'Saudi Arabia', 'label_it' => 'Arabia Saudita', 'metadata' => json_encode(['belfiore' => 'Z253', 'country' => 'SA'])],
            ['code' => 'Z249|IQ', 'label_en' => 'Iraq', 'label_it' => 'Iraq', 'metadata' => json_encode(['belfiore' => 'Z249', 'country' => 'IQ'])],
            ['code' => 'Z250|IR', 'label_en' => 'Iran', 'label_it' => 'Iran', 'metadata' => json_encode(['belfiore' => 'Z250', 'country' => 'IR'])],
            ['code' => 'Z246|PK', 'label_en' => 'Pakistan', 'label_it' => 'Pakistan', 'metadata' => json_encode(['belfiore' => 'Z246', 'country' => 'PK'])],
            ['code' => 'Z245|BD', 'label_en' => 'Bangladesh', 'label_it' => 'Bangladesh', 'metadata' => json_encode(['belfiore' => 'Z245', 'country' => 'BD'])],

            // Africa
            ['code' => 'Z336|EG', 'label_en' => 'Egypt', 'label_it' => 'Egitto', 'metadata' => json_encode(['belfiore' => 'Z336', 'country' => 'EG'])],
            ['code' => 'Z359|ZA', 'label_en' => 'South Africa', 'label_it' => 'Sudafrica', 'metadata' => json_encode(['belfiore' => 'Z359', 'country' => 'ZA'])],
            ['code' => 'Z330|MA', 'label_en' => 'Morocco', 'label_it' => 'Marocco', 'metadata' => json_encode(['belfiore' => 'Z330', 'country' => 'MA'])],
            ['code' => 'Z301|DZ', 'label_en' => 'Algeria', 'label_it' => 'Algeria', 'metadata' => json_encode(['belfiore' => 'Z301', 'country' => 'DZ'])],
            ['code' => 'Z341|TN', 'label_en' => 'Tunisia', 'label_it' => 'Tunisia', 'metadata' => json_encode(['belfiore' => 'Z341', 'country' => 'TN'])],
            ['code' => 'Z319|KE', 'label_en' => 'Kenya', 'label_it' => 'Kenya', 'metadata' => json_encode(['belfiore' => 'Z319', 'country' => 'KE'])],
            ['code' => 'Z338|ET', 'label_en' => 'Ethiopia', 'label_it' => 'Etiopia', 'metadata' => json_encode(['belfiore' => 'Z338', 'country' => 'ET'])],
            ['code' => 'Z335|NG', 'label_en' => 'Nigeria', 'label_it' => 'Nigeria', 'metadata' => json_encode(['belfiore' => 'Z335', 'country' => 'NG'])],

            // Oceania
            ['code' => 'Z700|AU', 'label_en' => 'Australia', 'label_it' => 'Australia', 'metadata' => json_encode(['belfiore' => 'Z700', 'country' => 'AU'])],
            ['code' => 'Z719|NZ', 'label_en' => 'New Zealand', 'label_it' => 'Nuova Zelanda', 'metadata' => json_encode(['belfiore' => 'Z719', 'country' => 'NZ'])],
        ];

        // Merge Italian cities and foreign countries
        $codes = array_merge($italianCities, $foreignCountries);

        return $codes;
    }
}
