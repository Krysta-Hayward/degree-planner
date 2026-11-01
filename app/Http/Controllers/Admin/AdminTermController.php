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

    // Display all terms for admin management.
    public function index(): View
    {
        $this->authorize('viewAny', Term::class);

        $terms = Term::orderBy('termNumber')->paginate(25);

        return view('admin.terms', compact('terms'));
    }

    // Display the create form.
    public function create(): View
    {
        $this->authorize('create', Term::class);

        $courses = Course::orderBy('courseNumber')->get();

        return view('admin.terms-create', compact('courses'));
    }

    // Save a new term.
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Term::class);

        $validated = $request->validate([
            'quarterYear' => ['required', 'string', 'max:255'],
            'course' => ['required', 'integer', 'exists:courses,id'],
        ]);

        Term::create($validated);

        return redirect()
            ->route('admin.terms.index')
            ->with('success', 'Term created successfully.');
    }

    // Display the edit form.
    public function edit(Term $term): View
    {
        $this->authorize('update', $term);

        $courses = Course::orderBy('courseNumber')->get();

        return view('admin.terms-edit', compact('term', 'courses'));
    }

    // Save changes to an existing term.
    public function update(Request $request, Term $term): RedirectResponse
    {
        $this->authorize('update', $term);

        $validated = $request->validate([
            'quarterYear' => ['required', 'string', 'max:255'],
            'course' => ['required', 'integer', 'exists:courses,id'],
        ]);

        $term->update($validated);

        return redirect()
            ->route('admin.terms.index')
            ->with('success', 'Term updated successfully.');
    }

    // Delete a term.
    public function destroy(Term $term): RedirectResponse
    {
        $this->authorize('delete', $term);

        $term->delete();

        return redirect()
            ->route('admin.terms.index')
            ->with('success', 'Term deleted successfully.');
    }
}
