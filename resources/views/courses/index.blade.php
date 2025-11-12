@extends('layouts.advanced-dashboard')

@section('title', trans('courses.course_list'))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>{{ trans('courses.course_list') }}</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ trans('courses.dashboard') }}</a></li>
                            <li class="breadcrumb-item active">{{ trans('courses.course_list') }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('courses.planning') }}" class="btn btn-outline-info">
                        <i class="fas fa-calendar-alt"></i> {{ trans('courses.course_planning') }}
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

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('courses.index') }}">
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
                            <div class="col-md-2">
                                <label class="form-label">{{ trans('courses.level') }}</label>
                                <select class="form-select" name="level">
                                    <option value="">{{ trans('courses.all_levels') }}</option>
                                    @foreach($levels as $key => $value)
                                        <option value="{{ $key }}" {{ request('level') == $key ? 'selected' : '' }}>
                                            {{ trans('courses.levels.' . $key, [], null, $value) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
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
                                    <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">
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
        @forelse($courses as $course)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 course-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title">{{ $course->title }}</h5>
                            <div class="text-end">
                                @if(!$course->is_active)
                                    <span class="badge bg-secondary mb-1">{{ trans('courses.inactive') }}</span><br>
                                @endif
                                <span class="badge bg-primary">{{ trans('courses.levels.' . $course->level, [], null, ucfirst($course->level)) }}</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">{{ trans('courses.course_code') }}: <strong>{{ $course->course_code }}</strong></small>
                        </div>

                        @if($course->description)
                            <p class="card-text text-muted small">
                                {{ Str::limit($course->description, 120) }}
                            </p>
                        @endif

                        <div class="row mb-3 text-center">
                            <div class="col-4">
                                <small class="text-muted d-block">{{ trans('courses.duration') }}</small>
                                <strong>{{ $course->duration_hours }}h</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">{{ trans('courses.credits') }}</small>
                                <strong>{{ $course->credits ?: trans('courses.n_a') }}</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">{{ trans('courses.price') }}</small>
                                <strong>${{ number_format($course->price, 2) }}</strong>
                            </div>
                        </div>

                        <div class="mb-3">
                            <span class="badge bg-info">{{ trans('courses.categories.' . $course->category, [], null, $categories[$course->category] ?? $course->category) }}</span>
                            <span class="badge bg-secondary">{{ trans('courses.delivery_methods.' . $course->delivery_method, [], null, $deliveryMethods[$course->delivery_method] ?? $course->delivery_method) }}</span>
                            @if($course->is_mandatory)
                                <span class="badge bg-warning">{{ trans('courses.mandatory') }}</span>
                            @endif
                        </div>

                        @if($course->teacher)
                            <div class="mb-2">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $course->teacher->photo_url }}" alt="{{ $course->teacher->full_name }}"
                                         class="rounded-circle me-2" style="width: 28px; height: 28px; object-fit: cover;">
                                    <div>
                                        <small class="text-muted d-block" style="line-height: 1.2;">
                                            <i class="fas fa-chalkboard-teacher"></i> Teacher
                                        </small>
                                        <small><strong>{{ $course->teacher->full_name }}</strong></small>
                                    </div>
                                </div>
                            </div>
                        @elseif($course->instructor)
                            <p class="card-text mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-user-tie"></i> {{ trans('courses.instructor') }}: {{ $course->instructor }}
                                </small>
                            </p>
                        @endif
                    </div>

                    <div class="card-footer bg-transparent">
                        <div class="d-flex gap-2">
                            <a href="{{ route('courses.show', $course) }}" class="btn btn-sm btn-outline-info flex-fill">
                                <i class="fas fa-eye"></i> {{ trans('courses.view') }}
                            </a>
                            @can('update', $course)
                            <a href="{{ route('courses.edit', $course) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i> {{ trans('courses.edit') }}
                            </a>
                            @endcan
                            @can('delete', $course)
                            <form action="{{ route('courses.destroy', $course) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('{{ trans('courses.confirm_delete') }}');">>
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">{{ trans('courses.no_courses_found') }}</h4>
                        <p class="text-muted">{{ trans('courses.no_courses_message') }}</p>
                        @can('create', App\Models\Course::class)
                        <a href="{{ route('courses.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> {{ trans('courses.create_first_course') }}
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    @if($courses->hasPages())
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $courses->withQueryString()->links() }}
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.course-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.course-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}
</style>
@endsection