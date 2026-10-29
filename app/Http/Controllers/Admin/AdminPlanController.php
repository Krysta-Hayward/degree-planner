<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class AdminPlanController extends Controller
{
    public function index(): View
    {
        $students = User::where('role', 'student')
            ->with('plans.course')
            ->orderBy('name')
            ->get()
            ->filter(fn ($student) => $student->plans->isNotEmpty())
            ->values()
            ->map(function ($student) {
                $student->planned_courses = $student->plans->count();

                $student->planned_credits = $student->plans
                    ->sum(fn ($plan) => $plan->course?->credits ?? 0);

                return $student;
            });

        return view('admin.plans', compact('students'));
    }

    public function show(User $student): View
    {
        abort_unless($student->role === 'student', 404);

        $student->load('plans.course');

        $plans = $student->plans
            ->sortBy(fn ($plan) => $plan->course?->courseNumber ?? '')
            ->values();

        $totalCredits = $plans->sum(
            fn ($plan) => $plan->course?->credits ?? 0
        );

        return view('admin.plan-details', compact(
            'student',
            'plans',
            'totalCredits'
        ));
    }
}
