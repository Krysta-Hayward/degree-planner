<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Term;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminCourseController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', Course::class);

        $courses = Course::with('terms')
            ->orderBy('courseNumber')
            ->paginate(25);

        return view('admin.courses.courses', compact('courses'));
    }

    public function create(): View
    {
        $this->authorize('create', Course::class);

        return view('admin.courses.courses-create', [
            'availableQuarterYears' => $this->availableQuarterYears(),
            'selectedQuarterYears' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Course::class);

        $validated = $request->validate(
            [
                'courseNumber' => [
                    'required',
                    'string',
                    'regex:/^(?:[A-Z]{3} [0-9]{3}|[A-Z]{4}&[0-9]{3})$/',
                    'unique:courses,courseNumber',
                ],
                'name' => ['required', 'string', 'max:255'],
                'credits' => ['required', 'numeric', 'min:0', 'max:5'],
                'description' => ['nullable', 'string', 'max:2000'],
                'quarterYears' => ['nullable', 'array'],
                'quarterYears.*' => [
                    'string',
                    Rule::in($this->availableQuarterYears()),
                ],
            ],
            [
                'courseNumber.regex' => 'Course number must be in the format AAA 123 or AAA&123 (e.g., CIS 272 or CMST&210).',
            ]
        );

        $selectedTerms = $validated['quarterYears'] ?? [];

        unset($validated['quarterYears']);

        DB::transaction(function () use ($validated, $selectedTerms) {
            $course = Course::create($validated);

            foreach ($selectedTerms as $quarterYear) {
                $term = new Term;
                $term->course = $course->id;
                $term->quarterYear = $quarterYear;
                $term->save();
            }
        });

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course created successfully.');
    }

    public function edit(Course $course): View
    {
        $this->authorize('update', $course);

        return view('admin.courses.courses-edit', [
            'course' => $course,
            'availableQuarterYears' => $this->availableQuarterYears(),
            'selectedQuarterYears' => Term::where('course', $course->id)
                ->pluck('quarterYear')
                ->unique()
                ->values()
                ->all(),
        ]);
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('update', $course);

        $validated = $request->validate(
            [
                'courseNumber' => [
                    'required',
                    'string',
                    'regex:/^(?:[A-Z]{3} [0-9]{3}|[A-Z]{4}&[0-9]{3})$/',
                    'unique:courses,courseNumber,'.$course->id,
                ],
                'name' => ['required', 'string', 'max:255'],
                'credits' => ['required', 'numeric', 'min:0', 'max:5'],
                'description' => ['nullable', 'string', 'max:2000'],
                'quarterYears' => ['nullable', 'array'],
                'quarterYears.*' => [
                    'string',
                    Rule::in($this->availableQuarterYears()),
                ],
            ],
            [
                'courseNumber.regex' => 'Course number must be in the format AAA 123 or AAA&123 (e.g., CIS 272 or CMST&210).',
            ]
        );

        $selectedTerms = array_unique($validated['quarterYears'] ?? []);

        unset($validated['quarterYears']);

        $preservedTerms = [];

        DB::transaction(function () use ($course, $validated, $selectedTerms, &$preservedTerms) {
            $course->update($validated);

            $existingTerms = Term::where('course', $course->id)->get();

            // Create newly selected terms that do not already exist.
            foreach ($selectedTerms as $quarterYear) {
                if (! $existingTerms->contains('quarterYear', $quarterYear)) {
                    $term = new Term;
                    $term->course = $course->id;
                    $term->quarterYear = $quarterYear;
                    $term->save();
                }
            }

            // Remove terms that were unchecked, unless students have plans using them.
            foreach ($existingTerms as $term) {
                if (in_array($term->quarterYear, $selectedTerms)) {
                    continue;
                }

                $isUsed = DB::table('plans')
                    ->where('class', $term->id)
                    ->exists();

                if ($isUsed) {
                    $preservedTerms[] = $term->quarterYear;
                } else {
                    $term->delete();
                }
            }
        });

        $redirect = redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course updated successfully.');

        if (! empty($preservedTerms)) {
            $redirect->with(
                'error',
                'Some terms could not be removed because students have them in their degree plans: '
                .implode(', ', array_unique($preservedTerms))
            );
        }

        return $redirect;
    }

    public function destroy(Course $course): RedirectResponse
    {
        $this->authorize('delete', $course);

        // Plans store a term, so check every term of this course
        $isPlanned = DB::table('plans')
            ->whereIn('class', DB::table('terms')->where('course', $course->id)->pluck('id'))
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

    private function availableQuarterYears(): array
    {
        $seasons = ['Winter', 'Spring', 'Summer', 'Fall'];
        $years = range((int) date('Y'), (int) date('Y') + 4);

        $options = [];

        foreach ($years as $year) {
            foreach ($seasons as $season) {
                $options[] = "$season $year";
            }
        }

        return $options;
    }
}
