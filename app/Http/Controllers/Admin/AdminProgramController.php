<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\View\View;

class AdminProgramController extends Controller
{
    public function index(): View
    {
        $programs = Program::orderBy('programNumber')->get();

        return view('admin.programs', compact('programs'));
    }
}
