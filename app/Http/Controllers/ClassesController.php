<?php

namespace App\Http\Controllers;

use App\Models\Course;

class ClassesController extends Controller
{
    public function index()
    {
        $classes = Course::orderBy('courseNumber')
            ->paginate(15);

        return view('classes', compact('classes'));
    }

    public function show(Course $course)
    {
        return view('class-details', compact('course'));
    }
    
}