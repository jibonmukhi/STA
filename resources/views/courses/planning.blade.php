@extends('layouts.advanced-dashboard')

@section('title', trans('courses.course_planning'))

@section('content')
<div class="container-fluid">

    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>{{ trans('courses.course_planning') }}</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ trans('courses.dashboard') }}</a></li>
                            <li class="breadcrumb-item active">{{ trans('courses.course_planning') }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list"></i> {{ trans('courses.all_courses') }}
                    </a>
                    @can('create', App\Models\Course::class)
                    <a href="{{ route('courses.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ trans('courses.add_course') }}
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>


    <!-- Planning Overview Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $courses->count() }}</h4>
                            <p class="mb-0">{{ trans('courses.total_active_courses') }}</p>
                        </div>
                        <div class="fs-2 opacity-75">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $coursesByCategory->count() }}</h4>
                            <p class="mb-0">{{ trans('courses.total_categories') }}</p>
                        </div>
                        <div class="fs-2 opacity-75">
                            <i class="fas fa-tags"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $courses->where('is_mandatory', true)->count() }}</h4>
                            <p class="mb-0">{{ trans('courses.mandatory_courses') }}</p>
                        </div>
                        <div class="fs-2 opacity-75">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $courses->sum('duration_hours') }}</h4>
                            <p class="mb-0">{{ trans('courses.total_hours') }}</p>
                        </div>
                        <div class="fs-2 opacity-75">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses by Category -->
    @if($coursesByCategory->count() > 0)
        @foreach($coursesByCategory as $category => $categoryCourses)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-folder-open me-2"></i>
                                {{ $categories[$category] ?? ucfirst(str_replace('_', ' ', $category)) }}
                                <span class="badge bg-secondary ms-2">{{ $categoryCourses->count() }} courses</span>
                            </h5>
                        </div>
                        <div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($categoryCourses as $course)
                                        <div class="col-lg-4 col-md-6 mb-3">
                                            <div class="card border h-100">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h6 class="card-title mb-1">{{ $course->title }}</h6>
                                                        @if($course->is_mandatory)
                                                            <span class="badge bg-warning">{{ trans('courses.mandatory') }}</span>
                                                        @endif
                                                    </div>

                                                    <div class="small text-muted mb-2">
                                                        <strong>{{ trans('courses.course_code') }}:</strong> {{ $course->course_code }}
                                                    </div>

                                                    <div class="row text-center mb-2">
                                                        <div class="col-4">
                                                            <small class="text-muted d-block">{{ trans('courses.level') }}</small>
                                                            <span class="badge bg-primary">{{ $levels[$course->level] ?? ucfirst($course->level) }}</span>
                                                        </div>
                                                        <div class="col-4">
                                                            <small class="text-muted d-block">{{ trans('courses.duration') }}</small>
                                                            <strong>{{ $course->duration_hours }}h</strong>
                                                        </div>
                                                        <div class="col-4">
                                                            <small class="text-muted d-block">{{ trans('courses.price') }}</small>
                                                            <strong>${{ number_format($course->price, 2) }}</strong>
                                                        </div>
                                                    </div>

                                                    <div class="mb-2">
                                                        @php
                                                            $statusColor = dataVaultColor('course_status', $course->status) ?? 'secondary';
                                                            $statusIcon = dataVaultIcon('course_status', $course->status) ?? 'fas fa-circle';
                                                            $statusLabel = dataVaultLabel('course_status', $course->status) ?? ucfirst($course->status);
                                                        @endphp
                                                        <span class="badge bg-{{ $statusColor }}">
                                                            <i class="{{ $statusIcon }}"></i> {{ $statusLabel }}
                                                        </span>
                                                        <span class="badge bg-secondary">{{ $deliveryMethods[$course->delivery_method] ?? ucfirst($course->delivery_method) }}</span>
                                                        @if($course->credits)
                                                            <span class="badge bg-info">{{ $course->credits }} credits</span>
                                                        @endif
                                                    </div>

                                                    @if($course->instructor)
                                                        <div class="small text-muted mb-2">
                                                            <i class="fas fa-user-tie"></i> {{ $course->instructor }}
                                                        </div>
                                                    @endif

                                                    @if($course->available_from || $course->available_until)
                                                        <div class="small text-muted mb-2">
                                                            <i class="fas fa-calendar"></i>
                                                            @if($course->available_from)
                                                                From {{ $course->available_from->format('M d, Y') }}
                                                            @endif
                                                            @if($course->available_until)
                                                                until {{ $course->available_until->format('M d, Y') }}
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="card-footer bg-transparent">
                                                    <div class="d-flex gap-1">
                                                        <a href="{{ route('courses.show', $course) }}"
                                                           class="btn btn-sm btn-outline-info">
                                                            <i class="fas fa-eye"></i> {{ trans('courses.view') }}
                                                        </a>
                                                        <a href="{{ route('courses.schedule', $course) }}"
                                                           class="btn btn-sm btn-outline-success">
                                                            <i class="fas fa-calendar"></i> {{ trans('courses.schedule') }}
                                                        </a>
                                                        @can('update', $course)
                                                        <a href="{{ route('courses.edit', $course) }}"
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-edit"></i> {{ trans('courses.edit') }}
                                                        </a>
                                                        @endcan
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">{{ trans('courses.no_active_courses') }}</h4>
                        <p class="text-muted">{{ trans('courses.no_active_courses_message') }}</p>
                        @can('create', App\Models\Course::class)
                        <a href="{{ route('courses.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> {{ trans('courses.create_first_course') }}
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.badge {
    font-size: 0.7em;
}

.collapse-btn {
    transition: transform 0.2s ease;
}

.collapse-btn.collapsed {
    transform: rotate(180deg);
}
</style>

@endsection