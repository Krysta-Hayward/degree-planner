<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdvisorStudentController extends Controller
{
    // Display a list of advisors students
    // Finds the student assigned to advisor
    // Need to link students to advisor
    // Display a table of students
    public function index()
    {
        return 'Hello world from the advisor student list';
    }
}
