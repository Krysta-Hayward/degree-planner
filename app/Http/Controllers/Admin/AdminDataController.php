<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminDataController extends Controller
{
    public function index(?string $table = null)
    {
        $tables = [
            'course_prerequisites',
            'course_program',
            'courses',
            'plan_courses',
            'plans',
            'programs',
            'students',
            'terms',
            'users',
        ];

        $data = [];

        foreach ($tables as $name) {
            if (Schema::hasTable($name)) {
                $data[$name] = DB::table($name)->count();
            }
        }

        $columns = [];
        $records = collect();

        if ($table !== null && array_key_exists($table, $data)) {
            $columns = Schema::getColumnListing($table);
            $records = DB::table($table)->limit(100)->get();

            // Display student and advisor names alongside their IDs.
            if ($table === 'students') {
                // Get user names indexed by their IDs.
                $users = DB::table('users')->pluck('name', 'id');

                $records = $records->map(function ($record) use ($users) {
                    // Preserve the original IDs.
                    $studentId = $record->student;
                    $advisorId = $record->advisor;

                    // Add separate ID columns.
                    $record->student_id = $studentId;
                    $record->advisor_id = $advisorId;

                    // Replace the original ID values with names.
                    $record->student = $users->get(
                        $studentId,
                        'Unknown student'
                    );

                    $record->advisor = $users->get(
                        $advisorId,
                        'Unknown advisor'
                    );

                    return $record;
                });

                // Set the order of the displayed columns.
                $columns = [
                    'id',
                    'student_id',
                    'student',
                    'advisor_id',
                    'advisor',
                    'created_at',
                    'updated_at',
                ];
            }
        } elseif ($table !== null) {
            abort(404, 'Table not found.');
        }

        return view('admin.data', [
            'tables' => $data,
            'selectedTable' => $table,
            'columns' => $columns,
            'records' => $records,
        ]);
    }
}
