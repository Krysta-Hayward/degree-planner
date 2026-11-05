<?php

namespace App\Http\Controllers;

use App\Models\Term;
use Illuminate\Http\Request;

class PublicTermController extends Controller
{

    public function index()
    {
        $terms = Term::with('courseDetails')
            ->orderBy('quarterYear')
            ->paginate(50);

        return view('public.terms', compact('terms'));
    }


    public function show(Term $term)
    {
        $term->load('courseDetails');

        return view('public.terms-details', compact('term'));
    }
}
