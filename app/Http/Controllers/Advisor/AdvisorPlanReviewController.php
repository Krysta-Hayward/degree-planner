<?php

namespace App\Http\Controllers\Advisor;

use App\Http\Controllers\Controller;
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

        // Needs attention: no program selected
        $noProgram = User::whereIn('id', $studentIds)
            ->whereNull('program')
            ->pluck('id');

        // Needs attention: no courses planned
        $hasPlans = DB::table('plans')
            ->whereIn('student', $studentIds)
            ->distinct()
            ->pluck('student');

        $noPlan = $studentIds->diff($hasPlans);

        // Needs attention: a planned course's prerequisite isn't on the plan
        $missingPrereq = DB::table('plans')
            ->join('terms', 'plans.class', '=', 'terms.id')
            ->join('course_prerequisites', 'course_prerequisites.course', '=', 'terms.course')
            ->whereIn('plans.student', $studentIds)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('plans as p2')
                    ->join('terms as t2', 'p2.class', '=', 't2.id')
                    ->whereColumn('p2.student', 'plans.student')
                    ->whereColumn('t2.course', 'course_prerequisites.prereq');
            })
            ->distinct()
            ->pluck('plans.student');

        // Each student's planned course IDs
        $plannedByStudent = DB::table('plans')
            ->join('terms', 'plans.class', '=', 'terms.id')
            ->whereIn('plans.student', $studentIds)
            ->get(['plans.student', 'terms.course'])
            ->groupBy('student')
            ->map(fn ($rows) => $rows->pluck('course'));

        // Every prerequisite: course → prereq
        $prerequisites = DB::table('course_prerequisites')->get(['course', 'prereq']);

        // A student is flagged if any planned course has a prereq they didn't plan
        $missingPrereq = $plannedByStudent
            ->filter(function ($courses) use ($prerequisites) {
                return $prerequisites->contains(function ($row) use ($courses) {
                    return $courses->contains($row->course)
                        && ! $courses->contains($row->prereq);
                });
            })
            ->keys();

        return view('advisor.plans.index', [
            'maps' => $maps,
        ]);
    }

    // One student's map for review
    public function show(ProgramMap $programMap)
    {
        Gate::authorize('view', $programMap);

        $student = User::find($programMap->student);

        $courses = DB::table('plans')
            ->join('terms', 'plans.class', '=', 'terms.id')
            ->join('courses', 'terms.course', '=', 'courses.id')
            ->where('plans.student', $programMap->student)
            ->orderBy('terms.quarterYear')
            ->orderBy('courses.courseNumber')
            ->select('terms.quarterYear', 'courses.courseNumber', 'courses.name', 'courses.credits')
            ->get()
            ->groupBy('quarterYear');

        $planned = DB::table('plans')
            ->join('terms', 'plans.class', '=', 'terms.id')
            ->where('plans.student', $programMap->student)
            ->pluck('terms.quarterYear', 'terms.course');

        $requirements = DB::table('programs')
            ->join('courses', 'programs.course', '=', 'courses.id')
            ->where('programs.name', $student->program)
            ->orderBy('courses.courseNumber')
            ->select('courses.id', 'courses.courseNumber', 'courses.name', 'courses.credits')
            ->get()
            ->map(function ($course) use ($planned) {
                $course->plannedIn = $planned[$course->id] ?? null;

                return $course;
            });

        // Prerequisites of planned courses, marked planned or missing
        $prerequisites = DB::table('course_prerequisites')
            ->join('courses as main', 'course_prerequisites.course', '=', 'main.id')
            ->join('courses as pre', 'course_prerequisites.prereq', '=', 'pre.id')
            ->whereIn('course_prerequisites.course', $planned->keys())
            ->orderBy('main.courseNumber')
            ->select('main.courseNumber as course_number', 'pre.id as prereq_id', 'pre.courseNumber as prereq_number')
            ->get()
            ->map(function ($row) use ($planned) {
                $row->prereqPlannedIn = $planned[$row->prereq_id] ?? null;

                return $row;
            });

        // Add 'comments.author' after testing students
        return view('advisor.plans.show', [
            'programMap' => $programMap->load('owner', 'comments.author'),
            'coursesByQuarter' => $courses,
            'programName' => $student->program,
            'requirements' => $requirements,
            'prerequisites' => $prerequisites,
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
            'body' => $validated['body'],
        ]);

        return redirect()
            ->route('advisor.plans.show', $programMap)
            ->with('success', 'Comment added.');
    }
}
