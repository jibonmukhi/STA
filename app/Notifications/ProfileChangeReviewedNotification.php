<?php

namespace App\Notifications;

use App\Models\ProfileChangeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProfileChangeReviewedNotification extends Notification
{
    use Queueable;

    protected $changeRequest;
    protected $approved;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProfileChangeRequest $changeRequest, bool $approved)
    {
        $this->changeRequest = $changeRequest;
        $this->approved = $approved;
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
        $reviewer = $this->changeRequest->reviewer;
        $changedFields = $this->changeRequest->getChangedFields();

        if ($this->approved) {
            $mailMessage = (new MailMessage)
                ->subject('Profile Change Request Approved')
                ->greeting('Hello ' . $notifiable->name . ',')
                ->line('Great news! Your profile change request has been approved.')
                ->line('**Reviewed by:** ' . $reviewer->full_name)
                ->line('**Approved on:** ' . $this->changeRequest->reviewed_at->format('M d, Y H:i'))
                ->line('---')
                ->line('**Updated Fields:**')
                ->line('• ' . implode("\n• ", $this->getFieldLabels($changedFields)))
                ->line('---')
                ->action('View Your Profile', route('profile.edit'))
                ->line('Your profile has been updated successfully.')
                ->line('Thank you!');
        } else {
            $mailMessage = (new MailMessage)
                ->subject('Profile Change Request Rejected')
                ->greeting('Hello ' . $notifiable->name . ',')
                ->line('Your profile change request has been rejected.')
                ->line('**Reviewed by:** ' . $reviewer->full_name)
                ->line('**Rejected on:** ' . $this->changeRequest->reviewed_at->format('M d, Y H:i'));

            if ($this->changeRequest->rejection_reason) {
                $mailMessage->line('---')
                    ->line('**Reason for Rejection:**')
                    ->line($this->changeRequest->rejection_reason)
                    ->line('---');
            }

            $mailMessage->line('If you have questions, please contact your company manager.')
                ->action('View Your Profile', route('profile.edit'))
                ->line('Thank you for your understanding.');
        }

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $reviewer = $this->changeRequest->reviewer;

        return [
            'type' => 'profile_change_reviewed',
            'title' => $this->approved ? 'Profile Change Approved' : 'Profile Change Rejected',
            'message' => $this->approved
                ? 'Your profile changes have been approved by ' . $reviewer->full_name
                : 'Your profile changes have been rejected by ' . $reviewer->full_name,
            'request_id' => $this->changeRequest->id,
            'approved' => $this->approved,
            'reviewer_name' => $reviewer->full_name,
            'reviewed_at' => $this->changeRequest->reviewed_at,
            'rejection_reason' => $this->changeRequest->rejection_reason,
            'action_url' => route('profile.edit'),
        ];
    }

    /**
     * Get human-readable field labels
     */
    private function getFieldLabels(array $fields): array
    {
        $labels = [
            'name' => 'Name',
            'surname' => 'Surname',
            'email' => 'Email',
            'phone' => 'Phone',
            'mobile' => 'Mobile',
            'gender' => 'Gender',
            'date_of_birth' => 'Date of Birth',
            'place_of_birth' => 'Place of Birth',
            'country' => 'Country',
            'cf' => 'Codice Fiscale',
            'address' => 'Address',
            'photo' => 'Profile Photo',
        ];

        return array_map(function($field) use ($labels) {
            return $labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
        }, $fields);
    }
}
