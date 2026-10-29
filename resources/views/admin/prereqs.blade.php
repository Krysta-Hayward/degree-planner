@extends('layouts.app')

@section('title', 'Manage Prerequisites - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('shared.sidebar')

        <main class="admin-content">
            <h1>Manage prerequisites</h1>
            <p>View prerequisites available in the Degree Planner.</p>

            <p>
                <strong>Total prerequisites:</strong> {{ $prereqs->count() }}
            </p>

            @if ($prereqs->isEmpty())
                <p>No prerequisites found.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Prerequisite Number</th>
                            <th>Prerequisite Name</th>
                            <th>Credits</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($prereqs as $prereq)
                            <tr>
                                <td>{{ $prereq->prereqNumber }}</td>
                                <td>{{ $prereq->name }}</td>
                                <td>{{ $prereq->credits }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </main>
    </div>
@endsection