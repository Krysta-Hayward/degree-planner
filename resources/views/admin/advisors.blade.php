@extends('layouts.app')

@section('title', 'Manage Advisors - Degree Planner')

@section('content')
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <h2>Degree Planner</h2>
            <p>Admin Panel</p>

            <nav>
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a href="{{ route('admin.data') }}">Database Overview</a>
                <a href="{{ route('admin.advisors') }}" class="active">
                    Manage Advisors
                </a>
                <a href="{{ route('admin.students') }}">Manage Students</a>
                <a href="/">Home</a>
            </nav>
        </aside>

        <main class="admin-content">
            <h1>Manage Advisors</h1>
            <p>View advisor accounts and their assigned students.</p>

            <p>
                <strong>Total advisors:</strong> {{ $advisors->count() }}
            </p>

            @if ($advisors->isEmpty())
                <p>No advisor accounts found.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Assigned Students</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($advisors as $advisor)
                            <tr>
                                <td>{{ $advisor->name }}</td>
                                <td>{{ $advisor->email }}</td>
                                <td>{{ $advisor->students_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </main>
    </div>
@endsection