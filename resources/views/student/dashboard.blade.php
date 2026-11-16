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
        
        <!-- Required Courses -->
        @if ($user->program)
            <section>
                <h2>Required Courses</h2>

                 <!-- Required courses that arnt planned  -->
                @php
                    $remaining = $requirements->reject(fn ($course) => in_array($course->id, $plannedCourseIds));
                @endphp

                <p>{{ $requirements->count() - $remaining->count() }} of {{ $requirements->count() }} required courses planned</p>

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
                                <!-- When courses are offered -->
                                <td data-label="Offered">
                                    @php
                                        $courseTerms = $terms->get($course->id, collect());

                                        $missing = $prerequisites->get($course->id, collect())
                                            ->reject(fn ($prereq) => in_array($prereq->prereq_id, $plannedCourseIds))
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

                    <table>
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Title</th>
                                <th>Credits</th>
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
                                    @unless ($locked)
                                        <td data-label="Action">
                                            @php
                                                $otherTerms = $terms->get($item->course_id, collect())
                                                    ->where('id', '!=', $item->term_id);
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
