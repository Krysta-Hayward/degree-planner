<?php

namespace App\Http\Controllers;

use App\Models\Course;

class PublicCourseController extends Controller
{
    public function index()
    {
        $courses = Course::orderBy('courseNumber')
            ->paginate(15);

        return view('public.courses', compact('courses'));
    }

    public function show(Course $course)
    {
        $course->load('prerequisiteCourses');

        return view('public.course-details', compact('course'));
    }
}
