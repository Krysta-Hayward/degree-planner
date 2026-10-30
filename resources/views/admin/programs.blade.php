@extends('layouts.app')

@section('title', 'Manage Programs - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <h1>Manage Programs</h1>
            <p>View degree programs and their required courses.</p>

            <p>
                <strong>Total programs:</strong> {{ $programs->count() }}
            </p>

            @if ($programs->isEmpty())
                <p>No programs found.</p>
            @else
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Program Name</th>
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
                                    <td>{{ $program->course_count }}</td>
                                    <td>
                                        <a href="{{ route('admin.programs.show', ['programName' => $program->name]) }}">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </main>
    </div>
@endsection