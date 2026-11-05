<?php

namespace App\Http\Controllers;

use App\Models\Course_Prerequisite;

class PublicPrereqController extends Controller
{

    public function index()
    {
        $prereqs = Course_Prerequisite::with([
            'courseDetails',
            'prerequisiteDetails'
        ])->paginate(25);

        return view('public.prereqs', compact('prereqs'));
    }

    public function show(Course_Prerequisite $prereq)
    {
        $prereqs = Course_Prerequisite::with([
            'courseDetails',
            'prerequisiteDetails'
        ])->where('course', $prereq->course)->get();

        return view('public.prereqs-details', compact('prereqs'));
    }
}
