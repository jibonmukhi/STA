@extends('layouts.advanced-dashboard')

@section('title', trans('courses.course_planning'))

@section('content')
<div class="container-fluid">

    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>{{ trans('courses.course_planning') }}</h2>
                    <p class="text-muted mb-2">
                        <i class="fas fa-info-circle me-1"></i>
                        Assign training courses to companies and track their progress
                    </p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ trans('courses.dashboard') }}</a></li>
                            <li class="breadcrumb-item active">{{ trans('courses.course_planning') }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('calendar') }}" class="btn btn-outline-info">
                        <i class="fas fa-calendar"></i> {{ trans('courses.view_calendar') }}
                    </a>
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
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">
                                <i class="fas fa-tasks me-2"></i>
                                Bulk Course Assignment
                            </h5>
                            <small class="opacity-75">Assign multiple courses to multiple companies at once</small>
                        </div>
                        <a href="{{ route('calendar') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-calendar me-1"></i>
                            View Calendar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('course-company-assignments.store') }}" method="POST" id="assignmentForm">
                        @csrf

                        <div class="alert alert-info mb-4">
                            <i class="fas fa-lightbulb me-2"></i>
                            <strong>How it works:</strong>
                            <ol class="mb-0 mt-2 ps-3">
                                <li>Select one or more courses from the left panel</li>
                                <li>Select one or more companies from the right panel</li>
                                <li>Optionally set a due date and mark as mandatory</li>
                                <li>Click "Assign Courses" to complete the assignment</li>
                            </ol>
                        </div>

                        <div class="row">
                            <!-- Course Multi-Select -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    Select Courses <span class="text-danger">*</span>
                                </label>

                                <!-- Selected Courses Display -->
                                <div id="selectedCoursesPanel" class="border rounded p-2 mb-2" style="min-height: 60px; display: none; background-color: #f8f9fa; border-color: #0d6efd !important;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="fw-bold text-primary">
                                            <i class="fas fa-check-circle me-1"></i>
                                            Selected Courses (<span id="courseCount">0</span>)
                                        </small>
                                        <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="clearCourseSelection()" style="font-size: 0.85rem; text-decoration: none;">
                                            <i class="fas fa-times-circle me-1"></i>Clear All
                                        </button>
                                    </div>
                                    <div id="selectedCoursesList" class="d-flex flex-wrap" style="gap: 4px;"></div>
                                </div>

                                <input type="text" class="form-control mb-2" id="courseSearch" placeholder="Search courses by name or code...">
                                <div class="border rounded" style="height: 250px; overflow-y: auto; background: white;">
                                    @foreach($coursesByCategory as $category => $categoryCourses)
                                        <div class="course-category-section">
                                            <div class="p-2 bg-light fw-bold text-primary sticky-top" style="border-bottom: 1px solid #dee2e6;">
                                                <i class="fas fa-folder-open me-2"></i>
                                                {{ $categories[$category] ?? ucfirst(str_replace('_', ' ', $category)) }}
                                            </div>
                                            @foreach($categoryCourses as $course)
                                                <div class="course-option p-2"
                                                     data-course-id="{{ $course->id }}"
                                                     data-course-title="{{ $course->title }}"
                                                     data-search="{{ strtolower($course->title . ' ' . $course->course_code) }}"
                                                     style="cursor: pointer; border-bottom: 1px solid #f0f0f0;">
                                                    <label style="cursor: pointer; width: 100%; margin: 0;">
                                                        <input type="checkbox" name="course_ids[]" value="{{ $course->id }}"
                                                               class="form-check-input me-2 course-checkbox">
                                                        <strong>{{ $course->title }}</strong>
                                                        <br>
                                                        <small class="text-muted ms-4">
                                                            {{ $course->course_code }} -
                                                            @php
                                                                $categoryColor = dataVaultColor('course_category', $course->category) ?? 'info';
                                                                $categoryLabel = dataVaultLabel('course_category', $course->category) ?? ucfirst($course->category);
                                                            @endphp
                                                            <span class="badge bg-{{ $categoryColor }}" style="font-size: 0.75rem;">{{ $categoryLabel }}</span> -
                                                            {{ $course->duration_hours }}h
                                                        </small>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-link text-primary" onclick="selectAllCourses()">
                                        Select All Visible
                                    </button>
                                </div>
                            </div>

                            <!-- Company Multi-Select -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    Assign to Companies <span class="text-danger">*</span>
                                </label>

                                <!-- Selected Companies Display -->
                                <div id="selectedCompaniesPanel" class="border rounded p-2 mb-2" style="min-height: 60px; display: none; background-color: #f8f9fa; border-color: #198754 !important;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="fw-bold text-success">
                                            <i class="fas fa-check-circle me-1"></i>
                                            Selected Companies (<span id="companyCount">0</span>)
                                        </small>
                                        <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="clearCompanySelection()" style="font-size: 0.85rem; text-decoration: none;">
                                            <i class="fas fa-times-circle me-1"></i>Clear All
                                        </button>
                                    </div>
                                    <div id="selectedCompaniesList" class="d-flex flex-wrap" style="gap: 4px;"></div>
                                </div>

                                <input type="text" class="form-control mb-2" id="companySearch" placeholder="Search companies...">
                                <div class="border rounded" style="height: 250px; overflow-y: auto; background: white;">
                                    @foreach($companies as $company)
                                        <div class="company-option p-2"
                                             data-company-id="{{ $company->id }}"
                                             data-company-name="{{ $company->name }}"
                                             data-search="{{ strtolower($company->name) }}"
                                             style="cursor: pointer; border-bottom: 1px solid #f0f0f0;">
                                            <label style="cursor: pointer; width: 100%; margin: 0;">
                                                <input type="checkbox" name="company_ids[]" value="{{ $company->id }}"
                                                       class="form-check-input me-2 company-checkbox">
                                                {{ $company->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-link text-primary" onclick="selectAllCompanies()">
                                        Select All Visible
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

</div>

<style>
/* Searchable Multi-select Styles */
.course-option input[type="checkbox"]:checked ~ strong,
.company-option input[type="checkbox"]:checked {
    color: #0d6efd;
}

.course-option:hover,
.company-option:hover {
    background-color: #f8f9fa !important;
}

.course-category-section .sticky-top {
    z-index: 1;
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
    const courseSearch = document.getElementById('courseSearch');
    const companySearch = document.getElementById('companySearch');
    const courseCheckboxes = document.querySelectorAll('.course-checkbox');
    const companyCheckboxes = document.querySelectorAll('.company-checkbox');
    const courseCount = document.getElementById('courseCount');
    const companyCount = document.getElementById('companyCount');
    const assignButton = document.getElementById('assignButton');

    // Course search functionality
    courseSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const courseOptions = document.querySelectorAll('.course-option');
        const categorySections = document.querySelectorAll('.course-category-section');

        categorySections.forEach(section => {
            let hasVisibleCourses = false;
            const options = section.querySelectorAll('.course-option');

            options.forEach(option => {
                const searchData = option.getAttribute('data-search');
                if (searchData.includes(searchTerm)) {
                    option.style.display = '';
                    hasVisibleCourses = true;
                } else {
                    option.style.display = 'none';
                }
            });

            // Hide category header if no courses match
            section.style.display = hasVisibleCourses ? '' : 'none';
        });
    });

    // Company search functionality
    companySearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const companyOptions = document.querySelectorAll('.company-option');

        companyOptions.forEach(option => {
            const searchData = option.getAttribute('data-search');
            if (searchData.includes(searchTerm)) {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        });
    });

    // Update course count and display selected items
    function updateCourseCount() {
        const selectedCheckboxes = Array.from(courseCheckboxes).filter(cb => cb.checked);
        const count = selectedCheckboxes.length;
        courseCount.textContent = count;

        // Update selected courses panel
        const panel = document.getElementById('selectedCoursesPanel');
        const list = document.getElementById('selectedCoursesList');

        if (count > 0) {
            panel.style.display = 'block';
            list.innerHTML = '';

            selectedCheckboxes.forEach(checkbox => {
                const courseOption = checkbox.closest('.course-option');
                const courseTitle = courseOption.getAttribute('data-course-title');
                const courseId = checkbox.value;

                const badge = document.createElement('span');
                badge.className = 'badge bg-primary d-inline-flex align-items-center';
                badge.style.cssText = 'margin: 2px; padding: 6px 8px; font-size: 0.85rem;';
                badge.innerHTML = `
                    <span style="margin-right: 6px;">${courseTitle}</span>
                    <i class="fas fa-times" onclick="deselectCourse(${courseId})"
                       style="font-size: 0.85rem; cursor: pointer; opacity: 0.8;"
                       onmouseover="this.style.opacity='1'"
                       onmouseout="this.style.opacity='0.8'"></i>
                `;
                list.appendChild(badge);
            });
        } else {
            panel.style.display = 'none';
        }

        updateAssignButton();
    }

    // Update company count and display selected items
    function updateCompanyCount() {
        const selectedCheckboxes = Array.from(companyCheckboxes).filter(cb => cb.checked);
        const count = selectedCheckboxes.length;
        companyCount.textContent = count;

        // Update selected companies panel
        const panel = document.getElementById('selectedCompaniesPanel');
        const list = document.getElementById('selectedCompaniesList');

        if (count > 0) {
            panel.style.display = 'block';
            list.innerHTML = '';

            selectedCheckboxes.forEach(checkbox => {
                const companyOption = checkbox.closest('.company-option');
                const companyName = companyOption.getAttribute('data-company-name');
                const companyId = checkbox.value;

                const badge = document.createElement('span');
                badge.className = 'badge bg-success d-inline-flex align-items-center';
                badge.style.cssText = 'margin: 2px; padding: 6px 8px; font-size: 0.85rem;';
                badge.innerHTML = `
                    <span style="margin-right: 6px;">${companyName}</span>
                    <i class="fas fa-times" onclick="deselectCompany(${companyId})"
                       style="font-size: 0.85rem; cursor: pointer; opacity: 0.8;"
                       onmouseover="this.style.opacity='1'"
                       onmouseout="this.style.opacity='0.8'"></i>
                `;
                list.appendChild(badge);
            });
        } else {
            panel.style.display = 'none';
        }

        updateAssignButton();
    }

    // Deselect individual course
    window.deselectCourse = function(courseId) {
        const checkbox = document.querySelector(`.course-checkbox[value="${courseId}"]`);
        if (checkbox) {
            checkbox.checked = false;
            updateCourseCount();
        }
    };

    // Deselect individual company
    window.deselectCompany = function(companyId) {
        const checkbox = document.querySelector(`.company-checkbox[value="${companyId}"]`);
        if (checkbox) {
            checkbox.checked = false;
            updateCompanyCount();
        }
    };

    // Update assign button state
    function updateAssignButton() {
        if (assignButton) {
            const hasCoursesSelected = Array.from(courseCheckboxes).some(cb => cb.checked);
            const hasCompaniesSelected = Array.from(companyCheckboxes).some(cb => cb.checked);
            assignButton.disabled = !(hasCoursesSelected && hasCompaniesSelected);
        }
    }

    // Listen to checkbox changes
    courseCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateCourseCount);
    });

    companyCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateCompanyCount);
    });

    // Hover effect for options
    document.querySelectorAll('.course-option, .company-option').forEach(option => {
        option.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
        });
        option.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });

    // Select all courses (visible only)
    window.selectAllCourses = function() {
        document.querySelectorAll('.course-option').forEach(option => {
            if (option.style.display !== 'none') {
                const checkbox = option.querySelector('.course-checkbox');
                checkbox.checked = true;
            }
        });
        updateCourseCount();
    };

    // Clear course selection
    window.clearCourseSelection = function() {
        courseCheckboxes.forEach(cb => cb.checked = false);
        updateCourseCount();
    };

    // Select all companies (visible only)
    window.selectAllCompanies = function() {
        document.querySelectorAll('.company-option').forEach(option => {
            if (option.style.display !== 'none') {
                const checkbox = option.querySelector('.company-checkbox');
                checkbox.checked = true;
            }
        });
        updateCompanyCount();
    };

    // Clear company selection
    window.clearCompanySelection = function() {
        companyCheckboxes.forEach(cb => cb.checked = false);
        updateCompanyCount();
    };

    // Clear all selections
    window.clearSelection = function() {
        clearCourseSelection();
        clearCompanySelection();
    };

    // Initial update
    updateCourseCount();
    updateCompanyCount();
});
</script>
@endpush

@endsection