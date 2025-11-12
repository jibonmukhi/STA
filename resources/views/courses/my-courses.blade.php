@extends('layouts.advanced-dashboard')

@section('title', 'My Courses')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>My Courses</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">My Courses</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('course-catalog') }}" class="btn btn-primary">
                        <i class="fas fa-book"></i> Browse Course Catalog
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Enrolled</h5>
                    <h2 class="mb-0">{{ $stats['total'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">In Progress</h5>
                    <h2 class="mb-0">{{ $stats['in_progress'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Completed</h5>
                    <h2 class="mb-0">{{ $stats['completed'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h5 class="card-title">Avg Progress</h5>
                    <h2 class="mb-0">{{ number_format($stats['average_progress'], 1) }}%</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- My Courses List -->
    <div class="row">
        @if($enrollments->count() > 0)
            @foreach($enrollments as $enrollment)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0">{{ $enrollment->course->title }}</h5>
                            @php
                                $statusColors = [
                                    'enrolled' => 'secondary',
                                    'in_progress' => 'info',
                                    'completed' => 'success',
                                    'dropped' => 'warning',
                                    'failed' => 'danger'
                                ];
                                $color = $statusColors[$enrollment->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $enrollment->status)) }}</span>
                        </div>

                        <p class="text-muted mb-2">
                            <small><i class="fas fa-code"></i> {{ $enrollment->course->course_code }}</small>
                        </p>

                        @if($enrollment->course->description)
                            <p class="card-text text-muted small">
                                {{ Str::limit($enrollment->course->description, 100) }}
                            </p>
                        @endif

                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-muted">Progress</small>
                                <small class="text-muted">{{ $enrollment->progress_percentage }}%</small>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar"
                                     style="width: {{ $enrollment->progress_percentage }}%;"
                                     aria-valuenow="{{ $enrollment->progress_percentage }}"
                                     aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>

                        <div class="row text-center mb-3">
                            <div class="col-6">
                                <small class="text-muted d-block">Duration</small>
                                <strong>{{ $enrollment->course->duration_hours }}h</strong>
                            </div>
                            @if($enrollment->course->teacher)
                            <div class="col-6">
                                <small class="text-muted d-block">Teacher</small>
                                <strong>{{ $enrollment->course->teacher->name }}</strong>
                            </div>
                            @endif
                        </div>

                        @if($enrollment->final_score || $enrollment->grade)
                        <div class="alert alert-success mb-3">
                            @if($enrollment->final_score)
                                <strong>Score:</strong> {{ $enrollment->final_score }}/100<br>
                            @endif
                            @if($enrollment->grade)
                                <strong>Grade:</strong> {{ $enrollment->grade }}
                            @endif
                        </div>
                        @endif

                        <div class="d-grid">
                            <a href="{{ route('courses.show', $enrollment->course) }}" class="btn btn-primary">
                                <i class="fas fa-eye"></i> View Course
                            </a>
                        </div>

                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-calendar"></i> Enrolled: {{ $enrollment->enrolled_at->format('M d, Y') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            <div class="col-12">
                <div class="d-flex justify-content-center mt-4">
                    {{ $enrollments->links() }}
                </div>
            </div>
        @else
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-graduation-cap fa-4x text-muted mb-3"></i>
                        <h4>You're not enrolled in any courses yet</h4>
                        <p class="text-muted">Browse our course catalog to find courses that interest you.</p>
                        <a href="{{ route('course-catalog') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-book"></i> Browse Course Catalog
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
