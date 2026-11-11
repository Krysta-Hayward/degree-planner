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
use App\Models\user;
use App\Services\NeedsAttention;
use Illuminate\Support\Facades\DB;

class AdvisorDashboardController extends Controller
{
    public function index()
    {
        // Students assigned to this advisor
        $studentIds = DB::table('students')
            ->where('advisor', auth()->id())
            ->pluck('student');


        return view('advisor.dashboard', [
            'studentCount' => $studentIds->count(),
            'awaitingReview' => ProgramMap::where('status', 'submitted')->whereIn('student', $studentIds)->count(),
            'flaggedCount' => NeedsAttention::forAdvisor(auth()->id())->count(),
        ]);
    }
}
