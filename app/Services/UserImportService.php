<?php

namespace App\Services;

use App\Models\User;
use App\Models\Company;
use App\Services\AuditLogService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Spatie\Permission\Models\Role;

class UserImportService
{
    public const TEMPLATE_HEADERS = [
        'name',
        'surname',
        'email',
        'password',
        'status',
        'role',
        'companies',
        'company_percentages',
        'primary_company',
        'date_of_birth',
        'place_of_birth',
        'country',
        'phone',
        'mobile',
        'gender',
        'cf',
        'address',
    ];

    public const TEMPLATE_SAMPLE_ROW = [
        'name' => 'Mario',
        'surname' => 'Rossi',
        'email' => 'mario.rossi@example.com',
        'password' => '',
        'status' => 'parked',
        'role' => 'end_user',
        'companies' => '1',
        'company_percentages' => '100',
        'primary_company' => '1',
        'date_of_birth' => '1988-04-12',
        'place_of_birth' => 'Rome',
        'country' => 'IT',
        'phone' => '+39 06 1234567',
        'mobile' => '+39 333 1234567',
        'gender' => 'male',
        'cf' => 'RSSMRA88D12H501X',
        'address' => 'Via Roma 1, 00100 Roma',
    ];

    private const REQUIRED_HEADERS = [
        'name',
        'surname',
        'email',
        'role',
        'status',
    ];

    /**
     * Import users from the provided spreadsheet.
     *
     * @return array{success_count:int,created:array<int, array<string, mixed>>,errors:array<int, array<string, mixed>>}
     */
    public function import(UploadedFile $file, User $actingUser): array
    {
        $result = [
            'success_count' => 0,
            'created' => [],
            'errors' => [],
        ];

        $realPath = $file->getRealPath();
        if (!$realPath) {
            $result['errors'][] = [
                'row' => 1,
                'message' => __('users.bulk_upload_unreadable_file'),
            ];
            return $result;
        }

        try {
            $reader = IOFactory::createReaderForFile($realPath);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($realPath);
        } catch (\Throwable $exception) {
            $result['errors'][] = [
                'row' => 1,
                'message' => __('users.bulk_upload_unreadable_file'),
            ];

            return $result;
        }
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        if (empty($rows)) {
            $result['errors'][] = [
                'row' => 1,
                'message' => __('users.bulk_upload_empty_file'),
            ];
            return $result;
        }

        $headerRow = array_shift($rows);
        $headerMap = $this->normalizeHeaders($headerRow);

        $missingHeaders = array_values(array_diff(self::REQUIRED_HEADERS, $headerMap->filter()->all()));
        if (!empty($missingHeaders)) {
            $result['errors'][] = [
                'row' => 1,
                'message' => __('users.bulk_upload_missing_headers', [
                    'headers' => implode(', ', $missingHeaders),
                ]),
            ];
            return $result;
        }

        $processedEmails = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // account for header row

            if ($this->rowIsEmpty($row)) {
                continue;
            }

            $data = $this->mapRowToData($headerMap, $row);
            $email = strtolower($data['email'] ?? '');

            if (!$email) {
                $result['errors'][] = [
                    'row' => $rowNumber,
                    'message' => __('users.bulk_upload_missing_email'),
                ];
                continue;
            }

            if (in_array($email, $processedEmails, true)) {
                $result['errors'][] = [
                    'row' => $rowNumber,
                    'email' => $email,
                    'message' => __('users.bulk_upload_duplicate_email'),
                ];
                continue;
            }

            $processedEmails[] = $email;
            $data['email'] = $email;

            $prepared = $this->prepareRowData($data);

            $validator = $this->makeRowValidator($prepared);
            if ($validator->fails()) {
                $result['errors'][] = [
                    'row' => $rowNumber,
                    'email' => $email,
                    'message' => implode(' | ', $validator->errors()->all()),
                ];
                continue;
            }

            $role = $this->resolveRole($prepared['role'] ?? null);
            if (!$role) {
                $result['errors'][] = [
                    'row' => $rowNumber,
                    'email' => $email,
                    'message' => __('users.bulk_upload_unknown_role', ['role' => $prepared['role']]),
                ];
                continue;
            }

            $companyValidation = $this->validateCompanyData($prepared);
            if ($companyValidation['failed']) {
                $result['errors'][] = [
                    'row' => $rowNumber,
                    'email' => $email,
                    'message' => $companyValidation['message'],
                ];
                continue;
            }

            try {
                DB::beginTransaction();

                $password = $prepared['password'] ?? null;
                $password = $password ?: Str::random(12);
                $user = User::create([
                    'name' => $prepared['name'],
                    'surname' => $prepared['surname'],
                    'email' => $email,
                    'password' => Hash::make($password),
                    'status' => $prepared['status'] ?? 'parked',
                    'phone' => $prepared['phone'] ?? null,
                    'mobile' => $prepared['mobile'] ?? null,
                    'gender' => $prepared['gender'] ?? null,
                    'date_of_birth' => $prepared['date_of_birth'] ?? null,
                    'place_of_birth' => $prepared['place_of_birth'] ?? null,
                    'country' => $prepared['country'] ?? null,
                    'cf' => $prepared['cf'] ?? null,
                    'address' => $prepared['address'] ?? null,
                ]);

                $user->assignRole($role);

                if (!empty($companyValidation['companies'])) {
                    $pivotData = [];
                    $primaryCompany = $companyValidation['primary'] ?? $companyValidation['companies'][0];

                    foreach ($companyValidation['companies'] as $idx => $companyId) {
                        $pivotData[$companyId] = [
                            'is_primary' => $companyId === $primaryCompany,
                            'role_in_company' => $role->name === 'company_manager' ? 'Manager' : 'Employee',
                            'joined_at' => Carbon::now(),
                            'percentage' => $companyValidation['percentages'][$idx] ?? null,
                        ];
                    }

                    $user->companies()->attach($pivotData);
                }

                DB::commit();

                $result['success_count']++;
                $result['created'][] = [
                    'row' => $rowNumber,
                    'email' => $user->email,
                    'name' => $user->full_name,
                    'generated_password' => $prepared['password'] ? null : $password,
                ];
            } catch (\Throwable $exception) {
                DB::rollBack();
                $result['errors'][] = [
                    'row' => $rowNumber,
                    'email' => $email,
                    'message' => $exception->getMessage(),
                ];
            }
        }

