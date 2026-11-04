@extends('layouts.app')

@section('title', 'Courses | Degree Planner')

@section('content')
<div>
    <h1>Available Courses</h1>

    <p>
        Browse the courses available through Degree Planner.
    </p>

    @if ($courses->count())
    <table>
        <thead>
            <tr>
                <th>Course Number</th>
                <th>Course Name</th>
                <th>Credits</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($courses as $course)
            <tr>
                <td>{{ $course->courseNumber }}</td>
                <td>{{ $course->name }}</td>
                <td>{{ $course->credits }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $courses->links() }}
    @else
    <p>No courses are currently available.</p>
    @endif
</div>
@endsection