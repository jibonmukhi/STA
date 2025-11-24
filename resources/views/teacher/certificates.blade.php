@extends('layouts.advanced-dashboard')

@section('page-title', __('teacher.certificates'))

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">{{ __('teacher.certificates') }}</h2>
                    <p class="text-muted">{{ __('teacher.manage_all_certificates') }}</p>
                </div>
                <div>
                    <a href="{{ route('teacher.my-courses') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> {{ __('teacher.back_to_courses') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('teacher.certificates') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">{{ __('teacher.filter_by_course') }}</label>
                            <select name="course_id" class="form-select" onchange="this.form.submit()">
                                <option value="">{{ __('teacher.all_courses') }}</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->title }} ({{ $course->course_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('teacher.filter_by_student') }}</label>
                            <select name="student_id" class="form-select" onchange="this.form.submit()">
                                <option value="">{{ __('teacher.all_students') }}</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->name }} ({{ $student->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('teacher.search') }}</label>
                            <div class="input-group">
                                <input type="text" name="search" class="form-control"
                                       placeholder="{{ __('teacher.search_certificates') }}"
                                       value="{{ request('search') }}">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <a href="{{ route('teacher.certificates') }}" class="btn btn-secondary w-100">
                                <i class="fas fa-redo"></i> {{ __('teacher.clear_filters') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                {{ __('teacher.total_certificates') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $certificates->total() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-certificate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                {{ __('teacher.total_courses') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $courses->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                {{ __('teacher.total_students') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $students->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                {{ __('teacher.this_month') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $certificates->filter(function($cert) {
                                    return $cert->issue_date->isCurrentMonth();
                                })->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Certificates Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">{{ __('teacher.certificates_list') }}</h5>
                </div>
                <div class="card-body">
                    @if($certificates->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('teacher.certificate_id') }}</th>
                                        <th>{{ __('teacher.student_name') }}</th>
                                        <th>{{ __('teacher.course_title') }}</th>
                                        <th>{{ __('teacher.issue_date') }}</th>
                                        <th>{{ __('teacher.expiration_date') }}</th>
                                        <th>{{ __('teacher.hours_completed') }}</th>
                                        <th>{{ __('teacher.status') }}</th>
                                        <th>{{ __('teacher.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($certificates as $certificate)
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    #{{ str_pad($certificate->id, 6, '0', STR_PAD_LEFT) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-2">
                                                        <span class="avatar-title rounded-circle bg-primary">
                                                            {{ strtoupper(substr($certificate->user->name, 0, 2)) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">{{ $certificate->user->name }}</div>
                                                        <small class="text-muted">{{ $certificate->user->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <span class="badge bg-info">{{ $certificate->subject }}</span>
                                                </div>
                                                @if($certificate->user->companies->isNotEmpty())
                                                    <small class="text-muted">
                                                        {{ $certificate->user->companies->pluck('name')->join(', ') }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-success">
                                                    <i class="fas fa-calendar-check"></i>
                                                    {{ $certificate->issue_date->format('d/m/Y') }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($certificate->expiration_date)
                                                    @if($certificate->expiration_date->isPast())
                                                        <span class="text-danger">
                                                            <i class="fas fa-exclamation-triangle"></i>
                                                            {{ $certificate->expiration_date->format('d/m/Y') }}
                                                        </span>
                                                    @elseif($certificate->expiration_date->diffInDays(now()) < 30)
                                                        <span class="text-warning">
                                                            <i class="fas fa-clock"></i>
                                                            {{ $certificate->expiration_date->format('d/m/Y') }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">
                                                            {{ $certificate->expiration_date->format('d/m/Y') }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">{{ __('teacher.no_expiration') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($certificate->hours_completed)
                                                    <span class="badge bg-success">
                                                        {{ $certificate->hours_completed }} {{ __('teacher.hours') }}
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($certificate->status === 'active')
                                                    <span class="badge bg-success">{{ __('teacher.active') }}</span>
                                                @elseif($certificate->status === 'expired')
                                                    <span class="badge bg-danger">{{ __('teacher.expired') }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $certificate->status }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('certificates.show', $certificate) }}"
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="{{ __('teacher.view_certificate') }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('certificates.download', ['certificate' => $certificate, 'type' => 'pdf']) }}"
                                                       class="btn btn-sm btn-outline-success"
                                                       title="{{ __('teacher.download_pdf') }}">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div>
                                {{ __('teacher.showing_entries', [
                                    'from' => $certificates->firstItem() ?? 0,
                                    'to' => $certificates->lastItem() ?? 0,
                                    'total' => $certificates->total()
                                ]) }}
                            </div>
                            {{ $certificates->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-certificate fa-4x text-muted mb-3"></i>
                            <h5>{{ __('teacher.no_certificates_yet') }}</h5>
                            <p class="text-muted">{{ __('teacher.no_certificates_message') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    display: inline-block;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 600;
    color: #fff;
}

.border-left-primary {
    border-left: 4px solid #4e73df !important;
}

.border-left-success {
    border-left: 4px solid #1cc88a !important;
}

.border-left-info {
    border-left: 4px solid #36b9cc !important;
}

.border-left-warning {
    border-left: 4px solid #f6c23e !important;
}
</style>
@endsection