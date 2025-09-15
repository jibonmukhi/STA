@extends('layouts.advanced-dashboard')

@section('page-title', $company->name . ' - Company Details')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">
                <i class="fas fa-building me-2"></i>{{ $company->name }}
                @if($company->active)
                    <span class="badge bg-success ms-2">Active</span>
                @else
                    <span class="badge bg-secondary ms-2">Inactive</span>
                @endif
            </h4>
            <div>
                @can('edit companies')
                <a href="{{ route('companies.edit', $company) }}" class="btn btn-primary me-2">
                    <i class="fas fa-edit me-2"></i>Edit Company
                </a>
                @endcan
                <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Companies
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Company Information Card -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>Company Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">ATECO Code</label>
                            <p class="form-control-plaintext">
                                @if($company->ateco_code)
                                    <span class="badge bg-primary fs-6">
                                        <i class="fas fa-tag me-2"></i>{{ $company->ateco_code }}
                                    </span>
                                    <small class="text-muted ms-2 d-block">(Economic Activity Classification Code)</small>
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Company Name</label>
                            <p class="form-control-plaintext">{{ $company->name }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <p class="form-control-plaintext">
                                @if($company->email)
                                    <a href="mailto:{{ $company->email }}" class="text-decoration-none">
                                        <i class="fas fa-envelope me-2"></i>{{ $company->email }}
                                    </a>
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">P.IVA / VAT Number</label>
                            <p class="form-control-plaintext">
                                {{ $company->piva ?: 'Not provided' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Phone</label>
                            <p class="form-control-plaintext">
                                @if($company->phone)
                                    <a href="tel:{{ $company->phone }}" class="text-decoration-none">
                                        <i class="fas fa-phone me-2"></i>{{ $company->phone }}
                                    </a>
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Website</label>
                            <p class="form-control-plaintext">
                                @if($company->website)
                                    <a href="{{ $company->website }}" target="_blank" class="text-decoration-none">
                                        <i class="fas fa-globe me-2"></i>{{ $company->website }}
                                        <i class="fas fa-external-link-alt ms-1"></i>
                                    </a>
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>


                <div class="mb-3">
                    <label class="form-label fw-bold">Address</label>
                    <p class="form-control-plaintext">
                        @if($company->address)
                            <i class="fas fa-map-marker-alt me-2"></i>
                            {{ $company->address }}
                        @else
                            <span class="text-muted">Not provided</span>
                        @endif
                    </p>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <p class="form-control-plaintext">
                                @if($company->active)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Active
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-times-circle me-1"></i>Inactive
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Created</label>
                            <p class="form-control-plaintext">
                                <i class="fas fa-calendar me-2"></i>
                                {{ $company->created_at->format('M d, Y') }}
                                <small class="text-muted">({{ $company->created_at->diffForHumans() }})</small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Company Logo Card -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-image me-2"></i>Company Logo
                </h5>
            </div>
            <div class="card-body text-center">
                <img src="{{ $company->logo_url }}" alt="{{ $company->name }}" 
                     class="img-fluid rounded shadow-sm" style="max-height: 200px;">
                
                @if($company->logo)
                    <div class="mt-3">
                        <small class="text-muted">Last updated: {{ $company->updated_at->format('M d, Y') }}</small>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @can('edit companies')
                    <a href="{{ route('companies.edit', $company) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Company
                    </a>
                    @endcan

                    @if($company->email)
                    <a href="mailto:{{ $company->email }}" class="btn btn-outline-primary">
                        <i class="fas fa-envelope me-2"></i>Send Email
                    </a>
                    @endif

                    @if($company->phone)
                    <a href="tel:{{ $company->phone }}" class="btn btn-outline-success">
                        <i class="fas fa-phone me-2"></i>Call Company
                    </a>
                    @endif

                    @if($company->website)
                    <a href="{{ $company->website }}" target="_blank" class="btn btn-outline-info">
                        <i class="fas fa-globe me-2"></i>Visit Website
                    </a>
                    @endif

                    @can('delete companies')
                    <form method="POST" action="{{ route('companies.destroy', $company) }}" 
                          onsubmit="return confirm('Are you sure you want to delete this company? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="fas fa-trash me-2"></i>Delete Company
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection