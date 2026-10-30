<?php

/*
ADVISOR HOME PAGE
studentCount - how many students are assigned to an advisor
awaitingReview - plans submitted for this advisor to review
flaggedStudents - students needing attention

Advisor can view program maps for students assigned to them
Advisor can update a student's program map to help plan their degree
Advisor can view course requirements and prerequisites within a program map
Changes to student's program map are saved to the database
*/

namespace App\Http\Controllers;

use App\Models\ProgramMap;
use Illuminate\Support\Facades\DB;

class AdvisorDashboardController extends Controller
{
    public function index()
    {
        // Students assigned to this advisor
        $studentIds = DB::table('students')
            ->where('advisor', auth()->id())
            ->pluck('student');

        $studentCount = $studentIds->count();

        // Program maps submitted by this advisor's students
        $awaitingReview = ProgramMap::where('status', 'submitted')
            ->whereIn('student', $studentIds)
            ->count();

        // TODO: Students with problems (no plan, failed prerequisite)
        $flaggedStudents = 0;

        return view('advisor.dashboard', [
            'studentCount' => $studentCount,
            'awaitingReview' => $awaitingReview,
            'flaggedStudents' => $flaggedStudents,
        ]);
    }
}