```blade
@extends('layouts.app')

@section('title', 'Manage Prerequisites - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <h1>Manage Prerequisites</h1>
            <p>View and manage prerequisite requirements for courses in the Degree Planner.</p>

            <div style="display: flex; align-items: center; gap: 20px;">
                <p>
                    <strong>Total prerequisite relationships:</strong>
                    {{ $prereqs->total() }}
                </p>

                <a href="{{ route('admin.prereqs.create') }}" class="button-links">
                    Create Prerequisite
                </a>
            </div>

            @if (session('success'))
                <p>{{ session('success') }}</p>
            @endif

            @if ($prereqs->isEmpty())
                <p>No prerequisites found.</p>
            @else
                <div class="pagination">
                    {{ $prereqs->links() }}
                </div>

                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Course Number</th>
                                <th>Course Name</th>
                                <th>Prerequisite Number</th>
                                <th>Prerequisite Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($prereqs as $prereq)
                                <tr>
                                    <td>{{ $prereq->courseDetails?->courseNumber ?? 'Unknown course' }}</td>
                                    <td>{{ $prereq->courseDetails?->name ?? 'Unknown course' }}</td>
                                    <td>{{ $prereq->prerequisiteDetails?->courseNumber ?? 'Unknown course' }}</td>
                                    <td>{{ $prereq->prerequisiteDetails?->name ?? 'Unknown course' }}</td>
                                    <td>
                                        <a href="{{ route('admin.prereqs.edit', $prereq) }}" class="button-links">
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('admin.prereqs.destroy', $prereq) }}"
                                            style="display: inline;"
                                            onsubmit="return confirm('Are you sure you want to delete this prerequisite?');">

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
                </div>

                <div class="pagination">
                    {{ $prereqs->links() }}
                </div>
            @endif
        </main>
    </div>
@endsection
```