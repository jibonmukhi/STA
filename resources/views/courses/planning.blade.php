@extends('layouts.advanced-dashboard')

@section('title', trans('courses.course_planning'))

@section('content')
<div class="container-fluid">

    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>{{ trans('courses.course_planning') }}</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ trans('courses.dashboard') }}</a></li>
                            <li class="breadcrumb-item active">{{ trans('courses.course_planning') }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list"></i> {{ trans('courses.all_courses') }}
                    </a>
                    @can('create', App\Models\Course::class)
                    <a href="{{ route('courses.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ trans('courses.add_course') }}
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Course Assignment Form -->
    @can('create', App\Models\Course::class)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-tasks me-2"></i>
                        Assign Courses to Companies
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('course-company-assignments.store') }}" method="POST" id="assignmentForm">
                        @csrf

                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Instructions:</strong> Select courses from the list below using checkboxes, then choose companies to assign them to.
                        </div>

                        <div class="row">
                            <!-- Selected Courses Display -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    Selected Courses <span class="text-danger">*</span>
                                </label>
                                <div class="border rounded p-3 bg-light" style="min-height: 120px; max-height: 200px; overflow-y: auto;">
                                    <div id="selectedCoursesDisplay" class="text-muted">
                                        <small>No courses selected</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Company Multi-Select -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    Assign to Companies <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control mb-2" id="companySearch" placeholder="Search companies...">
                                <div class="border rounded bg-white" style="max-height: 200px; overflow-y: auto;">
                                    @foreach($companies as $company)
                                    <div class="multiselect-item" data-name="{{ strtolower($company->name) }}">
                                        <div class="form-check p-2">
                                            <input class="form-check-input company-checkbox-item" type="checkbox"
                                                   name="company_ids[]" value="{{ $company->id }}" id="company{{ $company->id }}">
                                            <label class="form-check-label" for="company{{ $company->id }}">
                                                {{ $company->name }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <span id="companyCount">0</span> selected
                                    </small>
                                    <button type="button" class="btn btn-sm btn-link text-danger" onclick="clearCompanySelection()">
                                        Clear All
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Due Date (Optional)</label>
                                <input type="date" class="form-control" name="due_date" min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Mandatory</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="is_mandatory" value="1" id="mandatorySwitch">
                                    <label class="form-check-label" for="mandatorySwitch">Required</label>
                                </div>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="form-label">Notes (Optional)</label>
                                <input type="text" class="form-control" name="notes" maxlength="1000" placeholder="Add notes...">
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-secondary" onclick="clearSelection()">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary" id="assignButton" disabled>
                                <i class="fas fa-check"></i> Assign Courses
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endcan

    <!-- Planning Overview Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $courses->count() }}</h3>
                            <small>{{ trans('courses.total_active_courses') }}</small>
                        </div>
                        <i class="fas fa-graduation-cap fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $coursesByCategory->count() }}</h3>
                            <small>{{ trans('courses.total_categories') }}</small>
                        </div>
                        <i class="fas fa-tags fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $courses->where('is_mandatory', true)->count() }}</h3>
                            <small>{{ trans('courses.mandatory_courses') }}</small>
                        </div>
                        <i class="fas fa-exclamation-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $courses->sum('duration_hours') }}</h3>
                            <small>{{ trans('courses.total_hours') }}</small>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses by Category -->
    @if($coursesByCategory->count() > 0)
        @foreach($coursesByCategory as $category => $categoryCourses)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="fas fa-folder-open me-2"></i>
                                {{ $categories[$category] ?? ucfirst(str_replace('_', ' ', $category)) }}
                                <span class="badge bg-primary ms-2">{{ $categoryCourses->count() }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($categoryCourses as $course)
                                    <div class="col-lg-4 col-md-6 mb-3">
                                        <div class="card border course-card h-100" data-course-id="{{ $course->id }}">
                                            @can('create', App\Models\Course::class)
                                            <div class="position-absolute top-0 end-0 m-2">
                                                <input type="checkbox" class="form-check-input course-checkbox"
                                                       name="course_ids[]"
                                                       value="{{ $course->id }}"
                                                       data-course-title="{{ $course->title }}"
                                                       form="assignmentForm"
                                                       id="course_{{ $course->id }}"
                                                       style="width: 20px; height: 20px; cursor: pointer;">
                                            </div>
                                            @endcan
                                            <div class="card-body">
                                                <h6 class="card-title mb-2">{{ $course->title }}</h6>
                                                <p class="text-muted small mb-2">
                                                    <strong>Code:</strong> {{ $course->course_code }}
                                                </p>

                                                <div class="row text-center mb-3">
                                                    <div class="col-4">
                                                        <small class="text-muted d-block">Level</small>
                                                        <strong class="small">{{ $levels[$course->level] ?? ucfirst($course->level) }}</strong>
                                                    </div>
                                                    <div class="col-4">
                                                        <small class="text-muted d-block">Duration</small>
                                                        <strong class="small">{{ $course->duration_hours }}h</strong>
                                                    </div>
                                                    <div class="col-4">
                                                        <small class="text-muted d-block">Price</small>
                                                        <strong class="small">${{ number_format($course->price, 2) }}</strong>
                                                    </div>
                                                </div>

                                                <div class="mb-2">
                                                    @php
                                                        $statusColor = dataVaultColor('course_status', $course->status) ?? 'secondary';
                                                        $statusIcon = dataVaultIcon('course_status', $course->status) ?? 'fas fa-circle';
                                                        $statusLabel = dataVaultLabel('course_status', $course->status) ?? ucfirst($course->status);
                                                    @endphp
                                                    <span class="badge bg-{{ $statusColor }}">
                                                        <i class="{{ $statusIcon }}"></i> {{ $statusLabel }}
                                                    </span>
                                                    <span class="badge bg-secondary">
                                                        {{ $deliveryMethods[$course->delivery_method] ?? ucfirst($course->delivery_method) }}
                                                    </span>
                                                    @if($course->is_mandatory)
                                                        <span class="badge bg-warning">Mandatory</span>
                                                    @endif
                                                    @if($course->credits)
                                                        <span class="badge bg-info">{{ $course->credits }} credits</span>
                                                    @endif
                                                </div>

                                                @if($course->instructor)
                                                    <p class="small text-muted mb-1">
                                                        <i class="fas fa-user-tie"></i> {{ $course->instructor }}
                                                    </p>
                                                @endif

                                                @if($course->available_from || $course->available_until)
                                                    <p class="small text-muted mb-0">
                                                        <i class="fas fa-calendar"></i>
                                                        @if($course->available_from)
                                                            {{ $course->available_from->format('M d, Y') }}
                                                        @endif
                                                        @if($course->available_until)
                                                            - {{ $course->available_until->format('M d, Y') }}
                                                        @endif
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="card-footer bg-transparent">
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('courses.show', $course) }}" class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    <a href="{{ route('courses.schedule', $course) }}" class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-calendar"></i> Schedule
                                                    </a>
                                                    @can('update', $course)
                                                    <a href="{{ route('courses.edit', $course) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">{{ trans('courses.no_active_courses') }}</h4>
                        <p class="text-muted">{{ trans('courses.no_active_courses_message') }}</p>
                        @can('create', App\Models\Course::class)
                        <a href="{{ route('courses.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> {{ trans('courses.create_first_course') }}
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
/* Course Cards */
.course-card {
    transition: all 0.2s ease;
}

.course-card:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.course-card.selected {
    border-color: #0d6efd !important;
    border-width: 2px !important;
    background-color: #f8f9ff;
}

/* Multi-select Component */
.multiselect-item {
    border-bottom: 1px solid #e9ecef;
}

.multiselect-item:hover {
    background-color: #f8f9fa;
}

.multiselect-item:last-child {
    border-bottom: none;
}

.multiselect-item .form-check-label {
    cursor: pointer;
}

.multiselect-item .form-check-input:checked ~ .form-check-label {
    font-weight: 600;
    color: #0d6efd;
}
</style>

@push('styles')
<style>
/* Additional styles */
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Company search functionality
    const companySearch = document.getElementById('companySearch');
    const companyItems = document.querySelectorAll('.multiselect-item');
    const companyCheckboxes = document.querySelectorAll('.company-checkbox-item');
    const companyCount = document.getElementById('companyCount');

    // Search filter
    companySearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        companyItems.forEach(item => {
            const companyName = item.dataset.name;
            if (companyName.includes(searchTerm)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Update company count
    function updateCompanyCount() {
        const count = Array.from(companyCheckboxes).filter(cb => cb.checked).length;
        companyCount.textContent = count;
    }

    companyCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateCompanyCount);
    });

    // Clear company selection function
    window.clearCompanySelection = function() {
        companyCheckboxes.forEach(cb => cb.checked = false);
        updateCompanyCount();
    };

    const checkboxes = document.querySelectorAll('.course-checkbox');
    const selectedDisplay = document.getElementById('selectedCoursesDisplay');
    const assignButton = document.getElementById('assignButton');

    function updateSelectedDisplay() {
        const selected = Array.from(checkboxes).filter(cb => cb.checked);

        if (selected.length === 0) {
            selectedDisplay.innerHTML = '<small>No courses selected</small>';
            assignButton.disabled = true;
        } else {
            const courseList = selected.map(cb => {
                return `<div class="badge bg-primary me-1 mb-1">${cb.dataset.courseTitle}</div>`;
            }).join('');
            selectedDisplay.innerHTML = `<strong>${selected.length} selected:</strong><div class="mt-2">${courseList}</div>`;
            assignButton.disabled = false;
        }

        // Update card styling
        checkboxes.forEach(cb => {
            const card = cb.closest('.course-card');
            if (card) {
                if (cb.checked) {
                    card.classList.add('selected');
                } else {
                    card.classList.remove('selected');
                }
            }
        });
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedDisplay);
    });

    // Clear selection function
    window.clearSelection = function() {
        checkboxes.forEach(cb => cb.checked = false);
        updateSelectedDisplay();
        clearCompanySelection();
    };

    // Initial update
    updateSelectedDisplay();
});
</script>
@endpush

@endsection