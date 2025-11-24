@extends('layouts.advanced-dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>{{ $session->session_title }} - {{ __('teacher.mark_attendance') }}</h2>
            <p class="text-muted mb-0">
                <strong>{{ __('teacher.title') }}:</strong> {{ $session->course->title }} ({{ $session->course->course_code }})
            </p>
            <p class="text-muted mb-0">
                <strong>{{ __('teacher.start_date') }}:</strong> {{ $session->session_date->format('d/m/Y') }} |
                <strong>{{ __('common.time') }}:</strong> {{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }} |
                <strong>{{ __('common.duration') }}:</strong> {{ $session->duration_hours }}{{ __('teacher.hours') }}
            </p>
            @if($session->course->assignedCompanies && $session->course->assignedCompanies->isNotEmpty())
                <p class="text-muted mb-0">
                    <span class="badge bg-secondary">{{ $session->course->assignedCompanies->pluck('name')->join(', ') }}</span>
                </p>
            @endif
        </div>
        <div>
            <a href="{{ route('teacher.session-attendance', $session->course) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('teacher.attendance_grid') }}
            </a>
        </div>
    </div>

    @if($enrollments->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> {{ __('teacher.no_students_enrolled_attendance') }}
        </div>
    @else
        <!-- Attendance Statistics -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">{{ __('teacher.session_statistics') }}</h5>
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="p-3 bg-light rounded">
                                    <h4 class="mb-0">{{ $stats['total_enrolled'] }}</h4>
                                    <small class="text-muted">{{ __('teacher.total_students') }}</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 bg-success bg-opacity-10 rounded">
                                    <h4 class="mb-0 text-success">{{ $stats['present'] }}</h4>
                                    <small class="text-muted">{{ __('teacher.present') }}</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 bg-danger bg-opacity-10 rounded">
                                    <h4 class="mb-0 text-danger">{{ $stats['absent'] }}</h4>
                                    <small class="text-muted">{{ __('teacher.absent') }}</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 bg-secondary bg-opacity-10 rounded">
                                    <h4 class="mb-0 text-secondary">{{ $stats['not_marked'] }}</h4>
                                    <small class="text-muted">{{ __('teacher.not_marked_students') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-success" id="mark-all-present">
                        <i class="fas fa-check-double"></i> {{ __('teacher.mark_all_present') }}
                    </button>
                    <button type="button" class="btn btn-danger" id="mark-all-absent">
                        <i class="fas fa-times"></i> {{ __('teacher.mark_all_absent') }}
                    </button>
                    <button type="button" class="btn btn-info" id="save-all-btn">
                        <i class="fas fa-save"></i> {{ __('teacher.save_all_attendance') }}
                    </button>
                </div>

                @if($session->status !== 'completed' && $session->canBeClosed())
                    <button type="button" class="btn btn-primary float-end" id="close-session-btn">
                        <i class="fas fa-lock"></i> {{ __('teacher.close_session') }}
                    </button>
                @elseif($session->status === 'completed')
                    <button type="button" class="btn btn-success float-end" disabled>
                        <i class="fas fa-check"></i> {{ __('teacher.session_completed') }}
                    </button>
                @endif
            </div>
        </div>

        <!-- Attendance Form -->
        <div class="card">
            <div class="card-body">
                <form id="bulk-attendance-form">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>{{ __('teacher.student_name') }}</th>
                                    <th>{{ __('teacher.email') }}</th>
                                    <th style="width: 250px;">{{ __('teacher.attendance_status') }}</th>
                                    <th style="width: 150px;">{{ __('teacher.hours_attended') }}</th>
                                    <th style="width: 200px;">{{ __('teacher.notes') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($enrollments as $index => $enrollment)
                                    @php
                                        $attendance = $session->attendances()
                                            ->where('user_id', $enrollment->user_id)
                                            ->first();
                                        $currentStatus = $attendance ? $attendance->status : 'absent';
                                        $currentHours = $attendance ? $attendance->attended_hours : $session->duration_hours;
                                        $currentNotes = $attendance ? $attendance->notes : '';
                                    @endphp
                                    <tr class="student-row" data-enrollment-id="{{ $enrollment->id }}" data-user-id="{{ $enrollment->user_id }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle bg-primary text-white me-2" style="width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                    {{ strtoupper(substr($enrollment->user->name, 0, 1)) }}
                                                </div>
                                                <strong>{{ $enrollment->user->name }}</strong>
                                            </div>
                                        </td>
                                        <td>{{ $enrollment->user->email }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm status-buttons" role="group">
                                                <input type="radio" class="btn-check status-input"
                                                       name="status_{{ $enrollment->id }}"
                                                       id="present_{{ $enrollment->id }}"
                                                       value="present"
                                                       {{ $currentStatus === 'present' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-success" for="present_{{ $enrollment->id }}">
                                                    <i class="fas fa-check"></i> {{ __('teacher.present') }}
                                                </label>

                                                <input type="radio" class="btn-check status-input"
                                                       name="status_{{ $enrollment->id }}"
                                                       id="absent_{{ $enrollment->id }}"
                                                       value="absent"
                                                       {{ $currentStatus === 'absent' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-danger" for="absent_{{ $enrollment->id }}">
                                                    <i class="fas fa-times"></i> {{ __('teacher.absent') }}
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="number"
                                                   class="form-control form-control-sm hours-input"
                                                   name="hours_{{ $enrollment->id }}"
                                                   step="0.5"
                                                   min="0"
                                                   max="{{ $session->duration_hours }}"
                                                   value="{{ $currentHours }}"
                                                   placeholder="{{ $session->duration_hours }}">
                                        </td>
                                        <td>
                                            <input type="text"
                                                   class="form-control form-control-sm notes-input"
                                                   name="notes_{{ $enrollment->id }}"
                                                   value="{{ $currentNotes }}"
                                                   placeholder="Optional notes">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sessionId = {{ $session->id }};
    const sessionDuration = {{ $session->duration_hours }};

    // Mark all present
    const markAllPresentBtn = document.getElementById('mark-all-present');
    if (markAllPresentBtn) {
        markAllPresentBtn.addEventListener('click', function() {
            document.querySelectorAll('.student-row').forEach(function(row) {
                const enrollmentId = row.dataset.enrollmentId;
                const presentRadio = document.getElementById(`present_${enrollmentId}`);
                const hoursInput = row.querySelector('.hours-input');
                if (presentRadio) presentRadio.checked = true;
                if (hoursInput) hoursInput.value = sessionDuration;
            });
        });
    }

    // Mark all absent
    const markAllAbsentBtn = document.getElementById('mark-all-absent');
    if (markAllAbsentBtn) {
        markAllAbsentBtn.addEventListener('click', function() {
            document.querySelectorAll('.student-row').forEach(function(row) {
                const enrollmentId = row.dataset.enrollmentId;
                const absentRadio = document.getElementById(`absent_${enrollmentId}`);
                const hoursInput = row.querySelector('.hours-input');
                if (absentRadio) absentRadio.checked = true;
                if (hoursInput) hoursInput.value = 0;
            });
        });
    }

    // Handle status change - update hours
    document.querySelectorAll('.status-input').forEach(function(input) {
        input.addEventListener('change', function() {
            const row = this.closest('.student-row');
            const hoursInput = row.querySelector('.hours-input');
            const status = this.value;

            if (status === 'present') {
                hoursInput.value = sessionDuration;
            } else {
                hoursInput.value = 0;
            }
        });
    });

    // Save all attendance
    const saveAllBtn = document.getElementById('save-all-btn');
    if (saveAllBtn) {
        saveAllBtn.addEventListener('click', function() {
            const attendanceData = [];
            let hasError = false;

            document.querySelectorAll('.student-row').forEach(function(row) {
                const enrollmentId = row.dataset.enrollmentId;
                const userId = row.dataset.userId;
                const statusInput = row.querySelector('.status-input:checked');
                const hoursInput = row.querySelector('.hours-input');
                const notesInput = row.querySelector('.notes-input');

                if (!statusInput) {
                    alert('Please select attendance status for all students');
                    hasError = true;
                    return;
                }

                attendanceData.push({
                    user_id: parseInt(userId),
                    enrollment_id: parseInt(enrollmentId),
                    status: statusInput.value,
                    attended_hours: parseFloat(hoursInput.value) || sessionDuration,
                    notes: notesInput.value || null
                });
            });

            if (hasError) return;

            // Disable button to prevent double submission
            saveAllBtn.disabled = true;
            saveAllBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

            fetch(`/teacher/sessions/${sessionId}/attendance/bulk`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ attendances: attendanceData })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('All attendance saved successfully!');
                    location.reload();
                } else {
                    alert(data.message || 'Failed to save attendance');
                    saveAllBtn.disabled = false;
                    saveAllBtn.innerHTML = '<i class="fas fa-save"></i> Save All Attendance';
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
                saveAllBtn.disabled = false;
                saveAllBtn.innerHTML = '<i class="fas fa-save"></i> Save All Attendance';
            });
        });
    }

    // Close session
    const closeSessionBtn = document.getElementById('close-session-btn');
    if (closeSessionBtn) {
        closeSessionBtn.addEventListener('click', function() {
            if (!confirm('Are you sure you want to close this session? This action cannot be undone.')) {
                return;
            }

            fetch(`/teacher/sessions/${sessionId}/close`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Session closed successfully!');
                    location.reload();
                } else {
                    alert(data.message || 'Failed to close session');
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        });
    }
});
</script>
@endpush

@push('styles')
<style>
    .avatar-circle {
        flex-shrink: 0;
    }

    .student-row:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }

    .btn-check:checked + label {
        font-weight: bold;
    }
</style>
@endpush
@endsection
