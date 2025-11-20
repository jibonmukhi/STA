@extends('layouts.advanced-dashboard')

@section('title', trans('courses.start_new_course'))

@section('content')
<style>
.card {
    position: unset!important;
}

.master-course-wrapper {
    position: relative;
}

#masterCourseDropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1050;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    margin-top: 0.125rem;
    max-height: 300px;
    overflow-y: auto;
}

#masterCourseDropdown .dropdown-item {
    padding: 0.5rem 1rem;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
    white-space: normal;
    word-wrap: break-word;
}

#masterCourseDropdown .dropdown-item:hover {
    background-color: #f8f9fa;
}

#masterCourseDropdown .dropdown-item:last-child {
    border-bottom: none;
}

#masterCourseSearch {
    cursor: text;
}

.master-course-option strong {
    color: #0d6efd;
}

/* Ensure the card doesn't get covered */
#masterCourseDropdown.show {
    display: block !important;
}
</style>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>{{ trans('courses.start_new_course') }}</h2>
                    <p class="text-muted">{{ trans('courses.all_started_courses') }}</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ trans('courses.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('course-management.index') }}">{{ trans('courses.course_management') }}</a></li>
                            <li class="breadcrumb-item active">{{ trans('courses.start_new') }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('course-management.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> {{ trans('courses.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('course-management.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <!-- Master Course Selection -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-book"></i> {{ trans('courses.select_master_course') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">{{ trans('courses.master_course') }} *</label>
                            <div class="master-course-wrapper">
                                <input type="text" class="form-control @error('parent_course_id') is-invalid @enderror"
                                       id="masterCourseSearch"
                                       placeholder="{{ trans('courses.search_course_template') }}"
                                       autocomplete="off">
                                <input type="hidden" name="parent_course_id" id="masterCourseSelect" value="{{ old('parent_course_id') }}" required>

                                <!-- Dropdown list -->
                                <div id="masterCourseDropdown" style="display: none;">
                                    @foreach($masterCourses as $masterCourse)
                                        <a class="dropdown-item master-course-option"
                                           href="#"
                                           data-course-id="{{ $masterCourse->id }}"
                                           data-course="{{ json_encode($masterCourse) }}"
                                           data-search-text="{{ strtolower($masterCourse->course_code . ' ' . $masterCourse->title) }}">
                                            <strong>{{ $masterCourse->course_code }}</strong> - {{ $masterCourse->title }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                            @error('parent_course_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Type to search and select a master course template</small>
                        </div>
                    </div>
                </div>

                <!-- Template Details (Read-only) -->
                <div class="card mb-4" id="templateDetailsCard" style="display: none;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Template Details (Read-only)</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Title:</strong> <span id="template-title" class="text-muted">-</span></p>
                                <p><strong>Course Code:</strong> <span id="template-code" class="text-muted">-</span></p>
                                <p><strong>Category:</strong> <span id="template-category" class="text-muted">-</span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Delivery Method:</strong> <span id="template-delivery" class="text-muted">-</span></p>
                                <p><strong>Duration:</strong> <span id="template-duration" class="text-muted">-</span></p>
                                <p><strong>{{ trans('courses.course_programme') }}:</strong> <span id="template-description" class="text-muted">-</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Instance Information (Editable) -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Course Instance Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Instance Title *</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                       name="title" id="instanceTitle" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Customize the title for this instance</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Instance Code *</label>
                                <input type="text" class="form-control @error('course_code') is-invalid @enderror"
                                       name="course_code" id="instanceCode" value="{{ old('course_code') }}" required>
                                @error('course_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Unique code for this instance</small>
                            </div>
                        </div>

                        <!-- Hidden fields for template data -->
                        <input type="hidden" name="category" id="hiddenCategory">
                        <input type="hidden" name="delivery_method" id="hiddenDeliveryMethod">
                        <input type="hidden" name="duration_hours" id="hiddenDuration">
                        <input type="hidden" name="description" id="hiddenDescription">

                        <div class="mb-3">
                            <label class="form-label">Status *</label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                @foreach($statuses as $statusKey => $statusLabel)
                                    <option value="{{ $statusKey }}" {{ old('status', 'active') == $statusKey ? 'selected' : '' }}>
                                        {{ $statusLabel }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Select the initial status for this course</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Assigned Teachers *</label>
                            <div class="mb-2">
                                <input type="text" class="form-control form-control-sm" id="teacherSearch" placeholder="Search teachers..." onkeyup="filterTeachers()">
                            </div>
                            <div class="border rounded p-3" style="max-height: 250px; overflow-y: auto; background: white;">
                                <div class="mb-2">
                                    <small class="text-muted">Select one or more teachers to assign to this course instance</small>
                                </div>
                                @foreach($teachers as $teacher)
                                    <div class="form-check mb-2 teacher-search-item" data-teacher-name="{{ strtolower($teacher->full_name) }}" data-teacher-email="{{ strtolower($teacher->email) }}">
                                        <input class="form-check-input" type="checkbox" name="teacher_ids[]" value="{{ $teacher->id }}" id="teacher_{{ $teacher->id }}"
                                               {{ in_array($teacher->id, old('teacher_ids', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="teacher_{{ $teacher->id }}">
                                            {{ $teacher->full_name }} ({{ $teacher->email }})
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('teacher_ids')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Course Schedule</h5>
                    </div>
                    <div class="card-body">
                        <h5 class="mb-3">Schedule (Start to End Time)</h5>

                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Start Date</label>
                                <input type="text" class="form-control datepicker @error('start_date') is-invalid @enderror"
                                       id="start_date_display" value="{{ old('start_date') }}" placeholder="DD/MM/YYYY" autocomplete="off">
                                <input type="hidden" name="start_date" id="start_date_hidden" value="{{ old('start_date') }}">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label">Start Time</label>
                                <input type="text" class="form-control timepicker @error('start_time') is-invalid @enderror"
                                       name="start_time" value="{{ old('start_time') }}" placeholder="HH:MM" autocomplete="off">
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">End Date</label>
                                <input type="text" class="form-control datepicker @error('end_date') is-invalid @enderror"
                                       id="end_date_display" value="{{ old('end_date') }}" placeholder="DD/MM/YYYY" autocomplete="off">
                                <input type="hidden" name="end_date" id="end_date_hidden" value="{{ old('end_date') }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label">End Time</label>
                                <input type="text" class="form-control timepicker @error('end_time') is-invalid @enderror"
                                       name="end_time" value="{{ old('end_time') }}" placeholder="HH:MM" autocomplete="off">
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label">
                                    Active Course
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assign to Companies and Enroll Students Side by Side (Full Width) -->
        <div class="row mb-4">
            <!-- Assign to Companies -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-building"></i> {{ trans('courses.assign_to_companies') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">{{ trans('courses.select_companies') }}</label>
                            <div class="mb-2">
                                <input type="text" class="form-control form-control-sm" id="companySearch" placeholder="{{ trans('courses.search_companies') }}" onkeyup="filterCompanies()">
                            </div>
                            <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto; background: white;">
                                <div class="mb-2">
                                    <small class="text-muted">{{ trans('courses.select_company_to_assign') }}</small>
                                </div>
                                @if(isset($companies))
                                    @foreach($companies as $company)
                                        <div class="form-check mb-2 company-search-item" data-company-name="{{ strtolower($company->name) }}">
                                            <input class="form-check-input company-radio" type="radio" name="company_id" value="{{ $company->id }}" id="company_{{ $company->id }}" onchange="filterStudentsByCompanies()">
                                            <label class="form-check-label" for="company_{{ $company->id }}">
                                                {{ $company->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enroll Students -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-users"></i> {{ trans('courses.enroll_students') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">{{ trans('courses.select_students_to_enroll') }}</label>
                            <div class="alert alert-warning" id="companyFilterAlert">
                                <i class="fas fa-info-circle"></i> <small>Please select a company first to see available students.</small>
                            </div>
                            <div class="mb-2">
                                <input type="text" class="form-control form-control-sm" id="studentSearch" placeholder="{{ trans('courses.search_students') }}" onkeyup="filterStudents()">
                            </div>
                            <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto; background: white;">
                                <div class="mb-3">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllVisibleStudents()">
                                        <i class="fas fa-check-square"></i> {{ trans('courses.select_all_visible') }}
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllStudents()">
                                        <i class="fas fa-square"></i> {{ trans('courses.deselect_all') }}
                                    </button>
                                    @if(isset($users) && $users->count() > 0)
                                        <small class="text-muted ms-2">({{ $users->count() }} total users)</small>
                                    @endif
                                </div>
                                <div id="studentsList">
                                    @if(isset($users) && $users->count() > 0)
                                        @foreach($users as $user)
                                            @php
                                                $userCompanyIds = $user->companies->pluck('id')->toArray();
                                            @endphp
                                            <div class="form-check mb-2 student-search-item"
                                                 data-student-name="{{ strtolower($user->full_name) }}"
                                                 data-student-email="{{ strtolower($user->email) }}"
                                                 data-company-ids="{{ json_encode($userCompanyIds) }}"
                                                 style="display: none;">
                                                <input class="form-check-input student-checkbox" type="checkbox" name="student_ids[]" value="{{ $user->id }}" id="student_{{ $user->id }}">
                                                <label class="form-check-label d-flex align-items-center" for="student_{{ $user->id }}">
                                                    <div>
                                                        <strong>{{ $user->full_name }}</strong>
                                                        <small class="text-muted d-block">{{ $user->email }}</small>
                                                        @if($user->companies->count() > 0)
                                                            <small class="text-info d-block">
                                                                <i class="fas fa-building"></i> {{ $user->companies->pluck('name')->join(', ') }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                </label>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-muted">{{ trans('courses.no_students_available') }}</p>
                                    @endif
                                </div>
                            </div>
                            @error('student_ids')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <div class="mt-2">
                                <small class="text-muted"><span id="selectedStudentCount">0</span> {{ trans('courses.students_selected', ['count' => '']) }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Course Button at the end -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-end gap-3">
                    <a href="{{ route('course-management.index') }}" class="btn btn-secondary px-4 py-2">
                        <i class="fas fa-times me-2"></i>{{ trans('courses.cancel') }}
                    </a>
                    <button type="submit" class="btn btn-primary px-4 py-2">
                        <i class="fas fa-save me-2"></i>{{ trans('courses.create_course') }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('masterCourseSearch');
    const hiddenInput = document.getElementById('masterCourseSelect');
    const dropdown = document.getElementById('masterCourseDropdown');
    const courseOptions = document.querySelectorAll('.master-course-option');

    // Show dropdown when search input is focused
    searchInput.addEventListener('focus', function() {
        dropdown.style.display = 'block';
        filterCourses('');
    });

    // Filter courses as user types
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        filterCourses(searchTerm);
        dropdown.style.display = 'block';
    });

    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    // Handle course selection
    courseOptions.forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();

            const courseId = this.getAttribute('data-course-id');
            const courseText = this.textContent.trim();

            // Set values
            searchInput.value = courseText;
            hiddenInput.value = courseId;

            // Hide dropdown
            dropdown.style.display = 'none';

            // Populate form with course data
            try {
                const courseData = JSON.parse(this.getAttribute('data-course'));
                populateFormFields(courseData);
            } catch (e) {
                console.error('Error parsing course data:', e);
            }
        });
    });

    // Filter courses based on search term
    function filterCourses(searchTerm) {
        let visibleCount = 0;
        courseOptions.forEach(option => {
            const searchText = option.getAttribute('data-search-text');
            if (searchText.includes(searchTerm)) {
                option.style.display = 'block';
                visibleCount++;
            } else {
                option.style.display = 'none';
            }
        });

        // Show "no results" message if needed
        if (visibleCount === 0) {
            dropdown.style.display = 'block';
        }
    }

    // Populate form fields with template data
    function populateFormFields(courseData) {
        // Generate a unique course code by appending current date/time
        const now = new Date();
        const dateStr = now.getFullYear() +
                      String(now.getMonth() + 1).padStart(2, '0') +
                      String(now.getDate()).padStart(2, '0');
        const timeStr = String(now.getHours()).padStart(2, '0') +
                      String(now.getMinutes()).padStart(2, '0');

        // Show template details card
        document.getElementById('templateDetailsCard').style.display = 'block';

        // Populate template details (read-only display)
        document.getElementById('template-title').textContent = courseData.title || '-';
        document.getElementById('template-code').textContent = courseData.course_code || '-';
        document.getElementById('template-category').textContent = courseData.category || '-';
        document.getElementById('template-delivery').textContent = courseData.delivery_method || '-';
        document.getElementById('template-duration').textContent = (courseData.duration_hours || '-') + ' hours';
        document.getElementById('template-description').textContent = courseData.description || 'No description';

        // Populate instance fields (editable)
        document.getElementById('instanceTitle').value = courseData.title || '';
        document.getElementById('instanceCode').value = (courseData.course_code || '') + '-' + dateStr + '-' + timeStr;

        // Set hidden fields with template data
        document.getElementById('hiddenCategory').value = courseData.category || '';
        document.getElementById('hiddenDeliveryMethod').value = courseData.delivery_method || '';
        document.getElementById('hiddenDuration').value = courseData.duration_hours || '';
        document.getElementById('hiddenDescription').value = courseData.description || '';
    }

    // Load previously selected course on page load (for validation errors)
    if (hiddenInput.value) {
        const selectedOption = Array.from(courseOptions).find(
            opt => opt.getAttribute('data-course-id') === hiddenInput.value
        );
        if (selectedOption) {
            searchInput.value = selectedOption.textContent.trim();
        }
    }
});

function filterTeachers() {
    const searchTerm = document.getElementById('teacherSearch').value.toLowerCase();
    const teacherItems = document.querySelectorAll('.teacher-search-item');

    teacherItems.forEach(item => {
        const teacherName = item.getAttribute('data-teacher-name');
        const teacherEmail = item.getAttribute('data-teacher-email');

        if (teacherName.includes(searchTerm) || teacherEmail.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

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

function filterStudents() {
    const searchTerm = document.getElementById('studentSearch').value.toLowerCase();
    const studentItems = document.querySelectorAll('.student-search-item');

    studentItems.forEach(item => {
        const studentName = item.getAttribute('data-student-name');
        const studentEmail = item.getAttribute('data-student-email');
        const isVisible = item.style.display !== 'none';

        if ((studentName.includes(searchTerm) || studentEmail.includes(searchTerm)) && isVisible) {
            item.style.display = 'block';
        } else if (!studentName.includes(searchTerm) && !studentEmail.includes(searchTerm)) {
            item.style.display = 'none';
        }
    });
}

function filterStudentsByCompanies() {
    const selectedCompanyRadio = document.querySelector('.company-radio:checked');
    const selectedCompanyId = selectedCompanyRadio ? parseInt(selectedCompanyRadio.value) : null;

    const studentItems = document.querySelectorAll('.student-search-item');
    const companyFilterAlert = document.getElementById('companyFilterAlert');

    if (!selectedCompanyId) {
        // Hide all students if no company selected
        studentItems.forEach(item => {
            item.style.display = 'none';
        });
        // Show warning alert
        if (companyFilterAlert) {
            companyFilterAlert.classList.remove('alert-info');
            companyFilterAlert.classList.add('alert-warning');
            companyFilterAlert.innerHTML = '<i class="fas fa-info-circle"></i> <small>Please select a company first to see available students.</small>';
        }
    } else {
        // Hide warning alert and show info
        if (companyFilterAlert) {
            companyFilterAlert.classList.remove('alert-warning');
            companyFilterAlert.classList.add('alert-info');
            companyFilterAlert.innerHTML = '<i class="fas fa-info-circle"></i> <small>{{ trans("courses.select_companies_filter_info") }}</small>';
        }

        // Filter students by selected company
        studentItems.forEach(item => {
            const studentCompanyIds = JSON.parse(item.getAttribute('data-company-ids') || '[]');
            const hasMatchingCompany = studentCompanyIds.includes(selectedCompanyId);

            if (hasMatchingCompany) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    updateStudentCount();
}

function selectAllVisibleStudents() {
    const studentItems = document.querySelectorAll('.student-search-item');
    studentItems.forEach(item => {
        if (item.style.display !== 'none') {
            const checkbox = item.querySelector('.student-checkbox');
            if (checkbox) {
                checkbox.checked = true;
            }
        }
    });
    updateStudentCount();
}

function deselectAllStudents() {
    const studentCheckboxes = document.querySelectorAll('.student-checkbox');
    studentCheckboxes.forEach(cb => {
        cb.checked = false;
    });
    updateStudentCount();
}

function updateStudentCount() {
    const selectedCount = document.querySelectorAll('.student-checkbox:checked').length;
    document.getElementById('selectedStudentCount').textContent = selectedCount;
}

// Add event listeners to student checkboxes to update count
document.addEventListener('DOMContentLoaded', function() {
    const studentCheckboxes = document.querySelectorAll('.student-checkbox');
    studentCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateStudentCount);
    });

    // Initial count
    updateStudentCount();
});
</script>

@push('styles')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
<style>
.ui-datepicker {
    z-index: 9999 !important;
}
.ui-timepicker-wrapper {
    z-index: 9999 !important;
    max-height: 200px;
    overflow-y: auto;
    overflow-x: hidden;
}
.ui-timepicker-list {
    margin: 0;
    padding: 0;
    list-style: none;
}
.ui-timepicker-list li {
    padding: 5px 10px;
    cursor: pointer;
}
.ui-timepicker-list li:hover,
.ui-timepicker-selected {
    background-color: #0d6efd;
    color: white;
}
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize datepicker for start_date
    $('#start_date_display').datepicker({
        dateFormat: 'dd/mm/yy',
        altField: '#start_date_hidden',
        altFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        yearRange: '-10:+10',
        showButtonPanel: true,
        beforeShow: function(input, inst) {
            setTimeout(function() {
                inst.dpDiv.css({
                    'z-index': 9999
                });
            }, 0);
        }
    });

    // Initialize datepicker for end_date
    $('#end_date_display').datepicker({
        dateFormat: 'dd/mm/yy',
        altField: '#end_date_hidden',
        altFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        yearRange: '-10:+10',
        showButtonPanel: true,
        beforeShow: function(input, inst) {
            setTimeout(function() {
                inst.dpDiv.css({
                    'z-index': 9999
                });
            }, 0);
        }
    });

    // Initialize timepicker
    $('.timepicker').timepicker({
        timeFormat: 'HH:mm',
        interval: 15,
        minTime: '0:00',
        maxTime: '23:45',
        defaultTime: '9:00',
        startTime: '0:00',
        dynamic: false,
        dropdown: true,
        scrollbar: true
    });
});
</script>
@endpush

@endsection