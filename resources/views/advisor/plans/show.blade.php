@extends('layouts.admin-advisor-dashboards')

@section('heading', 'Review Program Map')

@section('dashboard')
    <h2>{{ $programMap->owner->name }}</h2>
    <p>Status: {{ ucfirst(str_replace('_', ' ', $programMap->status)) }}</p>
    <p>Program: {{ $programName ?? 'Not Selected'}}</p>

<!-- Planned Courses --> 
    <h3>Planned Courses</h3>
    @if ($coursesByQuarter->isEmpty())
        <p>This student hasn't planned any courses yet.</p>
    @else
        @foreach ($coursesByQuarter as $quarter => $courses)
        <details>
            <summary>
                    {{ $quarter }} · {{ $courses->count() }} {{ Str::plural('course', $courses->count()) }} · {{ $courses->sum('credits') }} credits
            </summary>

            <table class="student-table">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Name</th>
                        <th>Credits</th>
                    </tr>
                </thead>
                <tbody>
                   @foreach ($courses as $course)
                        <tr>
                            <td>{{ $course->courseNumber }}</td>
                            <td>{{ $course->name }}</td>
                            <td>{{ $course->credits }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <p>Total credits: {{ $courses->sum('credits') }}</p>
        </details>
        @endforeach
    @endif

<!-- Program Requirements-->   
    <details class="comments-panel">
        <summary>Program requirements</summary>

        @if (! $programName)
            <p>This student hasn't selected a program yet.</p>
        @elseif ($requirements->isEmpty())
            <p>No requirements found for {{ $programName }}.</p>
        @else
            <p>{{ $requirements->whereNotNull('plannedIn')->count() }} of {{ $requirements->count() }} required courses planned</p>

            <table class="student-table">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Name</th>
                        <th>Credits</th>
                        <th>Planned</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($requirements as $course)
                        <tr>
                            <td>{{ $course->courseNumber }}</td>
                            <td>{{ $course->name }}</td>
                            <td>{{ $course->credits }}</td>
                            <td>
                                @if ($course->plannedIn)
                                    <span class="plan-status plan-status--ok">{{ $course->plannedIn }}</span>
                                @else
                                    <span class="plan-status plan-status--missing">Not planned</span>
                                @endif
                            </td>
                        </tr>
                     @endforeach
                </tbody>
            </table>
        @endif
    </details>
<!-- Program Prerequisites-->
    <details class="comments-panel">
        <summary>Prerequisites</summary>

        @if ($prerequisites->isEmpty())
            <p>None of the planned courses have prerequisites.</p>
        @else
            <table class="student-table">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Requires</th>
                        <th>Prerequisite</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($prerequisites as $row)
                        <tr>
                            <td>{{ $row->course_number }}</td>
                            <td>{{ $row->prereq_number }}</td>
                            <td>
                                @if ($row->prereqPlannedIn)
                                    <span class="plan-status plan-status--ok">Planned in {{ $row->prereqPlannedIn }}</span>
                                @else
                                    <span class="plan-status plan-status--missing">Missing from plan</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </details>

<!--Comments-->
<!-- Uncomment after testing -->
    

    @can('review', $programMap)
        <div class="review-actions">
            <!-- Approve button -->
            <form method="POST" action="{{ route('advisor.plans.approve', $programMap) }}">
                @csrf
                <button type="submit" class="button-links">Approve</button>
            </form>

            <!-- Request Change button -->
            <form method="POST" action="{{ route('advisor.plans.changes', $programMap) }}">
                @csrf
                <button type="submit" class="button-links">Request changes</button>
            </form>
        </div>
    @endcan

    <a href="{{ route('advisor.plans.index') }}" class="button-links">Back to plan reviews</a>
@endsection