<?php

namespace App\Notifications;

use App\Models\ProfileChangeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProfileChangeRequestNotification extends Notification
{
    use Queueable;

    protected $changeRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProfileChangeRequest $changeRequest)
    {
        $this->changeRequest = $changeRequest;
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
        $user = $this->changeRequest->user;
        $changedFields = $this->changeRequest->getChangedFields();
        $fieldLabels = $this->getFieldLabels($changedFields);

        $mailMessage = (new MailMessage)
            ->subject('Profile Change Request - ' . $user->full_name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($user->full_name . ' has requested to update their profile information.');

        if ($this->changeRequest->request_message) {
            $mailMessage->line('**Reason:** ' . $this->changeRequest->request_message);
        }

        $mailMessage->line('---')
            ->line('**Fields Requested to Change:**')
            ->line('• ' . implode("\n• ", $fieldLabels))
            ->line('---');

        $mailMessage->action('Review Request', route('company-manager.profile-change-requests.show', $this->changeRequest->id))
            ->line('Please review this request and approve or reject the changes.')
            ->line('Thank you!');

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $user = $this->changeRequest->user;
        $changedFields = $this->changeRequest->getChangedFields();

        return [
            'type' => 'profile_change_request',
            'title' => 'Profile Change Request',
            'message' => $user->full_name . ' requested to update ' . count($changedFields) . ' field(s)',
            'request_id' => $this->changeRequest->id,
            'user_id' => $user->id,
            'user_name' => $user->full_name,
            'user_email' => $user->email,
            'changed_fields' => $changedFields,
            'request_message' => $this->changeRequest->request_message,
            'action_url' => route('company-manager.profile-change-requests.show', $this->changeRequest->id),
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
