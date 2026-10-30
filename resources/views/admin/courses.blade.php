@extends('layouts.app')

@section('title', 'Manage Courses - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <h1>Manage Courses</h1>
            <p>View courses available in the Degree Planner.</p>

            <div style="display: flex; align-items: center; gap: 20px;">
                <p>
                    <strong>Total courses:</strong> {{ $courses->total() }}
                </p>
                <button class="button-links"><a href="{{ route('admin.courses.create') }}">Create Course</a></button>
            </div>

            @if ($courses->isEmpty())
                <p>No courses found.</p>
            @else
                <div class="pagination">
                    {{ $courses->links() }}
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Course Number</th>
                            <th>Course Name</th>
                            <th>Credits</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($courses as $course)
                            <tr>
                                <td>{{ $course->courseNumber }}</td>
                                <td>{{ $course->name }}</td>
                                <td>{{ $course->credits }}</td>
                                <td><a href="{{  route('admin.courses.edit', $course) }}" class="button-links">Edit</a>
                                    <form method="POST" action="{{ route('admin.courses.destroy', $course) }}"
                                        onsubmit="return confirm('Are you sure you want to delete this course?');"
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
            @endif
            <div class="pagination">
                {{ $courses->links() }}
            </div>
        </main>
    </div>
@endsection