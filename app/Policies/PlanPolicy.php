<?php
/**
 * Determines roles for each on who can do what with the plans
 */

namespace App\Policies;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PlanPolicy
{
    /**
     * Determine who can see all plans
     */
    public function viewAny(User $user, Plan $plan): bool
    {
        return match($user->role){
            'admin' => true,
            default => false,
        };
    }

    /**
     * Course can be viewed if its a program map
     */
    public function view(User $user, Plan $plan): bool
    {
        return $plan->programMap !== null
            && $user->can('view', $plan->programMap);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Course row can be added, moved or removed, if its a program map it can be edited
     */
    public function update(User $user, Plan $plan): bool
    {
        return $plan->programMap !== null
            && $user->can('update', $plan->programMap);
    }

    /**
     * Determine who can delete plan.
     * Should just be student && admin?
     */
    public function delete(User $user, Plan $plan): bool
    {
        return $this->update($user, $plan);
    }

    /**
     * Determine who can resotre plan
     * Should be Admin? 
     * Plan gets saved into database?
     */
    public function restore(User $user, Plan $plan): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     * Admin
     */
    public function forceDelete(User $user, Plan $plan): bool
    {
        return false;
    }

    /**
     * If Advisor is assigned to student
     */

    public function isAdvisorOf(User $user, Plan $plan){
        return DB::table('student')
            ->where('advisor'->advisor->id)
            ->where('student'->studentId)
            ->exists();
    }
}
