@extends('layouts.app')

@section('title', $classroom->courseDetails->courseNumber . ' | Degree Planner')

@section('content')
            @php
    $course = $classroom->courseDetails;

    $terms = [];

    if ($classroom->fall)
        $terms[] = 'Fall';
    if ($classroom->winter)
        $terms[] = 'Winter';
    if ($classroom->spring)
        $terms[] = 'Spring';
    if ($classroom->summer)
        $terms[] = 'Summer';
            @endphp

            <div class="class-details-container">

                <h1>{{ $course->courseNumber }}</h1>
                <h2>{{ $course->name }}</h2>

                <h3>Course Information</h3>

                <table>
                    <tbody>
                        <tr>
                            <th>Course Number</th>
                            <td>{{ $course->courseNumber }}</td>
                        </tr>

                        <tr>
                            <th>Course Name</th>
                            <td>{{ $course->name }}</td>
                        </tr>

                        <tr>
                            <th>Credits</th>
                            <td>{{ $course->credits }}</td>
                        </tr>

                        <tr>
                            <th>Description</th>
                            <td>
                                {{ $course->description ?: 'No description available.' }}
                            </td>
                        </tr>
                    </tbody>
                </table>

                <h3>Class Availability</h3>

                <table>
                    <tbody>
                        <tr>
                            <th>Terms Available</th>
                            <td>
                                {{ count($terms) ? implode(', ', $terms) : 'Not Available' }}
                            </td>
                        </tr>

                        <tr>
                            <th>Total Seats</th>
                            <td>{{ $classroom->seats }}</td>
                        </tr>

                        <tr>
                            <th>Open Seats</th>
                            <td>{{ $classroom->seatsOpen }}</td>
                        </tr>

                        <tr>
                            <th>Teacher</th>
                            <td>{{ $classroom->teacher ?? 'Not Assigned' }}</td>
                        </tr>
                    </tbody>
                </table>

                <h3>Class Schedule</h3>

                <table>
                    <tbody>
                        <tr>
                            <th>Start Time</th>
                            <td>{{ $classroom->timeStart
        ? date('g:i A', strtotime($classroom->timeStart))
        : 'Not Scheduled' }}</td>
                        </tr>

                        <tr>
                            <th>End Time</th>
                            <td>{{ $classroom->timeEnd
        ? date('g:i A', strtotime($classroom->timeEnd))
        : 'Not Scheduled' }}</td>
                        </tr>
                    </tbody>
                </table>

                @auth
                    @if (auth()->user()->role === 'student')
                        <div class="enroll-container">

                            <form action="{{ route('student.enroll', ['classroom' => $classroom->id]) }}" method="POST">
                                @csrf

                                <label for="term">Choose Term:</label>

                                <select name="term" id="term" required>
                                    <option value="">Select a term</option>

                                    @foreach ($availableTerms as $term)
                                        <option value="{{ $term->id }}">
                                            {{ $term->quarterYear }}
                                        </option>
                                    @endforeach
                                </select>

                                <button type="submit" class="button-links-primary">
                                    Enroll
                                </button>
                            </form>

                            @if (session('success'))
                                <div class="alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert-error">
                                    {{ session('error') }}
                                </div>
                            @endif

                        </div>
                    @endif
                @endauth

                <a href="{{ route('classes') }}" class="button-links">
                    Back to Classes
                </a>

            </div>
@endsection
