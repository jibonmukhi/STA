@extends('layouts.advanced-dashboard')

@section('title', trans('courses.course_schedule'))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>{{ trans('courses.course_schedule') }}</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ trans('courses.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('courses.planning') }}">{{ trans('courses.course_planning') }}</a></li>
                            <li class="breadcrumb-item active">{{ trans('courses.schedule') }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('courses.planning') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> {{ trans('courses.back_to_planning') }}
                    </a>
                    <a href="{{ route('calendar') }}" class="btn btn-outline-info">
                        <i class="fas fa-calendar"></i> {{ trans('courses.view_calendar') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Info Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-graduation-cap me-2"></i>
                        {{ $course->title }}
                        <span class="badge bg-primary ms-2">{{ $course->course_code }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>{{ trans('courses.instructor') }}:</strong><br>
                            {{ $course->instructor ?: trans('courses.n_a') }}
                        </div>
                        <div class="col-md-3">
                            <strong>{{ trans('courses.duration') }}:</strong><br>
                            {{ $course->duration_hours }} {{ trans('courses.hours') }}
                        </div>
                        <div class="col-md-3">
                            <strong>{{ trans('courses.level') }}:</strong><br>
                            {{ trans('courses.levels.' . $course->level) }}
                        </div>
                        <div class="col-md-3">
                            <strong>{{ trans('courses.delivery_method') }}:</strong><br>
                            {{ trans('courses.delivery_methods.' . $course->delivery_method) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scheduled Sessions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>
                        {{ trans('courses.scheduled_sessions') }}
                        <span class="badge bg-secondary ms-2">{{ $events->count() }} {{ trans('courses.sessions') }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if($events->count() > 0)
                        <div class="row">
                            @foreach($events as $event)
                                <div class="col-lg-6 col-md-12 mb-3">
                                    <div class="card border h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-1">{{ $event->title }}</h6>
                                                <span class="badge bg-{{ $event->status == 'scheduled' ? 'success' : ($event->status == 'completed' ? 'primary' : 'warning') }}">
                                                    {{ trans('courses.' . $event->status) }}
                                                </span>
                                            </div>

                                            @if($event->description)
                                                <p class="text-muted small mb-2">{{ $event->description }}</p>
                                            @endif

                                            <div class="row text-sm mb-2">
                                                <div class="col-6">
                                                    <strong>{{ trans('courses.start_date') }}:</strong><br>
                                                    {{ $event->start_date->format('M d, Y') }}
                                                </div>
                                                <div class="col-6">
                                                    <strong>{{ trans('courses.start_time') }}:</strong><br>
                                                    {{ $event->start_time }} - {{ $event->end_time }}
                                                </div>
                                            </div>

                                            @if($event->location)
                                                <div class="mb-2">
                                                    <strong>{{ trans('courses.location') }}:</strong><br>
                                                    {{ $event->location }}
                                                </div>
                                            @endif

                                            @if($event->max_participants)
                                                <div class="mb-2">
                                                    <strong>{{ trans('courses.max_participants') }}:</strong> {{ $event->max_participants }}<br>
                                                    <strong>{{ trans('courses.registered_participants') }}:</strong> {{ $event->registered_participants }}<br>
                                                    <strong>{{ trans('courses.available_spots') }}:</strong> {{ $event->max_participants - $event->registered_participants }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <a href="{{ route('calendar') }}?date={{ $event->start_date->format('Y-m-d') }}"
                                               class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i> {{ trans('courses.view_in_calendar') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">{{ trans('courses.no_events_scheduled') }}</h5>
                            <p class="text-muted">{{ trans('courses.no_events_message') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}
</style>
@endsection