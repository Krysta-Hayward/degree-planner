<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Term;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminTermController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', Term::class);

        $terms = Term::orderBy('termNumber')->paginate(25);

        return view('admin.terms.terms', compact('terms'));
    }

    public function create(): View
    {
        $this->authorize('create', Term::class);

        $courses = Course::orderBy('courseNumber')->get();

        return view('admin.terms.terms-create', compact('courses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Term::class);

        $validated = $request->validate(
            [
                'quarterYear' => ['required', 'string', 'regex:/^(Winter|Spring|Summer|Fall) [0-9]{4}$/'],
                'course' => ['required', 'integer', 'exists:courses,id'],
            ],
            [
                'quarterYear.regex' => 'Term must be in the format: Season YYYY (e.g., Winter 2026).',
            ]
        );

        Term::create($validated);

        return redirect()
            ->route('admin.terms.index')
            ->with('success', 'Term created successfully.');
    }

    public function edit(Term $term): View
    {
        $this->authorize('update', $term);

        $courses = Course::orderBy('courseNumber')->get();

        return view('admin.terms.terms-edit', compact('term', 'courses'));
    }

    public function update(Request $request, Term $term): RedirectResponse
    {
        $this->authorize('update', $term);

        $validated = $request->validate(
            [
                'quarterYear' => ['required', 'string', 'regex:/^(Winter|Spring|Summer|Fall) [0-9]{4}$/'],
                'course' => ['required', 'integer', 'exists:courses,id'],
            ],
            [
                'quarterYear.regex' => 'Term must be in the format: Season YYYY (e.g., Winter 2026).',
            ]
        );

        $term->update($validated);

        return redirect()
            ->route('admin.terms.index')
            ->with('success', 'Term updated successfully.');
    }

    public function destroy(Term $term): RedirectResponse
    {
        $this->authorize('delete', $term);

        $term->delete();

        return redirect()
            ->route('admin.terms.index')
            ->with('success', 'Term deleted successfully.');
    }
}
