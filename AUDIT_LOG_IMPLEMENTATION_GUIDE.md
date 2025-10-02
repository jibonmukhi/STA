# Audit Log Implementation Guide

## 📍 Where to Add Audit Logging

### 1. **Authentication Events**

#### Login/Logout (AuthenticatedSessionController.php)
```php
use App\Services\AuditLogService;

// In the store() method after successful login:
public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();
    $request->session()->regenerate();

    // ADD THIS: Log the login
    AuditLogService::logLogin(Auth::user());

    // ... rest of your code
}

// In the destroy() method for logout:
public function destroy(Request $request): RedirectResponse
{
    // ADD THIS: Log the logout before destroying session
    AuditLogService::logLogout(Auth::user());

    Auth::guard('web')->logout();
    // ... rest of your code
}
```

---

### 2. **User Management Actions**

#### UserController.php - Important Actions
```php
use App\Services\AuditLogService;

// When approving a user
public function approveUser(User $user)
{
    $user->update(['status' => 'active']);

    // ADD THIS: Log the approval
    AuditLogService::logCustom(
        'user_approved',
        "User {$user->name} was approved by " . Auth::user()->name,
        'users',
        'info',
        ['user_id' => $user->id, 'approved_by' => Auth::id()]
    );

    return redirect()->back()->with('success', 'User approved');
}

// When assigning roles
public function assignRole(Request $request, User $user)
{
    $role = $request->input('role');
    $user->assignRole($role);

    // ADD THIS: Log role assignment
    AuditLogService::logRoleAssigned($user, $role);

    return redirect()->back()->with('success', 'Role assigned');
}

// When removing roles
public function removeRole(User $user, $role)
{
    $user->removeRole($role);

    // ADD THIS: Log role removal
    AuditLogService::logRoleRemoved($user, $role);

    return redirect()->back()->with('success', 'Role removed');
}
```

---

### 3. **Company Management**

#### CompanyController.php
```php
// Already automatic if you add the trait to Company model:
use App\Traits\HasAuditLog;

class Company extends Model
{
    use HasAuditLog; // This will automatically log create/update/delete
}

// For special actions:
public function assignUserToCompany(User $user, Company $company)
{
    $user->companies()->attach($company->id);

    // ADD THIS: Log the assignment
    AuditLogService::logCustom(
        'user_assigned_to_company',
        "{$user->name} assigned to company {$company->name}",
        'companies',
        'info',
        [
            'user_id' => $user->id,
            'company_id' => $company->id,
            'assigned_by' => Auth::id()
        ]
    );
}
```

---

### 4. **Certificate Operations**

#### CertificateController.php
```php
// In the model:
class Certificate extends Model
{
    use HasAuditLog; // Automatic logging
}

// For special actions like verification:
public function verify($verificationCode)
{
    $certificate = Certificate::where('verification_code', $verificationCode)->first();

    if ($certificate) {
        // ADD THIS: Log the verification
        AuditLogService::logCustom(
            'certificate_verified',
            "Certificate {$certificate->certificate_number} was verified",
            'certificates',
            'info',
            [
                'certificate_id' => $certificate->id,
                'verified_by_ip' => request()->ip()
            ]
        );
    }

    return view('certificates.verify', compact('certificate'));
}
```

---

### 5. **File Operations**

#### When uploading files:
```php
public function uploadDocument(Request $request)
{
    $file = $request->file('document');
    $path = $file->store('documents');

    // ADD THIS: Log file upload
    AuditLogService::logFileUpload(
        $file->getClientOriginalName(),
        $path,
        $model // optional: related model
    );

    return response()->json(['path' => $path]);
}

// When deleting files:
public function deleteDocument($id)
{
    $document = Document::find($id);
    Storage::delete($document->path);

    // ADD THIS: Log file deletion
    AuditLogService::logFileDelete(
        $document->filename,
        $document // the model being deleted
    );

    $document->delete();
}
```

---

### 6. **Data Export/Import**

#### When exporting data:
```php
public function exportUsers()
{
    $users = User::all();

    // ADD THIS: Log the export
    AuditLogService::logExport('users', $users->count());

    // ... generate and return CSV/Excel
}

// When importing data:
public function importUsers(Request $request)
{
    $file = $request->file('import_file');
    // ... process import

    // ADD THIS: Log the import
    AuditLogService::logImport('users', $importedCount);

    return redirect()->back()->with('success', "Imported {$importedCount} users");
}
```

---

### 7. **Course Enrollment**

#### CourseEnrollment operations:
```php
public function enrollStudent(Course $course, User $student)
{
    $enrollment = CourseEnrollment::create([
        'course_id' => $course->id,
        'user_id' => $student->id,
        'status' => 'enrolled'
    ]);

    // ADD THIS: Log enrollment
    AuditLogService::logCustom(
        'course_enrollment',
        "{$student->name} enrolled in {$course->title}",
        'courses',
        'info',
        [
            'course_id' => $course->id,
            'student_id' => $student->id,
            'enrollment_id' => $enrollment->id
        ]
    );
}
```

---

### 8. **Settings Changes**

#### SettingsController.php
```php
public function update(Request $request)
{
    $oldSettings = Setting::all()->pluck('value', 'key');

    foreach ($request->settings as $key => $value) {
        Setting::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    // ADD THIS: Log settings change
    AuditLogService::logCustom(
        'settings_updated',
        'System settings were updated',
        'settings',
        'warning',
        [
            'old_values' => $oldSettings->toArray(),
            'new_values' => $request->settings,
            'updated_by' => Auth::id()
        ]
    );
}
```

---

## 🔧 Quick Implementation Steps

### Step 1: Add Trait to Models
```php
// In any model you want to track automatically:
use App\Traits\HasAuditLog;

class YourModel extends Model
{
    use HasAuditLog;
}
```

### Step 2: Add Manual Logging for Special Actions
```php
use App\Services\AuditLogService;

// Simple log
AuditLogService::logCustom(
    'action_name',      // What happened
    'Description here', // Human-readable description
    'module_name',      // Module (users, companies, etc.)
    'info'              // Severity: info, warning, error, critical
);

// With additional data
AuditLogService::logCustom(
    'data_export',
    'Exported 500 users to CSV',
    'export',
    'info',
    ['filename' => 'users.csv', 'count' => 500] // metadata
);
```

### Step 3: Common Patterns

#### Pattern 1: Before/After Important Operations
```php
public function changeUserStatus(User $user, $newStatus)
{
    $oldStatus = $user->status;

    $user->update(['status' => $newStatus]);

    AuditLogService::logCustom(
        'status_changed',
        "User status changed from {$oldStatus} to {$newStatus}",
        'users',
        'warning',
        [
            'user_id' => $user->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus
        ]
    );
}
```

#### Pattern 2: Failed Attempts
```php
public function attemptLogin(Request $request)
{
    if (!Auth::attempt($request->only('email', 'password'))) {
        // Log failed login
        AuditLogService::logCustom(
            'login_failed',
            "Failed login attempt for email: {$request->email}",
            'auth',
            'warning',
            [
                'email' => $request->email,
                'ip' => $request->ip()
            ]
        );

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    // Log successful login
    AuditLogService::logLogin(Auth::user());
}
```

#### Pattern 3: Bulk Operations
```php
public function bulkDelete(Request $request)
{
    $ids = $request->input('ids');
    $count = User::whereIn('id', $ids)->count();

    User::whereIn('id', $ids)->delete();

    AuditLogService::logCustom(
        'bulk_delete',
        "Bulk deleted {$count} users",
        'users',
        'warning',
        [
            'deleted_ids' => $ids,
            'count' => $count,
            'deleted_by' => Auth::id()
        ]
    );
}
```

---

## 📊 Viewing Audit Logs

1. Login as **STA Manager**
2. Navigate to **Audit Logs** in the menu
3. You'll see:
   - All user activities
   - System changes
   - Login/logout events
   - File operations
   - Data exports/imports

### Filtering Options:
- By user
- By action type
- By module
- By date range
- By severity level

### Export Options:
- Export filtered results to CSV
- Schedule regular exports
- API endpoint for programmatic access

---

## 🔐 Security Best Practices

1. **Never log sensitive data:**
   ```php
   // BAD - Don't log passwords
   AuditLogService::log('updated', $user, ['password' => $password]);

   // GOOD - Exclude sensitive fields
   AuditLogService::log('password_changed', $user);
   ```

2. **Log security events with higher severity:**
   ```php
   // Failed login attempts
   AuditLogService::logCustom('login_failed', '...', 'auth', 'warning');

   // Permission changes
   AuditLogService::logCustom('permission_changed', '...', 'security', 'critical');
   ```

3. **Include context for investigations:**
   ```php
   AuditLogService::logCustom(
       'suspicious_activity',
       'Multiple failed login attempts',
       'security',
       'critical',
       [
           'ip' => request()->ip(),
           'user_agent' => request()->userAgent(),
           'attempts' => $attemptCount
       ]
   );
   ```

---

## 🎯 Priority Implementation Areas

### High Priority (Do First):
1. ✅ User model (already done)
2. ⚡ Login/Logout events
3. ⚡ User status changes (approve/reject)
4. ⚡ Role assignments
5. ⚡ Password changes

### Medium Priority:
6. ⚡ Company CRUD operations
7. ⚡ Certificate operations
8. ⚡ Course enrollments
9. ⚡ File uploads/deletions

### Low Priority:
10. ⚡ Settings changes
11. ⚡ Data exports/imports
12. ⚡ Report generation

---

## 💡 Testing Your Audit Logs

After implementing, test by:
1. Creating a new user
2. Updating user details
3. Logging in/out
4. Changing roles
5. Check `/audit-logs` to see all activities recorded

Remember: The audit log is your system's "black box" - it should record everything important for security and compliance!