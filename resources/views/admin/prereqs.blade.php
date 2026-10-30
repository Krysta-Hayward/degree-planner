@extends('layouts.app')

@section('title', 'Manage Prerequisites - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <h1>Manage Prerequisites</h1>
            <p>View the prerequisite requirements for courses in the Degree Planner.</p>

            <p>
                <strong>Total prerequisite relationships:</strong> {{ $prereqs->count() }}
            </p>

            @if ($prereqs->isEmpty())
                <p>No prerequisites found.</p>
            @else
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Course Number</th>
                                <th>Course Name</th>
                                <th>Prerequisite Number</th>
                                <th>Prerequisite Name</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($prereqs as $prereq)
                                <tr>
                                    <td>{{ $prereq->courseDetails?->courseNumber ?? 'Unknown course' }}</td>
                                    <td>{{ $prereq->courseDetails?->name ?? 'Unknown course' }}</td>
                                    <td>{{ $prereq->prerequisiteDetails?->courseNumber ?? 'Unknown course' }}</td>
                                    <td>{{ $prereq->prerequisiteDetails?->name ?? 'Unknown course' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </main>
    </div>
@endsection