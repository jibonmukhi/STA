@extends('layouts.advanced-dashboard')

@section('page-title', __('teacher.course_certificates'))

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">{{ __('teacher.course_certificates') }}: {{ $course->title }}</h2>
                    <p class="text-muted">{{ __('teacher.course_code') }}: {{ $course->course_code }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('teacher.course-details', $course) }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> {{ __('teacher.back_to_course') }}
                    </a>
                    <form action="{{ route('teacher.generate-certificates', $course) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success"
                                onclick="return confirm('{{ __('teacher.generate_certificates_confirm') }}')"
                                {{ $stats['pending_certificates'] == 0 ? 'disabled' : '' }}>
                            <i class="fas fa-plus-circle"></i> {{ __('teacher.generate_certificates') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0">{{ $stats['total_enrolled'] }}</h3>
                            <p class="mb-0">{{ __('teacher.total_enrolled') }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-users fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0">{{ $stats['certificates_issued'] }}</h3>
                            <p class="mb-0">{{ __('teacher.certificates_issued') }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-certificate fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0">{{ $stats['pending_certificates'] }}</h3>
                            <p class="mb-0">{{ __('teacher.pending_certificates') }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0">{{ $stats['completion_rate'] }}%</h3>
                            <p class="mb-0">{{ __('teacher.completion_rate') }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-chart-pie fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert if there are pending certificates -->
    @if($stats['pending_certificates'] > 0)
        <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>{{ __('teacher.attention') }}!</strong>
            {{ __('teacher.pending_certificates_message', ['count' => $stats['pending_certificates']]) }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Certificates Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('teacher.student_certificates') }}</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                            <i class="fas fa-print"></i> {{ __('teacher.print') }}
                        </button>
                        <button class="btn btn-sm btn-outline-info" onclick="exportToExcel()">
                            <i class="fas fa-file-excel"></i> {{ __('teacher.export') }}
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($certificates->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover" id="certificatesTable">
                                <thead>
                                    <tr>
                                        <th width="5%">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="selectAll">
                                            </div>
                                        </th>
                                        <th>{{ __('teacher.student') }}</th>
                                        <th>{{ __('teacher.company') }}</th>
                                        <th>{{ __('teacher.certificate_number') }}</th>
                                        <th>{{ __('teacher.issue_date') }}</th>
                                        <th>{{ __('teacher.expiration_date') }}</th>
                                        <th>{{ __('teacher.hours') }}</th>
                                        <th>{{ __('teacher.grade') }}</th>
                                        <th>{{ __('teacher.status') }}</th>
                                        <th>{{ __('teacher.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($certificates as $certificate)
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input certificate-select"
                                                           type="checkbox"
                                                           value="{{ $certificate->id }}">
                                                </div>
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
                                                @if($certificate->user->companies->isNotEmpty())
                                                    <span class="badge bg-secondary">
                                                        {{ $certificate->user->companies->first()->name }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <code>#{{ str_pad($certificate->id, 8, '0', STR_PAD_LEFT) }}</code>
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
                                                        {{ $certificate->expiration_date->format('d/m/Y') }}
                                                    @endif
                                                @else
                                                    <span class="text-muted">{{ __('teacher.no_expiration') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($certificate->hours_completed)
                                                    <span class="badge bg-info">
                                                        {{ $certificate->hours_completed }}h
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($certificate->grade)
                                                    <span class="badge bg-success">{{ $certificate->grade }}</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($certificate->status === 'active')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle"></i> {{ __('teacher.active') }}
                                                    </span>
                                                @elseif($certificate->status === 'expired')
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times-circle"></i> {{ __('teacher.expired') }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $certificate->status }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('certificates.show', $certificate) }}"
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="{{ __('teacher.view') }}"
                                                       target="_blank">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('certificates.download', ['certificate' => $certificate, 'type' => 'pdf']) }}"
                                                       class="btn btn-sm btn-outline-success"
                                                       title="{{ __('teacher.download_pdf') }}">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-outline-info"
                                                            onclick="sendCertificate({{ $certificate->id }})"
                                                            title="{{ __('teacher.send_email') }}">
                                                        <i class="fas fa-envelope"></i>
                                                    </button>
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
                            {{ $certificates->links() }}
                        </div>

                        <!-- Bulk Actions -->
                        <div class="mt-3" id="bulkActions" style="display: none;">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <h6 class="card-title">{{ __('teacher.bulk_actions') }}</h6>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-success" onclick="bulkDownload()">
                                            <i class="fas fa-download"></i> {{ __('teacher.download_selected') }}
                                        </button>
                                        <button class="btn btn-sm btn-info" onclick="bulkEmail()">
                                            <i class="fas fa-paper-plane"></i> {{ __('teacher.email_selected') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-certificate fa-4x text-muted mb-3"></i>
                            <h5>{{ __('teacher.no_certificates_for_course') }}</h5>
                            <p class="text-muted">{{ __('teacher.generate_certificates_hint') }}</p>
                            @if($stats['total_enrolled'] > 0)
                                <form action="{{ route('teacher.generate-certificates', $course) }}" method="POST" class="mt-3">
                                    @csrf
                                    <button type="submit" class="btn btn-primary"
                                            onclick="return confirm('{{ __('teacher.generate_certificates_confirm') }}')">
                                        <i class="fas fa-certificate"></i> {{ __('teacher.generate_certificates_now') }}
                                    </button>
                                </form>
                            @endif
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

.opacity-75 {
    opacity: 0.75;
}

@media print {
    .btn, .form-check, .pagination {
        display: none !important;
    }
}
</style>

<script>
// Select all functionality
document.getElementById('selectAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.certificate-select');
    checkboxes.forEach(cb => cb.checked = this.checked);
    toggleBulkActions();
});

// Individual checkbox change
document.querySelectorAll('.certificate-select').forEach(cb => {
    cb.addEventListener('change', toggleBulkActions);
});

function toggleBulkActions() {
    const selected = document.querySelectorAll('.certificate-select:checked');
    const bulkActions = document.getElementById('bulkActions');
    if (bulkActions) {
        bulkActions.style.display = selected.length > 0 ? 'block' : 'none';
    }
}

function getSelectedCertificates() {
    return Array.from(document.querySelectorAll('.certificate-select:checked'))
        .map(cb => cb.value);
}

function bulkDownload() {
    const selected = getSelectedCertificates();
    if (selected.length === 0) {
        alert('{{ __("teacher.select_certificates_first") }}');
        return;
    }
    // Implementation for bulk download
    console.log('Downloading certificates:', selected);
}

function bulkEmail() {
    const selected = getSelectedCertificates();
    if (selected.length === 0) {
        alert('{{ __("teacher.select_certificates_first") }}');
        return;
    }
    // Implementation for bulk email
    console.log('Emailing certificates:', selected);
}

function sendCertificate(certificateId) {
    if (confirm('{{ __("teacher.confirm_send_certificate") }}')) {
        // Implementation for sending individual certificate
        console.log('Sending certificate:', certificateId);
    }
}

function exportToExcel() {
    // Simple CSV export
    const table = document.getElementById('certificatesTable');
    let csv = [];

    // Get headers
    const headers = Array.from(table.querySelectorAll('thead th'))
        .slice(1) // Skip checkbox column
        .map(th => th.textContent.trim());
    csv.push(headers.join(','));

    // Get data
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const cells = Array.from(row.querySelectorAll('td'))
            .slice(1) // Skip checkbox column
            .map(td => {
                const text = td.textContent.trim().replace(/,/g, ';');
                return `"${text}"`;
            });
        csv.push(cells.join(','));
    });

    // Download
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'certificates_{{ $course->course_code }}_{{ now()->format("Y-m-d") }}.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}
</script>
@endsection