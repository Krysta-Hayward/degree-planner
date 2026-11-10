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

        <table>
            <tbody>
                <tr>
                    <th>Course Description</th>
                </tr>
                <tr>
                    <td>{{ $course->description }}</td>
                </tr>
            </tbody>
        </table>

        <table>
            <tbody>
                <tr>
                    <th>Class Dates</th>
                    <td>##date placeholder</td>
                </tr>
                <tr>
                    <th>Class Times</th>
                    <td>##time placeholder</td>
                </tr>
            </tbody>
        </table>

        <div>
            <button class="button-links-primary">Enroll</button>
        </div>

        <a href="{{ route('classes') }}" class="button-links">Back to Classes</a>
    </div>
@endsection