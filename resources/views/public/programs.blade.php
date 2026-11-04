@extends('layouts.app')

@section('title', 'Programs | Degree Planner')

@section('content')
    <div class="public-catalog">
        <h1>Degree Programs</h1>

        <p class="catalog-description">
            Browse the degree programs available through Degree Planner.
        </p>

        @forelse ($programGroups as $programName => $programRows)
            <section class="catalog-item">
                <h2>{{ $programName }}</h2>

                <table>
                    <thead>
                        <tr>
                            <th>Course Number</th>
                            <th>Course Name</th>
                            <th>Credits</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($programRows as $program)
                            @if ($program->courseDetails)
                                <tr>
                                    <td>
                                        {{ $program->courseDetails->courseNumber }}
                                    </td>
                                    <td>
                                        {{ $program->courseDetails->name }}
                                    </td>
                                    <td>
                                        {{ $program->courseDetails->credits }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </section>
        @empty
            <p>No programs are currently available.</p>
        @endforelse
    </div>
@endsection