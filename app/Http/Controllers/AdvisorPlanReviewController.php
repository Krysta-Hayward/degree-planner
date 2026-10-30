<?php

namespace App\Http\Controllers;

use App\Models\ProgramMap;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AdvisorPlanReviewController extends Controller
{
    // Maps waiting for this advisor's review
    public function index()
    {
        $studentIds = DB::table('students')
            ->where('advisor', auth()->id())
            ->pluck('student');

        $maps = ProgramMap::where('status', 'submitted')
            ->whereIn('student', $studentIds)
            ->with('owner')
            ->get();

        return view('advisor.plans.index', [
            'maps' => $maps,
        ]);
    }

    // One student's map for review
    public function show(ProgramMap $programMap)
    {
        Gate::authorize('view', $programMap);

        $courses = DB::table('plans')
            ->join('terms', 'plans.class', '=', 'terms.id')
            ->join('courses', 'terms.course', '=', 'courses.id')
            ->where('plans.student', $programMap->student)
            ->orderBy('terms.quarterYear')
            ->orderBy('courses.courseNumber')
            ->select('terms.quarterYear', 'courses.courseNumber', 'courses.name', 'courses.credits')
            ->get()
            ->groupBy('quarterYear');

        return view('advisor.plans.show', [
            'programMap' => $programMap->load('owner'),
            'coursesByQuarter' => $courses,
        ]);
    }

    public function approve(ProgramMap $programMap)
    {
        Gate::authorize('review', $programMap);

        $programMap->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('advisor.plans.index')->with('success', 'Program map approved.');
    }

    public function requestChanges(ProgramMap $programMap)
    {
        Gate::authorize('review', $programMap);

        $programMap->update([
            'status' => 'changes_requested',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('advisor.plans.index')->with('success', 'Changes requested.');
    }
}