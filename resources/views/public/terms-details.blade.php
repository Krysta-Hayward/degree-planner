@extends('layouts.app')

@section('title', 'Term Details | Degree Planner')

@section('content')
    <div>
        <h1>{{ $term->quarterYear }}</h1>

        <h2>Associated Course</h2>

        <table>
            <thead>
                <tr>
                    <th>Course Number</th>
                    <th>Course Name</th>
                    <th>Credits</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($courses as $course)
                    <tr>
                        <td>{{ $course->courseNumber }}</td>
                        <td>{{ $course->name }}</td>
                        <td>{{ $course->credits }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspane="3">No courses are available for this term.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <a href="{{ route('public.terms.index') }}" class="button-links">Back to Terms</a>
    </div>
@endsection