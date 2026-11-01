<?php
/**
 * Student can view, update (only while in status of draft or request_change), comment on thier own program map. 
 * Advisor can view, review (only while submitted) and comment on thier assigned student'program map,
 * Admin can view all maps  
 */ 
namespace App\Policies;

use App\Models\ProgramMap;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\DB;

class ProgramMapPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProgramMap $map): bool
    {
        return match ($user->role) {
            'admin' => true,
            'student' => (int) $map->student === $user->id,
            'advisor' => $this->isAdvisorOf($user, (int) $map->student),
            default => false,
        };
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProgramMap $map): bool
    {
        return $user->role === 'student'
            && (int) $map->student === $user->id
            && $map->isEditable();
    }

    /**
     * Who can comment on a map
     */
     public function comment(User $user, ProgramMap $map): bool
    {
        return match ($user->role) {
            'student' => (int) $map->student === $user->id,
            'advisor' => $this->isAdvisorOf($user, (int) $map->student),
            default => false,
        };
    }

     // Who can approve or request changes
    public function review(User $user, ProgramMap $map): bool
    {
        return $user->role === 'advisor'
            && $map->status === 'submitted'
            && $this->isAdvisorOf($user, (int) $map->student);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProgramMap $map): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ProgramMap $map): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ProgramMap $programMap): bool
    {
        return false;
    }

     // Is this advisor assigned to this student?
    private function isAdvisorOf(User $advisor, int $studentId): bool
    {
        return DB::table('students')
            ->where('advisor', $advisor->id)
            ->where('student', $studentId)
            ->exists();
    }
}
