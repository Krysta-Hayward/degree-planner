<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\View\View;

class AdminCourseController extends Controller
{
    public function index(): View
    {
        $courses = Course::orderBy('courseNumber')->get();

        return view('admin.courses', compact('courses'));
    }
}
