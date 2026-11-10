@extends('layouts.app')

@section('title', $course->courseNumber . ' | Degree Planner')

@section('content')

    <div>
        <h1>{{ $course->courseNumber }}</h1>

        <h2>{{ $course->name }}</h2>

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
            </tbody>
        </table>

        <table>
            <tbody>
                <tr>
                    <th>Course Description</th>
                </tr>
                <tr>
                    <td>{{ $course->description }}</td>
                </tr>
            </tbody>
        </table>

        <table>
            <tbody>
                <tr>
                    <th>Class Dates</th>
                    <td>##date placeholder</td>
                </tr>
                <tr>
                    <th>Class Times</th>
                    <td>##time placeholder</td>
                </tr>
            </tbody>
        </table>

        @auth
            @if (auth()->user()->role === 'student')
                <div class="enroll-container">
                    <form action="{{ route('student.enroll', ['course' => $course->id]) }}" method="POST">
                        @csrf

                        <button type="submit" class="button-links-primary">
                            Enroll
                        </button>
                    </form>

                    @if (session()->has('success'))
                        <div class="alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="alert-error">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
            @endif
        @endauth

        <a href="{{ route('classes') }}" class="button-links">Back to Classes</a>
    </div>
@endsection