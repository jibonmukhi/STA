@extends('layouts.advanced-dashboard')

@section('page-title', __('teacher.my_courses'))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>{{ __('teacher.my_courses') }}</h2>
                    <p class="text-muted">{{ __('teacher.courses_assigned_to_you') }}</p>
                </div>
                @can('create', App\Models\Course::class)
                <div>
                    <a href="{{ route('courses.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('teacher.create_course') }}
                    </a>
                </div>
                @endcan
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('teacher.my-courses') }}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('teacher.search') }}</label>
                                <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                                       placeholder="{{ __('teacher.search_courses') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ __('teacher.category') }}</label>
                                <select class="form-select" name="category">
                                    <option value="">{{ __('teacher.all_categories') }}</option>
                                    @foreach($categories as $key => $value)
                                        <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ __('teacher.status') }}</label>
                                <select class="form-select" name="status">
                                    <option value="">{{ __('teacher.all_statuses') }}</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('teacher.active') }}</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('teacher.inactive') }}</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('teacher.completed') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-3 mt-0">
                            <div class="col-md-12 d-flex justify-content-end">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> {{ __('teacher.filter') }}
                                    </button>
                                    <a href="{{ route('teacher.my-courses') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> {{ __('teacher.clear_filters') }}
                                    </a>
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
                            <i class="fas fa-book fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">{{ __('teacher.no_courses_found') }}</h4>
                            <p class="text-muted">{{ __('teacher.no_courses_yet') }}</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('teacher.course_code') }}</th>
                                        <th>{{ __('teacher.title') }}</th>
                                        <th>{{ __('teacher.category') }}</th>
                                        <th>{{ __('teacher.company') }}</th>
                                        <th>{{ __('teacher.start_date') }}</th>
                                        <th>{{ __('teacher.end_date') }}</th>
                                        <th>{{ __('teacher.hours') }}</th>
                                        <th>{{ __('teacher.participation') }}</th>
                                        <th>{{ __('teacher.students') }}</th>
                                        <th>{{ __('teacher.status') }}</th>
                                        <th class="text-end">{{ __('teacher.actions') }}</th>
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
                                                    <span class="badge bg-warning ms-1">{{ __('teacher.mandatory') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $categoryColor = dataVaultColor('course_category', $course->category) ?? 'info';
                                                    $categoryLabel = dataVaultLabel('course_category', $course->category) ?? $course->category;
                                                @endphp
                                                <span class="badge bg-{{ $categoryColor }}">
                                                    {{ $categoryLabel }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($course->assignedCompanies && $course->assignedCompanies->count() > 0)
                                                    <span class="badge bg-info">{{ $course->assignedCompanies->first()->name }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($course->start_date)
                                                    <small>{{ $course->start_date->format('d/m/Y') }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($course->end_date)
                                                    <small>{{ $course->end_date->format('d/m/Y') }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($course->duration_hours)
                                                    <small>{{ $course->duration_hours }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ __('teacher.delivery_methods.' . $course->delivery_method) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    {{ $course->students_count ?? 0 }}
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
                                                    <a href="{{ route('course-management.show', $course) }}" class="btn btn-sm btn-outline-info" title="{{ __('teacher.view_details') }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('teacher.course-students', $course) }}" class="btn btn-sm btn-outline-primary" title="{{ __('teacher.view_students') }}">
                                                        <i class="fas fa-users"></i>
                                                    </a>
                                                    @can('update', $course)
                                                    <a href="{{ route('courses.edit', $course) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('teacher.edit_course') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
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
                                        <label class="me-2 mb-0 text-nowrap">{{ __('teacher.rows_per_page') }}:</label>
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
                                        {{ __('teacher.showing_entries', [
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
    padding: 0.75rem 0.5rem;
}

.table tbody td {
    vertical-align: middle;
    padding: 0.75rem 0.5rem;
    font-size: 0.9rem;
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
