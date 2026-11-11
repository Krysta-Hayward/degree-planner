<?php

namespace App\Services;

use App\Models\ProgramMap;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class NeedsAttention
{
    // Students assigned to this advisor who need attention, each with their reasons
    public static function forAdvisor(int $advisorId): Collection
    {
        $studentIds = DB::table('students')
            ->where('advisor', $advisorId)
            ->pluck('student');

        $students = User::whereIn('id', $studentIds)->get()->keyBy('id');

        // Each student's planned course IDs
        $plannedByStudent = DB::table('plans')
            ->join('terms', 'plans.class', '=', 'terms.id')
            ->whereIn('plans.student', $studentIds)
            ->get(['plans.student', 'terms.course'])
            ->groupBy('student')
            ->map(fn ($rows) => $rows->pluck('course'));

        // Students with a planned course whose prerequisite isn't on their plan
        $missingPrereq = DB::table('plans as checked_plan')
            ->join('terms as checked_term', 'checked_plan.class', '=', 'checked_term.id')
            ->join('course_prerequisites', 'course_prerequisites.course', '=', 'checked_term.course')
            ->whereIn('checked_plan.student', $studentIds)
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('plans as other_plan')
                    ->join('terms as other_term', 'other_plan.class', '=', 'other_term.id')
                    ->whereColumn('other_plan.student', 'checked_plan.student')
                    ->whereColumn('other_term.course', 'course_prerequisites.prereq');
            })
            ->distinct()
            ->pluck('checked_plan.student');

        // Required courses for each program
        $requiredByProgram = DB::table('programs')
            ->get(['name', 'course'])
            ->groupBy('name')
            ->map(fn ($rows) => $rows->pluck('course'));

        // Decide each student's reasons, in order
        $flagged = collect();

        foreach ($students as $id => $student) {
            $planned = $plannedByStudent[$id] ?? collect();

            // 1. No program: nothing else can be checked
            if (! $student->program) {
                $flagged[$id] = ['No program selected'];
                continue;
            }

            // 2. No courses yet: nothing else can be checked
            if ($planned->isEmpty()) {
                $flagged[$id] = ['No plan yet'];
                continue;
            }

            // 3. Problems with the plan itself
            $reasons = [];

            if ($missingPrereq->contains($id)) {
                $reasons[] = 'Missing a prerequisite';
            }

            $required = $requiredByProgram[$student->program] ?? collect();

            if ($required->diff($planned)->isNotEmpty()) {
                $reasons[] = 'Missing required courses';
            }

            if ($reasons) {
                $flagged[$id] = $reasons;
            }
        }

        // Each flagged student's program map ID, if they have one
        $mapIds = ProgramMap::whereIn('student', $flagged->keys())
            ->pluck('id', 'student');

        return User::whereIn('id', $flagged->keys())
            ->orderBy('name')
            ->get()
            ->map(function ($student) use ($flagged, $mapIds) {
                $student->reasons = $flagged[$student->id];
                $student->mapId = $mapIds[$student->id] ?? null;
                return $student;
            });
    }
}