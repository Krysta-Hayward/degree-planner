<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Course_Prerequisite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminPrereqController extends Controller
{
    public function index(): View
    {
        $prereqs = Course_Prerequisite::with([
            'courseDetails',
            'prerequisiteDetails',
        ])
            ->orderBy('course')
            ->paginate(25);

        return view('admin.prereqs', compact('prereqs'));
    }

    public function create(): View
    {
        $courses = Course::orderBy('courseNumber')->get();

        return view('admin.prereqs-create', compact('courses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate($this->validationRules($request));

        Course_Prerequisite::create([
            'course' => $request->course,
            'prereq' => $request->prereq,
        ]);

        return redirect()
            ->route('admin.prereqs.index')
            ->with('success', 'Prerequisite created successfully.');
    }

    public function edit(Course_Prerequisite $prereq): View
    {
        $courses = Course::orderBy('courseNumber')->get();

        return view('admin.prereqs-edit', compact('prereq', 'courses'));
    }

    public function update(
        Request $request,
        Course_Prerequisite $prereq
    ): RedirectResponse {
        $request->validate($this->validationRules($request, $prereq));

        $prereq->update([
            'course' => $request->course,
            'prereq' => $request->prereq,
        ]);

        return redirect()
            ->route('admin.prereqs.index')
            ->with('success', 'Prerequisite updated successfully.');
    }

    public function destroy(Course_Prerequisite $prereq): RedirectResponse
    {
        $prereq->delete();

        return redirect()
            ->route('admin.prereqs.index')
            ->with('success', 'Prerequisite deleted successfully.');
    }

    private function validationRules(
        Request $request,
        ?Course_Prerequisite $prereq = null
    ): array {
        $uniqueRule = Rule::unique('course_prerequisites', 'course')
            ->where('prereq', $request->input('prereq'));

        if ($prereq) {
            $uniqueRule->ignore($prereq->id);
        }

        return [
            'course' => [
                'required',
                'integer',
                'exists:courses,id',
                'different:prereq',
                $uniqueRule,
            ],
            'prereq' => [
                'required',
                'integer',
                'exists:courses,id',
            ],
        ];
    }
}