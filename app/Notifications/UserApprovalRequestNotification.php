<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserApprovalRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $users;
    protected $requestedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct($users, User $requestedBy)
    {
        // $users can be a single user or collection of users
        $this->users = is_array($users) ? collect($users) : collect([$users]);
        $this->requestedBy = $requestedBy;
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
        $companyName = $this->requestedBy->primary_company->name ?? __('notifications.user_approval.unknown_company');

        $message = (new MailMessage)
            ->subject(__('notifications.mail.approval_request_subject'))
            ->greeting(__('notifications.mail.greeting', ['name' => $notifiable->name]))
            ->line($userCount === 1
                ? __('notifications.mail.approval_request_single', ['company' => $companyName])
                : __('notifications.mail.approval_request_multiple', ['count' => $userCount, 'company' => $companyName]));

        // Add user details
        foreach ($this->users->take(5) as $user) {
            $message->line("• {$user->full_name} ({$user->email})");
        }

        if ($userCount > 5) {
            $message->line(__('notifications.mail.and_more', ['count' => ($userCount - 5)]));
        }

        $message->line(__('notifications.user_approval.please_review'))
                ->action(__('notifications.user_approval.review_pending'), route('users.pending.approvals'))
                ->line(__('notifications.mail.thank_you'));

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
        $companyName = $this->requestedBy->primary_company->name ?? __('notifications.user_approval.unknown_company');

        return [
            'type' => 'user_approval_request',
            'title' => $userCount === 1
                ? __('notifications.user_approval.new_request')
                : __('notifications.user_approval.bulk_request'),
            'message' => $userCount === 1
                ? __('notifications.user_approval.single_user_message', ['name' => $this->users->first()->full_name, 'company' => $companyName])
                : __('notifications.user_approval.multiple_users_message', ['count' => $userCount, 'company' => $companyName]),
            'user_count' => $userCount,
            'user_ids' => $this->users->pluck('id')->toArray(),
            'requested_by' => $this->requestedBy->id,
            'company_name' => $companyName,
            'action_url' => route('users.pending.approvals'),
        ];
    }
}
