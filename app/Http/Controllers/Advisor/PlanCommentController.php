<?php
/*
Add comment to plan

*/

namespace App\Http\Controllers\Advisor;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanCommentController extends Controller
{
    //
    public function store(Plan $plan)
    {
        return 'Comment on plan' . $plan->id;    
    }

}
