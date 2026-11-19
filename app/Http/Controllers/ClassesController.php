<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Course;
use App\Models\Term;

class ClassesController extends Controller
{
    public function index()
    {
        $classes = Classroom::with('courseDetails')
            ->whereHas('courseDetails')
            ->orderBy(
                Course::select('courseNumber')
                    ->whereColumn('courses.id', 'classrooms.course')
            )
            ->paginate(15);

        return view('classes', compact('classes'));
    }

    public function show(Classroom $classroom)
    {
        $classroom->load('courseDetails');

        $availableTerms = Term::where('course', $classroom->course)
            ->get()
            ->sortBy(function ($term) {
                $seasons = [
                    'Winter' => 1,
                    'Spring' => 2,
                    'Summer' => 3,
                    'Fall' => 4,
                ];

                preg_match('/(\w+)\s+(\d{4})/', $term->quarterYear, $matches);

                $season = $seasons[$matches[1] ?? ''] ?? 0;
                $year = (int) ($matches[2] ?? 0);

                return ($year * 10) + $season;
            })
            ->values();

        return view('class-details', [
            'classroom' => $classroom,
            'availableTerms' => $availableTerms,
        ]);
    }
}
