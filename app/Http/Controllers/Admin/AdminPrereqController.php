<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Course_Prerequisite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            ->paginate(50);

        return view('admin.prereqs.prereqs', compact('prereqs'));
    }

    public function create(): View
    {
        $courses = Course::orderBy('courseNumber')->get();
        $selectedPrereqs = [];

        return view('admin.prereqs.prereqs-create', compact(
            'courses',
            'selectedPrereqs'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course' => [
                'required',
                'integer',
                'exists:courses,id',
            ],
            'prereqs' => [
                'required',
                'array',
                'min:1',
            ],
            'prereqs.*' => [
                'required',
                'integer',
                'exists:courses,id',
                'distinct',
                Rule::notIn([$request->input('course')]),
            ],
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['prereqs'] as $prereq) {
                Course_Prerequisite::create([
                    'course' => $validated['course'],
                    'prereq' => $prereq,
                ]);
            }
        });

        return redirect()
            ->route('admin.prereqs.index')
            ->with('success', 'Prerequisites created successfully.');
    }

    public function edit(Course_Prerequisite $prereq): View
    {
        $courses = Course::orderBy('courseNumber')->get();

        $selectedPrereqs = Course_Prerequisite::where(
            'course',
            $prereq->course
        )
            ->pluck('prereq')
            ->map(fn ($id) => (string) $id)
            ->toArray();

        return view('admin.prereqs.prereqs-edit', compact(
            'prereq',
            'courses',
            'selectedPrereqs'
        ));
    }

    public function update(
        Request $request,
        Course_Prerequisite $prereq
    ): RedirectResponse {
        $validated = $request->validate([
            'prereqs' => [
                'required',
                'array',
                'min:1',
            ],
            'prereqs.*' => [
                'required',
                'integer',
                'exists:courses,id',
                'distinct',
                Rule::notIn([$prereq->course]),
            ],
        ]);

        DB::transaction(function () use ($validated, $prereq) {
            // Replace the prerequisite list for this course.
            Course_Prerequisite::where('course', $prereq->course)->delete();

            foreach ($validated['prereqs'] as $prerequisiteId) {
                Course_Prerequisite::create([
                    'course' => $prereq->course,
                    'prereq' => $prerequisiteId,
                ]);
            }
        });

        return redirect()
            ->route('admin.prereqs.index')
            ->with('success', 'Prerequisites updated successfully.');
    }

    public function destroy(Course_Prerequisite $prereq): RedirectResponse
    {
        $prereq->delete();

        return redirect()
            ->route('admin.prereqs.index')
            ->with('success', 'Prerequisite deleted successfully.');
    }
}
