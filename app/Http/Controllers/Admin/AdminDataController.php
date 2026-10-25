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
            'users',
            'programs',
            'courses',
            'terms',
            'plans',
            'plan_courses',
            'course_program',
            'course_prerequisites',
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
