
@extends('layouts.app')

@section('title', 'Manage Users - Degree Planner')

@section('content')
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <h2>Degree Planner</h2>
            <p>Admin Panel</p>

            <nav>
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a href="{{ route('admin.data') }}">Database Overview</a>
                <a href="{{ route('admin.users') }}" class="active">Manage Users</a>
                <a href="{{ route('admin.advisors') }}">Manage Advisors</a>
                <a href="{{ route('admin.students') }}">Manage Students</a>
                <a href="/">Home</a>
            </nav>
        </aside>

        <main class="admin-content">
            <h1>Manage Users</h1>
            <p>View all user accounts in the Degree Planner.</p>

            <p>
                <strong>Total users:</strong> {{ $users->count() }}
            </p>

            @if ($users->isEmpty())
                <p>No user accounts found.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ ucfirst($user->role) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </main>
    </div>
@endsection
