<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Plan;

class AdvisorPlanReviewController extends Controller
{
   public function index()
   {
        // Will list plans awaiting for this advisor to review

        return view('layouts.admin-advisor-dashboards', [
                'advisorPage' => 'partials.advisor-plans',
            ]);
   }

   public function show(Plan $plan)
   {
        // Show one plan for advisor to review

   }

   public function approve(Plan $plan)
   {
       // Sets status to approve

   }


    public function requestChanges($plan)
    {
        // Request changes on a plan

    }


}
