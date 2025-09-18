@extends('layouts.advanced-dashboard')

@section('page-title', 'My Certificate')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <!-- Certificate Container -->
            <div class="card shadow-lg border-0" style="background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);">
                <div class="card-body p-5">
                    <!-- Certificate Header -->
                    <div class="text-center mb-5">
                        <div class="d-flex justify-content-center align-items-center mb-4">
                            @if(Auth::user()->primary_company && Auth::user()->primary_company->logo)
                                <img src="{{ Auth::user()->primary_company_logo }}"
                                     alt="{{ Auth::user()->primary_company->name }}"
                                     style="max-height: 80px; width: auto;" class="me-4">
                            @endif
                            <div class="text-start">
                                <h1 class="display-4 fw-bold text-primary mb-2">CERTIFICATE</h1>
                                <p class="h5 text-muted mb-0">{{ Auth::user()->primary_company->name ?? 'STA System' }}</p>
                            </div>
                        </div>
                        <div style="height: 3px; background: linear-gradient(90deg, #4f46e5 0%, #7c3aed 100%); margin: 0 auto; width: 200px;"></div>
                    </div>

                    <!-- Certificate Content -->
                    <div class="certificate-content text-center">
                        <h2 class="h3 text-secondary mb-4">This is to certify that</h2>

                        <div class="name-section mb-5">
                            <h1 class="display-5 fw-bold text-dark border-bottom border-primary pb-2 d-inline-block">
                                {{ Auth::user()->full_name }}
                            </h1>
                        </div>

                        <div class="mb-4">
                            <p class="lead text-muted mb-3">has successfully completed the requirements</p>
                            <p class="lead text-muted mb-3">and is hereby certified as an active member of</p>
                            <h3 class="text-primary fw-bold">{{ Auth::user()->primary_company->name ?? 'Our Organization' }}</h3>
                        </div>

                        <!-- Certificate Details -->
                        <div class="row mt-5">
                            <div class="col-md-6">
                                <div class="certificate-detail">
                                    <p class="text-muted small mb-1">Certificate ID</p>
                                    <p class="fw-bold">CERT-{{ str_pad(Auth::user()->id, 6, '0', STR_PAD_LEFT) }}-{{ date('Y') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="certificate-detail">
                                    <p class="text-muted small mb-1">Issue Date</p>
                                    <p class="fw-bold">{{ Auth::user()->created_at->format('F d, Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- User Details Section -->
                        <div class="mt-5 pt-4 border-top">
                            <div class="row">
                                <div class="col-md-6 text-start">
                                    <h5 class="text-primary mb-3">Personal Information</h5>
                                    <div class="info-row mb-2">
                                        <strong>Email:</strong> {{ Auth::user()->email }}
                                    </div>
                                    @if(Auth::user()->phone)
                                    <div class="info-row mb-2">
                                        <strong>Phone:</strong> {{ Auth::user()->phone }}
                                    </div>
                                    @endif
                                    @if(Auth::user()->date_of_birth)
                                    <div class="info-row mb-2">
                                        <strong>Date of Birth:</strong> {{ Auth::user()->date_of_birth->format('F d, Y') }}
                                    </div>
                                    @endif
                                    @if(Auth::user()->country)
                                    <div class="info-row mb-2">
                                        <strong>Country:</strong> {{ Auth::user()->country }}
                                    </div>
                                    @endif
                                </div>
                                <div class="col-md-6 text-start">
                                    <h5 class="text-primary mb-3">Company Information</h5>
                                    @if(Auth::user()->primary_company)
                                        <div class="info-row mb-2">
                                            <strong>Company:</strong> {{ Auth::user()->primary_company->name }}
                                        </div>
                                        <div class="info-row mb-2">
                                            <strong>Role:</strong> {{ Auth::user()->companies->first()->pivot->role_in_company ?? 'Member' }}
                                        </div>
                                        <div class="info-row mb-2">
                                            <strong>Ownership:</strong> {{ Auth::user()->companies->first()->pivot->percentage ?? 0 }}%
                                        </div>
                                        <div class="info-row mb-2">
                                            <strong>Member Since:</strong> {{ Auth::user()->companies->first()->pivot->joined_at ? \Carbon\Carbon::parse(Auth::user()->companies->first()->pivot->joined_at)->format('F Y') : 'N/A' }}
                                        </div>
                                    @else
                                        <div class="info-row mb-2">
                                            <em class="text-muted">No company assigned</em>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Signature Section -->
                        <div class="mt-5 pt-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="signature-line">
                                        <div style="height: 2px; background: #dee2e6; width: 200px; margin: 0 auto;"></div>
                                        <p class="mt-2 mb-0 small text-muted">System Administrator</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="signature-line">
                                        <div style="height: 2px; background: #dee2e6; width: 200px; margin: 0 auto;"></div>
                                        <p class="mt-2 mb-0 small text-muted">Date</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Official Seal -->
                        <div class="mt-4">
                            <div class="seal d-inline-block p-3" style="border: 3px solid #4f46e5; border-radius: 50%; background: rgba(79, 70, 229, 0.1);">
                                <i class="fas fa-certificate text-primary" style="font-size: 2rem;"></i>
                            </div>
                            <p class="small text-muted mt-2">Official Certificate Seal</p>
                        </div>
                    </div>

                    <!-- Print Button -->
                    <div class="text-center mt-5">
                        <button onclick="window.print()" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-print me-2"></i>
                            Print Certificate
                        </button>
                        <button onclick="downloadCertificate()" class="btn btn-outline-success btn-lg px-5 ms-3">
                            <i class="fas fa-download me-2"></i>
                            Download PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .btn, .navbar, .sidebar, .no-print {
            display: none !important;
        }
        .main-content {
            margin-left: 0 !important;
            padding: 0 !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        body {
            background: white !important;
        }
    }

    .certificate-content {
        min-height: 600px;
    }

    .info-row {
        padding: 2px 0;
    }

    .signature-line {
        margin-top: 30px;
    }
</style>

<script>
    function downloadCertificate() {
        // Simple implementation - in a real app you'd generate a proper PDF
        alert('PDF download feature would be implemented here.\nFor now, you can use the Print button and save as PDF.');
    }
</script>
@endsection