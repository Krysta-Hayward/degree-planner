<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdvisorStudentController extends Controller
{
    // Display a list of advisors students
    // Finds the student assigned to advisor
    // Need to link students to advisor
    // Display a table of students
    public function index()
    {

        $students = User::where('advisor_id', auth()->id())
            ->orderBy('name')
            ->get();

        return view('advisor.students.index', [
            'advisorPage' => 'partials.advisor-students',
            'students' => $students
        ]);
    }
}
