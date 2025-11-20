<?php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseUpdateNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $course;

    /**
     * Create a new notification instance.
     */
    public function __construct(Course $course)
    {
        $this->course = $course;
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
        $courseUrl = url('/course-management/' . $this->course->id);

        return (new MailMessage)
            ->subject('Course Update: ' . $this->course->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('This is a notification regarding the course: ' . $this->course->title)
            ->line('Course Code: ' . $this->course->course_code)
            ->line('Status: ' . ucfirst($this->course->status))
            ->line('Start Date: ' . ($this->course->start_date ? $this->course->start_date->format('d/m/Y') : 'TBD'))
            ->action('View Course Details', $courseUrl)
            ->line('Thank you for your participation!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'course_update',
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'course_code' => $this->course->course_code,
            'status' => $this->course->status,
            'message' => 'Course update notification for ' . $this->course->title,
        ];
    }
}
