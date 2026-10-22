<?php

/*
Add comment to plan

*/

namespace App\Http\Controllers\Advisor;

use App\Http\Controllers\Controller;
use App\Models\Plan;

class PlanCommentController extends Controller
{
    //
    public function store(Plan $plan)
    {
        return 'Comment on plan'.$plan->id;
    }
}
