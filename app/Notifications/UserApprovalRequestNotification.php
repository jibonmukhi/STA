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
        if ($users instanceof \Illuminate\Database\Eloquent\Collection || $users instanceof \Illuminate\Support\Collection) {
            $this->users = $users;
        } elseif (is_array($users)) {
            $this->users = collect($users);
        } else {
            $this->users = collect([$users]);
        }
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
        \Log::info('UserApprovalRequestNotification: Building email', [
            'recipient' => $notifiable->email,
            'user_count' => $this->users->count()
        ]);

        $userCount = $this->users->count();
        $primaryCompany = $this->requestedBy->primaryCompany()->first();
        $companyName = $primaryCompany ? $primaryCompany->name : __('notifications.user_approval.unknown_company');

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

        \Log::info('UserApprovalRequestNotification: Email message built successfully', [
            'recipient' => $notifiable->email,
            'subject' => __('notifications.mail.approval_request_subject')
        ]);

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
        $primaryCompany = $this->requestedBy->primaryCompany()->first();
        $companyName = $primaryCompany ? $primaryCompany->name : __('notifications.user_approval.unknown_company');

        // Get first user safely
        $firstUser = $this->users->first();
        $firstName = $firstUser ? $firstUser->full_name : 'Unknown User';

        return [
            'type' => 'user_approval_request',
            'title' => $userCount === 1
                ? __('notifications.user_approval.new_request')
                : __('notifications.user_approval.bulk_request'),
            'message' => $userCount === 1
                ? __('notifications.user_approval.single_user_message', ['name' => $firstName, 'company' => $companyName])
                : __('notifications.user_approval.multiple_users_message', ['count' => $userCount, 'company' => $companyName]),
            'user_count' => $userCount,
            'user_ids' => $this->users->pluck('id')->toArray(),
            'requested_by' => $this->requestedBy->id,
            'company_name' => $companyName,
            'action_url' => route('users.pending.approvals'),
        ];
    }
}
