<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuditLogController extends Controller
{
    /**
     * Display a listing of audit logs
     */
    public function index(Request $request)
    {
        // Check if user has permission to view audit logs
        $this->authorize('viewAny', AuditLog::class);

        $query = AuditLog::with('user');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by module
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // Filter by severity
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        // Filter by model type
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search in description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
                  ->orWhere('user_email', 'like', "%{$search}%")
                  ->orWhere('model_name', 'like', "%{$search}%");
            });
        }

        // Get unique values for filters
        $users = User::orderBy('name')->get();
        $actions = AuditLog::distinct()->pluck('action')->sort();
        $modules = AuditLog::distinct()->pluck('module')->sort();
        $modelTypes = AuditLog::distinct()->pluck('model_type')
            ->map(function($type) {
                return $type ? class_basename($type) : null;
            })
            ->filter()
            ->unique()
            ->sort();

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 50);
        $logs = $query->paginate($perPage)->withQueryString();

        // Statistics for dashboard
        $stats = [
            'total_today' => AuditLog::whereDate('created_at', today())->count(),
            'total_week' => AuditLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'total_month' => AuditLog::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'most_active_user' => AuditLog::select('user_name', DB::raw('count(*) as count'))
                ->whereNotNull('user_name')
                ->groupBy('user_name')
                ->orderBy('count', 'desc')
                ->first(),
        ];

        return view('audit-logs.index', compact(
            'logs',
            'users',
            'actions',
            'modules',
            'modelTypes',
            'stats'
        ));
    }

    /**
     * Display the specified audit log
     */
    public function show(AuditLog $auditLog)
    {
        // Check if user has permission to view audit logs
        $this->authorize('view', $auditLog);

        $auditLog->load('user');

        // Try to load the affected model if it exists
        $affectedModel = null;
        if ($auditLog->model_type && $auditLog->model_id) {
            try {
                $affectedModel = $auditLog->model();
            } catch (\Exception $e) {
                // Model might be deleted or class doesn't exist
            }
        }

        // Get related logs (same model or same user in close time proximity)
        $relatedLogs = AuditLog::where(function($query) use ($auditLog) {
            $query->where('user_id', $auditLog->user_id)
                  ->whereBetween('created_at', [
                      $auditLog->created_at->subMinutes(30),
                      $auditLog->created_at->addMinutes(30)
                  ]);
        })
        ->orWhere(function($query) use ($auditLog) {
            if ($auditLog->model_type && $auditLog->model_id) {
                $query->where('model_type', $auditLog->model_type)
                      ->where('model_id', $auditLog->model_id);
            }
        })
        ->where('id', '!=', $auditLog->id)
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

        return view('audit-logs.show', compact('auditLog', 'affectedModel', 'relatedLogs'));
    }

    /**
     * Export audit logs
     */
    public function export(Request $request)
    {
        // Check if user has permission to export audit logs
        $this->authorize('export', AuditLog::class);

        $query = AuditLog::with('user');

        // Apply same filters as index
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->get();

        // Log the export action
        \App\Services\AuditLogService::logExport('audit_logs', $logs->count());

        // Generate CSV
        $filename = 'audit_logs_' . now()->format('Y_m_d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'ID',
                'Date/Time',
                'User',
                'Email',
                'Role',
                'Action',
                'Module',
                'Model',
                'Model ID',
                'Description',
                'IP Address',
                'Severity'
            ]);

            // Data rows
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user_name,
                    $log->user_email,
                    $log->user_role,
                    $log->action,
                    $log->module,
                    $log->model_type ? class_basename($log->model_type) : '',
                    $log->model_id,
                    $log->description,
                    $log->ip_address,
                    $log->severity
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Delete old audit logs
     */
    public function cleanup(Request $request)
    {
        // Check if user has permission to delete audit logs
        $this->authorize('delete', AuditLog::class);

        $request->validate([
            'days' => 'required|integer|min:30|max:365'
        ]);

        $days = $request->input('days', 90);
        $cutoffDate = now()->subDays($days);

        $count = AuditLog::where('created_at', '<', $cutoffDate)->count();

        if ($request->input('confirm') === 'yes') {
            AuditLog::where('created_at', '<', $cutoffDate)->delete();

            // Log the cleanup action
            \App\Services\AuditLogService::logCustom(
                'audit_cleanup',
                "Deleted {$count} audit logs older than {$days} days",
                'system',
                'warning',
                ['deleted_count' => $count, 'days' => $days]
            );

            return redirect()->route('audit-logs.index')
                ->with('success', "Successfully deleted {$count} audit logs older than {$days} days.");
        }

        return view('audit-logs.cleanup', compact('days', 'count', 'cutoffDate'));
    }

    /**
     * Get activity statistics for charts
     */
    public function statistics(Request $request)
    {
        // Check if user has permission to view audit logs
        $this->authorize('viewAny', AuditLog::class);

        $days = $request->input('days', 7);
        $startDate = now()->subDays($days);

        // Activity by day
        $activityByDay = AuditLog::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Activity by action
        $activityByAction = AuditLog::select('action', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('action')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Activity by user
        $activityByUser = AuditLog::select('user_name', DB::raw('COUNT(*) as count'))
            ->whereNotNull('user_name')
            ->where('created_at', '>=', $startDate)
            ->groupBy('user_name')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Activity by module
        $activityByModule = AuditLog::select('module', DB::raw('COUNT(*) as count'))
            ->whereNotNull('module')
            ->where('created_at', '>=', $startDate)
            ->groupBy('module')
            ->orderBy('count', 'desc')
            ->get();

        return response()->json([
            'activityByDay' => $activityByDay,
            'activityByAction' => $activityByAction,
            'activityByUser' => $activityByUser,
            'activityByModule' => $activityByModule,
        ]);
    }
}