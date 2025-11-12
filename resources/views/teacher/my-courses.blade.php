@extends('layouts.advanced-dashboard')

@section('page-title', __('teacher.my_courses'))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('teacher.my_courses') }}</h5>
                    @can('create', App\Models\Course::class)
                        <a href="{{ route('courses.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> {{ __('teacher.create_course') }}
                        </a>
                    @endcan
                </div>
                <div class="card-body">
                    @if($courses->count() > 0)
                        <div class="row">
                            @foreach($courses as $course)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $course->title }}</h5>
                                            <p class="text-muted small">{{ $course->course_code }}</p>
                                            <p class="card-text">{{ Str::limit($course->description, 100) }}</p>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                @php
                                                    $statusColor = dataVaultColor('course_status', $course->status) ?? 'secondary';
                                                    $statusIcon = dataVaultIcon('course_status', $course->status) ?? 'fas fa-circle';
                                                    $statusLabel = dataVaultLabel('course_status', $course->status) ?? ucfirst($course->status);
                                                @endphp
                                                <span class="badge bg-{{ $statusColor }}">
                                                    <i class="{{ $statusIcon }}"></i> {{ $statusLabel }}
                                                </span>
                                                <span class="badge bg-info">
                                                    {{ $course->enrolled_students_count }} {{ __('teacher.students') }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <a href="{{ route('teacher.course-students', $course) }}" class="btn btn-sm btn-primary">
                                                {{ __('teacher.view_students') }}
                                            </a>
                                            @can('update', $course)
                                                <a href="{{ route('courses.edit', $course) }}" class="btn btn-sm btn-outline-secondary">
                                                    {{ __('teacher.edit_course') }}
                                                </a>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        {{ $courses->links() }}
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-book fa-4x text-muted mb-3"></i>
                            <p class="text-muted">{{ __('teacher.no_courses_yet') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
