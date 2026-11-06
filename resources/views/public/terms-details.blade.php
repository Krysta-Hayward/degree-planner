@extends('layouts.app')

@section('title', 'Term Details | Degree Planner')

@section('content')
    <div>
        <h1>{{ $term->quarterYear }}</h1>

        <h2>Associated Course</h2>

        <table>
            <thead>
                <tr>
                    <th>Course Number</th>
                    <th>Course Name</th>
                    <th>Credits</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>{{ $term->courseDetails->courseNumber }}</td>
                    <td>{{ $term->courseDetails->name }}</td>
                    <td>{{ $term->courseDetails->credits }}</td>
                </tr>
            </tbody>
        </table>

        <a href="{{ route('public.terms.index') }}" class="button-links">Back to Terms</a>
    </div>
@endsection