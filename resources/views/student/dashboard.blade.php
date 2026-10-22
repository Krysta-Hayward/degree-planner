@extends('layouts.app')

@section('title', 'My Degree Plan - Degree Planner')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/student.css') }}">

    <div class="student-page" id="student-top">
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
                <button type="submit">Save Program</button>
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
                                <td>{{ $course->courseNumber }}</td>
                                <td>{{ $course->name }}</td>
                                <td>{{ $course->credits }}</td>
                                <td>
                                    @forelse ($prerequisites->get($course->id, collect()) as $prereq)
                                        {{ $prereq->courseNumber }}@if (! $loop->last), @endif
                                    @empty
                                        None
                                    @endforelse
                                </td>
                                <td>
                                    @forelse ($terms->get($course->id, collect()) as $term)
                                        @php
                                            $missing = $prerequisites->get($course->id, collect())
                                                ->reject(fn ($prereq) => in_array($prereq->prereq_id, $plannedCourseIds))
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
                                                <button type="submit">Add {{ $term->quarterYear }}</button>
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
                            <td>{{ $course->courseNumber }}</td>
                            <td>{{ $course->name }}</td>
                            <td>{{ $course->credits }}</td>
                            <td>
                                @forelse ($prerequisites->get($course->id, collect()) as $prereq)
                                    {{ $prereq->courseNumber }}@if (! $loop->last), @endif
                                @empty
                                    None
                                @endforelse
                            </td>
                            <td>
                                @forelse ($terms->get($course->id, collect()) as $term)
                                    {{ $term->quarterYear }}@if (! $loop->last), @endif
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

        <section>
            <h2>My Program Map</h2>

            <table>
                <thead>
                    <tr>
                        <th>Term</th>
                        <th>Course</th>
                        <th>Title</th>
                        <th>Credits</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($planned as $item)
                        <tr>
                            <td>{{ $item->quarterYear }}</td>
                            <td>{{ $item->courseNumber }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->credits }}</td>
                            <td>
                                <form method="POST" action="{{ route('student.plan.remove', $item->plan_id) }}" class="inline-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">Your program map is empty. Add courses from the list above.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <p>Total planned credits: <strong>{{ $planned->sum('credits') }}</strong></p>
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
