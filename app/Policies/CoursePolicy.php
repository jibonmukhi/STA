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
        return $user->hasRole(['admin', 'sta_manager']);
    }

    public function update(User $user, Course $course)
    {
        return $user->hasRole(['admin', 'sta_manager']);
    }

    public function delete(User $user, Course $course)
    {
        return $user->hasRole(['admin', 'sta_manager']);
    }
}