<?php

namespace App\Notifications;

use App\Models\Course;
use App\Models\CourseCompanyAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $course;
    public $assignment;
    public $tempPassword;

    /**
     * Create a new notification instance.
     */
    public function __construct(Course $course, CourseCompanyAssignment $assignment, $tempPassword = null)
    {
        $this->course = $course;
        $this->assignment = $assignment;
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
            ->subject('Course Assigned: ' . $this->course->title)
            ->markdown('emails.course-assignment', [
                'course' => $this->course,
                'assignment' => $this->assignment,
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
            'type' => 'course_assigned',
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'company_id' => $this->assignment->company_id,
            'is_mandatory' => $this->assignment->is_mandatory,
            'due_date' => $this->assignment->due_date,
            'message' => 'A new course has been assigned: ' . $this->course->title,
        ];
    }
}
