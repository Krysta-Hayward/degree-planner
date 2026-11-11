@extends('layouts.admin-advisor-dashboards')

@section('heading', 'Needs Attention')

@section('dashboard')
    @if ($flaggedStudents->isEmpty())
        <p>All your students are on track.</p>
    @else
        <table class="student-table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Email</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($flaggedStudents as $student)
                    <tr>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->email }}</td>
                        <td>
                            @foreach ($student->reasons as $reason)
                                <span class="plan-status plan-status--missing">{{ $reason }}</span>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection