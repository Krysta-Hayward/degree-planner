<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Enroll;
use App\Models\Plan;
use App\Models\Program;
use App\Models\ProgramMap;
use App\Models\Term;
use App\Support\Quarter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    // Letter grade => grade points (4.0 scale)
    private const GRADE_POINTS = [
        'A' => 4.0, 'A-' => 3.7,
        'B+' => 3.3, 'B' => 3.0, 'B-' => 2.7,
        'C+' => 2.3, 'C' => 2.0, 'C-' => 1.7,
        'D+' => 1.3, 'D' => 1.0,
        'F' => 0.0,
    ];
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
            ->select('course_prerequisites.course as course_id', 'courses.id as prereq_id', 'courses.courseNumber', 'courses.name')
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
                'courses.id as course_id',
                'terms.quarterYear',
                'courses.courseNumber',
                'courses.name',
                'courses.credits'
            )
            ->get();

        /* Marquis */
        // Student program map/advisor as reviewer
        $programMap = ProgramMap::where('student', $user->id)
            ->with('reviewer', 'comments.author')
            ->first();

        // Class times for every course, grouped by course (used for display and conflicts)
        $sections = DB::table('classrooms')
            ->whereIn('course', $courseIds)
            ->orderBy('timeStart')
            ->get()
            ->groupBy('course');

        $conflicts = [];

        foreach ($planned->groupBy('quarterYear') as $quarter => $courses) {
            // The season of this quarter "fall" from "fall2026"
            preg_match('/^([a-z]+)/i', $quarter, $match);
            $season = strtolower($match[1] ?? '');

            $courses = $courses->values();

            // Compare every pair of courses in this quarter
            for ($i = 0; $i < $courses->count(); $i++) {
                for ($j = $i + 1; $j < $courses->count(); $j++) {
                    $a = $courses[$i];
                    $b = $courses[$j];

                    // Sections of each course that run in this season
                    $aSections = ($sections[$a->course_id] ?? collect())->filter(fn ($s) => $s->$season ?? false);
                    $bSections = ($sections[$b->course_id] ?? collect())->filter(fn ($s) => $s->$season ?? false);

                    // No class time for one of them: nothing to compare
                    if ($aSections->isEmpty() || $bSections->isEmpty()) {
                        continue;
                    }

                    // A conflict only if every section of one overlaps every section of the other
                    $alwaysOverlap = $aSections->every(
                        fn ($sa) => $bSections->every(
                            fn ($sb) => $sa->timeStart < $sb->timeEnd && $sb->timeStart < $sa->timeEnd
                        )
                    );

                    if ($alwaysOverlap) {
                        $sa = $aSections->first();
                        $sb = $bSections->first();

                        $conflicts[$quarter][] = [
                            'first' => $a->courseNumber,
                            'second' => $b->courseNumber,
                            'start' => max($sa->timeStart, $sb->timeStart),
                            'end' => min($sa->timeEnd, $sb->timeEnd),
                        ];
                    }
                }
            }
        }

        // Courses this student completed, newest first
        $history = DB::table('histories')
            ->join('courses', 'histories.course', '=', 'courses.id')
            ->where('histories.student', $user->id)
            ->orderByDesc('histories.id')
            ->select(
                'histories.id as history_id',
                'histories.course as course_id',
                'histories.letterGrade',
                'histories.gpa',
                'histories.completed',
                'courses.courseNumber',
                'courses.name',
                'courses.credits'
            )
            ->get();

        // Required courses completed with a passing grade (2.0 or better)
        $passedCourseIds = $history->where('gpa', '>=', 2.0)->pluck('course_id')->unique()->all();

        // Each required course: met, in progress, or not started
        $plannedIds = $planned->pluck('course_id')->all();

        $progress = $requirements->map(function ($course) use ($passedCourseIds, $plannedIds) {
            if (in_array($course->id, $passedCourseIds)) {
                $course->status = 'met';
            } elseif (in_array($course->id, $plannedIds)) {
                $course->status = 'in_progress';
            } else {
                $course->status = 'not_started';
            }

            return $course;
        });

        // Credits completed toward the program, out of its total
        $requiredCredits = $requirements->sum('credits');
        $completedCredits = $progress->where('status', 'met')->sum('credits');
        $creditPercent = $requiredCredits > 0 ? round($completedCredits / $requiredCredits * 100) : 0;

        return view('student.dashboard', [
            'user' => $user,
            'programs' => $programs,
            'catalog' => $catalog,
            'requirements' => $requirements,
            'prerequisites' => $prerequisites,
            'terms' => $terms,
            'planned' => $planned,
            'plannedTermIds' => $planned->pluck('term_id')->all(),
            'plannedCourseIds' => $planned->pluck('course_id')->all(),
            'programMap' => $programMap,
            'sections' => $sections,
            'conflicts' => $conflicts,
            'history' => $history,
            'gradeOptions' => array_keys(self::GRADE_POINTS),
            'progress' => $progress,
            'requiredCredits' => $requiredCredits,
            'completedCredits' => $completedCredits,
            'creditPercent' => $creditPercent,
            'passedCourseIds' => $passedCourseIds,
        ]);
    }

    /**
     * Save the degree program the student picked.
     */
    public function selectProgram(Request $request): RedirectResponse
    {
        // Submitted or approved plan cannot be cleared
        $map = ProgramMap::where('student', $request->user()->id)->first();

        if ($map && ! $map->isEditable()) {
            return $this->toTop()
                ->withErrors(['plan' => 'Your program map is under review or approved and can\'t be changed.']);
        }

        $data = $request->validate([
            'program' => ['required', 'string', 'exists:programs,name'],
        ]);

        $user = $request->user();

        // a different program means the old plan no longer applies
        if ($user->program !== null && $user->program !== $data['program']) {
            DB::table('plans')->where('student', $user->id)->delete();

            // The plan was cleared
            ProgramMap::where('student', $user->id)->update([
                'status' => 'draft',
                'reviewed_by' => null,
                'reviewed_at' => null,
            ]);
        }

        $user->program = $data['program'];
        $user->save();

        return $this->toTop()->with('status', 'Degree program saved.');
    }

    /**
     * Clear the student's chosen program and program map so they can start over.
     */
    public function clearProgram(Request $request): RedirectResponse
    {
        // Submitted or approved plan cannot be cleared
        $map = ProgramMap::where('student', $request->user()->id)->first();

        if ($map && ! $map->isEditable()) {
            return $this->toTop()
                ->withErrors(['plan' => 'Your program map is under review or approved and can\'t be changed.']);
        }

        $user = $request->user();
        $user->program = null;
        $user->save();

        // the old plan belongs to the old program, so start it over too
        DB::table('plans')->where('student', $user->id)->delete();

        // The plan was cleared
        ProgramMap::where('student', $user->id)->update([
            'status' => 'draft',
            'reviewed_by' => null,
            'reviewed_at' => null,
        ]);

        return $this->toTop()->with('status', 'Degree program and program map cleared.');
    }

    /**
     * Add a course (a term it is offered) to the student's program map.
     */
    public function addToPlan(Request $request): RedirectResponse
    {

        /* Marquis */
        // A submitted or approved program map can't be changed
        $map = ProgramMap::where('student', $request->user()->id)->first();

        if ($map && ! $map->isEditable()) {
            return $this->toTop()
                ->withErrors(['plan' => 'Your program map is under review or approved and can\'t be changed.']);
        }

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
            return $this->toTop()
                ->withErrors(['term' => 'That class is not part of your degree program.']);
        }

        $courseId = Term::query()->whereKey($data['term'])->value('course');

        // A course already passed doesn't need planning
        if ($this->passedCourseIds($user->id)->contains($courseId)) {
            return $this->toTop()
                ->withErrors(['term' => 'You already completed that course.']);
        }

        // A course can only be on the plan once; use Move to change its quarter
        $alreadyPlanned = DB::table('plans')
            ->join('terms', 'plans.class', '=', 'terms.id')
            ->where('plans.student', $user->id)
            ->where('terms.course', $courseId)
            ->exists();

        if ($alreadyPlanned) {
            return $this->toTop()
                ->withErrors(['term' => 'That course is already on your program map. Use Move to change its quarter.']);
        }

        // every prerequisite has to be on the student's program map first (or already passed)
        $missing = $this->missingPrerequisites($user->id, $courseId);

        if ($missing->isNotEmpty()) {
            return $this->toTop()
                ->withErrors(['term' => 'Add '.$missing->join(', ').' to your program map first.']);
        }

        // Planned prerequisites must be in an earlier quarter
        $newQuarter = Term::whereKey($data['term'])->value('quarterYear');

        $prereqQuarters = DB::table('plans')
            ->join('terms', 'plans.class', '=', 'terms.id')
            ->join('course_prerequisites', 'course_prerequisites.prereq', '=', 'terms.course')
            ->where('plans.student', $user->id)
            ->where('course_prerequisites.course', $courseId)
            ->pluck('terms.quarterYear');

        foreach ($prereqQuarters as $prereqQuarter) {
            if (Quarter::order($prereqQuarter) >= Quarter::order($newQuarter)) {
                return $this->toTop()
                    ->withErrors(['term' => 'Pick a quarter after '.$prereqQuarter.', when its prerequisite is planned.']);
            }
        }

        Plan::firstOrCreate([
            'student' => $user->id,
            'class' => $data['term'],
        ]);

        return $this->toTop()->with('status', 'Added to your program map.');
    }

    /**
     * Remove a course from the student's program map.
     */
    public function removeFromPlan(Request $request, Plan $plan): RedirectResponse
    {
        // A submitted or approved program map can't be changed
        $map = ProgramMap::where('student', $request->user()->id)->first();

        if ($map && ! $map->isEditable()) {
            return $this->toTop()
                ->withErrors(['plan' => 'Your program map is under review or approved and can\'t be changed.']);
        }

        // a student can never change another student's program map
        abort_if((int) $plan->student !== $request->user()->id, 403);

        $courseId = Term::query()->whereKey($plan->class)->value('course');

        // If the student already passed it, planned courses that need it are still covered
        $alreadyPassed = $this->passedCourseIds($plan->student)->contains($courseId);

        if (! $alreadyPassed) {
            // a class that is still on the map may need this one as a prerequisite
            $dependents = DB::table('plans')
                ->join('terms', 'plans.class', '=', 'terms.id')
                ->join('course_prerequisites', 'course_prerequisites.course', '=', 'terms.course')
                ->join('courses', 'terms.course', '=', 'courses.id')
                ->where('plans.student', $plan->student)
                ->where('course_prerequisites.prereq', $courseId)
                ->pluck('courses.courseNumber');

            if ($dependents->isNotEmpty()) {
                return $this->toTop()
                    ->withErrors(['plan' => 'Remove '.$dependents->unique()->join(', ').' first, it needs this class.']);
            }
        }

        $plan->delete();

        return $this->toTop()->with('status', 'Removed from your program map.');
    }

    /**
     * Move a course in the student's program map to a different quarter.
     */
    public function movePlan(Request $request, Plan $plan): RedirectResponse
    {
        // A submitted or approved program map can't be changed
        $map = ProgramMap::where('student', $request->user()->id)->first();

        if ($map && ! $map->isEditable()) {
            return $this->toTop()
                ->withErrors(['plan' => 'Your program map is under review or approved and can\'t be changed.']);
        }

        // A student can only move courses in their own program map
        abort_if((int) $plan->student !== $request->user()->id, 403);

        $data = $request->validate([
            'term' => ['required', 'integer', 'exists:terms,id'],
        ]);

        // The new quarter must be an offering of the same course
        $currentCourse = Term::whereKey($plan->class)->value('course');
        $newCourse = Term::whereKey($data['term'])->value('course');

        if ($currentCourse !== $newCourse) {
            return $this->toTop()
                ->withErrors(['term' => 'You can only move a course to another quarter it is offered.']);
        }

        $newQuarter = Term::whereKey($data['term'])->value('quarterYear');
        $studentId = $request->user()->id;

        // This course's planned prerequisites must still come before its new quarter
        $prereqQuarters = DB::table('plans')
            ->join('terms', 'plans.class', '=', 'terms.id')
            ->join('course_prerequisites', 'course_prerequisites.prereq', '=', 'terms.course')
            ->where('plans.student', $studentId)
            ->where('course_prerequisites.course', $currentCourse)
            ->pluck('terms.quarterYear');

        foreach ($prereqQuarters as $prereqQuarter) {
            if (Quarter::order($prereqQuarter) >= Quarter::order($newQuarter)) {
                return $this->toTop()
                    ->withErrors(['term' => 'Pick a quarter after '.$prereqQuarter.', when its prerequisite is planned.']);
            }
        }

        // Courses that need this one must still come after its new quarter, unless it's already passed
        if (! $this->passedCourseIds($studentId)->contains($currentCourse)) {
            $dependentQuarters = DB::table('plans')
                ->join('terms', 'plans.class', '=', 'terms.id')
                ->join('course_prerequisites', 'course_prerequisites.course', '=', 'terms.course')
                ->where('plans.student', $studentId)
                ->where('course_prerequisites.prereq', $currentCourse)
                ->pluck('terms.quarterYear');

            foreach ($dependentQuarters as $dependentQuarter) {
                if (Quarter::order($newQuarter) >= Quarter::order($dependentQuarter)) {
                    return $this->toTop()
                        ->withErrors(['term' => 'Pick a quarter before '.$dependentQuarter.', when a course that needs this one is planned.']);
                }
            }
        }

        $plan->update(['class' => $data['term']]);

        return $this->toTop()->with('status', 'Course moved.');
    }

    /**
     * Record a course the student completed, with its grade.
     */
    public function storeHistory(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'course' => ['required', 'integer', 'exists:courses,id'],
            'letterGrade' => ['required', 'in:'.implode(',', array_keys(self::GRADE_POINTS))],
            'season' => ['required', 'in:Fall,Winter,Spring,Summer'],
            'year' => ['required', 'integer', 'min:2000', 'max:'.now()->year],
        ]);

        // Same format as terms, e.g. "Fall 2025"
        $completed = $data['season'].' '.$data['year'];

        // The same course can't be recorded twice for the same quarter
        $alreadyRecorded = DB::table('histories')
            ->where('student', $user->id)
            ->where('course', $data['course'])
            ->where('completed', $completed)
            ->exists();

        if ($alreadyRecorded) {
            return $this->toTop()
                ->withErrors(['course' => 'That course is already recorded for '.$completed.'.']);
        }

        DB::table('histories')->insert([
            'student' => $user->id,
            'course' => $data['course'],
            'letterGrade' => $data['letterGrade'],
            'gpa' => self::GRADE_POINTS[$data['letterGrade']],
            'completed' => $completed,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $this->toTop()->with('status', 'Completed course recorded.');
    }

    /**
     * Remove a completed course the student recorded.
     */
    public function destroyHistory(Request $request, int $history): RedirectResponse
    {
        $deleted = DB::table('histories')
            ->where('id', $history)
            ->where('student', $request->user()->id)
            ->delete();

        abort_if($deleted === 0, 404);

        return $this->toTop()->with('status', 'Completed course removed.');
    }

    /* {{ Marquis }} */
    // Submit program map to advisor
    public function submitPlan(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Can't submit an empty plan
        if (! DB::table('plans')->where('student', $user->id)->exists()) {
            return $this->toTop()
                ->withErrors(['plan' => 'Add courses to your program map before submitting.']);
        }

        // The student's map, created as a draft the first time
        $map = ProgramMap::firstOrCreate(['student' => $user->id]);

        // Only drafts, or plans sent back, can be submitted
        if (! $map->isEditable()) {
            return $this->toTop()
                ->withErrors(['plan' => 'Your program map is already submitted or approved.']);
        }

        $map->update(['status' => 'submitted']);

        return $this->toTop()->with('status', 'Program map submitted to your advisor.');
    }

    /**
     * Course numbers of prerequisites the student has not put on their program map yet.
     */
    private function missingPrerequisites(int $studentId, int $courseId)
    {
        $plannedCourseIds = DB::table('plans')
            ->join('terms', 'plans.class', '=', 'terms.id')
            ->where('plans.student', $studentId)
            ->pluck('terms.course');

        // Planned or passed both count as covered
        $coveredCourseIds = $plannedCourseIds->merge($this->passedCourseIds($studentId));

        return DB::table('course_prerequisites')
            ->join('courses', 'course_prerequisites.prereq', '=', 'courses.id')
            ->where('course_prerequisites.course', $courseId)
            ->whereNotIn('course_prerequisites.prereq', $coveredCourseIds)
            ->orderBy('courses.courseNumber')
            ->pluck('courses.courseNumber');
    }

    /**
     * Courses the student completed with a passing grade (2.0 or better).
     */
    private function passedCourseIds(int $studentId)
    {
        return DB::table('histories')
            ->where('student', $studentId)
            ->where('gpa', '>=', 2.0)
            ->pluck('course');
    }

    /**
     * Send the student back to the top of their plan page after an action.
     */
    private function toTop(): RedirectResponse
    {
        return redirect(route('student.dashboard').'#student-top');
    }

    /**
     * Enroll the logged-in student in a course.
     */
    public function enroll(Request $request, Classroom $classroom): RedirectResponse
    {
        // Get the course associated with this classroom.
        $course = $classroom->courseDetails;

        abort_if(! $course, 404);

        // Create enrollment if the student is not already enrolled.
        $enrollment = Enroll::firstOrCreate([
            'student' => $request->user()->id,
            'course' => $course->id,
        ]);

        // Prevent duplicate enrollment.
        if (! $enrollment->wasRecentlyCreated) {
            return redirect()
                ->route('class-details', ['classroom' => $classroom->id])
                ->with('error', 'You are already enrolled in this course.');
        }

        return redirect()
            ->route('class-details', ['classroom' => $classroom->id])
            ->with('success', 'Successfully enrolled in '.$course->courseNumber.'.');
    }
}