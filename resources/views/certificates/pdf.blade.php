<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate - {{ $certificate->name }}</title>
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 40px;
            background: #fff;
            position: relative;
        }

        .certificate-container {
            border: 15px solid #4a5568;
            padding: 50px;
            position: relative;
            background: white;
            min-height: 650px;
        }

        .certificate-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .certificate-title {
            font-size: 48px;
            font-weight: bold;
            color: #2d3748;
            margin: 0 0 10px 0;
            text-transform: uppercase;
            letter-spacing: 5px;
        }

        .certificate-subtitle {
            font-size: 20px;
            color: #718096;
            margin: 0;
        }

        .certificate-body {
            text-align: center;
            margin: 40px 0;
        }

        .certify-text {
            font-size: 18px;
            color: #4a5568;
            margin-bottom: 30px;
        }

        .recipient-name {
            font-size: 36px;
            font-weight: bold;
            color: #1a202c;
            margin: 30px 0;
            padding-bottom: 10px;
            border-bottom: 3px solid #4299e1;
            display: inline-block;
        }

        .completion-text {
            font-size: 18px;
            color: #4a5568;
            margin: 30px 0;
            line-height: 1.6;
        }

        .course-name {
            font-size: 28px;
            font-weight: bold;
            color: #2b6cb0;
            margin: 30px 0;
        }

        .certificate-footer {
            margin-top: 60px;
        }

        .footer-content {
            display: table;
            width: 100%;
        }

        .footer-item {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            vertical-align: top;
        }

        .signature-line {
            border-top: 2px solid #cbd5e0;
            margin: 0 20px 10px 20px;
            padding-top: 10px;
        }

        .signature-label {
            font-size: 14px;
            color: #718096;
            font-weight: bold;
        }

        .signature-value {
            font-size: 16px;
            color: #2d3748;
            margin-top: 5px;
        }

        .details-section {
            margin-top: 40px;
            padding: 20px;
            background: #f7fafc;
            border-radius: 8px;
        }

        .detail-row {
            margin: 10px 0;
            font-size: 14px;
        }

        .detail-label {
            font-weight: bold;
            color: #4a5568;
            display: inline-block;
            width: 150px;
        }

        .detail-value {
            color: #2d3748;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120px;
            color: rgba(74, 85, 104, 0.05);
            font-weight: bold;
            z-index: 0;
        }

        .content-wrapper {
            position: relative;
            z-index: 1;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-success {
            background-color: #48bb78;
            color: white;
        }

        .badge-info {
            background-color: #4299e1;
            color: white;
        }

        .organization-logo {
            max-height: 80px;
            margin-bottom: 20px;
        }

        .qr-code {
            position: absolute;
            bottom: 40px;
            right: 40px;
            width: 100px;
            height: 100px;
            padding: 10px;
            background: white;
            border: 1px solid #e2e8f0;
        }

        .certificate-number {
            position: absolute;
            top: 40px;
            right: 40px;
            font-size: 12px;
            color: #718096;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="watermark">CERTIFIED</div>

        <div class="certificate-number">
            Certificate #{{ str_pad($certificate->id, 8, '0', STR_PAD_LEFT) }}
        </div>

        <div class="content-wrapper">
            <div class="certificate-header">
                @if($certificate->training_organization)
                    <div class="certificate-subtitle">{{ $certificate->training_organization }}</div>
                @endif
                <h1 class="certificate-title">Certificate of Completion</h1>
                <div class="certificate-subtitle">{{ __('This is to certify that') }}</div>
            </div>

            <div class="certificate-body">
                <div class="recipient-name">
                    {{ $certificate->user->name }}
                </div>

                <div class="completion-text">
                    {{ __('has successfully completed the course') }}
                </div>

                <div class="course-name">
                    {{ $certificate->subject }}
                </div>

                @if($certificate->name && $certificate->name != $certificate->subject)
                <div class="completion-text">
                    <strong>{{ $certificate->name }}</strong>
                </div>
                @endif

                @if($certificate->description)
                <div class="completion-text" style="font-size: 16px; margin-top: 20px;">
                    {{ $certificate->description }}
                </div>
                @endif
            </div>

            <div class="details-section">
                <div style="display: table; width: 100%;">
                    <div style="display: table-cell; width: 50%;">
                        @if($certificate->hours_completed)
                        <div class="detail-row">
                            <span class="detail-label">{{ __('Duration:') }}</span>
                            <span class="detail-value">{{ $certificate->hours_completed }} {{ __('hours') }}</span>
                        </div>
                        @endif

                        @if($certificate->grade)
                        <div class="detail-row">
                            <span class="detail-label">{{ __('Grade:') }}</span>
                            <span class="detail-value">
                                <span class="badge badge-success">{{ $certificate->grade }}</span>
                            </span>
                        </div>
                        @endif

                        @if($certificate->score)
                        <div class="detail-row">
                            <span class="detail-label">{{ __('Score:') }}</span>
                            <span class="detail-value">{{ $certificate->score }}%</span>
                        </div>
                        @endif
                    </div>
                    <div style="display: table-cell; width: 50%;">
                        @if($certificate->certificate_number)
                        <div class="detail-row">
                            <span class="detail-label">{{ __('Certificate No:') }}</span>
                            <span class="detail-value">{{ $certificate->certificate_number }}</span>
                        </div>
                        @endif

                        @if($certificate->verification_code)
                        <div class="detail-row">
                            <span class="detail-label">{{ __('Verification:') }}</span>
                            <span class="detail-value" style="font-family: monospace;">{{ $certificate->verification_code }}</span>
                        </div>
                        @endif

                        <div class="detail-row">
                            <span class="detail-label">{{ __('Status:') }}</span>
                            <span class="detail-value">
                                <span class="badge badge-{{ $certificate->status == 'active' ? 'success' : 'info' }}">
                                    {{ ucfirst($certificate->status) }}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="certificate-footer">
                <div class="footer-content">
                    <div class="footer-item">
                        <div class="signature-line">
                            <div class="signature-label">{{ __('Issue Date') }}</div>
                            <div class="signature-value">{{ $certificate->issue_date->format('F d, Y') }}</div>
                        </div>
                    </div>

                    <div class="footer-item">
                        <div class="signature-line">
                            <div class="signature-label">{{ __('Expiration Date') }}</div>
                            <div class="signature-value">
                                @if($certificate->expiration_date)
                                    {{ $certificate->expiration_date->format('F d, Y') }}
                                @else
                                    {{ __('No Expiration') }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="footer-item">
                        <div class="signature-line">
                            <div class="signature-label">{{ __('Authorized By') }}</div>
                            <div class="signature-value">
                                @if($certificate->instructor_name)
                                    {{ $certificate->instructor_name }}
                                @else
                                    {{ $certificate->training_organization }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($certificate->verification_code)
        <div style="position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); text-align: center; font-size: 11px; color: #718096;">
            {{ __('Verify this certificate at') }}: {{ url('/verify/' . $certificate->verification_code) }}
        </div>
        @endif
    </div>
</body>
</html>