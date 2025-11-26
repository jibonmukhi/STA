<?php

namespace App\Notifications;

use App\Models\Course;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseClosedNotification extends Notification
{
    use Queueable;

    protected $course;
    protected $teacher;

    /**
     * Create a new notification instance.
     */
    public function __construct(Course $course, User $teacher)
    {
        $this->course = $course;
        $this->teacher = $teacher;
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
        return (new MailMessage)
            ->subject('Course Closed: ' . $this->course->title)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A teacher has closed a course.')
            ->line('**Course Details:**')
            ->line('- **Title:** ' . $this->course->title)
            ->line('- **Code:** ' . $this->course->code)
            ->line('- **Teacher:** ' . $this->teacher->name)
            ->line('- **Closed on:** ' . now()->format('d/m/Y H:i'))
            ->line('All course sessions have been completed and the teacher has marked this course as done.')
            ->action('View Course Details', url('/courses/' . $this->course->id))
            ->line('Thank you for your attention.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'course_closed',
            'title' => 'Course Closed',
            'message' => $this->teacher->name . ' has closed the course: ' . $this->course->title . ' (' . $this->course->code . ')',
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'course_code' => $this->course->code,
            'teacher_id' => $this->teacher->id,
            'teacher_name' => $this->teacher->name,
            'closed_at' => now()->toDateTimeString(),
            'action_url' => url('/courses/' . $this->course->id),
        ];
    }
}
