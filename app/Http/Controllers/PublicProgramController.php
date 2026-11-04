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
}