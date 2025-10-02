<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CoursePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Course $course)
    {
        return true;
    }

    public function create(User $user)
    {
        // Only STA managers can create courses
        // Teachers can only manage courses assigned to them by STA managers
        return $user->hasRole(['admin', 'sta_manager']);
    }

    public function update(User $user, Course $course)
    {
        // STA managers can update all courses
        if ($user->hasRole(['admin', 'sta_manager'])) {
            return true;
        }

        // Teachers can only update their own courses
        if ($user->hasRole('teacher')) {
            return $course->teacher_id === $user->id;
        }

        return false;
    }

    public function delete(User $user, Course $course)
    {
        // Only STA managers can delete courses
        // Teachers cannot delete courses, only manage their assigned ones
        return $user->hasRole(['admin', 'sta_manager']);
    }

    public function manageStudents(User $user, Course $course)
    {
        // STA managers can manage all students
        if ($user->hasRole(['admin', 'sta_manager'])) {
            return true;
        }

        // Teachers can only manage students in their own courses
        if ($user->hasRole('teacher')) {
            return $course->teacher_id === $user->id;
        }

        return false;
    }
}