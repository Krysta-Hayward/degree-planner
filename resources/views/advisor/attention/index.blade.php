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
                    <th>Plan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($flaggedStudents as $student)
                    <tr>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->email }}</td>
                        <td>
                            <div class="reason-list">
                                @foreach ($student->reasons as $reason)
                                    <span class="plan-status plan-status--missing">{{ $reason }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td>
                            @if ($student->mapId)
                                <a href="{{ route('advisor.plans.show', $student->mapId) }}" class="button-links">View plan</a>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection