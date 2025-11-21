@extends('layouts.advanced-dashboard')

@section('title', trans('courses.edit_course'))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>{{ trans('courses.edit_course') }}: {{ $course->title }}</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ trans('courses.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('course-management.index') }}">{{ trans('courses.course_management') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('course-management.show', $course) }}">{{ $course->title }}</a></li>
                            <li class="breadcrumb-item active">{{ trans('courses.edit') }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('course-management.show', $course) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> {{ trans('courses.back_to_course') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('course-management.update', $course) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-6">
                <!-- Template Details (Read-only) -->
                @if($course->parentCourse)
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> {{ trans('courses.master_course_template') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Title:</strong> <span class="text-muted">{{ $course->parentCourse->title }}</span></p>
                                <p><strong>Course Code:</strong> <span class="text-muted">{{ $course->parentCourse->course_code }}</span></p>
                                <p><strong>Category:</strong> <span class="text-muted">{{ $categories[$course->parentCourse->category] ?? $course->parentCourse->category }}</span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Delivery Method:</strong> <span class="text-muted">{{ $deliveryMethods[$course->parentCourse->delivery_method] ?? $course->parentCourse->delivery_method }}</span></p>
                                <p><strong>Duration:</strong> <span class="text-muted">{{ $course->parentCourse->duration_hours }} hours</span></p>
                                <p><strong>{{ trans('courses.course_programme') }}:</strong> <span class="text-muted">{{ $course->parentCourse->description ?? '-' }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

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
                                       name="title" value="{{ old('title', $course->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Customize the title for this instance</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Instance Code *</label>
                                <input type="text" class="form-control @error('course_code') is-invalid @enderror"
                                       name="course_code" value="{{ old('course_code', $course->course_code) }}" required>
                                @error('course_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Unique code for this instance</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status *</label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                @foreach($statuses as $statusKey => $statusLabel)
                                    <option value="{{ $statusKey }}" {{ old('status', $course->status) == $statusKey ? 'selected' : '' }}>
                                        {{ $statusLabel }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Update the course status</small>
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
                                @php
                                    $assignedTeacherIds = $course->teachers->pluck('id')->toArray();
                                @endphp
                                @foreach($teachers as $teacher)
                                    <div class="form-check mb-2 teacher-search-item" data-teacher-name="{{ strtolower($teacher->full_name) }}" data-teacher-email="{{ strtolower($teacher->email) }}">
                                        <input class="form-check-input" type="checkbox" name="teacher_ids[]" value="{{ $teacher->id }}" id="teacher_{{ $teacher->id }}"
                                               {{ in_array($teacher->id, old('teacher_ids', $assignedTeacherIds)) ? 'checked' : '' }}>
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

            <div class="col-lg-6">
                <!-- Smart Calendar Session Scheduler -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> {{ trans('courses.class_sessions') }}</h5>
                        <span class="badge bg-light text-dark" id="sessionSummary">
                            <span id="totalSessions">{{ $course->sessions->count() }}</span> {{ trans('courses.sessions') }} | <span id="totalHours">{{ $course->sessions->sum('duration_hours') }}</span> {{ trans('courses.hours') }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle"></i>
                            <small>{{ trans('courses.course_programme') }}: <strong><span id="courseDuration">{{ $course->duration_hours ?? 0 }}</span> {{ trans('courses.hours') }}</strong></small>
                        </div>

                        <!-- Configuration Form -->
                        <div class="mb-3">
                            <label class="form-label">{{ trans('courses.start_date') }} *</label>
                            <input type="text" class="form-control form-control-sm datepicker @error('start_date') is-invalid @enderror"
                                   id="start_date_display" value="{{ old('start_date', $course->start_date?->format('d/m/Y')) }}" placeholder="DD/MM/YYYY" autocomplete="off">
                            <input type="hidden" name="start_date" id="start_date_hidden" value="{{ old('start_date', $course->start_date?->format('Y-m-d')) }}">
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">{{ trans('courses.session_start_time') }}</label>
                                <input type="time" class="form-control form-control-sm" id="sessionStartTime" value="{{ $course->sessions->first()?->start_time ? \Carbon\Carbon::parse($course->sessions->first()->start_time)->format('H:i') : '09:00' }}" onchange="updateSessionInputs()">
                            </div>
                            <div class="col-6">
                                <label class="form-label">{{ trans('courses.session_end_time') }}</label>
                                <input type="time" class="form-control form-control-sm" id="sessionEndTime" value="{{ $course->sessions->first()?->end_time ? \Carbon\Carbon::parse($course->sessions->first()->end_time)->format('H:i') : '13:00' }}" onchange="updateSessionInputs()">
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
                                                <button type="button" class="btn btn-sm btn-danger" onclick="clearAllSessions()">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="sessionsTableBody"></tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Hidden inputs for sessions -->
                        <div id="sessionInputs"></div>

                        <!-- Additional Fields -->
                        <input type="hidden" name="end_date" id="end_date_hidden" value="{{ old('end_date', $course->end_date?->format('Y-m-d')) }}">
                        <input type="hidden" name="start_time" id="start_time_hidden">
                        <input type="hidden" name="end_time" id="end_time_hidden">
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                   {{ old('is_active', $course->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label">
                                {{ trans('courses.active_course') }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assigned Company and Enroll Students Side by Side (Full Width) -->
        <div class="row mb-4">
            <!-- Assigned Company -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-building"></i> Assigned Company</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $assignedCompany = $course->assignedCompanies->first();
                        @endphp
                        @if($assignedCompany)
                            <div class="mb-2">
                                <small class="text-muted">Company assigned to this course instance:</small>
                            </div>
                            <div class="list-group">
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $assignedCompany->name }}</strong>
                                            @if($assignedCompany->pivot->is_mandatory)
                                                <span class="badge bg-warning ms-2">Mandatory</span>
                                            @endif
                                        </div>
                                        <small class="text-muted">
                                            Assigned: {{ $assignedCompany->pivot->assigned_date ? \Carbon\Carbon::parse($assignedCompany->pivot->assigned_date)->format('M d, Y') : '-' }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-muted mb-0">No company assigned yet. Enroll students to assign a company.</p>
                        @endif
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
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> <small>{{ trans('courses.select_companies_filter_info') }}</small>
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
                                        @php
                                            $enrolledStudentIds = $course->enrollments->pluck('user_id')->toArray();
                                        @endphp
                                        @foreach($users as $user)
                                            @php
                                                $userCompanyIds = $user->companies->pluck('id')->toArray();
                                                $assignedCompany = $course->assignedCompanies->first();
                                                $assignedCompanyId = $assignedCompany ? $assignedCompany->id : null;
                                                $matchesCompany = !$assignedCompanyId || in_array($assignedCompanyId, $userCompanyIds);
                                            @endphp
                                            <div class="form-check mb-2 student-search-item"
                                                 data-student-name="{{ strtolower($user->full_name) }}"
                                                 data-student-email="{{ strtolower($user->email) }}"
                                                 data-company-ids="{{ json_encode($userCompanyIds) }}"
                                                 style="display: {{ $matchesCompany ? 'block' : 'none' }};">
                                                <input class="form-check-input student-checkbox" type="checkbox" name="student_ids[]" value="{{ $user->id }}" id="student_{{ $user->id }}"
                                                       {{ in_array($user->id, $enrolledStudentIds) ? 'checked' : '' }}>
                                                <label class="form-check-label d-flex align-items-center" for="student_{{ $user->id }}">
                                                    <div>
                                                        <strong>{{ $user->full_name }}</strong>
                                                        <small class="text-muted d-block">{{ $user->email }}</small>
                                                        @if($user->companies->count() > 0)
                                                            <small class="text-info d-block">
                                                                <i class="fas fa-building"></i> {{ $user->companies->pluck('name')->join(', ') }}
                                                            </small>
                                                        @endif
                                                        @if(in_array($user->id, $enrolledStudentIds))
                                                            <small class="text-success d-block">
                                                                <i class="fas fa-check-circle"></i> Already enrolled
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

        <!-- Update Course Button at the end -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-end gap-3">
                    <a href="{{ route('course-management.show', $course) }}" class="btn btn-secondary px-4 py-2">
                        <i class="fas fa-times me-2"></i>{{ trans('courses.cancel') }}
                    </a>
                    <button type="submit" class="btn btn-primary px-4 py-2">
                        <i class="fas fa-save me-2"></i>{{ trans('courses.update_course') }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
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

function filterStudents() {
    const searchTerm = document.getElementById('studentSearch').value.toLowerCase();
    const studentItems = document.querySelectorAll('.student-search-item');

    studentItems.forEach(item => {
        const studentName = item.getAttribute('data-student-name');
        const studentEmail = item.getAttribute('data-student-email');

        if (studentName.includes(searchTerm) || studentEmail.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
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

// Add event listeners
document.addEventListener('DOMContentLoaded', function() {
    const studentCheckboxes = document.querySelectorAll('.student-checkbox');
    studentCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateStudentCount);
    });

    // Initial count
    updateStudentCount();

    // Load existing sessions into calendar
    loadExistingSessions();
});

// Smart Calendar Session Management
let selectedDates = [];
let existingSessionsData = [];

// Italian Holidays 2024-2025
const italianHolidays = [
    '2024-01-01', '2024-01-06', '2024-04-01', '2024-04-25', '2024-05-01',
    '2024-06-02', '2024-08-15', '2024-11-01', '2024-12-08', '2024-12-25', '2024-12-26',
    '2025-01-01', '2025-01-06', '2025-04-21', '2025-04-25', '2025-05-01',
    '2025-06-02', '2025-08-15', '2025-11-01', '2025-12-08', '2025-12-25', '2025-12-26'
];

function loadExistingSessions() {
    // Load existing sessions from the course
    @if($course->sessions->count() > 0)
        selectedDates = [
            @foreach($course->sessions as $session)
                '{{ $session->session_date->format('Y-m-d') }}',
            @endforeach
        ];

        existingSessionsData = [
            @foreach($course->sessions as $session)
                {
                    date: '{{ $session->session_date->format('Y-m-d') }}',
                    startTime: '{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}',
                    endTime: '{{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}'
                },
            @endforeach
        ];

        // Auto-render grid if sessions exist and start date is set
        const startDate = $('#start_date_hidden').val();
        if (startDate && selectedDates.length > 0) {
            renderSessionsGrid();
            updateSessionSummary();
            updateSessionInputs();
        }
    @endif
}

function isWeekend(date) {
    const day = date.getDay();
    return day === 0 || day === 6;
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
        alert('Course duration not available');
        return;
    }

    if (!sessionStartTime || !sessionEndTime) {
        alert('Please specify session start and end times');
        return;
    }

    const start = new Date(`2000-01-01 ${sessionStartTime}`);
    const end = new Date(`2000-01-01 ${sessionEndTime}`);
    const sessionDurationHours = (end - start) / (1000 * 60 * 60);

    if (sessionDurationHours <= 0) {
        alert('End time must be after start time');
        return;
    }

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

    renderSessionsGrid();
    updateSessionSummary();
    updateSessionInputs();
}

function renderSessionsGrid() {
    const grid = document.getElementById('sessionsGrid');
    const tbody = document.getElementById('sessionsTableBody');

    grid.style.display = 'block';
    tbody.innerHTML = '';

    const defaultStartTime = $('#sessionStartTime').val() || '09:00';
    const defaultEndTime = $('#sessionEndTime').val() || '13:00';

    selectedDates.forEach((dateStr, index) => {
        const sessionNumber = index + 1;

        // Use existing session data if available, otherwise use defaults
        let sessionStartTime = defaultStartTime;
        let sessionEndTime = defaultEndTime;

        if (existingSessionsData.length > 0 && existingSessionsData[index]) {
            sessionStartTime = existingSessionsData[index].startTime;
            sessionEndTime = existingSessionsData[index].endTime;
        }

        // Format date for display (dd/mm/yyyy)
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
                <button type="button" class="btn btn-sm btn-danger" onclick="removeSession(${index})">
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

        updateDurationDisplay(index);
    });
}

function updateSessionDate(index, newDate) {
    const oldDate = selectedDates[index];
    selectedDates[index] = newDate;

    // Update existing session data if available
    if (existingSessionsData.length > 0 && existingSessionsData[index]) {
        existingSessionsData[index].date = newDate;
    }

    // Sort both arrays together to maintain correspondence
    const combined = selectedDates.map((date, i) => ({
        date: date,
        data: existingSessionsData[i] || null
    }));

    combined.sort((a, b) => a.date.localeCompare(b.date));

    selectedDates = combined.map(item => item.date);
    existingSessionsData = combined.map(item => item.data).filter(item => item !== null);

    renderSessionsGrid();
    updateSessionSummary();
    updateSessionInputs();
}

function updateSessionTime(index, type, newTime) {
    // Update existing session data if available
    if (existingSessionsData.length > 0 && existingSessionsData[index]) {
        if (type === 'start') {
            existingSessionsData[index].startTime = newTime;
        } else if (type === 'end') {
            existingSessionsData[index].endTime = newTime;
        }
    }

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
                const hours = (end - start) / (1000 * 60 * 60);
                totalHours += hours;
            }
        }
    }

    $('#totalSessions').text(totalSessions);
    $('#totalHours').text(totalHours.toFixed(2));

    const courseDuration = parseFloat($('#courseDuration').text()) || 0;

    if (totalHours > courseDuration) {
        $('#sessionSummary').removeClass('bg-light text-dark').addClass('bg-warning text-dark');
    } else {
        $('#sessionSummary').removeClass('bg-warning text-dark').addClass('bg-light text-dark');
    }

    if (selectedDates.length > 0) {
        $('#end_date_hidden').val(selectedDates[selectedDates.length - 1]);
    }

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
#sessionsGrid {
    max-height: 500px;
    overflow-y: auto;
}

#sessionsGrid .table {
    font-size: 0.85rem;
    table-layout: fixed;
    width: 100%;
}

#sessionsGrid input[type="text"],
#sessionsGrid input[type="time"] {
    font-size: 0.85rem;
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