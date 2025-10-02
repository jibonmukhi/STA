@extends('layouts.advanced-dashboard')

@section('page-title', __('teacher.my_dashboard'))

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient-primary text-white">
                <div class="card-body">
                    <h3 class="card-title mb-1">{{ __('teacher.welcome') }}, {{ Auth::user()->name }}!</h3>
                    <p class="card-text opacity-75">{{ __('teacher.dashboard_description') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-book"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">{{ __('teacher.total_courses') }}</h6>
                            <h4 class="mb-0 text-primary">{{ $stats['total_courses'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-md bg-success text-white rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">{{ __('teacher.total_students') }}</h6>
                            <h4 class="mb-0 text-success">{{ $stats['total_students'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-md bg-info text-white rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">{{ __('teacher.completed_students') }}</h6>
                            <h4 class="mb-0 text-info">{{ $stats['completed_students'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-md bg-warning text-white rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-certificate"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">{{ __('teacher.certificates_issued') }}</h6>
                            <h4 class="mb-0 text-warning">{{ $stats['certificates_issued'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- My Courses -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('teacher.my_courses') }}</h5>
                    <a href="{{ route('teacher.my-courses') }}" class="btn btn-sm btn-primary">
                        {{ __('teacher.view_all_courses') }}
                    </a>
                </div>
                <div class="card-body">
                    @if($myCourses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('courses.course_title') }}</th>
                                        <th>{{ __('courses.course_code') }}</th>
                                        <th>{{ __('teacher.enrolled_students') }}</th>
                                        <th>{{ __('courses.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($myCourses->take(5) as $course)
                                        <tr>
                                            <td>
                                                <a href="{{ route('teacher.course-students', $course) }}" class="text-decoration-none">
                                                    {{ $course->title }}
                                                </a>
                                            </td>
                                            <td>{{ $course->course_code }}</td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $course->enrolled_students_count }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($course->is_active)
                                                    <span class="badge bg-success">{{ __('courses.active') }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ __('courses.inactive') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-book fa-3x text-muted mb-3"></i>
                            <p class="text-muted">{{ __('teacher.no_courses_yet') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('teacher.upcoming_events') }}</h5>
                    <a href="{{ route('teacher.schedule') }}" class="btn btn-sm btn-outline-primary">
                        {{ __('teacher.view_schedule') }}
                    </a>
                </div>
                <div class="card-body">
                    @if($upcomingEvents->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($upcomingEvents as $event)
                                <div class="list-group-item px-0">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $event->title }}</h6>
                                        <small>{{ $event->start_date->format('M d') }}</small>
                                    </div>
                                    <p class="mb-1 small text-muted">
                                        {{ $event->course->title ?? '' }}
                                    </p>
                                    <small class="text-muted">
                                        <i class="far fa-clock"></i> {{ $event->start_time }}
                                    </small>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="far fa-calendar fa-2x text-muted mb-3"></i>
                            <p class="text-muted small">{{ __('teacher.no_upcoming_events') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Enrollments -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('teacher.recent_enrollments') }}</h5>
                </div>
                <div class="card-body">
                    @if($recentEnrollments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('teacher.student_name') }}</th>
                                        <th>{{ __('courses.course_title') }}</th>
                                        <th>{{ __('teacher.enrolled_date') }}</th>
                                        <th>{{ __('teacher.progress') }}</th>
                                        <th>{{ __('teacher.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentEnrollments as $enrollment)
                                        <tr>
                                            <td>{{ $enrollment->user->full_name ?? 'N/A' }}</td>
                                            <td>{{ $enrollment->course->title ?? 'N/A' }}</td>
                                            <td>{{ $enrollment->enrolled_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar" role="progressbar"
                                                         style="width: {{ $enrollment->progress_percentage }}%"
                                                         aria-valuenow="{{ $enrollment->progress_percentage }}"
                                                         aria-valuemin="0" aria-valuemax="100">
                                                        {{ number_format($enrollment->progress_percentage, 0) }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{!! $enrollment->status_badge !!}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-user-plus fa-3x text-muted mb-3"></i>
                            <p class="text-muted">{{ __('teacher.no_recent_enrollments') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
