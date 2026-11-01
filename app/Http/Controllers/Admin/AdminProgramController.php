<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Program;
use App\Models\ProgramDetail;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminProgramController extends Controller
{
    use AuthorizesRequests;

    // Requirement groups offered in the form. Admins can also type their own.
    private const SUGGESTED_GROUPS = ['Core', 'Electives', 'General Education'];

    // Display all programs
    public function index(): View
    {
        $this->authorize('viewAny', Program::class);

        $descriptions = ProgramDetail::pluck('description', 'name');

        // Get each program name only once. groupBy (not distinct) so the
        // pagination total counts programs instead of course rows.
        $programs = Program::select('name')
            ->groupBy('name')
            ->orderBy('name')
            ->paginate(25)
            ->through(function ($program) use ($descriptions) {
                $program->course_count = Program::where('name', $program->name)->count();
                $program->description = $descriptions[$program->name] ?? null;

                return $program;
            });

        return view('admin.programs', compact('programs'));
    }

    // Show one program and its required courses, grouped by requirement group
    public function show(string $programName): View
    {
        $this->authorize('viewAny', Program::class);

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

        $groups = $courses->groupBy('requirement_group')->sortKeys();

        $description = ProgramDetail::where('name', $programName)->value('description');

        return view('admin.program-details', compact(
            'programName',
            'description',
            'courses',
            'groups',
            'totalCredits'
        ));
    }

    // Show the create program form
    public function create(): View
    {
        $this->authorize('create', Program::class);

        return view('admin.programs-create', [
            'courses' => Course::orderBy('courseNumber')->get(),
            'groupOptions' => $this->groupOptions(),
        ]);
    }

    // Save a new program and its required courses
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Program::class);

        $validated = $this->validateProgram($request);

        DB::transaction(function () use ($validated) {
            ProgramDetail::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            foreach ($this->selectedCourses($validated) as $courseId => $group) {
                Program::create([
                    'name' => $validated['name'],
                    'course' => $courseId,
                    'requirement_group' => $group,
                ]);
            }
        });

        return redirect()
            ->route('admin.programs.show', ['programName' => $validated['name']])
            ->with('success', 'Program created successfully.');
    }

    // Show the edit program form
    public function edit(string $programName): View
    {
        $this->authorize('update', Program::class);

        $current = Program::where('name', $programName)->pluck('requirement_group', 'course');

        abort_if($current->isEmpty(), 404);

        return view('admin.programs-edit', [
            'programName' => $programName,
            'description' => ProgramDetail::where('name', $programName)->value('description'),
            'current' => $current,
            'courses' => Course::orderBy('courseNumber')->get(),
            'groupOptions' => $this->groupOptions(),
        ]);
    }

    // Save changes to a program and its required courses
    public function update(Request $request, string $programName): RedirectResponse
    {
        $this->authorize('update', Program::class);

        abort_unless(Program::where('name', $programName)->exists(), 404);

        $validated = $this->validateProgram($request, $programName);
        $newName = $validated['name'];
        $selected = $this->selectedCourses($validated);

        DB::transaction(function () use ($programName, $newName, $validated, $selected) {
            // Programs created before descriptions existed may not have a details row yet.
            $detail = ProgramDetail::firstOrNew(['name' => $programName]);
            $detail->name = $newName;
            $detail->description = $validated['description'] ?? null;
            $detail->save();

            if ($newName !== $programName) {
                Program::where('name', $programName)->update(['name' => $newName]);

                // Students who chose this program keep it under its new name.
                DB::table('users')->where('program', $programName)->update(['program' => $newName]);
            }

            // Courses that were unchecked are no longer required.
            Program::where('name', $newName)
                ->whereNotIn('course', array_keys($selected))
                ->delete();

            foreach ($selected as $courseId => $group) {
                Program::updateOrCreate(
                    ['name' => $newName, 'course' => $courseId],
                    ['requirement_group' => $group],
                );
            }
        });

        return redirect()
            ->route('admin.programs.show', ['programName' => $newName])
            ->with('success', 'Program updated successfully.');
    }

    // Validation shared by create and update
    private function validateProgram(Request $request, ?string $currentName = null): array
    {
        $nameRules = ['required', 'string', 'max:255'];

        // The name has to be free in both tables, except for the program being edited.
        $nameRules[] = Rule::unique('program_details', 'name')->ignore($currentName, 'name');
        $nameRules[] = Rule::unique('programs', 'name')->ignore($currentName, 'name');

        return $request->validate([
            'name' => $nameRules,
            'description' => ['nullable', 'string', 'max:2000'],
            'courses' => ['required', 'array', 'min:1'],
            'courses.*' => ['integer', 'distinct', 'exists:courses,id'],
            'groups' => ['nullable', 'array'],
            'groups.*' => ['nullable', 'string', 'max:100'],
        ], [
            'courses.required' => 'Choose at least one required course.',
            'name.unique' => 'A program with that name already exists.',
        ]);
    }

    // Selected course ids mapped to their requirement group (blank means Core)
    private function selectedCourses(array $validated): array
    {
        $selected = [];

        foreach ($validated['courses'] as $courseId) {
            $group = trim($validated['groups'][$courseId] ?? '');
            $selected[(int) $courseId] = $group === '' ? 'Core' : $group;
        }

        return $selected;
    }

    // Suggested groups plus any groups already in use
    private function groupOptions(): array
    {
        return collect(self::SUGGESTED_GROUPS)
            ->merge(Program::distinct()->pluck('requirement_group'))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
