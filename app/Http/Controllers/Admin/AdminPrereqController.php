<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course_Prerequisite;
use Illuminate\View\View;

class AdminPrereqController extends Controller
{
    public function index(): View
    {
        $prereqs = Course_Prerequisite::orderBy('prereqNumber')->get();

        return view('admin.prereqs', compact('prereqs'));
    }
}
