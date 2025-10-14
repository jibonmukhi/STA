<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('companies.accept_invitation') }} - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .invitation-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 600px;
            width: 100%;
            overflow: hidden;
        }
        .invitation-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .invitation-body {
            padding: 40px;
        }
        .company-info {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .info-item {
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: 600;
            color: #666;
        }
        .info-value {
            color: #333;
        }
        .btn-accept {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 15px 40px;
            font-size: 18px;
            border-radius: 50px;
            transition: all 0.3s;
        }
        .btn-accept:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        .expiry-notice {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="invitation-card">
        <div class="invitation-header">
            <i class="fas fa-envelope-open-text fa-3x mb-3"></i>
            <h2 class="mb-0">{{ __('companies.company_manager_invitation') }}</h2>
            <p class="mb-0 mt-2">{{ config('app.name') }}</p>
        </div>

        <div class="invitation-body">
            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                </div>
            @endif

            <div class="text-center mb-4">
                <h4>{{ __('companies.welcome') }}, {{ $invitation->manager_name }}!</h4>
                <p class="text-muted">{{ __('companies.invited_to_manage') }} {{ config('app.name') }}</p>
            </div>

            <div class="company-info">
                <h5 class="mb-3">
                    <i class="fas fa-building me-2 text-primary"></i>{{ __('companies.company_details') }}
                </h5>
                <div class="info-item">
                    <span class="info-label">{{ __('companies.company_name') }}:</span>
                    <span class="info-value">{{ $invitation->company_name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">{{ __('companies.company_email') }}:</span>
                    <span class="info-value">{{ $invitation->company_email }}</span>
                </div>
                @if($invitation->company_phone)
                    <div class="info-item">
                        <span class="info-label">{{ __('companies.phone') }}:</span>
                        <span class="info-value">{{ $invitation->company_phone }}</span>
                    </div>
                @endif
                @if($invitation->company_piva)
                    <div class="info-item">
                        <span class="info-label">{{ __('companies.piva') }}:</span>
                        <span class="info-value">{{ $invitation->company_piva }}</span>
                    </div>
                @endif
            </div>

            <div class="expiry-notice">
                <i class="fas fa-clock me-2"></i>
                <strong>{{ __('companies.invitation_expires') }}</strong> {{ $invitation->expires_at->format('F j, Y \a\t g:i A') }}
            </div>

            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>{{ __('companies.next_steps') }}</strong>
                <ol class="mb-0 mt-2 ps-3">
                    <li>{{ __('companies.step_click_accept') }}</li>
                    <li>{{ __('companies.step_redirect_login') }}</li>
                    <li>{{ __('companies.step_use_credentials') }}</li>
                    <li>{{ __('companies.step_change_temp_password') }}</li>
                    <li>{{ __('companies.step_start_managing') }}</li>
                </ol>
            </div>

            <form action="{{ route('invitation.accept.process', ['token' => $invitation->token]) }}" method="POST" class="text-center mt-4">
                @csrf
                <button type="submit" class="btn btn-primary btn-accept">
                    <i class="fas fa-check-circle me-2"></i>{{ __('companies.accept_invitation') }}
                </button>
            </form>

            <div class="text-center mt-4">
                <p class="text-muted small mb-0">
                    {{ __('companies.ignore_if_unexpected') }}
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
