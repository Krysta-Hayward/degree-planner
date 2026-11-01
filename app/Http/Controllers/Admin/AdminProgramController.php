<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\View\View;

class AdminProgramController extends Controller
{
    public function index(): View
    {
        // Get each program name only once.
        $programs = Program::select('name')
            ->distinct()
            ->orderBy('name')
            ->paginate(25)
            ->map(function ($program) {
                $program->course_count = Program::where('name', $program->name)
                    ->count();

                return $program;
            });

        return view('admin.programs', compact('programs'));
    }

    public function show(string $programName): View
    {
        // Get every course associated with this program.
        $courses = Program::with('courseDetails')
            ->where('name', $programName)
            ->get()
            ->sortBy(fn ($program) => $program->courseDetails?->courseNumber ?? '')
            ->values();

        abort_if($courses->isEmpty(), 404);

        $totalCredits = $courses->sum(
            fn ($program) => $program->courseDetails?->credits ?? 0
        );

        return view('admin.program-details', compact(
            'programName',
            'courses',
            'totalCredits'
        ));
    }
}
