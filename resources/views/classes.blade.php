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
                        <th>Terms Available</th>
                        <th>Details</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($classes as $classroom)
                        <tr>
                            <td>{{ $classroom->courseDetails->courseNumber }}</td>
                            <td>{{ $classroom->courseDetails->name }}</td>
                            <td>{{ $classroom->courseDetails->credits }}</td>
                            <td>@php $terms = [];
                                if ($classroom->fall)
                                    $terms[] = 'Fall';
                                if ($classroom->winter)
                                    $terms[] = 'Winter';
                                if ($classroom->spring)
                                    $terms[] = 'Spring';
                                if ($classroom->summer)
                                    $terms[] = 'Summer';
                            @endphp

                                {{ count($terms) ? implode(', ', $terms) : 'Not Available' }}

                            </td>
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