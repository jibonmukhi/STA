<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display all notifications page
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all'); // all, unread, read

        $query = Auth::user()->notifications();

        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        }

        $notifications = $query->paginate(20);

        return view('notifications.index', compact('notifications', 'filter'));
    }

    /**
     * Get recent notifications for dropdown (AJAX)
     */
    public function getRecent()
    {
        $notifications = Auth::user()
            ->unreadNotifications()
            ->take(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->data['type'] ?? 'info',
                    'title' => $notification->data['title'] ?? 'Notification',
                    'message' => $notification->data['message'] ?? '',
                    'action_url' => $notification->data['action_url'] ?? '#',
                    'created_at' => $notification->created_at->diffForHumans(),
                    'icon' => $this->getIconForType($notification->data['type'] ?? 'info'),
                    'color' => $this->getColorForType($notification->data['type'] ?? 'info'),
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => Auth::user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount()
    {
        return response()->json([
            'count' => Auth::user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark a specific notification as read
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => __('notifications.notification_marked_read'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('notifications.notification_not_found'),
        ], 404);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => __('notifications.all_notifications_marked_read'),
        ]);
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->find($id);

        if ($notification) {
            $notification->delete();

            return redirect()->back()->with('success', __('notifications.notification_deleted'));
        }

        return redirect()->back()->with('error', __('notifications.notification_not_found'));
    }

    /**
     * Get icon class based on notification type
     */
    private function getIconForType($type)
    {
        return match($type) {
            'user_approval_request' => 'fas fa-user-clock',
            'user_approved' => 'fas fa-user-check',
            'user_rejected' => 'fas fa-user-times',
            'company_invitation' => 'fas fa-envelope',
            'company_manager_note' => 'fas fa-sticky-note',
            'course_enrollment' => 'fas fa-graduation-cap',
            'certificate_issued' => 'fas fa-certificate',
            'system_update' => 'fas fa-cog',
            'warning' => 'fas fa-exclamation-triangle',
            'success' => 'fas fa-check-circle',
            default => 'fas fa-bell',
        };
    }

    /**
     * Get color class based on notification type
     */
    private function getColorForType($type)
    {
        return match($type) {
            'user_approval_request' => 'warning',
            'user_approved' => 'success',
            'user_rejected' => 'danger',
            'company_invitation' => 'info',
            'company_manager_note' => 'warning',
            'course_enrollment' => 'primary',
            'certificate_issued' => 'success',
            'system_update' => 'info',
            'warning' => 'warning',
            'success' => 'success',
            default => 'secondary',
        };
    }
}
