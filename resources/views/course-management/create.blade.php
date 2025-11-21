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
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ $masterCourse->course_code }}</strong> - {{ $masterCourse->title }}
                                                </div>
                                                <span class="badge bg-info ms-2">{{ $masterCourse->duration_hours }}h</span>
                                            </div>
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
                <!-- Smart Calendar Session Scheduler -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> {{ trans('courses.class_sessions') }}</h5>
                        <span class="badge bg-light text-dark" id="sessionSummary">
                            <span id="totalSessions">0</span> {{ trans('courses.sessions') }} | <span id="totalHours">0</span> {{ trans('courses.hours') }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle"></i>
                            <small>{{ trans('courses.course_programme') }}: <strong><span id="courseDuration">0</span> {{ trans('courses.hours') }}</strong></small>
                        </div>

                        <!-- Configuration Form -->
                        <div class="mb-3">
                            <label class="form-label">{{ trans('courses.start_date') }} *</label>
                            <input type="text" class="form-control form-control-sm datepicker @error('start_date') is-invalid @enderror"
                                   id="start_date_display" value="{{ old('start_date') }}" placeholder="DD/MM/YYYY" autocomplete="off">
                            <input type="hidden" name="start_date" id="start_date_hidden" value="{{ old('start_date') }}">
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">{{ trans('courses.session_start_time') }}</label>
                                <input type="time" class="form-control form-control-sm" id="sessionStartTime" value="09:00" onchange="updateSessionInputs()">
                            </div>
                            <div class="col-6">
                                <label class="form-label">{{ trans('courses.session_end_time') }}</label>
                                <input type="time" class="form-control form-control-sm" id="sessionEndTime" value="13:00" onchange="updateSessionInputs()">
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="excludeWeekends" checked>
                                <label class="form-check-label" for="excludeWeekends">
                                    {{ trans('courses.exclude_weekends') }}
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="excludeHolidays" checked>
                                <label class="form-check-label" for="excludeHolidays">
                                    {{ trans('courses.exclude_italian_holidays') }}
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="weeklyMode">
                                <label class="form-check-label" for="weeklyMode">
                                    {{ trans('courses.weekly_mode') }}
                                </label>
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary btn-sm w-100 mb-3" onclick="generateCalendar()">
                            <i class="fas fa-magic"></i> {{ trans('courses.generate_sessions') }}
                        </button>

                        <!-- Sessions Grid -->
                        <div id="sessionsGrid" style="display: none;">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 40px;">#</th>
                                            <th style="width: 110px;">{{ trans('courses.session_date') }}</th>
                                            <th style="width: 130px;">{{ trans('courses.session_start_time') }}</th>
                                            <th style="width: 130px;">{{ trans('courses.session_end_time') }}</th>
                                            <th style="width: 60px;">{{ trans('courses.hours') }}</th>
                                            <th style="width: 40px;">
                                                <button type="button" class="btn btn-sm btn-danger" onclick="clearAllSessions()" title="Clear All">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="sessionsTableBody">
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Hidden inputs for sessions -->
                        <div id="sessionInputs"></div>

                        <!-- Additional Fields -->
                        <input type="hidden" name="end_date" id="end_date_hidden">
                        <input type="hidden" name="start_time" id="start_time_hidden">
                        <input type="hidden" name="end_time" id="end_time_hidden">
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label">
                                {{ trans('courses.active_course') }}
                            </label>
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

        // Update course duration display
        updateCourseDuration();
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

    // Watch for template duration changes to update course duration
    $('#hiddenDuration').on('change', function() {
        updateCourseDuration();
    });

    updateCourseDuration();
});

// Smart Calendar Session Management
let selectedDates = [];
let calendarInstance = null;

// Italian Holidays 2024-2025
const italianHolidays = [
    '2024-01-01', // New Year's Day
    '2024-01-06', // Epiphany
    '2024-04-01', // Easter Monday 2024
    '2024-04-25', // Liberation Day
    '2024-05-01', // Labour Day
    '2024-06-02', // Republic Day
    '2024-08-15', // Assumption
    '2024-11-01', // All Saints' Day
    '2024-12-08', // Immaculate Conception
    '2024-12-25', // Christmas
    '2024-12-26', // Santo Stefano
    '2025-01-01', // New Year's Day
    '2025-01-06', // Epiphany
    '2025-04-21', // Easter Monday 2025
    '2025-04-25', // Liberation Day
    '2025-05-01', // Labour Day
    '2025-06-02', // Republic Day
    '2025-08-15', // Assumption
    '2025-11-01', // All Saints' Day
    '2025-12-08', // Immaculate Conception
    '2025-12-25', // Christmas
    '2025-12-26'  // Santo Stefano
];

function updateCourseDuration() {
    const duration = parseFloat($('#hiddenDuration').val()) || 0;
    $('#courseDuration').text(duration);
    updateSessionSummary();
}

function isWeekend(date) {
    const day = date.getDay();
    return day === 0 || day === 6; // Sunday or Saturday
}

function isItalianHoliday(dateStr) {
    return italianHolidays.includes(dateStr);
}

function generateCalendar() {
    const startDate = $('#start_date_hidden').val();
    const courseDuration = parseFloat($('#courseDuration').text()) || 0;
    const sessionStartTime = $('#sessionStartTime').val();
    const sessionEndTime = $('#sessionEndTime').val();
    const excludeWeekends = $('#excludeWeekends').is(':checked');
    const excludeHolidays = $('#excludeHolidays').is(':checked');
    const weeklyMode = $('#weeklyMode').is(':checked');

    if (!startDate) {
        alert('Please select a course start date first');
        return;
    }

    if (courseDuration === 0) {
        alert('Please select a master course template first');
        return;
    }

    if (!sessionStartTime || !sessionEndTime) {
        alert('Please specify session start and end times');
        return;
    }

    // Calculate session duration in hours
    const start = new Date(`2000-01-01 ${sessionStartTime}`);
    const end = new Date(`2000-01-01 ${sessionEndTime}`);
    const sessionDurationHours = (end - start) / (1000 * 60 * 60);

    if (sessionDurationHours <= 0) {
        alert('End time must be after start time');
        return;
    }

    // Calculate number of sessions needed
    const numSessionsNeeded = Math.ceil(courseDuration / sessionDurationHours);

    // Auto-select business days
    selectedDates = [];
    let currentDate = new Date(startDate);
    let sessionsSelected = 0;

    while (sessionsSelected < numSessionsNeeded) {
        const dateStr = currentDate.toISOString().split('T')[0];
        const isWeekendDay = isWeekend(currentDate);
        const isHoliday = isItalianHoliday(dateStr);

        let shouldSelect = true;
        if (excludeWeekends && isWeekendDay) shouldSelect = false;
        if (excludeHolidays && isHoliday) shouldSelect = false;

        if (shouldSelect) {
            selectedDates.push(dateStr);
            sessionsSelected++;

            // If weekly mode, skip to same day next week
            if (weeklyMode) {
                currentDate.setDate(currentDate.getDate() + 7);
            } else {
                currentDate.setDate(currentDate.getDate() + 1);
            }
        } else {
            currentDate.setDate(currentDate.getDate() + 1);
        }
    }

    // Render sessions grid
    renderSessionsGrid();
    updateSessionSummary();
    updateSessionInputs();
}

function renderSessionsGrid() {
    const grid = document.getElementById('sessionsGrid');
    const tbody = document.getElementById('sessionsTableBody');

    grid.style.display = 'block';
    tbody.innerHTML = '';

    const sessionStartTime = $('#sessionStartTime').val() || '09:00';
    const sessionEndTime = $('#sessionEndTime').val() || '13:00';

    selectedDates.forEach((dateStr, index) => {
        const sessionNumber = index + 1;
        const date = new Date(dateStr);
        const displayDate = date.toLocaleDateString('it-IT');

        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="text-center">${sessionNumber}</td>
            <td>
                <input type="text" class="form-control form-control-sm session-datepicker"
                       id="session-date-${index}"
                       value="${displayDate}"
                       data-date-value="${dateStr}"
                       autocomplete="off"
                       readonly>
            </td>
            <td>
                <input type="time" class="form-control form-control-sm"
                       value="${sessionStartTime}"
                       onchange="updateSessionTime(${index}, 'start', this.value)">
            </td>
            <td>
                <input type="time" class="form-control form-control-sm"
                       value="${sessionEndTime}"
                       onchange="updateSessionTime(${index}, 'end', this.value)">
            </td>
            <td class="text-center">
                <span id="duration-${index}">0</span>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeSession(${index})" title="Remove">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);

        // Initialize datepicker for this row
        $(`#session-date-${index}`).datepicker({
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true,
            onSelect: function(dateText, inst) {
                // Convert dd/mm/yyyy to yyyy-mm-dd
                const parts = dateText.split('/');
                const isoDate = `${parts[2]}-${parts[1]}-${parts[0]}`;
                updateSessionDate(index, isoDate);
            }
        });

        // Calculate and display duration
        updateDurationDisplay(index);
    });
}

function updateSessionDate(index, newDate) {
    selectedDates[index] = newDate;
    selectedDates.sort();
    renderSessionsGrid();
    updateSessionSummary();
    updateSessionInputs();
}

function updateSessionTime(index, type, newTime) {
    // Times are stored in the grid inputs themselves
    updateDurationDisplay(index);
    updateSessionSummary();
    updateSessionInputs();
}

