@extends('layouts.app')

@section('title', 'Prerequisites | Degree Planner')

@section('content')
    <div>
        <h1>Course Prerequisites</h1>

        <p>
            Browse the prerequisites required for available courses.
        </p>

        @if ($prereqs->count())
            <table>
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Course Name</th>
                        <th>Prerequisite</th>
                        <th>Prerequisite Name</th>
                        <th>Details</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($prereqs as $prereq)
                        <tr>
                            <td>{{ $prereq->courseDetails->courseNumber }}</td>
                            <td>{{ $prereq->courseDetails->name }}</td>
                            <td>{{ $prereq->prerequisiteDetails->courseNumber }}</td>
                            <td>{{ $prereq->prerequisiteDetails->name }}</td>
                            <td>
                                <a href="{{ route('public.prereqs-details', $prereq->id) }}">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $prereqs->links() }}
        @else
            <p>No course prerequisites are currently available.</p>
        @endif
    </div>
@endsection
