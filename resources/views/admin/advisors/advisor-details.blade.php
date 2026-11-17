@extends('layouts.app')

@section('title', 'Advisor Details - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <a href="{{ route('admin.advisors') }}" class="button-links">
                Back to Advisors
            </a>

            <h1>{{ $advisor->name }}</h1>

            <p><strong>Email:</strong> {{ $advisor->email }}</p>

            <p>
                <strong>Assigned Students:</strong>
                {{ $students->total() }}
            </p>

            <h2>Assigned Students</h2>

            @if ($students->isEmpty())
                <p>No students are currently assigned to this advisor.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($students as $student)
                            <tr>
                                <td>{{ $student->name }}</td>
                                <td>{{ $student->email }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="pagination">
                    {{ $students->links() }}
                </div>
            @endif
        </main>
    </div>
@endsection