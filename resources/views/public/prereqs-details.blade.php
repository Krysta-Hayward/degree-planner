@extends('layouts.app')

@section('title', 'Prerequisite Details | Degree Planner')

@section('content')
    <div>
        <h1>Course Prerequisite Details</h1>

        <h2>Course</h2>

        <p>
            <strong>{{ $prereqs->first()->courseDetails->courseNumber }}</strong>
            - {{ $prereqs->first()->courseDetails->name }}
        </p>

        <h2>
            Required {{ $prereqs->count() > 1 ? 'Prerequisites' : 'Prerequisite' }}
        </h2>

        <table>
            <thead>
                <tr>
                    <th>Course Number</th>
                    <th>Course Name</th>
                    <th>Credits</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($prereqs as $prereq)
                    <tr>
                        <td><a href="{{ route('public.courses.show', $prereq->prerequisiteDetails->id) }}">
                            {{ $prereq->prerequisiteDetails->courseNumber }}
                        </a></td>
                        <td>{{ $prereq->prerequisiteDetails->name }}</td>
                        <td>{{ $prereq->prerequisiteDetails->credits }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <a href="{{ route('public.prereqs') }}" class="button-links">
            Back to Prerequisites
        </a>
    </div>
@endsection
