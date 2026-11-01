@extends('layouts.app')

@section('title', 'Student Degree Plans')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <h1>Student Degree Plans</h1>
            <p>View each student's planned courses and degree progress.</p>

            <section>
                <h2>Overview</h2>
                <p><strong>Students with plans:</strong> {{ $students->count() }}</p>

                @if ($students->isEmpty())
                    <p>No student plans have been created yet.</p>
                @else
                    <div class="pagination">
                        {{ $students->links() }}
                    </div>
                        <div style="overflow-x: auto;">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Planned Courses</th>
                                        <th>Planned Credits</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($students as $student)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.plans.show', $student) }}">
                                                    {{ $student->name }}
                                                </a>
                                            </td>
                                            <td>{{ $student->planned_courses }}</td>
                                            <td>{{ $student->planned_credits }}</td>
                                            <td>
                                                <a href="{{ route('admin.plans.show', $student) }}">
                                                    View Full Plan
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                @endif
            </section>
            <div class="pagination">
                {{ $students->links() }}
            </div>
        </main>
    </div>
@endsection