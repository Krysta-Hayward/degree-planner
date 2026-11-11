<?php

namespace App\Services;

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

        // No program selected
        $noProgram = User::whereIn('id', $studentIds)
            ->whereNull('program')
            ->pluck('id');

        // No courses planned
        $hasPlans = DB::table('plans')
            ->whereIn('student', $studentIds)
            ->distinct()
            ->pluck('student');

        $noPlan = $studentIds->diff($hasPlans);

        // A planned course's prerequisite isn't on the plan
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

        // Combine: student ID → list of reasons
        $flagged = collect();

        foreach ($noProgram as $id) {
            $flagged[$id] = [...($flagged[$id] ?? []), 'No program selected'];
        }

        foreach ($noPlan as $id) {
            $flagged[$id] = [...($flagged[$id] ?? []), 'No courses planned'];
        }

        foreach ($missingPrereq as $id) {
            $flagged[$id] = [...($flagged[$id] ?? []), 'Missing a prerequisite'];
        }

        return User::whereIn('id', $flagged->keys())
            ->orderBy('name')
            ->get()
            ->map(function ($student) use ($flagged) {
                $student->reasons = $flagged[$student->id];
                return $student;
            });
    }
}