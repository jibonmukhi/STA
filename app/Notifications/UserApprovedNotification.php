<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $users;
    protected $approvedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct($users, User $approvedBy)
    {
        // $users can be a single user or collection of users
        $this->users = is_array($users) ? collect($users) : collect([$users]);
        $this->approvedBy = $approvedBy;
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
        $userCount = $this->users->count();

        $message = (new MailMessage)
            ->subject(__('notifications.mail.user_approved_subject'))
            ->greeting(__('notifications.mail.greeting', ['name' => $notifiable->name]))
            ->line($userCount === 1
                ? __('notifications.mail.user_approved_single', ['approver' => $this->approvedBy->name])
                : __('notifications.mail.user_approved_multiple', ['count' => $userCount, 'approver' => $this->approvedBy->name]));

        // Add user details
        foreach ($this->users->take(10) as $user) {
            $message->line("✓ {$user->full_name} ({$user->email})");
        }

        if ($userCount > 10) {
            $message->line(__('notifications.mail.and_more', ['count' => ($userCount - 10)]));
        }

        $message->line($userCount === 1
                ? __('notifications.mail.account_active_single')
                : __('notifications.mail.account_active_multiple'))
                ->action(__('notifications.mail.view_company_users'), route('company-users.index'))
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
        $userCount = $this->users->count();

        return [
            'type' => 'user_approved',
            'title' => $userCount === 1
                ? __('notifications.user_approval.approved_title_single')
                : __('notifications.user_approval.approved_title_multiple'),
            'message' => $userCount === 1
                ? __('notifications.user_approval.approved_message_single', ['name' => $this->users->first()->full_name])
                : __('notifications.user_approval.approved_message_multiple', ['count' => $userCount]),
            'user_count' => $userCount,
            'user_ids' => $this->users->pluck('id')->toArray(),
            'approved_by' => $this->approvedBy->id,
            'approved_by_name' => $this->approvedBy->name,
            'action_url' => route('company-users.index'),
        ];
    }
}
