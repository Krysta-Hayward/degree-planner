<?php
/**
 * W
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Plan;
use Illuminate\Support\Facades\Gate;

class AdvisorPlanReviewController extends Controller
{
   public function index()
   {
        // Will list plans awaiting for this advisor to review
        return view('layouts.admin-advisor-dashboards', [
                'advisorPage' => 'partials.advisor.plans',
            ]);
   }

   public function show(Plan $plan)
   {
        // Show one plan for advisor to review
        Gate::authorize('view', $plan);

        return view('layouts.admin-advisor-dashboards', [
        'advisorPage' => 'partials.advisor-placeholder',
        'planId' => $plan->id,
    ]);

   }

   public function approve(Plan $plan)
   {
       // Sets status to approve
       Gate::authorize('review', $plan);

        return redirect()
        ->route('advisor.plans.show', $plan)
        ->with('success', "Approve clicked for plan {$plan->id}");

   }


    public function requestChanges($plan)
    {
        // Request changes on a plan
        Gate::authorize('review', $plan);

        return redirect()
        ->route('advisor.plans.show', $plan)
        ->with('success', "Request changes clicked for plan {$plan->id}");

    }

}
