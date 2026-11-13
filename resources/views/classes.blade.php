@extends('layouts.app')

@section('title', 'Classes | Degree Planner')

@section('content')
    <div>
        <h1>Available Classes</h1>

        <p>
            Browse the classes available through Degree Planner.
        </p>

        @if ($classes->count())
            <table>
                <thead>
                    <tr>
                        <th>Course Number</th>
                        <th>Course Name</th>
                        <th>Credits</th>
                        <th>Details</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($classes as $classroom)
                        <tr>
                            <td>{{ $course->courseNumber }}</td>
                            <td>{{ $course->name }}</td>
                            <td>{{ $course->credits }}</td>
                            <td>
                                <a href="{{ route('class-details', ['classroom' => $classroom->id]) }}">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $classes->links() }}
        @else
            <p>No courses are currently available.</p>
        @endif
    </div>
@endsection