@extends('layouts.admin-advisor-dashboards')

@section('heading', 'Plan Reviews')

@section('dashboard')
    @if (session('success'))
        <p class="success">{{ session('success') }}</p>
    @endif

    @if ($maps->isEmpty())
        <p>No plans waiting for review.</p>
    @else
        <table class="student-table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Submitted</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($maps as $map)
                    <tr>
                        <td>{{ $map->owner->name }}</td>
                        <td>{{ $map->updated_at->format('M j, Y') }}</td>
                        <td><a href="{{ route('advisor.plans.show', $map) }}">Review</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection