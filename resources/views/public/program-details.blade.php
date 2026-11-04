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
                            <td>{{ $row->courseDetails->courseNumber }}</td>
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

        <p>
            <a href="{{ route('public.programs') }}">Back to Programs</a>
        </p>
    </div>
@endsection