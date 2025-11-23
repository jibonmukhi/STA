@extends('layouts.advanced-dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>{{ $course->title }} - {{ __('teacher.session_attendance') }}</h2>
            <p class="text-muted mb-0">{{ __('teacher.course_code') }}: {{ $course->course_code }}</p>
        </div>
        <a href="{{ route('teacher.course-details', $course) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> {{ __('teacher.back_to_course') }}
        </a>
    </div>

    @if($sessions->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> {{ __('teacher.no_sessions_created') }}
        </div>
    @elseif($enrollments->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> {{ __('teacher.no_students_enrolled_attendance') }}
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover attendance-grid">
                        <thead>
                            <tr>
                                <th class="sticky-col first-col" style="min-width: 200px;">{{ __('teacher.student_name') }}</th>
                                @foreach($sessions as $session)
                                    <th class="text-center session-header" style="min-width: 120px;">
                                        <div class="session-info">
                                            <div class="fw-bold">{{ $session->session_title }}</div>
                                            <small class="text-muted">{{ $session->session_date->format('d/m/Y') }}</small>
                                            <small class="d-block text-muted">{{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }}</small>
                                            <small class="d-block">
                                                <span class="badge bg-{{ $session->status === 'completed' ? 'success' : 'secondary' }}">
                                                    {{ ucfirst($session->status) }}
                                                </span>
                                            </small>
                                        </div>
                                    </th>
                                @endforeach
                                <th class="text-center" style="min-width: 150px;">{{ __('teacher.progress') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enrollments as $enrollment)
                                <tr>
                                    <td class="sticky-col first-col">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                    {{ strtoupper(substr($enrollment->user->name, 0, 1)) }}
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $enrollment->user->name }}</div>
                                                <small class="text-muted">{{ $enrollment->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    @foreach($sessions as $session)
                                        @php
                                            $attendance = $attendanceMatrix[$enrollment->id][$session->id] ?? null;
                                            $status = $attendance->status ?? 'not_marked';
                                            $statusConfig = [
                                                'present' => ['icon' => 'fa-check', 'color' => 'success', 'text' => __('teacher.present')],
                                                'absent' => ['icon' => 'fa-times', 'color' => 'danger', 'text' => __('teacher.absent')],
                                                'excused' => ['icon' => 'fa-minus-circle', 'color' => 'warning', 'text' => __('teacher.excused')],
                                                'late' => ['icon' => 'fa-clock', 'color' => 'orange', 'text' => __('teacher.late')],
                                                'not_marked' => ['icon' => 'fa-question', 'color' => 'secondary', 'text' => __('teacher.not_marked')]
                                            ];
                                            $config = $statusConfig[$status];
                                        @endphp
                                        <td class="text-center attendance-cell"
                                            data-session-id="{{ $session->id }}"
                                            data-session-title="{{ $session->session_title }}"
                                            data-student-name="{{ $enrollment->user->name }}"
                                            data-enrollment-id="{{ $enrollment->id }}"
                                            data-user-id="{{ $enrollment->user_id }}"
                                            data-current-status="{{ $status }}"
                                            style="cursor: pointer;">
                                            <div class="attendance-status bg-{{ $config['color'] }} bg-opacity-10 p-2 rounded">
                                                <i class="fas {{ $config['icon'] }} text-{{ $config['color'] }} fa-lg"></i>
                                                <div class="small mt-1">{{ $config['text'] }}</div>
                                                @if($attendance && $attendance->attended_hours > 0)
                                                    <small class="text-muted d-block">{{ $attendance->attended_hours }}h</small>
                                                @endif
                                            </div>
                                        </td>
                                    @endforeach
                                    <td class="text-center">
                                        <div class="progress" style="height: 25px;">
                                            <div class="progress-bar bg-{{ $enrollment->progress_percentage >= 100 ? 'success' : ($enrollment->progress_percentage >= 50 ? 'info' : 'warning') }}"
                                                 role="progressbar"
                                                 style="width: {{ $enrollment->progress_percentage }}%"
                                                 aria-valuenow="{{ $enrollment->progress_percentage }}"
                                                 aria-valuemin="0"
                                                 aria-valuemax="100">
                                                {{ number_format($enrollment->progress_percentage, 0) }}%
                                            </div>
                                        </div>
                                        <small class="text-muted d-block mt-1">
                                            {{ $enrollment->sessions_attended }}/{{ $enrollment->total_sessions }} {{ __('teacher.sessions_attended') }}
                                        </small>
                                        <small class="text-muted d-block">
                                            {{ number_format($enrollment->attended_hours, 1) }}{{ __('teacher.hours') }} / {{ number_format($enrollment->total_required_hours, 1) }}{{ __('teacher.hours') }}
                                        </small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row mt-4">
                    @foreach($sessions as $session)
                        @php
                            $stats = $session->getAttendanceStats();
                        @endphp
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $session->session_title }}</h6>
                                    <p class="small text-muted mb-2">{{ $session->session_date->format('d/m/Y') }}</p>

                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <div class="text-center p-2 bg-success bg-opacity-10 rounded">
                                                <div class="fw-bold text-success">{{ $stats['present'] }}</div>
                                                <small class="text-muted">{{ __('teacher.present') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-2 bg-danger bg-opacity-10 rounded">
                                                <div class="fw-bold text-danger">{{ $stats['absent'] }}</div>
                                                <small class="text-muted">{{ __('teacher.absent') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-2 bg-warning bg-opacity-10 rounded">
                                                <div class="fw-bold text-warning">{{ $stats['excused'] }}</div>
                                                <small class="text-muted">{{ __('teacher.excused') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-2 bg-orange bg-opacity-10 rounded">
                                                <div class="fw-bold text-orange">{{ $stats['late'] }}</div>
                                                <small class="text-muted">{{ __('teacher.late') }}</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="progress mb-3" style="height: 20px;">
                                        <div class="progress-bar bg-info"
                                             role="progressbar"
                                             style="width: {{ $stats['total_enrolled'] > 0 ? ($stats['marked'] / $stats['total_enrolled'] * 100) : 0 }}%">
                                            {{ $stats['marked'] }}/{{ $stats['total_enrolled'] }} {{ __('teacher.marked') }}
                                        </div>
                                    </div>

                                    <a href="{{ route('teacher.session-attendance-detail', $session) }}" class="btn btn-warning btn-sm w-100 mb-2">
                                        <i class="fas fa-clipboard-check"></i> {{ __('teacher.mark_attendance') }}
                                    </a>

                                    @if($session->status === 'completed')
                                        <button class="btn btn-success btn-sm w-100" disabled>
                                            <i class="fas fa-check"></i> {{ __('teacher.session_completed') }}
                                        </button>
                                    @elseif($session->canBeClosed())
                                        <button class="btn btn-primary btn-sm w-100 close-session-btn"
                                                data-session-id="{{ $session->id }}"
                                                data-session-title="{{ $session->session_title }}">
                                            <i class="fas fa-lock"></i> {{ __('teacher.close_session') }}
                                        </button>
                                    @else
                                        <button class="btn btn-secondary btn-sm w-100" disabled>
                                            <i class="fas fa-exclamation-circle"></i> {{ $stats['not_marked'] }} {{ __('teacher.remaining') }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Attendance Status Modal -->
<div class="modal fade" id="attendanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('teacher.mark_attendance_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modal-session-id">
                <input type="hidden" id="modal-enrollment-id">
                <input type="hidden" id="modal-user-id">

                <div class="mb-3">
                    <label class="form-label">{{ __('teacher.student') }}:</label>
                    <div id="modal-student-name" class="fw-bold"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('teacher.session') }}:</label>
                    <div id="modal-session-title" class="fw-bold"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('teacher.attendance_status') }}:</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="attendance-status" id="status-present" value="present">
                        <label class="btn btn-outline-success" for="status-present">
                            <i class="fas fa-check"></i> {{ __('teacher.present') }}
                        </label>

                        <input type="radio" class="btn-check" name="attendance-status" id="status-late" value="late">
                        <label class="btn btn-outline-warning" for="status-late">
                            <i class="fas fa-clock"></i> {{ __('teacher.late') }}
                        </label>

                        <input type="radio" class="btn-check" name="attendance-status" id="status-absent" value="absent">
                        <label class="btn btn-outline-danger" for="status-absent">
                            <i class="fas fa-times"></i> {{ __('teacher.absent') }}
                        </label>

                        <input type="radio" class="btn-check" name="attendance-status" id="status-excused" value="excused">
                        <label class="btn btn-outline-info" for="status-excused">
                            <i class="fas fa-minus-circle"></i> {{ __('teacher.excused') }}
                        </label>
                    </div>
                </div>

                <div class="mb-3" id="hours-input-group">
                    <label class="form-label">{{ __('teacher.hours_attended') }}:</label>
                    <input type="number" class="form-control" id="attended-hours" step="0.5" min="0">
                    <small class="text-muted">{{ __('teacher.leave_empty_default') }}</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('teacher.notes') }} ({{ __('teacher.optional') }}):</label>
                    <textarea class="form-control" id="attendance-notes" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('teacher.cancel') }}</button>
                <button type="button" class="btn btn-primary" id="save-attendance-btn">{{ __('teacher.save_attendance') }}</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .attendance-grid {
        font-size: 0.9rem;
    }

    .sticky-col {
        position: sticky;
        background-color: #fff;
        z-index: 10;
    }

    .first-col {
        left: 0;
    }

    .session-header {
        background-color: #f8f9fa;
        vertical-align: middle;
    }

    .attendance-cell:hover {
        opacity: 0.8;
        transform: scale(1.05);
        transition: all 0.2s;
    }

    .attendance-status {
        min-height: 80px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .text-orange {
        color: #fd7e14;
    }

    .bg-orange {
        background-color: #fd7e14;
    }

    .avatar-sm {
        flex-shrink: 0;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    const attendanceModal = new bootstrap.Modal(document.getElementById('attendanceModal'));

    // Handle attendance cell click
    $('.attendance-cell').on('click', function() {
        const sessionId = $(this).data('session-id');
        const sessionTitle = $(this).data('session-title');
        const studentName = $(this).data('student-name');
        const enrollmentId = $(this).data('enrollment-id');
        const userId = $(this).data('user-id');
        const currentStatus = $(this).data('current-status');

        // Populate modal
        $('#modal-session-id').val(sessionId);
        $('#modal-enrollment-id').val(enrollmentId);
        $('#modal-user-id').val(userId);
        $('#modal-student-name').text(studentName);
        $('#modal-session-title').text(sessionTitle);

        // Set current status if marked
        if (currentStatus !== 'not_marked') {
            $(`input[name="attendance-status"][value="${currentStatus}"]`).prop('checked', true);
        } else {
            $('input[name="attendance-status"]').prop('checked', false);
        }

        // Clear inputs
        $('#attended-hours').val('');
        $('#attendance-notes').val('');

        attendanceModal.show();
    });

    // Show/hide hours input based on status
    $('input[name="attendance-status"]').on('change', function() {
        const status = $(this).val();
        if (status === 'present' || status === 'late') {
            $('#hours-input-group').show();
        } else {
            $('#hours-input-group').hide();
            $('#attended-hours').val('');
        }
    });

    // Save attendance
    $('#save-attendance-btn').on('click', function() {
        const sessionId = $('#modal-session-id').val();
        const enrollmentId = $('#modal-enrollment-id').val();
        const userId = $('#modal-user-id').val();
        const status = $('input[name="attendance-status"]:checked').val();
        const attendedHours = $('#attended-hours').val();
        const notes = $('#attendance-notes').val();

        if (!status) {
            alert('Please select an attendance status');
            return;
        }

        $.ajax({
            url: `/teacher/sessions/${sessionId}/attendance`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                user_id: userId,
                enrollment_id: enrollmentId,
                status: status,
                attended_hours: attendedHours || null,
                notes: notes || null
            },
            success: function(response) {
                if (response.success) {
                    // Reload page to show updated attendance
                    location.reload();
                } else {
                    alert(response.message || 'Failed to save attendance');
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'An error occurred';
                alert('Error: ' + errorMsg);
            }
        });
    });

    // Close session
    $('.close-session-btn').on('click', function() {
        const sessionId = $(this).data('session-id');
        const sessionTitle = $(this).data('session-title');

        if (!confirm(`Are you sure you want to close the session "${sessionTitle}"? This action cannot be undone.`)) {
            return;
        }

        $.ajax({
            url: `/teacher/sessions/${sessionId}/close`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert('Session closed successfully');
                    location.reload();
                } else {
                    alert(response.message || 'Failed to close session');
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'An error occurred';
                alert('Error: ' + errorMsg);
            }
        });
    });
});
</script>
@endpush
@endsection
