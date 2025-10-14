<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('companies.invitation_already_accepted') }} - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
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
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            color: #333;
            padding: 30px;
            text-align: center;
        }
        .invitation-body {
            padding: 40px;
        }
    </style>
</head>
<body>
    <div class="invitation-card">
        <div class="invitation-header">
            <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
            <h2 class="mb-0">{{ __('companies.invitation_already_accepted') }}</h2>
        </div>

        <div class="invitation-body">
            <div class="text-center mb-4">
                <div class="alert alert-success">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <h4>{{ __('companies.invitation_was_accepted') }}</h4>
                    <p class="mb-0">
                        {{ __('companies.accepted_for_company', ['company' => $invitation->company_name, 'date' => $invitation->accepted_at->format('F j, Y \a\t g:i A')]) }}
                    </p>
                </div>
            </div>

            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>{{ __('companies.already_have_account') }}</strong>
                <p class="mb-0 mt-2">
                    {{ __('companies.login_if_manager') }}
                </p>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt me-2"></i>{{ __('companies.go_to_login') }}
                </a>
            </div>

            <div class="text-center mt-3">
                <p class="text-muted small mb-0">
                    {{ __('companies.trouble_logging_in') }}
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
