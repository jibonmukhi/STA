<?php

namespace App\Notifications;

use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseEnrollmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $course;
    public $enrollment;
    public $tempPassword;

    /**
     * Create a new notification instance.
     */
    public function __construct(Course $course, CourseEnrollment $enrollment, $tempPassword = null)
    {
        $this->course = $course;
        $this->enrollment = $enrollment;
        $this->tempPassword = $tempPassword;
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
        $message = (new MailMessage)
            ->subject('You have been enrolled in: ' . $this->course->title)
            ->markdown('emails.course-enrollment', [
                'course' => $this->course,
                'enrollment' => $this->enrollment,
                'user' => $notifiable,
                'tempPassword' => $this->tempPassword,
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
        return [
            'type' => 'course_enrollment',
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'enrollment_id' => $this->enrollment->id,
            'status' => $this->enrollment->status,
            'message' => 'You have been enrolled in ' . $this->course->title,
        ];
    }
}
