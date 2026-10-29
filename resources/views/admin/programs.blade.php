@extends('layouts.app')

@section('title', 'Manage Programs - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <h1>Manage Programs</h1>
            <p>View programs available in the Degree Planner.</p>

            <p>
                <strong>Total programs:</strong> {{ $programs->count() }}
            </p>

            @if ($programs->isEmpty())
                <p>No programs found.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Program Number</th>
                            <th>Program Name</th>
                            <th>Credits</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($programs as $program)
                            <tr>
                                <td>{{ $program->programNumber }}</td>
                                <td>{{ $program->name }}</td>
                                <td>{{ $program->credits }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </main>
    </div>
@endsection