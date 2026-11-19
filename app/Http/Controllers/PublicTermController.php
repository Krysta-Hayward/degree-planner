<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Term;

class PublicTermController extends Controller
{
    public function index()
    {
        $terms = Term::selectRaw('MIN(id) as id, quarterYear')
            ->groupBy('quarterYear')
            ->orderBy('quarterYear')
            ->paginate(15);

        return view('public.terms', compact('terms'));
    }

    public function show(Term $term)
    {
        $courses = Course::whereIn('id', Term::where('quarterYear', $term->quarterYear)
            ->select('course')
            ->distinct())
            ->orderBy('courseNumber')
            ->get();

        return view('public.terms-details', compact('term', 'courses'));
    }
}
