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
                'exists:user,id',
            ],
        ]);

        if ($validated['advisor_id'] !== null) {
            $advisor = User::findOrFail($validated['advisor_id']);

            if ($advisor->role !== 'advisor') {
                return back()->withErrors([
                    'advisor_id' => 'Please select a valid advisor.'
                ]);
            }
        }

        $student->advisor_id = $validated['advisor_id'];
        $student->save();

        return back()->with('status', 'Advisor assignement saved.');
    }
}
