<?php

namespace App\Observers;

use App\Models\Course;
use App\Models\CourseEvent;

class CourseObserver
{
    /**
     * Handle the Course "saved" event.
     * This runs after both create and update.
     */
    public function saved(Course $course): void
    {
        // Only sync calendar if course has schedule information
        if ($course->start_date && $course->end_date) {
            $this->syncCourseCalendarEvents($course);
        }
    }

    /**
     * Sync calendar events for all enrolled users and teacher
     */
    protected function syncCourseCalendarEvents(Course $course): void
    {
        // Get all users who should have this event in their calendar
        $usersToSync = collect();

        // Add teacher if assigned
        if ($course->teacher_id) {
            $usersToSync->push($course->teacher_id);
        }

        // Add all enrolled students
        $enrolledUserIds = $course->enrollments()
            ->whereIn('status', ['enrolled', 'in_progress'])
            ->pluck('user_id');

        $usersToSync = $usersToSync->merge($enrolledUserIds)->unique();

        // For each user, create or update their calendar event
        foreach ($usersToSync as $userId) {
            $this->createOrUpdateCalendarEvent($course, $userId);
        }
    }

    /**
     * Create or update a calendar event for a specific user
     */
    protected function createOrUpdateCalendarEvent(Course $course, int $userId): void
    {
        // Check if event already exists for this user and course
        $event = CourseEvent::where('course_id', $course->id)
            ->where('user_id', $userId)
            ->first();

        $eventData = [
            'title' => $course->title,
            'description' => $course->description ?? 'Course: ' . $course->title,
            'course_id' => $course->id,
            'user_id' => $userId,
            'start_date' => $course->start_date,
            'start_time' => $course->start_time,
            'end_date' => $course->end_date,
            'end_time' => $course->end_time,
            'status' => 'scheduled',
        ];

        if ($event) {
            // Update existing event
            $event->update($eventData);
        } else {
            // Create new event
            CourseEvent::create($eventData);
        }
    }

    /**
     * Handle the Course "deleted" event.
     * Remove all calendar events when course is deleted.
     */
    public function deleted(Course $course): void
    {
        // Delete all calendar events associated with this course
        CourseEvent::where('course_id', $course->id)->delete();
    }
}
