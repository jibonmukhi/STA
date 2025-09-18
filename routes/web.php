<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\STAManagerDashboardController;
use App\Http\Controllers\CompanyManagerDashboardController;
use App\Http\Controllers\EndUserDashboardController;
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

    // End User Dashboard
    Route::get('/user/dashboard', [EndUserDashboardController::class, 'index'])
        ->middleware('role:end_user')
        ->name('user.dashboard');

    // End User Certificate (accessible by end_user and company_manager)
    Route::get('/user/certificate', [EndUserDashboardController::class, 'certificate'])
        ->middleware('role:end_user,company_manager')
        ->name('user.certificate');

    // End User Calendar (accessible by end_user and company_manager)
    Route::get('/user/calendar', [EndUserDashboardController::class, 'calendar'])
        ->middleware('role:end_user,company_manager')
        ->name('user.calendar');

    // End User Reports (accessible by end_user and company_manager)
    Route::get('/user/reports', [EndUserDashboardController::class, 'reports'])
        ->middleware('role:end_user,company_manager')
        ->name('user.reports');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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

require __DIR__.'/auth.php';
