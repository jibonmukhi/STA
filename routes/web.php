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
        ->middleware(['can:view personal reports', 'role:end_user'])
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

    // Data Vault API (available to authenticated users)
    Route::get('/api/data-vault/{categoryCode}', function($categoryCode) {
        return response()->json(dataVaultItems($categoryCode));
    })->name('api.data-vault');

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

    // Course Management Routes - Master Courses (Templates)
    Route::resource('courses', CoursesController::class);
    Route::get('/courses/planning/overview', [CoursesController::class, 'planning'])->name('courses.planning');
    Route::get('/courses/{course}/schedule', [CoursesController::class, 'schedule'])->name('courses.schedule');

    // Course Management Routes - Course Instances (Started Courses)
    // Define custom routes BEFORE resource route to avoid conflicts
    Route::patch('/course-management/{courseManagement}/update-status', [\App\Http\Controllers\CourseManagementController::class, 'updateStatus'])->name('course-management.update-status');
    Route::post('/course-management/{courseManagement}/send-notifications', [\App\Http\Controllers\CourseManagementController::class, 'sendNotifications'])->name('course-management.send-notifications');
    Route::get('/course-management/{courseManagement}/bulk-invite', [\App\Http\Controllers\CourseManagementController::class, 'showBulkInvite'])->name('course-management.bulk-invite');
    Route::post('/course-management/{courseManagement}/bulk-invite', [\App\Http\Controllers\CourseManagementController::class, 'sendBulkInvite'])->name('course-management.send-bulk-invite');
    Route::resource('course-management', \App\Http\Controllers\CourseManagementController::class);

    // Course Materials Routes
    Route::get('/courses/{course}/materials/create', [\App\Http\Controllers\CourseMaterialController::class, 'create'])->name('course-materials.create');
    Route::post('/courses/{course}/materials', [\App\Http\Controllers\CourseMaterialController::class, 'store'])->name('course-materials.store');
    Route::get('/course-materials/{material}/download', [\App\Http\Controllers\CourseMaterialController::class, 'download'])->name('course-materials.download');
    Route::delete('/course-materials/{material}', [\App\Http\Controllers\CourseMaterialController::class, 'destroy'])->name('course-materials.destroy');

    // Course Enrollments Routes
    Route::get('/courses/{course}/enrollments', [\App\Http\Controllers\CourseEnrollmentController::class, 'index'])->name('courses.enrollments.index');
    Route::get('/courses/{course}/enrollments/create', [\App\Http\Controllers\CourseEnrollmentController::class, 'create'])->name('courses.enrollments.create');
    Route::post('/courses/{course}/enrollments', [\App\Http\Controllers\CourseEnrollmentController::class, 'store'])->name('courses.enrollments.store');
    Route::get('/enrollments/{enrollment}/edit', [\App\Http\Controllers\CourseEnrollmentController::class, 'edit'])->name('enrollments.edit');
    Route::put('/enrollments/{enrollment}', [\App\Http\Controllers\CourseEnrollmentController::class, 'update'])->name('enrollments.update');
    Route::post('/enrollments/{enrollment}/update-progress', [\App\Http\Controllers\CourseEnrollmentController::class, 'updateProgress'])->name('enrollments.update-progress');
    Route::delete('/enrollments/{enrollment}', [\App\Http\Controllers\CourseEnrollmentController::class, 'destroy'])->name('enrollments.destroy');

    // Course Events Routes
    Route::get('/courses/{course}/events', [\App\Http\Controllers\CourseEventController::class, 'index'])->name('courses.events.index');
    Route::get('/courses/{course}/events/create', [\App\Http\Controllers\CourseEventController::class, 'create'])->name('courses.events.create');
    Route::post('/courses/{course}/events', [\App\Http\Controllers\CourseEventController::class, 'store'])->name('courses.events.store');
    Route::get('/events/{event}/edit', [\App\Http\Controllers\CourseEventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [\App\Http\Controllers\CourseEventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [\App\Http\Controllers\CourseEventController::class, 'destroy'])->name('events.destroy');

    // Course-Company Assignment Routes
    Route::post('/course-company-assignments', [\App\Http\Controllers\CourseCompanyAssignmentController::class, 'store'])->name('course-company-assignments.store');
    Route::delete('/course-company-assignments/{assignment}', [\App\Http\Controllers\CourseCompanyAssignmentController::class, 'destroy'])->name('course-company-assignments.destroy');

    // Student-facing Course Routes
    Route::get('/my-courses', [\App\Http\Controllers\CourseEnrollmentController::class, 'myCourses'])->name('my-courses');
    Route::get('/course-catalog', [\App\Http\Controllers\CourseEnrollmentController::class, 'catalog'])->name('course-catalog');
    Route::post('/courses/{course}/enroll', [\App\Http\Controllers\CourseEnrollmentController::class, 'enroll'])->name('courses.enroll');

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

    // Sent Notes Activity Log
    Route::get('/sta-manager/sent-notes', [STAManagerDashboardController::class, 'sentNotes'])->name('sta-manager.sent-notes');

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

    // Company Note Routes
    Route::post('/companies/{company}/send-note', [CompanyController::class, 'sendNote'])->name('companies.send-note');

    // Debug Routes (STA Manager only) - REMOVE IN PRODUCTION AFTER FIXING
    Route::get('/debug/notifications', [\App\Http\Controllers\DebugNotificationController::class, 'index'])->name('debug.notifications');
    Route::post('/debug/test-notification', [\App\Http\Controllers\DebugNotificationController::class, 'testNotification'])->name('debug.test-notification');

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
    // Profile Management
    Route::get('/company-manager/profile', [\App\Http\Controllers\CompanyManagerController::class, 'profile'])->name('company-manager.profile');
    Route::put('/company-manager/profile', [\App\Http\Controllers\CompanyManagerController::class, 'updateProfile'])->name('company-manager.profile.update');
    Route::put('/company-manager/password', [\App\Http\Controllers\CompanyManagerController::class, 'updatePassword'])->name('company-manager.password.update');

    // Audit Logs
    Route::get('/company-manager/audit-logs', [\App\Http\Controllers\CompanyManagerController::class, 'auditLogs'])->name('company-manager.audit-logs');

    // Bulk User Import
    Route::get('/company-manager/bulk-import', [\App\Http\Controllers\CompanyManagerController::class, 'bulkImportForm'])->name('company-manager.bulk-import');
    Route::post('/company-manager/bulk-import', [\App\Http\Controllers\CompanyManagerController::class, 'bulkImport'])->name('company-manager.bulk-import.process');
    Route::get('/company-manager/template/download', [\App\Http\Controllers\CompanyManagerController::class, 'downloadTemplate'])->name('company-manager.template.download');

    // Profile Change Requests
    Route::get('/company-manager/profile-change-requests', [\App\Http\Controllers\CompanyManagerController::class, 'profileChangeRequests'])->name('company-manager.profile-change-requests');
    Route::get('/company-manager/profile-change-requests/{request}', [\App\Http\Controllers\CompanyManagerController::class, 'showProfileChangeRequest'])->name('company-manager.profile-change-requests.show');
    Route::post('/company-manager/profile-change-requests/{request}/approve', [\App\Http\Controllers\CompanyManagerController::class, 'approveProfileChangeRequest'])->name('company-manager.profile-change-requests.approve');
    Route::post('/company-manager/profile-change-requests/{request}/reject', [\App\Http\Controllers\CompanyManagerController::class, 'rejectProfileChangeRequest'])->name('company-manager.profile-change-requests.reject');

    // Limited user management (only company users)
    Route::get('company-users', [UserController::class, 'companyUsers'])->name('company-users.index');
    Route::get('company-users/create', [UserController::class, 'createCompanyUser'])->name('company-users.create');
    Route::post('company-users', [UserController::class, 'storeCompanyUser'])->name('company-users.store');
    Route::get('company-users/{user}', [UserController::class, 'showCompanyUser'])->name('company-users.show');
    Route::get('company-users/{user}/edit', [UserController::class, 'editCompanyUser'])->name('company-users.edit');
    Route::put('company-users/{user}', [UserController::class, 'updateCompanyUser'])->name('company-users.update');
    Route::delete('company-users/{user}', [UserController::class, 'cancelApprovalRequest'])->name('company-users.cancel');
    Route::post('company-users/send-for-approval', [UserController::class, 'sendForApproval'])->name('company-users.send-for-approval');

    // Company profile management
    Route::get('my-companies', [CompanyController::class, 'myCompanies'])->name('my-companies.index');
});

