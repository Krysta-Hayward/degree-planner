@extends('layouts.app')

@section('title', 'Prerequisites | Degree Planner')

@section('content')
    <div>
        <h1>Course Prerequisites</h1>

        <p>
            Browse the prerequisites required for available courses.
        </p>

        @if ($courses->count())
            <table>
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Course Name</th>
                        <th>Prerequisite</th>
                        <th>Details</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($courses as $course)
                        <tr>
                            <td>{{ $course->courseNumber }}</td>
                            <td>{{ $course->name }}</td>
                            <td>
                                {{ $course->prerequisites_count }}
                                {{ Str::plural('prerequisite', $course->prerequisites_count) }}
                            </td>
                            <td>
                                <a href="{{ route('public.prereqs-details', $course->id) }}">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $courses->links() }}
        @else
            <p>No course prerequisites are currently available.</p>
        @endif
    </div>
@endsection
