<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Services\AuditLogService;
use App\Notifications\UserApprovedNotification;
use App\Notifications\UserRejectedNotification;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class STAManagerDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::active()->count(),
            'pending_users' => User::where('status', 'pending_approval')->count(),
            'total_companies' => Company::count(),
            'active_companies' => Company::active()->count(),
        ];

        $recentUsers = User::latest()->limit(5)->get();
        $pendingApprovals = User::where('status', 'pending_approval')->latest()->limit(10)->get();

        return view('dashboards.sta-manager', compact('stats', 'recentUsers', 'pendingApprovals'));
    }

    public function pendingApprovals(Request $request): View
    {
        $query = User::with(['roles', 'companies'])
            ->where('status', 'pending_approval');

        // Apply search filters
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('surname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('company_filter')) {
            $query->whereHas('companies', function($q) use ($request) {
                $q->where('companies.id', $request->get('company_filter'));
            });
        }

        $pendingUsers = $query->latest()->paginate(15);
        $companies = Company::orderBy('name')->get();

        $stats = [
            'total_pending' => User::where('status', 'pending_approval')->count(),
            'pending_this_week' => User::where('status', 'pending_approval')
                ->where('created_at', '>=', now()->startOfWeek())
                ->count(),
            'pending_this_month' => User::where('status', 'pending_approval')
                ->where('created_at', '>=', now()->startOfMonth())
                ->count(),
        ];

        return view('sta-manager.pending-approvals', compact('pendingUsers', 'companies', 'stats'));
    }

    public function approveUser(User $user): RedirectResponse
    {
        try {
            if ($user->status !== 'pending_approval') {
                return redirect()->back()
                    ->with('error', 'User is not in pending status.');
            }

            $user->update(['status' => 'active']);

            // Log user approval
            AuditLogService::logCustom(
                'user_approved',
                "User {$user->full_name} (ID: {$user->id}) was approved by " . Auth::user()->name,
                'users',
                'info',
                [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'approved_by' => Auth::id(),
                    'old_status' => 'pending_approval',
                    'new_status' => 'active'
                ]
            );

            // Notify company managers who created this user
            $companyManagers = User::role('company_manager')
                ->whereHas('companies', function($q) use ($user) {
                    $q->whereIn('companies.id', $user->companies->pluck('id'));
                })
                ->get();

            if ($companyManagers->isNotEmpty()) {
                Notification::send($companyManagers, new UserApprovedNotification($user, Auth::user()));
            }

            return redirect()->back()
                ->with('success', "User {$user->full_name} has been approved successfully.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to approve user: ' . $e->getMessage());
        }
    }

    public function rejectUser(User $user): RedirectResponse
    {
        try {
            if ($user->status !== 'pending_approval') {
                return redirect()->back()
                    ->with('error', 'User is not in pending status.');
            }

            $userName = $user->full_name;
            $userEmail = $user->email;
            $userId = $user->id;

            // Collect user data before deletion for notification
            $userData = [
                'name' => $user->name,
                'surname' => $user->surname,
                'email' => $user->email,
                'full_name' => $user->full_name,
            ];

            // Get company managers to notify before deleting user
            $companyManagers = User::role('company_manager')
                ->whereHas('companies', function($q) use ($user) {
                    $q->whereIn('companies.id', $user->companies->pluck('id'));
                })
                ->get();

            // Log user rejection (before deletion)
            AuditLogService::logCustom(
                'user_rejected',
                "User {$userName} (ID: {$userId}, Email: {$userEmail}) was rejected and deleted by " . Auth::user()->name,
                'users',
                'warning',
                [
                    'user_id' => $userId,
                    'user_email' => $userEmail,
                    'user_name' => $userName,
                    'rejected_by' => Auth::id(),
                    'status_before_deletion' => 'pending_approval'
                ]
            );

            // Delete user photos if they exist
            if ($user->photo) {
                $photoPath = storage_path('app/public/' . $user->photo);
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }
            }

            // Detach from companies
            $user->companies()->detach();

            // Delete the user
            $user->delete();

            // Send rejection notification to company managers
            if ($companyManagers->isNotEmpty()) {
                Notification::send($companyManagers, new UserRejectedNotification($userData, Auth::user()));
            }

            return redirect()->back()
                ->with('success', "User {$userName} has been rejected and removed from the system.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to reject user: ' . $e->getMessage());
        }
    }

    public function systemReports(): View
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::active()->count(),
            'parked_users' => User::where('status', 'parked')->count(),
            'total_companies' => Company::count(),
            'active_companies' => Company::where('active', true)->count(),
        ];

        $usersByRole = User::with('roles')
            ->get()
            ->groupBy(function($user) {
                return $user->roles->first()?->name ?? 'no_role';
            })
            ->map->count();

        $userRegistrationsByMonth = User::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $companyStats = Company::selectRaw('COUNT(*) as total_companies,
                                         SUM(CASE WHEN active = 1 THEN 1 ELSE 0 END) as active_companies')
            ->first();

        $recentActivity = User::with(['roles', 'companies'])
            ->latest()
            ->limit(10)
            ->get();

        return view('sta-manager.system-reports', compact(
            'stats',
            'usersByRole',
            'userRegistrationsByMonth',
            'companyStats',
            'recentActivity'
        ));
    }

    /**
     * Bulk approve multiple users
     */
    public function bulkApproveUsers(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'user_ids' => 'required|array|min:1',
                'user_ids.*' => 'exists:users,id'
            ]);

            $userIds = $request->input('user_ids');
            $users = User::whereIn('id', $userIds)
                ->where('status', 'pending_approval')
                ->get();

            if ($users->isEmpty()) {
                return redirect()->back()
                    ->with('warning', 'No users found with pending status.');
            }

            $approvedCount = 0;
            $skipped = [];

            foreach ($users as $user) {
                if ($user->status !== 'pending_approval') {
                    $skipped[] = $user->full_name;
                    continue;
                }

                $user->update(['status' => 'active']);
                $approvedCount++;

                // Log individual approval
                AuditLogService::logCustom(
                    'user_approved_bulk',
                    "User {$user->full_name} (ID: {$user->id}) was approved in bulk action by " . Auth::user()->name,
                    'users',
                    'info',
                    [
                        'user_id' => $user->id,
                        'user_email' => $user->email,
                        'approved_by' => Auth::id(),
                        'bulk_action' => true
                    ]
                );
            }

            // Get all company managers who need to be notified
            $companyIds = $users->flatMap(function($user) {
                return $user->companies->pluck('id');
            })->unique();

            $companyManagers = User::role('company_manager')
                ->whereHas('companies', function($q) use ($companyIds) {
                    $q->whereIn('companies.id', $companyIds);
                })
                ->get();

            // Send bulk approval notification
            if ($companyManagers->isNotEmpty()) {
                Notification::send($companyManagers, new UserApprovedNotification($users, Auth::user()));
            }

            $message = $approvedCount === 1
                ? "1 user has been approved successfully."
                : "{$approvedCount} users have been approved successfully.";

            if (!empty($skipped)) {
                $message .= " Skipped users (not in pending status): " . implode(', ', $skipped);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to approve users: ' . $e->getMessage());
        }
    }

    /**
     * Bulk reject multiple users
     */
    public function bulkRejectUsers(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'user_ids' => 'required|array|min:1',
                'user_ids.*' => 'exists:users,id'
            ]);

            $userIds = $request->input('user_ids');
            $users = User::whereIn('id', $userIds)
                ->where('status', 'pending_approval')
                ->get();

            if ($users->isEmpty()) {
                return redirect()->back()
                    ->with('warning', 'No users found with pending status.');
            }

            $rejectedCount = 0;
            $usersData = [];

            // Collect data and company managers before deletion
            $companyIds = $users->flatMap(function($user) {
                return $user->companies->pluck('id');
            })->unique();

            $companyManagers = User::role('company_manager')
                ->whereHas('companies', function($q) use ($companyIds) {
                    $q->whereIn('companies.id', $companyIds);
                })
                ->get();

            foreach ($users as $user) {
                if ($user->status !== 'pending_approval') {
                    continue;
                }

                $userName = $user->full_name;
                $userEmail = $user->email;
                $userId = $user->id;

                // Collect user data for notification
                $usersData[] = [
                    'name' => $user->name,
                    'surname' => $user->surname,
                    'email' => $user->email,
                    'full_name' => $user->full_name,
                ];

                // Log rejection
                AuditLogService::logCustom(
                    'user_rejected_bulk',
                    "User {$userName} (ID: {$userId}, Email: {$userEmail}) was rejected in bulk action by " . Auth::user()->name,
                    'users',
                    'warning',
                    [
                        'user_id' => $userId,
                        'user_email' => $userEmail,
                        'user_name' => $userName,
                        'rejected_by' => Auth::id(),
                        'bulk_action' => true
                    ]
                );

                // Delete user photo if exists
                if ($user->photo) {
                    $photoPath = storage_path('app/public/' . $user->photo);
                    if (file_exists($photoPath)) {
                        unlink($photoPath);
                    }
                }

                // Detach from companies
                $user->companies()->detach();

                // Delete user
                $user->delete();
                $rejectedCount++;
            }

            // Send bulk rejection notification
            if ($companyManagers->isNotEmpty() && !empty($usersData)) {
                Notification::send($companyManagers, new UserRejectedNotification($usersData, Auth::user()));
            }

            $message = $rejectedCount === 1
                ? "1 user has been rejected and removed from the system."
                : "{$rejectedCount} users have been rejected and removed from the system.";

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to reject users: ' . $e->getMessage());
        }
    }
}
