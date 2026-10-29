@extends('layouts.app')

@section('title', $programName . ' - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <p>
                <a href="{{ route('admin.programs') }}">← Back to Programs</a>
            </p>

            <h1>{{ $programName }}</h1>
            <p>View the courses associated with this degree program.</p>

            <section>
                <h2>Program Overview</h2>

                <p>
                    <strong>Required Courses:</strong> {{ $courses->count() }}
                </p>

                <p>
                    <strong>Total Credits:</strong> {{ $totalCredits }}
                </p>
            </section>

            <section>
                <h2>Required Courses</h2>

                @if ($courses->isEmpty())
                    <p>No courses are associated with this program.</p>
                @else
                    <div style="overflow-x: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Course Number</th>
                                    <th>Course Name</th>
                                    <th>Credits</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($courses as $program)
                                    <tr>
                                        <td>
                                            {{ $program->courseDetails?->courseNumber ?? 'Unknown course' }}
                                        </td>
                                        <td>
                                            {{ $program->courseDetails?->name ?? 'Unknown course' }}
                                        </td>
                                        <td>
                                            {{ $program->courseDetails?->credits ?? 0 }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </main>
    </div>
@endsection