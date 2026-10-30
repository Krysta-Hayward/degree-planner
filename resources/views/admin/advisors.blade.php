@extends('layouts.app')

@section('title', 'Manage Advisors - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <h1>Manage Advisors</h1>
            <p>View advisor accounts and their assigned students.</p>

            <p>
                <strong>Total advisors:</strong> {{ $advisors->count() }}
            </p>

            @if ($advisors->isEmpty())
                <p>No advisor accounts found.</p>
            @else
                <div class="pagination">
                    {{ $advisors->links() }}
                </div>
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
            <div class="pagination">
                {{ $advisors->links() }}
            </div>
        </main>
    </div>
@endsection