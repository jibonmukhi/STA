@extends('layouts.advanced-dashboard')

@section('title', 'Course Management')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>{{ trans('courses.course_management') }}</h2>
                    <p class="text-muted">{{ trans('courses.all_started_courses') }}</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ trans('courses.dashboard') }}</a></li>
                            <li class="breadcrumb-item active">{{ trans('courses.course_management') }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    @can('create', App\Models\Course::class)
                    <a href="{{ route('course-management.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ trans('courses.start_new') }}
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('course-management.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">{{ trans('courses.search') }}</label>
                                <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                                       placeholder="{{ trans('courses.search_courses') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ trans('courses.category') }}</label>
                                <select class="form-select" name="category">
                                    <option value="">{{ trans('courses.all_categories') }}</option>
                                    @foreach($categories as $key => $value)
                                        <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                            {{ trans('courses.categories.' . $key, [], null, $value) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ trans('courses.delivery_method') }}</label>
                                <select class="form-select" name="delivery_method">
                                    <option value="">{{ trans('courses.all_methods') }}</option>
                                    @foreach($deliveryMethods as $key => $value)
                                        <option value="{{ $key }}" {{ request('delivery_method') == $key ? 'selected' : '' }}>
                                            {{ trans('courses.delivery_methods.' . $key, [], null, $value) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="d-flex gap-2 w-100">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <a href="{{ route('course-management.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="show_inactive" value="1"
                                           {{ request('show_inactive') ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        {{ trans('courses.show_inactive_courses') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    @if($courses->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">{{ trans('courses.no_courses_found') }}</h4>
                            <p class="text-muted">{{ trans('courses.no_courses_message') }}</p>
                            @can('create', App\Models\Course::class)
                            <a href="{{ route('course-management.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> {{ trans('courses.start_new_course') }}
                            </a>
                            @endcan
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ trans('courses.course_code') }}</th>
                                        <th>{{ trans('courses.title') }}</th>
                                        <th>{{ trans('courses.category') }}</th>
                                        <th>{{ trans('courses.teacher') }}</th>
                                        <th>{{ trans('courses.duration') }}</th>
                                        <th>{{ trans('courses.delivery_method') }}</th>
                                        <th>{{ trans('courses.status') }}</th>
                                        <th class="text-end">{{ trans('courses.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($courses as $course)
                                        <tr class="course-row">
                                            <td>
                                                <strong class="text-primary">{{ $course->course_code }}</strong>
                                            </td>
                                            <td>
                                                <strong>{{ $course->title }}</strong>
                                                @if($course->is_mandatory)
                                                    <span class="badge bg-warning ms-1">{{ trans('courses.mandatory') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $categoryColor = dataVaultColor('course_category', $course->category) ?? 'info';
                                                    $categoryLabel = dataVaultLabel('course_category', $course->category) ?? ($categories[$course->category] ?? $course->category);
                                                @endphp
                                                <span class="badge bg-{{ $categoryColor }}">
                                                    {{ $categoryLabel }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($course->teachers && $course->teachers->count() > 0)
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach($course->teachers as $teacher)
                                                            <div class="d-flex align-items-center">
                                                                <img src="{{ $teacher->photo_url }}" alt="{{ $teacher->full_name }}"
                                                                     class="rounded-circle me-1" style="object-fit: cover; width: 24px; height: 24px;">
                                                                <span class="small">{{ $teacher->full_name }}</span>
                                                                @if(!$loop->last)<span class="text-muted mx-1">|</span>@endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @elseif($course->teacher)
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ $course->teacher->photo_url }}" alt="{{ $course->teacher->full_name }}"
                                                             class="rounded-circle me-2" style="object-fit: cover;">
                                                        <span>{{ $course->teacher->full_name }}</span>
                                                    </div>
                                                @elseif($course->instructor)
                                                    <span class="text-muted">{{ $course->instructor }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $course->duration_hours }}</strong>h
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ trans('courses.delivery_methods.' . $course->delivery_method, [], null, $deliveryMethods[$course->delivery_method] ?? $course->delivery_method) }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $statusColor = dataVaultColor('course_status', $course->status) ?? 'secondary';
                                                    $statusIcon = dataVaultIcon('course_status', $course->status) ?? 'fas fa-circle';
                                                    $statusLabel = dataVaultLabel('course_status', $course->status) ?? ucfirst($course->status);
                                                @endphp
                                                <span class="badge bg-{{ $statusColor }}">
                                                    <i class="{{ $statusIcon }}"></i> {{ $statusLabel }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('course-management.show', $course) }}" class="btn btn-sm btn-outline-info" title="{{ trans('courses.view') }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @can('update', $course)
                                                    <a href="{{ route('course-management.edit', $course) }}" class="btn btn-sm btn-outline-primary" title="{{ trans('courses.edit') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @endcan
                                                    @can('delete', $course)
                                                    <form action="{{ route('course-management.destroy', $course) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ trans('courses.delete') }}"
                                                                onclick="return confirm('{{ trans('courses.confirm_delete') }}');">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                    @endcan
                                                </div>
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
                                        <label class="me-2 mb-0 text-nowrap">{{ trans('courses.rows_per_page') }}:</label>
                                        <select class="form-select form-select-sm" id="perPageSelect" style="width: auto;">
                                            <option value="10" {{ request('per_page', 25) == 10 ? 'selected' : '' }}>10</option>
                                            <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
                                            <option value="50" {{ request('per_page', 25) == 50 ? 'selected' : '' }}>50</option>
                                            <option value="100" {{ request('per_page', 25) == 100 ? 'selected' : '' }}>100</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 text-center my-2 my-md-0">
                                    <small class="text-muted">
                                        {{ trans('courses.showing_entries', [
                                            'from' => $courses->firstItem() ?? 0,
                                            'to' => $courses->lastItem() ?? 0,
                                            'total' => $courses->total()
                                        ]) }}
                                    </small>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex justify-content-end">
                                        {{ $courses->withQueryString()->links('pagination::bootstrap-4') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.course-row {
    transition: background-color 0.2s;
}

.course-row:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.table thead th {
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #dee2e6;
    padding: 0.5rem;
}

.table tbody td {
    vertical-align: middle;
    padding: 0.5rem;
    font-size: 0.9rem;
}

.table tbody td img {
    width: 28px;
    height: 28px;
}

.table .badge {
    font-size: 0.75rem;
    padding: 0.25em 0.5em;
}

.pagination {
    margin-bottom: 0;
}
</style>

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