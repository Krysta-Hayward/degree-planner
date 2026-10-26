<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class AdvisorStudentController extends Controller
{
    // Display a list of advisors students
    // Finds the student assigned to advisor
    // Need to link students to advisor
    // Display a table of students
    public function index()
    {

        // IDs of students assigned to this advisor
        $studentIds = DB::table('students')
            ->where('advisor', auth()->id())
            ->pluck('student');

        // The student accounts for those IDs
        $students = User::whereIn('id', $studentIds)
            ->orderBy('name')
            ->get();


        return view('advisor.students.index', [
            'advisorPage' => 'partials.advisor.student-list',
            'students' => $students
        ]);
    }
}
