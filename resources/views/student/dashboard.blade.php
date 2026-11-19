@extends('layouts.app')

@section('title', 'My Degree Plan - Degree Planner')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/student.css') }}">

    <div class="student-page" id="student-top">
        @php
            $status = $programMap?->status ?? 'draft';
            $locked = in_array($status, ['submitted', 'approved']);
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
                <p>Selected program: <strong>{{ $user->program }}</strong></p>

                @unless ($locked)
                    <form method="POST" action="{{ route('student.program.clear') }}" class="inline-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            data-confirm="Changing your program will remove every course from your program map. Continue?"
                            data-confirm-title="Change Program" data-confirm-button="Change Program">
                            Change Program
                        </button>
                    </form>
                @endunless
            @else
                <p>You have not picked a program yet.</p>

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

        <!-- Required Courses -->
        @if ($user->program)
            <section>
                <h2>Required Courses</h2>

                @php
                    $remaining = $requirements->reject(fn($course) => in_array($course->id, $plannedCourseIds));
                @endphp

                <p>
                    {{ $requirements->count() - $remaining->count() }} of
                    {{ $requirements->count() }} required courses planned
                </p>

                <table>
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Title</th>
                            <th>Credits</th>
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
                                <td data-label="Prerequisites">
                                    @forelse ($prerequisites->get($course->id, collect()) as $prereq)
                                        @if (in_array($prereq->prereq_id, $plannedCourseIds))
                                            <span class="plan-status plan-status--ok">{{ $prereq->courseNumber }}</span>
                                        @else
                                            <span class="plan-status plan-status--missing">{{ $prereq->courseNumber }}</span>
                                        @endif
                                    @empty
                                        None
                                    @endforelse
                                </td>
                                <td data-label="Offered">
                                    @php
                                        $courseTerms = $terms->get($course->id, collect());
                                    @endphp

                                    @if ($courseTerms->isEmpty())
                                        Not scheduled yet
                                    @elseif ($locked)
                                        {{ $courseTerms->pluck('quarterYear')->join(', ') }}
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

        <!-- All Courses -->
        @unless ($user->program)
            <section>
                <h2>All Courses</h2>

                <!-- Schedule conflicts -->
                @foreach (($conflicts ?? []) as $quarterConflicts)
                    @foreach ($quarterConflicts as $conflict)
                        <p class="plan-status plan-status--missing">
                            ⚠ {{ $conflict['first'] }} and {{ $conflict['second'] }} may meet at the same time
                            ({{ \App\Support\ClassTime::range($conflict['start'], $conflict['end']) }}).
                            Check the days when you register.
                        </p>
                    @endforeach
                @endforeach

                {{ $catalog->links() }}
                <table>
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Title</th>
                            <th>Credits</th>
                            <th>Prerequisites</th>
                            <th>Offered</th>
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

        <!-- My Program Map -->
        <section>
            <h2>My Program Map</h2>

            @php
                $quarterOrder = function ($quarter) {
                    $seasons = ['winter' => 1, 'spring' => 2, 'summer' => 3, 'fall' => 4];
                    preg_match('/^([a-z]+)(\d{4})$/i', $quarter, $parts);

                    return $parts ? ((int) $parts[2]) * 10 + ($seasons[strtolower($parts[1])] ?? 0) : 0;
                };

                $plannedByQuarter = $planned
                    ->groupBy('quarterYear')
                    ->sortBy(fn($courses, $quarter) => $quarterOrder($quarter));
            @endphp

            @forelse ($plannedByQuarter as $quarter => $courses)
                <div class="quarter-block">
                    <h3>{{ $quarter }} · {{ $courses->sum('credits') }} credits</h3>

                    <table>
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Title</th>
                                <th>Credits</th>
                                <th>Current Selected Term</th>
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
                                    <td>{{ $item->quarterYear }}</td>
                                    @unless ($locked)
                                        <td data-label="Action">
                                            @php
                                                $otherTerms = $terms->get($item->course_id, collect())
                                                    ->where('id', '!=', $item->term_id);
                                            @endphp

                                            <div class="plan-actions">
                                                @if ($otherTerms->isNotEmpty())
                                                    <form method="POST" action="{{ route('student.plan.move', $item->plan_id) }}"
                                                        class="plan-actions__move">
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
                                <p>
                                    <strong>{{ $comment->author->name }}</strong> ·
                                    {{ $comment->created_at->format('M j, Y g:i A') }}
                                </p>
                                <p>{{ $comment->body }}</p>
                            </div>
                        @endforeach
                    @endif
                </div>
            @empty
                <p>Your program map is empty. Add courses from the list above.</p>
            @endforelse

            <p>Total planned credits: <strong>{{ $planned->sum('credits') }}</strong></p>

            <!-- Advisor Review -->
            <div class="plan-review-box">
                <h3>Advisor review</h3>

                @if ($status === 'submitted')
                    <p><span class="plan-status">Submitted</span> Waiting for your advisor to review it.</p>
                @elseif ($status === 'approved')
                    <p>
                        <span class="plan-status plan-status--ok">Approved</span>
                        by {{ $programMap->reviewer?->name ?? 'your advisor' }}
                        on {{ $programMap->reviewed_at?->format('M j, Y') }}
                    </p>
                @elseif ($status === 'changes_requested')
                    <p>
                        <span class="plan-status plan-status--missing">Changes requested</span>
                        Update your plan, then submit it again.
                    </p>
                @else
                    <p>Draft. Submit your program map when you're ready for your advisor to review it.</p>
                @endif

                @if (!$locked && $planned->isNotEmpty())
                    <form method="POST" action="{{ route('student.plan.submit') }}">
                        @csrf
                        <button type="submit" class="button-links">Submit for review</button>
                    </form>
                @endif

                <!-- Advisor Comments -->
                @if ($programMap && $programMap->comments->whereNull('quarter')->isNotEmpty())
                    <details class="comments-panel" open>
                        <summary>
                            Advisor comments ({{ $programMap->comments->whereNull('quarter')->count() }})
                        </summary>

                        @foreach ($programMap->comments->whereNull('quarter') as $comment)
                            <div class="plan-comment">
                                <p>
                                    <strong>{{ $comment->author->name }}</strong> ·
                                    {{ $comment->created_at->format('M j, Y g:i A') }}
                                </p>
                                <p>{{ $comment->body }}</p>
                            </div>
                        @endforeach
                    </details>
                @endif
            </div>
        </section>

        <section>
            <h2>My Enrolled Courses</h2>

            <table>
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Title</th>
                        <th>Credits</th>
                        <th>Prerequisites</th>
                        <th>Term</th>
                        <th>Class Times</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($enrolled as $enrollment)
                        @php
                            $course = $enrollment->courseDetails;
                        @endphp

                        @if ($course)
                            <tr>
                                <td>{{ $course->courseNumber }}</td>
                                <td>{{ $course->name }}</td>
                                <td>{{ $course->credits }}</td>

                                <td>
                                    {{ $prerequisites->get($course->id, collect())->pluck('courseNumber')->join(', ') ?: 'None' }}
                                </td>

                                <td>
                                    {{ $enrollment->term?->quarterYear ?? 'Not specified' }}
                                </td>

                                <td>
                                    @forelse ($sections->get($course->id, collect()) as $classroom)
                                        @if ($classroom->timeStart && $classroom->timeEnd)
                                            <div>
                                                {{ \App\Support\ClassTime::range($classroom->timeStart, $classroom->timeEnd) }}
                                            </div>
                                        @else
                                            <div>Not scheduled</div>
                                        @endif
                                    @empty
                                        Not scheduled
                                    @endforelse
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="6">You are not enrolled in any courses.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <script>
            // Start at the top after saving, adding or removing.
            if ('scrollRestoration' in history) {
                history.scrollRestoration = 'manual';
            }
            window.scrollTo(0, 0);
        </script>
    </div>
@endsection