function updateDurationDisplay(index) {
    const row = document.getElementById('sessionsTableBody').children[index];
    if (!row) return;

    const timeInputs = row.querySelectorAll('input[type="time"]');
    if (timeInputs.length < 2) return;

    const startTime = timeInputs[0].value;
    const endTime = timeInputs[1].value;

    const start = new Date(`2000-01-01 ${startTime}`);
    const end = new Date(`2000-01-01 ${endTime}`);
    const hours = (end - start) / (1000 * 60 * 60);

    const durationSpan = document.getElementById(`duration-${index}`);
    if (durationSpan) {
        durationSpan.textContent = hours.toFixed(2);
    }
}

function removeSession(index) {
    selectedDates.splice(index, 1);
    renderSessionsGrid();
    updateSessionSummary();
    updateSessionInputs();
}

function clearAllSessions() {
    if (confirm('Are you sure you want to clear all sessions?')) {
        selectedDates = [];
        document.getElementById('sessionsGrid').style.display = 'none';
        updateSessionSummary();
        updateSessionInputs();
    }
}

function updateSessionSummary() {
    const totalSessions = selectedDates.length;
    let totalHours = 0;

    // Calculate total hours from grid
    const tbody = document.getElementById('sessionsTableBody');
    if (tbody && tbody.children.length > 0) {
        for (let i = 0; i < tbody.children.length; i++) {
            const row = tbody.children[i];
            const timeInputs = row.querySelectorAll('input[type="time"]');
            if (timeInputs.length >= 2) {
                const startTime = timeInputs[0].value;
                const endTime = timeInputs[1].value;

                const start = new Date(`2000-01-01 ${startTime}`);
                const end = new Date(`2000-01-01 ${endTime}`);
                totalHours += (end - start) / (1000 * 60 * 60);
            }
        }
    }

    $('#totalSessions').text(totalSessions);
    $('#totalHours').text(totalHours.toFixed(2));

    const courseDuration = parseFloat($('#courseDuration').text()) || 0;

    // Update badge color
    if (totalHours > courseDuration) {
        $('#sessionSummary').removeClass('bg-light text-dark').addClass('bg-warning text-dark');
    } else {
        $('#sessionSummary').removeClass('bg-warning text-dark').addClass('bg-light text-dark');
    }

    // Update hidden end_date, start_time, end_time
    if (selectedDates.length > 0) {
        $('#end_date_hidden').val(selectedDates[selectedDates.length - 1]);

        // Get first session times
        if (tbody && tbody.children.length > 0) {
            const firstRow = tbody.children[0];
            const timeInputs = firstRow.querySelectorAll('input[type="time"]');
            if (timeInputs.length >= 2) {
                const sessionStartTime = timeInputs[0].value;
                const sessionEndTime = timeInputs[1].value;
                $('#start_time_hidden').val(sessionStartTime);
                $('#end_time_hidden').val(sessionEndTime);
            }
        }
    }
}

function updateSessionInputs() {
    const container = $('#sessionInputs');
    container.empty();

    const tbody = document.getElementById('sessionsTableBody');
    if (!tbody || tbody.children.length === 0) return;

    selectedDates.forEach((dateStr, index) => {
        const row = tbody.children[index];
        if (!row) return;

        const sessionNumber = index + 1;
        const timeInputs = row.querySelectorAll('input[type="time"]');
        if (timeInputs.length < 2) return;

        const startTime = timeInputs[0].value;
        const endTime = timeInputs[1].value;

        const start = new Date(`2000-01-01 ${startTime}`);
        const end = new Date(`2000-01-01 ${endTime}`);
        const sessionDurationHours = (end - start) / (1000 * 60 * 60);

        container.append(`
            <input type="hidden" name="sessions[${index}][title]" value="Session ${sessionNumber}">
            <input type="hidden" name="sessions[${index}][date]" value="${dateStr}">
            <input type="hidden" name="sessions[${index}][start_time]" value="${startTime}">
            <input type="hidden" name="sessions[${index}][end_time]" value="${endTime}">
            <input type="hidden" name="sessions[${index}][duration]" value="${sessionDurationHours.toFixed(2)}">
            <input type="hidden" name="sessions[${index}][order]" value="${sessionNumber}">
        `);
    });
}
</script>

@push('styles')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<style>
.ui-datepicker {
    z-index: 9999 !important;
}

/* Gradient Card Headers */
.card-header.bg-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    border: none;
}

.card-header.bg-primary.text-white {
    color: #ffffff !important;
}

/* Sessions Grid Styles */
#sessionsGrid .table {
    font-size: 0.85rem;
    table-layout: fixed;
    width: 100%;
}

#sessionsGrid input[type="text"],
#sessionsGrid input[type="time"] {
    font-size: 0.85rem;
    padding: 0.25rem;
    width: 100%;
    max-width: 100%;
}

#sessionsGrid .session-datepicker {
    cursor: pointer;
    background-color: white;
}

#sessionsGrid td {
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
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
});
</script>
@endpush

@endsection