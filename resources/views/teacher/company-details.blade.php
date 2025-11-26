@extends('layouts.advanced-dashboard')

@section('page-title', $company->name)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-building me-2"></i>{{ $company->name }}</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('teacher.my_dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('teacher.my-courses') }}">{{ __('teacher.my_courses') }}</a></li>
                            <li class="breadcrumb-item active">{{ $company->name }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('teacher.my-courses') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('teacher.back_to_courses') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Company Information Card -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> {{ __('teacher.company_information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold">{{ __('companies.company_name') }}</label>
                                <p>{{ $company->name }}</p>
                            </div>
                        </div>
                        @if($company->ateco_code)
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold">{{ __('companies.ATECO Code') }}</label>
                                <p><span class="badge bg-primary">{{ $company->ateco_code }}</span></p>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="row">
                        @if($company->email)
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold">{{ __('companies.email') }}</label>
                                <p>
                                    <a href="mailto:{{ $company->email }}" class="text-decoration-none">
                                        <i class="fas fa-envelope me-1"></i>{{ $company->email }}
                                    </a>
                                </p>
                            </div>
                        </div>
                        @endif
                        @if($company->phone)
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold">{{ __('companies.phone') }}</label>
                                <p>
                                    <a href="tel:{{ $company->phone }}" class="text-decoration-none">
                                        <i class="fas fa-phone me-1"></i>{{ $company->phone }}
                                    </a>
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>

                    @if($company->address || $company->city || $company->province || $company->postal_code)
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="fw-bold">{{ __('companies.address') }}</label>
                                <p>
                                    @if($company->address){{ $company->address }}<br>@endif
                                    @if($company->city || $company->province || $company->postal_code)
                                        {{ $company->postal_code }} {{ $company->city }}{{ $company->province ? ' (' . $company->province . ')' : '' }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- My Courses for This Company -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-book"></i> {{ __('teacher.my_courses_for_company') }}</h5>
                </div>
                <div class="card-body">
                    @if($courses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('teacher.course_code') }}</th>
                                        <th>{{ __('teacher.title') }}</th>
                                        <th>{{ __('teacher.students') }}</th>
                                        <th>{{ __('teacher.status') }}</th>
                                        <th>{{ __('teacher.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($courses as $course)
                                        <tr>
                                            <td>{{ $course->course_code }}</td>
                                            <td>{{ $course->title }}</td>
                                            <td><span class="badge bg-primary">{{ $course->enrollments->count() }}</span></td>
                                            <td>
                                                @php
                                                    $statusColor = dataVaultColor('course_status', $course->status) ?? 'secondary';
                                                    $statusLabel = dataVaultLabel('course_status', $course->status) ?? ucfirst($course->status);
                                                @endphp
                                                <span class="badge bg-{{ $statusColor }}">{{ $statusLabel }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('teacher.course-details', $course) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i> {{ __('teacher.view') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">{{ __('teacher.no_courses_for_company') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Company Users Card -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-users"></i> {{ __('teacher.company_contacts') }}</h5>
                </div>
                <div class="card-body">
                    @if($company->users && $company->users->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($company->users as $user)
                                <div class="list-group-item px-0">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $user->name }}</h6>
                                            @if($user->email)
                                                <small class="text-muted">
                                                    <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                                        <i class="fas fa-envelope"></i> {{ $user->email }}
                                                    </a>
                                                </small>
                                            @endif
                                            @if($user->phone)
                                                <br><small class="text-muted">
                                                    <a href="tel:{{ $user->phone }}" class="text-decoration-none">
                                                        <i class="fas fa-phone"></i> {{ $user->phone }}
                                                    </a>
                                                </small>
                                            @endif
                                            <div class="mt-1">
                                                @foreach($user->roles as $role)
                                                    <span class="badge bg-secondary">{{ $role->name }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">{{ __('teacher.no_contacts') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
