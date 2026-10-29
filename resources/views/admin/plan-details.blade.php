@extends('layouts.app')

@section('title', $student->name . "'s Degree Plan")

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <p><a href="{{ route('admin.plans') }}">← Back to Student Plans</a></p>

            <h1>{{ $student->name }}'s Degree Plan</h1>
            <p>Review the courses currently included in this student's plan.</p>

            <section>
                <h2>Plan Overview</h2>
                <p><strong>Student:</strong> {{ $student->name }}</p>
                <p><strong>Email:</strong> {{ $student->email }}</p>
                <p><strong>Planned Courses:</strong> {{ $plans->count() }}</p>
                <p><strong>Total Planned Credits:</strong> {{ $totalCredits }}</p>
            </section>

            <section>
                <h2>Planned Courses</h2>

                @if ($plans->isEmpty())
                    <p>This student has no courses in their plan yet.</p>
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
                                @foreach ($plans as $plan)
                                    <tr>
                                        <td>{{ $plan->course?->courseNumber ?? 'Unknown course' }}</td>
                                        <td>{{ $plan->course?->name ?? 'Unknown course' }}</td>
                                        <td>{{ $plan->course?->credits ?? 0 }}</td>
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
