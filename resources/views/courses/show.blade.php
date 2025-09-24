@extends('layouts.advanced-dashboard')

@section('title', $course->title)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>{{ $course->title }}</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Courses</a></li>
                            <li class="breadcrumb-item active">{{ $course->title }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('courses.edit', $course) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Course
                    </a>
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Course Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>Course Code:</strong>
                        </div>
                        <div class="col-sm-9">
                            {{ $course->course_code }}
                        </div>
                    </div>

                    @if($course->description)
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>Description:</strong>
                            </div>
                            <div class="col-sm-9">
                                <p>{{ $course->description }}</p>
                            </div>
                        </div>
                    @endif

                    @if($course->objectives)
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>Objectives:</strong>
                            </div>
                            <div class="col-sm-9">
                                <p>{{ $course->objectives }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>Category:</strong>
                        </div>
                        <div class="col-sm-9">
                            <span class="badge bg-info">{{ App\Models\Course::getCategories()[$course->category] ?? $course->category }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>Level:</strong>
                        </div>
                        <div class="col-sm-9">
                            <span class="badge bg-primary">{{ ucfirst($course->level) }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>Delivery Method:</strong>
                        </div>
                        <div class="col-sm-9">
                            <span class="badge bg-secondary">{{ App\Models\Course::getDeliveryMethods()[$course->delivery_method] ?? $course->delivery_method }}</span>
                        </div>
                    </div>

                    @if($course->instructor)
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>Instructor:</strong>
                            </div>
                            <div class="col-sm-9">
                                {{ $course->instructor }}
                            </div>
                        </div>
                    @endif

                    @if($course->prerequisites)
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>Prerequisites:</strong>
                            </div>
                            <div class="col-sm-9">
                                <p>{{ $course->prerequisites }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Course Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted d-block">Duration</small>
                            <strong>{{ $course->duration_hours }} hours</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Credits</small>
                            <strong>{{ $course->credits ?: 'N/A' }}</strong>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted d-block">Price</small>
                            <strong>${{ number_format($course->price, 2) }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Max Participants</small>
                            <strong>{{ $course->max_participants ?: 'Unlimited' }}</strong>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <small class="text-muted d-block">Status</small>
                            @if($course->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif

                            @if($course->is_mandatory)
                                <span class="badge bg-warning">Mandatory</span>
                            @endif
                        </div>
                    </div>

                    @if($course->available_from || $course->available_until)
                        <div class="row mb-3">
                            <div class="col-12">
                                <small class="text-muted d-block">Availability</small>
                                @if($course->available_from)
                                    <small>From: {{ $course->available_from->format('M d, Y') }}</small><br>
                                @endif
                                @if($course->available_until)
                                    <small>Until: {{ $course->available_until->format('M d, Y') }}</small>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection