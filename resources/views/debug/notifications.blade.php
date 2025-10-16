@extends('layouts.advanced-dashboard')

@section('page-title', 'Notification System Debug')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning">
                <h4><i class="fas fa-exclamation-triangle"></i> Debug Panel - For Staging/Production Troubleshooting</h4>
                <p>Use this page to diagnose why notifications are not working on staging/production server.</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Environment Info -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5><i class="fas fa-server"></i> 1. Environment Information</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                @foreach($results['environment'] as $key => $value)
                <tr>
                    <td><strong>{{ ucwords(str_replace('_', ' ', $key)) }}</strong></td>
                    <td>{{ $value }}</td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>

    <!-- Queue Configuration -->
    <div class="card mb-4">
        <div class="card-header {{ $results['queue']['default_connection'] === 'sync' ? 'bg-success' : 'bg-danger' }} text-white">
            <h5><i class="fas fa-tasks"></i> 2. Queue Configuration</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                @foreach($results['queue'] as $key => $value)
                <tr>
                    <td><strong>{{ ucwords(str_replace('_', ' ', $key)) }}</strong></td>
                    <td>
                        {{ $value }}
                        @if($key === 'warning' && strpos($value, 'MUST') !== false)
                            <div class="alert alert-danger mt-2">
                                <strong>CRITICAL:</strong> You need to run a queue worker!<br>
                                <strong>cPanel Solution:</strong> Setup a Cron Job to run every minute:<br>
                                <code>* * * * * cd /home/username/public_html && php artisan queue:work --stop-when-empty</code>
                            </div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>

    <!-- Mail Configuration -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5><i class="fas fa-envelope"></i> 3. Mail Configuration</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                @foreach($results['mail'] as $key => $value)
                <tr>
                    <td><strong>{{ ucwords(str_replace('_', ' ', $key)) }}</strong></td>
                    <td>{{ $value }}</td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>

    <!-- Database Tables -->
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            <h5><i class="fas fa-database"></i> 4. Database Tables</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                @foreach($results['database_tables'] as $table => $exists)
                <tr>
                    <td><strong>{{ $table }}</strong></td>
                    <td>
                        @if($exists)
                            <span class="badge bg-success">✓ EXISTS</span>
                        @else
                            <span class="badge bg-danger">✗ MISSING - Run migrations!</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>

    <!-- Company Managers -->
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">
            <h5><i class="fas fa-users"></i> 5. Company Managers</h5>
        </div>
        <div class="card-body">
            @if(isset($results['company_managers']['error']))
                <div class="alert alert-danger">{{ $results['company_managers']['error'] }}</div>
            @else
                <p><strong>Total Company Managers:</strong> {{ $results['company_managers']['total_count'] }}</p>
                @if($results['company_managers']['total_count'] > 0)
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Companies</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results['company_managers']['managers'] as $manager)
                            <tr>
                                <td>{{ $manager['id'] }}</td>
                                <td>{{ $manager['name'] }}</td>
                                <td>{{ $manager['email'] }}</td>
                                <td><span class="badge bg-{{ $manager['status'] === 'active' ? 'success' : 'warning' }}">{{ $manager['status'] }}</span></td>
                                <td>{{ implode(', ', $manager['companies']) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-danger">No company managers found!</div>
                @endif
            @endif
        </div>
    </div>

    <!-- Company Notes -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5><i class="fas fa-sticky-note"></i> 6. Company Notes</h5>
        </div>
        <div class="card-body">
            @if(isset($results['company_notes']['error']))
                <div class="alert alert-danger">{{ $results['company_notes']['error'] }}</div>
            @else
                <p><strong>Total Notes:</strong> {{ $results['company_notes']['total_count'] }}</p>
                @if(count($results['company_notes']['recent_notes']) > 0)
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Subject</th>
                                <th>Company</th>
                                <th>Sender</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results['company_notes']['recent_notes'] as $note)
                            <tr>
                                <td>{{ $note['id'] }}</td>
                                <td>{{ $note['subject'] }}</td>
                                <td>{{ $note['company'] }}</td>
                                <td>{{ $note['sender'] }}</td>
                                <td>{{ $note['created_at'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-info">No company notes sent yet.</div>
                @endif
            @endif
        </div>
    </div>

    <!-- Notifications -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5><i class="fas fa-bell"></i> 7. Notifications in Database</h5>
        </div>
        <div class="card-body">
            @if(isset($results['notifications']['error']))
                <div class="alert alert-danger">{{ $results['notifications']['error'] }}</div>
            @else
                <p><strong>Total Notifications:</strong> {{ $results['notifications']['total_notifications'] }}</p>
                <p><strong>Company Note Notifications:</strong> {{ $results['notifications']['company_note_notifications'] }}</p>
                @if(count($results['notifications']['recent_notifications']) > 0)
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Title</th>
                                <th>Message</th>
                                <th>Company</th>
                                <th>Read</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results['notifications']['recent_notifications'] as $notif)
                            <tr>
                                <td>{{ $notif['user_id'] }}</td>
                                <td>{{ $notif['title'] }}</td>
                                <td>{{ $notif['message'] }}</td>
                                <td>{{ $notif['company'] }}</td>
                                <td>{{ $notif['read_at'] ? 'Yes' : 'No' }}</td>
                                <td>{{ $notif['created_at'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-warning">No notifications in database! This means notifications are not being saved.</div>
                @endif
            @endif
        </div>
    </div>

    <!-- Queue Jobs -->
    <div class="card mb-4">
        <div class="card-header {{ $results['queue_jobs']['failed_jobs'] > 0 ? 'bg-danger' : 'bg-info' }} text-white">
            <h5><i class="fas fa-list"></i> 8. Queue Jobs Status</h5>
        </div>
        <div class="card-body">
            @if(isset($results['queue_jobs']['error']))
                <div class="alert alert-danger">{{ $results['queue_jobs']['error'] }}</div>
            @else
                <p><strong>Pending Jobs:</strong> {{ $results['queue_jobs']['pending_jobs'] }}</p>
                <p><strong>Failed Jobs:</strong> {{ $results['queue_jobs']['failed_jobs'] }}</p>
                @if($results['queue_jobs']['failed_jobs'] > 0)
                    <div class="alert alert-danger">
                        <strong>Failed jobs detected!</strong> Check the exceptions below.
                    </div>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Queue</th>
                                <th>Exception</th>
                                <th>Failed At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results['queue_jobs']['recent_failed'] as $job)
                            <tr>
                                <td>{{ $job['id'] }}</td>
                                <td>{{ $job['queue'] }}</td>
                                <td><small>{{ $job['exception'] }}</small></td>
                                <td>{{ $job['failed_at'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            @endif
        </div>
    </div>

    <!-- Routes -->
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            <h5><i class="fas fa-route"></i> 9. Required Routes</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                @foreach($results['routes'] as $route => $exists)
                <tr>
                    <td><strong>{{ $route }}</strong></td>
                    <td>
                        @if($exists)
                            <span class="badge bg-success">✓ EXISTS</span>
                        @else
                            <span class="badge bg-danger">✗ MISSING</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>

    <!-- Recent Logs -->
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h5><i class="fas fa-file-alt"></i> 10. Recent Logs (Last 50 lines)</h5>
        </div>
        <div class="card-body">
            <pre style="max-height: 400px; overflow-y: scroll; background: #f5f5f5; padding: 10px; font-size: 11px;">@foreach($results['recent_logs'] as $log){{ $log }}@endforeach</pre>
        </div>
    </div>

    <!-- Test Notification -->
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">
            <h5><i class="fas fa-vial"></i> 11. Send Test Notification</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('debug.test-notification') }}">
                @csrf
                <div class="mb-3">
                    <label>Select Company:</label>
                    <select name="company_id" class="form-select" required>
                        <option value="">-- Select a company --</option>
                        @foreach(\App\Models\Company::all() as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-paper-plane"></i> Send Test Notification
                </button>
                <small class="text-muted d-block mt-2">This will send a real notification to all managers of the selected company.</small>
            </form>
        </div>
    </div>

    <!-- Diagnosis Summary -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5><i class="fas fa-check-circle"></i> Diagnosis Summary</h5>
        </div>
        <div class="card-body">
            <h6>Common Issues & Solutions:</h6>
            <ul>
                <li><strong>Queue not 'sync':</strong> Setup cron job in cPanel to run queue worker every minute</li>
                <li><strong>No company managers:</strong> Assign 'company_manager' role to users</li>
                <li><strong>Failed jobs:</strong> Check exception messages above</li>
                <li><strong>Missing tables:</strong> Run migrations via cPanel terminal or upload migration files</li>
                <li><strong>No notifications in database:</strong> Check if queue worker is running</li>
            </ul>
        </div>
    </div>
</div>
@endsection
