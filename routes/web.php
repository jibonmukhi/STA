<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\STAManagerDashboardController;
use App\Http\Controllers\CompanyManagerDashboardController;
use App\Http\Controllers\EndUserDashboardController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


// Legacy dashboard route (fallback)
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Role-based dashboard routes
Route::middleware(['auth', 'verified'])->group(function () {
    // STA Manager Dashboard
    Route::get('/sta/dashboard', [STAManagerDashboardController::class, 'index'])
        ->middleware('role:sta_manager')
        ->name('sta.dashboard');

    // Company Manager Dashboard
    Route::get('/company/dashboard', [CompanyManagerDashboardController::class, 'index'])
        ->middleware('role:company_manager')
        ->name('company.dashboard');

    // Teacher Dashboard
    Route::get('/teacher/dashboard', [\App\Http\Controllers\TeacherDashboardController::class, 'index'])
        ->middleware('role:teacher')
        ->name('teacher.dashboard');

    // End User Dashboard
    Route::get('/user/dashboard', [EndUserDashboardController::class, 'index'])
        ->middleware('role:end_user')
        ->name('user.dashboard');

    // Common Pages (accessible by all authenticated users with permission check)
    Route::get('/certificate', [EndUserDashboardController::class, 'certificate'])
        ->middleware('can:view personal reports')
        ->name('certificate');

    Route::get('/calendar', [EndUserDashboardController::class, 'calendar'])
        ->middleware('can:view personal reports')
        ->name('calendar');

    Route::get('/reports', [EndUserDashboardController::class, 'reports'])
        ->middleware('can:view personal reports')
        ->name('reports');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Language switching (available to all authenticated users)
    Route::post('/language/switch', [SettingsController::class, 'switchLanguage'])->name('language.switch');

    // Public settings API
    Route::get('/api/settings/public', [SettingsController::class, 'getPublicSettings'])->name('settings.public');

    // Notification Routes (available to all authenticated users)
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/recent', [NotificationController::class, 'getRecent'])->name('recent');
        Route::get('/count', [NotificationController::class, 'getUnreadCount'])->name('count');
        Route::post('/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
    });

    // Certificate Management Routes (accessible to all authenticated users with role-based filtering)
    Route::resource('certificates', CertificateController::class);
    Route::get('/certificates/{certificate}/download/{type?}', [CertificateController::class, 'download'])->name('certificates.download');

    // Course Management Routes (accessible to all authenticated users with role-based filtering)
    Route::resource('courses', CoursesController::class);
    Route::get('/courses/planning/overview', [CoursesController::class, 'planning'])->name('courses.planning');
    Route::get('/courses/{course}/schedule', [CoursesController::class, 'schedule'])->name('courses.schedule');

});

