@extends('layouts.app')

@section('title', 'Manage Courses - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('shared.sidebar')

        <main class="admin-content">
            <h1>Manage Courses</h1>
            <p>View courses available in the Degree Planner.</p>

            <p>
                <strong>Total courses:</strong> {{ $courses->count() }}
            </p>

            @if ($courses->isEmpty())
                <p>No courses found.</p>
            @else
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
            @endif
        </main>
    </div>
@endsection
