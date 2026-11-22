@extends('layouts.app')

@section('title', 'My Degree Plan - Degree Planner')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/student.css') }}">

    <div class="student-page" id="student-top">
        @php
            $status = $programMap?->status ?? 'draft';
            $locked = in_array($status, ['submitted', 'approved']);

            $plannedQuarterOf = $planned->pluck('quarterYear', 'course_id');
            $coveredCourseIds = array_merge($plannedCourseIds, $passedCourseIds);
        @endphp

        <h1>My Degree Plan</h1>
        <p>Welcome, {{ $user->name }}.</p>

        @if (session('status'))
            <p class="notice">{{ session('status') }}</p>
        @endif

        @if ($errors->any())
            <p class="error">{{ $errors->first() }}</p>
        @endif

        <section>
            <h2>My Degree Program</h2>

            @if ($user->program)
                <!-- Program chosen: show it, with a way to change it -->
                <p>Selected program: <strong>{{ $user->program }}</strong></p>

                @unless ($locked)
                    <form method="POST" action="{{ route('student.program.clear') }}" class="inline-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Changing your program will remove every course from your program map. Continue?')">Change Program</button>
                    </form>
                @endunless
            @else
                <!-- Missing prerequisites: say what to add first -->
                <p>You have not picked a program yet.</p>

                <form method="POST" action="{{ route('student.program.select') }}">

                <!-- No program: let the student choose one -->
                <form method="POST" action="{{ route('student.program.select') }}">
                    @csrf
                    <label for="program">Choose your program</label>
                    <select id="program" name="program" required>
                        <option value="">Select a program</option>
                        @foreach ($programs as $name)
                            <option value="{{ $name }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="button-links">Save Program</button>
                </form>
            @endif
        </section>
                
                <!-- Degree Progress: credits and each requirement's status -->
        @if ($user->program)
            <section>
                <h2>Degree Progress</h2>

                <!-- Credits completed vs required -->
                <p>
                    <strong>{{ $completedCredits }}</strong> of {{ $requiredCredits }} credits completed
                    ({{ $creditPercent }}%)
                </p>

                <div class="progress-bar" role="progressbar" aria-valuenow="{{ $creditPercent }}" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar__fill" style="width: {{ $creditPercent }}%"></div>
                </div>

                <!-- Each requirement -->
                <p>
                    {{ $progress->where('status', 'met')->count() }} met ·
                    {{ $progress->where('status', 'in_progress')->count() }} in progress ·
                    {{ $progress->where('status', 'not_started')->count() }} not started
                </p>

                <table>
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Title</th>
                            <th>Credits</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($progress as $course)
                            <tr>
                                <td data-label="Course">{{ $course->courseNumber }}</td>
                                <td data-label="Title">{{ $course->name }}</td>
                                <td data-label="Credits">{{ $course->credits }}</td>
                                <td data-label="Status">
                                    @if ($course->status === 'met')
                                        <span class="plan-status plan-status--ok">Met</span>
                                    @elseif ($course->status === 'in_progress')
                                        <span class="plan-status plan-status--progress">In progress</span>
                                    @else
                                        <span class="plan-status">Not started</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
        @endif

        <!-- Completed Courses: record courses already taken, with grades -->
        @if ($user->program)
            <section>
                <h2>Completed Courses</h2>

                <!-- Record a completed course -->
                <form method="POST" action="{{ route('student.history.store') }}" class="history-form">
                    @csrf

                    <label for="history-course">Course</label>
                    <select id="history-course" name="course" required>
                        <option value="">Select a course</option>
                        @foreach ($requirements as $course)
                            <option value="{{ $course->id }}">{{ $course->courseNumber }} {{ $course->name }}</option>
                        @endforeach
                    </select>

                    <label for="history-grade">Grade</label>
                    <select id="history-grade" name="letterGrade" required>
                        @foreach ($gradeOptions as $grade)
                            <option value="{{ $grade }}">{{ $grade }}</option>
                        @endforeach
                    </select>

                    <label for="history-season">Quarter</label>
                    <select id="history-season" name="season" required>
                        <option value="Fall">Fall</option>
                        <option value="Winter">Winter</option>
                        <option value="Spring">Spring</option>
                        <option value="Summer">Summer</option>
                    </select>

                    <label for="history-year">Year</label>
                    <input type="number" id="history-year" name="year" min="2000" max="{{ now()->year }}" value="{{ now()->year }}" required>

                    <button type="submit" class="button-links">Record</button>
                </form>

                <!-- What the student has recorded -->
                <table>
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Title</th>
                            <th>Credits</th>
                            <th>Grade</th>
                            <th>Completed</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($history as $row)
                            <tr>
                                <td data-label="Course">{{ $row->courseNumber }}</td>
                                <td data-label="Title">{{ $row->name }}</td>
                                <td data-label="Credits">{{ $row->credits }}</td>
                                <td data-label="Grade">
                                    @if ($row->gpa >= 2.0)
                                        <span class="plan-status plan-status--ok">{{ $row->letterGrade }}</span>
                                    @else
                                        <span class="plan-status plan-status--missing">{{ $row->letterGrade }}</span>
                                    @endif
                                </td>
                                <td data-label="Completed">{{ $row->completed }}</td>
                                <td data-label="Action">
                                    <form method="POST" action="{{ route('student.history.destroy', $row->history_id) }}" class="inline-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="button-links">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">No completed courses recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>
        @endif
                
        <!-- Required Courses -->
        @if ($user->program)
            <section>
                <h2>Required Courses</h2>

                 <!-- Required courses that arnt planned  -->
                @php
                    $remaining = $requirements->reject(fn ($course) => in_array($course->id, $coveredCourseIds));
                @endphp

                <p>{{ $requirements->count() - $remaining->count() }} of {{ $requirements->count() }} required courses planned</p>

                <table>
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Title</th>
                            <th>Credits</th>
                           <!-- <th>Time</th> -->
                            <th>Prerequisites</th>
                            <th>Offered</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($remaining as $course)
                            <tr>
                                <td data-label="Course">{{ $course->courseNumber }}</td>
                                <td data-label="Title">{{ $course->name }}</td>
                                <td data-label="Credits">{{ $course->credits }}</td>
                             <!--   <td data-label="Time">
                                    @forelse ($sections[$course->id] ?? [] as $section)
                                         {{ \App\Support\ClassTime::range($section->timeStart, $section->timeEnd) }} @if (!$loop->last)<br>@endif
                                    @empty
                                        TBA
                                    @endforelse
                                </td> -->
                                <td data-label="Prerequisites">
                                    @forelse ($prerequisites->get($course->id, collect()) as $prereq)
                                        @if (in_array($prereq->prereq_id, $coveredCourseIds))
                                            <span class="plan-status plan-status--ok">{{ $prereq->courseNumber }}</span>
                                        @else
                                            <span class="plan-status plan-status--missing">{{ $prereq->courseNumber }}</span>
                                        @endif
                                    @empty
                                        None
                                    @endforelse
                                </td>
                                <!-- When courses are offered -->
                                <td data-label="Offered">
                                    @php
                                        $courseTerms = $terms->get($course->id, collect());

                                        $missing = $prerequisites->get($course->id, collect())
                                        ->reject(fn ($prereq) => in_array($prereq->prereq_id, $coveredCourseIds))
                                        ->pluck('courseNumber');
                                    @endphp

                                    <!-- Not offered in any quarter -->
                                    @if ($courseTerms->isEmpty())
                                        Not scheduled yet

                                    <!-- Plan locked: just list the quarters -->
                                    @elseif ($locked)
                                        {{ $courseTerms->pluck('quarterYear')->join(', ') }}

                                    <!-- Can be added: pick a quarter -->
                                    @else
                                        <form method="POST" action="{{ route('student.plan.add') }}" class="plan-actions__move">
                                            @csrf
                                            <select name="term" aria-label="Quarter for {{ $course->courseNumber }}">
                                                @foreach ($courseTerms as $term)
                                                    <option value="{{ $term->id }}">{{ $term->quarterYear }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="button-links">Add</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                               <td colspan="5">
                                    @if ($requirements->isEmpty())
                                        No courses are listed for this program yet.
                                    @else
                                        All required courses are on your program map.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>
        @endif
        
        <!-- Program Courses -->
        @unless ($user->program)
            <section>
                <h2>All Courses</h2>

                <table>
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Title</th>
                            <th>Credits</th>
                            <th>Prerequisites</th>
                            <th>Terms Offered</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($catalog as $course)
                            <tr>
                                <td data-label="Course">{{ $course->courseNumber }}</td>
                                <td data-label="Title">{{ $course->name }}</td>
                                <td data-label="Credits">{{ $course->credits }}</td>
                                <td data-label="Prerequisites">
                                    @forelse ($prerequisites->get($course->id, collect()) as $prereq)
                                        {{ $prereq->courseNumber }}@if (!$loop->last), @endif
                                    @empty
                                        None
                                    @endforelse
                                </td>
                                <td data-label="Terms Offered">
                                    @forelse ($terms->get($course->id, collect()) as $term)
                                        {{ $term->quarterYear }}@if (!$loop->last), @endif
                                    @empty
                                        Not scheduled yet
                                    @endforelse
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">No courses have been added yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>
        @endunless

        <!--My Program Map-->
        <section>
           <h2>My Program Map</h2>

            <!-- Sort quarters in the order they happen: fall2026 before winter2027 -->
            @php
                $quarterOrder = function ($quarter) {
                    $seasons = ['winter' => 1, 'spring' => 2, 'summer' => 3, 'fall' => 4];
                    preg_match('/^([a-z]+)(\d{4})$/i', $quarter, $parts);

                    return $parts ? ((int) $parts[2]) * 10 + ($seasons[strtolower($parts[1])] ?? 0) : 0;
                };

                $plannedByQuarter = $planned
                    ->groupBy('quarterYear')
                    ->sortBy(fn ($courses, $quarter) => $quarterOrder($quarter));
            @endphp

            @forelse ($plannedByQuarter as $quarter => $courses)
                <div class="quarter-block">
                    <h3>{{ $quarter }} · {{ $courses->sum('credits') }} credits</h3>

                    <!-- Schedule conflicts in this quarter -->
                    @foreach ($conflicts[$quarter] ?? [] as $conflict)
                        <p class="plan-status plan-status--missing">
                            ⚠ {{ $conflict['first'] }} and {{ $conflict['second'] }} may meet at the same time
                            ({{ \App\Support\ClassTime::range($conflict['start'], $conflict['end']) }}). Check the days when you register.
                        </p>
                    @endforeach

                    <table>
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Title</th>
                                <th>Credits</th>
                              <!--  <th>Time</th> -->
                                @unless ($locked)
                                    <th></th>
                                @endunless
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($courses as $item)
                                <tr>
                                    <td data-label="Course">{{ $item->courseNumber }}</td>
                                    <td data-label="Title">{{ $item->name }}</td>
                                    <td data-label="Credits">{{ $item->credits }}</td>
                                <!--    <td data-label="Time">
                                        @php
                                            preg_match('/^([a-z]+)/i', $quarter, $seasonMatch);
                                            $season = strtolower($seasonMatch[1] ?? '');

                                            $quarterSections = ($sections[$item->course_id] ?? collect())
                                                ->filter(fn ($s) => $s->$season ?? false);
                                        @endphp

                                        @forelse ($quarterSections as $section)
                                           {{ \App\Support\ClassTime::range($section->timeStart, $section->timeEnd) }} @if (!$loop->last)<br>@endif
                                        @empty
                                            TBA
                                        @endforelse
                                    </td> -->
                                    @unless ($locked)
                                        <td data-label="Action">
                                            @php
                                                // Latest quarter of this course's prerequisites
                                                $afterOrder = $prerequisites->get($item->course_id, collect())
                                                    ->map(fn ($prereq) => $plannedQuarterOf[$prereq->prereq_id] ?? null)
                                                    ->filter()
                                                    ->map(fn ($quarter) => \App\Support\Quarter::order($quarter))
                                                    ->max() ?? 0;

                                                // Earliest quarter of planned courses that need this one
                                                $beforeOrder = $prerequisites
                                                    ->flatten(1)
                                                    ->where('prereq_id', $item->course_id)
                                                    ->map(fn ($row) => $plannedQuarterOf[$row->course_id] ?? null)
                                                    ->filter()
                                                    ->map(fn ($quarter) => \App\Support\Quarter::order($quarter))
                                                    ->min() ?? PHP_INT_MAX;

                                                // Other quarters this course is offered, between those two
                                                $otherTerms = $terms->get($item->course_id, collect())
                                                    ->where('id', '!=', $item->term_id)
                                                    ->filter(function ($term) use ($afterOrder, $beforeOrder) {
                                                        $order = \App\Support\Quarter::order($term->quarterYear);
                                                        return $order > $afterOrder && $order < $beforeOrder;
                                                    });
                                            @endphp

                                            <div class="plan-actions">
                                                @if ($otherTerms->isNotEmpty())
                                                    <form method="POST" action="{{ route('student.plan.move', $item->plan_id) }}" class="plan-actions__move">
                                                        @csrf
                                                        @method('PATCH')
                                                        <select name="term" aria-label="Move {{ $item->courseNumber }} to quarter">
                                                            @foreach ($otherTerms as $term)
                                                                <option value="{{ $term->id }}">{{ $term->quarterYear }}</option>
                                                            @endforeach
                                                        </select>
                                                        <button type="submit" class="button-links">Move</button>
                                                    </form>
                                                @endif

                                                <form method="POST" action="{{ route('student.plan.remove', $item->plan_id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="button-links">Remove</button>
                                                </form>
                                            </div>
                                        </td>
                                    @endunless
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- Advisor Comments -->
                    @if ($programMap)
                        @foreach ($programMap->comments->where('quarter', $quarter) as $comment)
                            <div class="plan-comment">
                                <p><strong>{{ $comment->author->name }}</strong> · {{ $comment->created_at->format('M j, Y g:i A') }}</p>
                                <p>{{ $comment->body }}</p>
                            </div>
                        @endforeach 
                    @endif
                </div>
            @empty
                <p>Your program map is empty. Add courses from the list above.</p>
            @endforelse

            <p>Total planned credits: <strong>{{ $planned->sum('credits') }}</strong></p>

            <!-- Advisor review {Marquis}-->
            <div class="plan-review-box">
                <h3>Advisor review</h3>
                
                <!-- Status submitted -->
                @if ($status === 'submitted')
                    <p><span class="plan-status">Submitted</span> Waiting for your advisor to review.</p>
                @elseif ($status === 'approved')
                    <p>
                        <span class="plan-status plan-status--ok">Approved</span>
                        by {{ $programMap->reviewer?->name ?? 'your advisor' }}
                        on {{ $programMap->reviewed_at?->format('M j, Y') }}
                    </p>
                <!-- Status request changes -->
                @elseif ($status === 'changes_requested')
                    <p><span class="plan-status plan-status--missing">Changes requested</span> Update your plan, then submit it again.</p>
                @else
                <!-- Status drafted (never submitted)-->
                    <p>Draft. Submit your program map when you're ready for your advisor to review it.</p>
                @endif
                <!-- Submit when plan is not locked  -->
                @if (! $locked && $planned->isNotEmpty())
                    <form method="POST" action="{{ route('student.plan.submit') }}">
                        @csrf
                        <button type="submit" class="button-links">Submit for review</button>
                    </form>
                @endif

                <!--Advisor Comments-->
               @if ($programMap && $programMap->comments->whereNull('quarter')->isNotEmpty())
                    <details class="comments-panel" open>
                        <summary>Advisor comments ({{ $programMap->comments->whereNull('quarter')->count() }})</summary>


                       @foreach ($programMap->comments->whereNull('quarter') as $comment)
                            <div class="plan-comment">
                                <p><strong>{{ $comment->author->name }}</strong> · {{ $comment->created_at->format('M j, Y g:i A') }}</p>
                                <p>{{ $comment->body }}</p>
                            </div>
                        @endforeach
                    </details>
                @endif
            </div>
        </section>

        <script>
            // start at the top after saving, adding or removing
            if ('scrollRestoration' in history) {
                history.scrollRestoration = 'manual';
            }
            window.scrollTo(0, 0);
        </script>
    </div>
@endsection
