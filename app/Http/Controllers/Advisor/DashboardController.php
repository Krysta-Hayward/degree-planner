<?php

/*
ADVISOR HOME PAGE
Stats
Plans
Awaiting Review
Flagged Students
*/

namespace App\Http\Controllers\Advisor;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    //
    public function index()
    {
        return view('advisor.dashboard');
    }
}
