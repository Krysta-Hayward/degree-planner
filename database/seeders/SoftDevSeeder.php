<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SoftDevSeeder extends Seeder
{
    public function run(): void
    {
        // Array of courses
        $courses = [
            // First Quarter
            ['CIS 107', 'Software Math',  2],
            ['CIS 108', 'Computer Math',  3],
            ['CIS 111', 'HTML5/CSS3',  5],
            ['CIS 146', 'Introduction to Programming',  5],
            // Second Quarter
            ['CIS 114', 'Frontend Development I', 5],
            ['CIS 126', 'DBMS/SQL', 5],
            ['CIS 130', 'Responsive Web Design',  5],
            // Third Quarter
            ['CIS 117', 'Frontend Development II', 5],
            ['CMST&210', 'Interpersonal Communication', 5],
            ['ENGL&101', 'English Composition I', 5],
            // Fourth Quarter
            ['CIS 230', 'PHP I', 5],
            ['CIS 258',  'Backend Development I',  5],
            ['CIS 292',  'Programming Principles',  5],
            // Fifth Quarter
            ['CIS 217', 'Mobile Development I',  5],
            ['CIS 233',  'PHP II',  5],
            ['CIS 259',  'Backend Development II',  5],
            // Sixth Quarter
            ['CIS 218', 'Mobile Development II', 5],
            ['CIS 225', 'Content Magement Systems', 5],
            ['CIS 272', 'Agile Software Development', 5],
        ];

        // Program Name
        $ProgName = 'Software Development';

        // Seed courses
        foreach ($courses as $course) {
            DB::table('courses')->updateOrInsert(
                ['name' => $course[1]],
                [
                    'courseNumber' => $course[0],
                    'name' => $course[1],
                    'credits' => $course[2],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        }

        // Retrieve seeded courses
        $courses2 = Course::orderBy('id', 'desc')->take(19)->get();

        // Seed course
        foreach ($courses2->sortBy('id') as $course) {
            DB::table('programs')->updateOrInsert(
                ['name' => 'Software Development', 'course' => $course->id],
                [
                    'name' => 'Software Development',
                    'course' => $course->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        }
    }
}
