<?php

namespace App\Policies;

use App\Models\User;

class ProgramPolicy
{
    /**
     * Determine whether the user can view degree programs in the admin area.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can create degree programs.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can edit degree programs.
     */
    public function update(User $user): bool
    {
        return $user->role === 'admin';
    }
}
