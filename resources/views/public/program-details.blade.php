@extends('layouts.app')

@section('title', $program->name . ' | Degree Planner')

@section('content')
    <div class="public-catalog">
        <h1>{{ $program->name }}</h1>

        <p class="catalog-description">
            Required courses for this degree program.
        </p>

        <table>
            <thead>
                <tr>
                    <th>Course Number</th>
                    <th>Course Name</th>
                    <th>Credits</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($programRows as $row)
                    @if ($row->courseDetails)
                        <tr>
                            <td><a href="{{ route('public.courses.show', $row->courseDetails->id) }}">
                                {{ $row->courseDetails->courseNumber }}
                            </a></td>
                            <td>{{ $row->courseDetails->name }}</td>
                            <td>{{ $row->courseDetails->credits }}</td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="3">No required courses found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

            <a href="{{ route('public.programs') }}" class="button-links">Back to Programs</a>
    </div>
@endsection