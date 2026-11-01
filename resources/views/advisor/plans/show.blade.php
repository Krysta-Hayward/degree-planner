@extends('layouts.admin-advisor-dashboards')

@section('heading', 'Review Program Map')

@section('dashboard')
    <h2>{{ $programMap->owner->name }}</h2>
    <p>Status: {{ ucfirst(str_replace('_', ' ', $programMap->status)) }}</p>

    @if ($coursesByQuarter->isEmpty())
        <p>This student hasn't planned any courses yet.</p>
    @else
        @foreach ($coursesByQuarter as $quarter => $courses)
            <h3>{{ $quarter }}</h3>

            <table class="student-table">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Name</th>
                        <th>Credits</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($courses as $course)
                        <tr>
                            <td>{{ $course->courseNumber }}</td>
                            <td>{{ $course->name }}</td>
                            <td>{{ $course->credits }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <p>Total credits: {{ $courses->sum('credits') }}</p>
        @endforeach
    @endif

    @can('review', $programMap)
        <form method="POST" action="{{ route('advisor.plans.approve', $programMap) }}">
            @csrf
            <button type="submit">Approve</button>
        </form>

        <form method="POST" action="{{ route('advisor.plans.changes', $programMap) }}">
            @csrf
            <button type="submit">Request changes</button>
        </form>
    @endcan

    <a href="{{ route('advisor.plans.index') }}">Back to plan reviews</a>
@endsection