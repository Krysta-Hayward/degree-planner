<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminStudentController extends Controller
{
    public function index(): View
    {
        $students = User::where('role', 'student')
            ->with('advisor')
            ->orderBy('name')
            ->get();

        $advisors = User::where('role', 'advisor')
            ->orderBy('name')
            ->get();

        return view('admin.students', compact('students', 'advisors'));
    }

    public function updateAdvisor(Request $request, User $student): RedirectResponse
    {
        abort_unless($student->role === 'student', 404);

        $validated = $request->validate([
            'advisor_id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],
        ]);

        $advisorId = $validated['advisor_id'] ?? null;

        if ($advisorId !== null) {
            $advisor = User::findOrFail($advisorId);

            if ($advisor->role !== 'advisor') {
                return back()->withErrors([
                    'advisor_id' => 'The selected user is not an advisor.',
                ]);
            }
        }

        // Remove the existing advisor assignment.
        \App\Models\Student::where('student', $student->id)->delete();

        // Create the new assignment if an advisor was selected.
        if ($advisorId !== null) {
            \App\Models\Student::create([
                'advisor' => $advisorId,
                'student' => $student->id,
            ]);
        }

        return back()->with('status', 'Advisor assignement saved.');
    }
}
