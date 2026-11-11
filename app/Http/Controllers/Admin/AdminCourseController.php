<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminCourseController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', Course::class);

        $courses = Course::orderBy('courseNumber')->paginate(25);

        return view('admin.courses.courses', compact('courses'));
    }

    public function create(): View
    {
        $this->authorize('create', Course::class);

        return view('admin.courses.courses-create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Course::class);

        $validated = $request->validate(
            [
                'courseNumber' => [
                    'required',
                    'string',
                    'regex:/^[A-Z]{3} [0-9]{3}$/',
                    'unique:courses,courseNumber',
                ],
                'name' => ['required', 'string', 'max:255'],
                'credits' => ['required', 'numeric', 'min:0', 'max:5'],
            ],
            [
                'courseNumber.regex' => 'Course number must be in the format AAA 123 (e.g., CIS 272).',
            ]
        );

        Course::create($validated);

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course created successfully.');
    }

    public function edit(Course $course): View
    {
        $this->authorize('update', $course);

        return view('admin.courses.courses-edit', compact('course'));
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('update', $course);

        $validated = $request->validate(
            [
                'courseNumber' => [
                    'required',
                    'string',
                    'regex:/^[A-Z]{3} [0-9]{3}$/',
                    'unique:courses,courseNumber,'.$course->id,
                ],
                'name' => ['required', 'string', 'max:255'],
                'credits' => ['required', 'numeric', 'min:0', 'max:5'],
            ],
            [
                'courseNumber.regex' => 'Course number must be in the format AAA 123 (e.g., CIS 272).',
            ]
        );

        $course->update($validated);

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $this->authorize('delete', $course);

        $isPlanned = DB::table('plans')
            ->where('class', $course->id)
            ->exists();

        $isEnrolled = DB::table('enrolls')
            ->where('course', $course->id)
            ->exists();

        if ($isPlanned || $isEnrolled) {
            return redirect()
                ->route('admin.courses.index')
                ->with('error', 'This course cannot be deleted because it is being used in student plans or enrollments.');
        }

        $course->delete();

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course deleted successfully.');
    }
}
