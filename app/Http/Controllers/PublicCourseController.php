<?php

namespace App\Http\Controllers;

use App\Models\Course;

class PublicCourseController extends Controller
{
    public function index()
    {
        $courses = Course::orderBy('courseNumber')
            ->paginate(15);

        return view('courses.index', compact('courses'));
    }
}