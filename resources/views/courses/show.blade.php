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
                    @can('update', $course)
                    <a href="{{ route('courses.edit', $course) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Course
                    </a>
                    @endcan
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Master Course Template - No Direct Enrollments</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">This is a master course template. To enroll students, create a course instance from this template.</p>
                    <div class="row g-3">
                        @can('create', App\Models\Course::class)
                        <div class="col-md-4">
                            <a href="{{ route('course-management.create') }}" class="btn btn-success w-100">
                                <i class="fas fa-play"></i> Start New Course Instance
                            </a>
                        </div>
                        @endcan
                        <div class="col-md-4">
                            <a href="{{ route('course-management.index') }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-list"></i> View Course Instances
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('courses.planning') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-project-diagram"></i> Course Planning
                            </a>
                        </div>
                    </div>
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

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>Course Title:</strong>
                        </div>
                        <div class="col-sm-9">
                            {{ $course->title }}
                        </div>
                    </div>

                    @if($course->description)
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>{{ trans('courses.course_programme') }}:</strong>
                            </div>
                            <div class="col-sm-9">
                                <p>{{ $course->description }}</p>
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
                            <small class="text-muted d-block">Category</small>
                            @php
                                $categoryColor = dataVaultColor('course_category', $course->category) ?? 'info';
                                $categoryLabel = dataVaultLabel('course_category', $course->category) ?? (App\Models\Course::getCategories()[$course->category] ?? $course->category);
                            @endphp
                            <span class="badge bg-{{ $categoryColor }}">{{ $categoryLabel }}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Delivery Method</small>
                            <span class="badge bg-secondary">{{ App\Models\Course::getDeliveryMethods()[$course->delivery_method] ?? $course->delivery_method }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        @if($course->level)
                            <div class="col-6">
                                <small class="text-muted d-block">Level</small>
                                <span class="badge bg-info">{{ ucfirst($course->level) }}</span>
                            </div>
                        @endif
                        <div class="col-6">
                            <small class="text-muted d-block">Duration</small>
                            <strong>{{ $course->duration_hours }} hours</strong>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted d-block">Status</small>
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
        </div>
    </div>

    <!-- Course Instances Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-copy"></i> Course Instances
                        <span class="badge bg-primary ms-2">{{ $instances->total() }}</span>
                    </h5>
                    @can('create', App\Models\Course::class)
                    <a href="{{ route('course-management.create') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-play"></i> Start New Instance
                    </a>
                    @endcan
                </div>
                <div class="card-body p-0">
                    @if($instances->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Course Code</th>
                                        <th>Title</th>
                                        <th>Teachers</th>
                                        <th>Start Date</th>
                                        <th>Status</th>
                                        <th>Enrollments</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($instances as $instance)
                                    <tr>
                                        <td><strong>{{ $instance->course_code }}</strong></td>
                                        <td>{{ $instance->title }}</td>
                                        <td>
                                            @if($instance->teachers && $instance->teachers->count() > 0)
                                                <small>{{ $instance->teachers->pluck('name')->join(', ') }}</small>
                                            @else
                                                <small class="text-muted">-</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($instance->start_date)
                                                <small>{{ $instance->start_date->format('M d, Y') }}</small>
                                            @else
                                                <small class="text-muted">-</small>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $statusColor = dataVaultColor('course_status', $instance->status) ?? 'secondary';
                                                $statusLabel = dataVaultLabel('course_status', $instance->status) ?? ucfirst($instance->status);
                                            @endphp
                                            <span class="badge badge-sm bg-{{ $statusColor }}">{{ $statusLabel }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $instance->enrollments->count() }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('course-management.show', $instance) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer bg-light">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <div class="d-flex align-items-center">
                                        <label class="me-2 mb-0 text-nowrap">Rows per page:</label>
                                        <select class="form-select form-select-sm" id="perPageSelect" style="width: auto;">
                                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                            <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                                            <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                                            <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 text-center my-2 my-md-0">
                                    <small class="text-muted">
                                        Showing {{ $instances->firstItem() ?? 0 }} to {{ $instances->lastItem() ?? 0 }} of {{ $instances->total() }} entries
                                    </small>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex justify-content-end">
                                        {{ $instances->withQueryString()->links('pagination::bootstrap-4') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="p-4">
                            <p class="text-muted text-center py-4 mb-0">
                                No course instances created yet.
                                @can('create', App\Models\Course::class)
                                <a href="{{ route('course-management.create') }}" class="btn btn-sm btn-success mt-2">
                                    <i class="fas fa-play"></i> Start First Instance
                                </a>
                                @endcan
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const perPageSelect = document.getElementById('perPageSelect');

    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', this.value);
            url.searchParams.delete('page'); // Reset to first page when changing per_page
            window.location.href = url.toString();
        });
    }
});
</script>
@endsection