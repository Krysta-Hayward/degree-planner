<?php

namespace App\Http\Controllers;

use App\Models\Program;

class PublicProgramController extends Controller
{
    public function index()
    {
        $programGroups = Program::with('courseDetails')
            ->orderBy('name')
            ->get()
            ->groupBy('name');

        return view('public.programs', compact('programGroups'));
    }

    public function show(Program $program)
    {
        $programRows = Program::with('courseDetails')
            ->where('name', $program->name)
            ->get();

        return view('public.program-details', compact('program', 'programRows'));
    }
}