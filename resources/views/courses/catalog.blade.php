@extends('layouts.advanced-dashboard')

@section('title', 'Course Catalog')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Course Catalog</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Course Catalog</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('my-courses') }}" class="btn btn-outline-primary">
                        <i class="fas fa-graduation-cap"></i> My Courses
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Courses -->
    <div class="row">
        @if($courses->count() > 0)
            @foreach($courses as $course)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0">{{ $course->title }}</h5>
                            @if($course->is_mandatory)
                                <span class="badge bg-warning">Mandatory</span>
                            @endif
                        </div>

                        <p class="text-muted mb-2">
                            <small><i class="fas fa-code"></i> {{ $course->course_code }}</small>
                        </p>

                        @if($course->description)
                            <p class="card-text text-muted small mb-3">
                                {{ Str::limit($course->description, 120) }}
                            </p>
                        @endif

                        <div class="mb-3">
                            <span class="badge bg-info me-1">{{ App\Models\Course::getCategories()[$course->category] ?? $course->category }}</span>
                            <span class="badge bg-primary me-1">{{ ucfirst($course->level) }}</span>
                            <span class="badge bg-secondary">{{ App\Models\Course::getDeliveryMethods()[$course->delivery_method] ?? $course->delivery_method }}</span>
                        </div>

                        <div class="row text-center mb-3">
                            <div class="col-4">
                                <small class="text-muted d-block">Duration</small>
                                <strong>{{ $course->duration_hours }}h</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Credits</small>
                                <strong>{{ $course->credits ?: 'N/A' }}</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Price</small>
                                <strong>${{ number_format($course->price, 0) }}</strong>
                            </div>
                        </div>

                        @if($course->teacher)
                        <div class="mb-3">
                            <small class="text-muted d-block">Instructor</small>
                            <strong>{{ $course->teacher->name }}</strong>
                        </div>
                        @endif

                        @if($course->max_participants)
                        <div class="alert alert-info py-2 mb-3">
                            <small>
                                <i class="fas fa-users"></i>
                                {{ $course->enrolled_students_count }} / {{ $course->max_participants }} enrolled
                            </small>
                        </div>
                        @endif

                        <div class="d-grid gap-2">
                            <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-primary">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                            @php
                                $canEnroll = true;
                                if ($course->max_participants) {
                                    $canEnroll = $course->enrolled_students_count < $course->max_participants;
                                }
                            @endphp
                            @if($canEnroll)
                                <form action="{{ route('courses.enroll', $course) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-user-plus"></i> Enroll Now
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-secondary w-100" disabled>
                                    <i class="fas fa-ban"></i> Course Full
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            <div class="col-12">
                <div class="d-flex justify-content-center mt-4">
                    {{ $courses->links() }}
                </div>
            </div>
        @else
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
                        <h4>No Available Courses</h4>
                        <p class="text-muted">You are already enrolled in all available courses.</p>
                        <a href="{{ route('my-courses') }}" class="btn btn-primary">
                            <i class="fas fa-graduation-cap"></i> View My Courses
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
