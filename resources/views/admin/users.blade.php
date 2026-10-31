@extends('layouts.app')

@section('title', 'Manage Users - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <h1>Manage Users</h1>
            <p>View all user accounts in the Degree Planner.</p>

            <p>
                <strong>Total users:</strong> {{ $users->count() }}
            </p>

            @if ($users->isEmpty())
                <p>No user accounts found.</p>
            @else
                <div class="pagination">
                    {{ $users->links() }}
                </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Role</th>
                                <th>Name</th>
                                <th>Email</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ ucfirst($user->role) }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
            @endif
            <div class="pagination">
                {{ $users->links() }}
            </div>
        </main>
    </div>
@endsection