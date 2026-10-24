<?php

/*
ADVISOR HOME PAGE
studentCount - how many students are assigned to an advisor
awaitingReview - plans submitted for this advisor to review
flaggedStudents - students needing attention

Advisor can view program maps for students assigned to them
Advisor can update a student's program map to help plan thier degree
Advisor can view course requirements and prerequisites within a program map
Changes to student's program map are saved to the database
*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;

class AdvisorDashboardController extends Controller
{
    //
    public function index()
    {
         $advisorId = auth()->id();

         // Students assigned to this advisor
         $studentCount = User::where('advisor_id', auth()->id())->count();
         

         // TODO: Plans with status 'submitted' for this advisor's student
         $awaitingReview = 0;

         // TODO: Students with problems (no plan, failed prerequisite)
         $flaggedStudents = 0;
         

        return view('advisor.dashboard', [
            'advisorPage' => 'partials.advisor-main',
            'studentCount' => $studentCount,
            'awaitingReview' => $awaitingReview,
            'flaggedStudents' => $flaggedStudents,

        ]);
    }
}
