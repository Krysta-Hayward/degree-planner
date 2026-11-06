@extends('layouts.app')

@section('title', $course->courseNumber . ' | Degree Planner')

@section('content')
    <div class="public-catalog">
        <h1>{{ $course->courseNumber }}</h1>

        <h2>{{ $course->name }}</h2>

        <table>
            <tbody>
                <tr>
                    <th>Course Number</th>
                    <td>{{ $course->courseNumber }}</td>
                </tr>

                <tr>
                    <th>Course Name</th>
                    <td>{{ $course->name }}</td>
                </tr>

                <tr>
                    <th>Credits</th>
                    <td>{{ $course->credits }}</td>
                </tr>
            </tbody>
        </table>

        <a href="{{ route('public.courses') }}" class="button-links">Back to Courses</a>
    </div>
@endsection