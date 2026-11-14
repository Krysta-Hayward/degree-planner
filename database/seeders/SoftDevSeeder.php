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
            ['CIS 107', 'Software Math', 2, 'This course covers math concepts for Software Development Students, and covers objectives such as floating point math, assignment operations vs. equations, computer precedence, Boolean logic, and math related computer programming concepts.'],
            ['CIS 108', 'Computer Math', 3, 'Previously known as MATH 104. This course consists of general computer math concepts for all CIS AAS students. Students then continue with either math targeted to specific Network Design and Administration objectives or specific Software Development objectives.'],
            ['CIS 111', 'HTML5/CSS3', 5, 'This course introduces basic concepts of structuring and styling a static website with Hypertext Markup Language (HTML) and Cascading Style Sheets (CSS).'],
            ['CIS 146', 'Introduction to Programming', 5, 'This course will teach core programming concepts using examples from weakly and strongly typed programming languages. Debugging techniques are utilized by students to solve beginning-level software development problems. The understanding and mastery of the terms, concepts, and theories of today\'s software developers are the main objectives of this course.'],
            // Second Quarter
            ['CIS 114', 'Frontend Development I', 5, 'This course reinforces basic programming concepts through teaching the fundamentals of the client-side JavaScript language as well as Document Object Model (DOM) scripting. Included is an introduction to jQuery or other JavaScript library and 3rd party plugins. Prerequisite: CIS 111 and CIS 146, both with a 2.0 or better or instructor permission. Offered in spring odd years and fall and winter in even years.'],
            ['CIS 126', 'DBMS/SQL', 5, 'This course introduces ANSI SQL. Students learn the uses of SQL scripting as it pertains to common database management systems such as MySQL, Oracle, SQL Server or DB2. Students acquire the ability to create, modify and delete data and data structures. Students also learn to implement SQL using web technologies.'],
            ['CIS 130', 'Responsive Web Design', 5, 'This course will teach how to utilize responsive website design to create websites that can be professionally viewed on a variety of mobile devices, tablets, laptops and desktop computers with varying screen sizes. Prerequisite: CIS 111 with a 2.0 or better or instructor permission'],
            // Third Quarter
            ['CIS 117', 'Frontend Development II', 5, 'Students will learn the fundamentals of writing applications using a popular front-end framework. Students create scalable single page applications (SPA) for any type of website. Prerequisite: CIS 114 with a 2.0 or better or permission of instructor. Offered in spring even years and fall and winter odd years.'],
            ['CMST&210', 'Interpersonal Communication', 5, 'This course provides an opportunity to learn and apply the theory of interpersonal communication. Learning experiences include work with personal growth, verbal and nonverbal communication skills, active listening, stress management, and resolving communication conflicts to develop healthy personal relationships.'],
            ['ENGL&101', 'English Composition I', 5, 'This course develops and sharpens the basic principles of writing college-level essays. Students work on a series of essays to improve their ability to write clear, detailed prose and to use texts to support their claims. Competence in mechanics and standard English usage is assumed of all students taking ENGL& 101. SCC prerequisite: Self-placement. SFCC prerequisite: Successful completion of ENGL 99, or appropriate placement.'],
            // Fourth Quarter
            ['CIS 230', 'PHP I', 5, 'Students learn how to create powerful, interactive, database-driven web sites using PHP and a popular Model-View-Controller (MVC) framework. Prerequisite: CIS 114 and 126 with a 2.0 or better or instructor permission. Offered in spring odd years and in fall and winter in even years.'],
            ['CIS 258', 'Backend Development I', 5, 'This course presents concepts of creating websites through the use of a backend server architecture, including creating web services, APIs, and dynamic web pages that are connected to a database for storage of information. Students will connect browser-based interfaces to the back-end server through RESTful APIs. Prerequisite: Successful completion of CIS 114 with a 2.0 or better or permission of instructor. Offered in spring in odd years and in fall and winter in even years.'],
            ['CIS 282', 'Programming Principles', 5, 'Students learn object-oriented fundamentals using a modern programming language. Students are challenged to solve problems in an object-oriented fashion. Prerequisite: CIS 146 with a passing grade of 2.0 or better or permission of instructor. Offered in spring in odd years and in fall and winter in even years.'],
            // Fifth Quarter
            ['CIS 217', 'Mobile Development I', 5, 'This course introduces the fundamentals of mobile application development. Students will learn to build applications for smartphones and tablets using modern mobile development frameworks and tools. Prerequisite: CIS 282 with a passing grade of 2.0 or better or permission of instructor. Offered in spring even years and in fall and winter odd years.'],
            ['CIS 233', 'PHP II', 5, 'This course is an extension of CIS 230 and provides students a deeper understanding of creating powerful, interactive, database-driven web sites using PHP and a popular Model-View-Controller (MVC) framework. Students will learn how to implement authentication for a web application. Prerequisite: CIS 230 with a 2.0 or better or permission of instructor. Offered in spring in even years and in fall and winter in odd years.'],
            ['CIS 259', 'Backend Development II', 5, 'This course is an extension of CIS 258 and provides students a deeper understanding of creating backend applications using a server-side framework or platform. Prerequisite: CIS 117 and CIS 258 with a passing grade of 2.0 or better or permission of instructor. Offered in spring in even years and in fall and winter in odd years.'],
            // Sixth Quarter
            ['CIS 218', 'Mobile Development II', 5, 'This course presents advanced concepts of mobile application development. Students will build mobile applications that interact with online services through standard Application Programming Interfaces (APIs), including creating and consuming web-based APIs for use with native mobile applications. Prerequisite: Successful completion of CIS 217 with a 2.0 or better, or permission of instructor. Offered in spring odd years and in even years fall and winter.'],
            ['CIS 225', 'Content Magement Systems', 5, 'This course will provide an overview of existing Content Management Systems (CMS) and how to utilize these to create simple integrated websites. Prerequisite: CIS 230 with a passing grade of 2.0 or better or permission of instructor. Offered in spring odd years and in fall and winter in even years,'],
            ['CIS 272', 'Agile Software Development', 5, 'Students will learn about iterative and incremental development techniques found in agile programming methodologies. Students will have hands on experience working in teams and using tools to do source code versioning, testing, refactoring, and continuous integration. Prerequisite: CIS 259 and CIS 233 with a passing grade of 2.0 or better or permission of instructor.'],
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
                    'description' => $course[3],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
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
                ]
            );
        }
    }
}
