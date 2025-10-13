@extends('layouts.advanced-dashboard')

@section('page-title', __('users.bulk_upload_title'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4 gap-3">
            <div>
                <h4 class="mb-0">
                    <i class="fas fa-file-upload me-2"></i>{{ __('users.bulk_upload_title') }}
                </h4>
                <small class="text-muted">{{ __('users.bulk_upload_description') }}</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>{{ __('users.back_to_users') }}
                </a>
                <a href="{{ route('users.template.download') }}" class="btn btn-outline-primary">
                    <i class="fas fa-file-download me-2"></i>{{ __('users.bulk_upload_download_template') }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-upload me-2"></i>{{ __('users.bulk_upload') }}
                </h5>
            </div>
            <div class="card-body">
                @include('components.flash-messages')

                <form action="{{ route('users.bulk-upload.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="bulk-file" class="form-label">{{ __('users.bulk_upload') }}</label>
                        <input type="file" id="bulk-file" name="file"
                               class="form-control @error('file') is-invalid @enderror"
                               accept=".xlsx,.csv" required>
                        <small class="text-muted">{{ __('users.bulk_upload_allowed_types') }}</small>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-cloud-upload-alt me-2"></i>{{ __('users.bulk_upload_cta') }}
                    </button>
                </form>
            </div>
        </div>

        @php
            $summary = session('importSummary');
        @endphp

        @if($summary)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clipboard-list me-2"></i>{{ __('users.bulk_upload_results') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="fw-semibold mb-1">{{ __('users.bulk_upload_summary_title') }}</h6>
                        <p class="mb-0 text-muted">
                            {{ __('users.bulk_upload_template_hint', ['headers' => implode(', ', $templateHeaders)]) }}
                        </p>
                    </div>

                    @if(($summary['success_count'] ?? 0) > 0)
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ trans_choice('users.bulk_upload_success_count', $summary['success_count'], ['count' => $summary['success_count']]) }}
                        </div>

                        <div class="table-responsive mb-4">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>{{ __('users.bulk_upload_row') }}</th>
                                        <th>{{ __('users.name') }}</th>
                                        <th>{{ __('users.email') }}</th>
                                        <th>{{ __('users.bulk_upload_generated_password') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($summary['created'] as $created)
                                        <tr>
                                            <td>#{{ $created['row'] ?? '-' }}</td>
                                            <td>{{ $created['name'] ?? '-' }}</td>
                                            <td>{{ $created['email'] ?? '-' }}</td>
                                            <td>
                                                @if(!empty($created['generated_password']))
                                                    <span class="badge bg-success">{{ $created['generated_password'] }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">{{ __('users.bulk_upload_no_results') }}</p>
                    @endif

                    @if(!empty($summary['errors']))
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ trans_choice('users.bulk_upload_error_count', count($summary['errors']), ['count' => count($summary['errors'])]) }}
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('users.bulk_upload_row') }}</th>
                                        <th>{{ __('users.email') }}</th>
                                        <th>{{ __('users.bulk_upload_errors_found') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($summary['errors'] as $error)
                                        <tr>
                                            <td>#{{ $error['row'] ?? '-' }}</td>
                                            <td>{{ $error['email'] ?? '—' }}</td>
                                            <td>{{ $error['message'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>{{ __('users.bulk_upload_instructions_title') }}
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-3">
                        <i class="fas fa-check text-primary me-2"></i>{{ __('users.bulk_upload_instruction_1') }}
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-check text-primary me-2"></i>{{ __('users.bulk_upload_instruction_2') }}
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-check text-primary me-2"></i>{{ __('users.bulk_upload_instruction_3') }}
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-check text-primary me-2"></i>{{ __('users.bulk_upload_instruction_4') }}
                    </li>
                    <li class="mb-0">
                        <i class="fas fa-check text-primary me-2"></i>{{ __('users.bulk_upload_instruction_5') }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
