@extends('layouts.app')

@section('title', 'Manage Classrooms - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <h1>Manage Classrooms</h1>
            <p>View and manage classroom sections available in Degree Planner.</p>

            <div style="display: flex; align-items: center; gap: 20px;">
                <p>
                    <strong>Total classrooms:</strong> {{ $classrooms->total() }}
                </p>

                <a href="{{ route('admin.classrooms.create') }}" class="button-links">
                    Create Classroom
                </a>
            </div>

            @if (session('success'))
                <div class="alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert-error">
                    {{ session('error') }}
                </div>
            @endif

            @if ($classrooms->isEmpty())
                <p>No classrooms found.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Course Number</th>
                            <th>Course Name</th>
                            <th>Teacher</th>
                            <th>Total Seats</th>
                            <th>Open Seats</th>
                            <th>Terms Available</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($classrooms as $classroom)
                            <tr>
                                <td>
                                    {{ $classroom->courseDetails->courseNumber ?? 'Unknown' }}
                                </td>

                                <td>
                                    {{ $classroom->courseDetails->name ?? 'Unknown' }}
                                </td>

                                <td>
                                    {{ $classroom->teacher ?? 'Not Assigned' }}
                                </td>

                                <td>{{ $classroom->seats }}</td>

                                <td>{{ $classroom->seatsOpen }}</td>

                                <td>
                                    @php
                                        $terms = [];

                                        if ($classroom->fall)
                                            $terms[] = 'Fall';
                                        if ($classroom->winter)
                                            $terms[] = 'Winter';
                                        if ($classroom->spring)
                                            $terms[] = 'Spring';
                                        if ($classroom->summer)
                                            $terms[] = 'Summer';
                                    @endphp

                                    {{ count($terms) ? implode(', ', $terms) : 'None' }}
                                </td>

                                <td>
                                    <a href="{{ route('admin.classrooms.edit', $classroom) }}" class="button-links">
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('admin.classrooms.destroy', $classroom) }}"
                                        onsubmit="return confirm('Are you sure you want to delete this classroom?');"
                                        style="display: inline;">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="button-links-danger">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="pagination">
                    {{ $classrooms->links() }}
                </div>
            @endif
        </main>
    </div>
@endsection