# STA System - Coding Standards & Principles

**Version:** 1.0
**Last Updated:** 2025-10-15
**Purpose:** Ensure consistency, maintainability, and quality across the STA Laravel application

---

## Table of Contents

1. [Core Principles](#core-principles)
2. [Localization Standards](#localization-standards)
3. [Data Tables & Lists](#data-tables--lists)
4. [Forms & Validation](#forms--validation)
5. [Controllers & Business Logic](#controllers--business-logic)
6. [Flash Messages & User Feedback](#flash-messages--user-feedback)
7. [Blade Components](#blade-components)
8. [Naming Conventions](#naming-conventions)
9. [Quick Reference Checklist](#quick-reference-checklist)

---

## Core Principles

### 🌍 **Localization First**
Every piece of user-facing text MUST be localized. No hardcoded strings in views, controllers, or emails.

### 📊 **Consistent User Experience**
All data lists must provide search, pagination, sorting, and rows-per-page controls.

### ✅ **Validation Everywhere**
All forms must use Form Request classes with localized error messages.

### 📢 **Clear Feedback**
Users must receive clear, localized feedback for every action (success, error, warning, info).

### 🧩 **Component Reusability**
Use Blade components to avoid code duplication.

---

## Localization Standards

### ✅ DO: Use Translation Keys

```php
// In Controllers
return redirect()->route('users.index')
    ->with('success', __('users.user_created'));

// In Blade Views
<label for="name" class="form-label">
    {{ __('users.first_name') }} <span class="text-danger">*</span>
</label>

<button type="submit" class="btn btn-primary">
    <i class="fas fa-save me-2"></i>{{ __('users.create_user_btn') }}
</button>

// Placeholders
<input type="text"
       name="search_name"
       placeholder="{{ __('users.search_by_name') }}"
       value="{{ request('search_name') }}">
```

### ❌ DON'T: Hardcode Text

```php
// BAD - Hardcoded text
return redirect()->route('users.index')
    ->with('success', 'User created successfully!');

// BAD - Mixed hardcoded and localized
<label>Name <span class="text-danger">*</span></label>

// BAD - English-only placeholder
<input type="text" name="email" placeholder="Enter email address">
```

### Translation File Organization

```
resources/lang/
├── en/
│   ├── common.php          # Shared terms (save, cancel, delete, etc.)
│   ├── users.php           # User module translations
│   ├── companies.php       # Company module translations
│   ├── notifications.php   # Notification texts
│   └── validation.php      # Validation messages
└── it/
    ├── common.php
    ├── users.php
    ├── companies.php
    ├── notifications.php
    └── validation.php
```

### Translation Key Naming Convention

```php
// Pattern: {module}.{category}_{subcategory}_{type}

__('users.first_name')                    // Field label
__('users.search_by_name')                // Placeholder
__('users.create_user_btn')               // Button text
__('users.user_created')                  // Success message
__('users.delete_confirmation')           // Confirmation message
__('common.save')                         // Common action
__('common.cancel')                       // Common action
```

### Pluralization

```php
// Use trans_choice for countable items
trans_choice('users.bulk_upload_success_count', $count, ['count' => $count])

// In language file:
'bulk_upload_success_count' => ':count user successfully imported.|:count users successfully imported.',
```

### Dynamic Content in Translations

```php
// With placeholders
__('users.users_need_approval', ['count' => $pendingCount, 'users' => $userWord])

// In language file:
'users_need_approval' => ':count :users admin approval to become active.',
```

---

## Data Tables & Lists

### Required Features

Every data table/list page MUST include:

1. ✅ **Search functionality**
2. ✅ **Pagination controls**
3. ✅ **Rows per page dropdown** (values: 5, 10, 25, 50, 100)
4. ✅ **Sortable columns** (where applicable)
5. ✅ **Query parameter preservation** (search terms persist across pagination)
6. ✅ **Results summary** ("Showing X to Y of Z results")

### Use the Data Table Component

```blade
{{-- resources/views/users/index.blade.php --}}

<x-data-table-template
    title="{{ __('users.all_users') }}"
    :items="$users"
    routePrefix="users"
    :searchPlaceholder="__('users.search_users')">

    {{-- Action Buttons Slot --}}
    <x-slot name="actions">
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>{{ __('users.add_new_user') }}
        </a>
    </x-slot>

    {{-- Table Headers Slot --}}
    <x-slot name="tableHeaders">
        <thead>
            <tr>
                <th>{{ __('users.name') }}</th>
                <th>{{ __('users.email') }}</th>
                <th>{{ __('users.status') }}</th>
                <th>{{ __('users.actions') }}</th>
            </tr>
        </thead>
    </x-slot>

    {{-- Table Rows Slot --}}
    <x-slot name="tableRows">
        @forelse($users as $user)
            <tr>
                <td>{{ $user->full_name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}">
                        {{ __('users.' . $user->status) }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-primary">
                        {{ __('common.edit') }}
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center py-4">
                    {{ __('users.no_users_found') }}
                </td>
            </tr>
        @endforelse
    </x-slot>
</x-data-table-template>
```

### Controller Pattern for Lists

```php
public function index(Request $request)
{
    $query = User::with(['roles', 'companies']);

    // 1. Apply search filters
    if ($request->filled('search_name')) {
        $query->where('name', 'like', '%' . $request->get('search_name') . '%');
    }

    if ($request->filled('search_email')) {
        $query->where('email', 'like', '%' . $request->get('search_email') . '%');
    }

    // 2. Apply status filter
    if ($request->filled('status')) {
        $query->where('status', $request->get('status'));
    }

    // 3. Validate and apply per_page
    $perPage = $request->get('per_page', 10);
    if (!in_array($perPage, [5, 10, 25, 50, 100])) {
        $perPage = 10;
    }

    // 4. Validate and apply sorting
    $sortField = $request->get('sort', 'name');
    $sortDirection = $request->get('direction', 'asc');

    // Whitelist sortable columns
    if (!in_array($sortField, ['name', 'email', 'created_at', 'status'])) {
        $sortField = 'name';
    }

    if (!in_array($sortDirection, ['asc', 'desc'])) {
        $sortDirection = 'asc';
    }

    // 5. Paginate with query string preservation
    $users = $query->orderBy($sortField, $sortDirection)
                   ->paginate($perPage)
                   ->withQueryString();  // IMPORTANT: Preserves search params

    return view('users.index', compact('users'));
}
```

### Sortable Column Headers

```blade
<th>
    <a href="{{ request()->fullUrlWithQuery([
        'sort' => 'name',
        'direction' => request('sort') === 'name' && request('direction') === 'asc' ? 'desc' : 'asc'
    ]) }}" class="text-decoration-none text-dark">
        {{ __('users.name') }}
        @if(request('sort') === 'name')
            <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
        @else
            <i class="fas fa-sort ms-1 text-muted"></i>
        @endif
    </a>
</th>
```

### Per Page Dropdown with Preserved Parameters

```blade
<form method="GET" action="{{ route('users.index') }}" class="d-flex align-items-center">
    {{-- Preserve all search parameters --}}
    @if(request('search_name'))
        <input type="hidden" name="search_name" value="{{ request('search_name') }}">
    @endif
    @if(request('search_email'))
        <input type="hidden" name="search_email" value="{{ request('search_email') }}">
    @endif
    <input type="hidden" name="sort" value="{{ request('sort', 'name') }}">
    <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">

    <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
        <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
    </select>
    <span class="text-muted small ms-2">{{ __('users.entries_per_page') }}</span>
</form>
```

---

## Forms & Validation

### Form Request Classes (REQUIRED)

Every form MUST use a Form Request class for validation.

```php
// app/Http/Requests/StoreUserRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('create users');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'status' => 'nullable|in:active,inactive,parked',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.required', ['attribute' => __('users.first_name')]),
            'email.unique' => __('validation.unique', ['attribute' => __('users.email')]),
            'password.confirmed' => __('validation.confirmed', ['attribute' => __('users.password')]),
        ];
    }
}
```

### Controller Usage

```php
use App\Http\Requests\StoreUserRequest;

public function store(StoreUserRequest $request)
{
    // $request is automatically validated
    $validated = $request->validated();

    $user = User::create($validated);

    return redirect()->route('users.index')
        ->with('success', __('users.user_created'));
}
```

### Form Layout Standards

All forms MUST follow this structure:

```blade
<form method="POST" action="{{ route('users.store') }}">
    @csrf

    {{-- Personal Information Section --}}
    <div class="row">
        <div class="col-12">
            <h6 class="border-bottom pb-2 mb-3">
                <i class="fas fa-user me-2"></i>{{ __('users.personal_information') }}
            </h6>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="name" class="form-label">
                    {{ __('users.first_name') }} <span class="text-danger">*</span>
                </label>
                <input type="text"
                       class="form-control @error('name') is-invalid @enderror"
                       id="name"
                       name="name"
                       value="{{ old('name') }}"
                       required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label for="email" class="form-label">
                    {{ __('users.email_address') }} <span class="text-danger">*</span>
                </label>
                <input type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       id="email"
                       name="email"
                       value="{{ old('email') }}"
                       required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    {{-- Contact Information Section --}}
    <div class="row">
        <div class="col-12">
            <h6 class="border-bottom pb-2 mb-3 mt-4">
                <i class="fas fa-address-card me-2"></i>{{ __('users.contact_information') }}
            </h6>
        </div>
    </div>

    <!-- More fields... -->

    {{-- Form Actions --}}
    <hr>
    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
            <i class="fas fa-times me-2"></i>{{ __('common.cancel') }}
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>{{ __('users.create_user_btn') }}
        </button>
    </div>
</form>
```

### Required Field Indicators

```blade
{{-- Required field --}}
<label for="name" class="form-label">
    {{ __('users.first_name') }} <span class="text-danger">*</span>
</label>

{{-- Optional field (no indicator) --}}
<label for="phone" class="form-label">
    {{ __('users.phone_number') }}
</label>
```

### Error Display at Top of Form

```blade
{{-- Display general errors --}}
@if($errors->has('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ $errors->first('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Display validation errors --}}
@if($errors->any() && !$errors->has('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <strong>{{ __('users.please_fix_errors') }}</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
```

### Select Dropdowns with Localization

```blade
{{-- Status dropdown --}}
<select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
    @foreach(dataVaultItems('user_status') as $item)
        <option value="{{ $item['code'] }}" {{ old('status', 'parked') == $item['code'] ? 'selected' : '' }}>
            {{ $item['label'] }}
        </option>
    @endforeach
</select>

{{-- Gender dropdown --}}
<select class="form-select" id="gender" name="gender">
    <option value="">{{ __('users.select_gender') }}</option>
    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>{{ __('users.male') }}</option>
    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>{{ __('users.female') }}</option>
    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>{{ __('users.other') }}</option>
</select>
```

---

## Controllers & Business Logic

### CRUD Standard Pattern

```php
namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // See "Data Tables & Lists" section for full implementation
        $users = User::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->paginate($request->get('per_page', 10))
            ->withQueryString();

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $user = User::create($request->validated());

            return redirect()->route('users.index')
                ->with('success', __('users.user_created'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => __('users.create_failed')])
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
        $user->load(['roles']);
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $user->update($request->validated());

            return redirect()->route('users.index')
                ->with('success', __('users.user_updated'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => __('users.update_failed')])
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
                    ->with('error', __('users.cannot_delete_self'));
            }

            $userName = $user->full_name;
            $user->delete();

            return redirect()->route('users.index')
                ->with('success', __('users.user_deleted', ['name' => $userName]));

        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', __('users.delete_failed'));
        }
    }
}
```

### Redirect Patterns After CRUD

```php
// After CREATE → Redirect to index with success message
return redirect()->route('users.index')
    ->with('success', __('users.user_created'));

// After UPDATE → Redirect to index with success message
return redirect()->route('users.index')
    ->with('success', __('users.user_updated'));

// After DELETE → Redirect to index with success message
return redirect()->route('users.index')
    ->with('success', __('users.user_deleted'));

// On ERROR → Redirect back with input and error
return redirect()->back()
    ->withErrors(['error' => __('users.create_failed')])
    ->withInput();
```

---

## Flash Messages & User Feedback

### Flash Message Keys

Use these standardized keys:

- `success` - For successful operations
- `error` - For error messages
- `warning` - For warnings
- `info` - For informational messages

### Setting Flash Messages

```php
// Success
return redirect()->route('users.index')
    ->with('success', __('users.user_created'));

// Error
return redirect()->back()
    ->with('error', __('users.create_failed'))
    ->withInput();

// Warning
return redirect()->route('users.index')
    ->with('warning', __('users.pending_approval_notice'));

// Info
return redirect()->route('users.index')
    ->with('info', __('users.email_verification_sent'));
```

### Flash Message Component

**EVERY page MUST include the flash messages component:**

```blade
{{-- At the top of your content section --}}
@section('content')

@include('components.flash-messages')

{{-- Rest of your content --}}

@endsection
```

### Flash Messages Component Implementation

```blade
{{-- resources/views/components/flash-messages.blade.php --}}

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
```

---

## Blade Components

### When to Create a Component

Create a Blade component when:
- The same UI pattern appears 3+ times
- The pattern has clear, reusable props
- The pattern requires complex logic

### Existing Components to Use

```blade
{{-- Data table template --}}
<x-data-table-template ... />

{{-- Flash messages --}}
@include('components.flash-messages')

{{-- Language switcher --}}
<x-language-switcher />

{{-- Application logo --}}
<x-application-logo />

{{-- Form inputs --}}
<x-text-input />
<x-input-label />
<x-input-error />

{{-- Buttons --}}
<x-primary-button />
<x-secondary-button />
<x-danger-button />
```

### Component Props Standards

```blade
{{-- Use kebab-case for props --}}
<x-data-table-template
    :title="$title"
    :items="$items"
    :route-prefix="$routePrefix"
    :search-placeholder="$placeholder"
/>

{{-- NOT camelCase --}}
<x-data-table-template
    :searchPlaceholder="$placeholder"  {{-- ❌ DON'T --}}
/>
```

---

## Naming Conventions

### Routes

```php
// Use resource routes where possible
Route::resource('users', UserController::class);

// Named routes use dot notation
Route::get('/pending-approvals', [UserController::class, 'pendingApprovals'])
    ->name('users.pending-approvals');

// Pattern: {resource}.{action}
Route::post('/users/bulk-upload', [UserController::class, 'bulkUpload'])
    ->name('users.bulk-upload');
```

### Controllers

```php
// Singular resource name + Controller
UserController
CompanyController
RoleController

// Dashboard controllers
STAManagerDashboardController
CompanyManagerDashboardController
```

### Models

```php
// Singular, PascalCase
User
Company
Role
Certificate
```

### Views

```
resources/views/
├── users/
│   ├── index.blade.php          // List
│   ├── create.blade.php         // Create form
│   ├── edit.blade.php           // Edit form
│   ├── show.blade.php           // Details
│   └── bulk-upload.blade.php    // Special action
├── companies/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── ...
└── components/
    ├── flash-messages.blade.php
    └── data-table-template.blade.php
```

### Database Tables

```
users
companies
roles
certificates
company_user (pivot table - alphabetical order)
```

### Translation Keys

```
{module}.{category}_{detail}

users.first_name
users.search_by_email
users.create_user_btn
users.user_created
common.save
common.cancel
```

---

## Quick Reference Checklist

Use this checklist before committing code:

### For List/Index Pages

- [ ] Uses `paginate()` with `withQueryString()`
- [ ] Includes search functionality
- [ ] Has rows-per-page dropdown (5, 10, 25, 50, 100)
- [ ] Sortable columns are implemented
- [ ] Query parameters are preserved
- [ ] Shows results summary
- [ ] All text is localized
- [ ] Includes flash messages component

### For Forms

- [ ] Uses Form Request class for validation
- [ ] All labels are localized
- [ ] Required fields marked with `*`
- [ ] Form sections have icon headers
- [ ] Error messages displayed at top and inline
- [ ] Old input is preserved with `old()`
- [ ] Success/error redirects are implemented
- [ ] All buttons have icons and localized text

### For Controllers

- [ ] Authorization checks are in place
- [ ] Try-catch blocks for database operations
- [ ] Flash messages are localized
- [ ] Redirects follow standard patterns
- [ ] Query parameters are whitelisted
- [ ] Pagination defaults to 10

### For Blade Views

- [ ] `@extends('layouts.advanced-dashboard')` is used
- [ ] `@section('page-title')` is set with localized text
- [ ] Flash messages component is included
- [ ] All text uses `__()` helper
- [ ] Components are used where applicable
- [ ] Icons are used consistently (FontAwesome)

### For Translation Files

- [ ] Keys follow naming convention
- [ ] Both EN and IT files are updated
- [ ] Pluralization uses `|` separator
- [ ] Placeholders use `:variable` format
- [ ] No hardcoded text remains in code

---

## Examples from Codebase

### ✅ Good Example: UserController::index()

```php
public function index(Request $request)
{
    $query = User::with(['roles', 'companies']);

    // Search filters
    if ($request->filled('search_name')) {
        $query->where('name', 'like', '%' . $request->get('search_name') . '%');
    }

    if ($request->filled('search_email')) {
        $query->where('email', 'like', '%' . $request->get('search_email') . '%');
    }

    // Per page validation
    $perPage = $request->get('per_page', 10);
    if (!in_array($perPage, [5, 10, 25, 50, 100])) {
        $perPage = 10;
    }

    // Sorting validation
    $sortField = $request->get('sort', 'name');
    $sortDirection = $request->get('direction', 'asc');

    if (!in_array($sortField, ['name', 'email', 'created_at', 'status'])) {
        $sortField = 'name';
    }

    if (!in_array($sortDirection, ['asc', 'desc'])) {
        $sortDirection = 'asc';
    }

    $users = $query->orderBy($sortField, $sortDirection)
                   ->paginate($perPage)
                   ->withQueryString();

    $companies = Company::active()->orderBy('name')->get();

    return view('users.index', compact('users', 'companies'));
}
```

### ✅ Good Example: StoreUserRequest

```php
class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('create users');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'status' => 'nullable|in:active,inactive,parked',
        ];
    }

    public function messages(): array
    {
        return [
            'surname.required' => 'Surname is required.',
            'cf.unique' => 'This Codice Fiscale is already registered.',
        ];
    }
}
```

### ✅ Good Example: Localized Form

```blade
<div class="mb-3">
    <label for="name" class="form-label">
        {{ __('users.first_name') }} <span class="text-danger">*</span>
    </label>
    <input type="text"
           class="form-control @error('name') is-invalid @enderror"
           id="name"
           name="name"
           value="{{ old('name') }}"
           required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
```

---

## Tools & Commands (Future Enhancement)

Future additions to enforce these standards:

```bash
# Generate controller with standards
php artisan make:standard-controller UserController

# Generate Form Request with localization
php artisan make:standard-request StoreUserRequest

# Generate CRUD views with standards
php artisan make:standard-views users

# Validate existing code against standards
php artisan validate:standards
```

---

## Conclusion

Following these standards ensures:

- **Consistency** across the entire application
- **Maintainability** for future developers
- **User Experience** through localization and clear feedback
- **Code Quality** through validation and error handling
- **Performance** through pagination and query optimization

**Remember:** When in doubt, refer to existing implementations in:
- `UserController.php`
- `users/index.blade.php`
- `users/create.blade.php`
- `StoreUserRequest.php`

---

**Questions or Suggestions?**
Contact the development team or update this document via pull request.

**Last Updated:** 2025-10-15
**Version:** 1.0
