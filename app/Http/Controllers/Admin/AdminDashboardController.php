<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $tables = [
            'course_prerequisites' => 'Course Prerequisites',
            'course_program' => 'Course Programs',
            'courses' => 'Courses',
            'plan_courses' => 'Plan Courses',
            'plans' => 'Plans',
            'programs' => 'Programs',
            'students' => 'Students',
            'terms' => 'Terms',
            'users' => 'Users',
        ];

        $counts = [];

        $counts['Users'] = User::where('role', 'users')->count();
        $counts['Advisors'] = User::where('role', 'advisor')->count();
        $counts['Students'] = User::where('role', 'student')->count();

        foreach ($tables as $table => $label) {
            if (Schema::hasTable($table)) {
                $counts[$label] = DB::table($table)->count();
            }
        }

        return view('admin.dashboard', [
            'counts' => $counts,
        ]);
    }
}
