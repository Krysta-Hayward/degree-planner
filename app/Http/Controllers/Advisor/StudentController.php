<?php

/*
List of the Advisor's students
View one student's details and plans
*/

namespace App\Http\Controllers\Advisor;

use App\Http\Controllers\Controller;

class StudentController extends Controller
{
    //
    public function index()
    {
        return 'Hello world from student list';
    }

    public function show(User $student)
    {
        return 'Hello world from studen: '.$student->name;
    }
}
