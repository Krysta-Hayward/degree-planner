<?php

/*
Review submitted plan
Approve submitted plan or send it back

*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;

class PlanReviewController extends Controller
{
    //
    public function show(Plan $plan)
    {
        return 'Hellow world from reviewing plan: '.$plan->id;
    }

    public function approve(Plan $plan)
    {
        return 'Approved plan'.$plan->id;
    }

    public function requestChanges(Plan $plan)
    {
        return 'Request changes for plan '.$plan->id;
    }
}
