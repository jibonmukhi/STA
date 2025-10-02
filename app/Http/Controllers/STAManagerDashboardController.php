<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class STAManagerDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::active()->count(),
            'pending_users' => User::parked()->count(),
            'total_companies' => Company::count(),
            'active_companies' => Company::active()->count(),
        ];

        $recentUsers = User::latest()->limit(5)->get();
        $pendingApprovals = User::parked()->latest()->limit(10)->get();

        return view('dashboards.sta-manager', compact('stats', 'recentUsers', 'pendingApprovals'));
    }

    public function pendingApprovals(Request $request): View
    {
        $query = User::with(['roles', 'companies'])
            ->where('status', 'parked');

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
            'total_pending' => User::where('status', 'parked')->count(),
            'pending_this_week' => User::where('status', 'parked')
                ->where('created_at', '>=', now()->startOfWeek())
                ->count(),
            'pending_this_month' => User::where('status', 'parked')
                ->where('created_at', '>=', now()->startOfMonth())
                ->count(),
        ];

        return view('sta-manager.pending-approvals', compact('pendingUsers', 'companies', 'stats'));
    }

    public function approveUser(User $user): RedirectResponse
    {
        try {
            if ($user->status !== 'parked') {
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
                    'old_status' => 'parked',
                    'new_status' => 'active'
                ]
            );

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
            if ($user->status !== 'parked') {
                return redirect()->back()
                    ->with('error', 'User is not in pending status.');
            }

            $userName = $user->full_name;
            $userEmail = $user->email;
            $userId = $user->id;

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
                    'status_before_deletion' => 'parked'
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
            'parked_users' => User::parked()->count(),
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
}
