<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    /**
     * Determine whether the user can view any courses.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can view a course.
     */
    public function view(User $user, Course $course): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can create courses.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can update a course.
     */
    public function update(User $user, Course $course): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete a course.
     */
    public function delete(User $user, Course $course): bool
    {
        return $user->role === 'admin';
    }
}
