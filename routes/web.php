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
    Route::resource('users', UserController::class);

    // User Approvals
    Route::get('/users/pending/approvals', [STAManagerDashboardController::class, 'pendingApprovals'])->name('users.pending.approvals');
    Route::post('/users/{user}/approve', [STAManagerDashboardController::class, 'approveUser'])->name('users.approve');
    Route::post('/users/{user}/reject', [STAManagerDashboardController::class, 'rejectUser'])->name('users.reject');

    // System Reports
    Route::get('/system/reports', [STAManagerDashboardController::class, 'systemReports'])->name('system.reports');

    // Role Management Routes
    Route::resource('roles', RoleController::class);

    // Company Management Routes
    Route::resource('companies', CompanyController::class);

    // Settings Management Routes (STA Manager only)
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'store'])->name('settings.store');
});

// Company Manager Routes
Route::middleware(['auth', 'role:company_manager'])->group(function () {
    // Limited user management (only company users)
    Route::get('company-users', [UserController::class, 'companyUsers'])->name('company-users.index');
    Route::get('company-users/create', [UserController::class, 'createCompanyUser'])->name('company-users.create');
    Route::post('company-users', [UserController::class, 'storeCompanyUser'])->name('company-users.store');

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

require __DIR__.'/auth.php';