// Teacher Routes
Route::middleware(['auth', 'role:teacher'])->group(function () {
    // Teacher's courses management
    Route::get('/teacher/my-courses', [\App\Http\Controllers\TeacherDashboardController::class, 'myCourses'])->name('teacher.my-courses');
    Route::get('/teacher/courses/{course}', [\App\Http\Controllers\TeacherDashboardController::class, 'showCourse'])->name('teacher.course-details');
    Route::get('/teacher/courses/{course}/students', [\App\Http\Controllers\TeacherDashboardController::class, 'courseStudents'])->name('teacher.course-students');

    // Teacher's schedule
    Route::get('/teacher/schedule', [\App\Http\Controllers\TeacherDashboardController::class, 'schedule'])->name('teacher.schedule');

    // Teacher's certificates
    Route::get('/teacher/certificates', [\App\Http\Controllers\TeacherDashboardController::class, 'certificates'])->name('teacher.certificates');

    // Session Attendance Management
    Route::get('/teacher/courses/{course}/sessions/attendance', [\App\Http\Controllers\TeacherDashboardController::class, 'sessionAttendance'])->name('teacher.session-attendance');
    Route::get('/teacher/sessions/{session}/attendance', [\App\Http\Controllers\TeacherDashboardController::class, 'showSessionAttendance'])->name('teacher.session-attendance-detail');
    Route::post('/teacher/sessions/{session}/attendance', [\App\Http\Controllers\TeacherDashboardController::class, 'markAttendance'])->name('teacher.mark-attendance');
    Route::post('/teacher/sessions/{session}/attendance/bulk', [\App\Http\Controllers\TeacherDashboardController::class, 'bulkMarkAttendance'])->name('teacher.bulk-mark-attendance');
    Route::post('/teacher/sessions/{session}/close', [\App\Http\Controllers\TeacherDashboardController::class, 'closeSession'])->name('teacher.close-session');
});

// Public certificate verification (no auth required)
Route::get('/verify/{verificationCode}', [CertificateController::class, 'verify'])->name('certificates.verify');

// Company Invitation Acceptance Routes (Public - no auth required)
Route::get('/invitation/accept/{token}', [CompanyController::class, 'showAcceptInvitation'])->name('invitation.accept');
Route::post('/invitation/accept/{token}', [CompanyController::class, 'acceptInvitation'])->name('invitation.accept.process');

require __DIR__.'/auth.php';
