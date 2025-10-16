<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyNote;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class DebugNotificationController extends Controller
{
    /**
     * Display debug information page
     */
    public function index()
    {
        // Only allow STA managers to access this page
        if (!auth()->check() || !auth()->user()->hasRole('sta_manager')) {
            abort(403, 'Unauthorized access');
        }

        $results = [];

        // 1. Environment Information
        $results['environment'] = [
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug'),
            'app_url' => config('app.url'),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
        ];

        // 2. Queue Configuration
        $results['queue'] = [
            'default_connection' => config('queue.default'),
            'driver' => config('queue.connections.' . config('queue.default') . '.driver'),
            'warning' => config('queue.default') !== 'sync' ? 'Queue worker MUST be running for notifications to work!' : 'Queue is sync - jobs run immediately',
        ];

        // 3. Mail Configuration
        $results['mail'] = [
            'mailer' => config('mail.default'),
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'encryption' => config('mail.mailers.smtp.encryption'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
        ];

        // 4. Database Tables
        $results['database_tables'] = [
            'notifications' => Schema::hasTable('notifications'),
            'company_notes' => Schema::hasTable('company_notes'),
            'jobs' => Schema::hasTable('jobs'),
            'failed_jobs' => Schema::hasTable('failed_jobs'),
        ];

        // 5. Company Managers
        try {
            $companyManagerRole = Role::where('name', 'company_manager')->first();
            if ($companyManagerRole) {
                $managers = User::role('company_manager')->get();
                $results['company_managers'] = [
                    'total_count' => $managers->count(),
                    'managers' => $managers->map(function ($manager) {
                        return [
                            'id' => $manager->id,
                            'name' => $manager->full_name,
                            'email' => $manager->email,
                            'status' => $manager->status,
                            'companies' => $manager->companies->pluck('name')->toArray(),
                        ];
                    }),
                ];
            } else {
                $results['company_managers'] = [
                    'error' => 'company_manager role not found!',
                ];
            }
        } catch (\Exception $e) {
            $results['company_managers'] = [
                'error' => $e->getMessage(),
            ];
        }

        // 6. Recent Company Notes
        try {
            $recentNotes = CompanyNote::with(['sender', 'company'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            $results['company_notes'] = [
                'total_count' => CompanyNote::count(),
                'recent_notes' => $recentNotes->map(function ($note) {
                    return [
                        'id' => $note->id,
                        'subject' => $note->subject,
                        'company' => $note->company->name ?? 'Unknown',
                        'sender' => $note->sender->full_name ?? 'Unknown',
                        'created_at' => $note->created_at->format('Y-m-d H:i:s'),
                    ];
                }),
            ];
        } catch (\Exception $e) {
            $results['company_notes'] = [
                'error' => $e->getMessage(),
            ];
        }

        // 7. Notifications in Database
        try {
            $totalNotifications = DB::table('notifications')->count();
            $companyNoteNotifications = DB::table('notifications')
                ->where('type', 'App\\Notifications\\CompanyManagerNoteNotification')
                ->count();

            $recentNotifications = DB::table('notifications')
                ->where('type', 'App\\Notifications\\CompanyManagerNoteNotification')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            $results['notifications'] = [
                'total_notifications' => $totalNotifications,
                'company_note_notifications' => $companyNoteNotifications,
                'recent_notifications' => $recentNotifications->map(function ($notif) {
                    $data = json_decode($notif->data, true);
                    return [
                        'id' => $notif->id,
                        'user_id' => $notif->notifiable_id,
                        'title' => $data['title'] ?? 'N/A',
                        'message' => $data['message'] ?? 'N/A',
                        'company' => $data['company_name'] ?? 'N/A',
                        'read_at' => $notif->read_at,
                        'created_at' => $notif->created_at,
                    ];
                }),
            ];
        } catch (\Exception $e) {
            $results['notifications'] = [
                'error' => $e->getMessage(),
            ];
        }

        // 8. Queue Jobs
        try {
            $pendingJobs = DB::table('jobs')->count();
            $failedJobs = DB::table('failed_jobs')->count();

            $recentFailedJobs = DB::table('failed_jobs')
                ->orderBy('failed_at', 'desc')
                ->limit(5)
                ->get();

            $results['queue_jobs'] = [
                'pending_jobs' => $pendingJobs,
                'failed_jobs' => $failedJobs,
                'recent_failed' => $recentFailedJobs->map(function ($job) {
                    $payload = json_decode($job->payload, true);
                    return [
                        'id' => $job->id,
                        'queue' => $job->queue,
                        'exception' => substr($job->exception, 0, 200) . '...',
                        'failed_at' => $job->failed_at,
                    ];
                }),
            ];
        } catch (\Exception $e) {
            $results['queue_jobs'] = [
                'error' => $e->getMessage(),
            ];
        }

        // 9. Routes Check
        try {
            $routes = [
                'company-users.index' => \Illuminate\Support\Facades\Route::has('company-users.index'),
                'companies.send-note' => \Illuminate\Support\Facades\Route::has('companies.send-note'),
            ];
            $results['routes'] = $routes;
        } catch (\Exception $e) {
            $results['routes'] = [
                'error' => $e->getMessage(),
            ];
        }

        // 10. Recent Logs
        try {
            $logFile = storage_path('logs/laravel.log');
            if (file_exists($logFile)) {
                $logs = file($logFile);
                $recentLogs = array_slice($logs, -50);
                $results['recent_logs'] = array_reverse($recentLogs);
            } else {
                $results['recent_logs'] = ['Log file not found'];
            }
        } catch (\Exception $e) {
            $results['recent_logs'] = ['Error: ' . $e->getMessage()];
        }

        return view('debug.notifications', compact('results'));
    }

    /**
     * Test sending a notification
     */
    public function testNotification(Request $request)
    {
        // Only allow STA managers to access this
        if (!auth()->check() || !auth()->user()->hasRole('sta_manager')) {
            abort(403, 'Unauthorized access');
        }

        try {
            $companyId = $request->input('company_id');
            $company = Company::findOrFail($companyId);

            // Get company managers
            $companyManagers = $company->users()
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'company_manager');
                })
                ->get();

            if ($companyManagers->isEmpty()) {
                return back()->with('error', 'No company managers found for this company');
            }

            // Create a test note
            $note = CompanyNote::create([
                'company_id' => $company->id,
                'sent_by' => auth()->id(),
                'subject' => 'TEST NOTIFICATION - ' . now()->format('Y-m-d H:i:s'),
                'message' => 'This is a test notification sent from the debug panel. If you receive this, notifications are working!',
            ]);

            // Send notifications
            foreach ($companyManagers as $manager) {
                $manager->notify(new \App\Notifications\CompanyManagerNoteNotification($note));
            }

            return back()->with('success', "Test notification sent to {$companyManagers->count()} manager(s) of {$company->name}. Check their email and notification bell.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
