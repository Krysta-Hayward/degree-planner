<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Course;

class ClassesController extends Controller
{
    public function index()
    {
        $classes = Classroom::with('courseDetails')
            ->whereHas('courseDetails')
            ->orderBy(
                Course::select('courseNumber')
                    ->whereColumn('courses.id', 'classrooms.course')
            )
            ->paginate(15);

        return view('classes', compact('classes'));
    }

    public function show(Classroom $classroom)
    {
        $classroom->load('courseDetails');

        return view('class-details', compact('classroom'));
    }
}
