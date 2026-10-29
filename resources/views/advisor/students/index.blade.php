@extends('layouts.admin-advisor-dashboards')

@section('dashboard')
    <h2>My Students</h2>

    @if ($students->isEmpty())
        <p>No students are assigned to you yet.</p>
    @else
        <table class="student-table">
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
    @endif
@endsection
