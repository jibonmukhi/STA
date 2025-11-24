@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if($certificate)
                <!-- Verification Successful -->
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-check-circle"></i> {{ __('certificates.verification_successful') }}
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success mb-4">
                            <i class="fas fa-shield-alt"></i>
                            {{ __('certificates.certificate_verified_message') }}
                        </div>

                        <h5 class="mb-3">{{ __('certificates.certificate_details') }}</h5>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th width="35%">{{ __('certificates.certificate_number') }}</th>
                                        <td>{{ $certificate->certificate_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('certificates.verification_code') }}</th>
                                        <td><code>{{ $certificate->verification_code }}</code></td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('certificates.recipient_name') }}</th>
                                        <td>
                                            <strong>{{ $certificate->user->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $certificate->user->email }}</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('certificates.course_subject') }}</th>
                                        <td>{{ $certificate->subject }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('certificates.certificate_name') }}</th>
                                        <td>{{ $certificate->name }}</td>
                                    </tr>
                                    @if($certificate->description)
                                    <tr>
                                        <th>{{ __('certificates.description') }}</th>
                                        <td>{{ $certificate->description }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <th>{{ __('certificates.issue_date') }}</th>
                                        <td>
                                            <span class="text-success">
                                                <i class="fas fa-calendar-check"></i>
                                                {{ $certificate->issue_date->format('d F Y') }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('certificates.expiration_date') }}</th>
                                        <td>
                                            @if($certificate->expiration_date)
                                                @if($certificate->expiration_date->isPast())
                                                    <span class="text-danger">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                        {{ $certificate->expiration_date->format('d F Y') }}
                                                        ({{ __('certificates.expired') }})
                                                    </span>
                                                @elseif($certificate->expiration_date->diffInDays(now()) < 30)
                                                    <span class="text-warning">
                                                        <i class="fas fa-clock"></i>
                                                        {{ $certificate->expiration_date->format('d F Y') }}
                                                        ({{ __('certificates.expiring_soon') }})
                                                    </span>
                                                @else
                                                    <span class="text-success">
                                                        {{ $certificate->expiration_date->format('d F Y') }}
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-muted">{{ __('certificates.no_expiration') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('certificates.training_organization') }}</th>
                                        <td>
                                            {{ $certificate->training_organization }}
                                            @if($certificate->training_organization_code)
                                                <br>
                                                <small class="text-muted">
                                                    {{ __('certificates.organization_code') }}: {{ $certificate->training_organization_code }}
                                                </small>
                                            @endif
                                        </td>
                                    </tr>
                                    @if($certificate->instructor_name)
                                    <tr>
                                        <th>{{ __('certificates.instructor') }}</th>
                                        <td>{{ $certificate->instructor_name }}</td>
                                    </tr>
                                    @endif
                                    @if($certificate->hours_completed)
                                    <tr>
                                        <th>{{ __('certificates.hours_completed') }}</th>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $certificate->hours_completed }} {{ __('certificates.hours') }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endif
                                    @if($certificate->grade)
                                    <tr>
                                        <th>{{ __('certificates.grade') }}</th>
                                        <td>
                                            <span class="badge bg-success">{{ $certificate->grade }}</span>
                                        </td>
                                    </tr>
                                    @endif
                                    @if($certificate->score)
                                    <tr>
                                        <th>{{ __('certificates.score') }}</th>
                                        <td>{{ $certificate->score }}%</td>
                                    </tr>
                                    @endif
                                    @if($certificate->company)
                                    <tr>
                                        <th>{{ __('certificates.company') }}</th>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ $certificate->company->name }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <th>{{ __('certificates.certificate_type') }}</th>
                                        <td>
                                            <span class="badge bg-primary">
                                                {{ __('certificates.type_' . $certificate->certificate_type) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @if($certificate->level)
                                    <tr>
                                        <th>{{ __('certificates.level') }}</th>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ __('certificates.level_' . $certificate->level) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <th>{{ __('certificates.status') }}</th>
                                        <td>
                                            @if($certificate->status === 'active')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle"></i> {{ __('certificates.active') }}
                                                </span>
                                            @elseif($certificate->status === 'expired')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle"></i> {{ __('certificates.expired') }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">{{ $certificate->status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-info-circle"></i>
                                    {{ __('certificates.verification_timestamp') }}: {{ now()->format('d F Y H:i:s') }}
                                </p>
                            </div>
                            @if($certificate->certificate_file_path)
                                <a href="{{ route('certificates.download', ['certificate' => $certificate, 'type' => 'pdf']) }}"
                                   class="btn btn-success">
                                    <i class="fas fa-download"></i> {{ __('certificates.download_pdf') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <!-- Verification Failed -->
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-times-circle"></i> {{ __('certificates.verification_failed') }}
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ $message ?? __('certificates.certificate_not_found_or_invalid') }}
                        </div>

                        <p class="mt-3">{{ __('certificates.possible_reasons') }}:</p>
                        <ul>
                            <li>{{ __('certificates.invalid_verification_code') }}</li>
                            <li>{{ __('certificates.certificate_revoked_or_suspended') }}</li>
                            <li>{{ __('certificates.certificate_expired_status') }}</li>
                        </ul>

                        <div class="mt-4">
                            <p>{{ __('certificates.contact_support_message') }}</p>
                            <a href="{{ route('contact') }}" class="btn btn-primary">
                                <i class="fas fa-envelope"></i> {{ __('certificates.contact_support') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- QR Code Section (for future implementation) -->
            <div class="card mt-4">
                <div class="card-body text-center">
                    <h5>{{ __('certificates.verify_another_certificate') }}</h5>
                    <p class="text-muted">{{ __('certificates.enter_verification_code_below') }}</p>

                    <form action="/verify/" method="GET" class="mt-3" onsubmit="this.action = '/verify/' + this.code.value; return true;">
                        <div class="input-group">
                            <input type="text" name="code" class="form-control"
                                   placeholder="{{ __('certificates.verification_code_placeholder') }}"
                                   pattern="CERT-[A-Z0-9]+"
                                   required>
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i> {{ __('certificates.verify') }}
                            </button>
                        </div>
                        <small class="text-muted">{{ __('certificates.verification_code_format') }}: CERT-XXXXXXXXXX</small>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.badge {
    padding: 0.5em 0.75em;
}

code {
    background-color: #f8f9fa;
    padding: 0.25em 0.5em;
    border-radius: 0.25rem;
    font-size: 0.95em;
}
</style>
@endsection