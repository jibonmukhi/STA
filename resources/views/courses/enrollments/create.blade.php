@extends('layouts.advanced-dashboard')

@section('title', 'Enroll Users - ' . $course->title)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Enroll Users in Course</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Courses</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('courses.show', $course) }}">{{ $course->title }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ courseEnrollmentRoute('index', $course) }}">Enrollments</a></li>
                            <li class="breadcrumb-item active">Enroll Users</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ courseManagementRoute('show', $course) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Course
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Select Users to Enroll</h5>
                </div>
                <div class="card-body">
                    @if($allUsers->count() > 0)
                        <form action="{{ courseEnrollmentRoute('store', $course) }}" method="POST">
                            @csrf

                            @php
                                // Find the first company with enrolled users for default selection
                                $firstEnrolledCompanyId = null;
                                foreach($companies as $comp) {
                                    if (in_array($comp->id, $enrolledCompanyIds)) {
                                        $firstEnrolledCompanyId = $comp->id;
                                        break;
                                    }
                                }
                                // Priority: enrolled company > course's default company
                                $initialSelectedCompanyId = $firstEnrolledCompanyId ?: ($defaultCompanyId ?? '');
                            @endphp

                            <!-- Hidden field to capture selected company -->
                            <input type="hidden" name="selected_company_id" id="selected_company_id" value="{{ $initialSelectedCompanyId }}">

                            <div class="mb-3">
                                <label class="form-label">Course</label>
                                <input type="text" class="form-control" value="{{ $course->title }} ({{ $course->course_code }})" readonly>
                            </div>

                            @if(!auth()->user()->hasRole('company_manager'))
                            <div class="mb-3">
                                <label class="form-label">Filter by Company</label>
                                <div class="mb-2">
                                    <input type="text" class="form-control form-control-sm" id="companySearch" placeholder="Search companies..." onkeyup="filterCompanies()">
                                </div>
                                <div class="border rounded p-3" style="max-height: 250px; overflow-y: auto; background: white;">
                                    <div class="mb-2">
                                        <small class="text-muted">Select a company to filter students</small>
                                    </div>
                                    @php
                                        // Find the first company with enrolled users
                                        $firstEnrolledCompanyId = null;
                                        foreach($companies as $comp) {
                                            if (in_array($comp->id, $enrolledCompanyIds)) {
                                                $firstEnrolledCompanyId = $comp->id;
                                                break;
                                            }
                                        }
                                        // Determine which should be selected: enrolled company > default company > all companies
                                        $selectedCompanyId = $firstEnrolledCompanyId ?: ($defaultCompanyId ?? null);
                                    @endphp

                                    <div class="form-check mb-2">
                                        <input class="form-check-input company-radio" type="radio"
                                               name="company_filter"
                                               id="company_all"
                                               value=""
                                               {{ !$selectedCompanyId ? 'checked' : '' }}
                                               onchange="filterByCompanies()">
                                        <label class="form-check-label" for="company_all">
                                            <strong>All Companies</strong>
                                        </label>
                                    </div>
                                    @foreach($companies as $company)
                                        @php
                                            $hasEnrolledUsers = in_array($company->id, $enrolledCompanyIds);
                                        @endphp
                                        <div class="form-check mb-2 company-search-item" data-company-name="{{ strtolower($company->name) }}">
                                            <input class="form-check-input company-radio" type="radio"
                                                   name="company_filter"
                                                   id="company_{{ $company->id }}"
                                                   value="{{ $company->id }}"
                                                   {{ $selectedCompanyId == $company->id ? 'checked' : '' }}
                                                   onchange="filterByCompanies()">
                                            <label class="form-check-label" for="company_{{ $company->id }}">
                                                {{ $company->name }}
                                                @if($hasEnrolledUsers)
                                                    <span class="badge bg-success ms-1" style="font-size: 0.65rem;">Has Enrolled Users</span>
                                                @endif
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @else
                            <div class="mb-3">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> You can enroll users from your company: <strong>{{ auth()->user()->companies->first()?->name }}</strong>
                                </div>
                            </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label">Select Users</label>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> <small>Select companies above to filter students. Enrolled users are shown with a badge and cannot be enrolled again.</small>
                                </div>
                                <div class="mb-2">
                                    <input type="text" class="form-control form-control-sm" id="user_search" placeholder="Search users by name or email..." onkeyup="filterUsers()">
                                </div>
                                <div class="mb-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllAvailable()">Select All Available</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">Deselect All</button>
                                    <span class="badge bg-secondary ms-2">
                                        Total: {{ $allUsers->count() }} users
                                    </span>
                                    <span class="badge bg-success ms-1" id="enrolledCount">
                                        Enrolled: {{ count($enrolledUserIds) }}
                                    </span>
                                    <span class="badge bg-primary ms-1" id="selectedCount">
                                        Selected: 0
                                    </span>
                                </div>
                                <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                                    @if($allUsers->count() > 0)
                                        @foreach($allUsers as $user)
                                            @php
                                                $isEnrolled = in_array($user->id, $enrolledUserIds);
                                                $userCompanyIds = $user->companies->pluck('id')->toArray();
                                            @endphp
                                            <div class="mb-2 user-item {{ $isEnrolled ? 'enrolled-user' : 'form-check' }}"
                                                 data-user-name="{{ strtolower($user->name) }}"
                                                 data-user-email="{{ strtolower($user->email) }}"
                                                 data-company-ids="{{ json_encode($userCompanyIds) }}">
                                                @if($isEnrolled)
                                                    <!-- Enrolled user - no checkbox -->
                                                    <div class="d-flex align-items-start p-2">
                                                        <div class="me-2">
                                                            <i class="fas fa-check-circle text-success" style="font-size: 1.2rem;"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex justify-content-between align-items-start">
                                                                <div>
                                                                    <strong>{{ $user->name }}</strong>
                                                                    <small class="text-muted d-block">{{ $user->email }}</small>
                                                                    @if($user->companies->count() > 0)
                                                                        <small class="text-info d-block">
                                                                            <i class="fas fa-building"></i> {{ $user->companies->pluck('name')->join(', ') }}
                                                                        </small>
                                                                    @endif
                                                                </div>
                                                                <div>
                                                                    <span class="badge bg-success">Already Enrolled</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <!-- Available user - with checkbox -->
                                                    <input class="form-check-input user-checkbox"
                                                           type="checkbox"
                                                           name="user_ids[]"
                                                           value="{{ $user->id }}"
                                                           id="user{{ $user->id }}"
                                                           onchange="updateSelectedCount()">
                                                    <label class="form-check-label w-100" for="user{{ $user->id }}">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div>
                                                                <strong>{{ $user->name }}</strong>
                                                                <small class="text-muted d-block">{{ $user->email }}</small>
                                                                @if($user->companies->count() > 0)
                                                                    <small class="text-info d-block">
                                                                        <i class="fas fa-building"></i> {{ $user->companies->pluck('name')->join(', ') }}
                                                                    </small>
                                                                @endif
                                                            </div>
                                                            <div>
                                                                @if($user->primary_company)
                                                                    <span class="badge bg-info">{{ $user->primary_company->name }}</span>
                                                                @else
                                                                    <span class="badge bg-secondary">No Company</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </label>
                                                @endif
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-muted text-center py-3 mb-0">
                                            <i class="fas fa-info-circle"></i> No users available.
                                        </p>
                                    @endif
                                </div>
                                @error('user_ids')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Initial Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                    <option value="enrolled" {{ old('status', 'enrolled') == 'enrolled' ? 'selected' : '' }}>Enrolled</option>
                                    <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" rows="3">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('course-management.show', $course) }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-user-plus"></i> Enroll Selected Users
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> All available users are already enrolled in this course.
                        </div>
                        <a href="{{ route('course-management.show', $course) }}" class="btn btn-secondary">Back to Course</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function filterCompanies() {
        const searchTerm = document.getElementById('companySearch').value.toLowerCase();
        const companyItems = document.querySelectorAll('.company-search-item');

        companyItems.forEach(item => {
            const companyName = item.getAttribute('data-company-name');

            if (companyName.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function filterByCompanies() {
        const selectedRadio = document.querySelector('.company-radio:checked');
        const selectedCompanyId = selectedRadio ? parseInt(selectedRadio.value) : null;

        // Update the hidden field with selected company ID
        const hiddenField = document.getElementById('selected_company_id');
        if (hiddenField) {
            hiddenField.value = selectedCompanyId && !isNaN(selectedCompanyId) ? selectedCompanyId : '';
            console.log('Updated hidden field value:', hiddenField.value, 'Selected Company ID:', selectedCompanyId);
        } else {
            console.error('Hidden field not found!');
        }

        const userItems = document.querySelectorAll('.user-item');

        if (!selectedCompanyId || isNaN(selectedCompanyId)) {
            // Show all users if no company selected or "All Companies" is selected
            userItems.forEach(item => {
                item.style.display = 'block';
            });
        } else {
            // Filter users by selected company
            userItems.forEach(item => {
                const userCompanyIds = JSON.parse(item.getAttribute('data-company-ids') || '[]');
                const hasMatchingCompany = userCompanyIds.includes(selectedCompanyId);

                if (hasMatchingCompany) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        updateSelectedCount();
    }

    function selectAllAvailable() {
        document.querySelectorAll('.user-checkbox:not([disabled])').forEach(function(checkbox) {
            const parentItem = checkbox.closest('.user-item');
            if (parentItem && parentItem.style.display !== 'none') {
                checkbox.checked = true;
            }
        });
        updateSelectedCount();
    }

    function deselectAll() {
        document.querySelectorAll('.user-checkbox:not([disabled])').forEach(function(checkbox) {
            checkbox.checked = false;
        });
        updateSelectedCount();
    }

    function filterUsers() {
        const searchTerm = document.getElementById('user_search').value.toLowerCase();
        const userItems = document.querySelectorAll('.user-item');

        userItems.forEach(item => {
            const userName = item.getAttribute('data-user-name');
            const userEmail = item.getAttribute('data-user-email');

            if (userName.includes(searchTerm) || userEmail.includes(searchTerm)) {
                // Don't override company filter - check if item is currently visible due to company filter
                const userCompanyIds = JSON.parse(item.getAttribute('data-company-ids') || '[]');
                const selectedRadio = document.querySelector('.company-radio:checked');
                const selectedCompanyId = selectedRadio ? parseInt(selectedRadio.value) : null;

                if (!selectedCompanyId || isNaN(selectedCompanyId) || userCompanyIds.includes(selectedCompanyId)) {
                    item.style.display = 'block';
                }
            } else {
                item.style.display = 'none';
            }
        });
    }

    function updateSelectedCount() {
        const selectedCount = document.querySelectorAll('.user-checkbox:checked:not([disabled])').length;
        document.getElementById('selectedCount').textContent = 'Selected: ' + selectedCount;
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Trigger company filter on page load to apply default company selection
        filterByCompanies();
        updateSelectedCount();
    });
</script>

<style>
.enrolled-user {
    background-color: rgba(25, 135, 84, 0.08);
    border-left: 4px solid #198754;
    border-radius: 4px;
    padding: 4px !important;
}

.enrolled-user:hover {
    background-color: rgba(25, 135, 84, 0.12);
}

.user-item:not(.enrolled-user):hover {
    background-color: rgba(0, 123, 255, 0.05);
}
</style>
@endpush
@endsection
