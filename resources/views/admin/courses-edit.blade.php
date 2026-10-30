@extends('layouts.app')

@section('title', 'Edit Course - Degree Planner')

@section('content')
    <div class="admin-layout">
        @include('partials.sidebar')

        <main class="admin-content">
            <h1>Edit Course</h1>

            @if ($errors->any())
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <form method="POST" action="{{ route('admin.courses.update', $course) }}">
                @csrf
                @method('PUT')

                <label for="courseNumber">Course Number</label>
                <input id="courseNumber" type="text" name="courseNumber"
                    value="{{ old('courseNumber', $course->courseNumber) }}" required>

                <label for="name">Course Name</label>
                <input id="name" type="text" name="name" value="{{ old('name', $course->name) }}" required>

                <label for="credits">Credits</label>
                <input id="credits" type="number" name="credits" min="0" step="any"
                    value="{{ old('credits', $course->credits) }}" required>

                <button type="submit" class="button-links">
                    Save Changes
                </button>
            </form>

            <button class="button-links-danger">
                <a href="{{ route('admin.courses.index') }}">Cancel</a>
            </button>
        </main>
    </div>
@endsection