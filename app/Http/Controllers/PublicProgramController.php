<?php

namespace App\Http\Controllers;

use App\Models\Program;

class PublicProgramController extends Controller
{
    public function index()
    {
        $programs = Program::with('courseDetails')
            ->orderBy('name')
            ->paginate(10);

        return view('programs.index', compact('programs'));
    }
}