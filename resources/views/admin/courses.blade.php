
@extends('layouts.app')

@section('title', 'Manage Courses - Degree Planner')

@section('content')
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <h2>Degree Planner</h2>
            <p>Admin Panel</p>

            <nav>
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a href="{{ route('admin.data') }}">Database Overview</a>
                <a href="{{ route('admin.users') }}">Manage Users</a>
                <a href="{{ route('admin.advisors') }}">Manage Advisors</a>
                <a href="{{ route('admin.students') }}">Manage Students</a>
                <a href="{{ route('admin.courses') }}" class="active">
                    Manage Courses
                </a>
                <a href="/">Home</a>
            </nav>
        </aside>

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
