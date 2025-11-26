<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\AuditLogService;
use App\Http\Requests\BulkUserUploadRequest;
use App\Http\Requests\BulkUserStatusRequest;
use App\Services\UserImportService;
use App\Notifications\UserApprovalRequestNotification;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with(['roles', 'companies']);
        
        // Handle individual field searches
        if ($request->filled('search_name')) {
            $query->where('name', 'like', '%' . $request->get('search_name') . '%');
        }
        
        if ($request->filled('search_surname')) {
            $query->where('surname', 'like', '%' . $request->get('search_surname') . '%');
        }
        
        if ($request->filled('search_email')) {
            $query->where('email', 'like', '%' . $request->get('search_email') . '%');
        }
        
        if ($request->filled('search_phone')) {
            $query->where(function($q) use ($request) {
                $q->where('phone', 'like', '%' . $request->get('search_phone') . '%')
                  ->orWhere('mobile', 'like', '%' . $request->get('search_phone') . '%');
            });
        }
        
        if ($request->filled('search_cf')) {
            $query->where('cf', 'like', '%' . $request->get('search_cf') . '%');
        }

        if ($request->filled('search_place_of_birth')) {
            $query->where('place_of_birth', 'like', '%' . $request->get('search_place_of_birth') . '%');
        }
        
        // Handle advanced search options
        if ($request->filled('search_gender')) {
            $query->where('gender', $request->get('search_gender'));
        }
        
        if ($request->filled('search_status')) {
            $query->where('status', $request->get('search_status'));
        }

        // Handle status filter for dashboard links
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        
        if ($request->filled('search_company')) {
            $query->whereHas('companies', function($q) use ($request) {
                $q->where('companies.id', $request->get('search_company'));
            });
        }
        
        // Handle date range search
        if ($request->filled('search_date_from')) {
            $query->whereDate('created_at', '>=', $request->get('search_date_from'));
        }
        
        if ($request->filled('search_date_to')) {
            $query->whereDate('created_at', '<=', $request->get('search_date_to'));
        }
        
        // Handle per page
        $perPage = $request->get('per_page', 10);
        if (!in_array($perPage, [5, 10, 25, 50, 100])) {
            $perPage = 10;
        }
        
        // Handle sorting
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        
        if (!in_array($sortField, ['name', 'surname', 'username', 'email', 'phone', 'mobile', 'cf', 'date_of_birth', 'status', 'created_at'])) {
            $sortField = 'name';
        }
        
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }
        
        $users = $query->orderBy($sortField, $sortDirection)
                      ->paginate($perPage)
                      ->withQueryString();
        
        // Get companies for filter dropdown
        $companies = Company::active()->orderBy('name')->get();
        
        return view('users.index', compact('users', 'companies'));
    }

    /**
     * Download the blank bulk user template.
     */
    public function downloadTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Italian translations for headers
        $italianHeaders = [
            'name' => 'NOME',
            'surname' => 'COGNOME',
            'email' => 'EMAIL',
            'password' => 'PASSWORD',
            'status' => 'STATO',
            'role' => 'RUOLO',
            'companies' => 'AZIENDE',
            'company_percentages' => 'PERCENTUALI AZIENDE',
            'primary_company' => 'AZIENDA PRINCIPALE',
            'date_of_birth' => 'DATA DI NASCITA',
            'place_of_birth' => 'LUOGO DI NASCITA',
            'country' => 'PAESE',
            'phone' => 'TELEFONO',
            'mobile' => 'CELLULARE',
            'gender' => 'GENERE',
            'cf' => 'CODICE FISCALE',
            'address' => 'INDIRIZZO',
        ];

        foreach (UserImportService::TEMPLATE_HEADERS as $index => $header) {
            $column = $index + 1;
            $label = $italianHeaders[$header] ?? strtoupper(str_replace('_', ' ', $header));

            $sheet->setCellValueByColumnAndRow($column, 1, $label);
            $sheet->getColumnDimensionByColumn($column)->setAutoSize(true);
        }

        $sampleRow = UserImportService::sampleRow();
        foreach ($sampleRow as $index => $value) {
            $sheet->setCellValueByColumnAndRow($index + 1, 2, $value);
        }

        $sheet->getStyle('1:1')->getFont()->setBold(true);
        $sheet->freezePane('A2');

        $fileName = 'modello_importazione_utenti_' . now()->format('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(
            static function () use ($writer) {
                $writer->save('php://output');
            },
            $fileName,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );
    }

    /**
     * Display the bulk upload form.
     */
    public function showBulkUploadForm()
    {
        return view('users.bulk-upload', [
            'templateHeaders' => UserImportService::TEMPLATE_HEADERS,
        ]);
    }

    /**
     * Handle bulk user import.
     */
    public function bulkUpload(BulkUserUploadRequest $request, UserImportService $importService)
    {
        $result = $importService->import($request->file('file'), $request->user());

        $redirect = redirect()
            ->route('users.bulk-upload.form')
            ->with('importSummary', $result);

        if ($result['success_count'] > 0) {
            $redirect->with(
                'success',
                trans_choice('users.bulk_upload_success_count', $result['success_count'], ['count' => $result['success_count']])
            );
        }

        $errorCount = count($result['errors']);
        if ($errorCount > 0) {
            $redirect->with(
                'warning',
                trans_choice('users.bulk_upload_error_count', $errorCount, ['count' => $errorCount])
            );
        }

        return $redirect;
    }

    /**
     * Bulk update status for selected users.
     */
    public function bulkUpdateStatus(BulkUserStatusRequest $request)
    {
        $status = $request->input('status');
        $userIds = $request->input('user_ids');

        $users = User::whereIn('id', $userIds)->get();
        $updatedCount = 0;
        $skipped = [];

        foreach ($users as $user) {
            $previousStatus = $user->status;

            if ($previousStatus === $status) {
                $skipped[] = $user->email;
                continue;
            }

            $user->forceFill(['status' => $status])->saveQuietly();
            $updatedCount++;

            AuditLogService::logCustom(
                'user_status_changed',
                __('users.bulk_status_audit_log', [
                    'name' => $user->full_name,
                    'old' => $previousStatus,
                    'new' => $status,
                ]),
                'users',
                'info',
                [
                    'user_id' => $user->id,
                    'old_status' => $previousStatus,
                    'new_status' => $status,
                    'updated_by' => $request->user()->id,
                ]
            );
        }

        if ($updatedCount > 0) {
            session()->flash(
                'success',
                trans_choice('users.bulk_status_success', $updatedCount, [
                    'count' => $updatedCount,
                    'status' => __('users.' . $status),
                ])
            );
        }

        if (!empty($skipped)) {
            session()->flash(
                'warning',
                __('users.bulk_status_skipped', ['emails' => implode(', ', $skipped)])
            );
        }

        return redirect()->route('users.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        $companies = Company::active()->orderBy('name')->get();
        return view('users.create', compact('roles', 'companies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        try {
            
            $validated = $request->validated();

            $userData = [
                'name' => $validated['name'],
                'surname' => $validated['surname'] ?? null,
                'username' => $validated['username'],
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'place_of_birth' => $validated['place_of_birth'] ?? null,
                'country' => $validated['country'] ?? 'IT',
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'mobile' => $validated['mobile'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'cf' => $validated['cf'] ?? null,
                'address' => $validated['address'] ?? null,
                'status' => $validated['status'] ?? 'parked',
                'password' => Hash::make($validated['password']),
            ];

            // Handle photo upload
            if (isset($validated['photo'])) {
                // Use native PHP file operations to avoid fileinfo dependency  
                $photoFile = $validated['photo'];
                $fileExtension = strtolower($photoFile->getClientOriginalExtension());
                $fileName = time() . '_' . uniqid() . '.' . $fileExtension;
                $destinationPath = storage_path('app/public/user-photos');
                
                // Create directory if it doesn't exist
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                
                // Move uploaded file to destination
                if (move_uploaded_file($photoFile->getPathname(), $destinationPath . '/' . $fileName)) {
                    $userData['photo'] = 'user-photos/' . $fileName;
                } else {
                    return redirect()->back()
                        ->withErrors(['photo' => 'Failed to upload photo file.'])
                        ->withInput();
                }
            }

            $user = User::create($userData);

            // Assign roles
            if (isset($validated['roles'])) {
                $user->assignRole($validated['roles']);

                // Log role assignment
                AuditLogService::logCustom(
                    'roles_assigned',
                    "Assigned roles to user {$user->full_name}: " . implode(', ', (array)$validated['roles']),
                    'users',
                    'info',
                    [
                        'user_id' => $user->id,
                        'roles' => $validated['roles'],
                        'assigned_by' => auth()->id()
                    ]
                );
            }

            // Assign companies with percentages
            if (isset($validated['companies'])) {
                $companyData = [];
                $percentages = $validated['company_percentages'] ?? [];
                
                foreach ($validated['companies'] as $companyId) {
                    $percentage = isset($percentages[$companyId]) ? (float) $percentages[$companyId] : 0;
                    $companyData[$companyId] = [
                        'is_primary' => isset($validated['primary_company']) && $validated['primary_company'] == $companyId,
                        'percentage' => $percentage,
                        'joined_at' => now(),
                    ];
                }
                $user->companies()->attach($companyData);
            }

            $statusMessage = $user->status === 'parked' 
                ? "User '{$user->full_name}' has been created and is pending approval." 
                : "User '{$user->full_name}' has been created successfully.";
                
            return redirect()->route('users.index')
                ->with('success', $statusMessage);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create user: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['roles', 'companies']);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $user->load(['roles', 'companies']);
        $roles = Role::all();
        $companies = Company::active()->orderBy('name')->get();
        
        // Prepare existing companies data for JavaScript
        $existingCompanies = $user->companies->map(function($company) {
            return [
                'id' => $company->id,
                'name' => $company->name,
                'email' => $company->email,
                'percentage' => $company->pivot->percentage,
                'is_primary' => $company->pivot->is_primary
            ];
        })->values(); // Use values() to ensure proper array indexing
        
        return view('users.edit', compact('user', 'roles', 'companies', 'existingCompanies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $validated = $request->validated();

            $userData = [
                'name' => $validated['name'],
                'surname' => $validated['surname'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'place_of_birth' => $validated['place_of_birth'] ?? null,
                'country' => $validated['country'] ?? 'IT',
                'email' => $validated['email'],
                'username' => $validated['username'],
                'phone' => $validated['phone'] ?? null,
                'mobile' => $validated['mobile'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'cf' => $validated['cf'] ?? null,
                'address' => $validated['address'] ?? null,
                'status' => $validated['status'] ?? 'parked',
            ];

            // Handle photo upload
            if (isset($validated['photo'])) {
                // Delete old photo using native PHP
                if ($user->photo) {
                    $oldPhotoPath = storage_path('app/public/' . $user->photo);
                    if (file_exists($oldPhotoPath)) {
                        unlink($oldPhotoPath);
                    }
                }
                
                // Use native PHP file operations to avoid fileinfo dependency  
                $photoFile = $validated['photo'];
                $fileExtension = strtolower($photoFile->getClientOriginalExtension());
                $fileName = time() . '_' . uniqid() . '.' . $fileExtension;
                $destinationPath = storage_path('app/public/user-photos');
                
                // Create directory if it doesn't exist
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                
                // Move uploaded file to destination
                if (move_uploaded_file($photoFile->getPathname(), $destinationPath . '/' . $fileName)) {
                    $userData['photo'] = 'user-photos/' . $fileName;
                } else {
                    return redirect()->back()
                        ->withErrors(['photo' => 'Failed to upload photo file.'])
                        ->withInput();
                }
            }

            $user->update($userData);

            if (isset($validated['password']) && !empty($validated['password'])) {
                $user->update(['password' => Hash::make($validated['password'])]);
            }

            // Update roles
            $oldRoles = $user->getRoleNames()->toArray();
            $newRoles = $validated['roles'] ?? [];
            $user->syncRoles($newRoles);

            // Log role changes if any
            if ($oldRoles != $newRoles) {
                AuditLogService::logCustom(
                    'roles_updated',
                    "Updated roles for user {$user->full_name}. Old: [" . implode(', ', $oldRoles) . "], New: [" . implode(', ', $newRoles) . "]",
                    'users',
                    'info',
                    [
                        'user_id' => $user->id,
                        'old_roles' => $oldRoles,
                        'new_roles' => $newRoles,
                        'updated_by' => auth()->id()
                    ]
                );
            }

            // Update companies with percentages
            if (isset($validated['companies'])) {
                $companyData = [];
                $percentages = $validated['company_percentages'] ?? [];
                
                foreach ($validated['companies'] as $companyId) {
                    $percentage = isset($percentages[$companyId]) ? (float) $percentages[$companyId] : 0;
                    $companyData[$companyId] = [
                        'is_primary' => isset($validated['primary_company']) && $validated['primary_company'] == $companyId,
                        'percentage' => $percentage,
                        'joined_at' => now(),
                    ];
                }
                $user->companies()->sync($companyData);
            } else {
                $user->companies()->detach();
            }

            return redirect()->route('users.index')
                ->with('success', "User '{$user->full_name}' has been updated successfully.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update user: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            if ($user->id === auth()->id()) {
                return redirect()->route('users.index')
                    ->with('error', 'You cannot delete your own account.');
            }

            $userName = $user->full_name;
            
            // Delete photo file using native PHP
            if ($user->photo) {
                $photoPath = storage_path('app/public/' . $user->photo);
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }
            }

            // Log user deletion (before actual deletion)
            AuditLogService::logCustom(
                'user_deleted',
                "User {$userName} (ID: {$user->id}, Email: {$user->email}) was deleted",
                'users',
                'warning',
                [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'user_name' => $userName,
                    'deleted_by' => auth()->id(),
                    'had_roles' => $user->getRoleNames()->toArray(),
                    'had_companies' => $user->companies->pluck('name')->toArray()
                ]
            );

            // Detach companies
            $user->companies()->detach();

            $user->delete();

            return redirect()->route('users.index')
                ->with('success', "User '{$userName}' has been deleted successfully.");

        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * Show company users for company managers
     */
    public function companyUsers(Request $request)
    {
        $user = auth()->user();
        $userCompanies = $user->companies;

        // Get all users from the companies this user manages
        $companyUserIds = collect();
        foreach ($userCompanies as $company) {
            $companyUserIds = $companyUserIds->merge($company->users->pluck('id'));
        }

        $query = User::with(['roles', 'companies'])
            ->whereIn('id', $companyUserIds->unique());

        // Apply filters
        if ($request->filled('search_name')) {
            $query->where('name', 'like', '%' . $request->get('search_name') . '%');
        }

        if ($request->filled('search_email')) {
            $query->where('email', 'like', '%' . $request->get('search_email') . '%');
        }

        if ($request->filled('company')) {
            $query->whereHas('companies', function($q) use ($request) {
                $q->where('companies.id', $request->get('company'));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Handle per page validation
        $perPage = $request->get('per_page', 10);
        if (!in_array($perPage, [5, 10, 25, 50, 100])) {
            $perPage = 10;
        }

        $users = $query->paginate($perPage)
                      ->withQueryString();

        return view('company-users.index', compact('users', 'userCompanies'));
    }

    /**
     * Show form for creating company user
     */
    public function createCompanyUser()
    {
        $user = auth()->user();
        $companies = $user->companies;
        // Company managers can't assign STA manager role or teacher role
        $roles = Role::whereNotIn('name', ['sta_manager', 'teacher'])->get();

        return view('company-users.create', compact('companies', 'roles'));
    }

    /**
     * Store company user
     */
    public function storeCompanyUser(StoreUserRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'surname' => $request->surname,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'place_of_birth' => $request->place_of_birth,
                'country' => $request->country ?? 'IT', // Default to Italy
                'gender' => $request->gender,
                'cf' => $request->cf,
                'address' => $request->address,
                'status' => 'parked', // Company-created users need approval
                'password' => Hash::make('password123'), // Default password
            ]);

            // Assign role
            $roleToAssign = $request->role ?? 'end_user';
            $user->assignRole($roleToAssign);

            // Log role assignment
            AuditLogService::logCustom(
                'roles_assigned',
                "Assigned role '{$roleToAssign}' to company user {$user->full_name}",
                'users',
                'info',
                [
                    'user_id' => $user->id,
                    'role' => $roleToAssign,
                    'assigned_by' => auth()->id(),
                    'created_via' => 'company_manager'
                ]
            );

            // Attach to selected companies
            if ($request->companies) {
                foreach ($request->companies as $companyId) {
                    $user->companies()->attach($companyId, [
                        'is_primary' => count($request->companies) === 1,
                        'role_in_company' => $request->role_in_company ?? 'Employee',
                        'joined_at' => now(),
                        'percentage' => $request->percentage ?? 0,
                    ]);
                }
            }

            // Log user creation
            AuditLogService::logCustom(
                'user_created_by_company',
                "Company user {$user->full_name} created with parked status",
                'users',
                'info',
                [
                    'user_id' => $user->id,
                    'created_by' => auth()->id(),
                    'status' => 'parked',
                    'requires_approval' => true
                ]
            );

            return redirect()->route('company-users.index')
                ->with('success', __('users.company_user_created_success'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('users.company_user_create_failed', ['message' => $e->getMessage()]))
                ->withInput();
        }
    }

    /**
     * Send selected users for approval
     */
    public function sendForApproval(Request $request)
    {
        try {
            $request->validate([
                'user_ids' => 'required|array|min:1',
                'user_ids.*' => 'exists:users,id'
            ]);

            $userIds = $request->input('user_ids');
            $users = User::whereIn('id', $userIds)
                ->where('status', 'parked')
                ->get();

            if ($users->isEmpty()) {
                return redirect()->back()
                    ->with('warning', __('users.no_pending_users'));
            }

            // Verify these users belong to company manager's companies
            $managerCompanyIds = auth()->user()->companies->pluck('id');
            foreach ($users as $user) {
                $userCompanyIds = $user->companies->pluck('id');
                $hasCommonCompany = $managerCompanyIds->intersect($userCompanyIds)->isNotEmpty();

                if (!$hasCommonCompany) {
                    return redirect()->back()
                        ->with('error', __('users.unauthorized_company_users'));
                }
            }

            // Send notification to all STA Managers
            $staManagers = User::role('sta_manager')->get();

            if ($staManagers->isEmpty()) {
                \Log::warning('No STA Managers found to send approval notifications');
                return redirect()->back()
                    ->with('warning', 'No STA Managers available to process approval requests. Please contact support.');
            }

            // Send both email and database notifications
            \Log::info('Sending approval notifications to STA Managers', [
                'sta_manager_count' => $staManagers->count(),
                'sta_managers' => $staManagers->pluck('email')->toArray(),
                'user_count' => $users->count(),
                'users' => $users->pluck('email')->toArray(),
                'requested_by' => auth()->user()->email
            ]);

            // Change status from 'parked' to 'pending_approval'
            foreach ($users as $user) {
                $user->update(['status' => 'pending_approval']);
            }

            Notification::send($staManagers, new UserApprovalRequestNotification($users, auth()->user()));

            \Log::info('Approval notifications sent successfully');

            // Log the approval request
            AuditLogService::logCustom(
                'users_sent_for_approval',
                count($users) . " user(s) sent for approval by " . auth()->user()->name,
                'users',
                'info',
                [
                    'user_ids' => $users->pluck('id')->toArray(),
                    'user_count' => count($users),
                    'requested_by' => auth()->id(),
                ]
            );

            $message = trans_choice('users.users_sent_for_approval', count($users), ['count' => count($users)]);

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('users.send_for_approval_failed', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Display the specified company user (for company managers)
     */
    public function showCompanyUser(User $user)
    {
        // Verify the user belongs to one of the company manager's companies
        $managerCompanyIds = auth()->user()->companies->pluck('id');
        $userCompanyIds = $user->companies->pluck('id');

        if ($managerCompanyIds->intersect($userCompanyIds)->isEmpty()) {
            abort(403, 'Unauthorized access to this user.');
        }

        $user->load(['roles', 'companies']);

        // Flag to indicate this is company manager context
        $isCompanyManager = true;

        return view('users.show', compact('user', 'isCompanyManager'));
    }

    /**
     * Show the form for editing company user (for company managers)
     */
    public function editCompanyUser(User $user)
    {
        // Verify the user belongs to one of the company manager's companies
        $managerCompanyIds = auth()->user()->companies->pluck('id');
        $userCompanyIds = $user->companies->pluck('id');

        if ($managerCompanyIds->intersect($userCompanyIds)->isEmpty()) {
            abort(403, 'Unauthorized access to this user.');
        }

        $user->load(['roles', 'companies']);
        $companies = auth()->user()->companies; // Only show manager's companies
        $roles = Role::whereNotIn('name', ['sta_manager', 'teacher'])->get();

        // Prepare existing companies data for JavaScript
        $existingCompanies = $user->companies->map(function($company) {
            return [
                'id' => $company->id,
                'name' => $company->name,
                'email' => $company->email,
                'percentage' => $company->pivot->percentage,
                'is_primary' => $company->pivot->is_primary
            ];
        })->values();

        // Flag to indicate this is company manager context
        $isCompanyManager = true;

        return view('users.edit', compact('user', 'roles', 'companies', 'existingCompanies', 'isCompanyManager'));
    }

    /**
     * Update company user (for company managers)
     */
    public function updateCompanyUser(UpdateUserRequest $request, User $user)
    {
        // Verify the user belongs to one of the company manager's companies
        $managerCompanyIds = auth()->user()->companies->pluck('id');
        $userCompanyIds = $user->companies->pluck('id');

        if ($managerCompanyIds->intersect($userCompanyIds)->isEmpty()) {
            abort(403, 'Unauthorized access to this user.');
        }

        try {
            $validated = $request->validated();

            $userData = [
                'name' => $validated['name'],
                'surname' => $validated['surname'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'place_of_birth' => $validated['place_of_birth'] ?? null,
                'country' => $validated['country'] ?? 'IT',
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'mobile' => $validated['mobile'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'cf' => $validated['cf'] ?? null,
                'address' => $validated['address'] ?? null,
            ];

            $user->update($userData);

            if (isset($validated['password']) && !empty($validated['password'])) {
                $user->update(['password' => Hash::make($validated['password'])]);
            }

            // Update roles (company managers can't assign sta_manager or teacher roles)
            if (isset($validated['roles'])) {
                $allowedRoles = Role::whereNotIn('name', ['sta_manager', 'teacher'])->pluck('name')->toArray();
                $rolesToAssign = array_intersect($validated['roles'], $allowedRoles);
                $user->syncRoles($rolesToAssign);
            }

            // Update companies (only within manager's companies)
            if (isset($validated['companies'])) {
                $companyData = [];
                $percentages = $validated['company_percentages'] ?? [];

                // Filter to only include companies the manager has access to
                $allowedCompanyIds = $managerCompanyIds->toArray();
                $validCompanies = array_intersect($validated['companies'], $allowedCompanyIds);

                foreach ($validCompanies as $companyId) {
                    $percentage = isset($percentages[$companyId]) ? (float) $percentages[$companyId] : 0;
                    $companyData[$companyId] = [
                        'is_primary' => isset($validated['primary_company']) && $validated['primary_company'] == $companyId,
                        'percentage' => $percentage,
                        'joined_at' => $user->companies->where('id', $companyId)->first()->pivot->joined_at ?? now(),
                    ];
                }
                $user->companies()->sync($companyData);
            }

            return redirect()->route('company-users.show', $user)
                ->with('success', __('users.user_updated'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => __('users.update_failed') . ': ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Cancel approval request - revert status from pending_approval to parked (for company managers)
     */
    public function cancelApprovalRequest(User $user)
    {
        try {
            // Verify the user belongs to one of the company manager's companies
            $managerCompanyIds = auth()->user()->companies->pluck('id');
            $userCompanyIds = $user->companies->pluck('id');

            if ($managerCompanyIds->intersect($userCompanyIds)->isEmpty()) {
                abort(403, 'Unauthorized access to this user.');
            }

            // Only allow cancellation of pending_approval users
            if ($user->status !== 'pending_approval') {
                return redirect()->back()
                    ->with('error', __('users.can_only_cancel_pending'));
            }

            $userName = $user->full_name;

            // Revert status back to parked
            $user->update(['status' => 'parked']);

            // Log the cancellation
            AuditLogService::logCustom(
                'approval_request_cancelled',
                "Approval request cancelled for user {$userName} by " . auth()->user()->name,
                'users',
                'info',
                [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'user_name' => $userName,
                    'cancelled_by' => auth()->id(),
                    'reverted_to_status' => 'parked'
                ]
            );

            return redirect()->route('company-users.index')
                ->with('success', __('users.approval_request_cancelled', ['name' => $userName]));

        } catch (\Exception $e) {
            return redirect()->route('company-users.index')
                ->with('error', __('users.cancel_failed', ['message' => $e->getMessage()]));
        }
    }
}
