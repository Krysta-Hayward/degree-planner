<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Course;
use Illuminate\Http\Request;

class AdminClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classrooms = Classroom::with('courseDetails')
            ->orderBy('course')
            ->paginate(25);

        return view('admin.classrooms.classrooms', compact('classrooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $courses = Course::orderBy('courseNumber')->get();

        return view('admin.classrooms.classrooms-create', compact('courses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validateClassroom($request);

        Classroom::create($data);

        return redirect()
            ->route('admin.classrooms.index')
            ->with('success', 'Classroom created succesfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Classroom $classroom)
    {
        $courses = Course::orderBy('courseNumber')->get();

        return view('admin.classrooms.classrooms-edit', compact('classroom', 'courses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Classroom $classroom)
    {
        $data = $this->validateClassroom($request);

        $classroom->update($data);

        return redirect()
            ->route('admin.classrooms.index')
            ->with('success', 'Classroom updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classroom $classroom)
    {
        $classroom->delete();

        return redirect()
            ->route('admin.classrooms.index')
            ->with('success', 'Classroom deleted successfully.');
    }

    private function validateClassroom(Request $request): array
    {
        return $request->validate([
            'course' => ['required', 'integer', 'exists:courses,id'],
            'teacher' => ['nullable', 'string', 'max:255'],
            'seats' => ['required', 'integer', 'min:0'],
            'seatsOpen' => ['required', 'integer', 'min:0', 'lte:seats'],
            'fall' => ['required', 'boolean'],
            'winter' => ['required', 'boolean'],
            'spring' => ['required', 'boolean'],
            'summer' => ['required', 'boolean'],
            'timeStart' => ['nullable', 'date_format:H:i'],
            'timeEnd' => ['nullable', 'date_format:H:i', 'after:timeStart']
        ]);
    }
}
