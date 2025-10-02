@extends('layouts.advanced-dashboard')

@section('page-title', __('teacher.certificates'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('teacher.certificates') }}</h5>
                    <a href="{{ route('certificates.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('teacher.issue_certificate') }}
                    </a>
                </div>
                <div class="card-body">
                    @if($certificates->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('certificates.certificate_number') }}</th>
                                        <th>{{ __('certificates.name') }}</th>
                                        <th>{{ __('teacher.student_name') }}</th>
                                        <th>{{ __('certificates.issue_date') }}</th>
                                        <th>{{ __('certificates.status') }}</th>
                                        <th>{{ __('teacher.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($certificates as $certificate)
                                        <tr>
                                            <td>{{ $certificate->certificate_number }}</td>
                                            <td>{{ $certificate->name }}</td>
                                            <td>{{ $certificate->user->full_name ?? 'N/A' }}</td>
                                            <td>{{ $certificate->issue_date->format('M d, Y') }}</td>
                                            <td>
                                                <span class="badge {{ $certificate->status_badge_class }}">
                                                    {{ $certificate->formatted_status }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('certificates.show', $certificate) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $certificates->links() }}
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-certificate fa-4x text-muted mb-3"></i>
                            <p class="text-muted">{{ __('teacher.no_certificates_yet') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
