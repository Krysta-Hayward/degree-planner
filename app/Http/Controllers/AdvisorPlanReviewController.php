<?php

namespace App\Http\Controllers;

use App\Models\ProgramMap;
use App\Models\User;
use Illuminate\Http\Request;
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
            'programMap' => $programMap->load('owner', 'comments.author'),
            'coursesByQuarter' => $courses,
        ]);
    }

    // Advisor status: apporves student map (button)
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

    // Advisor status: request changes to student map (button) 
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

    // Save advisor's comment on a program map
    public function storeComment(Request $request, ProgramMap $programMap)
    {
        Gate::authorize('comment', $programMap);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $programMap->comments()->create([
            'user_id' => auth()->id(),
            'body' => $validated['body']
        ]);

        return redirect()
            ->route('advisor.plans.show', $programMap)
            ->with('success', 'Comment added.');
    }


}
