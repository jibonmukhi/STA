@extends('layouts.advanced-dashboard')

@section('page-title', $course->title)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>{{ $course->title }}</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('teacher.my_dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('teacher.my-courses') }}">{{ __('teacher.my_courses') }}</a></li>
                            <li class="breadcrumb-item active">{{ $course->title }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('teacher.my-courses') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('teacher.back_to_courses') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col">
                            <a href="{{ route('teacher.course-students', $course) }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-users"></i> {{ __('teacher.view_students') }}
                            </a>
                        </div>
                        <div class="col">
                            <a href="{{ route('teacher.session-attendance', $course) }}" class="btn btn-outline-warning w-100">
                                <i class="fas fa-clipboard-check"></i> {{ __('teacher.manage_attendance') }}
                            </a>
                        </div>
                        <div class="col">
                            <a href="{{ route('courses.schedule', $course) }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-clock"></i> {{ __('teacher.view_schedule') }}
                            </a>
                        </div>
                        <div class="col">
                            <a href="{{ route('teacher.schedule') }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-calendar-alt"></i> {{ __('teacher.my_schedule') }}
                            </a>
                        </div>
                        <div class="col">
                            <a href="{{ route('teacher.course-certificates', $course) }}" class="btn btn-warning w-100">
                                <i class="fas fa-award"></i> {{ __('teacher.view_certificates') }}
                            </a>
                        </div>
                        <div class="col">
                            <form action="{{ route('teacher.generate-certificates', $course) }}" method="POST" class="w-100">
                                @csrf
                                <button type="submit" class="btn btn-danger w-100" onclick="return confirm('{{ __('teacher.generate_certificates_confirm') }}')">
                                    <i class="fas fa-certificate"></i> {{ __('teacher.generate_certificates') }}
                                </button>
                            </form>
                        </div>
                        @php
                            $totalSessions = $course->sessions()->count();
                            $completedSessions = $course->sessions()->where('status', 'completed')->count();
                            $canClose = $totalSessions > 0 && $completedSessions === $totalSessions && $course->status !== 'done';
                        @endphp
                        @if($canClose)
                            <div class="col">
                                <button type="button" class="btn btn-success w-100" onclick="confirmCloseCourseDetail({{ $course->id }}, '{{ addslashes($course->title) }}')">
                                    <i class="fas fa-check-circle"></i> {{ __('teacher.close_course') }}
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Course Details -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-book"></i> {{ __('teacher.course_details') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>{{ __('teacher.course_code') }}:</strong>
                        </div>
                        <div class="col-sm-9">
                            <span class="badge bg-secondary" style="font-size: 0.95rem;">{{ $course->course_code }}</span>
                        </div>
                    </div>

                    @if($course->description)
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>{{ __('teacher.description') }}:</strong>
                            </div>
                            <div class="col-sm-9">
                                <p class="mb-0">{{ $course->description }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>{{ __('teacher.category') }}:</strong>
                        </div>
                        <div class="col-sm-9">
                            @php
                                $categoryColor = dataVaultColor('course_category', $course->category) ?? 'info';
                                $categoryLabel = dataVaultLabel('course_category', $course->category) ?? $course->category;
                            @endphp
                            <span class="badge bg-{{ $categoryColor }}">{{ $categoryLabel }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>{{ __('teacher.participation') }}:</strong>
                        </div>
                        <div class="col-sm-9">
                            <span class="badge bg-secondary">{{ __('teacher.delivery_methods.' . $course->delivery_method) }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>{{ __('teacher.status') }}:</strong>
                        </div>
                        <div class="col-sm-9">
                            @php
                                $statusColor = dataVaultColor('course_status', $course->status) ?? 'secondary';
                                $statusIcon = dataVaultIcon('course_status', $course->status) ?? 'fas fa-circle';
                                $statusLabel = dataVaultLabel('course_status', $course->status) ?? ucfirst($course->status);
                            @endphp
                            <span class="badge bg-{{ $statusColor }}">
                                <i class="{{ $statusIcon }}"></i> {{ $statusLabel }}
                            </span>
                        </div>
                    </div>

                    @if($course->teachers->count() > 0)
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>{{ __('teacher.assigned_teachers') }}:</strong>
                            </div>
                            <div class="col-sm-9">
                                @foreach($course->teachers as $teacher)
                                    <div class="card bg-light border-0 mb-2">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $teacher->photo_url }}" alt="{{ $teacher->full_name }}"
                                                     class="rounded-circle me-3" style="width: 48px; height: 48px; object-fit: cover; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <i class="fas fa-chalkboard-teacher text-primary me-2"></i>
                                                        <strong class="mb-0">{{ $teacher->full_name }}</strong>
                                                    </div>
                                                    <small class="text-muted d-block">
                                                        <i class="fas fa-envelope me-1"></i>{{ $teacher->email }}
                                                    </small>
                                                    @if($teacher->phone)
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-phone me-1"></i>{{ $teacher->phone }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($course->assignedCompanies && $course->assignedCompanies->count() > 0)
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>{{ $course->assignedCompanies->count() > 1 ? __('teacher.companies') : __('teacher.company') }}:</strong>
                            </div>
                            <div class="col-sm-9">
                                @foreach($course->assignedCompanies as $assignedCompany)
                                    <a href="{{ route('teacher.company.show', $assignedCompany) }}" class="badge bg-info text-decoration-none" style="font-size: 0.875rem; padding: 0.5rem 0.75rem;" title="{{ __('teacher.view_company_details') }}">
                                        <i class="fas fa-building me-1"></i>{{ $assignedCompany->name }}
                                        @if($assignedCompany->pivot->is_mandatory)
                                            <span class="badge bg-warning ms-1">{{ __('teacher.mandatory') }}</span>
                                        @endif
                                    </a>
                                    @if(!$loop->last)<br class="mb-2">@endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($course->start_date || $course->end_date)
                        <hr class="my-4">
                        <h6 class="mb-3"><i class="fas fa-clock"></i> {{ __('teacher.course_schedule') }}</h6>

                        @if($course->start_date)
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <strong>{{ __('teacher.start_date') }}:</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ $course->start_date->format('d/m/Y') }}
                                    @if($course->start_time)
                                        {{ __('teacher.at') }} {{ \Carbon\Carbon::parse($course->start_time)->format('H:i') }}
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($course->end_date)
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <strong>{{ __('teacher.end_date') }}:</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ $course->end_date->format('d/m/Y') }}
                                    @if($course->end_time)
                                        {{ __('teacher.at') }} {{ \Carbon\Carbon::parse($course->end_time)->format('H:i') }}
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Enrolled Students Summary -->
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-users"></i> {{ __('teacher.enrolled_students') }}
                        <span class="badge bg-white text-primary ms-2">{{ $stats['total_enrolled'] }}</span>
                    </h5>
                    <a href="{{ route('teacher.course-students', $course) }}" class="btn btn-sm btn-light">
                        <i class="fas fa-list"></i> {{ __('teacher.view_all') }}
                    </a>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="border-end">
                                <h3 class="text-primary mb-1">{{ $stats['total_enrolled'] }}</h3>
                                <small class="text-muted">{{ __('teacher.total_enrolled') }}</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border-end">
                                <h3 class="text-info mb-1">{{ $stats['in_progress'] }}</h3>
                                <small class="text-muted">{{ __('teacher.in_progress') }}</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h3 class="text-success mb-1">{{ $stats['completed'] }}</h3>
                            <small class="text-muted">{{ __('teacher.completed') }}</small>
                        </div>
                    </div>

                    @if($stats['total_enrolled'] > 0)
                        <hr class="my-3">
                        <div class="text-center">
                            <small class="text-muted">{{ __('teacher.average_progress') }}</small>
                            <div class="progress mt-2" style="height: 25px;">
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $stats['average_progress'] }}%;"
                                     aria-valuenow="{{ $stats['average_progress'] }}" aria-valuemin="0" aria-valuemax="100">
                                    <strong>{{ round($stats['average_progress']) }}%</strong>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Class Sessions Section -->
            @if($course->sessions->count() > 0)
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt"></i> {{ __('teacher.class_sessions') }}
                    </h5>
                    <span class="badge bg-white text-primary">{{ $course->sessions->count() }}</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">{{ __('teacher.total_hours') }}</small>
                        <strong>{{ $course->sessions->sum('duration_hours') }} {{ __('teacher.hours') }}</strong>
                    </div>

                    <div class="list-group session-list">
                        @foreach($course->sessions->take(5) as $session)
                        @php
                            $isCompleted = $session->status === 'completed';
                            $isPast = $session->session_date < now();
                            $isToday = $session->session_date->isToday();
                            $isFuture = $session->session_date > now();
                        @endphp
                        <a href="{{ route('teacher.session-attendance-detail', $session) }}" class="list-group-item list-group-item-action session-item {{ $isToday ? 'session-today' : '' }} {{ $isCompleted ? 'session-completed' : '' }}" style="text-decoration: none; color: inherit;">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="session-number me-2">{{ $loop->iteration }}</span>
                                        <h6 class="mb-0 session-title">{{ $session->session_title }}</h6>
                                    </div>
                                    <div class="session-details">
                                        <small class="d-flex align-items-center mb-1">
                                            <i class="fas fa-calendar text-primary me-2" style="width: 16px;"></i>
                                            <span class="fw-medium">{{ $session->session_date->format('d/m/Y') }}</span>
                                            @if($isToday)
                                                <span class="badge bg-warning ms-2 pulse">{{ __('teacher.today') }}</span>
                                            @endif
                                        </small>
                                        <small class="d-flex align-items-center mb-1">
                                            <i class="fas fa-clock text-success me-2" style="width: 16px;"></i>
                                            <span>{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}</span>
                                        </small>
                                        <small class="d-flex align-items-center">
                                            <i class="fas fa-hourglass-half text-info me-2" style="width: 16px;"></i>
                                            <span>{{ $session->duration_hours }} {{ __('teacher.hours') }}</span>
                                        </small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    @if($isCompleted)
                                        <span class="badge bg-success mb-2">
                                            <i class="fas fa-check"></i> {{ __('teacher.completed') }}
                                        </span>
                                    @elseif($isToday)
                                        <span class="badge bg-warning mb-2">
                                            <i class="fas fa-star"></i> {{ __('teacher.today') }}
                                        </span>
                                    @elseif($isPast)
                                        <span class="badge bg-secondary mb-2">
                                            <i class="fas fa-history"></i> {{ __('teacher.past') }}
                                        </span>
                                    @else
                                        <span class="badge bg-info mb-2">
                                            <i class="fas fa-clock"></i> {{ __('teacher.upcoming') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>

                    @if($course->sessions->count() > 5)
                        <div class="text-center mt-3">
                            <a href="{{ route('courses.schedule', $course) }}" class="btn btn-sm btn-outline-primary">
                                {{ __('teacher.view_all_sessions') }} ({{ $course->sessions->count() }})
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            @else
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> {{ __('teacher.course_info') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <small class="text-muted d-block">{{ __('teacher.hours') }}</small>
                            <strong>{{ $course->duration_hours }} {{ __('teacher.hours') }}</strong>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <small class="text-muted d-block">{{ __('teacher.status') }}</small>
                            @php
                                $statusColor = dataVaultColor('course_status', $course->status) ?? 'secondary';
                                $statusIcon = dataVaultIcon('course_status', $course->status) ?? 'fas fa-circle';
                                $statusLabel = dataVaultLabel('course_status', $course->status) ?? ucfirst($course->status);
                            @endphp
                            <span class="badge bg-{{ $statusColor }}">
                                <i class="{{ $statusIcon }}"></i> {{ $statusLabel }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Course Materials -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-folder"></i> {{ __('teacher.course_materials') }}</h5>
                </div>
                <div class="card-body">
                    @if($course->materials->count() > 0)
                        <div class="list-group">
                            @foreach($course->materials as $material)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $material->title }}</h6>
                                        <small class="text-muted d-block">
                                            <span class="badge bg-info">{{ ucfirst($material->material_type) }}</span>
                                            {{ $material->file_size_formatted }}
                                        </small>
                                    </div>
                                    @if($material->is_downloadable)
                                        <a href="{{ route('course-materials.download', $material) }}" class="btn btn-sm btn-success" title="{{ __('teacher.download') }}">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-3 mb-0">{{ __('teacher.no_materials') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Gradient Card Headers */
.card-header.bg-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    border: none;
}

.card-header.bg-primary.text-white {
    color: #ffffff !important;
}

.list-group-item {
    border-left: 3px solid transparent;
    transition: all 0.2s;
}

.list-group-item:hover {
    border-left-color: #667eea;
    background-color: rgba(102, 126, 234, 0.05);
}

/* Session List Enhancements */
.session-list .session-item {
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
    padding: 1rem;
    margin-bottom: 0.5rem;
    border-radius: 0.375rem;
    cursor: pointer;
}

.session-list .session-item:hover {
    border-left-color: #667eea;
    background-color: rgba(102, 126, 234, 0.05);
    transform: translateX(5px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    text-decoration: none !important;
}

.session-today {
    border-left-color: #ffc107 !important;
    background-color: rgba(255, 193, 7, 0.1);
    box-shadow: 0 2px 8px rgba(255, 193, 7, 0.2);
}

.session-today:hover {
    background-color: rgba(255, 193, 7, 0.15);
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
}

.session-completed {
    opacity: 0.75;
}

.session-completed .session-title {
    text-decoration: line-through;
    color: #6c757d;
}

.session-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: bold;
    font-size: 0.875rem;
    flex-shrink: 0;
}

.session-title {
    font-size: 1rem;
    font-weight: 600;
    color: #2d3748;
}

.session-details {
    margin-left: 38px;
}

.session-details small {
    color: #718096;
}

.session-details .fw-medium {
    font-weight: 500;
    color: #2d3748;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.7;
        transform: scale(1.05);
    }
}

.pulse {
    animation: pulse 2s infinite ease-in-out;
}
</style>

<!-- Close Course Confirmation Modal -->
<div class="modal fade" id="closeCourseModal" tabindex="-1" aria-labelledby="closeCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="closeCourseModalLabel">{{ __('teacher.close_course_confirmation') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('teacher.close_course_message') }}</p>
                <p><strong id="courseTitle"></strong></p>
                <p class="text-muted">{{ __('teacher.close_course_note') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('teacher.cancel') }}</button>
                <form id="closeCourseForm" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success">{{ __('teacher.close_course_confirm') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmCloseCourseDetail(courseId, courseTitle) {
    // Set the course title in the modal
    document.getElementById('courseTitle').textContent = courseTitle;

    // Set the form action URL
    const form = document.getElementById('closeCourseForm');
    form.action = '/teacher/courses/' + courseId + '/close';

    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('closeCourseModal'));
    modal.show();
}
</script>
@endpush
