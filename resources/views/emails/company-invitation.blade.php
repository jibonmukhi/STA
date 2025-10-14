<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('companies.company_manager_invitation') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            margin: -30px -30px 30px -30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            margin: 20px 0;
        }
        .credentials-box {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .credentials-box h3 {
            margin-top: 0;
            color: #007bff;
        }
        .credential-item {
            margin: 10px 0;
            padding: 10px;
            background-color: white;
            border-radius: 4px;
        }
        .credential-label {
            font-weight: bold;
            color: #666;
            display: block;
            margin-bottom: 5px;
        }
        .credential-value {
            font-family: 'Courier New', monospace;
            font-size: 16px;
            color: #333;
            background-color: #e9ecef;
            padding: 8px;
            border-radius: 4px;
            display: inline-block;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .company-info {
            background-color: #e7f3ff;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .company-info h3 {
            margin-top: 0;
            color: #0056b3;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        .expiry-notice {
            color: #dc3545;
            font-weight: bold;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ __('companies.company_manager_invitation') }}</h1>
        </div>

        <div class="content">
            <p>{{ __('companies.email_greeting') }} {{ $invitation->manager_name }} {{ $invitation->manager_surname }},</p>

            <p>{{ __('companies.email_invited_as_manager', ['app' => config('app.name')]) }}</p>

            <div class="company-info">
                <h3>{{ __('companies.email_company_details') }}</h3>
                <p><strong>{{ __('companies.company_name') }}:</strong> {{ $invitation->company_name }}</p>
                <p><strong>{{ __('companies.email') }}:</strong> {{ $invitation->company_email }}</p>
                @if($invitation->company_phone)
                    <p><strong>{{ __('companies.phone') }}:</strong> {{ $invitation->company_phone }}</p>
                @endif
                @if($invitation->company_piva)
                    <p><strong>{{ __('companies.piva') }}:</strong> {{ $invitation->company_piva }}</p>
                @endif
            </div>

            <div class="credentials-box">
                <h3>{{ __('companies.email_login_credentials') }}</h3>
                <p>{{ __('companies.email_use_credentials') }}</p>

                <div class="credential-item">
                    <span class="credential-label">{{ __('companies.email_username') }}</span>
                    <span class="credential-value">{{ $invitation->manager_email }}</span>
                </div>

                <div class="credential-item">
                    <span class="credential-label">{{ __('companies.email_temp_password') }}</span>
                    <span class="credential-value">{{ $plainPassword }}</span>
                </div>
            </div>

            <div class="warning">
                <strong>{{ __('companies.email_important') }}</strong> {{ __('companies.email_temp_password_notice') }}
            </div>

            <div style="text-align: center;">
                <a href="{{ $invitationUrl }}" class="button">{{ __('companies.email_accept_and_login') }}</a>
            </div>

            <p class="expiry-notice">
                ⏱️ {{ __('companies.email_expires_on') }} {{ $invitation->expires_at->format('F j, Y \a\t g:i A') }}
            </p>

            <h3>{{ __('companies.email_next_steps') }}</h3>
            <ol>
                <li>{{ __('companies.email_step_1') }}</li>
                <li>{{ __('companies.email_step_2') }}</li>
                <li>{{ __('companies.email_step_3') }}</li>
                <li>{{ __('companies.email_step_4') }}</li>
                <li>{{ __('companies.email_step_5') }}</li>
                <li>{{ __('companies.email_step_6') }}</li>
            </ol>

            <p>{{ __('companies.email_ignore_if_error') }}</p>

            <p>{{ __('companies.email_best_regards') }}<br>
            <strong>{{ __('companies.email_team', ['app' => config('app.name')]) }}</strong></p>
        </div>

        <div class="footer">
            <p>{{ __('companies.email_automated_message') }}</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('companies.email_all_rights_reserved') }}</p>
            <p>{{ __('companies.email_button_not_working') }}<br>
            <a href="{{ $invitationUrl }}">{{ $invitationUrl }}</a></p>
        </div>
    </div>
</body>
</html>