        AuditLogService::logCustom(
            'bulk_user_import',
            __('users.bulk_upload_audit_log', [
                'count' => $result['success_count'],
                'file' => $file->getClientOriginalName(),
            ]),
            'users',
            $result['errors'] ? 'warning' : 'info',
            [
                'success_count' => $result['success_count'],
                'error_count' => count($result['errors']),
                'imported_by' => $actingUser->id,
                'file_name' => $file->getClientOriginalName(),
            ]
        );

        return $result;
    }

    /**
     * Normalize header values to snake_case strings.
     */
    private function normalizeHeaders(array $headerRow): Collection
    {
        return collect($headerRow)->map(function ($value) {
            if ($value === null) {
                return null;
            }

            $normalized = Str::snake(Str::lower(trim((string) $value)));
            return in_array($normalized, self::TEMPLATE_HEADERS, true) ? $normalized : null;
        });
    }

    /**
     * Determine if the row is empty.
     */
    private function rowIsEmpty(array $row): bool
    {
        foreach ($row as $cell) {
            if ($cell !== null && trim((string) $cell) !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * Map a row to header-based data array.
     */
    private function mapRowToData(Collection $headerMap, array $row): array
    {
        $data = [];
        foreach ($headerMap as $index => $key) {
            if (!$key) {
                continue;
            }

            $value = $row[$index] ?? null;
            $data[$key] = is_string($value) ? trim($value) : $value;
        }

        return $data;
    }

    /**
     * Prepare raw row data for validation.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function prepareRowData(array $data): array
    {
        $prepared = $data;
        $prepared['status'] = strtolower($prepared['status'] ?? 'parked');
        $prepared['role'] = strtolower($prepared['role'] ?? 'end_user');

        if (!empty($prepared['date_of_birth'])) {
            try {
                $prepared['date_of_birth'] = Carbon::parse($prepared['date_of_birth'])->format('Y-m-d');
            } catch (\Throwable) {
                // keep original value for validator to catch
            }
        }

        if (!empty($prepared['country'])) {
            $prepared['country'] = strtoupper($prepared['country']);
        }

        return $prepared;
    }

    /**
     * Build validator for a single row.
     */
    private function makeRowValidator(array $data)
    {
        $countries = ['IT', 'US', 'GB', 'FR', 'DE', 'ES'];

        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:8'],
            'status' => ['required', 'in:active,inactive,parked'],
            'role' => ['required', 'string'],
            'phone' => ['nullable', 'string', 'max:20'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'place_of_birth' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'size:2', 'in:' . implode(',', $countries)],
            'cf' => ['nullable', 'string', 'size:16', 'unique:users,cf'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);
    }

    /**
     * Validate company-related columns and return normalized data.
     *
     * @param array<string, mixed> $data
     * @return array{failed:bool,message?:string,companies?:array<int,int>,percentages?:array<int,float>,primary?:int|null}
     */
    private function validateCompanyData(array $data): array
    {
        $companies = $this->splitList($data['companies'] ?? '');
        $percentagesRaw = $this->splitList($data['company_percentages'] ?? '');
        $primary = isset($data['primary_company']) && $data['primary_company'] !== ''
            ? (int) $data['primary_company']
            : null;

        if (empty($companies)) {
            return [
                'failed' => false,
                'companies' => [],
                'percentages' => [],
                'primary' => null,
            ];
        }

        $companyIds = array_map('intval', $companies);
        $existingCompanies = Company::whereIn('id', $companyIds)->pluck('id')->all();

        if (count($existingCompanies) !== count($companyIds)) {
            $missing = array_diff($companyIds, $existingCompanies);
            return [
                'failed' => true,
                'message' => __('users.bulk_upload_unknown_company', [
                    'ids' => implode(', ', $missing),
                ]),
            ];
        }

        $percentages = [];
        if (!empty($percentagesRaw)) {
            if (count($percentagesRaw) !== count($companyIds)) {
                return [
                    'failed' => true,
                    'message' => __('users.bulk_upload_percentage_mismatch'),
                ];
            }

            $percentages = array_map(static function ($value) {
                return (float) $value;
            }, $percentagesRaw);

            $total = array_sum($percentages);
            if (abs($total - 100) > 0.01) {
                return [
                    'failed' => true,
                    'message' => __('users.bulk_upload_percentage_total'),
                ];
            }
        }

        if ($primary !== null && !in_array($primary, $companyIds, true)) {
            return [
                'failed' => true,
                'message' => __('users.bulk_upload_primary_missing'),
            ];
        }

        return [
            'failed' => false,
            'companies' => $companyIds,
            'percentages' => $percentages,
            'primary' => $primary,
        ];
    }

    /**
     * Resolve role by given name.
     */
    private function resolveRole(?string $roleName): ?Role
    {
        if (!$roleName) {
            return null;
        }

        return Role::where('name', $roleName)->first();
    }

    /**
     * Split comma separated list into trimmed array.
     *
     * @return array<int, string>
     */
    private function splitList(?string $value): array
    {
        if (!$value) {
            return [];
        }

        return array_values(array_filter(array_map(static function ($item) {
            return trim((string) $item);
        }, explode(',', $value))));
    }

    /**
     * Get sample row values in header order.
     *
     * @return array<int, string>
     */
    public static function sampleRow(): array
    {
        return array_map(static function ($header) {
            return self::TEMPLATE_SAMPLE_ROW[$header] ?? '';
        }, self::TEMPLATE_HEADERS);
    }
}
