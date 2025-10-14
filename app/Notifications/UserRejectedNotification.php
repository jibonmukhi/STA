<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $userData;
    protected $rejectedBy;
    protected $reason;

    /**
     * Create a new notification instance.
     *
     * Note: Since users are deleted on rejection, we store user data as arrays
     */
    public function __construct($userData, User $rejectedBy, $reason = null)
    {
        // $userData should be array of user data (since users will be deleted)
        $this->userData = is_array($userData[0] ?? null) ? $userData : [$userData];
        $this->rejectedBy = $rejectedBy;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $userCount = count($this->userData);

        $message = (new MailMessage)
            ->subject(__('notifications.mail.user_rejected_subject'))
            ->greeting(__('notifications.mail.greeting', ['name' => $notifiable->name]))
            ->line($userCount === 1
                ? __('notifications.mail.user_rejected_single', ['rejector' => $this->rejectedBy->name])
                : __('notifications.mail.user_rejected_multiple', ['count' => $userCount, 'rejector' => $this->rejectedBy->name]));

        // Add user details
        foreach (array_slice($this->userData, 0, 10) as $userData) {
            $fullName = trim(($userData['name'] ?? '') . ' ' . ($userData['surname'] ?? ''));
            $email = $userData['email'] ?? 'N/A';
            $message->line("✗ {$fullName} ({$email})");
        }

        if ($userCount > 10) {
            $message->line(__('notifications.mail.and_more', ['count' => ($userCount - 10)]));
        }

        if ($this->reason) {
            $message->line(__('notifications.mail.reason_for_rejection', ['reason' => $this->reason]));
        }

        $message->line(__('notifications.mail.contact_sta_manager'))
                ->line(__('notifications.mail.resubmit_info'))
                ->action(__('notifications.mail.submit_new_user'), route('company-users.create'))
                ->line(__('notifications.mail.thank_you_general'));

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $userCount = count($this->userData);
        $firstUser = $this->userData[0] ?? [];
        $firstName = trim(($firstUser['name'] ?? '') . ' ' . ($firstUser['surname'] ?? ''));

        $data = [
            'type' => 'user_rejected',
            'title' => $userCount === 1
                ? __('notifications.user_approval.rejected_title_single')
                : __('notifications.user_approval.rejected_title_multiple'),
            'message' => $userCount === 1
                ? __('notifications.user_approval.rejected_message_single', ['name' => $firstName])
                : __('notifications.user_approval.rejected_message_multiple', ['count' => $userCount]),
            'user_count' => $userCount,
            'user_data' => $this->userData,
            'rejected_by' => $this->rejectedBy->id,
            'rejected_by_name' => $this->rejectedBy->name,
            'action_url' => route('company-users.create'),
        ];

        if ($this->reason) {
            $data['reason'] = $this->reason;
        }

        return $data;
    }
}
