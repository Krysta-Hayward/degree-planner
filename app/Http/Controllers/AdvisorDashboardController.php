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

class AdvisorDashboardController extends Controller
{
    //
    public function index()
    {
         $advisorId = auth()->id();

         // Students assigned to this advisor
         // $studentCount = User::where('advisor_id', $advisorId->count());

         // TODO: Plans with status 'submitted' for this advisor's student
         // $awaitingReview = count()

         // TODO: Students with problems (no plan, failed prerequisite)
         // $flaggedStudents = count()
         

        return view('layouts.admin-advisor-dashboards', [
            'studentCount' => 0,
            'awaitingReview' => 0,
            'flaggedStudents' => 0,

        ]);
    }
}
