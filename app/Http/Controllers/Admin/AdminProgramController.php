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

    private const SUGGESTED_GROUPS = [
        'Core',
        'Electives',
        'General Education',
    ];

    /**
     * Display all programs.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Program::class);

        $descriptions = ProgramDetail::pluck('description', 'name');

        $programs = Program::select('name')
            ->groupBy('name')
            ->orderBy('name')
            ->paginate(25)
            ->through(function ($program) use ($descriptions) {
                $program->course_count = Program::where('name', $program->name)->count();
                $program->description = $descriptions[$program->name] ?? null;

                return $program;
            });

        return view('admin.programs.programs', compact('programs'));
    }

    /**
     * Display a program and its required courses.
     */
    public function show(string $programName): View
    {
        $this->authorize('viewAny', Program::class);

        $courses = Program::with('courseDetails')
            ->where('name', $programName)
            ->get()
            ->sortBy(fn ($program) => $program->courseDetails?->courseNumber ?? '')
            ->values();

        abort_if($courses->isEmpty(), 404);

        $groups = $courses->groupBy('requirement_group')->sortKeys();

        $totalCredits = $courses->sum(
            fn ($program) => $program->courseDetails?->credits ?? 0
        );

        $description = ProgramDetail::where('name', $programName)
            ->value('description');

        return view('admin.programs.program-details', compact(
            'programName',
            'description',
            'courses',
            'groups',
            'totalCredits'
        ));
    }

    /**
     * Show the create form.
     */
    public function create(): View
    {
        $this->authorize('create', Program::class);

        return view('admin.programs.programs-create', [
            'courses' => $this->availableCourses(),
            'groupOptions' => $this->groupOptions(),
            'action' => route('admin.programs.store'),
            'method' => 'POST',
            'submitLabel' => 'Create Program',
            'current' => collect(),
        ]);
    }

    /**
     * Store a new program.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Program::class);

        $validated = $this->validateProgram($request);
        $selectedCourses = $this->selectedCourses($validated);

        DB::transaction(function () use ($validated, $selectedCourses) {
            ProgramDetail::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            foreach ($selectedCourses as $courseId => $group) {
                Program::create([
                    'name' => $validated['name'],
                    'course' => $courseId,
                    'requirement_group' => $group,
                ]);
            }
        });

        return redirect()
            ->route('admin.programs.details', [
                'programName' => $validated['name'],
            ])
            ->with('success', 'Program created successfully.');
    }

    /**
     * Show the edit form.
     */
    public function edit(string $programName): View
    {
        $this->authorize('update', Program::class);

        $current = Program::where('name', $programName)
            ->pluck('requirement_group', 'course');

        abort_if($current->isEmpty(), 404);

        return view('admin.programs.programs-edit', [
            'programName' => $programName,
            'description' => ProgramDetail::where('name', $programName)
                ->value('description'),
            'current' => $current,
            'courses' => $this->availableCourses(),
            'groupOptions' => $this->groupOptions(),
            'action' => route('admin.programs.update', [
                'programName' => $programName,
            ]),
            'method' => 'PUT',
            'submitLabel' => 'Update Program',
        ]);
    }

    /**
     * Update an existing program.
     */
    public function update(Request $request, string $programName): RedirectResponse
    {
        $this->authorize('update', Program::class);

        abort_unless(
            Program::where('name', $programName)->exists(),
            404
        );

        $validated = $this->validateProgram($request, $programName);
        $newName = $validated['name'];
        $selectedCourses = $this->selectedCourses($validated);

        DB::transaction(function () use ($programName, $newName, $validated, $selectedCourses) {
            $detail = ProgramDetail::firstOrNew([
                'name' => $programName,
            ]);

            $detail->name = $newName;
            $detail->description = $validated['description'] ?? null;
            $detail->save();

            if ($newName !== $programName) {
                Program::where('name', $programName)
                    ->update(['name' => $newName]);

                DB::table('users')
                    ->where('program', $programName)
                    ->update(['program' => $newName]);
            }

            Program::where('name', $newName)
                ->whereNotIn('course', array_keys($selectedCourses))
                ->delete();

            foreach ($selectedCourses as $courseId => $group) {
                Program::updateOrCreate(
                    [
                        'name' => $newName,
                        'course' => $courseId,
                    ],
                    [
                        'requirement_group' => $group,
                    ]
                );
            }
        });

        return redirect()
            ->route('admin.programs.details', [
                'programName' => $newName,
            ])
            ->with('success', 'Program updated successfully.');
    }

    /**
     * Validate program information.
     */
    private function validateProgram(
        Request $request,
        ?string $currentName = null
    ): array {
        $nameRules = [
            'required',
            'string',
            'max:255',
            Rule::unique('program_details', 'name')->ignore($currentName, 'name'),
            Rule::unique('programs', 'name')->ignore($currentName, 'name'),
        ];

        return $request->validate([
            'name' => $nameRules,
            'description' => ['nullable', 'string', 'max:2000'],
            'courses' => ['required', 'array', 'min:1'],
            'courses.*' => ['required', 'integer', 'distinct', 'exists:courses,id'],
            'groups' => ['nullable', 'array'],
            'groups.*' => ['nullable', 'string', 'max:100'],
        ], [
            'courses.required' => 'Choose at least one required course.',
            'courses.min' => 'Choose at least one required course.',
            'name.unique' => 'A program with that name already exists.',
        ]);
    }

    /**
     * Get selected course IDs and their requirement groups.
     */
    private function selectedCourses(array $validated): array
    {
        $selected = [];

        foreach ($validated['courses'] as $courseId) {
            $group = trim($validated['groups'][$courseId] ?? '');

            $selected[(int) $courseId] = $group ?: 'Core';
        }

        return $selected;
    }

    /**
     * Get available courses.
     */
    private function availableCourses()
    {
        return Course::orderBy('courseNumber')->get();
    }

    /**
     * Get suggested and previously used requirement groups.
     */
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
