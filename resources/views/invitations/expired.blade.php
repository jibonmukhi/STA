<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation Expired - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
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
            <i class="fas fa-clock fa-3x mb-3"></i>
            <h2 class="mb-0">Invitation Expired</h2>
        </div>

        <div class="invitation-body">
            <div class="text-center mb-4">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                    <h4>This invitation has expired</h4>
                    <p class="mb-0">
                        The invitation for <strong>{{ $invitation->company_name }}</strong> expired on
                        <strong>{{ $invitation->expires_at->format('F j, Y \a\t g:i A') }}</strong>.
                    </p>
                </div>
            </div>

            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>What to do next:</strong>
                <p class="mb-0 mt-2">
                    Please contact the system administrator to request a new invitation.
                    They will be able to send you a fresh invitation link.
                </p>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Go to Login Page
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
