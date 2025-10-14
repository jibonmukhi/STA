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

    /**
     * Create a new notification instance.
     *
     * Note: Since users are deleted on rejection, we store user data as arrays
     */
    public function __construct($userData, User $rejectedBy)
    {
        // $userData should be array of user data (since users will be deleted)
        $this->userData = is_array($userData[0] ?? null) ? $userData : [$userData];
        $this->rejectedBy = $rejectedBy;
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
            ->subject('User Registration Rejected')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($userCount === 1
                ? "The user registration you submitted has been reviewed and rejected by {$this->rejectedBy->name}."
                : "{$userCount} user registrations you submitted have been reviewed and rejected by {$this->rejectedBy->name}.");

        // Add user details
        foreach (array_slice($this->userData, 0, 10) as $userData) {
            $fullName = trim(($userData['name'] ?? '') . ' ' . ($userData['surname'] ?? ''));
            $email = $userData['email'] ?? 'N/A';
            $message->line("✗ {$fullName} ({$email})");
        }

        if ($userCount > 10) {
            $message->line("... and " . ($userCount - 10) . " more user(s)");
        }

        $message->line('If you believe this was an error or need further clarification, please contact the STA Manager.')
                ->line('You may resubmit the user registration with corrected information if needed.')
                ->action('Submit New User', route('company-users.create'))
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
        $userCount = count($this->userData);
        $firstUser = $this->userData[0] ?? [];
        $firstName = trim(($firstUser['name'] ?? '') . ' ' . ($firstUser['surname'] ?? ''));

        return [
            'type' => 'user_rejected',
            'title' => $userCount === 1 ? 'User Registration Rejected' : 'User Registrations Rejected',
            'message' => $userCount === 1
                ? "User registration for {$firstName} has been rejected"
                : "{$userCount} user registrations have been rejected",
            'user_count' => $userCount,
            'user_data' => $this->userData,
            'rejected_by' => $this->rejectedBy->id,
            'rejected_by_name' => $this->rejectedBy->name,
            'action_url' => route('company-users.create'),
        ];
    }
}
