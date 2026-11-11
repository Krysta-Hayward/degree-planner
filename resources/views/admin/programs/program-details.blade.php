@extends('layouts.app')

@section('title', $programName . ' - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <a href="{{ route('admin.programs.index') }}" class="button-links">← Back to Programs</a>

            @if (session('success'))
                <div class="alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <h1>{{ $programName }}</h1>
            <p>{{ $description ?: 'No description yet.' }}</p>

            <a href="{{ route('admin.programs.edit', ['programName' => $programName]) }}" class="button-links">Edit
                Program</a>

            <section>
                <h2>Program Overview</h2>

                <p>
                    <strong>Required Courses:</strong> {{ $courses->count() }}
                </p>

                <p>
                    <strong>Total Credits:</strong> {{ $totalCredits }}
                </p>
            </section>

            @foreach ($groups as $group => $groupCourses)
                <section>
                    <h2>{{ $group }}</h2>
                    <p>
                        {{ $groupCourses->count() }} {{ \Illuminate\Support\Str::plural('course', $groupCourses->count()) }},
                        {{ $groupCourses->sum(fn($program) => $program->courseDetails?->credits ?? 0) }} credits
                    </p>

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
                                @foreach ($groupCourses as $program)
                                    <tr>
                                        <td>{{ $program->courseDetails?->courseNumber ?? 'Unknown course' }}</td>
                                        <td>{{ $program->courseDetails?->name ?? 'Unknown course' }}</td>
                                        <td>{{ $program->courseDetails?->credits ?? 0 }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @endforeach
        </main>
    </div>
@endsection