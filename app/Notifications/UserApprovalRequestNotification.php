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
        $companyName = $this->requestedBy->primary_company->name ?? 'a company';

        $message = (new MailMessage)
            ->subject('New User Approval Request')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($userCount === 1
                ? "A new user has been submitted for approval by {$companyName}."
                : "{$userCount} new users have been submitted for approval by {$companyName}.");

        // Add user details
        foreach ($this->users->take(5) as $user) {
            $message->line("• {$user->full_name} ({$user->email})");
        }

        if ($userCount > 5) {
            $message->line("... and " . ($userCount - 5) . " more user(s)");
        }

        $message->line('Please review and approve or reject the user registration(s).')
                ->action('Review Pending Approvals', route('users.pending.approvals'))
                ->line('Thank you for managing the STA system!');

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
        $companyName = $this->requestedBy->primary_company->name ?? 'Unknown Company';

        return [
            'type' => 'user_approval_request',
            'title' => $userCount === 1 ? 'New User Approval Request' : 'New Bulk User Approval Request',
            'message' => $userCount === 1
                ? "New user {$this->users->first()->full_name} submitted for approval by {$companyName}"
                : "{$userCount} users submitted for approval by {$companyName}",
            'user_count' => $userCount,
            'user_ids' => $this->users->pluck('id')->toArray(),
            'requested_by' => $this->requestedBy->id,
            'company_name' => $companyName,
            'action_url' => route('users.pending.approvals'),
        ];
    }
}
