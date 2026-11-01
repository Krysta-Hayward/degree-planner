@extends('layouts.app')

@section('title', 'Manage Programs - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <h1>Manage Programs</h1>
            <p>View, create, and edit degree programs and their required courses.</p>

            @if (session('success'))
                <p class="notice">{{ session('success') }}</p>
            @endif

            <div style="display: flex; align-items: center; gap: 20px;">
                <p>
                    <strong>Total programs:</strong> {{ $programs->total() }}
                </p>
                <button class="button-links"><a href="{{ route('admin.programs.create') }}">Create Program</a></button>
            </div>

            @if ($programs->isEmpty())
                <p>No programs found.</p>
            @else
                <div class="pagination">
                    {{ $programs->links() }}
                </div>
                    <div style="overflow-x: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Program Name</th>
                                    <th>Description</th>
                                    <th>Required Courses</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($programs as $program)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.programs.show', ['programName' => $program->name]) }}">
                                                {{ $program->name }}
                                            </a>
                                        </td>
                                        <td>{{ \Illuminate\Support\Str::limit($program->description ?? '—', 80) }}</td>
                                        <td>{{ $program->course_count }}</td>
                                        <td>
                                            <a href="{{ route('admin.programs.show', ['programName' => $program->name]) }}">
                                                View Details
                                            </a>
                                            |
                                            <a href="{{ route('admin.programs.edit', ['programName' => $program->name]) }}">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
            @endif
            <div class="pagination">
                {{ $programs->links() }}
            </div>
        </main>
    </div>
@endsection