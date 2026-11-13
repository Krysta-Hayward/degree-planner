<?php

namespace App\Http\Controllers;

use App\Models\Classroom;

class ClassesController extends Controller
{
    public function index()
    {
        $classes = Classroom::with('course')
            ->join('courses', 'classrooms.course', '=', 'courses.id')
            ->select('classrooms.*')
            ->orderBy('courses.courseNumber')
            ->paginate(15);

        return view('classes', compact('classes'));
    }

    public function show(Classroom $classroom)
    {
        $classroom->load('course');
        return view('class-details', compact('classroom'));
    }
    
}