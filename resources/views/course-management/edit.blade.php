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
            <div class="col-lg-8">
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
                                       id="start_date_display" value="{{ old('start_date', $course->start_date?->format('d/m/Y')) }}" placeholder="DD/MM/YYYY" autocomplete="off">
                                <input type="hidden" name="start_date" id="start_date_hidden" value="{{ old('start_date', $course->start_date?->format('Y-m-d')) }}">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label">Start Time</label>
                                <input type="text" class="form-control timepicker @error('start_time') is-invalid @enderror"
                                       name="start_time" value="{{ old('start_time', $course->start_time) }}" placeholder="HH:MM" autocomplete="off">
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">End Date</label>
                                <input type="text" class="form-control datepicker @error('end_date') is-invalid @enderror"
                                       id="end_date_display" value="{{ old('end_date', $course->end_date?->format('d/m/Y')) }}" placeholder="DD/MM/YYYY" autocomplete="off">
                                <input type="hidden" name="end_date" id="end_date_hidden" value="{{ old('end_date', $course->end_date?->format('Y-m-d')) }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label">End Time</label>
                                <input type="text" class="form-control timepicker @error('end_time') is-invalid @enderror"
                                       name="end_time" value="{{ old('end_time', $course->end_time) }}" placeholder="HH:MM" autocomplete="off">
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                       {{ old('is_active', $course->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label">
                                    Active Course
                                </label>
                            </div>
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