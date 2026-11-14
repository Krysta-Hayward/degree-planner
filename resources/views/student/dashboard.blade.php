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

            @if ($user->program)
                <p>Selected program: <strong>{{ $user->program }}</strong></p>

                <form method="POST" action="{{ route('student.program.clear') }}" class="inline-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Clear Program</button>
                </form>
            @else
                <p>You have not picked a program yet.</p>
            @endif
        </section>

        @if ($user->program)
            <section>
                <h2>Required Courses</h2>

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
                        @forelse ($requirements as $course)
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
                                <td data-label="Offered">
                                    @forelse ($terms->get($course->id, collect()) as $term)
                                                                    @php
            $missing = $prerequisites->get($course->id, collect())
                ->reject(fn($prereq) => in_array($prereq->prereq_id, $plannedCourseIds))
                ->pluck('courseNumber');
                                                                    @endphp
                                                                    @if (in_array($term->id, $plannedTermIds))
                                                                        {{ $term->quarterYear }} (planned)
                                                                    @elseif ($missing->isNotEmpty())
                                                                        {{ $term->quarterYear }}: add {{ $missing->join(', ') }} first
                                                                    @else
                                                                        <form method="POST" action="{{ route('student.plan.add') }}" class="inline-form">
                                                                            @csrf
                                                                            <input type="hidden" name="term" value="{{ $term->id }}">
                                                                            <button type="submit" class="button-links">Add {{ $term->quarterYear }}</button>
                                                                        </form>
                                                                    @endif
                                    @empty
                                        Not scheduled yet
                                    @endforelse
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">No courses are listed for this program yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>
        @endif

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

        <!--My Program Map-->
        <section>
            <h2>My Program Map</h2>

            <table>
                <thead>
                    <tr>
                        <th>Term</th>
                        <th>Course</th>
                        <th>Title</th>
                        <th>Credits</th>
                        @unless ($locked)
                            <th></th>
                        @endunless
                    </tr>
                </thead>
                <tbody>
                    @forelse ($planned as $item)
                        <tr>
                            <td data-label="Term">{{ $item->quarterYear }}</td>
                            <td data-label="Course">{{ $item->courseNumber }}</td>
                            <td data-label="Title">{{ $item->name }}</td>
                            <td data-label="Credits">{{ $item->credits }}</td>
                            @unless ($locked)
                                <td data-label="Action">
                                    <form method="POST" action="{{ route('student.plan.remove', $item->plan_id) }}" class="inline-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="button-links">Remove</button>
                                    </form>
                                </td>
                            @endunless
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">Your program map is empty. Add courses from the list above.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

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
                @if ($programMap && $programMap->comments->isNotEmpty())
                    <details class="comments-panel" open>
                        <summary>Advisor comments ({{ $programMap->comments->count() }})</summary>

                        @foreach ($programMap->comments as $comment)
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
