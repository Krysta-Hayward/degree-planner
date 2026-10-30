<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Term;
use Illuminate\View\View;

class AdminTermController extends Controller
{
    public function index(): View
    {
        $terms = Term::orderBy('termNumber')->get();

        return view('admin.terms', compact('terms'));
    }
}
