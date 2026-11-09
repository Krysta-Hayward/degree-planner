<?php

namespace App\Http\Controllers;

use App\Models\Course;

class PublicPrereqController extends Controller
{
    public function index()
    {
        $courses = Course::whereHas('prerequisites')
            ->withCount('prerequisites')
            ->orderBy('courseNumber')
            ->paginate(50);

        return view('public.prereqs', compact('courses'));
    }

    public function show(Course $course)
    {
        $course->load('prerequisites.prerequisiteDetails');

        return view('public.prereqs-details', compact('course'));
    }
}
