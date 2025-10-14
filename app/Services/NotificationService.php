<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\UserApprovalRequestNotification;
use App\Notifications\UserApprovedNotification;
use App\Notifications\UserRejectedNotification;

class NotificationService
{
    /**
     * Send user approval request notification to STA managers
     */
    public static function sendUserApprovalRequest($users, User $requestedBy)
    {
        $staManagers = User::role('sta_manager')->get();

        if ($staManagers->isEmpty()) {
            return false;
        }

        Notification::send($staManagers, new UserApprovalRequestNotification($users, $requestedBy));

        return true;
    }

    /**
     * Send user approved notification to company managers
     */
    public static function sendUserApprovedNotification($users, User $approvedBy, $recipients = null)
    {
        if (is_null($recipients)) {
            // Send to the user who requested approval
            // This requires storing the requester info, which we might need to add
            return false;
        }

        Notification::send($recipients, new UserApprovedNotification($users, $approvedBy));

        return true;
    }

    /**
     * Send user rejected notification to company managers
     */
    public static function sendUserRejectedNotification($users, User $rejectedBy, $recipients = null, $reason = null)
    {
        if (is_null($recipients)) {
            // Send to the user who requested approval
            return false;
        }

        Notification::send($recipients, new UserRejectedNotification($users, $rejectedBy, $reason));

        return true;
    }

    /**
     * Send a generic notification
     */
    public static function sendGenericNotification($recipients, $type, $title, $message, $actionUrl = null, $additionalData = [])
    {
        $notificationData = array_merge([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'action_url' => $actionUrl,
        ], $additionalData);

        // Create a generic notification class on the fly
        $notification = new class($notificationData) extends \Illuminate\Notifications\Notification {
            protected $data;

            public function __construct($data)
            {
                $this->data = $data;
            }

            public function via($notifiable)
            {
                return ['database'];
            }

            public function toArray($notifiable)
            {
                return $this->data;
            }
        };

        if (is_array($recipients) || $recipients instanceof \Illuminate\Support\Collection) {
            Notification::send($recipients, $notification);
        } else {
            $recipients->notify($notification);
        }

        return true;
    }

    /**
     * Send course enrollment notification
     */
    public static function sendCourseEnrollmentNotification(User $user, $courseName, $courseUrl)
    {
        return self::sendGenericNotification(
            $user,
            'course_enrollment',
            __('notifications.course_enrollment_title'),
            __('notifications.course_enrollment_message', ['course' => $courseName]),
            $courseUrl
        );
    }

    /**
     * Send certificate issued notification
     */
    public static function sendCertificateIssuedNotification(User $user, $certificateName, $certificateUrl)
    {
        return self::sendGenericNotification(
            $user,
            'certificate_issued',
            __('notifications.certificate_issued_title'),
            __('notifications.certificate_issued_message', ['certificate' => $certificateName]),
            $certificateUrl
        );
    }

    /**
     * Send company invitation notification
     */
    public static function sendCompanyInvitationNotification(User $user, $companyName, $invitationUrl)
    {
        return self::sendGenericNotification(
            $user,
            'company_invitation',
            __('notifications.company_invitation_title'),
            __('notifications.company_invitation_message', ['company' => $companyName]),
            $invitationUrl
        );
    }

    /**
     * Send system update notification to all users or specific roles
     */
    public static function sendSystemUpdateNotification($title, $message, $roles = null, $actionUrl = null)
    {
        $query = User::query();

        if ($roles) {
            $query->role($roles);
        }

        $users = $query->get();

        return self::sendGenericNotification(
            $users,
            'system_update',
            $title,
            $message,
            $actionUrl
        );
    }

    /**
     * Mark all notifications as read for a user
     */
    public static function markAllAsRead(User $user)
    {
        $user->unreadNotifications()->update(['read_at' => now()]);

        return true;
    }

    /**
     * Delete old notifications (cleanup)
     */
    public static function deleteOldNotifications($daysOld = 30)
    {
        \DB::table('notifications')
            ->where('created_at', '<', now()->subDays($daysOld))
            ->delete();

        return true;
    }

    /**
     * Get unread notification count for a user
     */
    public static function getUnreadCount(User $user)
    {
        return $user->unreadNotifications()->count();
    }

    /**
     * Get recent notifications for a user
     */
    public static function getRecentNotifications(User $user, $limit = 5)
    {
        return $user->notifications()
            ->whereNull('read_at')
            ->latest()
            ->limit($limit)
            ->get();
    }
}
