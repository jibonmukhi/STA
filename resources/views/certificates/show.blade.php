@extends('layouts.advanced-dashboard')

@section('title', $certificate->name)

@push('styles')
<style>
    .certificate-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 10px;
        position: relative;
        overflow: hidden;
    }

    .certificate-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="1" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="1" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>') repeat;
    }

    .certificate-header .container-fluid {
        position: relative;
        z-index: 1;
    }

    .info-card {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        border-left: 4px solid #007bff;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.2);
    }

    .info-card h5 {
        color: #495057;
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #f8f9fa;
        display: flex;
        align-items: center;
    }

    .info-card h5 i {
        margin-right: 0.5rem;
        color: #007bff;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f8f9fa;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 500;
        color: #6c757d;
        flex: 1;
    }

    .info-value {
        color: #495057;
        flex: 2;
        text-align: right;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-active {
        background: #d4edda;
        color: #155724;
    }

    .status-expired {
        background: #f8d7da;
        color: #721c24;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-suspended {
        background: #ffeaa7;
        color: #6c5ce7;
    }

    .status-revoked {
        background: #fd79a8;
        color: #2d3436;
    }

    .file-card {
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
    }

    .file-card:hover {
        border-color: #007bff;
        background: #e7f3ff;
    }

    .file-card.has-file {
        border-style: solid;
        border-color: #28a745;
        background: #d4edda;
    }

    .file-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    .qr-code {
        text-align: center;
        padding: 1rem;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .verification-code {
        font-family: 'Courier New', monospace;
        background: #f8f9fa;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        font-size: 1.1rem;
        letter-spacing: 1px;
        border: 1px solid #dee2e6;
    }

    .action-button {
        margin: 0.25rem;
    }

    .certificate-stats {
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        border-radius: 10px;
        padding: 1.5rem;
        margin-top: 1rem;
    }

    .stat-item {
        text-align: center;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: white;
    }

    .stat-label {
        color: rgba(255,255,255,0.8);
        font-size: 0.9rem;
    }

    @media (max-width: 768px) {
        .certificate-header {
            padding: 1.5rem 0;
        }

        .info-item {
            flex-direction: column;
            align-items: flex-start;
        }

        .info-value {
            text-align: left;
            margin-top: 0.25rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Certificate Header -->
    <div class="certificate-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <i class="fas fa-certificate fa-3x"></i>
                        </div>
                        <div>
                            <h1 class="mb-1">{{ $certificate->name }}</h1>
                            <h4 class="mb-0 opacity-75">{{ $certificate->subject }}</h4>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <i class="fas fa-user me-2"></i>
                                <strong>{{ __('certificates.certificate_holder') }}:</strong> {{ $certificate->user->name }}
                            </p>
                            <p class="mb-2">
                                <i class="fas fa-building me-2"></i>
                                <strong>{{ __('certificates.issuing_organization') }}:</strong> {{ $certificate->training_organization }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2">
                                <i class="fas fa-calendar me-2"></i>
                                <strong>{{ __('certificates.issued_on') }}:</strong> {{ $certificate->issue_date->format('F j, Y') }}
                            </p>
                            <p class="mb-2">
                                <i class="fas fa-calendar-times me-2"></i>
                                <strong>{{ __('certificates.valid_until') }}:</strong> {{ $certificate->expiration_date->format('F j, Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="certificate-stats">
                        <div class="row">
                            <div class="col-6">
                                <div class="stat-item">
                                    <div class="stat-value">{{ $certificate->days_until_expiration }}</div>
                                    <div class="stat-label">{{ __('certificates.days_until_expiration') }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-item">
                                    <div class="stat-value">
                                        <span class="status-badge status-{{ $certificate->status }}">
                                            {{ __('certificates.status_' . $certificate->status) }}
                                        </span>
                                    </div>
                                    <div class="stat-label">{{ __('certificates.status') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('certificates.index') }}">{{ __('certificates.certificates') }}</a></li>
                        <li class="breadcrumb-item active">{{ $certificate->name }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Alert -->
    <div class="row">
        <div class="col-12">
            @if($certificate->status === 'active')
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>{{ __('certificates.certificate_active') }}
                </div>
            @elseif($certificate->status === 'expired')
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ __('certificates.certificate_expired') }}
                </div>
            @elseif($certificate->status === 'pending')
                <div class="alert alert-warning">
                    <i class="fas fa-clock me-2"></i>{{ __('certificates.certificate_pending') }}
                </div>
            @elseif($certificate->status === 'suspended')
                <div class="alert alert-warning">
                    <i class="fas fa-pause me-2"></i>{{ __('certificates.certificate_suspended') }}
                </div>
            @elseif($certificate->status === 'revoked')
                <div class="alert alert-danger">
                    <i class="fas fa-ban me-2"></i>{{ __('certificates.certificate_revoked') }}
                </div>
            @endif

            @if($certificate->is_expiring_soon)
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ __('certificates.certificate_expiring_soon') }}
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="info-card">
                <h5><i class="fas fa-info-circle"></i>{{ __('certificates.basic_information') }}</h5>

                <div class="info-item">
                    <span class="info-label">{{ __('certificates.certificate_name') }}</span>
                    <span class="info-value">{{ $certificate->name }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">{{ __('certificates.subject') }}</span>
                    <span class="info-value">{{ $certificate->subject }}</span>
                </div>

                @if($certificate->description)
                <div class="info-item">
                    <span class="info-label">{{ __('certificates.description') }}</span>
                    <span class="info-value">{{ $certificate->description }}</span>
                </div>
                @endif

                <div class="info-item">
                    <span class="info-label">{{ __('certificates.certificate_type') }}</span>
                    <span class="info-value">{{ __('certificates.type_' . $certificate->certificate_type) }}</span>
                </div>

                @if($certificate->level)
                <div class="info-item">
                    <span class="info-label">{{ __('certificates.level') }}</span>
                    <span class="info-value">{{ __('certificates.level_' . $certificate->level) }}</span>
                </div>
                @endif

                <div class="info-item">
                    <span class="info-label">{{ __('certificates.language') }}</span>
                    <span class="info-value">{{ __('certificates.' . ($certificate->language === 'en' ? 'english' : 'italian')) }}</span>
                </div>

                @if($certificate->company)
                <div class="info-item">
                    <span class="info-label">{{ __('certificates.company') }}</span>
                    <span class="info-value">{{ $certificate->company->name }}</span>
                </div>
                @endif
            </div>

            <!-- Training Information -->
            <div class="info-card">
                <h5><i class="fas fa-graduation-cap"></i>{{ __('certificates.training_information') }}</h5>

                <div class="info-item">
                    <span class="info-label">{{ __('certificates.training_organization') }}</span>
                    <span class="info-value">{{ $certificate->training_organization }}</span>
                </div>

                @if($certificate->training_organization_code)
                <div class="info-item">
                    <span class="info-label">{{ __('certificates.training_organization_code') }}</span>
                    <span class="info-value">{{ $certificate->training_organization_code }}</span>
                </div>
                @endif

                @if($certificate->instructor_name)
                <div class="info-item">
                    <span class="info-label">{{ __('certificates.instructor_name') }}</span>
                    <span class="info-value">{{ $certificate->instructor_name }}</span>
                </div>
                @endif

                @if($certificate->duration_months)
                <div class="info-item">
                    <span class="info-label">{{ __('certificates.duration_months') }}</span>
                    <span class="info-value">{{ $certificate->duration_months }} {{ __('certificates.duration_months') }}</span>
                </div>
                @endif

                @if($certificate->training_organization_address)
                <div class="info-item">
                    <span class="info-label">{{ __('certificates.training_organization_address') }}</span>
                    <span class="info-value">{{ $certificate->training_organization_address }}</span>
                </div>
                @endif
            </div>

            <!-- Certificate Details -->
            <div class="info-card">
                <h5><i class="fas fa-award"></i>{{ __('certificates.certificate_information') }}</h5>

                @if($certificate->certificate_number)
                <div class="info-item">
                    <span class="info-label">{{ __('certificates.certificate_number') }}</span>
                    <span class="info-value verification-code">{{ $certificate->certificate_number }}</span>
                </div>
                @endif

                <div class="info-item">
                    <span class="info-label">{{ __('certificates.issue_date') }}</span>
                    <span class="info-value">{{ $certificate->issue_date->format('F j, Y') }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">{{ __('certificates.expiration_date') }}</span>
                    <span class="info-value">{{ $certificate->expiration_date->format('F j, Y') }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">{{ __('certificates.status') }}</span>
                    <span class="info-value">
                        <span class="status-badge status-{{ $certificate->status }}">
                            {{ __('certificates.status_' . $certificate->status) }}
                        </span>
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label">{{ __('certificates.is_public') }}</span>
                    <span class="info-value">
                        @if($certificate->is_public)
                            <span class="badge bg-success">{{ __('general.yes') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ __('general.no') }}</span>
                        @endif
                    </span>
                </div>
            </div>

            <!-- Assessment Results -->
            @if($certificate->hours_completed || $certificate->credits || $certificate->score || $certificate->grade)
            <div class="info-card">
                <h5><i class="fas fa-chart-line"></i>{{ __('certificates.assessment_information') }}</h5>

                @if($certificate->hours_completed)
                <div class="info-item">
                    <span class="info-label">{{ __('certificates.hours_completed') }}</span>
                    <span class="info-value">{{ number_format($certificate->hours_completed, 2) }}</span>
                </div>
                @endif

                @if($certificate->credits)
                <div class="info-item">
                    <span class="info-label">{{ __('certificates.credits') }}</span>
                    <span class="info-value">{{ number_format($certificate->credits, 2) }}</span>
                </div>
                @endif

                @if($certificate->score)
                <div class="info-item">
                    <span class="info-label">{{ __('certificates.score') }}</span>
                    <span class="info-value">{{ $certificate->score }}/100</span>
                </div>
                @endif

                @if($certificate->grade)
                <div class="info-item">
                    <span class="info-label">{{ __('certificates.grade') }}</span>
                    <span class="info-value">{{ $certificate->grade }}</span>
                </div>
                @endif
            </div>
            @endif

            <!-- Compliance Information -->
            @if($certificate->regulatory_body || $certificate->compliance_standard)
            <div class="info-card">
                <h5><i class="fas fa-balance-scale"></i>{{ __('certificates.compliance_info') }}</h5>

                @if($certificate->regulatory_body)
                <div class="info-item">
                    <span class="info-label">{{ __('certificates.regulatory_body') }}</span>
                    <span class="info-value">{{ $certificate->regulatory_body }}</span>
                </div>
                @endif

                @if($certificate->compliance_standard)
                <div class="info-item">
                    <span class="info-label">{{ __('certificates.compliance_standard') }}</span>
                    <span class="info-value">{{ $certificate->compliance_standard }}</span>
                </div>
                @endif
            </div>
            @endif

            <!-- Renewal Information -->
            @if($certificate->renewal_required)
            <div class="info-card">
                <h5><i class="fas fa-sync-alt"></i>{{ __('certificates.renewal_information') }}</h5>

                <div class="info-item">
                    <span class="info-label">{{ __('certificates.renewal_required') }}</span>
                    <span class="info-value">
                        <span class="badge bg-info">{{ __('general.yes') }}</span>
                    </span>
                </div>

                @if($certificate->renewal_period_months)
                <div class="info-item">
                    <span class="info-label">{{ __('certificates.renewal_period_months') }}</span>
                    <span class="info-value">{{ $certificate->renewal_period_months }} months</span>
                </div>
                @endif

                @if($certificate->next_renewal_date)
                <div class="info-item">
                    <span class="info-label">{{ __('certificates.next_renewal_date') }}</span>
                    <span class="info-value">{{ $certificate->next_renewal_date->format('F j, Y') }}</span>
                </div>
                @endif
            </div>
            @endif

        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Actions Card -->
            <div class="info-card">
                <h5><i class="fas fa-cog"></i>{{ __('certificates.quick_actions') }}</h5>

                <div class="d-grid gap-2">
                    @can('update', $certificate)
                        <a href="{{ route('certificates.edit', $certificate) }}" class="btn btn-primary action-button">
                            <i class="fas fa-edit me-1"></i>{{ __('certificates.edit_certificate_btn') }}
                        </a>
                    @endcan

                    @if($certificate->certificate_file_path)
                        <a href="{{ route('certificates.download', [$certificate, 'certificate']) }}"
                           class="btn btn-success action-button" target="_blank">
                            <i class="fas fa-download me-1"></i>{{ __('certificates.download_certificate') }}
                        </a>
                    @endif

                    @if($certificate->transcript_file_path)
                        <a href="{{ route('certificates.download', [$certificate, 'transcript']) }}"
                           class="btn btn-info action-button" target="_blank">
                            <i class="fas fa-download me-1"></i>{{ __('certificates.download_transcript') }}
                        </a>
                    @endif

                    @can('delete', $certificate)
                        <button type="button" class="btn btn-danger action-button"
                                onclick="confirmDelete('{{ $certificate->name }}')">
                            <i class="fas fa-trash me-1"></i>{{ __('certificates.delete_certificate') }}
                        </button>

                        <form id="delete-form" action="{{ route('certificates.destroy', $certificate) }}"
                              method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endcan

                    <a href="{{ route('certificates.index') }}" class="btn btn-secondary action-button">
                        <i class="fas fa-arrow-left me-1"></i>{{ __('certificates.back_to_certificates') }}
                    </a>
                </div>
            </div>

            <!-- File Attachments -->
            <div class="info-card">
                <h5><i class="fas fa-paperclip"></i>{{ __('certificates.file_attachments') }}</h5>

                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="file-card {{ $certificate->certificate_file_path ? 'has-file' : '' }}">
                            <div class="file-icon">
                                @if($certificate->certificate_file_path)
                                    <i class="fas fa-file-pdf text-danger"></i>
                                @else
                                    <i class="fas fa-file text-muted"></i>
                                @endif
                            </div>
                            <h6>{{ __('certificates.certificate_file') }}</h6>
                            @if($certificate->certificate_file_path)
                                <a href="{{ route('certificates.download', [$certificate, 'certificate']) }}"
                                   class="btn btn-sm btn-success" target="_blank">
                                    <i class="fas fa-download me-1"></i>{{ __('certificates.download') }}
                                </a>
                            @else
                                <p class="text-muted mb-0">{{ __('certificates.no_file_available') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="file-card {{ $certificate->transcript_file_path ? 'has-file' : '' }}">
                            <div class="file-icon">
                                @if($certificate->transcript_file_path)
                                    <i class="fas fa-file-pdf text-danger"></i>
                                @else
                                    <i class="fas fa-file text-muted"></i>
                                @endif
                            </div>
                            <h6>{{ __('certificates.transcript_file') }}</h6>
                            @if($certificate->transcript_file_path)
                                <a href="{{ route('certificates.download', [$certificate, 'transcript']) }}"
                                   class="btn btn-sm btn-info" target="_blank">
                                    <i class="fas fa-download me-1"></i>{{ __('certificates.download') }}
                                </a>
                            @else
                                <p class="text-muted mb-0">{{ __('certificates.no_file_available') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Verification Information -->
            <div class="info-card">
                <h5><i class="fas fa-shield-alt"></i>{{ __('certificates.verification_info') }}</h5>

                @if($certificate->verification_code)
                <div class="text-center mb-3">
                    <div class="qr-code">
                        <div id="qrcode"></div>
                    </div>
                </div>

                <div class="info-item">
                    <span class="info-label">{{ __('certificates.verification_code') }}</span>
                    <span class="info-value">
                        <span class="verification-code">{{ $certificate->verification_code }}</span>
                    </span>
                </div>

                <div class="text-center mt-3">
                    <a href="{{ route('certificates.verify', $certificate->verification_code) }}"
                       class="btn btn-outline-primary btn-sm" target="_blank">
                        <i class="fas fa-external-link-alt me-1"></i>{{ __('certificates.verify_certificate') }}
                    </a>
                </div>
                @else
                <p class="text-muted">{{ __('certificates.no_verification_code') }}</p>
                @endif
            </div>

            <!-- Certificate Metadata -->
            <div class="info-card">
                <h5><i class="fas fa-info"></i>Metadata</h5>

                <div class="info-item">
                    <span class="info-label">{{ __('general.created_at') }}</span>
                    <span class="info-value">{{ $certificate->created_at->format('F j, Y g:i A') }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">{{ __('general.updated_at') }}</span>
                    <span class="info-value">{{ $certificate->updated_at->format('F j, Y g:i A') }}</span>
                </div>

                @if($certificate->verified_at)
                <div class="info-item">
                    <span class="info-label">{{ __('certificates.verified_at') }}</span>
                    <span class="info-value">{{ $certificate->verified_at->format('F j, Y g:i A') }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ensure certificate submenu is expanded
        const certificateSubmenu = document.getElementById('navigation-certificate-managementSubmenu');
        if (certificateSubmenu) {
            certificateSubmenu.classList.add('show');
            const parentToggle = certificateSubmenu.previousElementSibling?.querySelector('.nav-toggle');
            if (parentToggle) {
                parentToggle.classList.add('rotated');
            }
        }

        // Generate QR Code for verification
        @if($certificate->verification_code)
        const qrContainer = document.getElementById('qrcode');
        if (qrContainer) {
            const verifyUrl = '{{ route("certificates.verify", $certificate->verification_code) }}';
            QRCode.toCanvas(qrContainer, verifyUrl, {
                width: 150,
                height: 150,
                color: {
                    dark: '#000',
                    light: '#FFF'
                }
            }, function (error) {
                if (error) {
                    console.error('QR Code generation failed:', error);
                    qrContainer.innerHTML = '<p class="text-muted">QR Code generation failed</p>';
                }
            });
        }
        @endif
    });

    function confirmDelete(certificateName) {
        if (confirm('{{ __("certificates.delete_confirmation") }}'.replace(':name', certificateName))) {
            document.getElementById('delete-form').submit();
        }
    }

    // Copy verification code to clipboard
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            // Show success message
            const toast = document.createElement('div');
            toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed top-0 end-0 m-3';
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        Verification code copied to clipboard!
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            document.body.appendChild(toast);

            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();

            setTimeout(() => {
                document.body.removeChild(toast);
            }, 5000);
        });
    }

    // Add click handler to verification code
    document.addEventListener('DOMContentLoaded', function() {
        const verificationCodes = document.querySelectorAll('.verification-code');
        verificationCodes.forEach(code => {
            code.style.cursor = 'pointer';
            code.title = 'Click to copy';
            code.addEventListener('click', function() {
                copyToClipboard(this.textContent);
            });
        });
    });
</script>
@endpush