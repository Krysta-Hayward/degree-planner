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
     * Determine who can see this plan
     */
    public function view(User $user, Plan $plan): bool
    {
        return match($user->role){
            'student' => $plan->student === $user->id,
            'advisor' => $this->isAdvisorOf($user, $plan->student),
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
     * Determine who can update plan 
     * Should be just student?
     */
    public function update(User $user, Plan $plan): bool
    {
        return false;
    }

    /**
     * Determine who can delete plan.
     * Should just be student && admin?
     */
    public function delete(User $user, Plan $plan): bool
    {
        return false;
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
     * Who can approve or request changes.
     * Advisor
     */

    public function review(User $user, Plan $plan){
        return $user->role === 'advisor' && $this->isAdvisorOf($user, $plan->student);
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
