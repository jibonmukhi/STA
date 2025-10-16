<?php

namespace App\Notifications;

use App\Models\CompanyNote;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompanyManagerNoteNotification extends Notification
{

    protected $companyNote;

    /**
     * Create a new notification instance.
     */
    public function __construct(CompanyNote $companyNote)
    {
        $this->companyNote = $companyNote;
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
        $senderName = $this->companyNote->sender->full_name ?? 'STA Manager';
        $companyName = $this->companyNote->company->name ?? 'your company';
        $userData = $this->companyNote->user_data ?? [];

        $mailMessage = (new MailMessage)
            ->subject('Important Note from STA Manager - ' . $this->companyNote->subject)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have received a note from ' . $senderName . ' regarding ' . $companyName . '.');

        // Add user information if available
        if (!empty($userData)) {
            $mailMessage->line('---')
                ->line('**Regarding User:**')
                ->line('**Name:** ' . ($userData['full_name'] ?? 'N/A'))
                ->line('**Email:** ' . ($userData['email'] ?? 'N/A'));

            if (!empty($userData['cf'])) {
                $mailMessage->line('**Codice Fiscale:** ' . $userData['cf']);
            }

            if (!empty($userData['phone'])) {
                $mailMessage->line('**Phone:** ' . $userData['phone']);
            }

            if (!empty($userData['date_of_birth'])) {
                $mailMessage->line('**Date of Birth:** ' . $userData['date_of_birth'] . ' (' . ($userData['age'] ?? '') . ' years)');
            }

            if (!empty($userData['address'])) {
                $mailMessage->line('**Address:** ' . $userData['address']);
            }

            $mailMessage->line('---');
        }

        $mailMessage->line('**Subject:** ' . $this->companyNote->subject)
            ->line('**Message:**')
            ->line($this->companyNote->message)
            ->action('View Company Users', route('company-users.index'))
            ->line('Please review this message and take any necessary action.')
            ->line('Thank you for your attention.');

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $userData = $this->companyNote->user_data ?? [];

        return [
            'type' => 'company_manager_note',
            'title' => 'Note from STA Manager',
            'message' => $this->companyNote->subject,
            'note_id' => $this->companyNote->id,
            'company_id' => $this->companyNote->company_id,
            'company_name' => $this->companyNote->company->name ?? 'Unknown',
            'sender_name' => $this->companyNote->sender->full_name ?? 'STA Manager',
            'user_id' => $this->companyNote->user_id,
            'user_name' => $userData['full_name'] ?? 'Unknown User',
            'user_email' => $userData['email'] ?? '',
            'user_data' => $userData,
            'action_url' => route('company-users.index'),
        ];
    }
}
