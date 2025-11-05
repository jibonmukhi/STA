<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use App\Models\ProfileChangeRequest;
use App\Notifications\ProfileChangeReviewedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Services\AuditLogService;

class CompanyManagerController extends Controller
{
    /**
     * Show company manager profile page
     */
    public function profile()
    {
        $user = Auth::user();

        // Check if user is a company manager
        if (!$user->hasRole('company_manager')) {
            abort(403, 'Unauthorized access');
        }

        // Get user's companies with details
        $companies = $user->companies()->with('users')->get();

        // Get user statistics
        $stats = [
            'total_companies' => $companies->count(),
            'total_users_managed' => $companies->sum(function($company) {
                return $company->users()->whereHas('roles', function($q) {
                    $q->where('name', 'end_user');
                })->count();
            }),
            'pending_approvals' => $companies->sum(function($company) {
                return $company->users()->where('status', 'parked')->count();
            }),
            'active_users' => $companies->sum(function($company) {
                return $company->users()->where('status', 'active')->count();
            }),
        ];

        return view('company-manager.profile', compact('user', 'companies', 'stats'));
    }

    /**
     * Update company manager profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // Check if user is a company manager
        if (!$user->hasRole('company_manager')) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'place_of_birth' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female,other',
            'cf' => 'nullable|string|max:16',
            'address' => 'nullable|string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $oldData = $user->only([
            'name', 'surname', 'email', 'phone', 'mobile', 'date_of_birth',
            'place_of_birth', 'country', 'gender', 'cf', 'address'
        ]);

        $user->fill($request->except('photo'));

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            $photoPath = $request->file('photo')->store('photos', 'public');
            $user->photo = $photoPath;
        }

        $user->save();

        // Log the profile update
        AuditLogService::logUpdate(
            $user,
            $oldData,
            $user->only([
                'name', 'surname', 'email', 'phone', 'mobile', 'date_of_birth',
                'place_of_birth', 'country', 'gender', 'cf', 'address'
            ]),
            'Company Manager updated their profile',
            'profile',
            'info'
        );

        return redirect()->route('company-manager.profile')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Update company manager password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        // Check if user is a company manager
        if (!$user->hasRole('company_manager')) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Log password change
        AuditLogService::logCustom(
            'password_changed',
            'Company Manager changed their password',
            'security',
            'info',
            ['user_id' => $user->id]
        );

        return redirect()->route('company-manager.profile')
            ->with('success', 'Password updated successfully!');
    }

    /**
     * Show audit logs for company manager
     * Only shows logs related to users in their managed companies
     */
    public function auditLogs(Request $request)
    {
        $user = Auth::user();

        // Check if user is a company manager
        if (!$user->hasRole('company_manager')) {
            abort(403, 'Unauthorized access');
        }

        // Get all user IDs from companies this manager manages
        $companyUserIds = $user->companies()
            ->with('users')
            ->get()
            ->pluck('users')
            ->flatten()
            ->pluck('id')
            ->unique()
            ->toArray();

        // Add the manager's own ID
        $companyUserIds[] = $user->id;

        // Get audit logs for company-related users only
        $query = AuditLog::whereIn('user_id', $companyUserIds);

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by module
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('model_name', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%");
            });
        }

        // Get unique values for filters (only from company-related logs)
        $actions = AuditLog::whereIn('user_id', $companyUserIds)->distinct()->pluck('action')->sort();
        $modules = AuditLog::whereIn('user_id', $companyUserIds)->distinct()->pluck('module')->sort();

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination with per_page options
        $perPage = $request->get('per_page', 25);
        $logs = $query->paginate($perPage)->withQueryString();

        // Statistics (only from company-related logs)
        $stats = [
            'total_today' => AuditLog::whereIn('user_id', $companyUserIds)->whereDate('created_at', today())->count(),
            'total_week' => AuditLog::whereIn('user_id', $companyUserIds)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'total_month' => AuditLog::whereIn('user_id', $companyUserIds)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'total_all' => AuditLog::whereIn('user_id', $companyUserIds)->count(),
        ];

        return view('company-manager.audit-logs', compact('logs', 'actions', 'modules', 'stats'));
    }

    /**
     * Download blank Excel template for bulk user import
     */
    public function downloadTemplate()
    {
        $user = Auth::user();

        // Check if user is a company manager
        if (!$user->hasRole('company_manager')) {
            abort(403, 'Unauthorized access');
        }

        // Create new spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers (Italian)
        $headers = [
            'Nome *',
            'Cognome *',
            'Email *',
            'Telefono',
            'Cellulare',
            'Data di Nascita (YYYY-MM-DD)',
            'Luogo di Nascita',
            'Paese',
            'Genere (male/female/other)',
            'CF (Codice Fiscale)',
            'Indirizzo',
            'Percentuale Azienda *',
        ];

        // Write headers in bold
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:L1')->getFont()->setBold(true);
        $sheet->getStyle('A1:L1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Set column widths
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setWidth(20);
        }

        // Add sample data row (Italian)
        $sampleData = [
            'Mario',
            'Rossi',
            'mario.rossi@example.com',
            '+39 06 1234567',
            '+39 333 1234567',
            '1988-04-12',
            'Roma',
            'Italia',
            'male',
            'RSSMRA88D12H501X',
            'Via Roma 1, 00100 Roma, Italia',
            '100',
        ];

        $sheet->fromArray($sampleData, null, 'A2');
        $sheet->getStyle('A2:L2')->getFont()->setItalic(true);
        $sheet->getStyle('A2:L2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A2:L2')->getFill()->getStartColor()->setARGB('FFE0E0E0');

        // Add instructions in a separate section (Italian)
        $instructionRow = 4;
        $sheet->setCellValue('A' . $instructionRow, 'ISTRUZIONI:');
        $sheet->getStyle('A' . $instructionRow)->getFont()->setBold(true);

        $instructions = [
            '1. I campi contrassegnati con * sono obbligatori',
            '2. L\'email deve essere univoca (non già presente nel sistema)',
            '3. Formato data: YYYY-MM-DD (es. 1988-04-12)',
            '4. Genere: deve essere male, female o other',
            '5. Percentuale Azienda: deve essere compresa tra 1 e 100',
            '6. Non modificare la riga di intestazione',
            '7. Eliminare la riga di dati di esempio prima del caricamento',
            '8. Aggiungere gli utenti a partire dalla riga 3',
        ];

        $instructionRow++;
        foreach ($instructions as $instruction) {
            $sheet->setCellValue('A' . $instructionRow, $instruction);
            $instructionRow++;
        }

        // Log the template download
        AuditLogService::logCustom(
            'template_download',
            'Company Manager downloaded bulk user import template',
            'bulk_import',
            'info',
            ['user_id' => $user->id]
        );

        // Generate filename (Italian)
        $filename = 'modello_importazione_utenti_' . now()->format('Y_m_d') . '.xlsx';

        // Create writer and return download
        $writer = new Xlsx($spreadsheet);

        // Return as download
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Show bulk user import page
     */
    public function bulkImportForm()
    {
        $user = Auth::user();

        // Check if user is a company manager
        if (!$user->hasRole('company_manager')) {
            abort(403, 'Unauthorized access');
        }

        // Get user's companies for dropdown
        $companies = $user->companies;

        return view('company-manager.bulk-import', compact('companies'));
    }

    /**
     * Process bulk user import from Excel file
     */
    public function bulkImport(Request $request)
    {
        $user = Auth::user();

        // Check if user is a company manager
        if (!$user->hasRole('company_manager')) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240',
            'company_id' => 'required|exists:companies,id',
        ]);

        $companyId = $request->company_id;

        // Verify user has access to this company
        if (!$user->companies()->where('companies.id', $companyId)->exists()) {
            return back()->withErrors(['company_id' => 'You do not have access to this company']);
        }

        try {
            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Remove header row
            $headers = array_shift($rows);

            // Remove sample data row if it exists (check if email contains 'example.com')
            if (isset($rows[0][2]) && strpos($rows[0][2], 'example.com') !== false) {
                array_shift($rows);
            }

            $imported = 0;
            $skipped = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 3; // Accounting for header and sample rows

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Map columns
                $userData = [
                    'name' => $row[0] ?? null,
                    'surname' => $row[1] ?? null,
                    'email' => $row[2] ?? null,
                    'phone' => $row[3] ?? null,
                    'mobile' => $row[4] ?? null,
                    'date_of_birth' => $row[5] ?? null,
                    'place_of_birth' => $row[6] ?? null,
                    'country' => $row[7] ?? null,
                    'gender' => $row[8] ?? null,
                    'cf' => $row[9] ?? null,
                    'address' => $row[10] ?? null,
                    'percentage' => $row[11] ?? null,
                ];

                // Validate required fields
                if (empty($userData['name']) || empty($userData['surname']) || empty($userData['email'])) {
                    $errors[] = "Row {$rowNumber}: Missing required fields (Name, Surname, or Email)";
                    $skipped++;
                    continue;
                }

                // Validate email format
                if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Row {$rowNumber}: Invalid email format ({$userData['email']})";
                    $skipped++;
                    continue;
                }

                // Check if user already exists
                if (User::where('email', $userData['email'])->exists()) {
                    $errors[] = "Row {$rowNumber}: User with email {$userData['email']} already exists";
                    $skipped++;
                    continue;
                }

                // Validate percentage
                $percentage = floatval($userData['percentage']);
                if ($percentage < 1 || $percentage > 100) {
                    $errors[] = "Row {$rowNumber}: Invalid percentage (must be between 1 and 100)";
                    $skipped++;
                    continue;
                }

                // Validate gender
                if (!empty($userData['gender']) && !in_array(strtolower($userData['gender']), ['male', 'female', 'other'])) {
                    $errors[] = "Row {$rowNumber}: Invalid gender (must be male, female, or other)";
                    $skipped++;
                    continue;
                }

                try {
                    // Create user
                    $newUser = User::create([
                        'name' => $userData['name'],
                        'surname' => $userData['surname'],
                        'email' => $userData['email'],
                        'phone' => $userData['phone'],
                        'mobile' => $userData['mobile'],
                        'date_of_birth' => !empty($userData['date_of_birth']) ? $userData['date_of_birth'] : null,
                        'place_of_birth' => $userData['place_of_birth'],
                        'country' => $userData['country'],
                        'gender' => !empty($userData['gender']) ? strtolower($userData['gender']) : null,
                        'cf' => $userData['cf'],
                        'address' => $userData['address'],
                        'password' => Hash::make('password'), // Default password
                        'status' => 'parked', // Set as parked (pending approval)
                    ]);

                    // Assign end_user role
                    $newUser->assignRole('end_user');

                    // Attach to company with percentage
                    $newUser->companies()->attach($companyId, [
                        'percentage' => $percentage,
                        'is_primary' => true,
                        'joined_at' => now(),
                    ]);

                    $imported++;

                } catch (\Exception $e) {
                    $errors[] = "Row {$rowNumber}: Failed to create user - {$e->getMessage()}";
                    $skipped++;
                }
            }

            // Log the bulk import
            AuditLogService::logCustom(
                'bulk_import',
                "Company Manager imported {$imported} users, skipped {$skipped} rows",
                'bulk_import',
                $skipped > 0 ? 'warning' : 'info',
                [
                    'user_id' => $user->id,
                    'company_id' => $companyId,
                    'imported' => $imported,
                    'skipped' => $skipped,
                    'errors' => count($errors),
                ]
            );

            $message = "Successfully imported {$imported} users.";
            if ($skipped > 0) {
                $message .= " Skipped {$skipped} rows with errors.";
            }

            if (!empty($errors)) {
                return redirect()->route('company-manager.bulk-import')
                    ->with('warning', $message)
                    ->with('import_errors', $errors);
            }

            return redirect()->route('company-users.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            // Log the error
            AuditLogService::logCustom(
                'bulk_import_failed',
                'Company Manager bulk import failed: ' . $e->getMessage(),
                'bulk_import',
                'error',
                ['user_id' => $user->id, 'error' => $e->getMessage()]
            );

            return back()->withErrors(['excel_file' => 'Failed to process file: ' . $e->getMessage()]);
        }
    }

    /**
     * Show profile change requests for company manager
     */
    public function profileChangeRequests()
    {
        $user = Auth::user();

        // Check if user is a company manager
        if (!$user->hasRole('company_manager')) {
            abort(403, 'Unauthorized access');
        }

        // Get all user IDs from companies this manager manages
        $companyUserIds = $user->companies()
            ->with('users')
            ->get()
            ->pluck('users')
            ->flatten()
            ->pluck('id')
            ->unique()
            ->toArray();

        // Get pending profile change requests from users in managed companies
        $requests = ProfileChangeRequest::whereIn('user_id', $companyUserIds)
            ->where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('company-manager.profile-change-requests.index', compact('requests'));
    }

    /**
     * Show specific profile change request for review
     */
    public function showProfileChangeRequest($id)
    {
        $user = Auth::user();

        // Check if user is a company manager
        if (!$user->hasRole('company_manager')) {
            abort(403, 'Unauthorized access');
        }

        // Get the request
        $changeRequest = ProfileChangeRequest::with('user')->findOrFail($id);

        // Check if the request user belongs to one of manager's companies
        $companyUserIds = $user->companies()
            ->with('users')
            ->get()
            ->pluck('users')
            ->flatten()
            ->pluck('id')
            ->unique()
            ->toArray();

        if (!in_array($changeRequest->user_id, $companyUserIds)) {
            abort(403, 'Unauthorized to view this request');
        }

        // Get field labels for display
        $fieldLabels = [
            'name' => trans('profile.name'),
            'surname' => trans('profile.surname'),
            'email' => trans('profile.email'),
            'phone' => trans('profile.phone'),
            'mobile' => trans('profile.mobile'),
            'gender' => trans('profile.gender'),
            'date_of_birth' => trans('profile.date_of_birth'),
            'place_of_birth' => trans('profile.place_of_birth'),
            'country' => trans('profile.country'),
            'cf' => trans('profile.codice_fiscale'),
            'address' => trans('profile.address'),
            'photo' => trans('profile.profile_photo'),
        ];

        return view('company-manager.profile-change-requests.show', compact('changeRequest', 'fieldLabels'));
    }

    /**
     * Approve profile change request
     */
    public function approveProfileChangeRequest($id)
    {
        $user = Auth::user();

        // Check if user is a company manager
        if (!$user->hasRole('company_manager')) {
            abort(403, 'Unauthorized access');
        }

        // Get the request
        $changeRequest = ProfileChangeRequest::with('user')->findOrFail($id);

        // Check if the request user belongs to one of manager's companies
        $companyUserIds = $user->companies()
            ->with('users')
            ->get()
            ->pluck('users')
            ->flatten()
            ->pluck('id')
            ->unique()
            ->toArray();

        if (!in_array($changeRequest->user_id, $companyUserIds)) {
            abort(403, 'Unauthorized to approve this request');
        }

        // Check if already reviewed
        if ($changeRequest->status !== 'pending') {
            return redirect()->route('company-manager.profile-change-requests')
                ->with('error', 'This request has already been reviewed.');
        }

        // Approve the request
        $changeRequest->approve($user);

        // Notify the user
        $changeRequest->user->notify(new ProfileChangeReviewedNotification($changeRequest, true));

        return redirect()->route('company-manager.profile-change-requests')
            ->with('success', trans('profile.changes_approved'));
    }

    /**
     * Reject profile change request
     */
    public function rejectProfileChangeRequest(Request $request, $id)
    {
        $user = Auth::user();

        // Check if user is a company manager
        if (!$user->hasRole('company_manager')) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        // Get the change request
        $changeRequest = ProfileChangeRequest::with('user')->findOrFail($id);

        // Check if the request user belongs to one of manager's companies
        $companyUserIds = $user->companies()
            ->with('users')
            ->get()
            ->pluck('users')
            ->flatten()
            ->pluck('id')
            ->unique()
            ->toArray();

        if (!in_array($changeRequest->user_id, $companyUserIds)) {
            abort(403, 'Unauthorized to reject this request');
        }

        // Check if already reviewed
        if ($changeRequest->status !== 'pending') {
            return redirect()->route('company-manager.profile-change-requests')
                ->with('error', 'This request has already been reviewed.');
        }

        // Reject the request
        $changeRequest->reject($user, $request->input('rejection_reason'));

        // Notify the user
        $changeRequest->user->notify(new ProfileChangeReviewedNotification($changeRequest, false));

        return redirect()->route('company-manager.profile-change-requests')
            ->with('success', trans('profile.changes_rejected'));
    }
}
