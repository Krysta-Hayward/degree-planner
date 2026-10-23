<?php

/*
ADVISOR HOME PAGE
studentCount
pendingPlans
needsAttention
awaitingReview
flaggedStudents
*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class AdvisorDashboardController extends Controller
{
    //
    public function index()
    {
        return view('advisor.dashboard');
    }
}
