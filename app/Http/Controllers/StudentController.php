<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Enroll;
use App\Models\Plan;
use App\Models\Program;
use App\Models\ProgramMap;
use App\Models\Term;
use App\Models\Course;
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

        // Get this student's enrolled courses.
        $enrolled = Enroll::with('term')
            ->where('student', $user->id)
            ->orderBy('id')
            ->get();

        // Attach available terms and classroom schedules to each enrolled course.
        foreach ($enrolled as $course) {
            $course->setAttribute(
                'availableTerms',
                $terms->get($course->id, collect())
            );

            $course->setAttribute(
                'classroomSchedules',
                $sections->get($course->id, collect())
            );
        }

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
            'enrolled' => $enrolled,
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

        // every prerequisite has to be on the student's program map first
        $courseId = Term::query()->whereKey($data['term'])->value('course');
        $missing = $this->missingPrerequisites($user->id, $courseId);

        if ($missing->isNotEmpty()) {
            return $this->toTop()
                ->withErrors(['term' => 'Add '.$missing->join(', ').' to your program map first.']);
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
        // a student can never change another student's program map
        abort_if((int) $plan->student !== $request->user()->id, 403);

        // a class that is still on the map may need this one as a prerequisite
        $courseId = Term::query()->whereKey($plan->class)->value('course');
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

        $plan->update(['class' => $data['term']]);

        return $this->toTop()->with('status', 'Course moved.');
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

        return DB::table('course_prerequisites')
            ->join('courses', 'course_prerequisites.prereq', '=', 'courses.id')
            ->where('course_prerequisites.course', $courseId)
            ->whereNotIn('course_prerequisites.prereq', $plannedCourseIds)
            ->orderBy('courses.courseNumber')
            ->pluck('courses.courseNumber');
    }

    /**
     * Send the student back to the top of their plan page after an action.
     */
    private function toTop(): RedirectResponse
    {
        return redirect(route('student.dashboard').'#student-top');
    }


    /**
     * Enroll the logged-in student in a course for a selected term.
     */
    public function enroll(Request $request, Classroom $classroom): RedirectResponse
    {
        $course = $classroom->courseDetails;

        abort_if(!$course, 404);

        // Validate the selected term.
        $data = $request->validate([
            'term' => ['required', 'integer', 'exists:terms,id'],
        ]);

        // Make sure the selected term belongs to this classroom's course.
        $term = Term::where('id', $data['term'])
            ->where('course', $course->id)
            ->first();

        if (!$term) {
            return redirect()->back()->with(
                'error',
                'The selected term is not available for this course.'
            );
        }

        // Prevent duplicate enrollment in the same course.
        $existingEnrollment = Enroll::where('student', $request->user()->id)
            ->where('course', $course->id)
            ->first();

        if ($existingEnrollment) {
            return redirect()
                ->route('class-details', ['classroom' => $classroom->id])
                ->with('error', 'You are already enrolled in this course.');
        }

        // Check for overlapping classroom schedules.
        $enrolledCourses = Enroll::where('student', $request->user()->id)
            ->pluck('course');

        $existingClassrooms = Classroom::whereIn('course', $enrolledCourses)
            ->get();

        foreach ($existingClassrooms as $existingClass) {
            if (
                $classroom->timeStart &&
                $classroom->timeEnd &&
                $existingClass->timeStart &&
                $existingClass->timeEnd &&
                $classroom->timeStart < $existingClass->timeEnd &&
                $classroom->timeEnd > $existingClass->timeStart
            ) {
                return redirect()->back()->with(
                    'error',
                    'You cannot enroll in this class because it overlaps with another class in your schedule.'
                );
            }
        }

        // Save the course and selected term.
        Enroll::create([
            'student' => $request->user()->id,
            'course' => $course->id,
            'term_id' => $term->id,
        ]);

        // Automatically update the student's program map.
        $studentId = $request->user()->id;

        $programMap = ProgramMap::where('student', $studentId)->first();

        // Only update the program map if it is still editable.
        if (!$programMap || $programMap->isEditable()) {

            // Find this course's existing program map entry, if one exists.
            $existingPlan = Plan::where('student', $studentId)
                ->whereIn(
                    'class',
                    Term::where('course', $course->id)->select('id')
                )
                ->first();

            if ($existingPlan) {
                // Move the existing course to the selected term.
                $existingPlan->update([
                    'class' => $term->id,
                ]);
            } else {
                // Add the course to the program map.
                Plan::create([
                    'student' => $studentId,
                    'class' => $term->id,
                ]);
            }
        }


        return redirect()
            ->route('class-details', ['classroom' => $classroom->id])
            ->with('success', 'Successfully enrolled in ' . $course->courseNumber . ' for ' . $term->quarterYear . '.');
    }

}
