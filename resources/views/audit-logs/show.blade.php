@extends('layouts.advanced-dashboard')

@section('page-title', 'Audit Log Details')

@push('styles')
<style>
    .detail-label {
        font-weight: 600;
        color: #6c757d;
        min-width: 150px;
    }
    .change-item {
        padding: 10px;
        background: #f8f9fa;
        border-radius: 5px;
        margin-bottom: 10px;
    }
    .old-value {
        background-color: #ffebee;
        padding: 5px 10px;
        border-radius: 4px;
        border-left: 3px solid #dc3545;
    }
    .new-value {
        background-color: #e8f5e9;
        padding: 5px 10px;
        border-radius: 4px;
        border-left: 3px solid #28a745;
    }
    .json-display {
        background: #f4f4f4;
        padding: 15px;
        border-radius: 5px;
        font-family: 'Courier New', monospace;
        font-size: 0.9rem;
        max-height: 400px;
        overflow-y: auto;
    }
    .timeline-item {
        position: relative;
        padding-left: 40px;
        margin-bottom: 20px;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 30px;
        bottom: -20px;
        width: 2px;
        background: #dee2e6;
    }
    .timeline-item:last-child::before {
        display: none;
    }
    .timeline-icon {
        position: absolute;
        left: 5px;
        top: 5px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-history me-2"></i>Audit Log Details
                    </h4>
                    <p class="text-muted mb-0">Log Entry #{{ $auditLog->id }}</p>
                </div>
                <div>
                    <a href="{{ route('audit-logs.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Logs
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Details -->
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Basic Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="d-flex">
                                <span class="detail-label">Date & Time:</span>
                                <span>{{ $auditLog->created_at->format('Y-m-d H:i:s') }} ({{ $auditLog->created_at->diffForHumans() }})</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <span class="detail-label">Action:</span>
                                <span class="badge bg-{{ $auditLog->action_color }}">{{ $auditLog->formatted_action }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="d-flex">
                                <span class="detail-label">User:</span>
                                <span>
                                    @if($auditLog->user)
                                        <a href="{{ route('users.show', $auditLog->user) }}">
                                            {{ $auditLog->user_name }}
                                        </a>
                                        <small class="text-muted">({{ $auditLog->user_email }})</small>
                                    @else
                                        System
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <span class="detail-label">User Role:</span>
                                <span>{{ $auditLog->user_role ?: 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="d-flex">
                                <span class="detail-label">Module:</span>
                                <span>
                                    @if($auditLog->module)
                                        <span class="badge bg-secondary">{{ ucfirst($auditLog->module) }}</span>
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <span class="detail-label">Severity:</span>
                                <span class="badge bg-{{ $auditLog->severity_color }}">{{ ucfirst($auditLog->severity) }}</span>
                            </div>
                        </div>
                    </div>

                    @if($auditLog->description)
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="d-flex">
                                <span class="detail-label">Description:</span>
                                <span>{{ $auditLog->description }}</span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Model Information -->
            @if($auditLog->model_type)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-database me-2"></i>Affected Model
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="d-flex">
                                <span class="detail-label">Model Type:</span>
                                <span>{{ $auditLog->model_type_name }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <span class="detail-label">Model ID:</span>
                                <span>{{ $auditLog->model_id }}</span>
                            </div>
                        </div>
                    </div>

                    @if($auditLog->model_name)
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="d-flex">
                                <span class="detail-label">Model Name:</span>
                                <span>{{ $auditLog->model_name }}</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($affectedModel)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        This model still exists and can be viewed.
                        @if($auditLog->model_type == 'App\\Models\\User')
                            <a href="{{ route('users.show', $affectedModel) }}" class="alert-link">View User</a>
                        @elseif($auditLog->model_type == 'App\\Models\\Course')
                            <a href="{{ route('courses.show', $affectedModel) }}" class="alert-link">View Course</a>
                        @elseif($auditLog->model_type == 'App\\Models\\Company')
                            <a href="{{ route('companies.show', $affectedModel) }}" class="alert-link">View Company</a>
                        @endif
                    </div>
                    @else
                        @if($auditLog->action == 'deleted')
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            This model has been deleted.
                        </div>
                        @endif
                    @endif
                </div>
            </div>
            @endif

            <!-- Changes -->
            @if($auditLog->hasRecordedChanges())
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exchange-alt me-2"></i>Changes Made
                    </h5>
                </div>
                <div class="card-body">
                    @php $changes = $auditLog->getFormattedChanges(); @endphp
                    @if(count($changes) > 0)
                        @foreach($changes as $field => $values)
                        <div class="change-item">
                            <h6 class="mb-2">{{ $field }}</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted d-block mb-1">Old Value:</small>
                                    <div class="old-value">
                                        {!! $values['old'] !!}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block mb-1">New Value:</small>
                                    <div class="new-value">
                                        {!! $values['new'] !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <!-- Raw values display -->
                        @if($auditLog->old_values || $auditLog->new_values)
                        <div class="row">
                            @if($auditLog->old_values)
                            <div class="col-md-6">
                                <h6>Old Values</h6>
                                <div class="json-display">
                                    <pre>{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                            @endif
                            @if($auditLog->new_values)
                            <div class="col-md-6">
                                <h6>New Values</h6>
                                <div class="json-display">
                                    <pre>{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif
                    @endif
                </div>
            </div>
            @endif

            <!-- Request Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-network-wired me-2"></i>Request Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="d-flex">
                                <span class="detail-label">IP Address:</span>
                                <span>{{ $auditLog->ip_address ?: 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <span class="detail-label">HTTP Method:</span>
                                <span>
                                    @if($auditLog->method)
                                        <span class="badge bg-primary">{{ $auditLog->method }}</span>
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    @if($auditLog->url)
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="d-flex">
                                <span class="detail-label">URL:</span>
                                <span class="text-break">{{ $auditLog->url }}</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($auditLog->route_name)
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="d-flex">
                                <span class="detail-label">Route Name:</span>
                                <span><code>{{ $auditLog->route_name }}</code></span>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($auditLog->user_agent)
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex">
                                <span class="detail-label">User Agent:</span>
                                <span class="text-break"><small>{{ $auditLog->user_agent }}</small></span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Metadata -->
            @if($auditLog->metadata)
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tags me-2"></i>Additional Metadata
                    </h5>
                </div>
                <div class="card-body">
                    <div class="json-display">
                        <pre>{{ json_encode($auditLog->metadata, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Related Logs Sidebar -->
        <div class="col-lg-4">
            @if($relatedLogs->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-link me-2"></i>Related Activity
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($relatedLogs as $related)
                        <div class="timeline-item">
                            <div class="timeline-icon bg-{{ $related->action_color }}"></div>
                            <div class="small">
                                <div class="fw-semibold">{{ $related->formatted_action }}</div>
                                <div class="text-muted">
                                    {{ $related->activity_description }}
                                </div>
                                <div class="text-muted">
                                    <small>{{ $related->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection