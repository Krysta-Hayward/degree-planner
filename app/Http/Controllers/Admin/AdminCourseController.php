<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCourseController extends Controller
{
    // Display all courses
    public function index(): View
    {
        $this->authorize('viewAny', Course::class);

        $courses = Course::orderBy('courseNumber')->get();

        return view('admin.courses', compact('courses'));
    }

    // Show the create course form
    public function create(): View
    {
        $this->authorize('create', Course::class);

        return view('admin.courses-create');
    }

    // Save a new course
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Course::class);

        $validated = $request->validate([
            'courseNumber' => [
                'required',
                'string',
                'max:255',
                'unique:courses,courseNumber',
            ],
            'name' => ['required', 'string', 'max:255'],
            'credits' => ['required', 'numeric', 'min:0'],
        ]);

        Course::create($validated);

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course created successfully.');
    }

    // Show the edit course form
    public function edit(Course $course): View
    {
        $this->authorize('update', $course);

        return view('admin.courses-edit', compact('course'));
    }

    // Save changes to a course
    public function update(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('update', $course);

        $validated = $request->validate([
            'courseNumber' => [
                'required',
                'string',
                'max:255',
                'unique:courses,courseNumber,' . $course->id,
            ],
            'name' => ['required', 'string', 'max:255'],
            'credits' => ['required', 'numeric', 'min:0'],
        ]);

        $course->update($validated);

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course updated successfully.');
    }

    // Delete a course
    public function destroy(Course $course): RedirectResponse
    {
        $this->authorize('delete', $course);

        $course->delete();

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course deleted successfully.');
    }
}
