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
                            <li class="breadcrumb-item"><a href="{{ route('courses.enrollments.index', $course) }}">Enrollments</a></li>
                            <li class="breadcrumb-item active">Enroll Users</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('courses.enrollments.index', $course) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Enrollments
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
                        <form action="{{ route('courses.enrollments.store', $course) }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Course</label>
                                <input type="text" class="form-control" value="{{ $course->title }} ({{ $course->course_code }})" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Filter by Company</label>
                                <div class="mb-2">
                                    <input type="text" class="form-control form-control-sm" id="companySearch" placeholder="Search companies..." onkeyup="filterCompanies()">
                                </div>
                                <div class="border rounded p-3" style="max-height: 250px; overflow-y: auto; background: white;">
                                    <div class="mb-2">
                                        <small class="text-muted">Select companies to filter students (companies with enrolled users are pre-selected)</small>
                                    </div>
                                    @foreach($companies as $company)
                                        @php
                                            $hasEnrolledUsers = in_array($company->id, $enrolledCompanyIds);
                                        @endphp
                                        <div class="form-check mb-2 company-search-item" data-company-name="{{ strtolower($company->name) }}">
                                            <input class="form-check-input company-checkbox" type="checkbox"
                                                   id="company_{{ $company->id }}"
                                                   value="{{ $company->id }}"
                                                   {{ $hasEnrolledUsers ? 'checked' : '' }}
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

                            <div class="mb-3">
                                <label class="form-label">Select Users <span class="text-danger">*</span></label>
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
                                                 data-company-ids="{{ json_encode($userCompanyIds) }}"
                                                 style="{{ count($enrolledCompanyIds) > 0 && count(array_intersect($userCompanyIds, $enrolledCompanyIds)) > 0 ? 'display: block;' : (count($enrolledCompanyIds) > 0 ? 'display: none;' : 'display: block;') }}">
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
                                <a href="{{ route('courses.enrollments.index', $course) }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-user-plus"></i> Enroll Selected Users
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> All available users are already enrolled in this course.
                        </div>
                        <a href="{{ route('courses.enrollments.index', $course) }}" class="btn btn-secondary">Back to Enrollments</a>
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
        const selectedCompanyIds = Array.from(document.querySelectorAll('.company-checkbox:checked'))
            .map(cb => parseInt(cb.value));

        const userItems = document.querySelectorAll('.user-item');

        if (selectedCompanyIds.length === 0) {
            // Show all users if no companies selected
            userItems.forEach(item => {
                item.style.display = 'block';
            });
        } else {
            // Filter users by selected companies
            userItems.forEach(item => {
                const userCompanyIds = JSON.parse(item.getAttribute('data-company-ids') || '[]');
                const hasMatchingCompany = selectedCompanyIds.some(id => userCompanyIds.includes(id));

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
                const selectedCompanyIds = Array.from(document.querySelectorAll('.company-checkbox:checked'))
                    .map(cb => parseInt(cb.value));

                if (selectedCompanyIds.length === 0 || selectedCompanyIds.some(id => userCompanyIds.includes(id))) {
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
