<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Program;
use App\Models\Term;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    /**
     * The student's degree plan page: their program, the required courses
     * with prerequisites and offered terms, and their own program map.
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();

        $programs = Program::query()->distinct()->orderBy('name')->pluck('name');

        // every course in the catalog (the story says students see all available courses)
        $catalog = DB::table('courses')
            ->orderBy('courseNumber')
            ->select('id', 'courseNumber', 'name', 'credits')
            ->get();

        $courseIds = $catalog->pluck('id');

        // prerequisites of every course, grouped by course (one query, not one per row)
        $prerequisites = DB::table('course_prerequisites')
            ->join('courses', 'course_prerequisites.prereq', '=', 'courses.id')
            ->whereIn('course_prerequisites.course', $courseIds)
            ->select('course_prerequisites.course as course_id', 'courses.courseNumber', 'courses.name')
            ->get()
            ->groupBy('course_id');

        // terms every course is offered, grouped by course
        $terms = Term::query()
            ->whereIn('course', $courseIds)
            ->orderBy('quarterYear')
            ->get()
            ->groupBy('course');

        // required courses of the student's chosen program
        $requirements = collect();

        if ($user->program) {
            $requiredIds = Program::query()->where('name', $user->program)->pluck('course');
            $requirements = $catalog->whereIn('id', $requiredIds)->values();
        }

        // this student's program map (only their own plan rows)
        $planned = DB::table('plans')
            ->join('terms', 'plans.class', '=', 'terms.id')
            ->join('courses', 'terms.course', '=', 'courses.id')
            ->where('plans.student', $user->id)
            ->orderBy('terms.quarterYear')
            ->orderBy('courses.courseNumber')
            ->select(
                'plans.id as plan_id',
                'terms.id as term_id',
                'terms.quarterYear',
                'courses.courseNumber',
                'courses.name',
                'courses.credits'
            )
            ->get();

        return view('student.dashboard', [
            'user' => $user,
            'programs' => $programs,
            'catalog' => $catalog,
            'requirements' => $requirements,
            'prerequisites' => $prerequisites,
            'terms' => $terms,
            'planned' => $planned,
            'plannedTermIds' => $planned->pluck('term_id')->all(),
        ]);
    }

    /**
     * Save the degree program the student picked.
     */
    public function selectProgram(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'program' => ['required', 'string', 'exists:programs,name'],
        ]);

        $user = $request->user();
        $user->program = $data['program'];
        $user->save();

        return redirect()->route('student.dashboard')->with('status', 'Degree program saved.');
    }

    /**
     * Add a course (a term it is offered) to the student's program map.
     */
    public function addToPlan(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'term' => ['required', 'integer', 'exists:terms,id'],
        ]);

        $user = $request->user();

        // only courses from the student's own program can be planned
        $inProgram = Term::query()
            ->where('id', $data['term'])
            ->whereIn('course', Program::query()->where('name', $user->program)->select('course'))
            ->exists();

        if (! $inProgram) {
            return redirect()->route('student.dashboard')
                ->withErrors(['term' => 'That class is not part of your degree program.']);
        }

        Plan::firstOrCreate([
            'student' => $user->id,
            'class' => $data['term'],
        ]);

        return redirect()->route('student.dashboard')->with('status', 'Added to your program map.');
    }

    /**
     * Remove a course from the student's program map.
     */
    public function removeFromPlan(Request $request, Plan $plan): RedirectResponse
    {
        // a student can never change another student's program map
        abort_if((int) $plan->student !== $request->user()->id, 403);

        $plan->delete();

        return redirect()->route('student.dashboard')->with('status', 'Removed from your program map.');
    }
}
