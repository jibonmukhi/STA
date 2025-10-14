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
            ->subject('User Approval Confirmation')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($userCount === 1
                ? "Good news! The user you submitted has been approved by {$this->approvedBy->name}."
                : "Good news! {$userCount} users you submitted have been approved by {$this->approvedBy->name}.");

        // Add user details
        foreach ($this->users->take(10) as $user) {
            $message->line("✓ {$user->full_name} ({$user->email})");
        }

        if ($userCount > 10) {
            $message->line("... and " . ($userCount - 10) . " more user(s)");
        }

        $message->line($userCount === 1
                ? 'The user account is now active and can log in to the system.'
                : 'All user accounts are now active and can log in to the system.')
                ->action('View Company Users', route('company-users.index'))
                ->line('Thank you for using the STA system!');

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
            'title' => $userCount === 1 ? 'User Approved' : 'Users Approved',
            'message' => $userCount === 1
                ? "User {$this->users->first()->full_name} has been approved"
                : "{$userCount} users have been approved",
            'user_count' => $userCount,
            'user_ids' => $this->users->pluck('id')->toArray(),
            'approved_by' => $this->approvedBy->id,
            'approved_by_name' => $this->approvedBy->name,
            'action_url' => route('company-users.index'),
        ];
    }
}