// STA Manager Routes (Super Admin)
Route::middleware(['auth', 'role:sta_manager'])->group(function () {
    // User Management Routes
    Route::get('users/template/download', [UserController::class, 'downloadTemplate'])->name('users.template.download');
    Route::get('users/bulk-upload', [UserController::class, 'showBulkUploadForm'])->name('users.bulk-upload.form');
    Route::post('users/bulk-upload', [UserController::class, 'bulkUpload'])->name('users.bulk-upload.store');
    Route::post('users/bulk-status', [UserController::class, 'bulkUpdateStatus'])->name('users.bulk-status');
    Route::resource('users', UserController::class);

    // User Approvals
    Route::get('/users/pending/approvals', [STAManagerDashboardController::class, 'pendingApprovals'])->name('users.pending.approvals');
    Route::post('/users/{user}/approve', [STAManagerDashboardController::class, 'approveUser'])->name('users.approve');
    Route::post('/users/{user}/reject', [STAManagerDashboardController::class, 'rejectUser'])->name('users.reject');
    Route::post('/users/bulk-approve', [STAManagerDashboardController::class, 'bulkApproveUsers'])->name('users.bulk-approve');
    Route::post('/users/bulk-reject', [STAManagerDashboardController::class, 'bulkRejectUsers'])->name('users.bulk-reject');

    // System Reports
    Route::get('/system/reports', [STAManagerDashboardController::class, 'systemReports'])->name('system.reports');

    // Role Management Routes
    Route::resource('roles', RoleController::class);

    // Company Invitation Routes (Must be BEFORE resource route to avoid conflicts)
    Route::get('/companies/invitations', [CompanyController::class, 'invitationsList'])->name('companies.invitations.index');
    Route::get('/companies/invitations/{id}/details', [CompanyController::class, 'showInvitationDetails'])->name('companies.invitations.details');
    Route::get('/companies/invite/form', [CompanyController::class, 'showInviteForm'])->name('companies.invite.form');
    Route::post('/companies/invite/send', [CompanyController::class, 'sendInvite'])->name('companies.invite.send');
    Route::post('/companies/invitations/{id}/resend', [CompanyController::class, 'resendInvitation'])->name('companies.invitations.resend');
    Route::delete('/companies/invitations/{id}', [CompanyController::class, 'destroyInvitation'])->name('companies.invitations.destroy');

    // Company Management Routes
    Route::resource('companies', CompanyController::class);

    // Settings Management Routes (STA Manager only)
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'store'])->name('settings.store');

    // Data Vault Management Routes (STA Manager only)
    Route::prefix('data-vault')->name('data-vault.')->group(function () {
        Route::get('/', [\App\Http\Controllers\DataVaultController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\DataVaultController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\DataVaultController::class, 'store'])->name('store');
        Route::get('/{category}/edit', [\App\Http\Controllers\DataVaultController::class, 'edit'])->name('edit');
        Route::put('/{category}', [\App\Http\Controllers\DataVaultController::class, 'update'])->name('update');
        Route::delete('/{category}', [\App\Http\Controllers\DataVaultController::class, 'destroy'])->name('destroy');

        // Items management
        Route::get('/{category}/items', [\App\Http\Controllers\DataVaultItemController::class, 'index'])->name('items.index');
        Route::post('/{category}/items', [\App\Http\Controllers\DataVaultItemController::class, 'store'])->name('items.store');
        Route::put('/{category}/items/{item}', [\App\Http\Controllers\DataVaultItemController::class, 'update'])->name('items.update');
        Route::delete('/{category}/items/{item}', [\App\Http\Controllers\DataVaultItemController::class, 'destroy'])->name('items.destroy');
        Route::post('/{category}/items/reorder', [\App\Http\Controllers\DataVaultItemController::class, 'reorder'])->name('items.reorder');
    });

    // Audit Log Management Routes (STA Manager only)
    Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
        Route::get('/', [AuditLogController::class, 'index'])->name('index');
        Route::get('/export', [AuditLogController::class, 'export'])->name('export');
        Route::get('/statistics', [AuditLogController::class, 'statistics'])->name('statistics');
        Route::get('/cleanup', [AuditLogController::class, 'cleanup'])->name('cleanup');
        Route::post('/cleanup', [AuditLogController::class, 'cleanup'])->name('cleanup.perform');
        Route::get('/{auditLog}', [AuditLogController::class, 'show'])->name('show');
    });
});

// Company Manager Routes
Route::middleware(['auth', 'role:company_manager'])->group(function () {
    // Limited user management (only company users)
    Route::get('company-users', [UserController::class, 'companyUsers'])->name('company-users.index');
    Route::get('company-users/create', [UserController::class, 'createCompanyUser'])->name('company-users.create');
    Route::post('company-users', [UserController::class, 'storeCompanyUser'])->name('company-users.store');
    Route::post('company-users/send-for-approval', [UserController::class, 'sendForApproval'])->name('company-users.send-for-approval');

    // Company profile management
    Route::get('my-companies', [CompanyController::class, 'myCompanies'])->name('my-companies.index');
});

// Teacher Routes
Route::middleware(['auth', 'role:teacher'])->group(function () {
    // Teacher's courses management
    Route::get('/teacher/my-courses', [\App\Http\Controllers\TeacherDashboardController::class, 'myCourses'])->name('teacher.my-courses');
    Route::get('/teacher/courses/{course}/students', [\App\Http\Controllers\TeacherDashboardController::class, 'courseStudents'])->name('teacher.course-students');

    // Teacher's schedule
    Route::get('/teacher/schedule', [\App\Http\Controllers\TeacherDashboardController::class, 'schedule'])->name('teacher.schedule');

    // Teacher's certificates
    Route::get('/teacher/certificates', [\App\Http\Controllers\TeacherDashboardController::class, 'certificates'])->name('teacher.certificates');
});

// Public certificate verification (no auth required)
Route::get('/verify/{verificationCode}', [CertificateController::class, 'verify'])->name('certificates.verify');

// Company Invitation Acceptance Routes (Public - no auth required)
Route::get('/invitation/accept/{token}', [CompanyController::class, 'showAcceptInvitation'])->name('invitation.accept');
Route::post('/invitation/accept/{token}', [CompanyController::class, 'acceptInvitation'])->name('invitation.accept.process');

require __DIR__.'/auth.php';
